<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * Paginated notification history for the authenticated user (Admin or MR).
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate($request->input('per_page', 20));

        $payload = $notifications->toArray();
        $payload['data'] = collect($payload['data'])
            ->map(fn ($n) => $this->transform($n))
            ->all();

        return $this->successResponse($payload, 'Notifications retrieved successfully');
    }

    /**
     * Lightweight feed for a bell/dropdown: unread count + latest 10.
     */
    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();

        $recent = $user->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($n) => $this->transform($n->toArray()));

        return $this->successResponse([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $recent,
        ], 'Notification feed retrieved successfully');
    }

    /**
     * Mark a single notification as read.
     */
    public function read(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (! $notification) {
            return $this->errorResponse('Notification not found.', 404);
        }

        $notification->markAsRead();

        return $this->successResponse([
            'url' => $notification->data['url'] ?? null,
        ], 'Notification marked as read');
    }

    /**
     * Mark all of the user's notifications as read.
     */
    public function readAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->successResponse([], 'All notifications marked as read');
    }

    /**
     * Normalise a notification array for API clients.
     */
    protected function transform(array $notification): array
    {
        $data = $notification['data'] ?? [];

        return [
            'id' => $notification['id'],
            'type' => $data['type'] ?? 'general',
            'icon' => $data['icon'] ?? 'general',
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? null,
            'is_read' => ! is_null($notification['read_at']),
            'created_at' => $notification['created_at'],
        ];
    }
}

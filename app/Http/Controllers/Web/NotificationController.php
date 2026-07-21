<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * JSON feed for the navbar dropdown: unread count + most recent notifications.
     */
    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();

        $recent = $user->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($n) => $this->transform($n));

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $recent,
        ]);
    }

    /**
     * Full notifications history page.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read, then redirect to its target url.
     */
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'url' => $url]);
        }

        return redirect($url ?: route('notifications.index'));
    }

    /**
     * Mark all of the user's notifications as read.
     */
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Normalise a notification model for the frontend.
     */
    protected function transform($notification): array
    {
        $data = $notification->data;

        return [
            'id' => $notification->id,
            'type' => $data['type'] ?? 'general',
            'icon' => $data['icon'] ?? 'general',
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? null,
            'is_read' => $notification->read_at !== null,
            'time' => $notification->created_at->diffForHumans(),
        ];
    }
}

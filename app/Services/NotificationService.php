<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService extends BaseService
{
    /**
     * Send a notification to every active Admin user.
     *
     * Notification delivery is non-critical: any failure is logged but never
     * bubbles up to break the primary business action that triggered it.
     */
    public function notifyAdmins(Notification $notification, ?int $exceptUserId = null): void
    {
        $this->safely(function () use ($notification, $exceptUserId) {
            $admins = User::role('Admin')
                ->when($exceptUserId, fn ($q) => $q->where('id', '!=', $exceptUserId))
                ->get();

            if ($admins->isNotEmpty()) {
                NotificationFacade::send($admins, $notification);
            }
        });
    }

    /**
     * Send a notification to a single user (by model or id).
     */
    public function notifyUser(User|int $user, Notification $notification): void
    {
        $this->safely(function () use ($user, $notification) {
            $user = $user instanceof User ? $user : User::find($user);
            $user?->notify($notification);
        });
    }

    /**
     * Run a delivery closure, swallowing and logging any failure.
     */
    protected function safely(callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            Log::error('Notification delivery failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}

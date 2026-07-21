<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Order $order, protected string $newStatus)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order',
            'icon' => 'order',
            'title' => 'Order Status Updated',
            'message' => "Your order for {$this->order->doctor_name} is now \"{$this->newStatus}\".",
            'url' => route('mr.orders.show', $this->order->id),
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Order $order, protected string $mrName)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $itemCount = $this->order->items->sum('quantity');

        return [
            'type' => 'order',
            'icon' => 'order',
            'title' => 'New Order Collected',
            'message' => "{$this->mrName} collected an order from {$this->order->doctor_name} ({$itemCount} item(s)).",
            'url' => route('admin.orders.show', $this->order->id),
        ];
    }
}

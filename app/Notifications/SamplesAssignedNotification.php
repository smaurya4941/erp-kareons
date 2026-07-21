<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SamplesAssignedNotification extends Notification
{
    use Queueable;

    /**
     * @param int $productCount Number of distinct products assigned
     * @param int $totalQuantity Total quantity across those products
     */
    public function __construct(protected int $productCount, protected int $totalQuantity)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sample',
            'icon' => 'sample',
            'title' => 'New Samples Assigned',
            'message' => "You have been assigned {$this->totalQuantity} sample(s) across {$this->productCount} product(s).",
            'url' => route('mr.samples.index'),
        ];
    }
}

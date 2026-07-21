<?php

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailyReportSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(protected DailyReport $report, protected string $mrName)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $visits = $this->report->stats_snapshot['visits']['total_visits'] ?? 0;
        $orders = $this->report->stats_snapshot['orders']['total_orders'] ?? 0;

        return [
            'type' => 'report',
            'icon' => 'report',
            'title' => 'Daily Report Submitted',
            'message' => "{$this->mrName} completed the day with {$visits} visit(s) and {$orders} order(s).",
            'url' => route('admin.daily-reports.show', $this->report->id),
        ];
    }
}

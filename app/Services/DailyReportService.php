<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\Attendance;
use App\Models\DoctorVisit;
use App\Models\Order;
use Carbon\Carbon;

class DailyReportService
{
    /**
     * Generate or return an existing Draft report for the day with aggregated stats.
     */
    public function generateDraftReport(int $userId, string $date)
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        // Rule 2: Attendance should be completed before submitting the report.
        if (!$attendance || !$attendance->check_out_time) {
            throw new \Exception("You must complete your check-out before generating the Daily Report.");
        }

        // Calculate Working Hours
        $checkIn = Carbon::parse($attendance->check_in_time);
        $checkOut = Carbon::parse($attendance->check_out_time);
        $workingMinutes = $checkIn->diffInMinutes($checkOut);
        
        $hours = floor($workingMinutes / 60);
        $minutes = $workingMinutes % 60;
        $workingHoursStr = "{$hours} Hours {$minutes} Minutes";

        // Aggregate Doctor Visits
        $visits = DoctorVisit::with(['discussedProducts', 'distributedSamples'])
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();

        $totalVisits = $visits->count();
        $totalProductsDiscussed = $visits->sum(function($visit) {
            return $visit->discussedProducts->count();
        });
        $totalSamplesDistributed = $visits->sum(function($visit) {
            return $visit->distributedSamples->sum('quantity');
        });

        // Aggregate Orders
        $orders = Order::with('items')
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();
            
        $totalOrders = $orders->count();
        $totalOrderItems = $orders->sum(function($order) {
            return $order->items->sum('quantity');
        });

        $statsSnapshot = [
            'attendance' => [
                'check_in' => $checkIn->format('h:i A'),
                'check_out' => $checkOut->format('h:i A'),
                'working_hours' => $workingHoursStr,
            ],
            'visits' => [
                'total_visits' => $totalVisits,
                'total_products_discussed' => $totalProductsDiscussed,
                'total_samples_distributed' => $totalSamplesDistributed,
            ],
            'orders' => [
                'total_orders' => $totalOrders,
                'total_ordered_products' => $totalOrderItems,
            ]
        ];

        // Rule 1: Only one report per MR per day. Update or create Draft.
        return DailyReport::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            ['stats_snapshot' => $statsSnapshot]
        );
    }

    /**
     * Submit the MR's daily report for a date, compiling stats and saving the manual sections.
     *
     * @throws \Exception
     */
    public function submitReport(int $userId, string $date, array $data): DailyReport
    {
        // Ensures attendance/checkout rules pass and stats snapshot is fresh.
        $report = $this->generateDraftReport($userId, $date);

        if ($report->status !== 'Draft') {
            throw new \Exception("Today's report has already been submitted and cannot be edited.");
        }

        $report->update([
            'today_summary' => $data['today_summary'],
            'problems_faced' => $data['problems_faced'] ?? null,
            'tomorrow_plan' => $data['tomorrow_plan'],
            'status' => 'Submitted',
        ]);

        return $report->fresh();
    }
}

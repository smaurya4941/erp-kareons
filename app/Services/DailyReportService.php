<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\Attendance;
use App\Models\DoctorVisit;
use App\Models\Order;
use App\Models\User;
use App\Notifications\DailyReportSubmittedNotification;
use Carbon\Carbon;

class DailyReportService
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

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

        $statsSnapshot = $this->buildStatsSnapshot($userId, $date, $attendance);

        // Rule 1: Only one report per MR per day. Update or create Draft.
        return DailyReport::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            ['stats_snapshot' => $statsSnapshot]
        );
    }

    /**
     * Automatically compile and finalise the MR's daily report at check-out.
     *
     * This is triggered by the check-out flow so the report is prepared without
     * any manual effort. It freezes a full snapshot of the day (attendance,
     * every doctor visit with discussed products and samples, and all orders)
     * and marks the report as Submitted.
     *
     * If the MR has already hand-written summary notes they are preserved, and a
     * report that has already been Reviewed by an admin is left untouched.
     *
     * @throws \Exception
     */
    public function generateOnCheckout(int $userId, string $date): DailyReport
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        if (!$attendance || !$attendance->check_out_time) {
            throw new \Exception("Attendance check-out is required before the report can be generated.");
        }

        $existing = DailyReport::where('user_id', $userId)->where('date', $date)->first();

        // Never overwrite a report an admin has already reviewed.
        if ($existing && $existing->status === 'Reviewed') {
            return $existing;
        }

        $statsSnapshot = $this->buildStatsSnapshot($userId, $date, $attendance);

        $report = DailyReport::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'stats_snapshot' => $statsSnapshot,
                // Preserve anything the MR may have typed earlier; otherwise auto-fill.
                'today_summary' => $existing?->today_summary ?: $this->composeAutoSummary($statsSnapshot),
                'problems_faced' => $existing?->problems_faced,
                'tomorrow_plan' => $existing?->tomorrow_plan ?: 'Auto-generated at check-out. Continue field visits as per the assigned route.',
                'status' => 'Submitted',
            ]
        );

        // Notify admins that the MR has wrapped up the day (non-blocking).
        $mrName = User::find($userId)?->name ?? 'An MR';
        $this->notificationService->notifyAdmins(
            new DailyReportSubmittedNotification($report, $mrName)
        );

        return $report;
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

    /**
     * Build the full frozen snapshot of an MR's day: aggregated totals plus a
     * detailed breakdown of every visit, its products/samples, and orders.
     */
    protected function buildStatsSnapshot(int $userId, string $date, Attendance $attendance): array
    {
        // Working hours — prefer the authoritative working_minutes persisted at
        // check-out; fall back to recomputing from the timestamps if it's missing.
        $checkIn = Carbon::parse($attendance->check_in_time);
        $checkOut = Carbon::parse($attendance->check_out_time);
        $workingMinutes = (int) ($attendance->working_minutes ?? $checkIn->diffInMinutes($checkOut));
        $hours = intdiv($workingMinutes, 60);
        $minutes = $workingMinutes % 60;
        $workingHoursStr = "{$hours} Hours {$minutes} Minutes";

        // Doctor visits with their products and samples (eager-loaded to avoid N+1).
        $visits = DoctorVisit::with([
                'discussedProducts.product:id,name,product_code',
                'distributedSamples.product:id,name,product_code',
            ])
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->orderBy('time')
            ->get();

        $totalVisits = $visits->count();
        $totalProductsDiscussed = $visits->sum(fn ($visit) => $visit->discussedProducts->count());
        $totalSamplesDistributed = $visits->sum(fn ($visit) => $visit->distributedSamples->sum('quantity'));

        $visitDetails = $visits->map(function ($visit) {
            return [
                'doctor_name' => $visit->doctor_name,
                'clinic_name' => $visit->clinic_name,
                'specialization' => $visit->specialization,
                'area' => $visit->area,
                'time' => $visit->time ? Carbon::parse($visit->time)->format('h:i A') : null,
                'doctor_response' => $visit->doctor_response,
                'discussion_summary' => $visit->discussion_summary,
                'competitor_medicines' => $visit->competitor_medicines,
                'products' => $visit->discussedProducts->map(fn ($p) => [
                    'name' => $p->product->name ?? 'Unknown',
                    'interest_level' => $p->interest_level,
                    'remarks' => $p->remarks,
                ])->values()->all(),
                'samples' => $visit->distributedSamples->map(fn ($s) => [
                    'name' => $s->product->name ?? 'Unknown',
                    'quantity' => $s->quantity,
                ])->values()->all(),
            ];
        })->values()->all();

        // Orders with items.
        $orders = Order::with('items.product:id,name,product_code')
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        $totalOrders = $orders->count();
        $totalOrderItems = $orders->sum(fn ($order) => $order->items->sum('quantity'));

        $orderDetails = $orders->map(function ($order) {
            return [
                'doctor_name' => $order->doctor_name,
                'status' => $order->status,
                'remarks' => $order->remarks,
                'items' => $order->items->map(fn ($item) => [
                    'name' => $item->product->name ?? 'Unknown',
                    'quantity' => $item->quantity,
                ])->values()->all(),
            ];
        })->values()->all();

        return [
            'attendance' => [
                'check_in' => $checkIn->format('h:i A'),
                'check_out' => $checkOut->format('h:i A'),
                'working_hours' => $workingHoursStr,
                'working_minutes' => $workingMinutes,
            ],
            'visits' => [
                'total_visits' => $totalVisits,
                'total_products_discussed' => $totalProductsDiscussed,
                'total_samples_distributed' => $totalSamplesDistributed,
            ],
            'orders' => [
                'total_orders' => $totalOrders,
                'total_ordered_products' => $totalOrderItems,
            ],
            'details' => [
                'visits' => $visitDetails,
                'orders' => $orderDetails,
            ],
        ];
    }

    /**
     * Compose a human-readable auto summary from the aggregated stats.
     */
    protected function composeAutoSummary(array $stats): string
    {
        $visits = $stats['visits']['total_visits'] ?? 0;
        $products = $stats['visits']['total_products_discussed'] ?? 0;
        $samples = $stats['visits']['total_samples_distributed'] ?? 0;
        $orders = $stats['orders']['total_orders'] ?? 0;
        $hours = $stats['attendance']['working_hours'] ?? 'N/A';

        return "Auto-generated summary: Completed {$visits} doctor visit(s), discussed {$products} product(s), "
            . "distributed {$samples} sample(s) and collected {$orders} order(s). Total working time: {$hours}.";
    }
}

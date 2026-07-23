<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\DoctorVisit;
use App\Models\DoctorVisitSample;
use App\Models\Order;
use App\Models\SampleAssignment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    /**
     * Today's field-work summary for the authenticated MR (mobile home screen).
     */
    public function summary(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();

        $report = DailyReport::where('user_id', $userId)->whereDate('date', $today)->first();

        $visitsToday = DoctorVisit::where('user_id', $userId)->whereDate('date', $today)->get();

        $ordersToday = Order::where('user_id', $userId)->whereDate('created_at', $today)->count();

        $samplesGivenToday = (int) DoctorVisitSample::whereIn('doctor_visit_id', $visitsToday->pluck('id'))
            ->sum('quantity');

        $assignments = SampleAssignment::where('user_id', $userId)->get();
        $sampleStock = [
            'assigned' => (int) $assignments->sum('assigned_quantity'),
            'distributed' => (int) $assignments->sum('distributed_quantity'),
            'remaining' => (int) $assignments->sum(fn ($a) => $a->remaining_quantity),
            'low_stock' => $assignments->filter(fn ($a) => $a->remaining_quantity > 0 && $a->remaining_quantity <= 5)->count(),
        ];

        // Daily visit target is configurable via admin Settings; fall back to 12.
        $targetVisits = (int) (Setting::where('key', 'mr_daily_visit_target')->value('value') ?: 12);

        return $this->successResponse([
            'attendance' => [
                'checked_in' => (bool) $attendance,
                'checked_out' => (bool) ($attendance?->check_out_time),
                'check_in_time' => $attendance?->check_in_time,
                'check_out_time' => $attendance?->check_out_time,
                'is_late' => (bool) ($attendance?->is_late),
                'working_hours' => $attendance?->formatted_working_hours ?? '0h',
            ],
            'stats' => [
                'visits_today' => $visitsToday->count(),
                'target_visits' => $targetVisits,
                'orders_today' => $ordersToday,
                'samples_given_today' => $samplesGivenToday,
            ],
            'sample_stock' => $sampleStock,
            'daily_report' => [
                'submitted' => $report && $report->status !== 'Draft',
                'status' => $report?->status,
            ],
        ], 'MR dashboard summary retrieved successfully');
    }
}

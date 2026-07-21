<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\AdminDashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function admin(Request $request)
    {
        // Handle Date Filter
        $filter = $request->get('date_filter', 'today');
        
        $start = Carbon::today();
        $end = Carbon::today();
        
        switch ($filter) {
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::yesterday();
                break;
            case 'last_7_days':
                $start = Carbon::today()->subDays(6);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $start = Carbon::parse($request->start_date);
                    $end = Carbon::parse($request->end_date);
                }
                break;
        }

        // Cache the dashboard data based on filter for 5 minutes to optimize performance
        $cacheKey = "admin_dashboard_data_{$filter}_{$start->format('Ymd')}_{$end->format('Ymd')}";
        
        $data = cache()->remember($cacheKey, 300, function () use ($start, $end) {
            return [
                'kpis' => $this->dashboardService->getKpis($start, $end),
                'top_mrs' => $this->dashboardService->getTopPerformingMrs($start, $end),
                'recent_activities' => $this->dashboardService->getRecentActivities($start, $end),
                'chart_data' => $this->dashboardService->getChartData($start, $end),
                // We'll pass the dates for the views to display
                'start_date' => $start->format('d M Y'),
                'end_date' => $end->format('d M Y'),
            ];
        });

        // Add filter to data array
        $data['current_filter'] = $filter;
        
        return view('admin.dashboard', $data);
    }

    public function mr()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $attendance = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $attendanceYesterday = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->first();

        $report = \App\Models\DailyReport::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Today's field-work KPIs
        $visitsToday = \App\Models\DoctorVisit::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get();

        $ordersToday = \App\Models\Order::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        $samplesGivenToday = \App\Models\DoctorVisitSample::whereIn('doctor_visit_id', $visitsToday->pluck('id'))
            ->sum('quantity');

        // Yesterday's field-work KPIs (for day-over-day trend comparison)
        $visitsYesterday = \App\Models\DoctorVisit::where('user_id', $user->id)
            ->whereDate('date', $yesterday)
            ->get();

        $ordersYesterday = \App\Models\Order::where('user_id', $user->id)
            ->whereDate('created_at', $yesterday)
            ->count();

        $samplesGivenYesterday = \App\Models\DoctorVisitSample::whereIn('doctor_visit_id', $visitsYesterday->pluck('id'))
            ->sum('quantity');

        // Sample stock snapshot (assigned vs remaining)
        $assignments = \App\Models\SampleAssignment::where('user_id', $user->id)->get();
        $sampleStock = [
            'assigned' => (int) $assignments->sum('assigned_quantity'),
            'distributed' => (int) $assignments->sum('distributed_quantity'),
            'remaining' => (int) $assignments->sum(fn ($a) => $a->remaining_quantity),
            'low_stock' => $assignments->filter(fn ($a) => $a->remaining_quantity > 0 && $a->remaining_quantity <= 5)->count(),
        ];

        // Recent visits for the activity feed
        $recentVisits = \App\Models\DoctorVisit::where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(5)
            ->get();

        // Daily visit target is configurable via admin Settings; fall back to 12.
        $targetVisits = (int) (\App\Models\Setting::where('key', 'mr_daily_visit_target')->value('value') ?: 12);

        $stats = [
            'visits_today' => $visitsToday->count(),
            'target_visits' => $targetVisits,
            'orders_today' => $ordersToday,
            'samples_given_today' => (int) $samplesGivenToday,
            'working_hours' => $attendance?->formatted_working_hours ?? '0h',
            'checked_in' => (bool) $attendance,
            'checked_out' => (bool) ($attendance?->check_out_time),
        ];

        // Day-over-day trend for each KPI card (compares today vs yesterday)
        $trend = function ($todayValue, $yesterdayValue) {
            if ($yesterdayValue == 0) {
                return $todayValue > 0
                    ? ['label' => 'New', 'up' => true]
                    : ['label' => '0%', 'up' => true];
            }
            $pct = (int) round((($todayValue - $yesterdayValue) / $yesterdayValue) * 100);
            return [
                'label' => ($pct >= 0 ? '+' : '') . $pct . '%',
                'up' => $pct >= 0,
            ];
        };

        $trends = [
            'visits' => $trend($visitsToday->count(), $visitsYesterday->count()),
            'orders' => $trend($ordersToday, $ordersYesterday),
            'samples' => $trend((int) $samplesGivenToday, (int) $samplesGivenYesterday),
            'hours' => $trend((int) ($attendance?->working_minutes ?? 0), (int) ($attendanceYesterday?->working_minutes ?? 0)),
        ];

        // Time-of-day aware greeting
        $hour = Carbon::now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');

        $userRoute = $user->area ?? 'Field Route';

        // Weekly chart data (last 7 days)
        $last7Days = collect(range(6, 0))->map(function($days) {
            return Carbon::today()->subDays($days);
        });
        $weeklyChartLabels = $last7Days->map->format('D')->values()->toArray();
        $weeklyChartData = [];
        foreach ($last7Days as $day) {
            $weeklyChartData[] = \App\Models\DoctorVisit::where('user_id', $user->id)
                ->whereDate('date', $day->toDateString())
                ->count();
        }

        // Timeline events
        $timelineEvents = collect();
        if ($attendance) {
            $timelineEvents->push([
                'time' => Carbon::parse($attendance->check_in_time)->format('h:i A'),
                'sort_time' => Carbon::parse($attendance->check_in_time)->format('H:i:s'),
                'title' => 'Check In',
                'description' => 'Duty started',
                'color' => '#10B981',
                'completed' => true,
            ]);
            
            if ($attendance->check_out_time) {
                $timelineEvents->push([
                    'time' => Carbon::parse($attendance->check_out_time)->format('h:i A'),
                    'sort_time' => Carbon::parse($attendance->check_out_time)->format('H:i:s'),
                    'title' => 'Check Out',
                    'description' => 'Duty ended',
                    'color' => '#6B7280',
                    'completed' => true,
                ]);
            }
        }
        foreach ($visitsToday as $visit) {
            $timelineEvents->push([
                'time' => $visit->time ? Carbon::parse($visit->time)->format('h:i A') : '12:00 PM',
                'sort_time' => $visit->time ? Carbon::parse($visit->time)->format('H:i:s') : '12:00:00',
                'title' => 'Visit: ' . $visit->doctor_name,
                'description' => $visit->clinic_name ?: 'Doctor Visit',
                'color' => '#5B4CF0',
                'completed' => true,
            ]);
        }
        $timelineEvents = $timelineEvents->sortBy('sort_time')->values();

        // Pending Tasks
        $pendingTasks = collect();
        if ($report && $report->status === 'Draft') {
            $pendingTasks->push([
                'title' => 'Submit Daily Report',
                'description' => 'Your daily report is in draft',
            ]);
        } elseif ($attendance && !$attendance->check_out_time && Carbon::now()->hour >= 17) {
            $pendingTasks->push([
                'title' => 'End of Day Check Out',
                'description' => 'Please check out and submit report',
            ]);
        }
        
        if ($sampleStock['low_stock'] > 0) {
            $pendingTasks->push([
                'title' => 'Low Sample Stock',
                'description' => "You have {$sampleStock['low_stock']} items running low",
            ]);
        }

        return view('mr.dashboard', compact(
            'user',
            'attendance',
            'report',
            'stats',
            'trends',
            'greeting',
            'sampleStock',
            'recentVisits',
            'userRoute',
            'weeklyChartLabels',
            'weeklyChartData',
            'timelineEvents',
            'pendingTasks'
        ));
    }
}

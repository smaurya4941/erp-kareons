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

        $attendance = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $today)
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

        $stats = [
            'visits_today' => $visitsToday->count(),
            'orders_today' => $ordersToday,
            'samples_given_today' => (int) $samplesGivenToday,
            'working_hours' => $attendance?->formatted_working_hours,
            'checked_in' => (bool) $attendance,
            'checked_out' => (bool) ($attendance?->check_out_time),
        ];

        return view('mr.dashboard', compact(
            'user',
            'attendance',
            'report',
            'stats',
            'sampleStock',
            'recentVisits'
        ));
    }
}

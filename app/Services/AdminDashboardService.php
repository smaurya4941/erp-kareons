<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DoctorVisit;
use App\Models\Order;
use App\Models\User;
use App\Models\DoctorVisitSample;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Get primary KPI numbers
     */
    public function getKpis(Carbon $start, Carbon $end)
    {
        // For 'Today' metrics, we often specifically want today regardless of range, 
        // but to respect the user's filter, we'll apply it.
        $totalMrs = User::role('MR')->where('status', true)->count();
        
        $presentMrs = Attendance::whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->distinct('user_id')
            ->count('user_id');

        $totalVisits = DoctorVisit::whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])->count();
        
        $totalOrders = Order::whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])->count();
        
        $pendingOrders = Order::where('status', 'Pending')
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->count();

        $samplesDistributed = DoctorVisitSample::whereHas('visit', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
        })->sum('quantity');

        return [
            'total_mrs' => $totalMrs,
            'present_mrs' => $presentMrs,
            'absent_mrs' => max(0, $totalMrs - $presentMrs),
            'total_visits' => $totalVisits,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'samples_distributed' => $samplesDistributed,
        ];
    }

    /**
     * Top performing MRs based on visit count and orders
     */
    public function getTopPerformingMrs(Carbon $start, Carbon $end, $limit = 5)
    {
        return User::role('MR')
            ->withCount([
                'doctorVisits as visits_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
                },
                'orders as orders_count' => function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
                }
            ])
            ->orderByDesc('visits_count')
            ->orderByDesc('orders_count')
            ->take($limit)
            ->get();
    }

    /**
     * Build daily trend data for the dashboard charts.
     *
     * Returns a per-day series (labels + datasets) for doctor visits, orders,
     * samples distributed and present MRs. To keep the trend meaningful even
     * for single-day filters (Today / Yesterday), the window is expanded to a
     * minimum of 7 days ending on $end, and capped at 31 days to keep the
     * chart readable and the queries light.
     */
    public function getChartData(Carbon $start, Carbon $end)
    {
        $rangeEnd = $end->copy()->startOfDay();
        $rangeStart = $start->copy()->startOfDay();

        // Ensure a readable window: at least 7 days, at most 31.
        if ($rangeStart->diffInDays($rangeEnd) < 6) {
            $rangeStart = $rangeEnd->copy()->subDays(6);
        } elseif ($rangeStart->diffInDays($rangeEnd) > 30) {
            $rangeStart = $rangeEnd->copy()->subDays(30);
        }

        $periodStart = $rangeStart->copy()->startOfDay();
        $periodEnd = $rangeEnd->copy()->endOfDay();

        // Pre-aggregate each metric grouped by day to avoid N queries.
        $visitsByDay = DoctorVisit::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->groupBy('d')->pluck('c', 'd');

        $ordersByDay = Order::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->groupBy('d')->pluck('c', 'd');

        $samplesByDay = DoctorVisitSample::selectRaw('DATE(doctor_visits.created_at) as d, SUM(doctor_visit_samples.quantity) as c')
            ->join('doctor_visits', 'doctor_visits.id', '=', 'doctor_visit_samples.doctor_visit_id')
            ->whereBetween('doctor_visits.created_at', [$periodStart, $periodEnd])
            ->groupBy('d')->pluck('c', 'd');

        $presentByDay = Attendance::selectRaw('date as d, COUNT(DISTINCT user_id) as c')
            ->whereBetween('date', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
            ->groupBy('d')->pluck('c', 'd');

        $labels = [];
        $visits = [];
        $orders = [];
        $samples = [];
        $present = [];

        for ($day = $periodStart->copy(); $day->lte($periodEnd); $day->addDay()) {
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d M');
            $visits[] = (int) ($visitsByDay[$key] ?? 0);
            $orders[] = (int) ($ordersByDay[$key] ?? 0);
            $samples[] = (int) ($samplesByDay[$key] ?? 0);
            $present[] = (int) ($presentByDay[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'visits' => $visits,
            'orders' => $orders,
            'samples' => $samples,
            'present' => $present,
        ];
    }

    /**
     * Recent Activities Timeline
     */
    public function getRecentActivities(Carbon $start, Carbon $end)
    {
        // 1. Get recent check-ins
        $attendances = Attendance::with('user')
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->take(10)->get()
            ->map(function ($item) {
                return [
                    'time' => $item->created_at,
                    'type' => 'Attendance',
                    'message' => $item->user->name . ' checked in.',
                    'color' => 'green'
                ];
            });

        // 2. Get recent visits
        $visits = DoctorVisit::with('user')
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->take(10)->get()
            ->map(function ($item) {
                return [
                    'time' => $item->created_at,
                    'type' => 'Visit',
                    'message' => $item->user->name . ' completed visit with ' . $item->doctor_name,
                    'color' => 'blue'
                ];
            });

        // 3. Get recent orders
        $orders = Order::with('user')
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->take(10)->get()
            ->map(function ($item) {
                return [
                    'time' => $item->created_at,
                    'type' => 'Order',
                    'message' => 'Order received from ' . $item->doctor_name . ' (by ' . $item->user->name . ')',
                    'color' => 'yellow'
                ];
            });

        // Merge, sort, and slice
        $activities = collect($attendances)
            ->merge($visits)
            ->merge($orders)
            ->sortByDesc('time')
            ->take(15)
            ->values();

        return $activities;
    }
}

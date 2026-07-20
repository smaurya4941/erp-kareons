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

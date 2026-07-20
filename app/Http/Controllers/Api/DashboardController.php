<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ActivityLogResource;
use App\Models\User;
use App\Models\DoctorVisit;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseApiController
{
    /**
     * Dashboard core counters.
     */
    public function summary(): JsonResponse
    {
        $today = today()->toDateString();
        
        $data = [
            'active_mrs' => User::role('MR')->where('status', 'Active')->count(),
            'today_visits' => DoctorVisit::whereDate('created_at', $today)->count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'pending_orders' => Order::where('status', 'Pending')->count(),
        ];

        return $this->successResponse($data, 'Dashboard summary retrieved successfully');
    }

    /**
     * Data for dashboard charts (e.g., visits over last 7 days).
     */
    public function charts(): JsonResponse
    {
        $labels = [];
        $visits = [];
        $orders = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('d M');
            $visits[] = DoctorVisit::whereDate('created_at', $date)->count();
            $orders[] = Order::whereDate('created_at', $date)->count();
        }

        return $this->successResponse([
            'labels' => $labels,
            'datasets' => [
                'visits' => $visits,
                'orders' => $orders
            ]
        ], 'Dashboard chart data retrieved');
    }

    /**
     * Recent system activities.
     */
    public function recentActivities(): JsonResponse
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return $this->successResponse(
            ActivityLogResource::collection($activities),
            'Recent activities retrieved'
        );
    }
}

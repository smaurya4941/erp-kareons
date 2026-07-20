<?php

namespace App\Http\Controllers\Web\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderReportController extends Controller
{
    public function index(Request $request, ExportService $exportService)
    {
        $query = Order::with(['user', 'items.product']);

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhere('clinic_name', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply MR Filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply Date Filter
        $dateFilter = $request->get('date_filter', 'this_month');
        if ($dateFilter !== 'all') {
            $start = Carbon::now();
            $end = Carbon::now();
            
            if ($dateFilter == 'today') {
                $start = Carbon::today();
                $end = Carbon::today();
            } elseif ($dateFilter == 'yesterday') {
                $start = Carbon::yesterday();
                $end = Carbon::yesterday();
            } elseif ($dateFilter == 'last_7_days') {
                $start = Carbon::today()->subDays(6);
                $end = Carbon::today();
            } elseif ($dateFilter == 'this_month') {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            } elseif ($dateFilter == 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date);
                $end = Carbon::parse($request->end_date);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
        }

        $query->orderBy('created_at', 'desc');

        // Handle Export
        if ($request->get('export') === 'csv') {
            $data = $query->get();
            $columns = [
                'Date' => function($row) { return $row->created_at->format('Y-m-d H:i'); },
                'MR Name' => function($row) { return $row->user->name ?? 'N/A'; },
                'Doctor' => 'doctor_name',
                'Clinic' => 'clinic_name',
                'Area' => 'area',
                'Total Items' => function($row) { return $row->items->sum('quantity'); },
                'Remarks' => 'remarks',
                'Status' => 'status',
            ];
            return $exportService->downloadCsv($data, $columns, 'Order_Report_' . Carbon::now()->format('Ymd'));
        }

        // Summary Stats
        $summaryQuery = clone $query;
        $summaryQuery->getQuery()->orders = null;
        $summaryQuery->getQuery()->limit = null;
        $summaryQuery->getQuery()->offset = null;
        
        $totalOrders = $summaryQuery->count();
        $pendingOrders = (clone $summaryQuery)->where('status', 'Pending')->count();
        $completedOrders = (clone $summaryQuery)->where('status', 'Completed')->count();
        
        // Count total quantity across all matching orders
        // Need a join or nested sum to be perfectly accurate in DB, 
        // but for MVP we can pull IDs and sum related if not massive, or do a join
        $totalOrderedQuantity = DB::table('order_items')
            ->whereIn('order_id', $summaryQuery->select('id'))
            ->sum('quantity');

        // Prepare View Data
        $orders = $query->paginate($request->get('per_page', 25))->withQueryString();
        $mrs = User::role('MR')->get();
        $statuses = ['Pending', 'Reviewed', 'Completed'];

        return view('admin.reports.orders', compact(
            'orders', 'mrs', 'statuses', 'totalOrders', 'pendingOrders', 'completedOrders', 'totalOrderedQuantity'
        ));
    }
}

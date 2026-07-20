<?php

namespace App\Http\Controllers\Web\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function index(Request $request, ExportService $exportService)
    {
        $query = Attendance::with('user');

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
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
                // Today
            } elseif ($dateFilter == 'yesterday') {
                $start = Carbon::yesterday();
                $end = Carbon::yesterday();
            } elseif ($dateFilter == 'last_7_days') {
                $start = Carbon::today()->subDays(6);
            } elseif ($dateFilter == 'this_month') {
                $start = Carbon::now()->startOfMonth();
            } elseif ($dateFilter == 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date);
                $end = Carbon::parse($request->end_date);
            } else {
                $start = Carbon::now()->startOfMonth(); // Default if empty
            }

            $query->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);
        }

        // Ordering
        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        // Handle Export
        if ($request->get('export') === 'csv') {
            $data = $query->get();
            $columns = [
                'Date' => 'date',
                'Employee Name' => function($row) { return $row->user->name ?? 'N/A'; },
                'Employee Code' => function($row) { return $row->user->employee_code ?? 'N/A'; },
                'Check In' => function($row) { return $row->check_in_time ? Carbon::parse($row->check_in_time)->format('h:i A') : 'N/A'; },
                'Check Out' => function($row) { return $row->check_out_time ? Carbon::parse($row->check_out_time)->format('h:i A') : 'N/A'; },
                'Working Hours' => 'working_hours_calculated', // Note: assuming we have an accessor or we calculate it
                'Status' => 'status'
            ];
            return $exportService->downloadCsv($data, $columns, 'Attendance_Report_' . Carbon::now()->format('Ymd'));
        }

        // Prepare View Data
        $attendances = $query->paginate($request->get('per_page', 25))->withQueryString();
        
        $mrs = User::role('MR')->get();
        $statuses = ['Present', 'Absent', 'Incomplete'];

        // Summary Stats (Needs a clone of query without pagination)
        $summaryQuery = clone $query;
        $summaryQuery->getQuery()->orders = null; // Remove order by for aggregate speed
        $summaryQuery->getQuery()->limit = null;
        $summaryQuery->getQuery()->offset = null;
        
        $totalRecords = $summaryQuery->count();
        $presentCount = (clone $summaryQuery)->where('status', 'Present')->count();
        $absentCount = (clone $summaryQuery)->where('status', 'Absent')->count();
        $incompleteCount = (clone $summaryQuery)->where('status', 'Incomplete')->count();

        return view('admin.reports.attendance', compact('attendances', 'mrs', 'statuses', 'totalRecords', 'presentCount', 'absentCount', 'incompleteCount'));
    }
}

<?php

namespace App\Http\Controllers\Web\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MrPerformanceReportController extends Controller
{
    public function index(Request $request, ExportService $exportService)
    {
        // We will build a complex query using withCount and withSum 
        // to aggregate the performance of MRs within a date range.
        
        // Apply Date Filter
        $dateFilter = $request->get('date_filter', 'this_month');
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        
        if ($dateFilter !== 'all') {
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
                // already set
            } elseif ($dateFilter == 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date);
                $end = Carbon::parse($request->end_date);
            }
        } else {
            // Unbound, but let's just make it a very wide range
            $start = Carbon::parse('2020-01-01'); 
            $end = Carbon::now();
        }

        $startFormat = $start->format('Y-m-d');
        $endFormat = $end->format('Y-m-d');
        $startDateTime = $start->startOfDay();
        $endDateTime = $end->endOfDay();

        $query = User::role('MR')
            ->where('status', true); // only active MRs

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        // Aggregate relations conditionally
        $query->withCount([
            'attendances as working_days' => function ($query) use ($startFormat, $endFormat) {
                $query->whereBetween('date', [$startFormat, $endFormat])
                      ->where('status', 'Present');
            },
            'doctorVisits as visits_count' => function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
            },
            'orders as orders_count' => function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
            }
        ]);

        // Load attendances to calculate actual working hours (since we don't have a sum column directly available easily)
        // Note: For massive datasets, this should be done via a DB View or specialized query
        $query->with(['attendances' => function ($query) use ($startFormat, $endFormat) {
            $query->whereBetween('date', [$startFormat, $endFormat])
                  ->whereNotNull('check_in_time')
                  ->whereNotNull('check_out_time');
        }, 'doctorVisits' => function($query) use ($startDateTime, $endDateTime) {
            $query->whereBetween('created_at', [$startDateTime, $endDateTime])
                  ->with('samples');
        }]);

        // Fetch data
        $users = $query->get()->map(function($user) {
            // Calculate Working Hours
            $totalMinutes = 0;
            foreach ($user->attendances as $attendance) {
                $in = Carbon::parse($attendance->check_in_time);
                $out = Carbon::parse($attendance->check_out_time);
                $totalMinutes += $in->diffInMinutes($out);
            }
            $user->total_working_hours = floor($totalMinutes / 60) . 'h ' . ($totalMinutes % 60) . 'm';
            $user->total_minutes_raw = $totalMinutes;

            // Calculate Samples Distributed
            $totalSamples = 0;
            foreach ($user->doctorVisits as $visit) {
                $totalSamples += $visit->samples->sum('quantity');
            }
            $user->samples_distributed = $totalSamples;

            return $user;
        })->sortByDesc('visits_count')->values();

        // Handle Export
        if ($request->get('export') === 'csv') {
            $columns = [
                'MR Name' => 'name',
                'Employee Code' => 'employee_code',
                'Working Days' => 'working_days',
                'Doctor Visits' => 'visits_count',
                'Orders Collected' => 'orders_count',
                'Samples Distributed' => 'samples_distributed',
                'Working Hours' => 'total_working_hours',
            ];
            return $exportService->downloadCsv($users, $columns, 'MR_Performance_Report_' . Carbon::now()->format('Ymd'));
        }

        // Summary Stats
        $activeMrsCount = $users->count();
        $totalVisitsAll = $users->sum('visits_count');
        $totalOrdersAll = $users->sum('orders_count');
        $totalMinutesAll = $users->sum('total_minutes_raw');

        $avgVisits = $activeMrsCount > 0 ? round($totalVisitsAll / $activeMrsCount, 1) : 0;
        $avgOrders = $activeMrsCount > 0 ? round($totalOrdersAll / $activeMrsCount, 1) : 0;
        
        $avgMins = $activeMrsCount > 0 ? floor($totalMinutesAll / $activeMrsCount) : 0;
        $avgWorkingHours = floor($avgMins / 60) . 'h ' . ($avgMins % 60) . 'm';

        // Prepare View Data
        // Since we mapped the collection, we manually paginate if we want, or just show all (usually MR count is < 100 in MVP)
        // We will pass the full collection for MVP simplicity.
        return view('admin.reports.performance', compact(
            'users', 'avgVisits', 'avgOrders', 'avgWorkingHours', 'start', 'end'
        ));
    }
}

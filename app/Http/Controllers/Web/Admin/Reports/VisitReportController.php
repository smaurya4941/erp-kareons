<?php

namespace App\Http\Controllers\Web\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\DoctorVisit;
use App\Models\User;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitReportController extends Controller
{
    public function index(Request $request, ExportService $exportService)
    {
        $query = DoctorVisit::with(['user', 'discussedProducts.product', 'distributedSamples.product', 'order']);

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
                'Products Discussed' => function($row) { return $row->discussedProducts->count(); },
                'Samples Given' => function($row) { return $row->distributedSamples->sum('quantity'); },
                'Order Placed?' => function($row) { return $row->order ? 'Yes' : 'No'; },
            ];
            return $exportService->downloadCsv($data, $columns, 'Visit_Report_' . Carbon::now()->format('Ymd'));
        }

        // Summary Stats (Needs a clone of query without pagination)
        $summaryQuery = clone $query;
        $summaryQuery->getQuery()->orders = null;
        $summaryQuery->getQuery()->limit = null;
        $summaryQuery->getQuery()->offset = null;
        
        $totalVisits = $summaryQuery->count();
        $uniqueDoctors = (clone $summaryQuery)->distinct('doctor_name')->count('doctor_name');
        
        // Complex aggregation for most visited area
        $mostVisitedAreaRecord = (clone $summaryQuery)
            ->select('area', DB::raw('count(*) as count'))
            ->groupBy('area')
            ->orderBy('count', 'desc')
            ->first();
        $mostVisitedArea = $mostVisitedAreaRecord ? $mostVisitedAreaRecord->area : 'N/A';

        // Prepare View Data
        $visits = $query->paginate($request->get('per_page', 25))->withQueryString();
        $mrs = User::role('MR')->get();

        return view('admin.reports.visits', compact(
            'visits', 'mrs', 'totalVisits', 'uniqueDoctors', 'mostVisitedArea'
        ));
    }
}

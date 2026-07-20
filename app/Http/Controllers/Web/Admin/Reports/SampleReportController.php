<?php

namespace App\Http\Controllers\Web\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\DoctorVisitSample;
use App\Models\Product;
use App\Models\User;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SampleReportController extends Controller
{
    public function index(Request $request, ExportService $exportService)
    {
        $query = DoctorVisitSample::with(['visit.user', 'product']);

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('visit', function($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply MR Filter
        if ($request->filled('user_id')) {
            $query->whereHas('visit', function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        
        // Apply Product Filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Apply Date Filter based on Visit created_at
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

            $query->whereHas('visit', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            });
        }

        // Handle Export
        if ($request->get('export') === 'csv') {
            // Need to join for correct ordering/grouping in large exports or just use eager loading
            $data = $query->get();
            $columns = [
                'Date' => function($row) { return $row->visit->created_at->format('Y-m-d H:i'); },
                'MR Name' => function($row) { return $row->visit->user->name ?? 'N/A'; },
                'Doctor' => function($row) { return $row->visit->doctor_name ?? 'N/A'; },
                'Area' => function($row) { return $row->visit->area ?? 'N/A'; },
                'Product' => function($row) { return $row->product->name ?? 'N/A'; },
                'Quantity' => 'quantity',
            ];
            return $exportService->downloadCsv($data, $columns, 'Sample_Distribution_Report_' . Carbon::now()->format('Ymd'));
        }

        // Summary Stats
        $summaryQuery = clone $query;
        $totalSamples = $summaryQuery->sum('quantity');
        
        // Find most distributed product in this range
        $topProductRecord = (clone $summaryQuery)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->first();
            
        $topProductName = 'N/A';
        if ($topProductRecord) {
            $product = Product::find($topProductRecord->product_id);
            if ($product) {
                $topProductName = $product->name . ' (' . $topProductRecord->total_qty . ')';
            }
        }

        // Find Top MR in this range
        // For accurate MR aggregation on the sample level, it's better to group by visit.user_id via a join
        $topMrRecord = DB::table('doctor_visit_samples')
            ->join('doctor_visits', 'doctor_visit_samples.doctor_visit_id', '=', 'doctor_visits.id')
            ->select('doctor_visits.user_id', DB::raw('SUM(doctor_visit_samples.quantity) as total_qty'))
            ->whereIn('doctor_visit_samples.id', (clone $summaryQuery)->select('id'))
            ->groupBy('doctor_visits.user_id')
            ->orderBy('total_qty', 'desc')
            ->first();
            
        $topMrName = 'N/A';
        if ($topMrRecord) {
            $user = User::find($topMrRecord->user_id);
            if ($user) {
                $topMrName = $user->name . ' (' . $topMrRecord->total_qty . ')';
            }
        }

        // Pagination - order by visit date
        // Best approach in Eloquent is a join for sorting, or just sort by the sample ID (usually chronological)
        $samples = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 25))->withQueryString();
        
        $mrs = User::role('MR')->get();
        $products = Product::where('status', true)->get();

        return view('admin.reports.samples', compact(
            'samples', 'mrs', 'products', 'totalSamples', 'topProductName', 'topMrName'
        ));
    }
}

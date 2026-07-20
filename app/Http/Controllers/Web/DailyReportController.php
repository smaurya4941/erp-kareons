<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        // Only show reports that have been submitted by the MR (Rule 6 logic - Admin sees finalized data)
        $query = DailyReport::with('user')->whereIn('status', ['Submitted', 'Reviewed']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != '' && $request->end_date != '') {
            $query->whereBetween('date', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($qUser) use ($search) {
                $qUser->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        $reports = $query->paginate($request->get('per_page', 15))->withQueryString();

        $mrs = User::role('MR')->get();

        // Dashboard Stats
        $today = Carbon::today()->format('Y-m-d');
        
        $stats = [
            'today' => DailyReport::where('date', $today)->whereIn('status', ['Submitted', 'Reviewed'])->count(),
            'pending_review' => DailyReport::where('status', 'Submitted')->count(),
            'total_visits_today' => 0,
            'total_orders_today' => 0,
        ];
        
        // Sum visits and orders for today from the JSON snapshot
        $reportsToday = DailyReport::where('date', $today)->whereIn('status', ['Submitted', 'Reviewed'])->get();
        foreach ($reportsToday as $r) {
            $snap = $r->stats_snapshot;
            $stats['total_visits_today'] += $snap['visits']['total_visits'] ?? 0;
            $stats['total_orders_today'] += $snap['orders']['total_orders'] ?? 0;
        }

        return view('admin.reports.index', compact('reports', 'mrs', 'stats'));
    }

    public function show(DailyReport $report)
    {
        $report->load('user');
        return view('admin.reports.show', compact('report'));
    }

    public function markReviewed(Request $request, DailyReport $report)
    {
        if ($report->status === 'Submitted') {
            $report->update(['status' => 'Reviewed']);
            return back()->with('success', 'Report marked as Reviewed.');
        }
        
        return back()->with('error', 'Report cannot be reviewed.');
    }
}

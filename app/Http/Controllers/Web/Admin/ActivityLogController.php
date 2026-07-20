<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a paginated list of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'subject'])->latest();

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by Module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by User Name
        if ($request->filled('user_search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_search . '%')
                  ->orWhere('employee_code', 'like', '%' . $request->user_search . '%');
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        $modules = ActivityLog::select('module')->distinct()->pluck('module');

        return view('admin.logs.index', compact('logs', 'modules'));
    }

    /**
     * Display a visual chronological timeline of events.
     */
    public function timeline(Request $request)
    {
        $query = ActivityLog::with(['user', 'subject'])->latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', today());
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $timelineEvents = $query->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.logs.timeline', compact('timelineEvents', 'users'));
    }

    /**
     * Show detailed JSON changes for a specific log entry.
     */
    public function show(ActivityLog $log)
    {
        $log->load(['user', 'subject']);
        return view('admin.logs.show', compact('log'));
    }
}

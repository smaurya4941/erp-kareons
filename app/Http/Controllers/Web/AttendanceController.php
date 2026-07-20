<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances (Admin view).
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->has('date') && $request->date != '') {
            $query->where('date', $request->date);
        } else {
            // Default to today if no date specified
            $query->where('date', Carbon::today()->toDateString());
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $query->orderBy('date', 'desc')->orderBy('id', 'desc');
        $attendances = $query->paginate($request->get('per_page', 15))->withQueryString();

        $mrs = User::role('MR')->get();
        
        // Calculate daily stats for dashboard cards
        $todayStr = $request->get('date', Carbon::today()->toDateString());
        $totalMrs = $mrs->count();
        $presentToday = Attendance::where('date', $todayStr)->where('status', 'Present')->count();
        $incompleteToday = Attendance::where('date', $todayStr)->where('status', 'Incomplete')->count();
        $absentToday = $totalMrs - ($presentToday + $incompleteToday);

        return view('admin.attendance.index', compact('attendances', 'mrs', 'presentToday', 'incompleteToday', 'absentToday'));
    }

    /**
     * Display the specified attendance details.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('user');
        return view('admin.attendance.show', compact('attendance'));
    }
}

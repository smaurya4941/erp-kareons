<?php

namespace App\Http\Controllers\Web\Mr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display MR's attendance history
     */
    public function index(Request $request)
    {
        $attendances = Attendance::where('user_id', auth()->id())
                                 ->orderBy('date', 'desc')
                                 ->paginate(15);
                                 
        return view('mr.attendance.index', compact('attendances'));
    }

    /**
     * Show the mark attendance page
     */
    public function markForm()
    {
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('user_id', auth()->id())
                                ->where('date', $today)
                                ->first();

        // Already checked out?
        if ($attendance && $attendance->check_out_time) {
            return redirect()->route('mr.attendance.index')
                             ->with('info', 'You have already completed your attendance for today.');
        }

        return view('mr.attendance.mark', compact('attendance'));
    }

    /**
     * Handle Check In via Web Form
     */
    public function checkIn(CheckInRequest $request)
    {
        try {
            $data = $request->validated();
            $data['device_info'] = [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'platform' => 'Web Browser'
            ];

            $this->attendanceService->checkIn(auth()->id(), $data);
            
            return redirect()->route('mr.dashboard')->with('success', 'Attendance marked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle Check Out via Web Form
     */
    public function checkOut(CheckOutRequest $request)
    {
        try {
            $data = $request->validated();
            $data['device_info'] = [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'platform' => 'Web Browser'
            ];

            $this->attendanceService->checkOut(auth()->id(), $data);
            
            return redirect()->route('mr.dashboard')->with('success', 'Check out completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends BaseApiController
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get MR's attendance history
     */
    public function index(Request $request): JsonResponse
    {
        $attendances = Attendance::where('user_id', $request->user()->id)
                                 ->orderBy('date', 'desc')
                                 ->paginate(15);
                                 
        return $this->successResponse(
            AttendanceResource::collection($attendances)->response()->getData(true),
            'Attendance history retrieved'
        );
    }

    /**
     * Get today's attendance status
     */
    public function today(Request $request): JsonResponse
    {
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('user_id', $request->user()->id)
                                ->where('date', $today)
                                ->first();

        if (!$attendance) {
            return $this->successResponse(null, 'No attendance marked for today yet.');
        }

        return $this->successResponse(new AttendanceResource($attendance), 'Today\'s attendance retrieved.');
    }

    /**
     * Handle Check In
     */
    public function checkIn(CheckInRequest $request): JsonResponse
    {
        try {
            // Include IP and User Agent in device info
            $data = $request->validated();
            $data['device_info'] = array_merge($data['device_info'] ?? [], [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'platform' => 'API/Mobile App'
            ]);

            $attendance = $this->attendanceService->checkIn($request->user()->id, $data);
            
            return $this->successResponse(new AttendanceResource($attendance), 'Attendance marked successfully.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Handle Check Out
     */
    public function checkOut(CheckOutRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['device_info'] = array_merge($data['device_info'] ?? [], [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'platform' => 'API/Mobile App'
            ]);

            $attendance = $this->attendanceService->checkOut($request->user()->id, $data);
            
            return $this->successResponse(new AttendanceResource($attendance), 'Check out completed successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}

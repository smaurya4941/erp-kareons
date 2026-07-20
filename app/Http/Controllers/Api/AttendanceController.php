<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends BaseApiController
{
    /**
     * Display a listing of attendances (Admin view).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with('user');

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        $attendances = $query->paginate($request->get('per_page', 15));

        return $this->successResponse(
            AttendanceResource::collection($attendances)->response()->getData(true),
            'Attendances retrieved successfully'
        );
    }

    /**
     * Display the specified attendance.
     */
    public function show(Attendance $attendance): JsonResponse
    {
        return $this->successResponse(
            new AttendanceResource($attendance->load('user')), 
            'Attendance record retrieved successfully'
        );
    }
}

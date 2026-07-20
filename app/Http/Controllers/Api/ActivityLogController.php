<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends BaseApiController
{
    /**
     * Display a paginated listing of activity logs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with(['user']);

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->latest()->paginate($request->input('per_page', 20));

        return $this->successResponse(
            ActivityLogResource::collection($logs)->response()->getData(true),
            'Activity logs retrieved successfully'
        );
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog): JsonResponse
    {
        $activityLog->load(['user', 'subject']);

        return $this->successResponse(
            new ActivityLogResource($activityLog),
            'Activity log details retrieved successfully'
        );
    }
}

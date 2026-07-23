<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DailyReportResource;
use App\Models\DailyReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyReportController extends BaseApiController
{
    /**
     * Display a listing of daily reports.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DailyReport::with(['user']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $reports = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 15));

        return $this->successResponse(
            DailyReportResource::collection($reports)->response()->getData(true),
            'Daily reports retrieved successfully'
        );
    }

    /**
     * Display the specified report.
     */
    public function show(DailyReport $dailyReport): JsonResponse
    {
        $dailyReport->load(['user']);

        return $this->successResponse(new DailyReportResource($dailyReport), 'Daily report retrieved successfully');
    }

    /**
     * Admin marks a submitted report as Reviewed.
     */
    public function review(DailyReport $dailyReport): JsonResponse
    {
        if ($dailyReport->status !== 'Submitted') {
            return $this->errorResponse('Report cannot be reviewed.', 400);
        }

        $dailyReport->update(['status' => 'Reviewed']);

        return $this->successResponse(
            new DailyReportResource($dailyReport->fresh('user')),
            'Report marked as Reviewed.'
        );
    }

    /**
     * Generate a summary of submitted reports for a given date.
     */
    public function summary(Request $request): JsonResponse
    {
        $date = $request->input('date', today()->toDateString());

        $reports = DailyReport::whereDate('date', $date)->get();

        $totalVisits = $reports->sum(fn ($report) => data_get($report->stats_snapshot, 'visits.total_visits', 0));

        return $this->successResponse([
            'date' => $date,
            'total_reports' => $reports->count(),
            'total_submitted' => $reports->where('status', 'Submitted')->count(),
            'total_visits_reported' => $totalVisits,
        ], 'Daily report summary generated');
    }
}

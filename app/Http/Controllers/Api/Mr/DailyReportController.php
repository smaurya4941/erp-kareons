<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\DailyReport\SubmitDailyReportRequest;
use App\Http\Resources\DailyReportResource;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyReportController extends BaseApiController
{
    protected DailyReportService $dailyReportService;

    public function __construct(DailyReportService $dailyReportService)
    {
        $this->dailyReportService = $dailyReportService;
    }

    /**
     * Submit today's daily report (compiles stats + saves manual sections).
     */
    public function store(SubmitDailyReportRequest $request): JsonResponse
    {
        try {
            $report = $this->dailyReportService->submitReport(
                $request->user()->id,
                Carbon::today()->toDateString(),
                $request->validated()
            );

            return $this->successResponse(
                new DailyReportResource($report),
                'Daily report submitted successfully.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * List the MR's own daily report history.
     */
    public function index(Request $request): JsonResponse
    {
        $reports = DailyReport::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->successResponse(
            DailyReportResource::collection($reports)->response()->getData(true),
            'Daily report history retrieved successfully.'
        );
    }
}

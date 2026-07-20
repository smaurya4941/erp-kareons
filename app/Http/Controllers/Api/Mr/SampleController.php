<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\SampleAssignmentResource;
use App\Services\SampleAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SampleController extends BaseApiController
{
    protected SampleAssignmentService $sampleService;

    public function __construct(SampleAssignmentService $sampleService)
    {
        $this->sampleService = $sampleService;
    }

    /**
     * Get MR's own assigned samples (Read-only)
     */
    public function index(Request $request): JsonResponse
    {
        $assignments = $this->sampleService->getMrSamples($request->user()->id);
        
        return $this->successResponse(
            SampleAssignmentResource::collection($assignments),
            'My samples retrieved successfully'
        );
    }
}

<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\DoctorVisit\StoreSampleDistributionRequest;
use App\Http\Resources\SampleDistributionResource;
use App\Models\DoctorVisitSample;
use App\Services\DoctorVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SampleDistributionController extends BaseApiController
{
    protected DoctorVisitService $visitService;

    public function __construct(DoctorVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Distribute samples during a visit. Enforces the MR's remaining stock.
     */
    public function store(StoreSampleDistributionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $visit = $this->visitService->distributeSamples(
                $request->user()->id,
                $data['doctor_visit_id'],
                $data['samples']
            );

            return $this->successResponse(
                SampleDistributionResource::collection($visit->distributedSamples),
                'Samples distributed successfully.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * List the MR's own sample distributions.
     */
    public function index(Request $request): JsonResponse
    {
        $distributions = DoctorVisitSample::with(['product', 'visit'])
            ->whereHas('visit', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->successResponse(
            SampleDistributionResource::collection($distributions)->response()->getData(true),
            'Sample distributions retrieved successfully.'
        );
    }
}

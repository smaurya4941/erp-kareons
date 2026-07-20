<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\DoctorVisit\StoreDoctorVisitRequest;
use App\Http\Resources\DoctorVisitResource;
use App\Models\DoctorVisit;
use App\Services\DoctorVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorVisitController extends BaseApiController
{
    protected DoctorVisitService $visitService;

    public function __construct(DoctorVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Get MR's doctor visit history
     */
    public function index(Request $request): JsonResponse
    {
        $visits = DoctorVisit::with(['discussedProducts.product', 'distributedSamples.product', 'order.items.product'])
                             ->where('user_id', $request->user()->id)
                             ->orderBy('date', 'desc')
                             ->orderBy('time', 'desc')
                             ->paginate(15);
                                 
        return $this->successResponse(
            DoctorVisitResource::collection($visits)->response()->getData(true),
            'Doctor visits retrieved successfully'
        );
    }

    /**
     * Store a new doctor visit (Full JSON Payload)
     */
    public function store(StoreDoctorVisitRequest $request): JsonResponse
    {
        try {
            $visit = $this->visitService->createVisit($request->user()->id, $request->validated());
            
            $visit->load([
                'user', 
                'discussedProducts.product', 
                'distributedSamples.product', 
                'order.items.product'
            ]);

            return $this->successResponse(new DoctorVisitResource($visit), 'Doctor visit recorded successfully.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Show a specific visit for the MR
     */
    public function show(Request $request, DoctorVisit $doctorVisit): JsonResponse
    {
        if ($doctorVisit->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $doctorVisit->load([
            'user', 
            'discussedProducts.product', 
            'distributedSamples.product', 
            'order.items.product'
        ]);

        return $this->successResponse(new DoctorVisitResource($doctorVisit), 'Visit retrieved');
    }
}

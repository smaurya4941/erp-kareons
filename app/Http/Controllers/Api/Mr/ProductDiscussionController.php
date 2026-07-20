<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\DoctorVisit\StoreProductDiscussionRequest;
use App\Http\Resources\ProductDiscussionResource;
use App\Models\DoctorVisit;
use App\Services\DoctorVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductDiscussionController extends BaseApiController
{
    protected DoctorVisitService $visitService;

    public function __construct(DoctorVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Attach one or more discussed products to an existing visit.
     */
    public function store(StoreProductDiscussionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $visit = $this->visitService->addProductDiscussions(
                $request->user()->id,
                $data['doctor_visit_id'],
                $data['products']
            );

            return $this->successResponse(
                ProductDiscussionResource::collection($visit->discussedProducts),
                'Product discussion recorded successfully.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * List discussed products for one of the MR's own visits.
     */
    public function show(Request $request, DoctorVisit $visit): JsonResponse
    {
        if ($visit->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $visit->load('discussedProducts.product');

        return $this->successResponse(
            ProductDiscussionResource::collection($visit->discussedProducts),
            'Product discussions retrieved successfully.'
        );
    }
}

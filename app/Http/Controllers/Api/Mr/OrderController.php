<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\DoctorVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    protected DoctorVisitService $visitService;

    public function __construct(DoctorVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Record a new order taken by the MR.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $order = $this->visitService->recordOrder(
                $request->user()->id,
                $data['items'],
                $data['doctor_visit_id'],
                $data['remarks'] ?? null
            );

            return $this->successResponse(
                new OrderResource($order),
                'Order recorded successfully.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * List the MR's own orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['items.product', 'visit'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->successResponse(
            OrderResource::collection($orders)->response()->getData(true),
            'Orders retrieved successfully.'
        );
    }

    /**
     * Show one of the MR's own orders.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $order->load(['items.product', 'visit', 'statusHistories.changedBy']);

        return $this->successResponse(
            new OrderResource($order),
            'Order retrieved successfully.'
        );
    }
}

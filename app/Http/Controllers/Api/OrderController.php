<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseApiController
{
    /**
     * Columns the client is allowed to sort by (prevents SQL injection via `sort`).
     */
    private const SORTABLE = ['created_at', 'status', 'doctor_name', 'id'];

    /**
     * Display a listing of all orders for Admin.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'visit', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($qUser) => $qUser->where('name', 'like', "%{$search}%"));
            });
        }

        // Sorting — whitelist column and direction.
        $sort = in_array($request->input('sort'), self::SORTABLE, true) ? $request->input('sort') : 'created_at';
        $direction = strtolower($request->input('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);

        $orders = $query->paginate($request->input('per_page', 15));

        return $this->successResponse(
            OrderResource::collection($orders)->response()->getData(true),
            'Orders retrieved successfully'
        );
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['user', 'visit', 'items.product', 'statusHistories.changedBy']);

        return $this->successResponse(new OrderResource($order), 'Order retrieved successfully');
    }

    /**
     * Update order status and record the change in history.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $newStatus = $request->validated('status');

        DB::transaction(function () use ($order, $newStatus) {
            if ($order->status !== $newStatus) {
                $order->update(['status' => $newStatus]);

                $order->statusHistories()->create([
                    'changed_by_user_id' => auth()->id(),
                    'status' => $newStatus,
                ]);
            }
        });

        $order->load(['user', 'visit', 'items.product', 'statusHistories.changedBy']);

        return $this->successResponse(new OrderResource($order), 'Order status updated successfully');
    }
}

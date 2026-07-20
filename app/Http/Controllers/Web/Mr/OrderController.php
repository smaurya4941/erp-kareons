<?php

namespace App\Http\Controllers\Web\Mr;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display MR's own orders.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['items.product'])
                        ->where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
                                 
        return view('mr.orders.index', compact('orders'));
    }

    /**
     * Display order details for MR.
     */
    public function show(Order $order)
    {
        // Ensure MR can only see their own order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['visit', 'items.product', 'statusHistories.changedBy']);
        return view('mr.orders.show', compact('order'));
    }
}

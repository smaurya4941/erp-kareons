<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != '' && $request->end_date != '') {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(), 
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderBy('created_at', 'desc');
        $orders = $query->paginate($request->get('per_page', 15))->withQueryString();

        $mrs = User::role('MR')->get();

        // Dashboard Stats
        $today = Carbon::today();
        $stats = [
            'today' => Order::whereDate('created_at', $today)->count(),
            'pending' => Order::where('status', 'Pending')->count(),
            'reviewed' => Order::where('status', 'Reviewed')->count(),
            'completed' => Order::where('status', 'Completed')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'mrs', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'visit', 'items.product', 'statusHistories.changedBy']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        try {
            DB::transaction(function () use ($request, $order) {
                $newStatus = $request->validated('status');
                
                if ($order->status !== $newStatus) {
                    $order->update(['status' => $newStatus]);
                    
                    $order->statusHistories()->create([
                        'changed_by_user_id' => auth()->id(),
                        'status' => $newStatus,
                    ]);
                }
            });

            return back()->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update order status.');
        }
    }
}

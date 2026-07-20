@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">My Orders</h2>
    <p class="text-sm text-gray-500">Track the status of orders you collected during doctor visits.</p>
</div>

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Order ID / Date</th>
                    <th class="px-4 py-3">Doctor</th>
                    <th class="px-4 py-3">Products Ordered</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($orders as $order)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-bold text-gray-900">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $order->doctor_name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-blue-600 font-bold">
                        {{ $order->items->sum('quantity') }} units ({{ $order->items->count() }} items)
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-bold rounded-full 
                            {{ $order->status === 'Completed' ? 'bg-green-100 text-green-700' : 
                               ($order->status === 'Reviewed' ? 'bg-indigo-100 text-indigo-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('mr.orders.show', $order) }}" class="text-blue-600 hover:underline text-sm font-medium">View</a>
                    </td>
                </tr>
                @endforeach
                
                @if($orders->isEmpty())
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">You haven't collected any orders yet.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-card>
@endsection

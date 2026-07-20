@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center @media print { hidden }">
    <div>
        <div class="flex items-center text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.reports.hub') }}" class="hover:text-blue-600">Reports Hub</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">Order Report</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Order Report</h2>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 @media print { hidden }">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Orders</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalOrders) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Pending Orders</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($pendingOrders) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Completed Orders</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($completedOrders) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Items Ordered</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalOrderedQuantity) }}</p>
    </div>
</div>

<x-report-filters :mrs="$mrs" :statuses="$statuses" :showStatusFilter="true" />

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap text-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">MR</th>
                    <th class="px-4 py-3">Doctor</th>
                    <th class="px-4 py-3">Products</th>
                    <th class="px-4 py-3 text-center">Total Quantity</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center @media print { hidden }">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse($orders as $order)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $order->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $order->user->name }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-900">{{ $order->doctor_name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->clinic_name }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <ul class="list-disc list-inside text-xs text-gray-600">
                            @foreach($order->items as $item)
                                <li>{{ $item->product->name ?? 'Unknown' }} ({{ $item->quantity }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-gray-800">{{ $order->items->sum('quantity') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs 
                            {{ $order->status === 'Pending' ? 'text-yellow-700 bg-yellow-100' : 
                               ($order->status === 'Reviewed' ? 'text-blue-700 bg-blue-100' : 'text-green-700 bg-green-100') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center @media print { hidden }">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline text-sm font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No orders found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 @media print { hidden }">
        {{ $orders->links() }}
    </div>
</x-card>
@endsection

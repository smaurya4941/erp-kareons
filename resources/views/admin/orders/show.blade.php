@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Order #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <p class="text-sm text-gray-500">Collected on {{ $order->created_at->format('l, d M Y h:i A') }}</p>
    </div>
    <div>
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.orders.index') }}'">Back to Orders</x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Details -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Ordered Items -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 flex items-center justify-between">
                <span>Ordered Products</span>
                <span class="text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $order->items->sum('quantity') }} Total Units</span>
            </h3>
            
            <table class="w-full text-left whitespace-nowrap text-sm">
                <thead>
                    <tr class="text-xs font-semibold text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-2">Product Name</th>
                        <th class="px-4 py-2 text-right">Quantity Requested</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-gray-700">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $item->product->name }}</td>
                        <td class="px-4 py-3 font-bold text-right text-blue-600">{{ $item->quantity }} units</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($order->remarks)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 p-4 rounded text-sm">
                    <h4 class="font-bold text-yellow-800 mb-1">MR Remarks:</h4>
                    <p class="text-yellow-700 whitespace-pre-wrap">{{ $order->remarks }}</p>
                </div>
            @endif
        </x-card>

        <!-- People Details -->
        <div class="grid grid-cols-2 gap-6">
            <x-card>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Medical Representative</h3>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg mr-4">
                        {{ substr($order->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $order->user->name }}</h4>
                        <p class="text-xs text-gray-500">Code: {{ $order->user->employee_code }}</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 flex justify-between items-center">
                    <span>Doctor</span>
                    <a href="{{ route('admin.visits.show', $order->visit->id) }}" class="text-xs text-blue-600 hover:underline">View Visit Report</a>
                </h3>
                <h4 class="font-bold text-gray-800 text-lg">{{ $order->doctor_name }}</h4>
                <p class="text-sm text-gray-600">{{ $order->visit->specialization }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ $order->visit->address ?: $order->visit->area }}</p>
            </x-card>
        </div>
    </div>

    <!-- Right Column: Status & Timeline -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Status Update -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Order Status</h3>
            
            <div class="mb-4">
                <span class="px-3 py-1 text-sm font-bold rounded-full 
                    {{ $order->status === 'Completed' ? 'bg-green-100 text-green-700' : 
                       ($order->status === 'Reviewed' ? 'bg-indigo-100 text-indigo-700' : 'bg-yellow-100 text-yellow-700') }}">
                    Current: {{ $order->status }}
                </span>
            </div>

            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4 border-t pt-4">
                @csrf
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Change Status To:</label>
                <div class="flex items-center space-x-2">
                    <select name="status" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                        <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Reviewed" {{ $order->status == 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <x-button type="submit" variant="primary">Update</x-button>
                </div>
            </form>
        </x-card>

        <!-- Audit Timeline -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Status Timeline</h3>
            
            <div class="relative border-l-2 border-gray-200 ml-3">
                @foreach($order->statusHistories as $history)
                <div class="mb-6 ml-6 relative">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full -left-[1.65rem] top-1.5 border border-white"></div>
                    <div class="text-xs text-gray-500">{{ $history->created_at->format('d M Y, h:i A') }}</div>
                    <div class="font-bold text-gray-800 text-sm mt-1">Status changed to {{ $history->status }}</div>
                    <div class="text-xs text-gray-600 mt-1">by {{ $history->changedBy->name }} ({{ $history->changedBy->hasRole('Admin') ? 'Admin' : 'MR' }})</div>
                </div>
                @endforeach
            </div>
        </x-card>

    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Order Details</h2>
        <p class="text-sm text-gray-500">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} collected on {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>
    <div>
        <x-button variant="secondary" onclick="window.location.href='{{ route('mr.orders.index') }}'" class="w-full sm:w-auto justify-center">Back</x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 flex justify-between items-center">
                <span>Doctor Information</span>
            </h3>
            <h4 class="font-bold text-gray-800 text-lg">{{ $order->doctor_name }}</h4>
            <p class="text-sm text-gray-600">{{ $order->visit->specialization }}</p>
            <p class="text-sm text-gray-600 mt-2">Clinic: {{ $order->visit->clinic_name ?: 'N/A' }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $order->visit->address ?: $order->visit->area }}</p>
        </x-card>

        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Ordered Products</h3>
            
            <table class="w-full text-left whitespace-nowrap text-sm">
                <thead>
                    <tr class="text-xs font-semibold text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-2">Product Name</th>
                        <th class="px-4 py-2 text-right">Quantity</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-gray-700">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $item->product->name }}</td>
                        <td class="px-4 py-3 font-bold text-right text-blue-600">{{ $item->quantity }} units</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($order->remarks)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 p-4 rounded text-sm">
                    <h4 class="font-bold text-yellow-800 mb-1">Your Remarks:</h4>
                    <p class="text-yellow-700 whitespace-pre-wrap">{{ $order->remarks }}</p>
                </div>
            @endif
        </x-card>

    </div>

    <div class="lg:col-span-1">
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Status Tracking</h3>
            
            <div class="mb-6">
                <span class="px-3 py-1 text-sm font-bold rounded-full 
                    {{ $order->status === 'Completed' ? 'bg-green-100 text-green-700' : 
                       ($order->status === 'Reviewed' ? 'bg-indigo-100 text-indigo-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $order->status }}
                </span>
            </div>
            
            <div class="relative border-l-2 border-gray-200 ml-3">
                @foreach($order->statusHistories as $history)
                <div class="mb-6 ml-6 relative">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full -left-[1.65rem] top-1.5 border border-white"></div>
                    <div class="text-xs text-gray-500">{{ $history->created_at->format('d M Y, h:i A') }}</div>
                    <div class="font-bold text-gray-800 text-sm mt-1">Status: {{ $history->status }}</div>
                    <div class="text-xs text-gray-600 mt-1">Updated by {{ $history->changedBy->hasRole('Admin') ? 'Admin' : 'You' }}</div>
                </div>
                @endforeach
            </div>
        </x-card>
    </div>
</div>
@endsection

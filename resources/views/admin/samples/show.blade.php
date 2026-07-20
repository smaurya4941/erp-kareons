@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Sample Ledger: {{ $user->name }}</h2>
        <p class="text-sm text-gray-500">Employee Code: {{ $user->employee_code }}</p>
    </div>
    <div class="flex space-x-3">
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.samples.index') }}'">Back</x-button>
        <x-button variant="primary" onclick="window.location.href='{{ route('admin.samples.create', ['user_id' => $user->id]) }}'">+ Assign More</x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Current Balances -->
    <x-card>
        <x-slot name="header">
            <h3 class="font-semibold text-gray-800">Current Balances</h3>
        </x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3 text-right">Assigned</th>
                        <th class="px-4 py-3 text-right">Distributed</th>
                        <th class="px-4 py-3 text-right">Remaining</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($assignments as $assignment)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <div class="font-medium text-sm text-gray-900">{{ $assignment->product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $assignment->product->product_code }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-sm">{{ $assignment->assigned_quantity }}</td>
                        <td class="px-4 py-3 text-right text-sm">{{ $assignment->distributed_quantity }}</td>
                        <td class="px-4 py-3 text-right font-bold text-sm text-blue-600">{{ $assignment->remaining_quantity }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($assignment->remaining_quantity > 0)
                                <a href="{{ route('admin.samples.adjust.form', ['user' => $user->id, 'product' => $assignment->product_id]) }}" class="text-xs text-red-600 hover:underline border border-red-200 bg-red-50 px-2 py-1 rounded">Adjust</a>
                            @else
                                <span class="text-xs text-gray-400">Empty</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($assignments->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No products assigned yet.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Transaction History -->
    <x-card>
        <x-slot name="header">
            <h3 class="font-semibold text-gray-800">Transaction History</h3>
        </x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3">Reason / By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($transactions as $trx)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ $trx->created_at->format('d M, y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">
                            {{ $trx->product->name }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <span class="px-2 py-1 rounded-full capitalize
                                {{ $trx->type == 'assigned' || $trx->type == 'increased' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $trx->type == 'reduced' || $trx->type == 'returned' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $trx->type == 'adjustment' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $trx->type == 'distributed' ? 'bg-blue-100 text-blue-700' : '' }}
                            ">
                                {{ $trx->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold {{ $trx->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $trx->quantity > 0 ? '+'.$trx->quantity : $trx->quantity }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <div class="text-gray-800">{{ $trx->reason ?? '-' }}</div>
                            <div class="text-gray-400 italic">By: {{ $trx->performer ? $trx->performer->name : 'System' }}</div>
                        </td>
                    </tr>
                    @endforeach
                    @if($transactions->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No transaction history.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </x-card>
</div>
@endsection

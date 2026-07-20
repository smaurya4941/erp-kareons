@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">My Sample Stock</h2>
    <p class="text-sm text-gray-500">View your currently assigned samples for doctor visits.</p>
</div>

{{-- Mobile: card list --}}
<div class="space-y-3 md:hidden">
    @forelse($assignments as $assignment)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ $assignment->product->name }}</p>
                    <p class="text-xs text-gray-500 truncate">Code: {{ $assignment->product->product_code }} · {{ $assignment->product->strength }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-bold rounded-full flex-shrink-0 {{ $assignment->remaining_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $assignment->remaining_quantity }} left
                </span>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-center">
                <div class="bg-gray-50 rounded-lg py-2">
                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Assigned</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $assignment->assigned_quantity }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg py-2">
                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Distributed</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $assignment->distributed_quantity }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500">No samples have been assigned to you yet.</div>
    @endforelse
</div>

<x-card class="hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Product Name</th>
                    <th class="px-4 py-3 text-center">Total Assigned</th>
                    <th class="px-4 py-3 text-center">Distributed</th>
                    <th class="px-4 py-3 text-center">Available Stock</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($assignments as $assignment)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $assignment->product->name }}</div>
                        <div class="text-xs text-gray-500">Code: {{ $assignment->product->product_code }} | {{ $assignment->product->strength }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-medium text-gray-600">
                        {{ $assignment->assigned_quantity }}
                    </td>
                    <td class="px-4 py-3 text-center font-medium text-gray-600">
                        {{ $assignment->distributed_quantity }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 font-bold rounded-full {{ $assignment->remaining_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $assignment->remaining_quantity }}
                        </span>
                    </td>
                </tr>
                @endforeach
                
                @if($assignments->isEmpty())
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No samples have been assigned to you yet.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-card>
@endsection

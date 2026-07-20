@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">My Sample Stock</h2>
    <p class="text-sm text-gray-500">View your currently assigned samples for doctor visits.</p>
</div>

<x-card>
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

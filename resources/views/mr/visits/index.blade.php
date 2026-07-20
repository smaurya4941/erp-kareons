@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">My Doctor Visits</h2>
        <p class="text-sm text-gray-500">History of all your field meetings and discussions.</p>
    </div>
    <div>
        <x-button variant="primary" onclick="window.location.href='{{ route('mr.visits.create') }}'">
            + New Doctor Visit
        </x-button>
    </div>
</div>

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Doctor</th>
                    <th class="px-4 py-3">Area</th>
                    <th class="px-4 py-3 text-center">Products</th>
                    <th class="px-4 py-3 text-center">Samples</th>
                    <th class="px-4 py-3 text-center">Order</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($visits as $visit)
                <tr class="text-gray-700 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='#'">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $visit->date->format('d M, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($visit->time)->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $visit->doctor_name }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->specialization }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $visit->area ?: '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-center text-blue-600">
                        {{ $visit->discussedProducts->count() }}
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-center text-green-600">
                        {{ $visit->distributedSamples->sum('quantity') }}
                    </td>
                    <td class="px-4 py-3 text-center text-xs font-semibold">
                        @if($visit->order)
                            <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Yes</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                
                @if($visits->isEmpty())
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">You haven't recorded any doctor visits yet.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $visits->links() }}
    </div>
</x-card>
@endsection

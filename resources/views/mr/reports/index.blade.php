@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">My Daily Reports</h2>
        <p class="text-sm text-gray-500">History of your end-of-day reports.</p>
    </div>
    <!-- The "End Day" button is conditionally generated here if needed, but normally accessed via Dashboard -->
    <x-button onclick="window.location.href='{{ route('mr.reports.create') }}'" variant="primary">
        End Day / Draft Today's Report
    </x-button>
</div>

@if(session('error'))
<div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
    <p>{{ session('error') }}</p>
</div>
@endif

@if(session('success'))
<div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
    <p>{{ session('success') }}</p>
</div>
@endif

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-center">Visits</th>
                    <th class="px-4 py-3 text-center">Orders</th>
                    <th class="px-4 py-3 text-center">Working Hrs</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($reports as $report)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-bold text-gray-900">{{ $report->date->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $report->date->format('l') }}</div>
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-medium">
                        {{ $report->stats_snapshot['visits']['total_visits'] ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-medium">
                        {{ $report->stats_snapshot['orders']['total_orders'] ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                        {{ $report->stats_snapshot['attendance']['working_hours'] ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-bold rounded-full 
                            {{ $report->status === 'Draft' ? 'bg-gray-100 text-gray-700' : 
                               ($report->status === 'Reviewed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $report->status }}
                        </span>
                        @if($report->status === 'Draft' && $report->date->format('Y-m-d') === \Carbon\Carbon::today()->format('Y-m-d'))
                            <a href="{{ route('mr.reports.create') }}" class="block mt-1 text-xs text-blue-600 hover:underline">Complete Draft</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                
                @if($reports->isEmpty())
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">You haven't submitted any daily reports yet.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</x-card>
@endsection

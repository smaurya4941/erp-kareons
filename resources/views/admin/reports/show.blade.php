@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Daily Report: {{ $report->date->format('d M Y') }}</h2>
        <p class="text-sm text-gray-500">Submitted by {{ $report->user->name }} on {{ $report->updated_at->format('d M Y, h:i A') }}</p>
    </div>
    <div class="flex space-x-2">
        @if($report->status === 'Submitted')
            <form action="{{ route('admin.daily-reports.review', $report) }}" method="POST">
                @csrf
                <x-button type="submit" variant="primary">Mark as Reviewed</x-button>
            </form>
        @else
            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-md font-bold text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Reviewed
            </span>
        @endif
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.daily-reports.index') }}'">Back</x-button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Attendance Summary -->
    <x-card>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Attendance Summary</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-500 uppercase">Check In</p>
                <p class="font-bold text-gray-800">{{ $report->stats_snapshot['attendance']['check_in'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Check Out</p>
                <p class="font-bold text-gray-800">{{ $report->stats_snapshot['attendance']['check_out'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Working Hours</p>
                <p class="font-bold text-blue-600">{{ $report->stats_snapshot['attendance']['working_hours'] ?? 'N/A' }}</p>
            </div>
        </div>
    </x-card>

    <!-- Daily Statistics -->
    <x-card>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Field Activity Stats</h3>
        <div class="grid grid-cols-4 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-500 uppercase">Visits</p>
                <p class="font-bold text-2xl text-gray-800">{{ $report->stats_snapshot['visits']['total_visits'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Products</p>
                <p class="font-bold text-2xl text-gray-800">{{ $report->stats_snapshot['visits']['total_products_discussed'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Samples</p>
                <p class="font-bold text-2xl text-gray-800">{{ $report->stats_snapshot['visits']['total_samples_distributed'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Orders</p>
                <p class="font-bold text-2xl text-gray-800">{{ $report->stats_snapshot['orders']['total_orders'] ?? 0 }}</p>
            </div>
        </div>
    </x-card>
</div>

<div class="space-y-6">
    <x-card>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Today's Summary</h3>
        <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $report->today_summary }}</p>
    </x-card>

    @if($report->problems_faced)
    <x-card>
        <h3 class="text-sm font-semibold text-red-500 uppercase tracking-wider mb-4 border-b border-red-100 pb-2">Problems Faced</h3>
        <p class="text-red-700 whitespace-pre-wrap leading-relaxed">{{ $report->problems_faced }}</p>
    </x-card>
    @endif

    <x-card>
        <h3 class="text-sm font-semibold text-blue-500 uppercase tracking-wider mb-4 border-b border-blue-100 pb-2">Tomorrow's Plan</h3>
        <p class="text-blue-700 whitespace-pre-wrap leading-relaxed">{{ $report->tomorrow_plan }}</p>
    </x-card>
</div>
@endsection

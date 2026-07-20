@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center @media print { hidden }">
    <div>
        <div class="flex items-center text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.reports.hub') }}" class="hover:text-blue-600">Reports Hub</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">MR Performance Report</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">MR Performance Report</h2>
        <p class="text-sm text-gray-500">Period: {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}</p>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 @media print { hidden }">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Average Visits (per MR)</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $avgVisits }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Average Orders (per MR)</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $avgOrders }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Average Working Hours</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $avgWorkingHours }}</p>
    </div>
</div>

<!-- Special filter instance for Performance (No MR dropdown needed since rows ARE MRs) -->
<x-report-filters :showMrFilter="false" :showStatusFilter="false" :showProductFilter="false" />

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap text-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Rank</th>
                    <th class="px-4 py-3">MR Details</th>
                    <th class="px-4 py-3 text-center">Working Days</th>
                    <th class="px-4 py-3 text-center">Total Visits</th>
                    <th class="px-4 py-3 text-center">Total Orders</th>
                    <th class="px-4 py-3 text-center">Samples Distributed</th>
                    <th class="px-4 py-3 text-center">Total Working Hours</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse($users as $index => $mr)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3 text-center font-bold text-gray-500">
                        #{{ $index + 1 }}
                        @if($index === 0) 🏆 @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-900">{{ $mr->name }}</div>
                        <div class="text-xs text-gray-500">{{ $mr->employee_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $mr->working_days }}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $mr->visits_count }}</td>
                    <td class="px-4 py-3 text-center font-bold text-purple-600">{{ $mr->orders_count }}</td>
                    <td class="px-4 py-3 text-center font-bold text-teal-600">{{ $mr->samples_distributed }}</td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-600">{{ $mr->total_working_hours }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No active MRs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
@endsection

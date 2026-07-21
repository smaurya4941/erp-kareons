@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center @media print { hidden }">
    <div>
        <div class="flex items-center text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.reports.hub') }}" class="hover:text-blue-600">Reports Hub</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">Doctor Visit Report</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Doctor Visit Report</h2>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 @media print { hidden }">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Visits</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalVisits) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-indigo-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Unique Doctors</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($uniqueDoctors) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Most Visited Area</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $mostVisitedArea }}</p>
    </div>
</div>

<x-report-filters :mrs="$mrs" :showStatusFilter="false" />

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap text-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">MR</th>
                    <th class="px-4 py-3">Doctor / Clinic</th>
                    <th class="px-4 py-3">Area</th>
                    <th class="px-4 py-3 text-center">Products Discussed</th>
                    <th class="px-4 py-3 text-center">Samples Given</th>
                    <th class="px-4 py-3 text-center">Order Status</th>
                    <th class="px-4 py-3 text-center @media print { hidden }">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse($visits as $visit)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $visit->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $visit->user->name }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-900">{{ $visit->doctor_name }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->clinic_name }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $visit->area }}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $visit->discussedProducts->count() }}</td>
                    <td class="px-4 py-3 text-center font-bold text-teal-600">{{ $visit->distributedSamples->sum('quantity') }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($visit->order)
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs text-green-700 bg-green-100">Order Placed</span>
                        @else
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs text-gray-700 bg-gray-100">No Order</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center @media print { hidden }">
                        <a href="{{ route('admin.visits.show', $visit) }}" class="text-blue-600 hover:underline text-sm font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No doctor visits found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 @media print { hidden }">
        {{ $visits->links() }}
    </div>
</x-card>
@endsection

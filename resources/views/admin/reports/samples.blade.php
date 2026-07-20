@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center @media print { hidden }">
    <div>
        <div class="flex items-center text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.reports.hub') }}" class="hover:text-blue-600">Reports Hub</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">Sample Distribution Report</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Sample Distribution Report</h2>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 @media print { hidden }">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-teal-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Samples Distributed</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalSamples) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-indigo-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Most Distributed Product</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $topProductName }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Top MR</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $topMrName }}</p>
    </div>
</div>

<x-report-filters :mrs="$mrs" :products="$products" :showProductFilter="true" :showStatusFilter="false" />

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap text-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">MR</th>
                    <th class="px-4 py-3">Doctor</th>
                    <th class="px-4 py-3">Area</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3 text-center">Quantity</th>
                    <th class="px-4 py-3 text-center @media print { hidden }">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse($samples as $sample)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $sample->visit->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $sample->visit->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $sample->visit->user->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-900">{{ $sample->visit->doctor_name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $sample->visit->area ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-blue-600">{{ $sample->product->name ?? 'Unknown' }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-gray-800">
                        {{ $sample->quantity }}
                    </td>
                    <td class="px-4 py-3 text-center @media print { hidden }">
                        <a href="{{ route('admin.visits.show', $sample->visit_id) }}" class="text-blue-600 hover:underline text-sm font-medium">View Visit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No samples found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 @media print { hidden }">
        {{ $samples->links() }}
    </div>
</x-card>
@endsection

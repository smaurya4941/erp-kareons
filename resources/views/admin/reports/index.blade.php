@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Daily Reports</h2>
    <p class="text-sm text-gray-500">End-of-day summaries submitted by Medical Representatives.</p>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Reports Today</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['today'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Pending Review</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_review'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Visits Today</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_visits_today'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Orders Today</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_orders_today'] }}</p>
    </div>
</div>

<x-card>
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.daily-reports.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-input type="text" name="search" placeholder="Search MR Name or Code..." value="{{ request('search') }}" />
            </div>
            
            <div class="w-32">
                <select name="status" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Submitted" {{ request('status') == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="Reviewed" {{ request('status') == 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                </select>
            </div>

            <div class="w-40">
                <select name="user_id" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
                    <option value="">All MRs</option>
                    @foreach($mrs as $mr)
                        <option value="{{ $mr->id }}" {{ request('user_id') == $mr->id ? 'selected' : '' }}>{{ $mr->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-32 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" placeholder="Start Date">
                <span class="text-gray-500 mt-1">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-32 mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" placeholder="End Date">
            </div>

            <div>
                <x-button variant="secondary" type="submit">Filter</x-button>
                <a href="{{ route('admin.daily-reports.index') }}" class="ml-2 text-sm text-blue-600 hover:underline">Clear</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">MR Details</th>
                    <th class="px-4 py-3 text-center">Visits</th>
                    <th class="px-4 py-3 text-center">Orders</th>
                    <th class="px-4 py-3 text-center">Working Hrs</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($reports as $report)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-bold text-gray-900">{{ $report->date->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $report->date->format('l') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $report->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $report->user->employee_code }}</div>
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
                            {{ $report->status === 'Reviewed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $report->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.daily-reports.show', $report) }}" class="text-blue-600 hover:underline text-sm font-medium">Review</a>
                    </td>
                </tr>
                @endforeach
                
                @if($reports->isEmpty())
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No reports found.</td>
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

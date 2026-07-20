@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Activity Logs</h2>
        <p class="text-sm text-gray-500">System audit trail and user actions.</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.logs.timeline') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Timeline View
        </a>
    </div>
</div>

<x-card class="mb-6">
    <form method="GET" action="{{ route('admin.logs.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <x-input name="user_search" type="text" placeholder="Search by User or Employee Code..." value="{{ request('user_search') }}" class="w-full" />
        </div>
        <div>
            <select name="module" class="block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                <option value="">All Modules</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input name="start_date" type="date" value="{{ request('start_date') }}" />
        </div>
        <div>
            <x-input name="end_date" type="date" value="{{ request('end_date') }}" />
        </div>
        <div>
            <x-button type="submit" variant="primary">Filter</x-button>
            <a href="{{ route('admin.logs.index') }}" class="ml-2 text-sm text-gray-500 hover:underline">Clear</a>
        </div>
    </form>
</x-card>

<x-card class="p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full whitespace-nowrap">
            <thead>
                <tr class="text-left text-xs font-semibold tracking-wide text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-6 py-3">Date & Time</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Module</th>
                    <th class="px-6 py-3">Action</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3">Severity</th>
                    <th class="px-6 py-3 text-right">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($log->user)
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                    {{ substr($log->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $log->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->user->employee_code }}</div>
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400 italic">System</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">
                            {{ $log->module }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-700">
                        {{ $log->action }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {{ $log->description }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'Information' => 'text-blue-700 bg-blue-50',
                                'Warning' => 'text-yellow-700 bg-yellow-50',
                                'Important' => 'text-orange-700 bg-orange-50',
                                'Critical' => 'text-red-700 bg-red-50'
                            ];
                            $colorClass = $colors[$log->severity] ?? 'text-gray-700 bg-gray-50';
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                            {{ $log->severity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.logs.show', $log) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p>No activity logs found matching the criteria.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    @endif
</x-card>
@endsection

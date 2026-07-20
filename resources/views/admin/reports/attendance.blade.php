@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center @media print { hidden }">
    <div>
        <div class="flex items-center text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.reports.hub') }}" class="hover:text-blue-600">Reports Hub</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">Attendance Report</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Attendance Report</h2>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 @media print { hidden }">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-gray-400">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Total Records</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRecords) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Present</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($presentCount) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Absent</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($absentCount) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Incomplete</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($incompleteCount) }}</p>
    </div>
</div>

<x-report-filters :mrs="$mrs" :statuses="$statuses" :showStatusFilter="true" />

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap text-sm">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Employee</th>
                    <th class="px-4 py-3">Check In</th>
                    <th class="px-4 py-3">Check Out</th>
                    <th class="px-4 py-3 text-center">Working Hours</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse($attendances as $record)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $record->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $record->user->employee_code }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}</td>
                    <td class="px-4 py-3">{{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">
                        @if($record->check_in_time && $record->check_out_time)
                            @php
                                $start = \Carbon\Carbon::parse($record->check_in_time);
                                $end = \Carbon\Carbon::parse($record->check_out_time);
                                $mins = $start->diffInMinutes($end);
                            @endphp
                            {{ floor($mins / 60) }}h {{ $mins % 60 }}m
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs 
                            {{ $record->status === 'Present' ? 'text-green-700 bg-green-100' : 
                               ($record->status === 'Absent' ? 'text-red-700 bg-red-100' : 'text-yellow-700 bg-yellow-100') }}">
                            {{ $record->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No attendance records found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 @media print { hidden }">
        {{ $attendances->links() }}
    </div>
</x-card>
@endsection

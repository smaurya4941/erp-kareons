@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Attendance Register</h2>
    <p class="text-sm text-gray-500">Monitor field force attendance and working hours.</p>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Present Today</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $presentToday }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Incomplete (Working)</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $incompleteToday }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Absent / Not Checked In</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $absentToday }}</p>
    </div>
</div>

<x-card>
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-input type="text" name="search" placeholder="Search MR by name or code..." value="{{ request('search') }}" />
            </div>
            
            <div class="w-40">
                <input type="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->toDateString()) }}" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
            </div>

            <div class="w-40">
                <select name="status" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present</option>
                    <option value="Incomplete" {{ request('status') == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                    <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                </select>
            </div>
            
            <div>
                <x-button variant="secondary" type="submit">Filter</x-button>
                <a href="{{ route('admin.attendance.index') }}" class="ml-2 text-sm text-blue-600 hover:underline">Clear</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">MR Details</th>
                    <th class="px-4 py-3">Check In</th>
                    <th class="px-4 py-3">Check Out</th>
                    <th class="px-4 py-3">Working Hrs</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($attendances as $record)
                <tr class="text-gray-700">
                    <td class="px-4 py-3 text-sm">
                        {{ $record->date->format('d M, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $record->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $record->user->employee_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($record->check_in_time)
                            <div class="font-medium text-gray-900">{{ $record->check_in_time->format('h:i A') }}</div>
                            @if($record->is_late)
                                <span class="text-xs text-red-500 font-semibold">Late</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($record->check_out_time)
                            <div class="font-medium text-gray-900">{{ $record->check_out_time->format('h:i A') }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-medium">
                        {{ $record->formatted_working_hours }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($record->status === 'Present')
                            <x-badge type="success">Present</x-badge>
                        @elseif($record->status === 'Incomplete')
                            <x-badge type="warning">Incomplete</x-badge>
                        @else
                            <x-badge type="danger">Absent</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.attendance.show', $record) }}" class="text-blue-600 hover:underline text-sm font-medium">View</a>
                    </td>
                </tr>
                @endforeach
                
                @if($attendances->isEmpty())
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No attendance records found for this criteria.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
</x-card>
@endsection

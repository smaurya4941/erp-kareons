@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">My Attendance</h2>
        <p class="text-sm text-gray-500">View your attendance history and working hours.</p>
    </div>
    <div>
        <x-button variant="primary" onclick="window.location.href='{{ route('mr.attendance.mark') }}'">
            Mark Attendance Today
        </x-button>
    </div>
</div>

<x-card>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Check In</th>
                    <th class="px-4 py-3">Check Out</th>
                    <th class="px-4 py-3">Working Hours</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($attendances as $record)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium">
                        {{ $record->date->format('d M, Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($record->check_in_time)
                            {{ $record->check_in_time->format('h:i A') }}
                            @if($record->is_late)
                                <span class="ml-2 text-xs text-red-500 font-semibold">(Late)</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $record->check_out_time ? $record->check_out_time->format('h:i A') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold">
                        {{ $record->formatted_working_hours }}
                    </td>
                    <td class="px-4 py-3 text-center text-xs">
                        @if($record->status === 'Present')
                            <x-badge type="success">Present</x-badge>
                        @elseif($record->status === 'Incomplete')
                            <x-badge type="warning">Working</x-badge>
                        @else
                            <x-badge type="danger">Absent</x-badge>
                        @endif
                    </td>
                </tr>
                @endforeach
                
                @if($attendances->isEmpty())
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No attendance history found.</td>
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

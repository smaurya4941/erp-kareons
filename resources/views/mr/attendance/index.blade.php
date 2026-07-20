@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">My Attendance</h2>
        <p class="text-sm text-gray-500">View your attendance history and working hours.</p>
    </div>
    <div>
        <x-button variant="primary" onclick="window.location.href='{{ route('mr.attendance.mark') }}'" class="w-full sm:w-auto">
            Mark Attendance Today
        </x-button>
    </div>
</div>

{{-- Mobile: card list --}}
<div class="space-y-3 md:hidden">
    @forelse($attendances as $record)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <p class="font-bold text-gray-900">{{ $record->date->format('d M, Y') }}</p>
                @if($record->status === 'Present')
                    <x-badge type="success">Present</x-badge>
                @elseif($record->status === 'Incomplete')
                    <x-badge type="warning">Working</x-badge>
                @else
                    <x-badge type="danger">Absent</x-badge>
                @endif
            </div>
            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <div class="bg-gray-50 rounded-lg py-2">
                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">In</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $record->check_in_time ? $record->check_in_time->format('h:i A') : '-' }}
                        @if($record->check_in_time && $record->is_late)<span class="text-[10px] text-red-500">Late</span>@endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg py-2">
                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Out</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $record->check_out_time ? $record->check_out_time->format('h:i A') : '-' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg py-2">
                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Hours</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $record->formatted_working_hours }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500">No attendance history found.</div>
    @endforelse
    <div class="pt-2">{{ $attendances->links() }}</div>
</div>

<x-card class="hidden md:block">
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

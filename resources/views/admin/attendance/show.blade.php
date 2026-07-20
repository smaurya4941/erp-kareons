@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Attendance Details</h2>
        <p class="text-sm text-gray-500">View detailed attendance log for {{ $attendance->date->format('l, d M Y') }}</p>
    </div>
    <div>
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.attendance.index') }}'">Back</x-button>
    </div>
</div>

<!-- Employee Header -->
<x-card class="mb-6">
    <div class="flex items-center">
        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xl mr-4">
            {{ substr($attendance->user->name, 0, 1) }}
        </div>
        <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-800">{{ $attendance->user->name }}</h3>
            <p class="text-gray-500 text-sm">Employee Code: {{ $attendance->user->employee_code }}</p>
        </div>
        <div class="text-right">
            <div class="text-sm text-gray-500">Status</div>
            <div class="mt-1">
                @if($attendance->status === 'Present')
                    <x-badge type="success">Present</x-badge>
                @elseif($attendance->status === 'Incomplete')
                    <x-badge type="warning">Incomplete</x-badge>
                @else
                    <x-badge type="danger">Absent</x-badge>
                @endif
            </div>
        </div>
        <div class="text-right ml-8 border-l pl-8">
            <div class="text-sm text-gray-500">Total Working Hours</div>
            <div class="text-2xl font-bold text-gray-800">{{ $attendance->formatted_working_hours }}</div>
        </div>
    </div>
</x-card>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Check In Details -->
    <x-card>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h4 class="font-bold text-green-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Check In
                </h4>
                <span class="font-bold text-lg text-gray-800">{{ $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : 'N/A' }}</span>
            </div>
            @if($attendance->is_late)
                <span class="text-xs text-red-500 font-medium">Marked Late</span>
            @endif
        </x-slot>

        @if($attendance->check_in_time)
            <!-- Selfie -->
            <div class="mb-4">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Selfie Verification</span>
                <img src="{{ asset('storage/'.$attendance->check_in_selfie) }}" alt="Check In Selfie" class="w-full h-64 object-cover rounded-lg shadow-sm border">
            </div>

            <!-- Location -->
            <div class="mb-4">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Location Data</span>
                @if($attendance->check_in_lat && $attendance->check_in_lng)
                    <div class="bg-gray-50 p-3 rounded border text-sm text-gray-700 mb-2">
                        <div class="flex justify-between mb-1">
                            <span class="font-medium text-gray-500">Coordinates:</span>
                            <span>{{ $attendance->check_in_lat }}, {{ $attendance->check_in_lng }}</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="font-medium text-gray-500">Accuracy:</span>
                            <span class="{{ $attendance->check_in_accuracy > 50 ? 'text-red-500 font-semibold' : 'text-green-600' }}">
                                {{ $attendance->check_in_accuracy }} meters
                            </span>
                        </div>
                        <div class="mt-2">
                            <span class="font-medium text-gray-500 block mb-1">Address:</span>
                            <span>{{ $attendance->check_in_address ?: 'Not provided' }}</span>
                        </div>
                    </div>
                    <!-- Google Map Iframe for Check In -->
                    <iframe 
                        width="100%" 
                        height="200" 
                        frameborder="0" 
                        style="border:0; border-radius: 0.5rem;" 
                        src="https://maps.google.com/maps?q={{ $attendance->check_in_lat }},{{ $attendance->check_in_lng }}&hl=en&z=15&output=embed" 
                        allowfullscreen>
                    </iframe>
                @else
                    <div class="text-sm text-gray-500 italic">Location data not available.</div>
                @endif
            </div>

            <!-- Device Info -->
            <div>
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Device Info</span>
                <div class="text-xs text-gray-600 font-mono bg-gray-100 p-2 rounded overflow-hidden">
                    @if(is_array($attendance->check_in_device_info))
                        @foreach($attendance->check_in_device_info as $key => $value)
                            <div class="mb-1"><span class="font-bold">{{ $key }}:</span> {{ $value }}</div>
                        @endforeach
                    @else
                        No device info recorded.
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">No check-in data.</div>
        @endif
    </x-card>

    <!-- Check Out Details -->
    <x-card>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h4 class="font-bold text-red-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Check Out
                </h4>
                <span class="font-bold text-lg text-gray-800">{{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : 'Pending' }}</span>
            </div>
        </x-slot>

        @if($attendance->check_out_time)
            <!-- Selfie -->
            <div class="mb-4">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Selfie Verification</span>
                <img src="{{ asset('storage/'.$attendance->check_out_selfie) }}" alt="Check Out Selfie" class="w-full h-64 object-cover rounded-lg shadow-sm border">
            </div>

            <!-- Location -->
            <div class="mb-4">
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Location Data</span>
                @if($attendance->check_out_lat && $attendance->check_out_lng)
                    <div class="bg-gray-50 p-3 rounded border text-sm text-gray-700 mb-2">
                        <div class="flex justify-between mb-1">
                            <span class="font-medium text-gray-500">Coordinates:</span>
                            <span>{{ $attendance->check_out_lat }}, {{ $attendance->check_out_lng }}</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="font-medium text-gray-500">Accuracy:</span>
                            <span class="{{ $attendance->check_out_accuracy > 50 ? 'text-red-500 font-semibold' : 'text-green-600' }}">
                                {{ $attendance->check_out_accuracy }} meters
                            </span>
                        </div>
                        <div class="mt-2">
                            <span class="font-medium text-gray-500 block mb-1">Address:</span>
                            <span>{{ $attendance->check_out_address ?: 'Not provided' }}</span>
                        </div>
                    </div>
                    <!-- Google Map Iframe for Check Out -->
                    <iframe 
                        width="100%" 
                        height="200" 
                        frameborder="0" 
                        style="border:0; border-radius: 0.5rem;" 
                        src="https://maps.google.com/maps?q={{ $attendance->check_out_lat }},{{ $attendance->check_out_lng }}&hl=en&z=15&output=embed" 
                        allowfullscreen>
                    </iframe>
                @else
                    <div class="text-sm text-gray-500 italic">Location data not available.</div>
                @endif
            </div>

            <!-- Device Info -->
            <div>
                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Device Info</span>
                <div class="text-xs text-gray-600 font-mono bg-gray-100 p-2 rounded overflow-hidden">
                    @if(is_array($attendance->check_out_device_info))
                        @foreach($attendance->check_out_device_info as $key => $value)
                            <div class="mb-1"><span class="font-bold">{{ $key }}:</span> {{ $value }}</div>
                        @endforeach
                    @else
                        No device info recorded.
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-gray-500 font-medium">Waiting for Check Out...</p>
                <p class="text-xs text-gray-400 mt-2">The MR is currently active in the field.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection

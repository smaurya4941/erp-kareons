@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">MR Dashboard</h2>
    <p class="text-sm text-gray-500">Welcome back, {{ $user->name }}.</p>
</div>

<x-card class="max-w-2xl">
    <x-slot name="header">
        <h4 class="font-semibold text-gray-800">Profile Summary</h4>
    </x-slot>
    
    <div class="flex items-center space-x-6">
        @if($user->photo)
            <img src="{{ asset('storage/'.$user->photo) }}" class="w-24 h-24 rounded-full object-cover">
        @else
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=1d4ed8&background=eff6ff" class="w-24 h-24 rounded-full">
        @endif
        
        <div>
            <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
            <p class="text-gray-600 mb-1">Code: <span class="font-semibold">{{ $user->employee_code }}</span></p>
            <p class="text-gray-600 mb-1">Joining Date: <span class="font-semibold">{{ $user->joining_date ? $user->joining_date->format('d M Y') : 'N/A' }}</span></p>
            <p class="text-gray-600">Status: 
                @if($user->status)
                    <x-badge type="success">Active</x-badge>
                @else
                    <x-badge type="danger">Inactive</x-badge>
                @endif
            </p>
        </div>
    </div>
</x-card>

<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Today's Action Center -->
    <x-card>
        <h4 class="font-semibold text-gray-800 mb-4 border-b pb-2">Today's Action Center</h4>
        
        @php
            $today = \Carbon\Carbon::today()->format('Y-m-d');
            $attendance = \App\Models\Attendance::where('user_id', auth()->id())->where('date', $today)->first();
            $report = \App\Models\DailyReport::where('user_id', auth()->id())->where('date', $today)->first();
        @endphp

        <div class="space-y-4">
            <!-- Step 1: Attendance -->
            <div class="flex items-center justify-between p-3 {{ $attendance ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded">
                <div>
                    <h5 class="font-bold {{ $attendance ? 'text-green-800' : 'text-gray-700' }}">1. Attendance</h5>
                    <p class="text-xs text-gray-500">
                        @if($attendance)
                            Checked in at {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                        @else
                            You haven't checked in yet.
                        @endif
                    </p>
                </div>
                <div>
                    <x-button variant="{{ $attendance ? 'secondary' : 'primary' }}" onclick="window.location.href='{{ route('mr.attendance.index') }}'">
                        {{ $attendance ? 'Manage' : 'Check In Now' }}
                    </x-button>
                </div>
            </div>

            <!-- Step 2: Doctor Visits -->
            <div class="flex items-center justify-between p-3 {{ $attendance && !$attendance->check_out_time ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200 opacity-75' }} border rounded">
                <div>
                    <h5 class="font-bold {{ $attendance && !$attendance->check_out_time ? 'text-blue-800' : 'text-gray-700' }}">2. Field Work</h5>
                    <p class="text-xs text-gray-500">Record doctor visits and orders.</p>
                </div>
                <div>
                    <x-button variant="{{ $attendance && !$attendance->check_out_time ? 'primary' : 'secondary' }}" onclick="window.location.href='{{ route('mr.visits.create') }}'" :disabled="!$attendance || $attendance->check_out_time">
                        New Visit
                    </x-button>
                </div>
            </div>

            <!-- Step 3: End Day Report -->
            <div class="flex items-center justify-between p-3 {{ $attendance && $attendance->check_out_time && (!$report || $report->status === 'Draft') ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200 opacity-75' }} border rounded">
                <div>
                    <h5 class="font-bold {{ $attendance && $attendance->check_out_time ? 'text-yellow-800' : 'text-gray-700' }}">3. End Day Report</h5>
                    <p class="text-xs text-gray-500">
                        @if($report && $report->status !== 'Draft')
                            Submitted successfully.
                        @elseif($attendance && $attendance->check_out_time)
                            Ready to submit.
                        @else
                            Available after checkout.
                        @endif
                    </p>
                </div>
                <div>
                    @if($report && $report->status !== 'Draft')
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-md font-bold text-xs">Completed</span>
                    @else
                        <x-button variant="primary" onclick="window.location.href='{{ route('mr.reports.create') }}'" :disabled="!$attendance || !$attendance->check_out_time">
                            End Day
                        </x-button>
                    @endif
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection

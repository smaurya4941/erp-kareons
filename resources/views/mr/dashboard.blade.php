@extends('layouts.app')

@section('content')
{{-- Greeting header + live attendance status --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center gap-4">
        @if($user->photo)
            <img src="{{ asset('storage/'.$user->photo) }}" class="w-14 h-14 rounded-full object-cover ring-2 ring-brand-100">
        @else
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=1d4ed8&background=eff6ff" class="w-14 h-14 rounded-full">
        @endif
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Hi, {{ \Illuminate\Support\Str::of($user->name)->explode(' ')->first() }} 👋</h2>
            <p class="text-sm text-gray-500">
                {{ \Carbon\Carbon::today()->format('l, d M Y') }} &middot; Code {{ $user->employee_code }}
            </p>
        </div>
    </div>
    <div>
        @if(!$stats['checked_in'])
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 text-gray-600 text-sm font-semibold">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Not checked in
            </span>
        @elseif($stats['checked_out'])
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">
                <span class="w-2 h-2 rounded-full bg-gray-500"></span> Checked out &middot; {{ $stats['working_hours'] ?? '—' }}
            </span>
        @else
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 text-green-800 text-sm font-semibold">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> On duty
            </span>
        @endif
    </div>
</div>

{{-- KPI stat cards (each links to its module) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('mr.visits.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-brand-200 transition-all">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-800">{{ $stats['visits_today'] }}</p>
        <p class="text-xs text-gray-500 font-medium">Visits today</p>
    </a>

    <a href="{{ route('mr.orders.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-brand-200 transition-all">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-800">{{ $stats['orders_today'] }}</p>
        <p class="text-xs text-gray-500 font-medium">Orders today</p>
    </a>

    <a href="{{ route('mr.samples.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-brand-200 transition-all">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-800">{{ $stats['samples_given_today'] }}</p>
        <p class="text-xs text-gray-500 font-medium">Samples given today</p>
    </a>

    <a href="{{ route('mr.samples.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-brand-200 transition-all">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
            @if($sampleStock['low_stock'] > 0)
                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">{{ $sampleStock['low_stock'] }} low</span>
            @endif
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-800">{{ $sampleStock['remaining'] }}</p>
        <p class="text-xs text-gray-500 font-medium">Samples in stock</p>
    </a>
</div>

{{-- Quick actions --}}
<div class="mb-6">
    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Quick Actions</h4>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $quickActions = [
                ['route' => 'mr.attendance.index', 'label' => $stats['checked_in'] ? 'Attendance' : 'Check In', 'color' => 'brand', 'path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route' => 'mr.visits.create', 'label' => 'New Visit', 'color' => 'blue', 'path' => 'M12 4v16m8-8H4'],
                ['route' => 'mr.samples.index', 'label' => 'Give Samples', 'color' => 'amber', 'path' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'],
                ['route' => 'mr.reports.create', 'label' => 'End Day', 'color' => 'emerald', 'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ];
            $colorMap = [
                'brand' => 'bg-brand-50 text-brand-600',
                'blue' => 'bg-blue-50 text-blue-600',
                'amber' => 'bg-amber-50 text-amber-600',
                'emerald' => 'bg-emerald-50 text-emerald-600',
            ];
        @endphp
        @foreach($quickActions as $action)
            <a href="{{ route($action['route']) }}" class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition-all text-center">
                <div class="w-11 h-11 rounded-xl {{ $colorMap[$action['color']] }} flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['path'] }}"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700">{{ $action['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Today's Action Center (guided workflow) --}}
    <div class="lg:col-span-2">
        <x-card>
            <h4 class="font-semibold text-gray-800 mb-4 border-b pb-2">Today's Workflow</h4>
            <div class="space-y-4">
                {{-- Step 1: Attendance --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 {{ $stats['checked_in'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded-xl">
                    <div>
                        <h5 class="font-bold {{ $stats['checked_in'] ? 'text-green-800' : 'text-gray-700' }}">1. Attendance</h5>
                        <p class="text-xs text-gray-500">
                            @if($stats['checked_out'])
                                Checked out &middot; {{ $stats['working_hours'] ?? '—' }} worked
                            @elseif($attendance)
                                Checked in at {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                            @else
                                You haven't checked in yet.
                            @endif
                        </p>
                    </div>
                    <x-button variant="{{ $attendance ? 'secondary' : 'primary' }}" onclick="window.location.href='{{ route('mr.attendance.index') }}'" class="w-full sm:w-auto flex-shrink-0">
                        {{ $attendance ? 'Manage' : 'Check In Now' }}
                    </x-button>
                </div>

                {{-- Step 2: Field Work --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 {{ $attendance && !$stats['checked_out'] ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200 opacity-75' }} border rounded-xl">
                    <div>
                        <h5 class="font-bold {{ $attendance && !$stats['checked_out'] ? 'text-blue-800' : 'text-gray-700' }}">2. Field Work</h5>
                        <p class="text-xs text-gray-500">{{ $stats['visits_today'] }} visits &middot; {{ $stats['orders_today'] }} orders recorded today.</p>
                    </div>
                    <x-button variant="{{ $attendance && !$stats['checked_out'] ? 'primary' : 'secondary' }}" onclick="window.location.href='{{ route('mr.visits.create') }}'" :disabled="!$attendance || $stats['checked_out']" class="w-full sm:w-auto flex-shrink-0">
                        New Visit
                    </x-button>
                </div>

                {{-- Step 3: End Day Report --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 {{ $stats['checked_out'] && (!$report || $report->status === 'Draft') ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200 opacity-75' }} border rounded-xl">
                    <div>
                        <h5 class="font-bold {{ $stats['checked_out'] ? 'text-yellow-800' : 'text-gray-700' }}">3. End Day Report</h5>
                        <p class="text-xs text-gray-500">
                            @if($report && $report->status !== 'Draft')
                                Submitted successfully.
                            @elseif($stats['checked_out'])
                                Ready to submit.
                            @else
                                Available after checkout.
                            @endif
                        </p>
                    </div>
                    @if($report && $report->status !== 'Draft')
                        <span class="inline-flex items-center justify-center px-3 py-1 bg-green-100 text-green-800 rounded-md font-bold text-xs w-full sm:w-auto flex-shrink-0">Completed</span>
                    @else
                        <x-button variant="primary" onclick="window.location.href='{{ route('mr.reports.create') }}'" :disabled="!$stats['checked_out']" class="w-full sm:w-auto flex-shrink-0">
                            End Day
                        </x-button>
                    @endif
                </div>
            </div>
        </x-card>
    </div>

    {{-- Recent visits feed --}}
    <div>
        <x-card>
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h4 class="font-semibold text-gray-800">Recent Visits</h4>
                <a href="{{ route('mr.visits.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View all</a>
            </div>
            @forelse($recentVisits as $visit)
                <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-9 h-9 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0 text-sm font-bold">
                        {{ strtoupper(substr($visit->doctor_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $visit->doctor_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $visit->clinic_name ?: $visit->area ?: 'Field visit' }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($visit->date)->format('d M') }} &middot; {{ $visit->time ? \Carbon\Carbon::parse($visit->time)->format('h:i A') : '' }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-400">No visits recorded yet.</p>
                    <a href="{{ route('mr.visits.create') }}" class="inline-block mt-2 text-xs font-semibold text-brand-600 hover:text-brand-700">Record your first visit →</a>
                </div>
            @endforelse
        </x-card>
    </div>
</div>
@endsection

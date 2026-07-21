@extends('layouts.app')

@section('content')
@php
    $snapshot = $report->stats_snapshot ?? [];
    $att = $snapshot['attendance'] ?? [];

    $statusMap = [
        'Draft'     => ['dot' => 'bg-gray-400',    'text' => 'text-gray-600'],
        'Submitted' => ['dot' => 'bg-blue-500',    'text' => 'text-blue-700'],
        'Reviewed'  => ['dot' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
    ];
    $st = $statusMap[$report->status] ?? $statusMap['Draft'];

    $mrName = $report->user->name ?? auth()->user()->name;
    $initials = collect(explode(' ', trim($mrName)))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');

    $stats = [
        ['label' => 'Working Hours', 'value' => $workingHours,
         'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Doctor Visits', 'value' => $snapshot['visits']['total_visits'] ?? 0,
         'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z'],
        ['label' => 'Samples Given', 'value' => $snapshot['visits']['total_samples_distributed'] ?? 0,
         'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Orders', 'value' => $snapshot['orders']['total_orders'] ?? 0,
         'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
    ];
@endphp

{{-- ============ HEADER ============ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 mb-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center text-base font-bold">
                {{ strtoupper($initials) ?: 'MR' }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ $mrName }}</h2>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $st['text'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>{{ $report->status }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">{{ $report->date->format('l, d M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            @if($checkIn || $checkOut)
                <div class="hidden sm:flex items-center gap-4 text-sm text-gray-500 pr-4 border-r border-gray-100">
                    <span>In <strong class="font-semibold text-gray-700">{{ $checkIn ?? '—' }}</strong></span>
                    <span>Out <strong class="font-semibold text-gray-700">{{ $checkOut ?? '—' }}</strong></span>
                </div>
            @endif
            <x-button variant="secondary" onclick="window.location.href='{{ route('mr.reports.index') }}'" class="justify-center">Back</x-button>
        </div>
    </div>
</div>

{{-- ============ STAT CARDS ============ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @foreach($stats as $s)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-gray-200 transition">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
                </span>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</p>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-3 leading-none tracking-tight">{{ $s['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- ============ PROBLEMS FACED ============ --}}
@if($report->problems_faced)
<div class="mb-5 rounded-2xl border border-gray-100 bg-white shadow-sm p-5">
    <div class="flex items-start gap-3">
        <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </span>
        <div>
            <h3 class="text-sm font-bold text-gray-800">Problems Faced</h3>
            <p class="text-gray-600 whitespace-pre-wrap leading-relaxed mt-1 text-sm">{{ $report->problems_faced }}</p>
        </div>
    </div>
</div>
@endif

{{-- ============ DETAILS ============ --}}
@include('partials.report-details', ['snapshot' => $snapshot])
@endsection

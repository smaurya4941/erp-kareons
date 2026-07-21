@extends('layouts.app')

@section('content')
@php
    $iconBg = fn($type) => [
        'order' => 'bg-indigo-50 text-indigo-500',
        'sample' => 'bg-green-50 text-green-600',
        'report' => 'bg-blue-50 text-blue-500',
    ][$type] ?? 'bg-gray-100 text-gray-500';

    $iconPath = fn($type) => [
        'order' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'sample' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        'report' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ][$type] ?? 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
@endphp

<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Notifications</h2>
        <p class="text-sm text-gray-500">All your recent activity and alerts.</p>
    </div>
    @if($notifications->isNotEmpty())
    <form action="{{ route('notifications.read-all') }}" method="POST">
        @csrf
        <x-button type="submit" variant="secondary" class="w-full sm:w-auto justify-center">Mark all as read</x-button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
    <p>{{ session('success') }}</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @forelse($notifications as $notification)
        @php $data = $notification->data; @endphp
        <a href="{{ route('notifications.read', $notification->id) }}"
            class="flex items-start gap-3 sm:gap-4 px-4 sm:px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-[#5B4CF0]/[0.03]' }}">
            <span class="mt-0.5 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $iconBg($data['type'] ?? 'general') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $iconPath($data['type'] ?? 'general') }}"></path>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-800">{{ $data['title'] ?? 'Notification' }}</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ $data['message'] ?? '' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @unless($notification->read_at)
                <span class="mt-1.5 w-2.5 h-2.5 rounded-full bg-[#5B4CF0] flex-shrink-0" title="Unread"></span>
            @endunless
        </a>
    @empty
        <div class="py-16 px-6 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <p class="text-gray-600 font-medium">No notifications yet</p>
            <p class="text-sm text-gray-400 mt-1">Activity and alerts will show up here.</p>
        </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endif
@endsection

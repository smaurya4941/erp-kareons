@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Activity Timeline</h2>
        <p class="text-sm text-gray-500">Chronological feed of user operations.</p>
    </div>
    <div>
        <a href="{{ route('admin.logs.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Logs Table</a>
    </div>
</div>

<x-card class="mb-6">
    <form method="GET" action="{{ route('admin.logs.timeline') }}" class="flex flex-col md:flex-row gap-4 items-end">
        <div>
            <x-label value="Select User" />
            <select name="user_id" class="block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->employee_code }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-label value="Date" />
            <x-input name="date" type="date" value="{{ request('date', now()->format('Y-m-d')) }}" />
        </div>
        <div>
            <x-button type="submit" variant="primary">View Timeline</x-button>
        </div>
    </form>
</x-card>

<div class="max-w-3xl mx-auto">
    <div class="relative border-l-2 border-blue-200 ml-4 pl-6 space-y-8 pb-12">
        
        @forelse($timelineEvents as $event)
        <div class="relative">
            <!-- Timeline Node -->
            <div class="absolute -left-[35px] top-1 h-5 w-5 rounded-full border-2 border-white bg-blue-500 shadow"></div>
            
            <x-card class="py-3 px-4">
                <div class="flex justify-between items-start mb-1">
                    <div class="font-bold text-gray-900 flex items-center space-x-2">
                        @if($event->user)
                            <span>{{ $event->user->name }}</span>
                        @else
                            <span class="italic">System</span>
                        @endif
                        <span class="text-xs font-normal text-gray-500">&bull; {{ $event->module }}</span>
                    </div>
                    <div class="text-xs text-gray-500 font-medium">
                        {{ $event->created_at->format('h:i A') }}
                    </div>
                </div>
                <div class="text-sm text-blue-600 font-semibold mb-2">
                    {{ $event->action }}
                </div>
                <p class="text-sm text-gray-700">
                    {{ $event->description }}
                </p>
                <div class="mt-3 text-right">
                    <a href="{{ route('admin.logs.show', $event) }}" class="text-xs text-gray-400 hover:text-blue-600">View Details &rarr;</a>
                </div>
            </x-card>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <p>No timeline events recorded for this selection.</p>
        </div>
        @endforelse

    </div>
</div>
@endsection

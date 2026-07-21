@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">My Doctor Visits</h2>
        <p class="text-sm text-gray-500">History of all your field meetings and discussions.</p>
    </div>
    <div class="hidden sm:block">
        @if($attendance && !$attendance->check_out_time)
            <x-button variant="primary" onclick="window.location.href='{{ route('mr.visits.create') }}'">
                + New Doctor Visit
            </x-button>
        @else
            <x-button variant="primary" class="opacity-50 cursor-not-allowed" title="You must be checked in to create a doctor visit." disabled>
                + New Doctor Visit
            </x-button>
        @endif
    </div>
</div>

{{-- Mobile: card list --}}
<div class="space-y-3 md:hidden">
    @forelse($visits as $visit)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 active:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0 font-bold">
                        {{ strtoupper(substr($visit->doctor_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $visit->doctor_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $visit->specialization }}{{ $visit->area ? ' · '.$visit->area : '' }}</p>
                    </div>
                </div>
                @if($visit->order)
                    <span class="text-[10px] font-bold text-yellow-700 bg-yellow-100 px-2 py-1 rounded-full flex-shrink-0">Order</span>
                @endif
            </div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-gray-400">{{ $visit->date->format('d M, Y') }} · {{ \Carbon\Carbon::parse($visit->time)->format('h:i A') }}</span>
                <div class="flex items-center gap-3 font-semibold">
                    <span class="text-blue-600">{{ $visit->discussedProducts->count() }} prod</span>
                    <span class="text-green-600">{{ $visit->distributedSamples->sum('quantity') }} smpl</span>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500">You haven't recorded any doctor visits yet.</div>
    @endforelse
    <div class="pt-2">{{ $visits->links() }}</div>
</div>

{{-- Desktop: table --}}
<x-card class="hidden md:block">
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Doctor</th>
                    <th class="px-4 py-3">Area</th>
                    <th class="px-4 py-3 text-center">Products</th>
                    <th class="px-4 py-3 text-center">Samples</th>
                    <th class="px-4 py-3 text-center">Order</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($visits as $visit)
                <tr class="text-gray-700 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='#'">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $visit->date->format('d M, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($visit->time)->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $visit->doctor_name }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->specialization }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $visit->area ?: '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-center text-blue-600">
                        {{ $visit->discussedProducts->count() }}
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-center text-green-600">
                        {{ $visit->distributedSamples->sum('quantity') }}
                    </td>
                    <td class="px-4 py-3 text-center text-xs font-semibold">
                        @if($visit->order)
                            <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Yes</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach

                @if($visits->isEmpty())
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">You haven't recorded any doctor visits yet.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $visits->links() }}
    </div>
</x-card>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Doctor Visits Log</h2>
    <p class="text-sm text-gray-500">Monitor all field meetings, discussions, and sample distributions.</p>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Total Visits (Filter Date)</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $totalVisitsToday }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Doctors Met</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $uniqueDoctorsToday }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Orders Collected</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $ordersToday }}</p>
    </div>
</div>

<x-card>
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.visits.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-input type="text" name="search" placeholder="Search Doctor, Clinic or MR..." value="{{ request('search') }}" />
            </div>
            
            <div class="w-40">
                <input type="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->toDateString()) }}" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
            </div>

            <div class="w-40">
                <select name="user_id" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
                    <option value="">All MRs</option>
                    @foreach($mrs as $mr)
                        <option value="{{ $mr->id }}" {{ request('user_id') == $mr->id ? 'selected' : '' }}>{{ $mr->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <select name="doctor_response" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" onchange="this.form.submit()">
                    <option value="">All Responses</option>
                    <option value="Interested" {{ request('doctor_response') == 'Interested' ? 'selected' : '' }}>Interested</option>
                    <option value="Not Interested" {{ request('doctor_response') == 'Not Interested' ? 'selected' : '' }}>Not Interested</option>
                    <option value="Will Think" {{ request('doctor_response') == 'Will Think' ? 'selected' : '' }}>Will Think</option>
                    <option value="Requested Follow-up" {{ request('doctor_response') == 'Requested Follow-up' ? 'selected' : '' }}>Requested Follow-up</option>
                </select>
            </div>
            
            <div>
                <x-button variant="secondary" type="submit">Filter</x-button>
                <a href="{{ route('admin.visits.index') }}" class="ml-2 text-sm text-blue-600 hover:underline">Clear</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Date/Time</th>
                    <th class="px-4 py-3">MR Details</th>
                    <th class="px-4 py-3">Doctor</th>
                    <th class="px-4 py-3">Area</th>
                    <th class="px-4 py-3">Response</th>
                    <th class="px-4 py-3">Products Dis.</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($visits as $visit)
                <tr class="text-gray-700">
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $visit->date->format('d M, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($visit->time)->format('h:i A') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $visit->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->user->employee_code }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $visit->doctor_name }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->specialization }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $visit->area ?: '-' }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full 
                            {{ $visit->doctor_response === 'Interested' ? 'bg-green-100 text-green-700' : 
                               ($visit->doctor_response === 'Not Interested' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $visit->doctor_response }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-center">
                        {{ $visit->discussedProducts->count() }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.visits.show', $visit) }}" class="text-blue-600 hover:underline text-sm font-medium">Details</a>
                    </td>
                </tr>
                @endforeach
                
                @if($visits->isEmpty())
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No visits found for this criteria.</td>
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

@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Sample Assignment</h2>
    <x-button variant="primary" onclick="window.location.href='{{ route('admin.samples.create') }}'">
        + Assign Samples
    </x-button>
</div>

<x-card>
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.samples.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-input type="text" name="search" placeholder="Search MR by name or code..." value="{{ request('search') }}" />
            </div>
            <div>
                <x-button variant="secondary" type="submit">Search</x-button>
                <a href="{{ route('admin.samples.index') }}" class="ml-2 text-sm text-blue-600 hover:underline">Clear</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">MR Details</th>
                    <th class="px-4 py-3 text-center">Total Assigned</th>
                    <th class="px-4 py-3 text-center">Total Distributed</th>
                    <th class="px-4 py-3 text-center">Net Remaining</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @foreach($users as $user)
                <tr class="text-gray-700 hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                            <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                        </div>
                        <div class="text-xs text-gray-500">Code: {{ $user->employee_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-medium text-blue-600">
                        {{ $user->total_assigned ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-center font-medium text-green-600">
                        {{ $user->total_distributed ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-gray-800">
                        {{ ($user->total_assigned ?? 0) - ($user->total_distributed ?? 0) }}
                    </td>
                    <td class="px-4 py-3 text-sm flex justify-center space-x-3 items-center mt-2">
                        <a href="{{ route('admin.samples.show', $user) }}" class="px-3 py-1 text-sm text-white bg-gray-800 rounded hover:bg-gray-700">
                            View Ledger
                        </a>
                        <a href="{{ route('admin.samples.create', ['user_id' => $user->id]) }}" class="px-3 py-1 text-sm text-blue-600 bg-blue-100 rounded hover:bg-blue-200">
                            Assign More
                        </a>
                    </td>
                </tr>
                @endforeach
                
                @if($users->isEmpty())
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No MRs found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-card>
@endsection

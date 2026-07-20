@props([
    'mrs' => [],
    'products' => [],
    'statuses' => [],
    'showMrFilter' => true,
    'showProductFilter' => false,
    'showStatusFilter' => false,
    'showDateFilter' => true,
    'showSearch' => true,
    'exportRoute' => null
])

<x-card class="mb-6 @media print { hidden }">
    <form method="GET" action="{{ url()->current() }}" id="report-filter-form" class="space-y-4">
        
        <div class="flex flex-wrap gap-4 items-end">
            
            @if($showSearch)
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Search</label>
                <x-input type="text" name="search" placeholder="Search keywords..." value="{{ request('search') }}" />
            </div>
            @endif

            @if($showDateFilter)
            <div class="w-40">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date Range</label>
                <select name="date_filter" id="date_filter" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('date_filter') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ request('date_filter', 'last_7_days') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            
            <!-- Custom Date Range Fields (Hidden by default) -->
            <div id="custom_date_fields" class="flex items-center space-x-2 {{ request('date_filter') == 'custom' ? '' : 'hidden' }}">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Start</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-36 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                </div>
                <div class="mt-5 text-gray-400">-</div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">End</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-36 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                </div>
            </div>
            @endif

            @if($showMrFilter)
            <div class="w-48">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Medical Rep (MR)</label>
                <select name="user_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                    <option value="">All MRs</option>
                    @foreach($mrs as $mr)
                        <option value="{{ $mr->id }}" {{ request('user_id') == $mr->id ? 'selected' : '' }}>{{ $mr->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($showProductFilter)
            <div class="w-48">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Product</label>
                <select name="product_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($showStatusFilter)
            <div class="w-40">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-4">
            <div class="flex items-center space-x-2">
                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Apply Filters
                </x-button>
                <a href="{{ url()->current() }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Reset</a>
            </div>

            <div class="flex items-center space-x-2">
                <x-button variant="secondary" type="button" onclick="window.print()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print
                </x-button>
                
                <button type="submit" name="export" value="csv" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export CSV
                </button>
            </div>
        </div>
    </form>
</x-card>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFilter = document.getElementById('date_filter');
        const customFields = document.getElementById('custom_date_fields');

        if(dateFilter) {
            dateFilter.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customFields.classList.remove('hidden');
                } else {
                    customFields.classList.add('hidden');
                }
            });
        }
    });
</script>

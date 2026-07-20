@extends('layouts.app')

@section('content')
<div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Order Management</h2>
        <p class="text-sm font-medium text-gray-500 mt-1">Track and manage orders collected by MRs during field visits.</p>
    </div>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover relative overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-blue-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Received Today</h3>
            <p class="text-3xl font-black text-gray-900">{{ $stats['today'] }}</p>
        </div>
    </div>
    <div class="group bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 shadow-sm border border-amber-100 card-hover relative overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-amber-200 to-transparent rounded-bl-full opacity-30 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <h3 class="text-[11px] font-bold text-amber-700 uppercase tracking-widest mb-1">Pending</h3>
            <p class="text-3xl font-black text-amber-900">{{ $stats['pending'] }}</p>
        </div>
    </div>
    <div class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover relative overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-indigo-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Reviewed</h3>
            <p class="text-3xl font-black text-gray-900">{{ $stats['reviewed'] }}</p>
        </div>
    </div>
    <div class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover relative overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-green-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Completed</h3>
            <p class="text-3xl font-black text-gray-900">{{ $stats['completed'] }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" placeholder="Search Doctor or MR..." value="{{ request('search') }}" 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 sm:text-sm transition-all shadow-sm">
            </div>
            
            <div class="w-32 relative">
                <select name="status" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 bg-white shadow-sm appearance-none transition-all cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Reviewed" {{ request('status') == 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
            </div>

            <div class="w-40 relative">
                <select name="user_id" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 bg-white shadow-sm appearance-none transition-all cursor-pointer" onchange="this.form.submit()">
                    <option value="">All MRs</option>
                    @foreach($mrs as $mr)
                        <option value="{{ $mr->id }}" {{ request('user_id') == $mr->id ? 'selected' : '' }}>{{ $mr->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
            </div>
            
            <div class="flex items-center space-x-2 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-32 border-0 text-sm focus:ring-0 text-gray-600 bg-transparent py-1.5 cursor-pointer" placeholder="Start Date">
                <span class="text-gray-300 font-bold">&rarr;</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-32 border-0 text-sm focus:ring-0 text-gray-600 bg-transparent py-1.5 cursor-pointer" placeholder="End Date">
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-xl font-medium text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition-all shadow-sm">
                    Filter
                </button>
                @if(request('search') || request('status') || request('user_id') || request('start_date'))
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-500 hover:text-brand-600 transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap text-sm">
            <thead>
                <tr class="text-[10px] font-extrabold tracking-wider text-gray-400 uppercase bg-gray-50/50">
                    <th class="px-6 py-4">Order Info</th>
                    <th class="px-6 py-4">MR Representative</th>
                    <th class="px-6 py-4">Doctor</th>
                    <th class="px-6 py-4">Volume</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @foreach($orders as $order)
                <tr class="hover:bg-brand-50/30 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 font-mono tracking-tight group-hover:text-brand-700 transition-colors">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-[11px] text-gray-400 font-medium mt-0.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-200 to-brand-100 text-brand-700 flex items-center justify-center font-bold text-xs mr-3 shadow-sm border border-brand-200">
                                {{ substr($order->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-[10px] text-gray-400 tracking-wider">ID: {{ $order->user->employee_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-700 flex items-center gap-2">
                            <div class="p-1.5 bg-blue-50 text-blue-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            {{ $order->doctor_name }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-700">{{ $order->items->sum('quantity') }} Units</div>
                        <div class="text-[11px] text-gray-500 font-medium">{{ $order->items->count() }} Products</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($order->status === 'Completed')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Completed
                            </span>
                        @elseif($order->status === 'Reviewed')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Reviewed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span> Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 hover:border-brand-200 transition-all shadow-sm">
                            Manage
                            <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </td>
                </tr>
                @endforeach
                
                @if($orders->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <p class="text-sm font-medium text-gray-500">No orders found.</p>
                            <p class="text-xs mt-1">Adjust your filters or date range.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="p-6 border-t border-gray-50 bg-gray-50/30">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard Overview</h2>
        <p class="text-sm font-medium text-gray-500 mt-1">Real-time metrics and MR performance tracking.</p>
    </div>
    
    <!-- Date Filters -->
    <div class="flex items-center space-x-3 bg-white p-1.5 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center space-x-2">
            <select name="date_filter" class="text-sm border-0 bg-gray-50 text-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500 cursor-pointer font-medium" onchange="this.form.submit()">
                <option value="today" {{ $current_filter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $current_filter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="last_7_days" {{ $current_filter == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="this_month" {{ $current_filter == 'this_month' ? 'selected' : '' }}>This Month</option>
            </select>
            <button type="button" onclick="window.location.reload()" class="p-2 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors" title="Refresh">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
            <button type="button" onclick="window.print()" class="btn-primary py-2 px-3 flex items-center shadow-brand-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Export
            </button>
        </form>
    </div>
</div>

<div class="mb-6 flex items-center space-x-2 text-sm">
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800">
        Active Period
    </span>
    <p class="font-semibold text-gray-600">{{ $start_date }} @if($start_date != $end_date) <span class="text-gray-400 mx-1">&rarr;</span> {{ $end_date }} @endif</p>
</div>

<!-- Section 1: KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-10">
    <!-- Present MRs -->
    <a href="{{ route('admin.attendance.index') }}" class="group relative bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-green-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Present MRs</h3>
                <div class="flex items-baseline space-x-2">
                    <p class="text-3xl font-black text-gray-900">{{ $kpis['present_mrs'] }}</p>
                    <p class="text-sm font-medium text-gray-400">/ {{ $kpis['total_mrs'] }}</p>
                </div>
            </div>
        </div>
    </a>
    
    <!-- Absent MRs -->
    <a href="{{ route('admin.attendance.index') }}" class="group relative bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-red-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <div>
                <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Absent MRs</h3>
                <p class="text-3xl font-black text-gray-900">{{ $kpis['absent_mrs'] }}</p>
            </div>
        </div>
    </a>
    
    <!-- Doctor Visits -->
    <a href="{{ route('admin.visits.index') }}" class="group relative bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover overflow-hidden lg:col-span-1 xl:col-span-1">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-blue-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Doctor Visits</h3>
                <p class="text-3xl font-black text-gray-900">{{ $kpis['total_visits'] }}</p>
            </div>
        </div>
    </a>
    
    <!-- Total Orders -->
    <a href="{{ route('admin.orders.index') }}" class="group relative bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-purple-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Total Orders</h3>
                <p class="text-3xl font-black text-gray-900">{{ $kpis['total_orders'] }}</p>
            </div>
        </div>
    </a>

    <!-- Pending Orders -->
    <a href="{{ route('admin.orders.index', ['status' => 'Pending']) }}" class="group relative bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-5 shadow-sm border border-orange-100 card-hover overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-orange-200 to-transparent rounded-bl-full opacity-30 transition-transform group-hover:scale-110"></div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="p-3 bg-orange-100 text-orange-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-[11px] font-bold text-orange-800 uppercase tracking-widest">Pending Orders</h3>
                <p class="text-3xl font-black text-orange-900">{{ $kpis['pending_orders'] }}</p>
            </div>
        </div>
    </a>

    <!-- Samples Dist -->
    <div class="group relative bg-white rounded-2xl p-5 shadow-sm border border-gray-100 overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-teal-100 to-transparent rounded-bl-full opacity-50"></div>
        <div class="flex items-center space-x-4 relative z-10">
            <div class="p-3 bg-teal-50 text-teal-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <div>
                <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Samples Dist.</h3>
                <p class="text-3xl font-black text-gray-900">{{ $kpis['samples_distributed'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Trend Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    <!-- Field Activity Trend (Visits & Orders) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                    Field Activity Trend
                </h3>
                <span class="text-[11px] font-semibold text-gray-400">Daily visits &amp; orders</span>
            </div>
            <div class="p-6">
                <div class="relative" style="height: 300px;">
                    <canvas id="activityTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Distribution -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Samples Distributed
                </h3>
            </div>
            <div class="p-6">
                <div class="relative" style="height: 300px;">
                    <canvas id="sampleTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Trend -->
<div class="mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                Attendance Trend
            </h3>
            <span class="text-[11px] font-semibold text-gray-400">Present MRs per day</span>
        </div>
        <div class="p-6">
            <div class="relative" style="height: 260px;">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    <!-- Top Performing MRs -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                    Top Performing MRs
                </h3>
                <a href="{{ route('admin.reports.hub') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800 transition-colors flex items-center">
                    Full Reports
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap text-sm">
                    <thead>
                        <tr class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider bg-gray-50/50">
                            <th class="px-6 py-4">MR Name</th>
                            <th class="px-6 py-4 text-center">Visits</th>
                            <th class="px-6 py-4 text-center">Orders</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        @forelse($top_mrs as $mr)
                        <tr class="hover:bg-brand-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-200 to-brand-100 text-brand-700 flex items-center justify-center font-bold text-xs mr-3">
                                        {{ substr($mr->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $mr->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $mr->employee_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    {{ $mr->visits_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                    {{ $mr->orders_count }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                No MR activity found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activities Timeline -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    Live Feed
                </h3>
            </div>
            
            <div class="relative p-6 flex-1 overflow-y-auto" style="max-height: 400px;">
                <div class="absolute left-8 top-6 bottom-6 w-px bg-gray-100"></div>
                
                @forelse($recent_activities as $activity)
                <div class="relative flex items-start mb-6 last:mb-0 group">
                    <div class="absolute left-2 top-1.5 w-3 h-3 bg-{{ $activity['color'] }}-400 rounded-full border-2 border-white shadow-sm transition-transform group-hover:scale-125"></div>
                    <div class="ml-10">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">{{ $activity['time']->diffForHumans() }}</div>
                        <div class="font-medium text-gray-800 text-sm mt-0.5 leading-snug">{{ $activity['message'] }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-400 text-sm italic mt-10">
                    No recent activity.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const chartData = @json($chart_data);

        // Skip gracefully if Chart.js failed to load (e.g. offline).
        if (typeof Chart === 'undefined') { return; }

        Chart.defaults.font.family = "'Inter', ui-sans-serif, system-ui, sans-serif";
        Chart.defaults.color = '#6b7280';

        const gridColor = 'rgba(0,0,0,0.05)';
        const baseScales = {
            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 12 } },
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { precision: 0 } }
        };

        function makeGradient(ctx, area, hex) {
            const g = ctx.createLinearGradient(0, area.top, 0, area.bottom);
            g.addColorStop(0, hex + '55');
            g.addColorStop(1, hex + '00');
            return g;
        }

        // Field Activity Trend — Visits & Orders
        const actCanvas = document.getElementById('activityTrendChart');
        if (actCanvas) {
            new Chart(actCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Doctor Visits',
                            data: chartData.visits,
                            borderColor: '#3b82f6',
                            backgroundColor: (c) => c.chart.chartArea ? makeGradient(c.chart.ctx, c.chart.chartArea, '#3b82f6') : 'transparent',
                            borderWidth: 2, tension: 0.35, fill: true,
                            pointRadius: 2, pointHoverRadius: 5, pointBackgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Orders',
                            data: chartData.orders,
                            borderColor: '#8b5cf6',
                            backgroundColor: (c) => c.chart.chartArea ? makeGradient(c.chart.ctx, c.chart.chartArea, '#8b5cf6') : 'transparent',
                            borderWidth: 2, tension: 0.35, fill: true,
                            pointRadius: 2, pointHoverRadius: 5, pointBackgroundColor: '#8b5cf6'
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } } },
                    scales: baseScales
                }
            });
        }

        // Sample Distribution — bar
        const sampleCanvas = document.getElementById('sampleTrendChart');
        if (sampleCanvas) {
            new Chart(sampleCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Samples',
                        data: chartData.samples,
                        backgroundColor: '#14b8a6',
                        hoverBackgroundColor: '#0d9488',
                        borderRadius: 6, maxBarThickness: 26
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }

        // Attendance Trend — present MRs
        const attCanvas = document.getElementById('attendanceTrendChart');
        if (attCanvas) {
            new Chart(attCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Present MRs',
                        data: chartData.present,
                        borderColor: '#22c55e',
                        backgroundColor: (c) => c.chart.chartArea ? makeGradient(c.chart.ctx, c.chart.chartArea, '#22c55e') : 'transparent',
                        borderWidth: 2, tension: 0.35, fill: true,
                        pointRadius: 2, pointHoverRadius: 5, pointBackgroundColor: '#22c55e'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: baseScales
                }
            });
        }
    })();
</script>
@endpush

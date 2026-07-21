@extends('layouts.app')

@section('content')
<div class="relative w-full max-w-7xl mx-auto space-y-3 sm:space-y-4 animate-fade-in-up pb-16 lg:pb-0 px-2 lg:px-0">

    {{-- Welcome Section --}}
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="flex-1 space-y-0.5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight leading-none">
                {{ $greeting }}, {{ \Illuminate\Support\Str::of($user->name)->explode(' ')->first() }} <span class="wave">👋</span>
            </h1>
            <p class="text-gray-500 text-[11px] max-w-xl">
                {{ \Carbon\Carbon::today()->format('l, j F Y') }} &middot; Route: <span class="font-semibold text-gray-700">{{ $userRoute }}</span>
            </p>
        </div>
    </section>

    {{-- Premium Statistics Cards --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $statCards = [
                ['label' => 'Visits Today', 'value' => $stats['visits_today'], 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => 'from-[#5B4CF0] to-[#8B5CF6]', 'trend' => $trends['visits']['label'], 'trendUp' => $trends['visits']['up'], 'route' => route('mr.visits.index')],
                ['label' => 'Orders Today', 'value' => $stats['orders_today'], 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'from-[#10B981] to-emerald-400', 'trend' => $trends['orders']['label'], 'trendUp' => $trends['orders']['up'], 'route' => route('mr.orders.index')],
                ['label' => 'Samples', 'value' => $stats['samples_given_today'], 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => 'from-[#F59E0B] to-amber-400', 'trend' => $trends['samples']['label'], 'trendUp' => $trends['samples']['up'], 'route' => route('mr.samples.index')],
                ['label' => 'Hours', 'value' => $stats['working_hours'] ?? '0h', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'from-indigo-400 to-cyan-400', 'trend' => $trends['hours']['label'], 'trendUp' => $trends['hours']['up'], 'route' => route('mr.attendance.index')],
            ];
        @endphp
        @foreach($statCards as $card)
            <a href="{{ $card['route'] }}" class="group relative bg-white p-3 sm:p-4 rounded-[16px] shadow-[0_2px_15px_rgb(0,0,0,0.02)] border border-gray-100/50 hover:shadow-[0_8px_25px_rgb(0,0,0,0.05)] hover:-translate-y-0.5 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow text-white transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"></path></svg>
                    </div>
                    <span class="flex items-center gap-0.5 text-[9px] font-bold {{ $card['trendUp'] ? 'text-[#10B981] bg-[#10B981]/10' : 'text-[#EF4444] bg-[#EF4444]/10' }} px-1.5 py-0.5 rounded-full">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['trendUp'] ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"></path></svg>
                        {{ $card['trend'] }}
                    </span>
                </div>
                <div>
                    <h3 class="text-gray-400 text-[10px] font-bold tracking-wide uppercase mb-0.5">{{ $card['label'] }}</h3>
                    <p class="text-xl font-black text-gray-900 leading-none tracking-tight">{{ $card['value'] }}</p>
                </div>
                <!-- Decorative background shape -->
                <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-gradient-to-br {{ $card['color'] }} opacity-[0.03] rounded-full blur-md group-hover:opacity-10 transition-opacity"></div>
            </a>
        @endforeach
    </section>

    {{-- Main 3-Column Split --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
        
        {{-- Left Side: Quick Actions & Analytics --}}
        <div class="lg:col-span-2 space-y-3 sm:space-y-4 flex flex-col min-h-0">
            {{-- Quick Actions --}}
            <section class="bg-white rounded-[16px] p-3 sm:p-4 shadow-[0_2px_15px_rgb(0,0,0,0.02)] border border-gray-100 flex-shrink-0">
                <div class="grid grid-cols-4 gap-2">
                    @php
                        $quickActions = [
                            ['route' => 'mr.visits.create', 'label' => 'Start Visit', 'icon' => 'M12 4v16m8-8H4', 'gradient' => 'from-[#5B4CF0] to-[#8B5CF6]'],
                            ['route' => 'mr.attendance.index', 'label' => $stats['checked_in'] ? 'Attendance' : 'Check In', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'gradient' => 'from-[#10B981] to-emerald-500'],
                            ['route' => 'mr.samples.index', 'label' => 'Assign Samples', 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7', 'gradient' => 'from-[#F59E0B] to-orange-400'],
                            ['route' => 'mr.reports.index', 'label' => 'Daily Report', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5', 'gradient' => 'from-indigo-400 to-blue-500'],
                        ];
                    @endphp
                    @foreach($quickActions as $action)
                        @php
                            $isDisabled = false;
                            if ($action['route'] === 'mr.visits.create' && (!isset($attendance) || !$attendance || $stats['checked_out'])) {
                                $isDisabled = true;
                            }
                        @endphp
                        @if($isDisabled)
                            <div class="flex flex-col items-center justify-center gap-1.5 bg-gray-50 rounded-xl border border-gray-100 p-2 opacity-50 cursor-not-allowed text-center">
                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center shadow-inner">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"></path></svg>
                                </div>
                                <span class="text-[9px] font-bold text-gray-400 leading-tight">{{ $action['label'] }}</span>
                            </div>
                        @else
                            <a href="{{ $action['route'] !== '#' ? route($action['route']) : '#' }}" class="group flex flex-col items-center justify-center gap-1.5 bg-white rounded-xl border border-gray-100 hover:border-transparent p-2 hover:shadow-[0_4px_12px_rgb(0,0,0,0.04)] active:scale-95 transition-all duration-300 text-center relative overflow-hidden z-10">
                                <div class="absolute inset-0 bg-gradient-to-b {{ $action['gradient'] }} opacity-0 group-hover:opacity-10 transition-opacity duration-300 -z-10"></div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $action['gradient'] }} text-white flex items-center justify-center shadow-sm group-hover:shadow transform group-hover:-translate-y-0.5 transition-all duration-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"></path></svg>
                                </div>
                                <span class="text-[9px] font-extrabold text-gray-600 group-hover:text-gray-900 transition-colors leading-tight">{{ $action['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>

            {{-- Analytics Section --}}
            <section class="bg-white rounded-[16px] p-4 sm:p-5 shadow-[0_2px_15px_rgb(0,0,0,0.02)] border border-gray-100 flex-1 flex flex-col min-h-[200px] relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#5B4CF0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Weekly Performance
                    </h4>
                    <select class="text-[9px] font-bold border border-gray-200 bg-white text-gray-600 rounded-lg focus:ring-0 cursor-pointer py-1 px-2 hover:bg-gray-50 transition-colors shadow-sm outline-none">
                        <option>This Week</option>
                        <option>Last Week</option>
                    </select>
                </div>
                <div class="flex-1 w-full relative">
                    <canvas id="performanceChart"></canvas>
                </div>
            </section>
        </div>

        {{-- Right Side: Timeline & Tasks --}}
        <div class="space-y-3 sm:space-y-4 flex flex-col h-full">
            {{-- Today's Timeline --}}
            <section class="bg-white rounded-[16px] p-4 sm:p-5 shadow-[0_2px_15px_rgb(0,0,0,0.02)] border border-gray-100 flex-1 relative overflow-hidden">
                <h4 class="text-xs font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#10B981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Today's Timeline
                </h4>
                
                <div class="relative border-l-2 border-gray-100 ml-2 space-y-4 pb-2">
                    @forelse($timelineEvents as $event)
                        <div class="relative pl-5">
                            <div class="absolute -left-[9px] top-1 w-3.5 h-3.5 rounded-full ring-2 ring-white shadow-sm" style="background-color: {{ $event['color'] }}"></div>
                            <div class="flex items-center justify-between mb-0.5">
                                <h5 class="text-[11px] font-bold text-gray-800">{{ $event['title'] }}</h5>
                                <span class="text-[9px] font-bold" style="color: {{ $event['color'] }}">{{ $event['time'] }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 leading-snug">{{ $event['description'] }}</p>
                        </div>
                    @empty
                        <div class="py-4 text-center">
                            <p class="text-[10px] text-gray-400 font-bold">No activity yet today. Start by checking in!</p>
                        </div>
                    @endforelse
                </div>
                <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-gradient-to-b from-gray-50 to-transparent rounded-full opacity-50 pointer-events-none"></div>
            </section>

            {{-- Pending Tasks --}}
            <section class="bg-white rounded-[16px] p-4 sm:p-5 shadow-[0_2px_15px_rgb(0,0,0,0.02)] border border-gray-100">
                <h4 class="text-xs font-bold text-gray-900 mb-3 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#F59E0B]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Pending Tasks
                    </span>
                    @if($pendingTasks->count() > 0)
                        <span class="bg-amber-100 text-amber-700 text-[9px] font-bold px-1.5 py-0.5 rounded-md">{{ $pendingTasks->count() }}</span>
                    @endif
                </h4>
                <div class="space-y-1.5">
                    @forelse($pendingTasks as $task)
                        <div class="group flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-colors cursor-pointer">
                            <div class="mt-0.5 w-3.5 h-3.5 rounded border-2 border-gray-300 group-hover:border-[#5B4CF0] transition-colors flex-shrink-0"></div>
                            <div>
                                <h5 class="text-[10px] font-bold text-gray-800 leading-tight">{{ $task['title'] }}</h5>
                                <p class="text-[9px] text-gray-500 mt-0.5 leading-tight">{{ $task['description'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-2">
                            <p class="text-[10px] text-gray-400 font-bold">All caught up!</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Chart.js
        if(document.getElementById('performanceChart')) {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            // Gradient for line chart
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(91, 76, 240, 0.2)');
            gradient.addColorStop(1, 'rgba(91, 76, 240, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($weeklyChartLabels) !!},
                    datasets: [{
                        label: 'Visits',
                        data: {!! json_encode($weeklyChartData) !!},
                        borderColor: '#5B4CF0',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#5B4CF0',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 12,
                            titleFont: { size: 13, family: 'Inter' },
                            bodyFont: { size: 14, family: 'Inter', weight: 'bold' },
                            displayColors: false,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            border: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 }, color: '#9ca3af', padding: 10 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            border: { display: false },
                            ticks: { font: { family: 'Inter', size: 12 }, color: '#6b7280', padding: 10 }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' },
                }
            });
        }
    });
</script>
<style>
    /* Custom Keyframes & Utility classes */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .wave {
        display: inline-block;
        animation: wave-animation 2.5s infinite;
        transform-origin: 70% 70%;
    }
    @keyframes wave-animation {
        0% { transform: rotate( 0.0deg) }
        10% { transform: rotate(14.0deg) }  
        20% { transform: rotate(-8.0deg) }
        30% { transform: rotate(14.0deg) }
        40% { transform: rotate(-4.0deg) }
        50% { transform: rotate(10.0deg) }
        60% { transform: rotate( 0.0deg) }
        100% { transform: rotate( 0.0deg) }
    }
</style>
@endpush

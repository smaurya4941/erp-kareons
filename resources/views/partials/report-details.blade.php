{{--
    Detailed breakdown of an MR's auto-generated daily report.
    Expects: $snapshot (array) = $report->stats_snapshot
    Shared by both the MR and Admin report views. No route dependencies.
--}}
@php
    $visitDetails = $snapshot['details']['visits'] ?? [];
    $orderDetails = $snapshot['details']['orders'] ?? [];

    // Subtle text-only badge tone per doctor response.
    $responseBadge = fn ($response) => match ($response) {
        'Interested'           => 'bg-blue-50 text-blue-600',
        'Not Interested'       => 'bg-rose-50 text-rose-600',
        'Will Think'           => 'bg-amber-50 text-amber-600',
        'Requested Follow-up'  => 'bg-emerald-50 text-emerald-600',
        default                => 'bg-gray-100 text-gray-500',
    };
@endphp

<div class="space-y-5">
    {{-- ============ DOCTOR VISITS ============ --}}
    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Doctor Visits</h3>
            <span class="text-xs font-semibold text-gray-500 bg-gray-100 rounded-full px-2.5 py-1">{{ count($visitDetails) }} total</span>
        </div>

        <div class="p-3 sm:p-4">
            @forelse($visitDetails as $i => $visit)
                @php
                    $docName = $visit['doctor_name'] ?? 'Unknown Doctor';
                    $vi = collect(explode(' ', trim($docName)))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                    $hasBody = !empty($visit['discussion_summary']) || !empty($visit['products']) || !empty($visit['samples']) || !empty($visit['competitor_medicines']);
                @endphp
                <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }"
                     class="rounded-xl border border-gray-100 bg-white hover:border-gray-200 transition mb-2.5 last:mb-0 overflow-hidden">
                    {{-- header (clickable) --}}
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center gap-3 p-3.5 text-left {{ $hasBody ? 'hover:bg-gray-50/70' : '' }} transition"
                            {{ $hasBody ? '' : 'disabled' }}>
                        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-sm font-bold">
                            {{ strtoupper($vi) ?: 'DR' }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-gray-900 truncate">{{ $docName }}</p>
                                @if(!empty($visit['doctor_response']))
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $responseBadge($visit['doctor_response']) }}">{{ $visit['doctor_response'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                {{ $visit['specialization'] ?? '—' }}
                                @if(!empty($visit['clinic_name'])) · {{ $visit['clinic_name'] }} @endif
                                @if(!empty($visit['area'])) · {{ $visit['area'] }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if(!empty($visit['time']))
                                <span class="text-xs font-medium text-gray-400 whitespace-nowrap">{{ $visit['time'] }}</span>
                            @endif
                            @if($hasBody)
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            @endif
                        </div>
                    </button>

                    {{-- body (collapsible) --}}
                    @if($hasBody)
                    <div x-show="open" x-collapse>
                        <div class="px-3.5 pb-3.5 pt-1 space-y-3">
                            @if(!empty($visit['discussion_summary']))
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold mb-1">Discussion</p>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $visit['discussion_summary'] }}</p>
                                </div>
                            @endif

                            @if(!empty($visit['products']))
                                <div>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold mb-1.5">Products Discussed</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($visit['products'] as $product)
                                            <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1 text-xs font-medium text-gray-700">
                                                {{ $product['name'] }}
                                                @if(!empty($product['interest_level']))
                                                    <span class="text-[10px] font-semibold text-gray-400">{{ $product['interest_level'] }}</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($visit['samples']))
                                <div>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold mb-1.5">Samples Distributed</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($visit['samples'] as $sample)
                                            <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1 text-xs font-medium text-gray-700">
                                                {{ $sample['name'] }} <span class="font-bold text-gray-500">× {{ $sample['quantity'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($visit['competitor_medicines']))
                                <p class="text-xs text-gray-500"><span class="font-semibold text-gray-600">Competitor mentions:</span> {{ $visit['competitor_medicines'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-9 h-9 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
                    <p class="text-sm text-gray-400">No doctor visits were recorded on this day.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ============ ORDERS ============ --}}
    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Orders Collected</h3>
            <span class="text-xs font-semibold text-gray-500 bg-gray-100 rounded-full px-2.5 py-1">{{ count($orderDetails) }} total</span>
        </div>

        <div class="p-3 sm:p-4">
            @forelse($orderDetails as $order)
                <div class="rounded-xl border border-gray-100 bg-white p-4 mb-2.5 last:mb-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-gray-900">{{ $order['doctor_name'] ?? 'Unknown Doctor' }}</p>
                        <span class="px-2.5 py-0.5 text-[10px] font-semibold rounded-full bg-amber-50 text-amber-600">{{ $order['status'] ?? 'Pending' }}</span>
                    </div>
                    @if(!empty($order['items']))
                        <ul class="mt-3 rounded-lg border border-gray-100 divide-y divide-gray-100">
                            @foreach($order['items'] as $item)
                                <li class="flex items-center justify-between px-3 py-2 text-sm text-gray-700">
                                    <span>{{ $item['name'] }}</span>
                                    <span class="font-semibold text-gray-800">× {{ $item['quantity'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if(!empty($order['remarks']))
                        <p class="text-xs text-gray-500 mt-2.5"><span class="font-semibold text-gray-600">Remarks:</span> {{ $order['remarks'] }}</p>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-9 h-9 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
                    <p class="text-sm text-gray-400">No orders were collected on this day.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>

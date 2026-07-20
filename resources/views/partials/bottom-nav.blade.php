@role('MR')
@php
    $itemBase = 'flex flex-col items-center justify-center gap-1 flex-1 h-full transition-colors duration-200 relative';
@endphp
{{-- Mobile bottom navigation (MR only) — thumb-friendly, app-like --}}
<nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white/90 backdrop-blur-lg border-t border-gray-100 shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.08)]"
     style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="flex items-stretch justify-around h-16 max-w-lg mx-auto px-1">
        {{-- Dashboard --}}
        <a href="{{ route('mr.dashboard') }}" class="{{ $itemBase }} {{ request()->routeIs('mr.dashboard') ? 'text-brand-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="{{ request()->routeIs('mr.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-semibold leading-none">Home</span>
        </a>

        {{-- Visits --}}
        <a href="{{ route('mr.visits.index') }}" class="{{ $itemBase }} {{ request()->routeIs('mr.visits.index') ? 'text-brand-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span class="text-[10px] font-semibold leading-none">Visits</span>
        </a>

        {{-- Center action: New Visit (elevated FAB) --}}
        <a href="{{ route('mr.visits.create') }}" class="flex-1 flex justify-center items-start -mt-5" aria-label="New Visit">
            <span class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-400 text-white flex items-center justify-center shadow-lg shadow-brand-500/40 ring-4 ring-white active:scale-95 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            </span>
        </a>

        {{-- Orders --}}
        <a href="{{ route('mr.orders.index') }}" class="{{ $itemBase }} {{ request()->routeIs('mr.orders.*') ? 'text-brand-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <span class="text-[10px] font-semibold leading-none">Orders</span>
        </a>

        {{-- Reports --}}
        <a href="{{ route('mr.reports.index') }}" class="{{ $itemBase }} {{ request()->routeIs('mr.reports.*') ? 'text-brand-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-[10px] font-semibold leading-none">Reports</span>
        </a>
    </div>
</nav>
@endrole

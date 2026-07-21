@php
    // Shared classes for nav items so desktop + mobile stay in sync.
    $navBase = 'inline-flex items-center w-full px-4 py-2.5 text-sm font-medium transition-all duration-300 rounded-xl hover:bg-gray-800/80 hover:text-white group';
    $navActive = 'bg-[#5B4CF0] text-white shadow-[0_0_15px_rgba(91,76,240,0.5)] border-l-4 border-l-[#8B5CF6]';
    $navIdle = 'text-gray-400';
@endphp

<ul class="mt-8 space-y-2 px-3">
    {{-- Dashboard (role-aware target) --}}
    @php $dashboardRoute = auth()->user()?->hasRole('Admin') ? 'admin.dashboard' : 'mr.dashboard'; @endphp
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs($dashboardRoute) ? $navActive : $navIdle }}" href="{{ route($dashboardRoute) }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="ml-4">Dashboard</span>
        </a>
    </li>

    @role('Admin')
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.users.*') ? $navActive : $navIdle }}" href="{{ route('admin.users.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="ml-4">Users</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.products.*') ? $navActive : $navIdle }}" href="{{ route('admin.products.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <span class="ml-4">Products</span>
        </a>
    </li>

    <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
        Field Operations
    </div>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.daily-reports.*') ? $navActive : $navIdle }}" href="{{ route('admin.reports.hub') }}">
            <svg class="w-5 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="ml-4">Reports Center</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.visits.*') ? $navActive : $navIdle }}" href="{{ route('admin.visits.index') }}">
            <svg class="w-5 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="ml-4">Doctor Visits</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.orders.*') ? $navActive : $navIdle }}" href="{{ route('admin.orders.index') }}">
            <svg class="w-5 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="ml-4">Order Collection</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.attendance.*') ? $navActive : $navIdle }}" href="{{ route('admin.attendance.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="ml-4">Attendance</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.samples.*') ? $navActive : $navIdle }}" href="{{ route('admin.samples.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <span class="ml-4">Sample Assignment</span>
        </a>
    </li>
    <li class="relative mt-8">
        <div class="h-px bg-gray-800 w-full mb-4"></div>
        <a class="{{ $navBase }} {{ request()->routeIs('admin.settings.*') ? $navActive : $navIdle }}" href="{{ route('admin.settings.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="ml-4">Settings</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('admin.logs.*') ? $navActive : $navIdle }}" href="{{ route('admin.logs.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span class="ml-4">Activity Logs</span>
        </a>
    </li>
    @endrole

    @role('MR')
    <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
        My Work
    </div>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('mr.attendance.*') ? $navActive : $navIdle }}" href="{{ route('mr.attendance.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="ml-4">Attendance</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('mr.visits.*') ? $navActive : $navIdle }}" href="{{ route('mr.visits.index') }}">
            <svg class="w-5 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span class="ml-4">Doctor Visits</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('mr.samples.*') ? $navActive : $navIdle }}" href="{{ route('mr.samples.index') }}">
            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <span class="ml-4">Sample Stock</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('mr.orders.*') ? $navActive : $navIdle }}" href="{{ route('mr.orders.index') }}">
            <svg class="w-5 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="ml-4">My Orders</span>
        </a>
    </li>
    <li class="relative">
        <a class="{{ $navBase }} {{ request()->routeIs('mr.reports.*') ? $navActive : $navIdle }}" href="{{ route('mr.reports.index') }}">
            <svg class="w-5 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="ml-4">Daily Reports</span>
        </a>
    </li>
    @endrole
</ul>

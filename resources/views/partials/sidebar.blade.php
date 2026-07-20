<!-- Desktop sidebar -->
<aside class="z-20 hidden w-64 overflow-y-auto bg-gray-900 border-r border-gray-800 md:block flex-shrink-0 shadow-2xl">
    <div class="py-6 text-gray-300">
        <a class="ml-6 flex items-center gap-2 text-2xl font-bold text-white tracking-tight" href="{{ auth()->user()?->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard') }}">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center shadow-lg shadow-brand-500/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            KareOns
        </a>
        @include('partials.nav-links')
    </div>
</aside>

<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 z-10 bg-gray-900/80 backdrop-blur-sm md:hidden" style="display: none;"></div>

<!-- Mobile sidebar -->
<aside class="fixed inset-y-0 left-0 z-20 flex-shrink-0 w-64 overflow-y-auto bg-gray-900 border-r border-gray-800 md:hidden shadow-2xl" x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-200" x-transition:enter-start="opacity-0 transform -translate-x-20" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 transform -translate-x-20" @keydown.escape="sidebarOpen = false" style="display: none;">
    <div class="py-6 text-gray-300">
        <a class="ml-6 flex items-center gap-2 text-2xl font-bold text-white tracking-tight" href="{{ auth()->user()?->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard') }}">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center shadow-lg shadow-brand-500/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            KareOns
        </a>
        @include('partials.nav-links')
    </div>
</aside>

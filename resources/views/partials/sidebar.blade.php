<!-- Desktop sidebar -->
<aside class="z-20 hidden w-64 overflow-y-auto bg-gray-900/90 backdrop-blur-xl border border-gray-800/50 md:block flex-shrink-0 shadow-[0_8px_30px_rgb(0,0,0,0.12)] m-4 my-6 rounded-[24px] h-[calc(100vh-48px)] transition-all duration-300 group">
    <div class="pt-5 pb-4 text-gray-300">
        <a class="ml-6 flex items-center gap-3 text-xl font-bold text-white tracking-tight" href="{{ auth()->user()?->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard') }}">
            <div class="w-9 h-9 rounded-[10px] bg-white flex items-center justify-center shadow-[0_0_15px_rgba(91,76,240,0.25)] overflow-hidden">
                <x-brand-logo class="w-8 h-8" />
            </div>
            {{ company_name() }}
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
            <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center shadow-lg shadow-brand-500/20 overflow-hidden">
                <x-brand-logo class="w-8 h-8" />
            </div>
            {{ company_name() }}
        </a>
        @include('partials.nav-links')
    </div>
</aside>

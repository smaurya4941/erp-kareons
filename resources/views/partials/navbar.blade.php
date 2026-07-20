<header class="z-10 py-3 glass sticky top-0">
    <div class="container flex items-center justify-between h-full px-4 sm:px-6 mx-auto gap-2">

        <!-- Mobile hamburger -->
        <button class="p-2 -ml-1 rounded-lg md:hidden focus:outline-none focus:ring-2 focus:ring-brand-500 hover:bg-gray-100/50 transition-colors flex-shrink-0" @click="sidebarOpen = !sidebarOpen" aria-label="Menu">
            <svg class="w-6 h-6 text-gray-600" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
        </button>

        <!-- Mobile brand (shown when search is hidden) -->
        <a href="{{ auth()->user()?->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard') }}" class="flex items-center gap-2 md:hidden font-bold text-gray-800 tracking-tight">
            <span class="w-7 h-7 rounded-lg bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center shadow-md shadow-brand-500/30 flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </span>
            <span class="text-base">KareOns</span>
        </a>

        <!-- Search input (desktop / tablet) -->
        <div class="hidden md:flex justify-center flex-1 lg:mr-32">
            <div class="relative w-full max-w-xl mr-6 focus-within:text-brand-500 transition-colors">
                <div class="absolute inset-y-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input class="w-full pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 bg-gray-100/80 border-0 rounded-full focus:bg-white focus:ring-2 focus:ring-brand-500/50 focus:outline-none py-2.5 transition-all shadow-inner" type="text" placeholder="Search across KareOns..." aria-label="Search">
            </div>
        </div>

        <ul class="flex items-center flex-shrink-0 space-x-2 sm:space-x-6 ml-auto md:ml-0">
            <!-- Notifications (Mockup) -->
            <li class="relative">
                <button class="relative align-middle rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 p-2 text-gray-400 hover:text-brand-600 transition-colors" aria-label="Notifications" aria-haspopup="true">
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                    <!-- Notification badge -->
                    <span aria-hidden="true" class="absolute top-1.5 right-1.5 inline-block w-2 h-2 transform translate-x-1 -translate-y-1 bg-red-500 border-2 border-white rounded-full"></span>
                </button>
            </li>

            <!-- Profile menu -->
            <li class="relative" x-data="{ profileOpen: false }">
                <button class="align-middle rounded-full focus:outline-none focus:ring-2 focus:ring-brand-500 flex items-center space-x-3 p-1 hover:bg-gray-100/50 transition-all border border-transparent hover:border-gray-200" @click="profileOpen = !profileOpen" @keydown.escape="profileOpen = false" aria-label="Account" aria-haspopup="true">
                    @if(auth()->user() && auth()->user()->profile_photo_path)
                        <img class="object-cover w-9 h-9 rounded-full shadow-sm" src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="" aria-hidden="true">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-brand-200 to-brand-100 border border-brand-300 flex items-center justify-center text-brand-700 font-bold text-sm shadow-sm">
                            {{ auth()->user() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                        </div>
                    @endif
                    <div class="hidden md:flex flex-col items-start mr-2">
                        <span class="text-sm font-semibold text-gray-700 leading-tight">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">{{ auth()->user()->roles->first()->name ?? 'Guest' }}</span>
                    </div>
                </button>
                <div x-show="profileOpen" @click.away="profileOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-2" class="absolute right-0 w-56 p-2 mt-3 space-y-1 text-gray-600 bg-white border border-gray-100/50 rounded-xl shadow-xl shadow-gray-200/50" style="display: none;">
                    <div class="px-3 py-2 text-xs font-semibold tracking-wider text-gray-400 uppercase border-b border-gray-50 mb-1">Account</div>
                    <a class="inline-flex items-center w-full px-3 py-2.5 text-sm font-medium transition-colors duration-150 rounded-lg hover:bg-brand-50 hover:text-brand-700" href="{{ route('profile.edit') }}">
                        <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center w-full px-3 py-2.5 text-sm font-medium transition-colors duration-150 rounded-lg hover:bg-red-50 hover:text-red-700">
                            <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Sign out</span>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</header>

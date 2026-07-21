<header class="z-10 py-4 sticky top-0 bg-[#F6F8FC]/80 backdrop-blur-xl border-b border-gray-200/50">
    <div class="container flex items-center justify-between h-full px-4 sm:px-6 mx-auto gap-4">

        <!-- Mobile hamburger -->
        <button class="p-2 -ml-1 rounded-xl md:hidden focus:outline-none focus:ring-2 focus:ring-[#5B4CF0] hover:bg-white shadow-sm transition-all flex-shrink-0" @click="sidebarOpen = !sidebarOpen" aria-label="Menu">
            <svg class="w-5 h-5 text-gray-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile brand -->
        <a href="{{ auth()->user()?->hasRole('Admin') ? route('admin.dashboard') : route('mr.dashboard') }}" class="flex items-center gap-2 md:hidden font-bold text-gray-900 tracking-tight">
            <span class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-lg shadow-indigo-500/20 flex-shrink-0 overflow-hidden">
                <x-brand-logo class="w-8 h-8" />
            </span>
            <span class="text-lg">{{ company_name() }}</span>
        </a>

        <!-- Search input (desktop / tablet) -->
        <div class="hidden md:flex justify-start flex-1">
            <div class="relative w-full max-w-md group">
                <div class="absolute inset-y-0 flex items-center pl-4">
                    <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#5B4CF0] transition-colors" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input class="w-full pl-11 pr-16 text-sm text-gray-700 placeholder-gray-400 bg-white border-0 rounded-full focus:ring-2 focus:ring-[#5B4CF0]/30 focus:outline-none py-2.5 transition-all shadow-[0_2px_10px_rgb(0,0,0,0.02)] hover:shadow-[0_4px_15px_rgb(0,0,0,0.04)]" type="text" placeholder="Search anything..." aria-label="Search">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="hidden sm:inline-block px-2 py-1 text-[10px] font-medium text-gray-400 border border-gray-200 rounded-md bg-gray-50/50">⌘K</span>
                </div>
            </div>
        </div>

        <ul class="flex items-center flex-shrink-0 space-x-3 sm:space-x-4 ml-auto md:ml-0">
            
            @if(auth()->user()?->hasRole('MR'))
            @php
                $isOnDuty = auth()->user()->isOnDuty();
            @endphp
            <!-- Attendance Status Pill -->
            <li class="hidden sm:block">
                <div class="flex items-center gap-2 px-3 py-1.5 border rounded-full shadow-sm {{ $isOnDuty ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-200' }}">
                    <span class="relative flex h-2 w-2">
                      @if($isOnDuty)
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                      @else
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
                      @endif
                    </span>
                    <span class="text-xs font-semibold {{ $isOnDuty ? 'text-green-700' : 'text-gray-600' }}">
                        {{ $isOnDuty ? 'On Duty' : 'Off Duty' }}
                    </span>
                </div>
            </li>
            @endif

            <!-- Notifications -->
            <li class="relative" x-data="notificationCenter()" x-init="init()">
                <button @click="toggle()" @keydown.escape="open = false"
                    class="relative align-middle rounded-full bg-white shadow-[0_2px_10px_rgb(0,0,0,0.02)] hover:shadow-[0_4px_15px_rgb(0,0,0,0.04)] focus:outline-none focus:ring-2 focus:ring-[#5B4CF0] p-2.5 text-gray-500 hover:text-[#5B4CF0] transition-all"
                    aria-label="Notifications" aria-haspopup="true" :aria-expanded="open">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <!-- Dynamic unread badge -->
                    <span x-show="unreadCount > 0" x-cloak
                        class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-[#EF4444] border-2 border-white rounded-full"
                        x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </button>

                <!-- Dropdown panel -->
                <div x-show="open" @click.away="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                    class="absolute right-0 mt-3 w-80 max-w-[calc(100vw-1.5rem)] bg-white border border-gray-100 rounded-2xl shadow-[0_10px_40px_rgb(0,0,0,0.12)] overflow-hidden z-50">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-800">Notifications</span>
                            <span x-show="unreadCount > 0" x-text="unreadCount"
                                class="px-1.5 py-0.5 text-[10px] font-bold text-[#5B4CF0] bg-[#5B4CF0]/10 rounded-full"></span>
                        </div>
                        <button @click="markAllRead()" x-show="unreadCount > 0"
                            class="text-xs font-semibold text-[#5B4CF0] hover:underline">Mark all read</button>
                    </div>

                    <!-- Body -->
                    <div class="max-h-[60vh] sm:max-h-96 overflow-y-auto">
                        <!-- Loading -->
                        <div x-show="loading" class="py-10 text-center">
                            <svg class="w-6 h-6 mx-auto text-gray-300 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>

                        <!-- Empty state -->
                        <div x-show="!loading && notifications.length === 0" class="py-10 px-6 text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <p class="text-sm text-gray-500">You're all caught up!</p>
                            <p class="text-xs text-gray-400 mt-0.5">No notifications yet.</p>
                        </div>

                        <!-- List -->
                        <template x-for="n in notifications" :key="n.id">
                            <a :href="'{{ url('/notifications') }}/' + n.id + '/read'"
                                class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors"
                                :class="!n.is_read ? 'bg-[#5B4CF0]/[0.03]' : ''">
                                <span class="mt-0.5 w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                                    :class="iconBg(n.icon)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="iconPath(n.icon)"></path>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 leading-snug" x-text="n.title"></p>
                                    <p class="text-xs text-gray-500 mt-0.5 leading-snug" x-text="n.message"></p>
                                    <p class="text-[11px] text-gray-400 mt-1" x-text="n.time"></p>
                                </div>
                                <span x-show="!n.is_read" class="mt-1.5 w-2 h-2 rounded-full bg-[#5B4CF0] flex-shrink-0"></span>
                            </a>
                        </template>
                    </div>

                    <!-- Footer -->
                    <a href="{{ route('notifications.index') }}"
                        class="block px-4 py-3 text-center text-sm font-semibold text-[#5B4CF0] border-t border-gray-50 hover:bg-gray-50 transition-colors">
                        View all notifications
                    </a>
                </div>
            </li>

            <!-- Profile menu -->
            <li class="relative" x-data="{ profileOpen: false }">
                <button class="align-middle rounded-full focus:outline-none focus:ring-2 focus:ring-[#5B4CF0] flex items-center space-x-2 p-1 bg-white shadow-[0_2px_10px_rgb(0,0,0,0.02)] hover:shadow-[0_4px_15px_rgb(0,0,0,0.06)] transition-all border border-transparent" @click="profileOpen = !profileOpen" @keydown.escape="profileOpen = false" aria-label="Account" aria-haspopup="true">
                    @if(auth()->user() && auth()->user()->profile_photo_path)
                        <img class="object-cover w-9 h-9 rounded-full" src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="" aria-hidden="true">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#5B4CF0] to-[#8B5CF6] flex items-center justify-center text-white font-bold text-sm shadow-inner">
                            {{ auth()->user() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                        </div>
                    @endif
                    <div class="hidden md:flex flex-col items-start mr-3 ml-1">
                        <span class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name ?? 'User' }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 mr-2 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div x-show="profileOpen" @click.away="profileOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-2" class="absolute right-0 w-56 p-2 mt-3 space-y-1 text-gray-600 bg-white border border-gray-100 rounded-2xl shadow-[0_10px_40px_rgb(0,0,0,0.08)]" style="display: none;">
                    <div class="px-3 py-2 text-xs font-semibold tracking-wider text-gray-400 uppercase border-b border-gray-50 mb-1">Account</div>
                    <a class="inline-flex items-center w-full px-3 py-2.5 text-sm font-medium transition-colors duration-200 rounded-xl hover:bg-[#F6F8FC] hover:text-[#5B4CF0]" href="{{ route('profile.edit') }}">
                        <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center w-full px-3 py-2.5 text-sm font-medium transition-colors duration-200 rounded-xl hover:bg-red-50 hover:text-red-600">
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

@push('scripts')
<script>
    function notificationCenter() {
        return {
            open: false,
            loading: false,
            fetched: false,
            unreadCount: 0,
            notifications: [],
            feedUrl: '{{ route('notifications.feed') }}',
            readAllUrl: '{{ route('notifications.read-all') }}',
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),

            init() {
                this.refreshFeed();
                // Keep the badge fresh while the tab stays open.
                setInterval(() => { if (!document.hidden) this.refreshFeed(); }, 60000);
            },

            async refreshFeed() {
                try {
                    const res = await fetch(this.feedUrl, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.unreadCount = data.unread_count ?? 0;
                    this.notifications = data.notifications ?? [];
                    this.fetched = true;
                } catch (e) { /* silent: notifications are non-critical */ }
            },

            async toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.loading = !this.fetched;
                    await this.refreshFeed();
                    this.loading = false;
                }
            },

            async markAllRead() {
                try {
                    await fetch(this.readAllUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    });
                } catch (e) { /* ignore */ }
                this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
                this.unreadCount = 0;
            },

            iconBg(type) {
                return {
                    order: 'bg-indigo-50 text-indigo-500',
                    sample: 'bg-green-50 text-green-600',
                    report: 'bg-blue-50 text-blue-500',
                }[type] || 'bg-gray-100 text-gray-500';
            },

            iconPath(type) {
                return {
                    order: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    sample: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                    report: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                }[type] || 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
            },
        };
    }
</script>
@endpush

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ company_name() }} ERP</title>
    <link rel="icon" type="image/png" href="{{ favicon_url() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js (Collapse plugin must load before the core) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#F6F8FC] text-gray-900 selection:bg-[#5B4CF0] selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <!-- Navbar -->
            @include('partials.navbar')

            <!-- Main Page Content -->
            <main class="w-full grow p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto pb-24 md:pb-8 relative z-0">
                @yield('content')
            </main>
        </div>

        <!-- Mobile bottom navigation (MR) -->
        @include('partials.bottom-nav')

    </div>

    @stack('scripts')
</body>
</html>

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Reports Hub</h2>
    <p class="text-sm text-gray-500">Analytics, exports, and detailed operational reports.</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Attendance Records</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['attendance_records']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Doctor Visits</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['doctor_visits']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Orders Collected</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['orders']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-teal-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Samples Distributed</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['samples_distributed']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-indigo-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Daily Reports</h3>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['daily_reports']) }}</p>
    </div>
</div>

<!-- Report Navigation -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <a href="{{ route('admin.reports.performance') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all">
        <div class="flex items-center mb-3">
            <div class="p-2 bg-blue-100 rounded text-blue-600 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <h5 class="mb-0 text-lg font-bold tracking-tight text-gray-900">MR Performance Report</h5>
        </div>
        <p class="font-normal text-gray-700 text-sm">Rankings, average visits, orders, and working hours per employee.</p>
    </a>

    <a href="{{ route('admin.reports.attendance') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-green-300 transition-all">
        <div class="flex items-center mb-3">
            <div class="p-2 bg-green-100 rounded text-green-600 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h5 class="mb-0 text-lg font-bold tracking-tight text-gray-900">Attendance Report</h5>
        </div>
        <p class="font-normal text-gray-700 text-sm">Detailed check-in/out logs, working hours, and absent tracking.</p>
    </a>

    <a href="{{ route('admin.reports.visits') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all">
        <div class="flex items-center mb-3">
            <div class="p-2 bg-indigo-100 rounded text-indigo-600 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h5 class="mb-0 text-lg font-bold tracking-tight text-gray-900">Doctor Visit Report</h5>
        </div>
        <p class="font-normal text-gray-700 text-sm">Every clinic visited, products discussed, and overall coverage.</p>
    </a>

    <a href="{{ route('admin.reports.orders') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-purple-300 transition-all">
        <div class="flex items-center mb-3">
            <div class="p-2 bg-purple-100 rounded text-purple-600 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <h5 class="mb-0 text-lg font-bold tracking-tight text-gray-900">Order Report</h5>
        </div>
        <p class="font-normal text-gray-700 text-sm">Sales tracking, pending approvals, and completed orders.</p>
    </a>

    <a href="{{ route('admin.reports.samples') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-teal-300 transition-all">
        <div class="flex items-center mb-3">
            <div class="p-2 bg-teal-100 rounded text-teal-600 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <h5 class="mb-0 text-lg font-bold tracking-tight text-gray-900">Sample Distribution Report</h5>
        </div>
        <p class="font-normal text-gray-700 text-sm">Detailed log of which samples went to which doctors.</p>
    </a>

    <a href="{{ route('admin.daily-reports.index') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-yellow-300 transition-all">
        <div class="flex items-center mb-3">
            <div class="p-2 bg-yellow-100 rounded text-yellow-600 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h5 class="mb-0 text-lg font-bold tracking-tight text-gray-900">Daily Reports</h5>
        </div>
        <p class="font-normal text-gray-700 text-sm">End-of-day summaries and status updates from the field force.</p>
    </a>

</div>
@endsection

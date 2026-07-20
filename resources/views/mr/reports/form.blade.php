@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">End Day Report</h2>
        <p class="text-sm text-gray-500">Review your daily statistics and submit your final report for {{ $report->date->format('d M Y') }}.</p>
    </div>
</div>

<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded shadow-sm">
    <p class="text-sm text-blue-700"><strong>Note:</strong> Statistics below are automatically calculated based on your activity today. Once you submit this report, it becomes read-only and your day is officially concluded.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Auto-generated Stats Panel -->
    <div class="lg:col-span-1 space-y-6">
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Your Activity Today</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Working Hours</span>
                    <span class="font-bold text-gray-800">{{ $report->stats_snapshot['attendance']['working_hours'] ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Doctor Visits</span>
                    <span class="font-bold text-gray-800">{{ $report->stats_snapshot['visits']['total_visits'] ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Products Discussed</span>
                    <span class="font-bold text-gray-800">{{ $report->stats_snapshot['visits']['total_products_discussed'] ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Samples Distributed</span>
                    <span class="font-bold text-gray-800">{{ $report->stats_snapshot['visits']['total_samples_distributed'] ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Orders Collected</span>
                    <span class="font-bold text-gray-800">{{ $report->stats_snapshot['orders']['total_orders'] ?? '0' }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- The Form -->
    <div class="lg:col-span-2">
        <x-card>
            <form action="{{ route('mr.reports.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Today's Summary <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-2">Provide a brief overview of your accomplishments today.</p>
                    <textarea name="today_summary" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 @error('today_summary') border-red-500 @enderror" rows="4" required>{{ old('today_summary', $report->today_summary) }}</textarea>
                    @error('today_summary')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Problems Faced (Optional)</label>
                    <p class="text-xs text-gray-500 mb-2">Did you experience any challenges, rejections, or operational issues?</p>
                    <textarea name="problems_faced" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 @error('problems_faced') border-red-500 @enderror" rows="3">{{ old('problems_faced', $report->problems_faced) }}</textarea>
                    @error('problems_faced')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tomorrow's Plan <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-2">What is your target or route for tomorrow?</p>
                    <textarea name="tomorrow_plan" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 @error('tomorrow_plan') border-red-500 @enderror" rows="3" required>{{ old('tomorrow_plan', $report->tomorrow_plan) }}</textarea>
                    @error('tomorrow_plan')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4 border-t pt-4">
                    <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('mr.dashboard') }}'">Cancel</x-button>
                    <x-button type="submit" variant="primary">Submit Final Report</x-button>
                </div>
            </form>
        </x-card>
    </div>

</div>
@endsection

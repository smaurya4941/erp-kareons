@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Visit Detail</h2>
        <p class="text-sm text-gray-500">{{ $visit->date->format('l, d M Y') }} at {{ \Carbon\Carbon::parse($visit->time)->format('h:i A') }}</p>
    </div>
    <div>
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.visits.index') }}'">Back</x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- MR Info -->
    <x-card class="lg:col-span-1">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Representative</h3>
        <div class="flex items-center mb-2">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg mr-4">
                {{ substr($visit->user->name, 0, 1) }}
            </div>
            <div>
                <h4 class="font-bold text-gray-800">{{ $visit->user->name }}</h4>
                <p class="text-xs text-gray-500">Code: {{ $visit->user->employee_code }}</p>
            </div>
        </div>
    </x-card>

    <!-- Doctor Info -->
    <x-card class="lg:col-span-2 border-t-4 border-blue-500">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Doctor Information</h3>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">{{ $visit->status }}</span>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Name</p>
                <p class="font-bold text-gray-800 text-lg">{{ $visit->doctor_name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Specialization</p>
                <p class="font-medium text-gray-800">{{ $visit->specialization }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Clinic / Hospital</p>
                <p class="font-medium text-gray-800">{{ $visit->clinic_name ?: 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Phone / Area</p>
                <p class="font-medium text-gray-800">{{ $visit->phone ?: 'N/A' }} | {{ $visit->area ?: 'N/A' }}</p>
            </div>
        </div>
    </x-card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 space-y-6">
        <!-- Discussion Note -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Discussion Details</h3>
            
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Doctor's Response</p>
                <span class="px-3 py-1 text-sm font-bold rounded-full 
                            {{ $visit->doctor_response === 'Interested' ? 'bg-green-100 text-green-800' : 
                               ($visit->doctor_response === 'Not Interested' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ $visit->doctor_response }}
                </span>
            </div>

            <div class="mb-4">
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Official Summary</p>
                <p class="text-gray-700 bg-gray-50 p-4 rounded border whitespace-pre-wrap">{{ $visit->discussion_summary }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t pt-4">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Competitor Medicines</p>
                    <p class="text-sm text-gray-700">{{ $visit->competitor_medicines ?: 'None mentioned' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Internal Remarks</p>
                    <p class="text-sm text-gray-700">{{ $visit->remarks ?: 'None' }}</p>
                </div>
            </div>
        </x-card>

        <!-- Products Discussed -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Products Discussed
            </h3>
            
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold text-gray-500 uppercase border-b">
                        <th class="pb-2">Product Name</th>
                        <th class="pb-2 text-center">Interest Level</th>
                        <th class="pb-2">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm text-gray-700">
                    @foreach($visit->discussedProducts as $dp)
                    <tr>
                        <td class="py-3 font-semibold">{{ $dp->product->name }} ({{ $dp->product->strength }})</td>
                        <td class="py-3 text-center">
                            @if($dp->interest_level)
                                <span class="px-2 py-1 text-xs rounded-full border {{ $dp->interest_level === 'High' ? 'bg-green-50 border-green-200 text-green-700' : ($dp->interest_level === 'Low' ? 'bg-gray-100 border-gray-200 text-gray-600' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
                                    {{ $dp->interest_level }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3 text-gray-500">{{ $dp->remarks ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
        
        <!-- Orders & Samples -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Samples -->
            <x-card>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Samples Distributed</h3>
                @if($visit->distributedSamples->isNotEmpty())
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <tbody class="divide-y text-gray-700">
                            @foreach($visit->distributedSamples as $sample)
                            <tr>
                                <td class="py-2 font-medium">{{ $sample->product->name }}</td>
                                <td class="py-2 font-bold text-right text-blue-600">{{ $sample->quantity }} units</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500 italic text-center py-4">No samples distributed.</p>
                @endif
            </x-card>

            <!-- Orders -->
            <x-card>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 flex items-center justify-between">
                    <span>Order Collected</span>
                    @if($visit->order)
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">{{ $visit->order->status }}</span>
                    @endif
                </h3>
                
                @if($visit->order && $visit->order->items->isNotEmpty())
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <tbody class="divide-y text-gray-700">
                            @foreach($visit->order->items as $item)
                            <tr>
                                <td class="py-2 font-medium">{{ $item->product->name }}</td>
                                <td class="py-2 font-bold text-right text-green-600">{{ $item->quantity }} units</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500 italic text-center py-4">No orders collected.</p>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Location Side panel -->
    <div class="lg:col-span-1">
        <x-card>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Location Evidence</h3>
            
            @if($visit->lat && $visit->lng)
                <!-- Map -->
                <div class="mb-4 rounded-lg overflow-hidden border">
                    <iframe 
                        width="100%" 
                        height="250" 
                        frameborder="0" 
                        style="border:0;" 
                        src="https://maps.google.com/maps?q={{ $visit->lat }},{{ $visit->lng }}&hl=en&z=15&output=embed" 
                        allowfullscreen>
                    </iframe>
                </div>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="block text-xs font-bold text-gray-500">Coordinates:</span>
                        <span class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-700">{{ $visit->lat }}, {{ $visit->lng }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500">Accuracy:</span>
                        <span class="font-bold {{ $visit->accuracy > 50 ? 'text-red-500' : 'text-green-600' }}">{{ $visit->accuracy }} meters</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500">Captured Address:</span>
                        <span class="text-gray-700">{{ $visit->address ?: 'Not provided' }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="text-sm">No GPS coordinates recorded.</p>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Activity Log Details</h2>
        <p class="text-sm text-gray-500">Audit Reference #{{ $log->id }}</p>
    </div>
    <div>
        <a href="{{ route('admin.logs.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Logs</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <div class="md:col-span-1 space-y-6">
        <x-card>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Meta Information</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-500 block">Date & Time</span>
                    <span class="font-medium text-gray-900">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Module</span>
                    <span class="font-medium text-gray-900">{{ $log->module }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Action</span>
                    <span class="font-medium text-blue-600">{{ $log->action }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Severity</span>
                    <span class="font-medium text-gray-900">{{ $log->severity }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Status</span>
                    <span class="font-medium {{ $log->status === 'Success' ? 'text-green-600' : 'text-red-600' }}">{{ $log->status }}</span>
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">User & Device</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-500 block">User</span>
                    <span class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Employee Code</span>
                    <span class="font-medium text-gray-900">{{ $log->user->employee_code ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">IP Address</span>
                    <span class="font-medium text-gray-900">{{ $log->ip_address ?? 'Unknown' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">User Agent</span>
                    <span class="font-medium text-gray-700 break-words text-xs">{{ $log->user_agent ?? 'Unknown' }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <div class="md:col-span-2 space-y-6">
        <x-card>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Description</h3>
            <p class="text-gray-900">{{ $log->description }}</p>
            
            @if($log->subject_type)
            <div class="mt-4 pt-4 border-t">
                <span class="text-xs text-gray-500 block">Target Record</span>
                <span class="text-sm font-medium bg-gray-100 px-2 py-1 rounded inline-block mt-1">
                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                </span>
            </div>
            @endif
        </x-card>

        <x-card>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Data Properties (Before / After)</h3>
            
            @if($log->properties && (isset($log->properties['old']) || isset($log->properties['new']) || isset($log->properties['attributes'])))
                
                @if(isset($log->properties['old']) || isset($log->properties['new']))
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-bold text-red-500 mb-2 uppercase">Old Values</div>
                            <pre class="bg-red-50 p-4 rounded text-xs text-red-900 overflow-x-auto">@php
                                echo json_encode($log->properties['old'] ?? [], JSON_PRETTY_PRINT);
                            @endphp</pre>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-green-500 mb-2 uppercase">New Values</div>
                            <pre class="bg-green-50 p-4 rounded text-xs text-green-900 overflow-x-auto">@php
                                echo json_encode($log->properties['new'] ?? [], JSON_PRETTY_PRINT);
                            @endphp</pre>
                        </div>
                    </div>
                @else
                    <div>
                        <div class="text-xs font-bold text-gray-500 mb-2 uppercase">Attributes</div>
                        <pre class="bg-gray-100 p-4 rounded text-xs text-gray-800 overflow-x-auto">@php
                            echo json_encode($log->properties['attributes'] ?? $log->properties, JSON_PRETTY_PRINT);
                        @endphp</pre>
                    </div>
                @endif
                
            @else
                <p class="text-sm text-gray-500 italic">No specific property changes recorded for this event.</p>
            @endif
            
        </x-card>
    </div>

</div>
@endsection

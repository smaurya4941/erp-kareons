@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Product Details</h2>
        <p class="text-sm text-gray-500">View master product information.</p>
    </div>
    <div class="flex space-x-3">
        <x-button variant="secondary" onclick="window.location.href='{{ route('admin.products.index') }}'">Back</x-button>
        <x-button variant="primary" onclick="window.location.href='{{ route('admin.products.edit', $product) }}'">Edit Product</x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Product Image -->
            <div class="w-full md:w-1/3 flex flex-col items-center">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="w-48 h-48 rounded-lg object-cover border shadow-sm">
                @else
                    <div class="w-48 h-48 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border shadow-sm">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
                <div class="mt-4">
                    @if($product->status)
                        <x-badge type="success">Active</x-badge>
                    @else
                        <x-badge type="danger">Inactive</x-badge>
                    @endif
                </div>
            </div>

            <!-- Basic Info -->
            <div class="w-full md:w-2/3">
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $product->name }}</h3>
                <p class="text-gray-500 font-mono text-sm mb-4">Code: {{ $product->product_code }}</p>
                
                <div class="grid grid-cols-2 gap-y-4 gap-x-2 text-sm">
                    <div>
                        <span class="block text-gray-500">Category</span>
                        <span class="font-medium text-gray-900">{{ $product->category }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Strength</span>
                        <span class="font-medium text-gray-900">{{ $product->strength }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Pack Size</span>
                        <span class="font-medium text-gray-900">{{ $product->pack_size }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Added On</span>
                        <span class="font-medium text-gray-900">{{ $product->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <span class="block text-gray-500 text-sm mb-1">Description</span>
                    <p class="text-gray-700 text-sm whitespace-pre-line">{{ $product->description ?: 'No description provided.' }}</p>
                </div>
            </div>
        </div>
    </x-card>

    <div class="space-y-6">
        <!-- Usage Stats (Phase 2 placeholders) -->
        <x-card>
            <x-slot name="header">
                <h4 class="font-semibold text-gray-800">Product Usage</h4>
            </x-slot>
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b pb-2">
                    <span class="text-sm text-gray-600">Assigned to MRs</span>
                    <span class="font-semibold text-gray-900">0</span>
                </div>
                <div class="flex justify-between items-center border-b pb-2">
                    <span class="text-sm text-gray-600">Total Samples Distributed</span>
                    <span class="font-semibold text-gray-900">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Orders Received</span>
                    <span class="font-semibold text-gray-900">0</span>
                </div>
                <p class="text-xs text-gray-400 mt-2">* Statistics will become live as additional modules are developed.</p>
            </div>
        </x-card>

        <!-- Additional Info -->
        <x-card>
            <x-slot name="header">
                <h4 class="font-semibold text-gray-800">Additional Information</h4>
            </x-slot>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Brand</span>
                    <span class="font-medium">{{ $product->brand ?: 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Manufacturer</span>
                    <span class="font-medium">{{ $product->manufacturer ?: 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">HSN Code</span>
                    <span class="font-medium">{{ $product->hsn_code ?: 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">MRP</span>
                    <span class="font-medium">{{ $product->mrp ? '₹'.$product->mrp : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">GST</span>
                    <span class="font-medium">{{ $product->gst ? $product->gst.'%' : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Expiry Required</span>
                    <span class="font-medium">{{ $product->expiry_required ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection

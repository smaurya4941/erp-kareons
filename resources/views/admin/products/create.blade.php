@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Add New Product</h2>
    <p class="text-sm text-gray-500">Create a new master product for the ERP. Product Code will be auto-generated.</p>
</div>

<x-card class="max-w-4xl">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Basic Info -->
            <div class="md:col-span-2 text-lg font-semibold text-gray-700 border-b pb-2">Basic Information</div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Product Name *</label>
                <x-input name="name" value="{{ old('name') }}" placeholder="e.g. KareOns Pain Relief Oil" required />
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Category *</label>
                <select name="category" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" required>
                    <option value="" disabled selected>Select a Category</option>
                    @foreach(['Tablet', 'Capsule', 'Syrup', 'Oil', 'Powder', 'Drops', 'Cream', 'Ointment'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Strength *</label>
                <x-input name="strength" value="{{ old('strength') }}" placeholder="e.g. 500 mg, 100 ml, 5%" required />
                @error('strength') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Pack Size *</label>
                <x-input name="pack_size" value="{{ old('pack_size') }}" placeholder="e.g. 10 Tablets, 100 ml Bottle" required />
                @error('pack_size') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Status *</label>
                <select name="status" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" required>
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active (Available across ERP)</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive (Hidden from new entries)</option>
                </select>
                @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Product Image (Square recommended)</label>
                <input type="file" name="image" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept=".jpg,.jpeg,.png,.webp" />
                @error('image') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Product Description</label>
                <textarea name="description" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3" placeholder="Short product information...">{{ old('description') }}</textarea>
                @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <!-- Future Ready Fields (Optional) -->
            <div class="md:col-span-2 mt-4 text-lg font-semibold text-gray-700 border-b pb-2">Additional Information (Optional)</div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Brand</label>
                <x-input name="brand" value="{{ old('brand', 'KareOns') }}" />
                @error('brand') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Manufacturer</label>
                <x-input name="manufacturer" value="{{ old('manufacturer') }}" />
                @error('manufacturer') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">HSN Code</label>
                <x-input name="hsn_code" value="{{ old('hsn_code') }}" />
                @error('hsn_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">MRP (₹)</label>
                    <x-input type="number" step="0.01" name="mrp" value="{{ old('mrp') }}" />
                    @error('mrp') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">GST (%)</label>
                    <x-input type="number" step="0.01" name="gst" value="{{ old('gst') }}" />
                    @error('gst') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Barcode</label>
                <x-input name="barcode" value="{{ old('barcode') }}" />
                @error('barcode') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Expiry Tracking Required</label>
                <select name="expiry_required" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white">
                    <option value="0" {{ old('expiry_required') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('expiry_required') == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                @error('expiry_required') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
            <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('admin.products.index') }}'">Cancel</x-button>
            <x-button type="submit" variant="primary">Create Product</x-button>
        </div>
    </form>
</x-card>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Add New Product</h2>
    <p class="text-sm text-gray-500">Enter the product name to add it and assign it to MRs. You can fill in the other details later.</p>
</div>

<x-card class="max-w-2xl">
    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Product Name *</label>
            <x-input name="name" value="{{ old('name') }}" placeholder="e.g. KareOns Pain Relief Oil" required autofocus />
            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            <p class="mt-2 text-xs text-gray-400">Product Code is auto-generated. The product is created as Active by default.</p>
        </div>

        <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
            <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('admin.products.index') }}'">Cancel</x-button>
            <x-button type="submit" variant="primary">Create Product</x-button>
        </div>
    </form>
</x-card>
@endsection

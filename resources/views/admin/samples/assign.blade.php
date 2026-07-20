@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Assign Samples to MR</h2>
    <p class="text-sm text-gray-500">Allocate product samples to a Medical Representative's inventory.</p>
</div>

<x-card class="max-w-4xl">
    <form action="{{ route('admin.samples.store') }}" method="POST" x-data="sampleAssignmentForm()">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Select MR *</label>
            <select name="user_id" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" required>
                <option value="" disabled {{ !$selectedMr ? 'selected' : '' }}>-- Select a Medical Representative --</option>
                @foreach($mrs as $mr)
                    <option value="{{ $mr->id }}" {{ $selectedMr == $mr->id ? 'selected' : '' }}>
                        {{ $mr->name }} ({{ $mr->employee_code }})
                    </option>
                @endforeach
            </select>
            @error('user_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4 text-lg font-semibold text-gray-700 border-b pb-2 flex justify-between items-center">
            <span>Products to Assign</span>
            <button type="button" @click="addRow()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Another Product
            </button>
        </div>

        @error('products') <div class="text-sm text-red-500 mb-4">{{ $message }}</div> @enderror

        <!-- Dynamic Product Rows -->
        <div class="space-y-4 mb-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="flex items-start gap-4 p-4 border rounded bg-gray-50">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Product *</label>
                        <select :name="`products[${index}][product_id]`" x-model="row.product_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white" required>
                            <option value="" disabled>-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->strength }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-32">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Quantity *</label>
                        <input type="number" min="1" :name="`products[${index}][quantity]`" x-model="row.quantity" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                    </div>

                    <div class="pt-6">
                        <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700 p-2" x-show="rows.length > 1" title="Remove row">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
            <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('admin.samples.index') }}'">Cancel</x-button>
            <x-button type="submit" variant="primary">Save Assignment</x-button>
        </div>
    </form>
</x-card>

<!-- AlpineJS Script for Dynamic Rows -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sampleAssignmentForm', () => ({
            rows: [
                { product_id: '', quantity: 1 }
            ],
            addRow() {
                this.rows.push({ product_id: '', quantity: 1 });
            },
            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            }
        }));
    });
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Adjust Sample Stock</h2>
    <p class="text-sm text-gray-500">Reduce or return assigned samples for an MR.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">
    <div class="lg:col-span-1">
        <x-card>
            <div class="text-sm text-gray-500 mb-1">Medical Representative</div>
            <div class="font-bold text-gray-800 text-lg">{{ $user->name }}</div>
            <div class="text-xs text-gray-400 mb-4">{{ $user->employee_code }}</div>
            
            <div class="text-sm text-gray-500 mb-1">Product</div>
            <div class="font-bold text-blue-700">{{ $product->name }}</div>
            <div class="text-xs text-gray-400 border-b pb-4 mb-4">{{ $product->product_code }}</div>
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Total Assigned:</span>
                <span class="font-medium">{{ $assignment->assigned_quantity }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Total Distributed:</span>
                <span class="font-medium">{{ $assignment->distributed_quantity }}</span>
            </div>
            <div class="flex justify-between items-center bg-blue-50 p-2 rounded mt-2">
                <span class="text-sm font-semibold text-blue-800">Currently Available:</span>
                <span class="text-lg font-bold text-blue-800">{{ $assignment->remaining_quantity }}</span>
            </div>
        </x-card>
    </div>

    <div class="lg:col-span-2">
        <x-card>
            <form action="{{ route('admin.samples.adjust') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Action Type *</label>
                    <div class="mt-2 grid grid-cols-3 gap-3">
                        <label class="border rounded p-3 cursor-pointer hover:bg-gray-50 flex items-center">
                            <input type="radio" name="action_type" value="return" class="text-blue-600" required checked>
                            <span class="ml-2 text-sm text-gray-700">Returned by MR</span>
                        </label>
                        <label class="border rounded p-3 cursor-pointer hover:bg-gray-50 flex items-center">
                            <input type="radio" name="action_type" value="reduce" class="text-blue-600" required>
                            <span class="ml-2 text-sm text-gray-700">Reduce/Remove</span>
                        </label>
                        <label class="border rounded p-3 cursor-pointer hover:bg-gray-50 flex items-center">
                            <input type="radio" name="action_type" value="adjustment" class="text-blue-600" required>
                            <span class="ml-2 text-sm text-gray-700">Stock Adjustment</span>
                        </label>
                    </div>
                    @error('action_type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Quantity to Remove *</label>
                    <x-input type="number" name="quantity" min="1" max="{{ $assignment->remaining_quantity }}" value="{{ old('quantity') }}" required />
                    <p class="text-xs text-gray-500 mt-1">Cannot exceed currently available balance ({{ $assignment->remaining_quantity }}).</p>
                    @error('quantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Reason / Remarks *</label>
                    <textarea name="reason" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="2" placeholder="Explain why this stock is being reduced..." required>{{ old('reason') }}</textarea>
                    @error('reason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
                    <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('admin.samples.show', $user->id) }}'">Cancel</x-button>
                    <x-button type="submit" variant="primary" class="bg-red-600 hover:bg-red-700">Confirm Adjustment</x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Edit User: {{ $user->name }}</h2>
    <p class="text-sm text-gray-500">Employee Code: {{ $user->employee_code }}</p>
</div>

<x-card class="max-w-3xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                <x-input name="name" value="{{ old('name', $user->name) }}" required />
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address *</label>
                <x-input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mobile Number *</label>
                <x-input name="mobile" value="{{ old('mobile', $user->mobile) }}" required />
                @error('mobile') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Joining Date</label>
                <x-input type="date" name="joining_date" value="{{ old('joining_date', optional($user->joining_date)->format('Y-m-d')) }}" />
                @error('joining_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3">{{ old('address', $user->address) }}</textarea>
                @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                <input type="file" name="photo" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" />
                @error('photo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white">
                    <option value="MR" {{ old('role', $user->roles->first()?->name) == 'MR' ? 'selected' : '' }}>Medical Representative (MR)</option>
                    <option value="Admin" {{ old('role', $user->roles->first()?->name) == 'Admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white">
                    <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
            <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('admin.users.index') }}'">Cancel</x-button>
            <x-button type="submit" variant="primary">Update User</x-button>
        </div>
    </form>
</x-card>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Add Medical Representative</h2>
    <p class="text-sm text-gray-500">Create a new MR account. Employee Code will be auto-generated.</p>
</div>

<x-card class="max-w-3xl">
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                <x-input name="name" value="{{ old('name') }}" required />
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address *</label>
                <x-input type="email" name="email" value="{{ old('email') }}" required />
                @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mobile Number *</label>
                <x-input name="mobile" value="{{ old('mobile') }}" required />
                @error('mobile') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Joining Date</label>
                <x-input type="date" name="joining_date" value="{{ old('joining_date') }}" />
                @error('joining_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="3">{{ old('address') }}</textarea>
                @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Password *</label>
                <x-input type="password" name="password" required />
                @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                <x-input type="password" name="password_confirmation" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                <input type="file" name="photo" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" />
                @error('photo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 bg-white">
                    <option value="MR" {{ old('role') == 'MR' ? 'selected' : '' }}>Medical Representative (MR)</option>
                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
            <x-button type="button" variant="secondary" onclick="window.location.href='{{ route('admin.users.index') }}'">Cancel</x-button>
            <x-button type="submit" variant="primary">Create User</x-button>
        </div>
    </form>
</x-card>
@endsection

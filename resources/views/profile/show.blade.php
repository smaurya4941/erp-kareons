@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">My Profile</h2>
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg border border-green-100">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Profile Info -->
    <x-card class="md:col-span-1">
        <div class="flex flex-col items-center text-center">
            @if(auth()->user()->photo)
                <img src="{{ asset('storage/'.auth()->user()->photo) }}" class="w-32 h-32 rounded-full object-cover mb-4">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=1d4ed8&background=eff6ff" class="w-32 h-32 rounded-full mb-4">
            @endif
            <h3 class="text-xl font-bold">{{ auth()->user()->name }}</h3>
            <p class="text-gray-500">{{ auth()->user()->roles->first()?->name }}</p>
            <p class="mt-2 text-sm text-gray-600 font-semibold">{{ auth()->user()->employee_code }}</p>
        </div>
        <div class="mt-6 border-t pt-4">
            <div class="mb-2"><span class="font-semibold text-gray-700">Email:</span> {{ auth()->user()->email }}</div>
            <div class="mb-2"><span class="font-semibold text-gray-700">Mobile:</span> {{ auth()->user()->mobile }}</div>
            <div class="mb-2"><span class="font-semibold text-gray-700">Joined:</span> {{ optional(auth()->user()->joining_date)->format('d M Y') ?? 'N/A' }}</div>
        </div>
    </x-card>

    <!-- Change Password -->
    <x-card class="md:col-span-2">
        <x-slot name="header">
            <h4 class="font-semibold text-gray-800">Change Password</h4>
        </x-slot>
        
        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            
            <div class="mb-4 max-w-md">
                <label class="block text-sm font-medium text-gray-700">Current Password *</label>
                <x-input type="password" name="current_password" required />
                @error('current_password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4 max-w-md">
                <label class="block text-sm font-medium text-gray-700">New Password *</label>
                <x-input type="password" name="password" required />
                <span class="text-xs text-gray-500">Minimum 8 characters, at least one uppercase, one lowercase, and one number.</span>
                @error('password') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6 max-w-md">
                <label class="block text-sm font-medium text-gray-700">Confirm New Password *</label>
                <x-input type="password" name="password_confirmation" required />
            </div>

            <x-button type="submit" variant="primary">Update Password</x-button>
        </form>
    </x-card>
</div>
@endsection

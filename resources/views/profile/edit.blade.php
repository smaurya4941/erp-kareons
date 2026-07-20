@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">My Profile</h2>
    <p class="text-sm text-gray-500">Manage your personal information and security settings.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- Profile Information Form -->
    <div class="md:col-span-2">
        <x-card class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Profile Information</h3>
            
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="flex items-center space-x-6 mb-6">
                    <div class="shrink-0">
                        @if($user->profile_photo_path)
                            <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Current profile photo" />
                        @else
                            <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <label class="block">
                        <span class="sr-only">Choose profile photo</span>
                        <input type="file" name="avatar" class="block w-full text-sm text-slate-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100
                        "/>
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-label for="employee_code" value="Employee Code" />
                        <x-input id="employee_code" type="text" class="block w-full mt-1 bg-gray-100 cursor-not-allowed" value="{{ $user->employee_code }}" readonly />
                    </div>
                    <div>
                        <x-label for="name" value="Full Name *" />
                        <x-input id="name" name="name" type="text" class="block w-full mt-1" :value="old('name', $user->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="email" value="Email Address *" />
                        <x-input id="email" name="email" type="email" class="block w-full mt-1" :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="phone" value="Mobile Number" />
                        <x-input id="phone" name="phone" type="text" class="block w-full mt-1" :value="old('phone', $user->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="gender" value="Gender" />
                        <select id="gender" name="gender" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="dob" value="Date of Birth" />
                        <x-input id="dob" name="dob" type="date" class="block w-full mt-1" :value="old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '')" />
                        <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-label for="address" value="Residential Address" />
                        <textarea id="address" name="address" rows="3" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">{{ old('address', $user->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button variant="primary" type="submit">Save Profile</x-button>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Password Change Form -->
    <div>
        <x-card>
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Change Password</h3>
            
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <x-label for="current_password" value="Current Password *" />
                        <x-input id="current_password" name="current_password" type="password" class="block w-full mt-1" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="password" value="New Password *" />
                        <x-input id="password" name="password" type="password" class="block w-full mt-1" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters.</p>
                    </div>
                    <div>
                        <x-label for="password_confirmation" value="Confirm New Password *" />
                        <x-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full mt-1" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button variant="primary" type="submit">Update Password</x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

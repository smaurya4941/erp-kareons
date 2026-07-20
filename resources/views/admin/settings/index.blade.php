@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">System Settings</h2>
        <p class="text-sm text-gray-500">Configure global application parameters.</p>
    </div>
</div>

<div x-data="{ activeTab: '{{ session('last_tab', 'company') }}' }" class="flex flex-col md:flex-row gap-6">
    
    <!-- Settings Sidebar -->
    <div class="w-full md:w-64 shrink-0">
        <x-card class="p-0 overflow-hidden">
            <nav class="flex flex-col">
                <button @click="activeTab = 'company'" :class="{ 'bg-blue-50 text-blue-700 border-l-4 border-blue-600': activeTab === 'company', 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent': activeTab !== 'company' }" class="px-6 py-4 text-left font-semibold text-sm transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Company Profile
                </button>
                <button @click="activeTab = 'general'" :class="{ 'bg-blue-50 text-blue-700 border-l-4 border-blue-600': activeTab === 'general', 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent': activeTab !== 'general' }" class="px-6 py-4 text-left font-semibold text-sm transition-colors border-t flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    General Settings
                </button>
                <button @click="activeTab = 'maps'" :class="{ 'bg-blue-50 text-blue-700 border-l-4 border-blue-600': activeTab === 'maps', 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent': activeTab !== 'maps' }" class="px-6 py-4 text-left font-semibold text-sm transition-colors border-t flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    Google Maps
                </button>
                <button @click="activeTab = 'system'" :class="{ 'bg-blue-50 text-blue-700 border-l-4 border-blue-600': activeTab === 'system', 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent': activeTab !== 'system' }" class="px-6 py-4 text-left font-semibold text-sm transition-colors border-t flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    System Control
                </button>
            </nav>
        </x-card>
    </div>

    <!-- Settings Content -->
    <div class="flex-1">
        
        <!-- Company Settings -->
        <div x-show="activeTab === 'company'" style="display: none;">
            <x-card>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Company Profile</h3>
                <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="setting_group" value="company">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Branding -->
                        <div class="md:col-span-2 flex space-x-8 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                                <div class="flex items-center space-x-4">
                                    <div class="h-16 w-16 bg-gray-100 rounded flex items-center justify-center border">
                                        @if($getSetting('company_logo', 'company'))
                                            <img src="{{ asset('storage/' . $getSetting('company_logo', 'company')) }}" class="max-h-14 max-w-14 object-contain">
                                        @else
                                            <span class="text-xs text-gray-400">None</span>
                                        @endif
                                    </div>
                                    <input type="file" name="company_logo" class="text-sm">
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-label for="company_name" value="Company Name *" />
                            <x-input id="company_name" name="company_name" type="text" class="block w-full mt-1" value="{{ $getSetting('company_name', 'company', 'string', 'KareOns') }}" required />
                        </div>
                        <div>
                            <x-label for="company_email" value="Official Email" />
                            <x-input id="company_email" name="company_email" type="email" class="block w-full mt-1" value="{{ $getSetting('company_email', 'company') }}" />
                        </div>
                        <div>
                            <x-label for="company_phone" value="Official Phone" />
                            <x-input id="company_phone" name="company_phone" type="text" class="block w-full mt-1" value="{{ $getSetting('company_phone', 'company') }}" />
                        </div>
                        <div>
                            <x-label for="company_website" value="Website" />
                            <x-input id="company_website" name="company_website" type="text" class="block w-full mt-1" value="{{ $getSetting('company_website', 'company') }}" />
                        </div>
                        <div class="md:col-span-2">
                            <x-label for="company_address" value="Office Address" />
                            <textarea id="company_address" name="company_address" rows="3" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">{{ $getSetting('company_address', 'company') }}</textarea>
                        </div>
                        <div>
                            <x-label for="gst_number" value="GST Number" />
                            <x-input id="gst_number" name="gst_number" type="text" class="block w-full mt-1" value="{{ $getSetting('gst_number', 'company') }}" />
                        </div>
                        <div>
                            <x-label for="pan_number" value="PAN Number" />
                            <x-input id="pan_number" name="pan_number" type="text" class="block w-full mt-1" value="{{ $getSetting('pan_number', 'company') }}" />
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-button variant="primary" type="submit">Save Company Settings</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- General Settings -->
        <div x-show="activeTab === 'general'" style="display: none;">
            <x-card>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">General Settings</h3>
                <form method="POST" action="{{ route('admin.settings.store') }}">
                    @csrf
                    <input type="hidden" name="setting_group" value="general">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="timezone" value="System Timezone" />
                            <select id="timezone" name="timezone" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                                <option value="Asia/Kolkata" {{ $getSetting('timezone', 'general') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                <option value="UTC" {{ $getSetting('timezone', 'general') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="date_format" value="Date Format" />
                            <select id="date_format" name="date_format" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                                <option value="d-m-Y" {{ $getSetting('date_format', 'general') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                                <option value="Y-m-d" {{ $getSetting('date_format', 'general') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="m/d/Y" {{ $getSetting('date_format', 'general') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="time_format" value="Time Format" />
                            <select id="time_format" name="time_format" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                                <option value="12" {{ $getSetting('time_format', 'general') == '12' ? 'selected' : '' }}>12 Hour (AM/PM)</option>
                                <option value="24" {{ $getSetting('time_format', 'general') == '24' ? 'selected' : '' }}>24 Hour</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="currency" value="Default Currency" />
                            <select id="currency" name="currency" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                                <option value="INR" {{ $getSetting('currency', 'general') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                <option value="USD" {{ $getSetting('currency', 'general') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-button variant="primary" type="submit">Save General Settings</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Google Maps Settings -->
        <div x-show="activeTab === 'maps'" style="display: none;">
            <x-card>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Google Maps Integration</h3>
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                    <strong>Note:</strong> A valid Google Maps API Key is required for GPS attendance tracking and mapping doctor visit locations.
                </div>
                
                <form method="POST" action="{{ route('admin.settings.store') }}">
                    @csrf
                    <input type="hidden" name="setting_group" value="maps">
                    
                    <div class="space-y-4">
                        <div>
                            <x-label for="google_maps_api_key" value="Google Maps API Key" />
                            <x-input id="google_maps_api_key" name="google_maps_api_key" type="password" class="block w-full mt-1" value="{{ $getSetting('google_maps_api_key', 'maps') }}" placeholder="AIzaSy..." />
                            <p class="text-xs text-gray-500 mt-1">Leave blank to disable map features.</p>
                        </div>
                        <div>
                            <x-label for="map_zoom_level" value="Default Map Zoom Level" />
                            <x-input id="map_zoom_level" name="map_zoom_level" type="number" min="1" max="20" class="block w-1/3 mt-1" value="{{ $getSetting('map_zoom_level', 'maps', 'integer', '15') }}" />
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-button variant="primary" type="submit">Save Map Settings</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- System Settings -->
        <div x-show="activeTab === 'system'" style="display: none;">
            <x-card>
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 border-red-100">System Control</h3>
                
                <form method="POST" action="{{ route('admin.settings.store') }}">
                    @csrf
                    <input type="hidden" name="setting_group" value="system">
                    
                    <div class="space-y-6">
                        <div class="p-4 border rounded-lg bg-gray-50 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Maintenance Mode</h4>
                                <p class="text-xs text-gray-500">When enabled, MRs will not be able to access the system. Admins bypass this block.</p>
                            </div>
                            <div>
                                <label class="inline-flex relative items-center cursor-pointer">
                                    <input type="checkbox" name="maintenance_mode" value="true" class="sr-only peer" {{ $getSetting('maintenance_mode', 'system') == 'true' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="session_timeout" value="Session Timeout (Minutes)" />
                                <x-input id="session_timeout" name="session_timeout" type="number" class="block w-full mt-1" value="{{ $getSetting('session_timeout', 'system', 'integer', '120') }}" />
                            </div>
                            <div>
                                <x-label for="max_upload_size" value="Max File Upload Size (MB)" />
                                <x-input id="max_upload_size" name="max_upload_size" type="number" class="block w-full mt-1" value="{{ $getSetting('max_upload_size', 'system', 'integer', '5') }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-button variant="primary" type="submit">Save System Settings</x-button>
                    </div>
                </form>
            </x-card>
        </div>

    </div>
</div>
@endsection

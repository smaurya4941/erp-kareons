@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Mark Attendance</h2>
    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('l, d M Y') }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
    
    <!-- Status Card -->
    <x-card class="md:col-span-2">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Today's Status</h3>
                @if(!$attendance)
                    <p class="text-gray-500 mt-1">You have not checked in yet today.</p>
                @elseif($attendance && !$attendance->check_out_time)
                    <p class="text-green-600 font-semibold mt-1">You are currently checked in and working.</p>
                    <p class="text-sm text-gray-500 mt-1">Checked in at: {{ $attendance->check_in_time->format('h:i A') }}</p>
                @else
                    <p class="text-blue-600 font-semibold mt-1">You have completed your work for today.</p>
                    <p class="text-sm text-gray-500 mt-1">Working hours: {{ $attendance->formatted_working_hours }}</p>
                @endif
            </div>
            <div>
                @if(!$attendance)
                    <x-badge type="danger">Absent</x-badge>
                @elseif($attendance && !$attendance->check_out_time)
                    <x-badge type="warning">Incomplete</x-badge>
                @else
                    <x-badge type="success">Present</x-badge>
                @endif
            </div>
        </div>
    </x-card>

    @if(!$attendance || ($attendance && !$attendance->check_out_time))
    <!-- Capture UI -->
    <x-card class="md:col-span-2">
        <form 
            id="attendanceForm" 
            action="{{ !$attendance ? route('mr.attendance.checkin') : route('mr.attendance.checkout') }}" 
            method="POST" 
            enctype="multipart/form-data"
        >
            @csrf
            
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2">
                    {{ !$attendance ? 'Step 1: Check In' : 'Step 2: Check Out' }}
                </h3>
                <p class="text-sm text-gray-500">Please allow camera and location access to proceed.</p>
            </div>

            <!-- Video/Canvas Container -->
            <div class="flex flex-col items-center justify-center bg-gray-100 rounded-lg p-4 mb-6 border-2 border-dashed border-gray-300 relative overflow-hidden" style="min-height: 300px;">
                <video id="video" class="w-full max-w-md rounded shadow-sm bg-black" autoplay playsinline></video>
                <canvas id="canvas" class="hidden w-full max-w-md rounded shadow-sm"></canvas>
                
                <div id="camera-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 bg-opacity-90">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm text-gray-600">Initializing Camera...</span>
                </div>
            </div>

            <!-- GPS Status -->
            <div class="mb-6 p-4 rounded bg-gray-50 border">
                <h4 class="font-semibold text-gray-700 text-sm mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Location Data
                </h4>
                <div id="gps-status" class="text-sm text-yellow-600 font-medium">Fetching GPS coordinates...</div>
                
                <!-- Hidden inputs to hold the data -->
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <input type="hidden" name="accuracy" id="accuracy">
                <!-- Fallback file input if JS fails -->
                <input type="file" name="selfie" id="selfie-file" class="hidden">
            </div>

            <div class="flex justify-center space-x-4">
                <button type="button" id="capture-btn" class="px-6 py-3 bg-gray-800 text-white rounded-lg font-bold shadow-md hover:bg-gray-700 disabled:opacity-50 flex items-center" disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Take Selfie
                </button>
                <button type="button" id="retake-btn" class="hidden px-6 py-3 bg-gray-500 text-white rounded-lg font-bold shadow-md hover:bg-gray-600">
                    Retake
                </button>
                <button type="submit" id="submit-btn" class="hidden px-6 py-3 {{ !$attendance ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg font-bold shadow-md">
                    {{ !$attendance ? 'Confirm Check In' : 'Confirm Check Out' }}
                </button>
            </div>
        </form>
    </x-card>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('capture-btn');
            const retakeBtn = document.getElementById('retake-btn');
            const submitBtn = document.getElementById('submit-btn');
            const loading = document.getElementById('camera-loading');
            const gpsStatus = document.getElementById('gps-status');
            const latInput = document.getElementById('lat');
            const lngInput = document.getElementById('lng');
            const accuracyInput = document.getElementById('accuracy');
            
            let stream = null;
            let hasLocation = false;

            // 1. Initialize Camera
            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                    video.srcObject = stream;
                    loading.classList.add('hidden');
                    checkReadiness();
                } catch (err) {
                    loading.innerHTML = '<span class="text-red-500 font-bold">Error accessing camera. Please check permissions.</span>';
                    console.error('Camera error:', err);
                }
            }

            // 2. Initialize GPS
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latInput.value = position.coords.latitude;
                            lngInput.value = position.coords.longitude;
                            accuracyInput.value = position.coords.accuracy;
                            
                            let accText = `<span class="text-green-600">Lat: ${position.coords.latitude.toFixed(4)}, Lng: ${position.coords.longitude.toFixed(4)} <br>(Accuracy: ${Math.round(position.coords.accuracy)}m)</span>`;
                            if (position.coords.accuracy > 50) {
                                accText += ` <br><span class="text-red-500 text-xs">Warning: Poor GPS accuracy.</span>`;
                            }
                            gpsStatus.innerHTML = accText;
                            hasLocation = true;
                            checkReadiness();
                        },
                        (error) => {
                            gpsStatus.innerHTML = `<span class="text-red-500 font-bold">Error getting location: ${error.message}</span>`;
                            // Still allow readiness to proceed without strict GPS lock (based on business rules, can make strict later)
                            hasLocation = true; 
                            checkReadiness();
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    gpsStatus.innerHTML = "Geolocation is not supported by this browser.";
                    hasLocation = true;
                    checkReadiness();
                }
            }

            function checkReadiness() {
                if (stream && hasLocation) {
                    captureBtn.removeAttribute('disabled');
                }
            }

            startCamera();
            getLocation();

            // 3. Capture Logic
            captureBtn.addEventListener('click', () => {
                // Draw video to canvas
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                
                // Hide video, show canvas
                video.classList.add('hidden');
                canvas.classList.remove('hidden');
                
                // Toggle buttons
                captureBtn.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
                submitBtn.classList.remove('hidden');
                
                // Convert canvas to Blob and stick it in the file input via DataTransfer
                canvas.toBlob((blob) => {
                    const file = new File([blob], "selfie.jpg", { type: "image/jpeg" });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('selfie-file').files = dt.files;
                }, 'image/jpeg', 0.8); // 80% quality
            });

            // 4. Retake Logic
            retakeBtn.addEventListener('click', () => {
                video.classList.remove('hidden');
                canvas.classList.add('hidden');
                
                captureBtn.classList.remove('hidden');
                retakeBtn.classList.add('hidden');
                submitBtn.classList.add('hidden');
                
                document.getElementById('selfie-file').value = '';
            });
            
            // Clean up camera on form submit so it doesn't stay on
            document.getElementById('attendanceForm').addEventListener('submit', () => {
                if(stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                submitBtn.innerHTML = 'Processing...';
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        });
    </script>
    @endif
</div>
@endsection

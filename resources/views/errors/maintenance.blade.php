<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - KareOns ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col items-center justify-center">
    
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
        <!-- Logo -->
        <div class="mb-6 flex justify-center">
            @if(setting('company_logo'))
                <img src="{{ asset('storage/' . setting('company_logo')) }}" alt="Company Logo" class="h-16 object-contain">
            @else
                <div class="h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                    K
                </div>
            @endif
        </div>

        <!-- Maintenance Icon -->
        <div class="mx-auto w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">System Under Maintenance</h1>
        <p class="text-gray-600 mb-6">
            {{ setting('company_name', 'KareOns') }} ERP is currently undergoing scheduled maintenance and updates. 
            We are working hard to bring you new features and improvements.
        </p>
        
        <p class="text-sm font-semibold text-blue-600">
            Please check back soon!
        </p>
        
        <div class="mt-8 pt-6 border-t border-gray-100">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-900 underline">Sign Out</button>
                </form>
            @endauth
        </div>
    </div>
    
    <div class="mt-8 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} {{ setting('company_name', 'KareOns') }}. All rights reserved.
    </div>

</body>
</html>

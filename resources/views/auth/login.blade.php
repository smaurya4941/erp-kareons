<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ company_name() }}</title>
    <link rel="icon" type="image/png" href="{{ favicon_url() }}">
    @vite(['resources/css/app.css'])
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-50 relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-brand-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="w-full max-w-md p-10 bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white relative z-10 mx-4">
        <div class="mb-10 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white shadow-lg shadow-brand-500/20 mb-6 overflow-hidden">
                <x-brand-logo class="w-16 h-16" />
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ company_name() }}</h1>
            <p class="text-sm font-medium text-gray-500 mt-2">Sign in to your account</p>
        </div>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            
            <div class="mb-5">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <x-input name="email" type="email" value="{{ old('email') }}" required autofocus class="pl-11 w-full bg-gray-50/50 border-gray-200 focus:bg-white rounded-xl py-3" placeholder="you@kareons.com" />
                </div>
                @error('email') <span class="text-xs font-semibold text-red-500 mt-2 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Password</label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <x-input name="password" type="password" required class="pl-11 w-full bg-gray-50/50 border-gray-200 focus:bg-white rounded-xl py-3" placeholder="••••••••" />
                </div>
            </div>

            <div class="flex items-center justify-between mb-8">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 transition-colors">
                    <span class="ml-2 text-sm font-medium text-gray-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-brand-500/30 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform hover:-translate-y-0.5">
                Sign In
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>
    </div>
</body>
</html>

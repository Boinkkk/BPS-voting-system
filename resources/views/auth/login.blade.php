@extends('layouts.guest')

@section('content')
<div class="max-w-md w-full mx-auto bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden relative">
    
    <div class="p-8 sm:p-10">
        <div class="flex justify-center mb-6">
            <!-- Placeholder for BPS Logo, user can replace this later -->
            <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-2xl shadow-md rotate-3">
                <div class="-rotate-3">BPS</div>
            </div>
        </div>
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">BPS Selection System</h2>
            <p class="text-sm text-gray-500">Secure Employee Portal Login</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="identifier" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Username or NIP
                </label>
                <div class="mt-1">
                    <input id="identifier" name="identifier" type="text" autocomplete="email" required value="{{ old('identifier') }}"
                        class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm bg-gray-50/50"
                        placeholder="Enter your identification number">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="flex items-center text-sm font-medium text-gray-700">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Password
                    </label>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-sky-600 hover:text-sky-500 text-xs">
                            Forgot password?
                        </a>
                    </div>
                </div>
                <div class="mt-1 relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm bg-gray-50/50 pr-10"
                        placeholder="••••••••">
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded cursor-pointer">
                <label for="remember" class="ml-2 block text-xs text-gray-700 cursor-pointer">
                    Remember this device
                </label>
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#0D628B] hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors duration-200">
                    Sign In
                    <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
            
            <div class="mt-6 border-t border-gray-100 pt-6"></div>
        </form>
    </div>
</div>

<div class="mt-8 text-center text-xs text-gray-500">
    <p>&copy; {{ date('Y') }} Badan Pusat Statistik (BPS). All rights reserved.</p>
    <p class="mt-1">For technical assistance, contact the BPS Service Desk.</p>
</div>
@endsection

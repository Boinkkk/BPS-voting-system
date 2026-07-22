@extends('layouts.guest')

@section('content')
<main class="w-full flex-grow flex flex-col items-center justify-center px-margin-mobile md:px-margin-desktop py-xl animate-fade-in relative z-10">
    <!-- Logo / Branding Center -->
    <div class="mb-10 text-center flex flex-col items-center">
        <img alt="BPS Logo" class="h-16 md:h-20 w-auto mb-6 drop-shadow-sm" src="{{ asset('images/logo.svg') }}">
        <h1 class="font-headline-lg text-headline-lg md:text-[32px] md:leading-[40px] text-[28px] leading-[36px] font-bold text-on-surface tracking-tight">SIVOTA</h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant opacity-75 mt-1 uppercase tracking-wider">
            Sistem Informasi Voting Terpadu
        </p>
        
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-[440px] bg-surface-container-lowest rounded-2xl md:rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant p-8 md:p-10 login-card transition-all duration-300">
        
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <div class="flex">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6" id="loginForm">
            @csrf
            
            <!-- Username Field -->
            <div class="space-y-2">
                <label class="flex items-center gap-2 font-label-md text-label-md font-semibold text-on-surface-variant" for="identifier">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Username or NIP
                </label>
                <div class="relative flex items-center input-focus-glow transition-all rounded-lg">
                    <input class="w-full px-4 py-3.5 bg-surface-container-low border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:border-primary focus:ring-0 transition-all focus:ring-2 focus:ring-primary/10" id="identifier" name="identifier" placeholder="admin@bps.go.id" required type="text" value="{{ old('identifier') }}">
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="flex items-center gap-2 font-label-md text-label-md font-semibold text-on-surface-variant" for="password">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Password
                    </label>
                    <a class="font-label-md text-label-md font-semibold text-primary hover:underline transition-all" href="#">
                        Forgot password?
                    </a>
                </div>
                <div class="relative flex items-center input-focus-glow transition-all rounded-lg group">
                    <input class="w-full px-4 py-3.5 pr-12 bg-surface-container-low border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:border-primary focus:ring-0 transition-all focus:ring-2 focus:ring-primary/10" id="password" name="password" placeholder="••••••••••••" required type="password">
                    <button class="absolute right-3 text-on-surface-variant hover:text-primary p-1 transition-colors" onclick="togglePassword()" type="button">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="passwordIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-3">
                <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary focus:ring-offset-0 bg-surface-container-low transition-all cursor-pointer" id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                <label class="font-body-sm text-body-sm text-on-surface-variant cursor-pointer select-none" for="remember">
                    Remember this device
                </label>
            </div>

            <!-- Primary Action -->
            <button class="btn-active mt-2 group relative w-full bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold py-4 rounded-xl md:rounded-2xl flex items-center justify-center gap-2 hover:bg-primary-container active:scale-[0.98] transition-all overflow-hidden shadow-lg shadow-primary/20" type="submit" id="submitBtn">
                <span class="btn-text">Sign In</span>
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="submitIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                <!-- Subtle shimmer effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
            </button>
        </form>

        <!-- Support / Extra Options -->
        <div class="mt-8 pt-8 border-t border-surface-variant flex flex-col items-center gap-4">
            <p class="font-body-sm text-body-sm text-on-surface-variant text-center">
                Need technical assistance? Contact BPS Service Desk.
            </p>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }

    const form = document.getElementById('loginForm');
    form.addEventListener('submit', () => {
        const btn = document.getElementById('submitBtn');
        const text = btn.querySelector('.btn-text');
        const icon = btn.querySelector('.btn-icon');
        btn.disabled = true;
        text.textContent = 'Authenticating...';
        icon.innerHTML = '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" /><path fill="currentColor" stroke="none" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75" />';
        icon.classList.add('animate-spin');
    });

    // Interactive background effect
    document.addEventListener('mousemove', (e) => {
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        document.body.style.backgroundImage = `radial-gradient(circle at ${x * 100}% ${y * 100}%, rgba(0,145,218,0.03) 0%, transparent 50%)`;
    });
</script>
@endpush

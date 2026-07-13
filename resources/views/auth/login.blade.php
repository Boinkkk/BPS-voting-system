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
                    <div class="flex-shrink-0">
                        <span class="material-symbols-outlined text-red-400 text-[20px]">error</span>
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
                    <span class="material-symbols-outlined text-[20px]" data-icon="person">person</span>
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
                        <span class="material-symbols-outlined text-[20px]" data-icon="lock">lock</span>
                        Password
                    </label>
                    <a class="font-label-md text-label-md font-semibold text-primary hover:underline transition-all" href="#">
                        Forgot password?
                    </a>
                </div>
                <div class="relative flex items-center input-focus-glow transition-all rounded-lg group">
                    <input class="w-full px-4 py-3.5 pr-12 bg-surface-container-low border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline focus:border-primary focus:ring-0 transition-all focus:ring-2 focus:ring-primary/10" id="password" name="password" placeholder="••••••••••••" required type="password">
                    <button class="absolute right-3 text-on-surface-variant hover:text-primary p-1 transition-colors" onclick="togglePassword()" type="button">
                        <span class="material-symbols-outlined" data-icon="visibility" id="passwordIcon">visibility</span>
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
                <span class="material-symbols-outlined transition-transform group-hover:translate-x-1 btn-icon" data-icon="login">login</span>
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
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    const form = document.getElementById('loginForm');
    form.addEventListener('submit', () => {
        const btn = document.getElementById('submitBtn');
        const text = btn.querySelector('.btn-text');
        const icon = btn.querySelector('.btn-icon');
        btn.disabled = true;
        text.textContent = 'Authenticating...';
        icon.textContent = 'progress_activity';
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

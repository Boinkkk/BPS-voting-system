@extends('layouts.guest')

@section('content')
<main class="w-full min-h-screen flex items-center justify-center bg-white overflow-hidden relative">
    
    <div class="flex w-full min-h-screen relative z-10">
        
        <!-- Left Column: Interactive Visuals (Hidden on small screens) -->
        <div class="hidden lg:flex lg:w-[55%] relative bg-slate-900 items-center justify-center overflow-hidden group" id="interactive-bg">
            <!-- Dynamic background -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700 via-primary to-blue-900 z-0"></div>
            
            <!-- Mouse follower glow -->
            <div id="mouse-glow" class="absolute w-[800px] h-[800px] bg-white rounded-full mix-blend-overlay filter blur-[150px] opacity-20 pointer-events-none transition-transform duration-75 ease-out z-10 top-0 left-0" style="transform: translate(-50%, -50%);"></div>
            
            <!-- Floating Elements / Particles -->
            <div class="absolute inset-0 z-20 pointer-events-none opacity-40 transition-transform duration-700 ease-out" id="particles">
                <div class="absolute w-40 h-40 border border-white/20 rounded-full top-1/4 left-1/4 animate-pulse" style="animation-duration: 4s;"></div>
                <div class="absolute w-80 h-80 border border-white/10 rounded-full top-2/3 right-1/4 animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>
                <div class="absolute w-12 h-12 border-2 border-white/30 rotate-45 top-1/3 right-1/3 animate-bounce"></div>
            </div>

            <div class="relative z-30 flex flex-col items-center justify-center p-12 text-center h-full max-w-2xl">
                <!-- Wrapper for Logo to scale on hover -->
                <div class="transform transition-transform duration-500 group-hover:scale-105 mb-8">
                    <img alt="BPS Logo" class="h-28 w-auto drop-shadow-2xl" src="{{ asset('images/logo.svg') }}">
                </div>
                <h1 class="text-5xl font-extrabold text-white tracking-tight leading-tight mb-6 drop-shadow-lg" id="hero-title">
                    Data Mencerdaskan Bangsa
                </h1>
                <p class="text-xl text-blue-50 font-medium leading-relaxed opacity-90 drop-shadow">
                    Sistem Informasi Voting Terpadu Badan Pusat Statistik. Mewujudkan efisiensi, transparansi, dan akurasi tinggi.
                </p>
            </div>
            
            <!-- Decorative overlay pattern -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_2px,transparent_2px)] [background-size:32px_32px] z-20 mix-blend-overlay"></div>
        </div>

        <!-- Right Column: Clean Form -->
        <div class="w-full lg:w-[45%] flex flex-col items-center justify-center bg-white p-6 sm:p-12 md:p-24 relative shadow-[-20px_0_40px_rgba(0,0,0,0.1)] z-30">
            <div class="w-full max-w-md mx-auto relative z-10">
                
                <!-- Mobile Logo -->
                <div class="flex lg:hidden flex-col items-center mb-8">
                    <img alt="BPS Logo" class="h-16 w-auto mb-4 drop-shadow-sm" src="{{ asset('images/logo.svg') }}">
                </div>

                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Selamat Datang</h2>
                    <p class="text-slate-500 font-medium">Silakan masuk ke akun SIVOTA Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate-fade-in-up">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium">
                                    {{ $errors->first() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-7" id="loginForm">
                    @csrf

                    <!-- Username Field -->
                    <div class="space-y-2 relative group">
                        <label class="text-xs font-bold text-slate-500 tracking-wider uppercase" for="identifier">
                            Username / NIP
                        </label>
                        <div class="relative flex items-center transition-all overflow-hidden rounded-xl bg-slate-50 border border-slate-200 focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/20 hover:border-slate-300">
                            <div class="w-14 flex items-center justify-center text-slate-400 group-focus-within:text-primary transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <input class="w-full py-4 pr-4 bg-transparent text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-0 border-none" id="identifier" name="identifier" placeholder="admin@bps.go.id" required type="text" value="{{ old('identifier') }}">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2 relative group">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold text-slate-500 tracking-wider uppercase" for="password">
                                Password
                            </label>
                            <a class="text-sm font-bold text-primary hover:text-blue-700 hover:underline transition-all" href="#">
                                Lupa Password?
                            </a>
                        </div>
                        <div class="relative flex items-center transition-all overflow-hidden rounded-xl bg-slate-50 border border-slate-200 focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/20 hover:border-slate-300">
                            <div class="w-14 flex items-center justify-center text-slate-400 group-focus-within:text-primary transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <input class="w-full py-4 pr-12 bg-transparent text-slate-900 font-medium placeholder:text-slate-400 focus:outline-none focus:ring-0 border-none" id="password" name="password" placeholder="••••••••••••" required type="password">
                            <button class="absolute right-4 text-slate-400 hover:text-primary transition-colors focus:outline-none" onclick="togglePassword()" type="button" aria-label="Toggle password visibility">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="passwordIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-3 pt-2">
                        <input class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary focus:ring-offset-0 bg-white transition-all cursor-pointer" id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                        <label class="text-sm font-medium text-slate-600 cursor-pointer select-none hover:text-slate-900 transition-colors" for="remember">
                            Ingat perangkat ini
                        </label>
                    </div>

                    <!-- Primary Action -->
                    <button class="relative w-full bg-slate-900 text-white font-bold text-lg py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-primary hover:-translate-y-1 active:scale-[0.98] transition-all overflow-hidden group shadow-[0_8px_20px_rgba(0,0,0,0.1)] hover:shadow-[0_15px_30px_rgba(0,145,218,0.3)] duration-300" type="submit" id="submitBtn">
                        <span class="btn-text z-10 transition-transform group-hover:-translate-x-1">Sign In</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-4 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0 btn-icon z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="submitIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        
                        <!-- Sweep effect -->
                        <div class="absolute inset-0 bg-white/20 -translate-x-[150%] skew-x-[-30deg] group-hover:animate-[sweep_0.75s_ease-in-out_forwards] z-0"></div>
                    </button>
                </form>

                <!-- Footer under form -->
                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col items-center">
                    <p class="text-sm font-medium text-slate-400 text-center">
                        Butuh bantuan teknis? <a href="#" class="text-primary font-bold hover:underline">Hubungi BPS Service Desk</a>
                    </p>
                </div>
            </div>
            
            <!-- Minimalist decorative background behind the form -->
            <div class="absolute right-0 bottom-0 w-64 h-64 bg-slate-50 rounded-tl-[100px] -z-10 opacity-50 pointer-events-none"></div>
        </div>

    </div>
</main>
@endsection

@push('scripts')
<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.4s ease-out forwards;
    }
    @keyframes sweep {
        0% { transform: translateX(-150%) skewX(-30deg); }
        100% { transform: translateX(150%) skewX(-30deg); }
    }
    
    /* Hide the global footer for a cleaner login page */
    footer {
        display: none !important;
    }
    
    /* Fix Chrome Autofill background color */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px #f8fafc inset !important; /* Matches bg-slate-50 */
        -webkit-text-fill-color: #0f172a !important; /* Matches text-slate-900 */
        transition: background-color 5000s ease-in-out 0s;
    }
</style>
<script>
    // Password toggle
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

    // Interactive Mouse Glow & Parallax on the left panel
    const bgContainer = document.getElementById('interactive-bg');
    const mouseGlow = document.getElementById('mouse-glow');
    const particles = document.getElementById('particles');
    const heroTitle = document.getElementById('hero-title');

    if(bgContainer && mouseGlow) {
        let mouseX = 0, mouseY = 0;
        
        bgContainer.addEventListener('mousemove', (e) => {
            const rect = bgContainer.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
            
            // Mouse glow center position
            mouseGlow.style.left = mouseX + 'px';
            mouseGlow.style.top = mouseY + 'px';

            // Parallax effect for particles
            const xOffset = (mouseX / rect.width - 0.5) * 40;
            const yOffset = (mouseY / rect.height - 0.5) * 40;
            
            if(particles) {
                particles.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
            }
            if(heroTitle) {
                heroTitle.style.transform = `translate(${xOffset * 0.3}px, ${yOffset * 0.3}px)`;
            }
        });
        
        bgContainer.addEventListener('mouseleave', () => {
            if(particles) particles.style.transform = 'translate(0, 0)';
            if(heroTitle) heroTitle.style.transform = 'translate(0, 0)';
        });
    }

    // Submit state
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', () => {
        const btn = document.getElementById('submitBtn');
        const text = btn.querySelector('.btn-text');
        const icon = btn.querySelector('.btn-icon');
        
        btn.disabled = true;
        btn.classList.add('opacity-90', 'cursor-not-allowed', 'scale-[0.98]');
        
        text.textContent = 'Authenticating...';
        icon.innerHTML = '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" /><path fill="currentColor" stroke="none" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75" />';
        icon.classList.add('animate-spin', 'opacity-100', 'translate-x-0');
    });

</script>
@endpush

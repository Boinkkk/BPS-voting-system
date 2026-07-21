<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIVOTA - Sistem Informasi Voting Terpadu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            bps: {
                                primary: '#0F4C81',
                                secondary: '#2563EB',
                                accent: '#F59E0B',
                                green: '#10b981',
                                dark: '#0a192f'
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <!-- GSAP for Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            background-color: #ffffff;
            color: #333333;
        }

        /* Glassmorphism & SaaS Elements */
        .glass-nav {
            background: rgba(15, 76, 129, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-nav.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(15, 76, 129, 0.15);
            border-color: rgba(15, 76, 129, 0.2);
        }

        .stat-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px -10px rgba(15, 76, 129, 0.15);
        }

        /* Number Circle */
        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #fffbeb;
            border: 2px solid #F59E0B;
            color: #F59E0B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
        }

        .step-item:hover .step-circle {
            background-color: #F59E0B;
            color: white;
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        /* Custom Buttons */
        .btn-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-yellow {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-dark {
            background: #0a192f;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(10, 25, 47, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-ripple::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 150%;
            height: 150%;
            background: rgba(255,255,255,0.2);
            transform: translate(-50%, -50%) scale(0);
            border-radius: 50%;
            transition: transform 0.5s ease;
        }

        .btn-green:hover, .btn-yellow:hover, .btn-dark:hover {
            transform: translateY(-2px);
        }
        
        .btn-green:hover { box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5); }
        .btn-yellow:hover { box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5); }
        .btn-dark:hover { box-shadow: 0 8px 25px rgba(10, 25, 47, 0.5); }

        .btn-green:hover::before, .btn-yellow:hover::before, .btn-dark:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }

        /* Animated Wavy Divider */
        .wave-divider {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 10;
        }
        
        .waves {
            position: relative;
            width: 100%;
            height: 120px;
            margin-bottom: -7px; /* Fix Safari gap */
        }

        .wave-layer-1 { fill: rgba(255, 255, 255, 0.7); }
        .wave-layer-2 { fill: rgba(219, 234, 254, 0.5); } /* Soft blue glassmorphism */
        .wave-layer-3 { fill: rgba(255, 255, 255, 0.3); }
        .wave-layer-4 { fill: #ffffff; }

        .parallax > use {
            animation: move-forever 25s cubic-bezier(0.55, 0.5, 0.45, 0.5) infinite;
            transform: translate3d(0, 0, 0); /* Force Hardware Acceleration */
            will-change: transform;
        }

        .parallax > use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 12s;
        }
        .parallax > use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 16s;
        }
        .parallax > use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 20s;
        }
        .parallax > use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 24s;
        }

        @keyframes move-forever {
            0% { transform: translate3d(-90px, 0, 0); }
            100% { transform: translate3d(85px, 0, 0); }
        }

        @media (max-width: 768px) {
            .waves { height: 60px; min-height: 60px; }
        }

        /* Mouse cursor glow (adapted for both dark and light sections) */
        #cursor-glow {
            position: fixed;
            top: 0;
            left: 0;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 60%);
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            z-index: 10;
            transition: opacity 0.3s ease;
            opacity: 0;
            mix-blend-mode: overlay;
        }

        /* Animation Classes */
        .gsap-hide {
            opacity: 0;
            visibility: hidden;
        }

        /* Hero Abstract Shape Animation */
        .floating-shape {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
    </style>
</head>
<body class="selection:bg-bps-accent/30 selection:text-bps-dark">
    
    <div id="cursor-glow"></div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3 cursor-pointer group">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo BPS" class="h-10 w-auto transform transition-transform group-hover:scale-105">
                    <div class="h-8 w-px bg-white/30 mx-1"></div>
                    <span class="text-2xl font-bold tracking-tight text-white">SIVOTA</span>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="#" class="text-sm font-semibold text-white border-b-2 border-bps-accent pb-1">Beranda</a>
                    <a href="#mekanisme" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Mekanisme</a>
                    <a href="#tentang" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Tentang</a>
                    <a href="#faq" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">FAQ</a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-white hover:text-bps-accent transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-white hover:text-bps-accent transition-colors mr-2">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Section 1: Hero -->
    <section class="relative pt-32 pb-40 lg:pt-48 lg:pb-56 overflow-hidden bg-bps-primary text-white">
        <!-- Parallax Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -right-[10%] w-[70%] h-[70%] rounded-full bg-blue-400/10 blur-[100px] gsap-parallax" data-speed="0.5"></div>
            <div class="absolute bottom-[10%] -left-[10%] w-[50%] h-[50%] rounded-full bg-bps-accent/10 blur-[80px] gsap-parallax" data-speed="0.3"></div>
            
            <!-- Floating Particles -->
            <div class="absolute top-1/4 left-1/4 w-2 h-2 rounded-full bg-white/20 animate-ping"></div>
            <div class="absolute top-1/3 right-1/4 w-3 h-3 rounded-full bg-bps-accent/40 floating-shape" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-1/4 left-1/3 w-2 h-2 rounded-full bg-green-400/40 floating-shape" style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-20">
                
                <!-- Hero Content -->
                <div class="w-full lg:w-1/2 text-center lg:text-left">
                    <h1 class="text-4xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-[1.2] gsap-hero">
                        Selamat datang di <br/>
                        <span class="relative inline-block mt-2">
                            SIVOTA
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-bps-accent" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0,5 Q50,10 100,5" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </h1>
                    
                    <p class="mt-6 text-lg lg:text-xl text-blue-100 max-w-2xl mx-auto lg:mx-0 leading-relaxed mb-10 gsap-hero">
                        Platform sistem voting digital yang dirancang untuk mendukung proses pemilihan secara mudah, transparan, adil, dan akuntabel di lingkungan instansi.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start gsap-hero">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-green btn-ripple px-8 py-3.5 rounded-full text-base font-bold tracking-wide flex items-center justify-center gap-2">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-green btn-ripple px-8 py-3.5 rounded-full text-base font-bold tracking-wide flex items-center justify-center gap-2">
                                Mulai Sekarang
                            </a>
                        @endauth
                        <a href="#mekanisme" class="btn-yellow btn-ripple px-8 py-3.5 rounded-full text-base font-bold tracking-wide flex items-center justify-center text-white">
                            Info Lebih Lanjut
                        </a>
                    </div>
                </div>

                <!-- Hero Illustration (SaaS Premium Winner Badge) -->
                <div class="w-full lg:w-1/2 relative flex justify-center items-center mt-16 lg:mt-0 gsap-hero-img perspective-dramatic">
                    
                    @if(isset($pemenangTerbaru) && $pemenangTerbaru->kandidat && $pemenangTerbaru->kandidat->pegawai)
                        <div class="relative w-[280px] h-[280px] lg:w-[400px] lg:h-[400px] floating-shape flex items-center justify-center">
                            
                            <!-- Outer Glowing Ring -->
                            <div class="absolute inset-0 rounded-full border border-yellow-400/30 bg-gradient-to-br from-yellow-400/20 to-blue-500/10 backdrop-blur-xl animate-spin" style="animation-duration: 20s;"></div>
                            
                            <!-- Second Ring -->
                            <div class="absolute inset-6 rounded-full border border-white/20 bg-white/5 backdrop-blur-md"></div>

                            <!-- User Photo -->
                            <div class="relative z-10 w-48 h-48 lg:w-72 lg:h-72 rounded-full p-2 bg-gradient-to-b from-yellow-400 to-yellow-600 shadow-[0_0_50px_rgba(250,204,21,0.5)]">
                                <div class="w-full h-full rounded-full overflow-hidden bg-bps-primary border-4 border-white/20 relative flex items-center justify-center">
                                    <img src="{{ $pemenangTerbaru->kandidat->pegawai->foto_profil_url }}" onerror="this.src='{{ asset('images/default_profile.svg') }}'" class="w-full h-full object-cover" alt="Foto Pemenang">
                                    <div class="absolute inset-0 shadow-[inset_0_0_20px_rgba(0,0,0,0.5)]"></div>
                                </div>
                            </div>
                            
                            <!-- Name floating pill (SaaS Ribbon) -->
                            <div class="absolute -bottom-10 lg:-bottom-14 left-1/2 transform -translate-x-1/2 w-[120%] lg:w-[130%] z-20 flex flex-col items-center">
                                <div class="bg-white/10 backdrop-blur-2xl border border-white/30 shadow-[0_15px_35px_rgb(0,0,0,0.25)] rounded-3xl px-6 py-4 lg:py-5 w-full text-center relative overflow-hidden">
                                    <!-- Shine effect -->
                                    <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/30 to-transparent"></div>
                                    
                                    <h3 class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight drop-shadow-md truncate">
                                        {{ $pemenangTerbaru->kandidat->pegawai->nama }}
                                    </h3>
                                </div>
                                
                                <!-- Period & Title Badge -->
                                <div class="mt-4 flex flex-col items-center justify-center">
                                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-bps-dark text-xs lg:text-sm font-bold uppercase tracking-widest px-6 py-2 rounded-full shadow-[0_5px_15px_rgba(250,204,21,0.4)] border border-yellow-300">
                                        Pegawai Terbaik
                                    </div>
                                    <span class="mt-3 text-sm text-blue-200 font-bold tracking-widest uppercase opacity-90 drop-shadow-md">
                                        {{ $pemenangTerbaru->periode->nama ?? 'Periode Terbaru' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Floating Stars -->
                            <svg class="absolute -top-4 right-12 w-10 h-10 text-yellow-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="absolute bottom-24 -left-8 w-8 h-8 text-yellow-300 animate-bounce" fill="currentColor" viewBox="0 0 20 20" style="animation-duration: 3s;"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    @else
                        <!-- Generic Placeholder -->
                        <div class="relative w-[280px] h-[280px] lg:w-[400px] lg:h-[400px] floating-shape flex items-center justify-center">
                            <div class="absolute inset-0 rounded-full border border-white/20 bg-white/5 backdrop-blur-md border-dashed"></div>
                            <div class="text-center z-10 p-8">
                                <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                <h3 class="text-2xl font-bold text-white mb-2">SIVOTA</h3>
                                <p class="text-blue-200">Menunggu Pemilihan Pegawai Terbaik.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Animated Wavy Divider -->
        <div class="wave-divider">
            <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                </defs>
                <g class="parallax">
                    <use xlink:href="#gentle-wave" x="48" y="0" class="wave-layer-1" />
                    <use xlink:href="#gentle-wave" x="48" y="3" class="wave-layer-2" />
                    <use xlink:href="#gentle-wave" x="48" y="5" class="wave-layer-3" />
                    <use xlink:href="#gentle-wave" x="48" y="7" class="wave-layer-4" />
                </g>
            </svg>
        </div>
    </section>

    <!-- Section 2: Tentang & Mekanisme -->
    <section id="mekanisme" class="py-20 lg:py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            
            <!-- About SIVOTA -->
            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32 gsap-fade-up">
                <div class="w-full lg:w-1/2 flex justify-center">
                    <!-- Abstract Premium SVG Icon -->
                    <div class="relative w-64 h-64">
                        <div class="absolute inset-0 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
                        <svg class="w-full h-full relative z-10" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Hexagon base -->
                            <path d="M100 20L169.282 60V140L100 180L30.718 140V60L100 20Z" fill="#F8FAFC" stroke="#E2E8F0" stroke-width="4"/>
                            <!-- Colored segments representing data/voting -->
                            <path d="M100 20L169.282 60V140L100 100V20Z" fill="#0F4C81" opacity="0.1"/>
                            <path d="M30.718 60L100 20V100L30.718 60Z" fill="#F59E0B" opacity="0.15"/>
                            <path d="M30.718 140L100 100V180L30.718 140Z" fill="#10B981" opacity="0.1"/>
                            <!-- Bold Checkmark / Graphic -->
                            <path d="M60 105L85 130L150 65" stroke="#F59E0B" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" class="animate-dash" style="stroke-dasharray: 200; stroke-dashoffset: 0;"/>
                            <path d="M60 105L85 130L150 65" stroke="#0F4C81" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" style="transform: translate(-4px, 4px); mix-blend-mode: multiply; opacity: 0.2;"/>
                        </svg>
                    </div>
                </div>
                
                <div class="w-full lg:w-1/2">
                    <h2 class="text-3xl lg:text-4xl font-bold text-bps-primary mb-6">Penilaian Pegawai Terbaik</h2>
                    <p class="text-gray-600 leading-relaxed mb-4 text-justify">
                        Sistem Informasi Voting Terpadu (SIVOTA) adalah platform evaluasi kinerja yang menggabungkan data objektif (Capaian Kinerja Pegawai dan Absensi) dengan penilaian subjektif partisipatif (Voting Ber-AKHLAK).
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-8 text-justify">
                        Mekanisme ini bertujuan untuk menghindari bias penilaian, mendorong transparansi, dan menghasilkan kandidat pegawai terbaik yang dapat dipertanggungjawabkan secara teknis maupun moral, mewujudkan lingkungan kerja yang profesional dan efisien.
                    </p>
                    <a href="#" class="btn-dark btn-ripple inline-flex items-center gap-2 px-8 py-3 rounded-full text-sm font-semibold">
                        Selengkapnya 
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>

            <!-- Mechanism Steps -->
            <div class="mb-12 gsap-fade-up">
                <div class="flex items-center gap-4 mb-2">
                    <div class="h-px bg-bps-accent w-12"></div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Standar Pelayanan</span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-bps-primary uppercase">MEKANISME PENILAIAN PEGAWAI TERBAIK</h2>
            </div>

            <!-- Grid Layout mimicking Romantik (2 columns, staggered) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10 relative">
                
                <!-- Vertical connecting line (mobile hidden, desktop centered) -->
                <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-px bg-gray-200 transform -translate-x-1/2 z-0"></div>

                <!-- Step 1 -->
                <div class="flex gap-6 step-item relative z-10 bg-white p-4 rounded-2xl hover:bg-gray-50 transition-colors gsap-step">
                    <div class="shrink-0">
                        <div class="step-circle">1</div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Input Data</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Kepala Umum melakukan input data Capaian Kinerja Pegawai (CKP) dan Absensi secara berkala melalui sistem.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-6 step-item relative z-10 bg-white p-4 rounded-2xl hover:bg-gray-50 transition-colors md:mt-12 gsap-step">
                    <div class="shrink-0">
                        <div class="step-circle">2</div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Perhitungan 10 Terbaik</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Sistem secara otomatis menghitung dan merangking 10 kandidat terbaik dengan mempertimbangkan bobot absensi dan skor CKP.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-6 step-item relative z-10 bg-white p-4 rounded-2xl hover:bg-gray-50 transition-colors gsap-step">
                    <div class="shrink-0">
                        <div class="step-circle">3</div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Voting Ber-AKHLAK</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Pemungutan suara dilakukan oleh seluruh pegawai dengan menjawab 12 pertanyaan terkait penerapan nilai dasar AKHLAK.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-6 step-item relative z-10 bg-white p-4 rounded-2xl hover:bg-gray-50 transition-colors md:mt-12 gsap-step">
                    <div class="shrink-0">
                        <div class="step-circle">4</div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Pemilihan Kepala Kantor</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Kepala Kantor memverifikasi dan memilih pemenang dari 3 Kandidat terbaik berdasarkan hasil voting komulatif, absensi, dan CKP.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex gap-6 step-item relative z-10 bg-white p-4 rounded-2xl hover:bg-gray-50 transition-colors md:col-span-2 md:w-1/2 md:mx-auto mt-0 md:-mt-8 gsap-step">
                    <div class="shrink-0">
                        <div class="step-circle">5</div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Pengumuman Pemenang</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">Pegawai terbaik diumumkan secara resmi melalui dashboard aplikasi dan berhak menerima penghargaan atas dedikasinya.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Section 3: Maklumat -->
    <section id="tentang" class="relative pt-24 pb-12 bg-bps-primary text-white overflow-hidden">
        <!-- Abstract Bg for Maklumat -->
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+CgkJPHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwYzExLjA0NiAwIDIwLTguOTU0IDIwLTIwUzMxLjA0NiAwIDIwIDAgMCA4Ljk1NCAwIDIwczguOTU0IDIwIDIwIDIwem0wIDE1YTE1IDE1IDAgMSAwIDAtMzAgMTUgMTUgMCAwIDAgMCAzMHoiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz4KCTwvc3ZnPg==')]"></div>
        
        <div class="max-w-4xl mx-auto px-6 lg:px-8 relative z-10 text-center mb-16 gsap-scale-in">
            <h2 class="text-3xl lg:text-4xl font-light mb-6">Maklumat Pelayanan</h2>
            <p class="text-blue-100 text-lg lg:text-xl font-light italic leading-relaxed">
                "Dengan ini kami menyatakan sanggup menyelenggarakan penilaian dan evaluasi kinerja sesuai dengan standar pelayanan yang telah ditetapkan, transparan, objektif, dan apabila kami tidak menepati janji, kami siap menerima sanksi sesuai dengan peraturan yang berlaku."
            </p>
        </div>

        <!-- Wavy Divider pointing up, seamlessly merging with the white section below -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden line-height-0">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="w-full h-12 md:h-20 block" style="margin-bottom: -1px;">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,119.5,192.73,101.43,237.52,88.42,279.79,72.48,321.39,56.44Z" fill="#ffffff"></path>
            </svg>
        </div>
    </section>

    <!-- Stats Section overlapping the wave -->
    <section class="bg-white relative z-20 pb-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 -mt-6 md:-mt-10 relative z-30">
            <div class="flex items-center gap-4 mb-8 justify-center">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">STATISTIK SIVOTA {{ date('Y') }}</span>
                <div class="h-px bg-bps-accent w-12"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Stat 1 -->
                <div class="stat-card p-8 text-center gsap-stat">
                    <div class="w-14 h-14 rounded-full bg-bps-accent text-white flex items-center justify-center mx-auto -mt-14 mb-4 shadow-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <div class="text-4xl font-bold text-bps-primary mb-2 counter" data-target="{{ $statPegawai }}">0</div>
                    <div class="text-sm text-gray-500">Pegawai Aktif</div>
                </div>

                <!-- Stat 2 -->
                <div class="stat-card p-8 text-center gsap-stat">
                    <div class="w-14 h-14 rounded-full bg-bps-accent text-white flex items-center justify-center mx-auto -mt-14 mb-4 shadow-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="text-4xl font-bold text-bps-primary mb-2 counter" data-target="{{ $statVoting }}">0</div>
                    <div class="text-sm text-gray-500">Total Suara Voting</div>
                </div>

                <!-- Stat 3 -->
                <div class="stat-card p-8 text-center gsap-stat">
                    <div class="w-14 h-14 rounded-full bg-bps-accent text-white flex items-center justify-center mx-auto -mt-14 mb-4 shadow-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div class="text-4xl font-bold text-bps-primary mb-2 counter" data-target="{{ $statPeriode }}">0</div>
                    <div class="text-sm text-gray-500">Total Periode SIVOTA</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Footer -->
    <footer class="bg-bps-dark text-white pt-16 pb-8 border-t-[16px] border-bps-primary relative z-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 mb-12">
                
                <!-- Info -->
                <div class="lg:col-span-7">
                    <h4 class="text-sm font-bold text-gray-400 tracking-wider mb-6">HUBUNGI KAMI</h4>
                    
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <svg class="w-6 h-6 text-bps-accent shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="text-gray-300 text-sm leading-relaxed">
                                Badan Pusat Statistik Kabupaten Bangkalan (BPS - Statistics of Bangkalan Regency) Jl. Halim Perdanakusuma No. 5 Bangkalan
                            </span>
                        </li>
                        <li class="flex items-center gap-4">
                            <svg class="w-6 h-6 text-bps-accent shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span class="text-gray-300 text-sm">bps3526@bps.go.id</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <svg class="w-6 h-6 text-bps-accent shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            <span class="text-gray-300 text-sm">(031) 3095622</span>
                        </li>
                    </ul>
                </div>

                <!-- Spacer for grid -->
                <div class="hidden lg:block lg:col-span-2"></div>

                <!-- Links -->
                <div class="lg:col-span-3 flex flex-col space-y-4">
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Unduh Formulir</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Standar Pelayanan</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Maklumat Pelayanan</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Pelayanan Statistik Terpadu</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Pengaduan</a>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2 opacity-80">
                    <div class="w-8 h-8 bg-white rounded flex items-center justify-center font-bold text-bps-primary text-xs">SVT</div>
                    <span class="text-sm font-semibold tracking-wider">SIVOTA</span>
                </div>
                <div class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Sistem Informasi Voting Terpadu BPS. Hak Cipta Dilindungi.
                </div>
            </div>
        </div>
    </footer>

    <!-- GSAP Animations Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Register ScrollTrigger
            gsap.registerPlugin(ScrollTrigger);

            // Initial states
            gsap.set(".gsap-hero", { y: 30, opacity: 0 });
            gsap.set(".gsap-hero-img", { x: 50, opacity: 0, rotateY: 15 });
            gsap.set(".gsap-hero-badge", { scale: 0.8, opacity: 0 });
            gsap.set(".gsap-fade-up", { y: 40, opacity: 0 });
            gsap.set(".gsap-step", { x: -30, opacity: 0 });
            gsap.set(".gsap-scale-in", { scale: 0.9, opacity: 0 });
            gsap.set(".gsap-stat", { y: 30, opacity: 0 });

            // Hero Timeline
            const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
            
            tl.to(".gsap-hero", {
                y: 0,
                opacity: 1,
                duration: 1,
                stagger: 0.15,
                delay: 0.2
            })
            .to(".gsap-hero-img", {
                x: 0,
                opacity: 1,
                rotateY: -10,
                duration: 1.5,
                ease: "expo.out"
            }, "-=0.8")
            .to(".gsap-hero-badge", {
                scale: 1,
                opacity: 1,
                duration: 0.8,
                ease: "back.out(1.7)"
            }, "-=0.5");

            // Scroll Parallax for Hero Background
            gsap.utils.toArray('.gsap-parallax').forEach(layer => {
                const speed = layer.getAttribute('data-speed');
                gsap.to(layer, {
                    y: () => (ScrollTrigger.maxScroll(window) * speed),
                    ease: "none",
                    scrollTrigger: {
                        trigger: "body",
                        start: "top top",
                        end: "bottom top",
                        scrub: 1
                    }
                });
            });

            // Fade Up Elements
            gsap.utils.toArray('.gsap-fade-up').forEach(element => {
                gsap.to(element, {
                    scrollTrigger: {
                        trigger: element,
                        start: "top 85%",
                        toggleActions: "play none none reverse"
                    },
                    y: 0,
                    opacity: 1,
                    duration: 0.8,
                    ease: "power2.out"
                });
            });

            // Scale In Elements
            gsap.utils.toArray('.gsap-scale-in').forEach(element => {
                gsap.to(element, {
                    scrollTrigger: {
                        trigger: element,
                        start: "top 80%",
                        toggleActions: "play none none reverse"
                    },
                    scale: 1,
                    opacity: 1,
                    duration: 1,
                    ease: "power3.out"
                });
            });

            // Steps stagger reveal (Fade Right/Left based on grid)
            ScrollTrigger.batch(".gsap-step", {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, 
                    x: 0, 
                    stagger: 0.15, 
                    overwrite: true, 
                    duration: 0.8,
                    ease: "power3.out"
                }),
                start: "top 85%"
            });

            // Stats stagger reveal + Counter animation
            ScrollTrigger.batch(".gsap-stat", {
                onEnter: batch => {
                    gsap.to(batch, {
                        opacity: 1, 
                        y: 0, 
                        stagger: 0.15, 
                        duration: 0.8,
                        ease: "back.out(1.2)",
                        onComplete: () => {
                            // Run counter animation after card reveals
                            batch.forEach(card => {
                                const counter = card.querySelector('.counter');
                                if(counter && !counter.classList.contains('counted')) {
                                    counter.classList.add('counted');
                                    const target = parseInt(counter.getAttribute('data-target'));
                                    gsap.to(counter, {
                                        innerHTML: target,
                                        duration: 2,
                                        snap: { innerHTML: 1 },
                                        ease: "power1.out",
                                        onUpdate: function() {
                                            counter.innerHTML = Math.round(this.targets()[0].innerHTML);
                                        }
                                    });
                                }
                            });
                        }
                    });
                },
                start: "top 90%"
            });

            // Mouse Cursor Glow Tracking (Only active on dark backgrounds)
            const cursorGlow = document.getElementById('cursor-glow');
            let mouseX = window.innerWidth / 2;
            let mouseY = window.innerHeight / 2;
            let glowX = mouseX;
            let glowY = mouseY;
            let isHoveringDark = false;

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                
                // Determine if over a dark section to show glow
                const target = e.target;
                const bgColors = window.getComputedStyle(target).backgroundColor;
                // Simple heuristic: if rgba(x,y,z) sum is low, it's dark
                const rgb = bgColors.match(/\d+/g);
                if (rgb && rgb.length >= 3) {
                    const brightness = (parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000;
                    isHoveringDark = brightness < 128;
                } else {
                    isHoveringDark = true; // fallback to true for hero
                }

                if (isHoveringDark) {
                    cursorGlow.style.opacity = 1;
                } else {
                    cursorGlow.style.opacity = 0;
                }
            });

            document.addEventListener('mouseleave', () => {
                cursorGlow.style.opacity = 0;
            });

            function animateGlow() {
                glowX += (mouseX - glowX) * 0.1;
                glowY += (mouseY - glowY) * 0.1;
                cursorGlow.style.transform = `translate(${glowX}px, ${glowY}px) translate(-50%, -50%)`;
                requestAnimationFrame(animateGlow);
            }
            animateGlow();

            // Navbar Scrolled State
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</body>
</html>

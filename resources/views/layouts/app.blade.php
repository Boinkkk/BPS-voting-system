<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body { font-family: 'Hanken Grotesk', sans-serif; }
        
        /* Smooth Page Transition */
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition {
            animation: pageFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Active Sidebar Indicator */
        .sidebar-link-active {
            background-color: color-mix(in srgb, var(--color-bps-secondary) 10%, transparent);
            color: var(--color-bps-secondary);
            position: relative;
            transition: all 0.3s ease;
        }
        .sidebar-link-active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: var(--color-bps-secondary);
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            box-shadow: 1px 0 6px color-mix(in srgb, var(--color-bps-secondary) 40%, transparent);
        }
        
        /* SVG icon inside active link uses parent color — no JS needed for icon */
        .sidebar-link-active > svg {
            color: var(--color-bps-secondary) !important;
        }

        /* Skeleton loader overlay */
        #page-skeleton {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 80px; /* Mobile: leave space for fixed bottom nav (h-20 = 80px) */
            z-index: 200;
            background-color: rgba(248, 250, 252, 0.95);
            backdrop-filter: blur(4px);
            padding: 1.5rem;
        }
        @media (min-width: 768px) {
            #page-skeleton {
                bottom: 0; /* Desktop: stretch to bottom */
            }
        }
        #page-skeleton.active {
            display: block;
            animation: fadeIn 0.15s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .skeleton-shimmer {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 0.5rem;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* SVG Magic Indicator logic handled in HTML/Tailwind classes */

        .nav-item .icon-wrapper {
            transform: translateY(4px);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), color 0.4s ease;
            color: rgba(255, 255, 255, 0.7);
        }
        .nav-item .text-label {
            opacity: 0;
            transform: translateY(12px);
            transition: all 0.4s ease;
        }
        .nav-item.active .icon-wrapper {
            transform: translateY(-40px);
            color: var(--color-bps-accent);
            z-index: 20;
        }
        .nav-item.active .text-label {
            opacity: 1;
            transform: translateY(-2px);
        }
        
        /* Sidebar Locked State Overrides */
        @media (min-width: 768px) {
            #sidebar.is-locked {
                width: 16rem !important; /* 64 * 0.25rem = 16rem */
            }
            #sidebar.is-locked .md\:opacity-0 {
                opacity: 1 !important;
            }
            #sidebar.is-locked .md\:w-0 {
                width: auto !important;
            }
            #sidebar.is-locked .md\:justify-center {
                justify-content: flex-start !important;
            }
            #sidebar.is-locked .md\:mx-auto {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            #sidebar.is-locked .md\:px-2 {
                padding-left: 0.75rem !important; /* px-3 */
                padding-right: 0.75rem !important;
            }
            #sidebar.is-locked .md\:mr-0 {
                margin-right: 0.75rem !important; /* mr-3 */
            }
            #sidebar.is-locked p span.md\:hidden {
                display: inline !important;
            }
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIVOTA - Sistem Voting & Penilaian BPS</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bps-bg antialiased text-bps-text h-screen flex flex-col md:flex-row overflow-hidden">


    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-bps-primary border-b border-bps-primary px-4 py-3 flex-shrink-0 z-30 relative shadow-sm">
        <div class="bg-white px-2.5 py-1.5 rounded-lg shadow-sm flex items-center gap-2">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo BPS" class="h-6 w-auto">
            <span class="font-extrabold text-slate-800 text-sm tracking-wide">SIVOTA</span>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Notification Icon -->
            <button onclick="toggleNotificationModal()" class="text-white hover:text-gray-100 focus:outline-none relative block">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                @if(isset($globalNotifications) && count($globalNotifications) > 0)
                <span class="absolute top-0 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                @endif
            </button>
            
            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="mobileProfileBtn" class="flex items-center focus:outline-none">
                    <img src="{{ Auth::user()->foto_profil_url ?? asset('images/default-avatar.png') }}" alt="Profile" class="h-8 w-8 rounded-full border border-gray-300 object-cover">
                </button>
                <!-- Dropdown Menu -->
                <div id="mobileProfileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" wire:navigate>Profil Saya</a>
                    <a href="{{ route('panduan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" wire:navigate>Buku Panduan</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside id="sidebar" wire:persist="sidebar" class="fixed md:relative z-50 md:z-20 transform -translate-x-full md:translate-x-0 w-64 md:w-[80px] group md:hover:w-64 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col h-full shadow-sm transition-all duration-300 ease-in-out overflow-x-hidden">
        <div class="h-16 flex items-center justify-between md:justify-start px-5 md:group-hover:px-5 border-b border-gray-100 transition-all duration-300 overflow-hidden relative">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo BPS" class="h-9 max-w-none w-auto flex-shrink-0 transition-all duration-300">
                <span class="font-extrabold text-slate-800 text-xl tracking-tight opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">SIVOTA</span>
            </div>
            
            <!-- Lock Button (Desktop only) -->
            <button id="sidebarLockBtn" class="hidden md:flex absolute right-4 text-gray-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none p-1.5 rounded-md hover:bg-blue-50 z-10" title="Kunci Sidebar">
                <svg id="lockIcon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <svg id="unlockIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </button>

            <!-- Close button for mobile -->
            <button id="closeMenuBtn" class="md:hidden text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" wire:navigate>
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('dashboard') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Dashboard</span>
            </a>

            <a href="{{ route('kalender') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('kalender') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" wire:navigate>
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kalender') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Kalender Pemilihan</span>
            </a>

            <a href="{{ route('glosarium.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('glosarium.*') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" wire:navigate>
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('glosarium.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Glosarium</span>
            </a>

            <a href="{{ route('faq.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('faq.*') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" wire:navigate>
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('faq.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">FAQ (Bantuan)</span>
            </a>

            <a href="{{ route('admin.kandidat.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kandidat.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.kandidat.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Kandidat Terbaik</span>
            </a>

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin')
            <div class="mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Administrator</span></p>
                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pegawai.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.pegawai.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Data Pegawai</span>
                </a>
                <a href="{{ route('admin.pengumuman.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pengumuman.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.pengumuman.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Pengumuman</span>
                </a>
                <a href="{{ route('admin.audit.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.audit.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.audit.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Audit Log</span>
                </a>
                <a href="{{ url('/telescope') }}" target="_blank" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-bps-bg hover:text-gray-900">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">System Monitor <svg class="inline w-3 h-3 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg></span>
                </a>
            </div>
            @endif

            @php
                $isAdmin = Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin';
                $isKepalaUmum = Auth::user() && Auth::user()->role && (Auth::user()->role->tipe == 'Kepala Umum' || Auth::user()->role->tipe == 'Kepala_Umum');
                $isTimPenilai = false;
                if (Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Pegawai') {
                    $isTimPenilai = \App\Models\TimPenilai::where('pegawai_id', Auth::user()->id)
                        ->whereHas('periode', function ($q) {
                            $q->where('status', '!=', 'selesai');
                        })->exists();
                }
            @endphp
            
            @if($isAdmin)
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Master Data</span></p>
                <a href="{{ route('admin.periode.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.periode.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.periode.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Periode</span>
                </a>
                <a href="{{ route('admin.pengaturan-bobot.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pengaturan-bobot.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.pengaturan-bobot.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Bobot</span>
                </a>
                <a href="{{ route('admin.glosarium.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.glosarium.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.glosarium.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Glosarium</span>
                </a>
                <a href="{{ route('admin.faq.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.faq.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.faq.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen FAQ</span>
                </a>
            </div>
            @endif
            
            @if($isAdmin || $isTimPenilai || $isKepalaUmum)
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">
                    {{ $isAdmin ? 'Admin Menu' : ($isKepalaUmum ? 'Kepala Umum Menu' : 'Tim Penilai Menu') }}
                </span></p>
                <a href="{{ route('admin.kinerja.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kinerja.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }} hidden" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.kinerja.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Penilaian Kinerja</span>
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.absensi.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.absensi.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Input Presensi</span>
                </a>
                <a href="{{ route('admin.ckp.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.ckp.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.ckp.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Input CKP</span>
                </a>
                @if($isAdmin || $isTimPenilai)
                <a href="{{ route('admin.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.survey.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.survey.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Survey</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.monitoring.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.monitoring.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Monitoring Survei</span>
                </a>
                @endif
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && in_array(Auth::user()->role->tipe, ['Admin', 'Pegawai', 'Kepala Umum', 'Kepala_Umum']))
            <div class="mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Menu Pegawai</span></p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('pegawai.survey.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Voting Kandidat Terbaik</span>
                </a>
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Kepala Kantor')
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Kepala Bagian</span></p>
                
                <a href="{{ route('kepala.tim_penilai.index') }}" class="flex items-center hidden justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.tim_penilai.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kepala.tim_penilai.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Tim Penilai & Surat Tugas</span>
                </a>
                <a href="{{ route('kepala.review.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.review.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kepala.review.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Review Nominasi</span>
                </a>
                
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Menu Pegawai</span></p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-bps-bg hover:text-gray-900' }}" wire:navigate>
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('pegawai.survey.*') ? 'text-bps-secondary' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Voting Kandidat</span>
                </a>
            </div>
            @endif


        </nav>
        
        <div class="p-4 border-t border-gray-200 flex flex-col space-y-4 items-start md:items-center md:group-hover:items-start transition-all duration-300">
            <a href="{{ route('profile') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start w-full transition-all duration-300" title="Profil" wire:navigate>
                <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden border border-gray-300 flex-shrink-0">
                    <img src="{{ Auth::user()->foto_profil_url }}" alt="User avatar" class="h-full w-full object-cover">
                </div>
                <div class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap overflow-hidden flex-1 w-auto md:w-0 md:group-hover:w-auto">
                    <p class="text-sm font-medium text-bps-text truncate">{{ Auth::user()->nama ?? 'User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->role->tipe ?? 'Role' }}</p>
                    <p class="text-[10px] text-gray-400 font-mono mt-0.5 truncate" id="sidebar-clock"></p>
                </div>
            </a>
            
            <a href="{{ route('panduan.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start w-full transition-all duration-300 text-blue-600 hover:text-blue-700 hover:bg-blue-50 p-2 -ml-2 mt-2 rounded-md" title="Buku Panduan" wire:navigate>
                <svg class="w-5 h-5 flex-shrink-0 mx-0 md:mx-auto md:group-hover:mx-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="ml-3 text-sm font-medium opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap overflow-hidden text-left w-auto md:w-0 md:group-hover:w-auto">
                    Buku Panduan
                </span>
            </a>
            
            <script>
                function updateClock() {
                    const now = new Date();
                    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                    document.getElementById('sidebar-clock').textContent = now.toLocaleDateString('id-ID', options);
                }
                setInterval(updateClock, 1000);
                updateClock();
            </script>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center justify-start md:justify-center md:group-hover:justify-start w-full transition-all duration-300 text-red-500 hover:text-red-600 hover:bg-red-50 p-2 -ml-2 rounded-md" title="Logout">
                    <svg class="w-5 h-5 flex-shrink-0 mx-0 md:mx-auto md:group-hover:mx-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="ml-3 text-sm font-medium opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap overflow-hidden text-left w-auto md:w-0 md:group-hover:w-auto">
                        Logout
                    </span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-bps-bg/50 relative z-10">
        <!-- Skeleton Loader: absolute inside flex-1, won't overlap sidebar or bottom nav -->
        <div id="page-skeleton" role="status" aria-label="Memuat halaman...">
            <div class="mx-auto" style="width: 100%; max-width: 64rem; padding-top: 1rem;">
                <div class="skeleton-shimmer h-8 w-1/3 mb-6"></div>
                <div class="skeleton-shimmer h-32 w-full mb-4"></div>
                <div class="skeleton-shimmer h-6 w-full mb-2"></div>
                <div class="skeleton-shimmer h-6 w-5/6 mb-2"></div>
                <div class="skeleton-shimmer h-6 w-4/6 mb-6"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="skeleton-shimmer h-24 w-full"></div>
                    <div class="skeleton-shimmer h-24 w-full"></div>
                </div>
            </div>
        </div>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 relative">
            <div class="max-w-5xl mx-auto page-transition pb-36 md:pb-0">
                @if(isset($globalSticky) && count($globalSticky) > 0)
                <div class="space-y-4 mb-6 sticky top-0 z-40 bg-bps-bg/90 backdrop-blur-sm py-2">
                    @foreach($globalSticky as $sticky)
                    @php
                        if ($sticky->prioritas == 'Critical') {
                            $bgClass = 'bg-gradient-to-r from-red-600 to-red-700';
                            $textClass = 'text-white';
                            $textMuted = 'text-red-100';
                            $badgeClass = 'bg-red-800 text-red-100 border border-red-500';
                            $iconColor = 'text-red-400';
                        } else {
                            $bgClass = 'bg-gradient-to-r from-orange-500 to-orange-600';
                            $textClass = 'text-white';
                            $textMuted = 'text-orange-50';
                            $badgeClass = 'bg-orange-700 text-orange-50 border border-orange-400';
                            $iconColor = 'text-orange-300';
                        }
                    @endphp
                    <div class="{{ $bgClass }} rounded-xl shadow-lg p-5 relative overflow-hidden group border border-white/10">
                        <div class="absolute right-0 top-0 {{ $iconColor }} opacity-20 group-hover:opacity-30 transition-opacity transform group-hover:scale-110">
                            <svg class="w-32 h-32 -mt-8 -mr-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="flex items-start relative z-10">
                            <div class="flex-shrink-0 mt-0.5 bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                <span class="text-2xl drop-shadow-md">{{ $sticky->prioritas == 'Critical' ? '🚨' : '⚠️' }}</span>
                            </div>
                            <div class="ml-4 w-full">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 gap-2">
                                    <h3 class="text-base font-bold {{ $textClass }} drop-shadow-sm">{{ $sticky->judul }}</h3>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md {{ $badgeClass }} shadow-sm whitespace-nowrap">{{ $sticky->prioritas == 'Critical' ? 'CRITICAL ALERT' : 'HIGH PRIORITY' }}</span>
                                </div>
                                <div class="text-sm {{ $textMuted }} whitespace-pre-wrap leading-relaxed">{{ $sticky->konten }}</div>
                                
                                @if(is_array($sticky->lampiran) && count($sticky->lampiran) > 0)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($sticky->lampiran as $lampiran)
                                            <a href="{{ Storage::url($lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs bg-black/20 hover:bg-black/30 text-white px-3 py-1.5 rounded-lg transition-colors backdrop-blur-sm border border-white/10">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                {{ basename($lampiran) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Desktop Floating Notification Icon -->
    <button onclick="toggleNotificationModal()" class="hidden md:flex fixed bottom-8 right-8 z-50 bg-bps-secondary hover:bg-blue-700 text-white p-4 rounded-full shadow-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all hover:scale-110 items-center justify-center group border-2 border-white">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        @if(isset($globalNotifications) && count($globalNotifications) > 0)
        <span class="absolute top-0 right-0 block h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
        @endif
        <span class="absolute right-full mr-4 bg-gray-900 text-white text-xs px-2 py-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none shadow-lg font-medium">Lihat Pengumuman</span>
    </button>

    <!-- Mobile Bottom Navigation (Only visible on mobile) -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-bps-primary z-40 shadow-[0_-4px_20px_-5px_rgba(15,76,129,0.3)] rounded-t-3xl pb-safe">
        <div class="relative flex justify-center items-center h-20 w-full" id="bottomNav">
            @php
                $tipe = Auth::user()->role->tipe ?? 'Pegawai';
                $activeIndex = -1; // Default to -1 (none active in main nav)

                if ($tipe == 'Pegawai') {
                    if(request()->routeIs('dashboard')) $activeIndex = 0;
                    elseif(request()->routeIs('admin.kandidat.*')) $activeIndex = 1;
                    elseif(request()->routeIs('pegawai.survey.*')) $activeIndex = 2;
                    elseif(request()->routeIs('glosarium.*')) $activeIndex = 3;
                } elseif ($tipe == 'Admin') {
                    if(request()->routeIs('admin.periode.*')) $activeIndex = 0;
                    elseif(request()->routeIs('admin.pegawai.*')) $activeIndex = 1;
                    elseif(request()->routeIs('dashboard')) $activeIndex = 2;
                    elseif(request()->routeIs('admin.monitoring.*')) $activeIndex = 3;
                } elseif ($tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum') {
                    if(request()->routeIs('admin.ckp.*')) $activeIndex = 0;
                    elseif(request()->routeIs('admin.absensi.*')) $activeIndex = 1;
                    elseif(request()->routeIs('pegawai.survey.*')) $activeIndex = 2;
                    elseif(request()->routeIs('glosarium.*')) $activeIndex = 3;
                } elseif ($tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian') {
                    if(request()->routeIs('dashboard')) $activeIndex = 0;
                    elseif(request()->routeIs('admin.kandidat.*')) $activeIndex = 1;
                    elseif(request()->routeIs('kepala.review.*')) $activeIndex = 2;
                    elseif(request()->routeIs('glosarium.*')) $activeIndex = 3;
                }
            @endphp

            <!-- The Magic Indicator (SVG Based) -->
            <div id="magicIndicator" class="absolute top-0 left-0 w-1/5 h-full flex justify-center transition-transform duration-500 ease-[cubic-bezier(0.68,-0.55,0.265,1.55)] z-10 pointer-events-none" style="transform: translateX({{ max(0, $activeIndex) * 100 }}%); {{ $activeIndex === -1 ? 'opacity: 0;' : '' }}">
                <!-- SVG Cutout Mask -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-[120px] h-[30px] text-[#f9fafb]">
                    <svg viewBox="0 0 120 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M 0 0 L 15 0 C 30 0 40 30 60 30 C 80 30 90 0 105 0 L 120 0 Z" fill="currentColor"/>
                    </svg>
                </div>
                <!-- Magic Circle -->
                <div class="absolute top-[-26px] left-1/2 transform -translate-x-1/2 w-[52px] h-[52px] bg-white rounded-full shadow-lg shadow-black/10 z-[11]"></div>
            </div>

            @if($tipe == 'Pegawai')
                <a href="{{ route('dashboard') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Dashboard</span>
                </a>
                <a href="{{ route('admin.kandidat.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Kandidat</span>
                </a>
                <a href="{{ route('pegawai.survey.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Voting</span>
                </a>
                <a href="{{ route('glosarium.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Glosarium</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Lainnya</span>
                </button>

            @elseif($tipe == 'Admin')
                <a href="{{ route('admin.periode.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Periode</span>
                </a>
                <a href="{{ route('admin.pegawai.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Pegawai</span>
                </a>
                <a href="{{ route('dashboard') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Dashboard</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Monitoring</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Lainnya</span>
                </button>

            @elseif($tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum')
                <a href="{{ route('admin.ckp.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Input CKP</span>
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Input Absen</span>
                </a>
                <a href="{{ route('pegawai.survey.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Voting</span>
                </a>
                <a href="{{ route('glosarium.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Glosarium</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Lainnya</span>
                </button>

            @elseif($tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                <a href="{{ route('dashboard') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Dashboard</span>
                </a>
                <a href="{{ route('admin.kandidat.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Kandidat</span>
                </a>
                <a href="{{ route('kepala.review.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Review</span>
                </a>
                <a href="{{ route('glosarium.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3" wire:navigate>
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Glosarium</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-white">Lainnya</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Mobile More Menu Overlay -->
    <div id="mobileMoreOverlay" class="fixed inset-0 bg-gray-900/40 z-[60] hidden transition-opacity duration-300 opacity-0 md:hidden"></div>
    <div id="mobileMoreSheet" class="fixed bottom-0 left-0 w-full bg-white rounded-t-3xl shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.1)] z-[70] transform translate-y-full transition-transform duration-300 md:hidden">
        <div class="p-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4"></div>
            <h3 class="text-center font-bold text-gray-800 mb-4">Menu Lainnya</h3>
            
            <div class="grid grid-cols-4 gap-4 pb-4">
                <!-- 1. Kalender (Semua Role) -->
                <a href="{{ route('kalender') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                    <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-[10px] text-center leading-tight">Kalender</span>
                </a>

                <!-- 2. FAQ Bantuan (Semua Role) -->
                <a href="{{ route('faq.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                    <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] text-center leading-tight">FAQ</span>
                </a>
                
                <!-- 3. Dashboard (Khusus Kepala Umum, karena yang lain sudah ada di navbar bawah utama) -->
                @if($tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum')
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                    <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-[10px] text-center leading-tight">Dashboard</span>
                </a>
                @endif

                <!-- 4. Kandidat Terbaik (Admin & Kepala Umum) -->
                @if($tipe == 'Admin' || $tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum')
                <a href="{{ route('admin.kandidat.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                    <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                    <span class="text-[10px] text-center leading-tight">Kandidat Terbaik</span>
                </a>
                @endif

                <!-- 5. Glosarium (Admin) -->
                @if($tipe == 'Admin')
                <a href="{{ route('glosarium.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                    <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-[10px] text-center leading-tight">Glosarium</span>
                </a>
                @endif

                <!-- 6. Voting (Admin, Kepala Kantor, Kepala Bagian) -->
                @if($tipe == 'Admin' || $tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                <a href="{{ route('pegawai.survey.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                    <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    </div>
                    <span class="text-[10px] text-center leading-tight">Voting</span>
                </a>
                @endif

                <!-- ADMIN EXCLUSIVE -->
                @if($tipe == 'Admin')
                    <a href="{{ route('admin.pengumuman.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Pengumuman</span>
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Audit Log</span>
                    </a>
                    <a href="{{ url('/telescope') }}" target="_blank" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary">
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Monitor</span>
                    </a>
                    <a href="{{ route('admin.pengaturan-bobot.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Manajemen Bobot</span>
                    </a>
                    <a href="{{ route('admin.glosarium.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kelola Glosarium</span>
                    </a>
                    <a href="{{ route('admin.faq.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kelola FAQ</span>
                    </a>
                    <a href="{{ route('admin.absensi.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Presensi</span>
                    </a>
                    <a href="{{ route('admin.ckp.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Input CKP</span>
                    </a>
                    <a href="{{ route('admin.survey.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Survey</span>
                    </a>
                @endif

                <!-- Kinerja (Admin, Kepala Umum) -->
                @if($tipe == 'Admin' || $tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum')
                    <a href="{{ route('admin.kinerja.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kinerja</span>
                    </a>
                @endif
                
                <!-- Tim Penilai (Kepala Kantor / Bagian) -->
                @if($tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                    <a href="{{ route('kepala.tim_penilai.index') }}" class="flex flex-col items-center text-gray-600 hover:text-bps-secondary" wire:navigate>
                        <div class="w-12 h-12 rounded-2xl bg-bps-bg flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Tim Penilai</span>
                    </a>
                @endif
            </div>
            
        </div>
    </div>
    
    <!-- Notification Modal -->
    <div id="notificationModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-[100] hidden flex justify-center md:justify-end items-start pt-16 md:pt-4 md:pr-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white w-full max-w-sm rounded-xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0 m-4 md:m-0" id="notificationContent">
            <div class="bg-bps-primary p-4 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg">Notifikasi & Pengumuman</h3>
                <button onclick="toggleNotificationModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-0">
                @if(isset($globalNotifications) && count($globalNotifications) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($globalNotifications as $notif)
                            @php
                                $color = 'blue';
                                $bg = 'bg-white';
                                if($notif->prioritas == 'Critical') { $color = 'red'; $bg = 'bg-red-50'; }
                                elseif($notif->prioritas == 'High') { $color = 'orange'; }
                                elseif($notif->kategori == 'Hasil') { $color = 'green'; }
                            @endphp
                            <a href="{{ route('dashboard') }}" class="block p-4 hover:bg-gray-50 transition-colors {{ $bg }}" wire:navigate>
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-xs font-bold uppercase tracking-wider text-{{ $color }}-600">{{ $notif->kategori }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $notif->publish_at ? $notif->publish_at->diffForHumans() : $notif->created_at->diffForHumans() }}</span>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-1 leading-tight">{{ $notif->judul }}</h4>
                                <p class="text-xs text-gray-500 line-clamp-2">{{ Str::limit($notif->konten, 80) }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <p class="text-sm text-gray-500 font-medium">Belum ada notifikasi baru</p>
                    </div>
                @endif
            </div>
            <div class="border-t border-gray-100 p-3 bg-gray-50 text-center">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-bps-secondary hover:text-bps-primary uppercase tracking-wider" wire:navigate>Lihat Semua di Dashboard</a>
            </div>
        </div>
    </div>
    
<script>
    // Notification Modal Logic
    function toggleNotificationModal() {
        const modal = document.getElementById('notificationModal');
        const content = document.getElementById('notificationContent');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('notificationModal');
        const content = document.getElementById('notificationContent');
        if (!modal.classList.contains('hidden')) {
            // If click is on the dark background (not inside content)
            if (event.target === modal) {
                toggleNotificationModal();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Profile Dropdown Logic
        const profileBtn = document.getElementById('mobileProfileBtn');
        const profileMenu = document.getElementById('mobileProfileMenu');
        
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function(e) {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
        }

        // Mobile More Menu Sheet Logic
        const moreBtns = document.querySelectorAll('.mobile-more-btn');
        const moreOverlay = document.getElementById('mobileMoreOverlay');
        const moreSheet = document.getElementById('mobileMoreSheet');

        function toggleMoreMenu() {
            if (moreSheet.classList.contains('translate-y-full')) {
                moreSheet.classList.remove('translate-y-full');
                moreOverlay.classList.remove('hidden');
                setTimeout(() => moreOverlay.classList.remove('opacity-0'), 10);
            } else {
                moreSheet.classList.add('translate-y-full');
                moreOverlay.classList.add('opacity-0');
                setTimeout(() => moreOverlay.classList.add('hidden'), 300);
            }
        }

        moreBtns.forEach(btn => btn.addEventListener('click', toggleMoreMenu));
        if (moreOverlay) moreOverlay.addEventListener('click', toggleMoreMenu);

        // Magic Indicator Animation
        const navItems = document.querySelectorAll('.nav-item');
        const magicIndicator = document.getElementById('magicIndicator');

        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Jika tombol Lainnya ditekan, biarkan event listener satunya yang menangani toggleMenu
                if (this.classList.contains('mobile-more-btn')) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault(); // Pause navigation
                const index = this.getAttribute('data-index');
                const href = this.getAttribute('href');
                
                // Animate
                if(magicIndicator) {
                    magicIndicator.style.opacity = '1';
                    magicIndicator.style.transform = `translateX(${index * 100}%)`;
                }
                updateActiveNav(index);

                // Wait for animation to finish before navigating
                setTimeout(() => {
                    window.location.href = href;
                }, 400);
            });
        });

        function updateActiveNav(activeIndex) {
            navItems.forEach(nav => {
                if(nav.getAttribute('data-index') == activeIndex) {
                    nav.classList.add('active');
                } else {
                    nav.classList.remove('active');
                }
            });
        }

        // Sidebar Desktop logic (kept from before, if any buttons trigger it on desktop)
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const sidebar = document.getElementById('sidebar');

        function toggleDesktopMenu() {
            if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
            } else if (sidebar) {
                sidebar.classList.add('-translate-x-full');
            }
        }

        if (closeMenuBtn) closeMenuBtn.addEventListener('click', toggleDesktopMenu);
    });
</script>

    @if(!app()->environment('production') && Auth::check() && Auth::user()->role && Auth::user()->role->tipe === 'Admin')
    <div id="devTimeWidget" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-2xl border border-blue-200 p-4 z-[9999] w-72">
        <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-2">
            <h4 class="text-sm font-bold text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Dev Time Control
            </h4>
            <button onclick="document.getElementById('devTimeWidget').style.display='none'" class="text-gray-400 hover:text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="{{ route('dev.time.set') }}" method="POST" class="mb-2">
            @csrf
            <div class="mb-2">
                <label class="block text-[11px] text-gray-500 mb-1 font-semibold uppercase">Simulate Time</label>
                @php
                    $currentTestTime = \Illuminate\Support\Facades\Cache::get('global_test_time') ?? session('test_time');
                @endphp
                <input type="datetime-local" name="test_time" value="{{ $currentTestTime ? \Carbon\Carbon::parse($currentTestTime)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" class="w-full text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white text-xs font-bold py-2 rounded hover:bg-blue-700 transition-colors">Set Fake Time</button>
        </form>
        
        @if($currentTestTime)
        <form action="{{ route('dev.time.reset') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-gray-100 text-gray-700 text-xs font-bold py-2 rounded hover:bg-gray-200 transition-colors border border-gray-200">Reset to Actual</button>
        </form>
        @endif
        
        <div class="mt-3 text-[10px] text-gray-500 text-center font-mono bg-gray-50 py-1 rounded border border-gray-100">
            {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
    @endif
    
    <!-- Idle Timeout Script (60 minutes) -->
    <script>
        let idleTimer;
        const idleTime = 60 * 60 * 1000; // 60 minutes

        function resetTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(() => {
                window.location.href = "{{ route('login') }}?timeout=1";
            }, idleTime);
        }

        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onmousedown = resetTimer; 
        window.onclick = resetTimer;
        window.onscroll = resetTimer;
        window.onkeypress = resetTimer;
    </script>

    @stack('modals')
    @stack('scripts')
</body>
</html>

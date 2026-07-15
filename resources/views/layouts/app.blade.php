<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            background-color: rgb(0 145 218 / 0.1);
            color: #0091DA;
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
            background-color: #0091DA;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            box-shadow: 1px 0 6px rgba(0, 145, 218, 0.4);
        }
        /* Magic Indicator Navigation */
        .magic-cutout {
            position: absolute;
            top: -24px;
            width: 64px;
            height: 64px;
            background-color: #f9fafb;
            border-radius: 50%;
        }
        .magic-cutout::before, .magic-cutout::after {
            content: '';
            position: absolute;
            top: 24px;
            width: 24px;
            height: 24px;
            background-color: transparent;
        }
        .magic-cutout::before {
            left: -25px;
            border-top-right-radius: 24px;
            box-shadow: 0 -12px 0 0 #f9fafb;
        }
        .magic-cutout::after {
            right: -25px;
            border-top-left-radius: 24px;
            box-shadow: 0 -12px 0 0 #f9fafb;
        }
        .magic-circle {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 52px;
            height: 52px;
            background-color: #0091DA;
            border-radius: 50%;
            z-index: 11;
        }

        .nav-item .icon-wrapper {
            transform: translateY(4px);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), color 0.4s ease;
            color: #9ca3af;
        }
        .nav-item .text-label {
            opacity: 0;
            transform: translateY(12px);
            transition: all 0.4s ease;
        }
        .nav-item.active .icon-wrapper {
            transform: translateY(-30px);
            color: #ffffff;
            z-index: 20;
        }
        .nav-item.active .text-label {
            opacity: 1;
            transform: translateY(-2px);
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPS Selection System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 antialiased text-[#1D1D1B] h-screen flex flex-col md:flex-row overflow-hidden">


    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-slate-50 border-b border-gray-200 px-4 py-3 flex-shrink-0 z-30 relative shadow-sm">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo BPS" class="h-8 w-auto">
        <div class="flex items-center space-x-4">
            <!-- Notification Icon -->
            <button class="text-gray-500 hover:text-[#0091DA] focus:outline-none relative">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                <span class="absolute top-0 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>
            
            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="mobileProfileBtn" class="flex items-center focus:outline-none">
                    <img src="{{ Auth::user()->foto_profil_url ?? asset('images/default-avatar.png') }}" alt="Profile" class="h-8 w-8 rounded-full border border-gray-300 object-cover">
                </button>
                <!-- Dropdown Menu -->
                <div id="mobileProfileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed md:relative z-50 md:z-20 transform -translate-x-full md:translate-x-0 w-64 md:w-[80px] group md:hover:w-64 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col h-full shadow-sm transition-all duration-300 ease-in-out overflow-x-hidden">
        <div class="h-16 flex items-center justify-between md:justify-start px-5 md:group-hover:px-6 border-b border-gray-100 transition-all duration-300 overflow-hidden">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo BPS" class="h-9 max-w-none w-auto flex-shrink-0 transition-all duration-300">
            <!-- Close button for mobile -->
            <button id="closeMenuBtn" class="md:hidden text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('dashboard') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Dashboard</span>
            </a>

            <a href="{{ route('kalender') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('kalender') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kalender') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Kalender Pemilihan</span>
            </a>

            <a href="{{ route('glosarium.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('glosarium.*') ? 'sidebar-link-active' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('glosarium.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Glosarium</span>
            </a>

            <a href="{{ route('admin.kandidat.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kandidat.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.kandidat.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Kandidat Terbaik</span>
            </a>

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin')
            <div class="mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Administrator</span></p>
                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pegawai.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.pegawai.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Data Pegawai</span>
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
                <a href="{{ route('admin.periode.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.periode.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.periode.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Periode</span>
                </a>
                <a href="{{ route('admin.pengaturan-bobot.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pengaturan-bobot.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.pengaturan-bobot.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Bobot</span>
                </a>
                <a href="{{ route('admin.glosarium.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.glosarium.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.glosarium.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Glosarium</span>
                </a>
            </div>
            @endif
            
            @if($isAdmin || $isTimPenilai || $isKepalaUmum)
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">
                    {{ $isAdmin ? 'Admin Menu' : ($isKepalaUmum ? 'Kepala Umum Menu' : 'Tim Penilai Menu') }}
                </span></p>
                <a href="{{ route('admin.kinerja.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kinerja.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} hidden">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.kinerja.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Penilaian Kinerja</span>
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.absensi.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.absensi.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Input Presensi</span>
                </a>
                <a href="{{ route('admin.ckp.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.ckp.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.ckp.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Input CKP</span>
                </a>
                @if($isAdmin || $isTimPenilai)
                <a href="{{ route('admin.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.survey.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.survey.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Survey</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.monitoring.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.monitoring.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Monitoring Survei</span>
                </a>
                @endif
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && in_array(Auth::user()->role->tipe, ['Pegawai', 'Kepala Umum', 'Kepala_Umum']))
            <div class="mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Menu Pegawai</span></p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('pegawai.survey.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Voting Kandidat Terbaik</span>
                </a>
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Kepala Kantor')
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Kepala Bagian</span></p>
                
                <a href="{{ route('kepala.tim_penilai.index') }}" class="flex items-center hidden justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.tim_penilai.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kepala.tim_penilai.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Tim Penilai & Surat Tugas</span>
                </a>
                <a href="{{ route('kepala.review.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.review.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kepala.review.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Review Nominasi</span>
                </a>
                
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Menu Pegawai</span></p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('pegawai.survey.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Voting Kandidat</span>
                </a>
            </div>
            @endif


        </nav>
        
        <div class="p-4 border-t border-gray-200 flex flex-col space-y-4 items-start md:items-center md:group-hover:items-start transition-all duration-300">
            <a href="{{ route('profile') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start w-full transition-all duration-300" title="Profil">
                <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden border border-gray-300 flex-shrink-0">
                    <img src="{{ Auth::user()->foto_profil_url }}" alt="User avatar" class="h-full w-full object-cover">
                </div>
                <div class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap overflow-hidden flex-1 w-auto md:w-0 md:group-hover:w-auto">
                    <p class="text-sm font-medium text-[#1D1D1B] truncate">{{ Auth::user()->nama ?? 'User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->role->tipe ?? 'Role' }}</p>
                </div>
            </a>
            
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
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50/50">
        

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto p-8 relative">
            <div class="max-w-5xl mx-auto page-transition">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Bottom Navigation (Only visible on mobile) -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-[#EB891B] z-40 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)] rounded-t-3xl pb-safe">
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

            <!-- The Magic Indicator -->
            <div id="magicIndicator" class="absolute top-0 left-0 w-1/5 h-full flex justify-center transition-transform duration-500 ease-[cubic-bezier(0.68,-0.55,0.265,1.55)] z-10 pointer-events-none" style="transform: translateX({{ max(0, $activeIndex) * 100 }}%); {{ $activeIndex === -1 ? 'opacity: 0;' : '' }}">
                <div class="magic-cutout">
                    <div class="magic-circle shadow-lg shadow-[#0091DA]/40"></div>
                </div>
            </div>

            @if($tipe == 'Pegawai')
                <a href="{{ route('dashboard') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Dashboard</span>
                </a>
                <a href="{{ route('admin.kandidat.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Kandidat</span>
                </a>
                <a href="{{ route('pegawai.survey.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Voting</span>
                </a>
                <a href="{{ route('glosarium.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Glosarium</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Lainnya</span>
                </button>

            @elseif($tipe == 'Admin')
                <a href="{{ route('admin.periode.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Periode</span>
                </a>
                <a href="{{ route('admin.pegawai.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Pegawai</span>
                </a>
                <a href="{{ route('dashboard') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Dashboard</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Monitoring</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Lainnya</span>
                </button>

            @elseif($tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum')
                <a href="{{ route('admin.ckp.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Input CKP</span>
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Input Absen</span>
                </a>
                <a href="{{ route('pegawai.survey.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Voting</span>
                </a>
                <a href="{{ route('glosarium.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Glosarium</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Lainnya</span>
                </button>

            @elseif($tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                <a href="{{ route('dashboard') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 0 ? 'active' : '' }}" data-index="0">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Dashboard</span>
                </a>
                <a href="{{ route('admin.kandidat.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 1 ? 'active' : '' }}" data-index="1">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Kandidat</span>
                </a>
                <a href="{{ route('kepala.review.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 2 ? 'active' : '' }}" data-index="2">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Review</span>
                </a>
                <a href="{{ route('glosarium.index') }}" class="nav-item w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 3 ? 'active' : '' }}" data-index="3">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Glosarium</span>
                </a>
                <button type="button" class="nav-item mobile-more-btn w-1/5 h-full flex flex-col items-center justify-center relative z-20 {{ $activeIndex == 4 ? 'active' : '' }}" data-index="4">
                    <div class="icon-wrapper transition-all duration-500 ease-in-out">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </div>
                    <span class="text-label absolute bottom-3 text-[10px] font-semibold text-[#0091DA]">Lainnya</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Mobile More Menu Overlay -->
    <div id="mobileMoreOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-[60] hidden transition-opacity duration-300 opacity-0 md:hidden"></div>
    <div id="mobileMoreSheet" class="fixed bottom-0 left-0 w-full bg-white rounded-t-3xl shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.1)] z-[70] transform translate-y-full transition-transform duration-300 md:hidden">
        <div class="p-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4"></div>
            <h3 class="text-center font-bold text-gray-800 mb-4">Menu Lainnya</h3>
            
            <div class="grid grid-cols-4 gap-4 pb-4">
                @if($tipe == 'Pegawai' || $tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum' || $tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                    <a href="{{ route('kalender') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kalender</span>
                    </a>
                @endif
                
                @if($tipe == 'Admin')
                    <a href="{{ route('admin.pengaturan-bobot.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Manajemen Bobot</span>
                    </a>
                    <a href="{{ route('admin.glosarium.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Glosarium</span>
                    </a>
                    <a href="{{ route('admin.kandidat.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kandidat Terbaik</span>
                    </a>
                    <a href="{{ route('admin.pengumuman.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Pengumuman</span>
                    </a>
                    <a href="{{ route('kalender') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kalender</span>
                    </a>
                    <a href="{{ route('admin.survey.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Survey</span>
                    </a>
                @endif
                
                @if($tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum' || $tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                    <a href="{{ route('admin.kinerja.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Kinerja</span>
                    </a>
                @endif

                @if($tipe == 'Kepala Umum' || $tipe == 'Kepala_Umum')
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Dashboard</span>
                    </a>
                @endif

                @if($tipe == 'Kepala Kantor' || $tipe == 'Kepala Bagian')
                    <a href="{{ route('kepala.tim_penilai.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Tim Penilai</span>
                    </a>
                    <a href="{{ route('pegawai.survey.index') }}" class="flex flex-col items-center text-gray-600 hover:text-[#0091DA]">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-1 border border-gray-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <span class="text-[10px] text-center leading-tight">Voting</span>
                    </a>
                @endif
            </div>
            
        </div>
    </div>
    
<script>
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

    @stack('scripts')
</body>
</html>

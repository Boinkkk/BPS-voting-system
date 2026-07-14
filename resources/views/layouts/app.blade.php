<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Hanken Grotesk', sans-serif; }</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPS Selection System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 antialiased text-[#1D1D1B] h-screen flex flex-col md:flex-row overflow-hidden">


    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3 flex-shrink-0 z-30 relative shadow-sm">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo BPS" class="h-8 w-auto">
        <button id="mobileMenuBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2 -mr-2">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

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
            <a href="{{ route('dashboard') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('dashboard') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Dashboard</span>
            </a>

            <a href="{{ route('admin.kandidat.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kandidat.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.kandidat.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Kandidat Terbaik</span>
            </a>

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin')
            <div class="mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Administrator</span></p>
                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pegawai.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
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
                <a href="{{ route('admin.periode.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.periode.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.periode.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Periode</span>
                </a>
                <a href="{{ route('admin.pengaturan-bobot.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pengaturan-bobot.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.pengaturan-bobot.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Bobot</span>
                </a>
            </div>
            @endif
            
            @if($isAdmin || $isTimPenilai || $isKepalaUmum)
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">
                    {{ $isAdmin ? 'Admin Menu' : ($isKepalaUmum ? 'Kepala Umum Menu' : 'Tim Penilai Menu') }}
                </span></p>
                <a href="{{ route('admin.kinerja.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kinerja.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} hidden">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.kinerja.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Penilaian Kinerja</span>
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.absensi.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.absensi.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Input Presensi</span>
                </a>
                <a href="{{ route('admin.ckp.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.ckp.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.ckp.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Input CKP</span>
                </a>
                @if($isAdmin || $isTimPenilai)
                <a href="{{ route('admin.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.survey.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.survey.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Manajemen Survey</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.monitoring.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('admin.monitoring.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Monitoring Survei</span>
                </a>
                @endif
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && in_array(Auth::user()->role->tipe, ['Pegawai', 'Kepala Umum', 'Kepala_Umum']))
            <div class="mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Menu Pegawai</span></p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('pegawai.survey.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Voting Kandidat Terbaik</span>
                </a>
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Kepala Kantor')
            <div class="mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Kepala Bagian</span></p>
                
                <a href="{{ route('kepala.tim_penilai.index') }}" class="flex items-center hidden justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.tim_penilai.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kepala.tim_penilai.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Tim Penilai & Surat Tugas</span>
                </a>
                <a href="{{ route('kepala.review.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.review.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 md:mr-0 md:group-hover:mr-3 transition-all duration-300 mx-0 md:mx-auto md:group-hover:mx-0 {{ request()->routeIs('kepala.review.*') ? 'text-[#0091DA]' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    <span class="opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-1 overflow-hidden">Review Nominasi</span>
                </a>
                
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-3"><span class="inline md:hidden md:group-hover:inline">Menu Pegawai</span></p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center justify-start md:justify-center md:group-hover:justify-start px-3 md:px-2 md:group-hover:px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'bg-[#0091DA]/10 text-[#0091DA]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
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
            <div class="max-w-5xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
    
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleMenu() {
            if (sidebar.classList.contains('-translate-x-full')) {
                // Open menu
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => sidebarOverlay.classList.remove('opacity-0'), 10);
            } else {
                // Close menu
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('opacity-0');
                setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
            }
        }

        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleMenu);
        if (closeMenuBtn) closeMenuBtn.addEventListener('click', toggleMenu);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleMenu);
    });
</script>

    @stack('scripts')
</body>
</html>

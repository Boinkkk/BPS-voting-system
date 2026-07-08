<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPS Selection System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-slate-800 h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col h-full z-20 shadow-sm relative">
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <span class="text-xl font-bold text-sky-700">BPS Selection System</span>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-sky-700' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.kandidat.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kandidat.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="mr-3 text-lg {{ request()->routeIs('admin.kandidat.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">🏆</span>
                Kandidat Terbaik
            </a>

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin')
            <div class="px-3 mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Administrator</p>
                <a href="{{ route('admin.pegawai.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.pegawai.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.pegawai.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">👥</span>
                    Data Pegawai
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
            <div class="px-3 mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Master Data</p>
                <a href="{{ route('admin.periode.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.periode.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.periode.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">⏳</span>
                    Manajemen Periode
                </a>
            </div>
            @endif
            
            @if($isAdmin || $isTimPenilai || $isKepalaUmum)
            <div class="px-3 mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">
                    {{ $isAdmin ? 'Admin Menu' : ($isKepalaUmum ? 'Kepala Umum Menu' : 'Tim Penilai Menu') }}
                </p>
                <a href="{{ route('admin.kinerja.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.kinerja.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.kinerja.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">📊</span>
                    Penilaian Kinerja
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.absensi.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.absensi.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">📅</span>
                    Absensi Pegawai
                </a>
                <a href="{{ route('admin.ckp.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.ckp.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.ckp.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">🎯</span>
                    Nilai CKP
                </a>
                @if($isAdmin || $isTimPenilai)
                <a href="{{ route('admin.survey.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.survey.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.survey.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">📝</span>
                    Manajemen Survey
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.monitoring.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('admin.monitoring.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">📈</span>
                    Monitoring Survei
                </a>
                @endif
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Pegawai')
            <div class="px-3 mb-2 mt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Menu Pegawai</p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('pegawai.survey.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">🗳️</span>
                    Voting Kandidat Terbaik
                </a>
            </div>
            @endif

            @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Kepala Kantor')
            <div class="px-3 mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Kepala Bagian</p>
                
                <a href="{{ route('kepala.tim_penilai.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.tim_penilai.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('kepala.tim_penilai.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">👥</span>
                    Tim Penilai & Surat Tugas
                </a>
                <a href="{{ route('kepala.review.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('kepala.review.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('kepala.review.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">👑</span>
                    Review Nominasi
                </a>
                
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-3">Menu Pegawai</p>
                <a href="{{ route('pegawai.survey.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('pegawai.survey.*') ? 'bg-[#e6f4fa] text-[#0091d5]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="mr-3 text-lg {{ request()->routeIs('pegawai.survey.*') ? 'text-[#0091d5]' : 'text-gray-400' }}">🗳️</span>
                    Voting Kandidat Terbaik (Read-Only)
                </a>
            </div>
            @endif


        </nav>
        
        <div class="p-4 border-t border-gray-200 space-y-1">
            <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-md">
                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Help Center
            </a>
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="flex w-full items-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 rounded-md">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50/50">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10">
            <div class="flex space-x-6 text-sm font-medium">
                <a href="#" class="text-gray-900 border-b-2 border-transparent hover:border-gray-300 pb-5 pt-5">Overview</a>
                <a href="#" class="text-gray-500 hover:text-gray-900 pb-5 pt-5">Reports</a>
                <a href="#" class="text-gray-500 hover:text-gray-900 pb-5 pt-5">Archive</a>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-full text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 bg-gray-50" placeholder="Search selection history...">
                </div>
                
                <button class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Notifications</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
                
                <button class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Settings</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                
                <div class="h-8 w-8 rounded-full bg-gray-200 overflow-hidden border border-gray-300">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=0D8ABC&color=fff" alt="User avatar" class="h-full w-full object-cover">
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto p-8 relative">
            <div class="max-w-5xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>

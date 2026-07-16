@extends('layouts.app')

@section('content')
<div class="mb-4 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Sistem Pemilihan Pegawai Terbaik BerAKHLAK BPS</p>
    </div>
</div>

@if(isset($popupPengumuman) && $popupPengumuman)
<!-- POPUP PENGUMUMAN -->
<div id="pengumumanPopup" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden transform transition-all">
        <div class="bg-blue-600 p-4 text-center">
            <h3 class="text-xl font-bold text-white uppercase tracking-wider">PENGUMUMAN PENTING</h3>
        </div>
        <div class="p-6">
            <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $popupPengumuman->judul }}</h4>
            <div class="text-gray-700 text-sm mb-6 whitespace-pre-wrap">{{ $popupPengumuman->konten }}</div>
            
            @if(is_array($popupPengumuman->lampiran) && count($popupPengumuman->lampiran) > 0)
                <div class="mb-6">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Lampiran:</p>
                    <div class="flex flex-col gap-2">
                        @foreach($popupPengumuman->lampiran as $lampiran)
                            <a href="{{ Storage::url($lampiran) }}" target="_blank" class="text-blue-600 text-sm hover:underline flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                {{ basename($lampiran) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <button id="btnSayaMengerti" data-id="{{ $popupPengumuman->id }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Saya Mengerti
            </button>
        </div>
    </div>
</div>
@endif

@if(isset($stickyPengumumans) && $stickyPengumumans->count() > 0)
<!-- STICKY PENGUMUMAN / BANNER (Medium Priority) -->
<div class="space-y-4 mb-6 sticky top-0 z-30 bg-bps-bg/90 backdrop-blur-sm py-2">
    @foreach($stickyPengumumans as $sticky)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-r-xl shadow-md p-5 relative overflow-hidden group">
        <div class="absolute right-0 top-0 text-blue-500 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-24 h-24 -mt-4 -mr-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        </div>
        <div class="flex items-start">
            <div class="flex-shrink-0 mt-0.5">
                <span class="text-xl">📢</span>
            </div>
            <div class="ml-3 w-full">
                <div class="flex justify-between items-center mb-1">
                    <h3 class="text-sm font-bold text-blue-900">{{ $sticky->judul }}</h3>
                    <span class="text-xs text-blue-500 font-medium bg-blue-100 px-2 py-0.5 rounded">{{ strtoupper($sticky->prioritas) }}</span>
                </div>
                <div class="text-sm text-blue-800 whitespace-pre-wrap">{{ $sticky->konten }}</div>
                
                @if(is_array($sticky->lampiran) && count($sticky->lampiran) > 0)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($sticky->lampiran as $lampiran)
                            <a href="{{ Storage::url($lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-xs bg-white text-blue-700 px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
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

@if(isset($bannerPengumumans) && $bannerPengumumans->count() > 0)
<!-- NON-STICKY BANNER PENGUMUMAN (Normal/Low Priority) -->
<div class="space-y-4 mb-6">
    @foreach($bannerPengumumans as $banner)
    <div class="bg-gradient-to-r from-gray-50 to-slate-50 border-l-4 border-gray-400 rounded-r-xl shadow-sm p-5 relative overflow-hidden group">
        <div class="absolute right-0 top-0 text-gray-400 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-24 h-24 -mt-4 -mr-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        </div>
        <div class="flex items-start">
            <div class="flex-shrink-0 mt-0.5">
                <span class="text-xl">ℹ️</span>
            </div>
            <div class="ml-3 w-full">
                <div class="flex justify-between items-center mb-1">
                    <h3 class="text-sm font-bold text-gray-900">{{ $banner->judul }}</h3>
                    <span class="text-xs text-gray-500 font-medium bg-gray-200 px-2 py-0.5 rounded">{{ strtoupper($banner->prioritas) }}</span>
                </div>
                <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $banner->konten }}</div>
                
                @if(is_array($banner->lampiran) && count($banner->lampiran) > 0)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($banner->lampiran as $lampiran)
                            <a href="{{ Storage::url($lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-xs bg-white text-gray-700 px-2 py-1 rounded border border-gray-300 hover:bg-gray-100 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
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

@if(isset($regularPengumumans) && $regularPengumumans->count() > 0)
<!-- REGULAR PENGUMUMAN -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm w-full p-6 mt-4 mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3m0 0l3-3m-3 3V8"></path></svg>
        Pengumuman Terbaru
    </h3>
    <div class="space-y-4">
        @foreach($regularPengumumans as $pengumuman)
        @php
            $color = 'blue';
            $dotColor = 'bg-blue-500';
            if($pengumuman->prioritas == 'Critical') {
                $color = 'red';
                $dotColor = 'bg-red-500';
            } elseif($pengumuman->prioritas == 'High') {
                $color = 'orange';
                $dotColor = 'bg-orange-500';
            } elseif($pengumuman->kategori == 'Hasil') {
                $color = 'green';
                $dotColor = 'bg-green-500';
            }
        @endphp
        <div class="border-b border-gray-100 last:border-0 pb-4 last:pb-0">
            <div class="flex items-start">
                <div class="mt-1.5 mr-3 w-2 h-2 rounded-full {{ $dotColor }} flex-shrink-0 animate-pulse"></div>
                <div class="w-full">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                        <h4 class="text-base font-semibold text-gray-900">{{ $pengumuman->judul }}</h4>
                        <span class="text-xs text-gray-400 mt-1 sm:mt-0">{{ $pengumuman->publish_at ? $pengumuman->publish_at->format('d M Y') : $pengumuman->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="mt-1 flex items-center gap-2 mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-{{ $color }}-600 bg-{{ $color }}-50 px-2 py-0.5 rounded">{{ $pengumuman->kategori }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($pengumuman->konten, 150) }}</p>
                    
                    <div x-data="{ open: false }">
                        @if(strlen($pengumuman->konten) > 150 || (is_array($pengumuman->lampiran) && count($pengumuman->lampiran) > 0))
                            <button @click="open = !open" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors" x-text="open ? 'Tutup' : 'Lihat Selengkapnya'"></button>
                        @endif
                        
                        <div x-show="open" x-transition class="mt-3 pt-3 border-t border-gray-100" style="display: none;">
                            <div class="text-sm text-gray-800 whitespace-pre-wrap">{{ $pengumuman->konten }}</div>
                            
                            @if(is_array($pengumuman->lampiran) && count($pengumuman->lampiran) > 0)
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Lampiran</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($pengumuman->lampiran as $lampiran)
                                            <a href="{{ Storage::url($lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-xs bg-gray-50 text-gray-700 px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 hover:border-gray-300 transition-all">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                {{ basename($lampiran) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(isset($activePeriode) && isset($phaseDetails))
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm w-full p-6 mt-4 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900">Kalender Timeline</h3>
            </div>
            @if($phaseDetails['next_phase'])
            <div class="mt-4 md:mt-0 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-center shadow-sm">
                <div class="bg-blue-100 p-2 rounded-full mr-3">
                    <svg class="w-5 h-5 text-bps-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-bps-secondary font-bold uppercase tracking-wider mb-0.5">Reminder</p>
                    <p class="text-sm text-blue-900 font-medium">
                        @if($phaseDetails['days_left'] == 0)
                            <span class="font-bold">Hari ini</span> masuk ke <span class="font-bold text-blue-700">{{ $phaseDetails['next_phase'] }}</span>
                        @else
                            Tinggal <span class="font-bold text-blue-700">{{ $phaseDetails['days_left'] }} hari</span> lagi masuk ke <span class="font-bold text-blue-700">{{ $phaseDetails['next_phase'] }}</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>

            @include('components.calendar-grid')
        </div>
@endif

@if(isset($top3) && $top3->count() > 0)
    <!-- Confetti Overlay -->
    <div class="fixed inset-0 pointer-events-none z-50 overflow-hidden" id="confetti-container"></div>

    <div class="relative bg-gradient-to-br from-[#0f172a] via-[#1e3a8a] to-[#0091d5] rounded-[2rem] p-6 lg:p-10 shadow-2xl overflow-hidden mt-4 border border-blue-800/30">
        <!-- Abstract Background Decor -->
        <div class="absolute -top-32 -right-32 w-[30rem] h-[30rem] bg-yellow-400 opacity-20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-[30rem] h-[30rem] bg-sky-400 opacity-20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute inset-0 bg-white/5 backdrop-blur-[2px] pointer-events-none"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+CjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz4KPHBhdGggZD0iTTAgMGwyMCAyMEw0MCAwaC0xTDIwIDE5LjUgMSAwem0wIDQwbDIwLTIwTDAgMHYxbDE5LjUgMjBMMCAzOXptNDAgMGwtMjAtMjBMMCA0MGgxTDIwIDIwLjUgMzkgNDB6bTAtNDBMMjAgMjAgNDAgNDB2LTFsLTE5LjUtMjBMMzkgMHoiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPgo8L3N2Zz4=')] opacity-50 pointer-events-none"></div>

        <div class="relative z-10 text-center mb-10">
            <span class="bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 px-5 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-4 inline-block shadow-lg">HASIL PEMILIHAN KARYAWAN TERBAIK</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white mb-3 drop-shadow-sm">KARYAWAN TERBAIK BPS</h2>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto font-light">Merayakan integritas, dedikasi, dan profesionalisme para insan statistik terbaik pada <b class="font-bold text-white">{{ $pemenangTerakhir->periode->nama }}</b>.</p>
        </div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-4 gap-10">
            
            <!-- Podium Section (Takes 3 columns) -->
            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end px-2">
                    <!-- 2nd Place -->
                    @if($top3->has(1))
                    <div class="order-2 md:order-1 transform transition-all duration-300 hover:-translate-y-2 group">
                        <div class="flex flex-col items-center">
                            <div class="relative mb-6">
                                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-slate-300 overflow-hidden bg-slate-100 shadow-[0_0_20px_rgba(203,213,225,0.3)] flex items-center justify-center transition-transform group-hover:scale-105">
                                    @if($top3[1]->kandidat->pegawai->foto_profil)
                                        <img src="{{ $top3[1]->kandidat->pegawai->foto_profil_url }}" alt="{{ $top3[1]->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl font-black text-slate-400">{{ substr($top3[1]->kandidat->pegawai->nama, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="absolute -bottom-3 -right-1 bg-gradient-to-br from-slate-300 to-slate-500 text-slate-900 rounded-full w-12 h-12 flex items-center justify-center font-black text-xl border-4 border-[#1e3a8a] shadow-lg">2</div>
                            </div>
                            <div class="bg-white/95 backdrop-blur-md w-full rounded-t-2xl p-6 text-center border-t border-x border-slate-200/50 shadow-xl min-h-[150px]">
                                <h3 class="text-xl font-bold text-slate-800 truncate" title="{{ $top3[1]->kandidat->pegawai->nama }}">{{ $top3[1]->kandidat->pegawai->nama }}</h3>
                                <p class="text-xs text-slate-500 mt-1 font-semibold">NIP. {{ $top3[1]->kandidat->pegawai->nip }}</p>
                                <p class="text-sm font-semibold mt-3 text-slate-600 truncate bg-slate-100/80 py-1.5 px-2 rounded-lg" title="{{ $top3[1]->kandidat->pegawai->jabatan }}">{{ $top3[1]->kandidat->pegawai->jabatan }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="order-2 md:order-1"></div>
                    @endif

                    <!-- 1st Place (Winner) -->
                    <div class="order-1 md:order-2 transform transition-all duration-300 hover:-translate-y-3 relative z-20">
                        <div class="flex flex-col items-center">
                            <div class="relative mb-8 group">
                                <div class="absolute inset-0 bg-yellow-400 rounded-full blur-2xl opacity-50 group-hover:opacity-70 transition-opacity animate-pulse"></div>
                                <div class="w-32 h-32 md:w-48 md:h-48 rounded-full border-4 border-yellow-400 overflow-hidden bg-white shadow-[0_0_30px_rgba(250,204,21,0.5)] relative z-10 flex items-center justify-center transition-transform group-hover:scale-105">
                                    @if($top3[0]->kandidat->pegawai->foto_profil)
                                        <img src="{{ $top3[0]->kandidat->pegawai->foto_profil_url }}" alt="{{ $top3[0]->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-7xl font-black text-yellow-500">{{ substr($top3[0]->kandidat->pegawai->nama, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="absolute -bottom-5 -right-2 bg-gradient-to-br from-yellow-300 to-yellow-600 text-yellow-900 rounded-full w-16 h-16 flex items-center justify-center font-bold text-3xl border-4 border-[#1e3a8a] z-20 shadow-2xl">&#x1F3C6;</div>
                                <div class="absolute -top-8 left-1/2 z-20 -translate-x-1/2 text-yellow-400 flex flex-col items-center animate-bounce">
                                    <svg class="w-16 h-16 drop-shadow-[0_0_10px_rgba(250,204,21,0.8)]" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5ZM19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z"/></svg>
                                </div>
                            </div>
                            <div class="bg-gradient-to-b from-yellow-400 to-amber-600 text-white w-full rounded-t-3xl p-8 text-center shadow-2xl relative overflow-hidden min-h-[190px] border border-yellow-300/50">
                                <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                                <h3 class="text-3xl font-black mb-1 relative z-10 drop-shadow-md" title="{{ $top3[0]->kandidat->pegawai->nama }}">{{ $top3[0]->kandidat->pegawai->nama }}</h3>
                                <p class="text-sm opacity-90 relative z-10 font-medium">NIP. {{ $top3[0]->kandidat->pegawai->nip }}</p>
                                <p class="text-sm font-bold mt-3 text-yellow-100 relative z-10 truncate bg-black/10 py-2 px-3 rounded-xl backdrop-blur-sm shadow-inner" title="{{ $top3[0]->kandidat->pegawai->jabatan }}">{{ $top3[0]->kandidat->pegawai->jabatan }}</p>
                            </div>
                        </div>
                        @if($pemenangTerakhir->catatan_kepala)
                        <div class="mt-4 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl shadow-xl relative overflow-hidden">
                            <div class="absolute top-2 left-2 text-yellow-400/20">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 32 32"><path d="M10 8c-3.3 0-6 2.7-6 6v10h10V14H8c0-2.2 1.8-4 4-4V8zm16 0c-3.3 0-6 2.7-6 6v10h10V14h-6c0-2.2 1.8-4 4-4V8z"></path></svg>
                            </div>
                            <h4 class="text-xs font-black text-yellow-300 uppercase tracking-widest mb-2 relative z-10">Catatan Pimpinan</h4>
                            <p class="text-white text-sm italic relative z-10 font-light leading-relaxed">"{{ $pemenangTerakhir->catatan_kepala }}"</p>
                            <p class="text-xs text-blue-200 mt-3 text-right font-bold relative z-10">- {{ $pemenangTerakhir->pemilih->nama ?? 'Kepala Bagian' }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- 3rd Place -->
                    @if($top3->has(2))
                    <div class="order-3 transform transition-all duration-300 hover:-translate-y-2 group">
                        <div class="flex flex-col items-center">
                            <div class="relative mb-6">
                                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-orange-400 overflow-hidden bg-slate-100 shadow-[0_0_20px_rgba(249,115,22,0.3)] flex items-center justify-center transition-transform group-hover:scale-105">
                                    @if($top3[2]->kandidat->pegawai->foto_profil)
                                        <img src="{{ $top3[2]->kandidat->pegawai->foto_profil_url }}" alt="{{ $top3[2]->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl font-black text-orange-400">{{ substr($top3[2]->kandidat->pegawai->nama, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="absolute -bottom-3 -right-1 bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full w-12 h-12 flex items-center justify-center font-black text-xl border-4 border-[#1e3a8a] shadow-lg">3</div>
                            </div>
                            <div class="bg-white/95 backdrop-blur-md w-full rounded-t-2xl p-6 text-center border-t border-x border-orange-200/50 shadow-xl min-h-[150px]">
                                <h3 class="text-xl font-bold text-slate-800 truncate" title="{{ $top3[2]->kandidat->pegawai->nama }}">{{ $top3[2]->kandidat->pegawai->nama }}</h3>
                                <p class="text-xs text-slate-500 mt-1 font-semibold">NIP. {{ $top3[2]->kandidat->pegawai->nip }}</p>
                                <p class="text-sm font-semibold mt-3 text-slate-600 truncate bg-orange-50 py-1.5 px-2 rounded-lg text-orange-900" title="{{ $top3[2]->kandidat->pegawai->jabatan }}">{{ $top3[2]->kandidat->pegawai->jabatan }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="order-3"></div>
                    @endif
                </div>
            </div>

            <!-- Motivational Quote Sidebar (Takes 1 column) -->
            <div class="lg:col-span-1 flex flex-col justify-center mt-8 lg:mt-0">
                <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-8 rounded-3xl shadow-2xl h-full flex flex-col items-center justify-center text-center relative overflow-hidden group hover:bg-white/15 transition-all duration-500">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-400/20 rounded-full blur-xl group-hover:bg-yellow-400/30 transition-all"></div>
                    <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-blue-400/20 rounded-full blur-xl group-hover:bg-blue-400/30 transition-all"></div>
                    
                    <svg class="w-12 h-12 text-yellow-400 mb-6 drop-shadow-md transform group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 32 32">
                        <path d="M10 8c-3.3 0-6 2.7-6 6v10h10V14H8c0-2.2 1.8-4 4-4V8zm16 0c-3.3 0-6 2.7-6 6v10h10V14h-6c0-2.2 1.8-4 4-4V8z"></path>
                    </svg>
                    
                    <p class="text-lg font-medium text-white mb-8 leading-relaxed relative z-10 italic">
                        "Karyawan terbaik bukan hanya tentang memiliki kinerja luar biasa, tetapi juga tentang bagaimana menginspirasi sekelilingnya dengan nilai-nilai luhur <b>BerAKHLAK</b>."
                    </p>
                    
                    <div class="w-16 h-1.5 bg-gradient-to-r from-yellow-300 to-yellow-500 rounded-full mb-6 shadow-sm"></div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <span class="text-sm text-blue-100 font-bold uppercase tracking-widest mb-1">Jadilah Inspirasi</span>
                        <span class="text-xs text-blue-300/80 font-medium">BPS Kabupaten Tegal</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

@elseif(isset($activePeriode) && isset($votingProgress))
    @if(isset($quorumWarning) && $quorumWarning)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-yellow-800">
                        Peringatan: Tingkat partisipasi voting saat ini masih di bawah 50% ({{ round($percentVoting, 1) }}%). Hasil pemilihan mungkin belum sepenuhnya representatif.
                    </p>
                </div>
            </div>
        </div>
    @endif
    <!-- Progress Voting -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm w-full p-8 mt-4">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Progress Voting</h3>
                <p class="text-sm text-gray-500 mt-1">Periode: {{ $activePeriode->nama }} <span class="uppercase font-semibold text-xs ml-2 bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $activePeriode->status }}</span></p>
            </div>
            <div class="flex items-center gap-4 text-sm font-medium">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 border border-green-600"></span> Sudah Voting</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-100 border border-gray-300"></span> Belum Voting</div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($votingProgress as $vp)
                <div class="flex items-center p-3 rounded-xl border {{ $vp['sudah_voting'] ? 'border-green-200 bg-green-50/40' : 'border-gray-100 bg-bps-bg/40' }}">
                    <div class="relative flex-shrink-0">
                        @if($vp['foto'])
                            <img src="{{ $vp['foto'] }}" class="w-12 h-12 rounded-full object-cover border-2 {{ $vp['sudah_voting'] ? 'border-green-500' : 'border-gray-200' }}">
                        @else
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base {{ $vp['sudah_voting'] ? 'bg-green-100 text-green-700 border-2 border-green-500' : 'bg-gray-200 text-gray-600 border-2 border-gray-200' }}">
                                {{ substr($vp['nama'], 0, 1) }}
                            </div>
                        @endif
                        @if($vp['sudah_voting'])
                            <div class="absolute -bottom-1 -right-1 bg-green-500 rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 min-w-0">
                        <p class="text-sm font-semibold {{ $vp['sudah_voting'] ? 'text-gray-900' : 'text-gray-600' }} truncate" title="{{ $vp['nama'] }}">{{ $vp['nama'] }}</p>
                        <p class="text-xs {{ $vp['sudah_voting'] ? 'text-green-600 font-medium' : 'text-gray-400' }} mt-0.5">{{ $vp['sudah_voting'] ? 'Selesai' : 'Menunggu' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@else
    <!-- Empty state -->
    <div class="w-full bg-white border border-gray-200 rounded-xl shadow-sm min-h-[24rem] flex flex-col items-center justify-center p-8 text-center mt-4">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900">Belum ada pemenang saat ini</h3>
        <p class="text-gray-500 mt-1 max-w-sm">Data pemenang pegawai terbaik akan muncul di sini setelah periode penilaian selesai dan Kepala Bagian telah menentukan pemenang.</p>
    </div>
@endif

@push('scripts')
<style>
    .confetti { position: absolute; width: 8px; height: 8px; background-color: #fce18a; animation: confetti-fall linear forwards; }
    @keyframes confetti-fall { 0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; } 100% { transform: translateY(100vh) rotate(720deg); opacity: 0; } }
</style>
<script>
    function createConfetti() {
        const container = document.getElementById('confetti-container');
        if (!container) return;
        const colors = ['#0091d5', '#fce18a', '#ffb86e', '#91da40', '#ffffff'];
        for (let i = 0; i < 100; i++) {
            const c = document.createElement('div');
            c.classList.add('confetti');
            c.style.left = Math.random() * 100 + 'vw';
            c.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            c.style.animationDuration = (Math.random() * 3 + 2) + 's';
            c.style.opacity = Math.random();
            c.style.width = (Math.random() * 10 + 5) + 'px';
            c.style.height = c.style.width;
            container.appendChild(c);
            setTimeout(() => c.remove(), 5000);
        }
    }
    if (document.getElementById('confetti-container')) {
        window.addEventListener('load', createConfetti);
    }
    
    // Popup Logic
    const btnSayaMengerti = document.getElementById('btnSayaMengerti');
    if (btnSayaMengerti) {
        btnSayaMengerti.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const popup = document.getElementById('pengumumanPopup');
            
            // UI Update first for responsiveness
            popup.classList.add('opacity-0');
            setTimeout(() => {
                popup.style.display = 'none';
            }, 300);
            
            // Send AJAX
            fetch(`/pengumuman/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            }).catch(err => console.error(err));
        });
    }
</script>
@endpush
@endsection

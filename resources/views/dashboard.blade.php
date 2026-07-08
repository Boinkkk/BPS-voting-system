@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Utama</h1>
    <p class="text-gray-500 text-sm mt-1">Sistem Pemilihan Pegawai Terbaik BerAKHLAK BPS</p>
</div>

@if(isset($pemenangTerakhir) && $pemenangTerakhir)
<div class="relative overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-xl p-8 mb-8">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-gradient-to-br from-yellow-300 to-yellow-500 opacity-20 blur-2xl"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-gradient-to-tr from-sky-300 to-[#0091d5] opacity-10 blur-3xl"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-center md:justify-start gap-8">
        <!-- Avatar/Trophy Area -->
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse"></div>
            <div class="relative w-32 h-32 md:w-40 md:h-40 bg-white rounded-full border-4 border-yellow-400 p-2 shadow-2xl flex items-center justify-center">
                <span class="text-6xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-yellow-500 to-orange-600">
                    {{ substr($pemenangTerakhir->kandidat->pegawai->nama, 0, 1) }}
                </span>
                
                <!-- Trophy Badge -->
                <div class="absolute -bottom-4 -right-4 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white w-14 h-14 rounded-full border-4 border-white flex items-center justify-center shadow-lg transform rotate-12">
                    <span class="text-2xl">🏆</span>
                </div>
            </div>
        </div>

        <!-- Winner Details -->
        <div class="text-center md:text-left flex-1">
            <div class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full uppercase tracking-wider mb-3 border border-yellow-200 shadow-sm">
                Pegawai Terbaik - {{ $pemenangTerakhir->periode->nama }}
            </div>
            
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-2">
                {{ $pemenangTerakhir->kandidat->pegawai->nama }}
            </h2>
            
            <div class="text-lg text-[#0091d5] font-semibold mb-4 flex items-center justify-center md:justify-start gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                {{ $pemenangTerakhir->kandidat->pegawai->jabatan }}
            </div>
            
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 inline-block shadow-sm">
                <p class="text-sm text-gray-500 mb-1">Skor Akhir Kinerja & Voting</p>
                <div class="text-2xl font-black text-gray-800">
                    {{ number_format($pemenangTerakhir->kandidat->skor, 2, ',', '.') }}
                </div>
            </div>
        </div>
        
        <!-- Congratulation Note -->
        <div class="w-full md:w-64 bg-gradient-to-br from-sky-50 to-white border border-sky-100 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-2 opacity-10">
                <span class="text-6xl">⭐</span>
            </div>
            <h4 class="text-sm font-bold text-sky-800 uppercase tracking-wide mb-2">Catatan Pimpinan</h4>
            <p class="text-gray-600 text-sm italic relative z-10">
                "{{ $pemenangTerakhir->catatan_kepala ?: 'Selamat atas pencapaian luar biasa ini. Teruslah berkarya dan memberikan yang terbaik!' }}"
            </p>
            <p class="text-xs text-gray-400 mt-4 text-right">
                - {{ $pemenangTerakhir->pemilih->nama ?? 'Kepala Bagian' }}
            </p>
        </div>
    </div>
</div>
@else
<div class="bg-white border border-gray-200 rounded-xl shadow-sm h-96 flex flex-col items-center justify-center p-8 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900">Belum ada pemenang saat ini</h3>
    <p class="text-gray-500 mt-1 max-w-sm">Data pemenang pegawai terbaik akan muncul di sini setelah periode penilaian selesai dan Kepala Bagian telah menentukan pemenang.</p>
</div>
@endif
@endsection

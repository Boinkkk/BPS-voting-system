@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Utama</h1>
    <p class="text-gray-500 text-sm mt-1">Sistem Pemilihan Pegawai Terbaik BerAKHLAK BPS</p>
</div>

@if(isset($top3) && $top3->count() > 0)
    <!-- Confetti Overlay -->
    <div class="fixed inset-0 pointer-events-none z-50 overflow-hidden" id="confetti-container"></div>
    
    <div class="text-center mb-12">
        <span class="bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">Official Results</span>
        <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-2">BPS Employee Excellence</h2>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto">Merayakan integritas, dedikasi, dan profesionalisme para insan statistik terbaik dalam mengawal data bangsa pada <b>{{ $pemenangTerakhir->periode->nama }}</b>.</p>
    </div>

    <!-- Podium Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end mb-16 px-2">
        
        <!-- 2nd Place -->
        @if($top3->has(1))
        <div class="order-2 md:order-1 transform transition duration-300 hover:-translate-y-2">
            <div class="flex flex-col items-center">
                <div class="relative mb-6">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-gray-300 overflow-hidden bg-white shadow-lg flex items-center justify-center">
                        @if($top3[1]->kandidat->pegawai->foto_profil)
                            <img src="{{ $top3[1]->kandidat->pegawai->foto_profil_url }}" alt="{{ $top3[1]->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-gray-500">{{ substr($top3[1]->kandidat->pegawai->nama, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-gray-400 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">2</div>
                </div>
                <div class="bg-white w-full rounded-t-xl p-6 text-center border-t border-x border-gray-200 shadow-sm min-h-[140px]">
                    <h3 class="text-xl font-bold text-gray-900 truncate" title="{{ $top3[1]->kandidat->pegawai->nama }}">{{ $top3[1]->kandidat->pegawai->nama }}</h3>
                    <p class="text-xs text-gray-500 mt-1">NIP. {{ $top3[1]->kandidat->pegawai->nip }}</p>
                    <p class="text-sm font-semibold mt-2 text-gray-700 truncate" title="{{ $top3[1]->kandidat->pegawai->jabatan }}">{{ $top3[1]->kandidat->pegawai->jabatan }}</p>
                    <div class="mt-3 text-lg font-black text-blue-600">{{ number_format($top3[1]->kandidat->skor, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @else
        <div class="order-2 md:order-1"></div>
        @endif

        <!-- 1st Place (Winner) -->
        <div class="order-1 md:order-2 transform transition duration-300 hover:-translate-y-2 relative z-10">
            <div class="flex flex-col items-center">
                <div class="relative mb-6 group">
                    <div class="absolute inset-0 bg-yellow-400 rounded-full blur-2xl opacity-30 group-hover:opacity-50 transition-opacity"></div>
                    <div class="w-32 h-32 md:w-48 md:h-48 rounded-full border-4 border-yellow-400 overflow-hidden bg-white shadow-2xl relative z-10 flex items-center justify-center">
                        @if($top3[0]->kandidat->pegawai->foto_profil)
                            <img src="{{ $top3[0]->kandidat->pegawai->foto_profil_url }}" alt="{{ $top3[0]->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-6xl font-bold text-yellow-600">{{ substr($top3[0]->kandidat->pegawai->nama, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="absolute -bottom-4 -right-2 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-full w-14 h-14 flex items-center justify-center font-bold text-2xl border-4 border-white z-20 shadow-xl">
                        🏆
                    </div>
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-yellow-500 flex flex-col items-center animate-bounce">
                        <span class="text-4xl">⭐</span>
                    </div>
                </div>
                <div class="bg-gradient-to-b from-blue-600 to-blue-800 text-white w-full rounded-t-2xl p-6 text-center shadow-2xl relative overflow-hidden min-h-[180px]">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <svg height="100%" preserveAspectRatio="none" viewBox="0 0 100 100" width="100%">
                            <path d="M0 100 L50 0 L100 100 Z" fill="currentColor"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 relative z-10" title="{{ $top3[0]->kandidat->pegawai->nama }}">{{ $top3[0]->kandidat->pegawai->nama }}</h3>
                    <p class="text-sm opacity-90 relative z-10">NIP. {{ $top3[0]->kandidat->pegawai->nip }}</p>
                    <p class="text-base font-bold mt-3 text-blue-200 relative z-10 truncate" title="{{ $top3[0]->kandidat->pegawai->jabatan }}">{{ $top3[0]->kandidat->pegawai->jabatan }}</p>
                    <div class="mt-4 text-2xl font-black text-yellow-300 relative z-10">{{ number_format($top3[0]->kandidat->skor, 2, ',', '.') }}</div>
                    <div class="mt-4 inline-flex items-center gap-1 px-4 py-1.5 bg-white/20 rounded-full text-xs font-bold relative z-10">
                        JUARA 1 TERBAIK
                    </div>
                </div>
            </div>
            
            <!-- Congratulation Note -->
            @if($pemenangTerakhir->catatan_kepala)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 p-4 rounded-xl shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-2 opacity-10">
                    <span class="text-4xl">⭐</span>
                </div>
                <h4 class="text-xs font-bold text-yellow-800 uppercase tracking-wide mb-1">Catatan Pimpinan</h4>
                <p class="text-gray-700 text-sm italic relative z-10">
                    "{{ $pemenangTerakhir->catatan_kepala }}"
                </p>
                <p class="text-xs text-gray-500 mt-2 text-right">
                    - {{ $pemenangTerakhir->pemilih->nama ?? 'Kepala Bagian' }}
                </p>
            </div>
            @endif
        </div>

        <!-- 3rd Place -->
        @if($top3->has(2))
        <div class="order-3 transform transition duration-300 hover:-translate-y-2">
            <div class="flex flex-col items-center">
                <div class="relative mb-6">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-amber-600 overflow-hidden bg-white shadow-lg flex items-center justify-center">
                        @if($top3[2]->kandidat->pegawai->foto_profil)
                            <img src="{{ $top3[2]->kandidat->pegawai->foto_profil_url }}" alt="{{ $top3[2]->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-amber-700">{{ substr($top3[2]->kandidat->pegawai->nama, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-amber-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">3</div>
                </div>
                <div class="bg-white w-full rounded-t-xl p-6 text-center border-t border-x border-gray-200 shadow-sm min-h-[140px]">
                    <h3 class="text-xl font-bold text-gray-900 truncate" title="{{ $top3[2]->kandidat->pegawai->nama }}">{{ $top3[2]->kandidat->pegawai->nama }}</h3>
                    <p class="text-xs text-gray-500 mt-1">NIP. {{ $top3[2]->kandidat->pegawai->nip }}</p>
                    <p class="text-sm font-semibold mt-2 text-gray-700 truncate" title="{{ $top3[2]->kandidat->pegawai->jabatan }}">{{ $top3[2]->kandidat->pegawai->jabatan }}</p>
                    <div class="mt-3 text-lg font-black text-blue-600">{{ number_format($top3[2]->kandidat->skor, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @else
        <div class="order-3"></div>
        @endif
    </div>

    @push('scripts')
    <style>
        .confetti {
            position: absolute;
            width: 8px;
            height: 8px;
            background-color: #fce18a;
            animation: confetti-fall linear forwards;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
    <script>
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            if(!container) return;
            const colors = ['#0091d5', '#fce18a', '#ffb86e', '#91da40', '#ffffff'];
            
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                confetti.style.opacity = Math.random();
                confetti.style.width = (Math.random() * 10 + 5) + 'px';
                confetti.style.height = confetti.style.width;
                container.appendChild(confetti);

                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }
        window.addEventListener('load', createConfetti);
    </script>
    @endpush
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

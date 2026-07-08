@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="font-semibold text-2xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
        Review Nominasi Pegawai Terbaik
    </h2>
    <p class="text-sm text-gray-500 mt-1">Pilih 1 pegawai terbaik dari 3 nominasi teratas hasil voting pegawai.</p>
</div>

<div class="py-6" style="font-family: 'Hanken Grotesk', sans-serif;">
    <div class="max-w-7xl mx-auto">

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if(!$periodeReview)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <span class="text-3xl">☕</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Periode Review</h3>
                <p class="text-gray-500">Saat ini tidak ada periode penilaian yang berstatus "Review Kepala Bagian".</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Periode: {{ $periodeReview->nama }}</h3>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($periodeReview->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periodeReview->tanggal_selesai)->format('d M Y') }}</p>
                </div>
                <div class="px-4 py-2 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md font-semibold text-sm">
                    Status: Menunggu Keputusan Anda
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($kandidats as $index => $h)
                    <div class="bg-white rounded-xl shadow-md border {{ $index == 0 ? 'border-yellow-300' : 'border-gray-200' }} overflow-hidden transform transition-all hover:-translate-y-1 hover:shadow-lg flex flex-col">
                        <div class="bg-gradient-to-r {{ $index == 0 ? 'from-yellow-500 to-yellow-600' : ($index == 1 ? 'from-gray-400 to-gray-500' : 'from-orange-400 to-orange-500') }} p-4 text-white text-center relative">
                            <span class="absolute top-4 right-4 bg-white/20 px-2 py-1 rounded text-xs font-bold">Rank {{ $h->ranking_final }}</span>
                            <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center text-3xl shadow-inner mt-4 mb-2">
                                <span class="{{ $index == 0 ? 'text-yellow-500' : ($index == 1 ? 'text-gray-500' : 'text-orange-500') }} font-bold">
                                    {{ substr($h->kandidat->pegawai->nama, 0, 1) }}
                                </span>
                            </div>
                            <h3 class="font-bold text-lg leading-tight">{{ $h->kandidat->pegawai->nama }}</h3>
                            <p class="text-sm text-white/80 mt-1">{{ $h->kandidat->pegawai->jabatan }}</p>
                        </div>
                        
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                                    <span class="text-gray-500 text-sm">NIP</span>
                                    <span class="font-medium text-gray-900">{{ $h->kandidat->pegawai->nip }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                                    <span class="text-gray-500 text-sm">Skor Evaluasi (0-100)</span>
                                    <span class="font-bold text-[#0091d5]">{{ number_format($h->kandidat->skor, 2, ',', '.') }}</span>
                                </div>
                                <div class="text-sm text-gray-600 text-center mb-6 italic">
                                    "Kandidat ini adalah salah satu dari 3 peraih voting tertinggi oleh pegawai."
                                </div>
                            </div>
                            
                            <form action="{{ route('kepala.review.pilih', $h->id) }}" method="POST" onsubmit="return confirm('Anda yakin menetapkan {{ $h->kandidat->pegawai->nama }} sebagai Pegawai Terbaik? Tindakan ini akan mengakhiri periode penilaian.');">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Catatan/Alasan (Opsional)</label>
                                    <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded text-sm focus:ring-[#0091d5] focus:border-[#0091d5]" placeholder="Berikan ucapan selamat atau alasan pemilihan..."></textarea>
                                </div>
                                <button type="submit" class="w-full py-3 bg-[#0091d5] text-white font-bold rounded-lg shadow hover:bg-sky-700 transition-colors flex justify-center items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tetapkan Pemenang
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($kandidats->isEmpty())
                <div class="text-center py-8 text-gray-500 border rounded-lg bg-gray-50 mt-6">
                    Data nominasi top 3 belum di-generate. Silakan hubungi Administrator.
                </div>
            @endif
        @endif

    </div>
</div>
@endsection

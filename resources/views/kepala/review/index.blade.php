@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="font-semibold text-2xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
        Review Nominasi Pegawai Terbaik
    </h2>
    <p class="text-sm text-gray-500 mt-1">Pemilihan Karyawan Terbaik dari Nominasi 3 Teratas berdasarkan Survey, Nilai CKP, Dan Absensi</p>
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
                                    <span class="text-gray-500 text-sm">Skor CKP (0-100)</span>
                                    <span class="font-bold text-gray-700">{{ number_format($h->kandidat->skor_ckp, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                                    <span class="text-gray-500 text-sm">Skor Absensi (0-100)</span>
                                    <span class="font-bold text-gray-700">{{ number_format($h->kandidat->skor_absensi, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                                    <span class="text-gray-500 text-sm">Skor Survei (0-100)</span>
                                    <span class="font-bold text-gray-700">{{ number_format($h->skor_survey_normalized ?? 0, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                                    <span class="text-gray-500 text-sm">Skor Fase 1 (Evaluasi)</span>
                                    <span class="font-bold text-[#0091d5]">{{ number_format($h->kandidat->skor, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                                    <span class="text-gray-500 text-sm">Skor Akhir Keseluruhan</span>
                                    <span class="font-bold text-[#0091d5]">{{ number_format($h->skor_akhir_voting, 2, ',', '.') }}</span>
                                </div>
                                <div class="text-sm text-gray-600 text-center mb-6 italic">
                                    "Kandidat ini adalah salah satu dari 3 peraih voting tertinggi oleh pegawai."
                                </div>
                            </div>
                            
                            <form id="form-pilih-{{ $h->id }}" action="{{ route('kepala.review.pilih', $h->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Catatan/Alasan (Opsional)</label>
                                    <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded text-sm focus:ring-[#0091d5] focus:border-[#0091d5]" placeholder="Berikan ucapan selamat atau alasan pemilihan..."></textarea>
                                </div>
                                <button type="button" onclick="openConfirmModal('{{ $h->id }}', '{{ addslashes($h->kandidat->pegawai->nama) }}')" class="w-full py-3 bg-[#0091d5] text-white font-bold rounded-lg shadow hover:bg-sky-700 transition-colors flex justify-center items-center">
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

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="font-family: 'Hanken Grotesk', sans-serif;">
    <!-- Backdrop -->
    <div id="modalOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity opacity-0 backdrop-blur-sm" aria-hidden="true" onclick="closeConfirmModal()"></div>

    <!-- Modal Panel -->
    <div id="modalPanel" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transition-all opacity-0 translate-y-8 w-full max-w-md mx-auto flex flex-col z-10 border border-gray-100" style="min-width: 280px;">
        <div class="bg-white px-5 pt-6 pb-5 sm:p-6 sm:pb-5">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-full bg-blue-50 sm:mx-0 sm:h-12 sm:w-12">
                    <svg class="h-7 w-7 text-[#0091d5]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-4 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                        Konfirmasi Penetapan
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Anda yakin ingin menetapkan <strong id="modalKandidatName" class="text-gray-900"></strong> sebagai Pegawai Terbaik? Tindakan ini akan mengakhiri periode penilaian dan tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
            <button type="button" onclick="submitForm()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-[#0091d5] text-base font-bold text-white hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0091d5] sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                Ya, Tetapkan
            </button>
            <button type="button" onclick="closeConfirmModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0091d5] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentFormId = null;

    function openConfirmModal(id, name) {
        currentFormId = 'form-pilih-' + id;
        document.getElementById('modalKandidatName').innerText = name;
        
        const modal = document.getElementById('confirmModal');
        const overlay = document.getElementById('modalOverlay');
        const panel = document.getElementById('modalPanel');
        
        // Remove hidden so display: flex takes over
        modal.classList.remove('hidden');
        
        // Trigger transitions
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-8');
            panel.classList.add('opacity-100', 'translate-y-0');
        }, 20);
    }

    function closeConfirmModal() {
        const overlay = document.getElementById('modalOverlay');
        const panel = document.getElementById('modalPanel');
        
        overlay.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0');
        panel.classList.add('opacity-0', 'translate-y-8');
        
        setTimeout(() => {
            document.getElementById('confirmModal').classList.add('hidden');
            currentFormId = null;
        }, 300);
    }

    function submitForm() {
        if (currentFormId) {
            document.getElementById(currentFormId).submit();
        }
    }
</script>
@endpush
@endsection

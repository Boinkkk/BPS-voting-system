@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="font-bold text-3xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
        Review Nominasi Pegawai Terbaik
    </h2>
    <p class="text-base text-gray-500 mt-2">Pilih Karyawan Terbaik dari Nominasi 3 Teratas berdasarkan seluruh aspek penilaian.</p>
</div>

<div class="py-2" style="font-family: 'Hanken Grotesk', sans-serif;">
    <div class="max-w-7xl mx-auto">

        @if (session('error'))
            <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-200 flex items-center shadow-sm">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if(!$periodeReview)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-6 border border-gray-100 shadow-inner">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Periode Review</h3>
                <p class="text-gray-500 max-w-md mx-auto">Saat ini tidak ada periode penilaian yang berstatus "Review Kepala Bagian". Silakan tunggu tim penilai menyelesaikan tugasnya.</p>
            </div>
        @else
            <!-- Header Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-[#0091d5] opacity-5 rounded-bl-full pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 bg-[#0091d5]/10 text-[#0091d5] text-xs font-bold rounded-full uppercase tracking-wider">Sedang Berlangsung</span>
                        <h3 class="font-bold text-gray-900 text-xl">{{ $periodeReview->nama }}</h3>
                    </div>
                    <p class="text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ \Carbon\Carbon::parse($periodeReview->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periodeReview->tanggal_selesai)->format('d M Y') }}
                    </p>
                </div>
                <div class="relative z-10 px-5 py-3 bg-gradient-to-r from-amber-100 to-yellow-100 text-yellow-800 border border-yellow-200/50 rounded-xl font-bold text-sm shadow-sm flex items-center">
                    <span class="relative flex h-3 w-3 mr-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-500 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                    </span>
                    Menunggu Keputusan Anda
                </div>
            </div>

            <!-- Podium Layout -->
            <div class="flex flex-col md:flex-row justify-center items-end gap-6 lg:gap-8 pt-8 pb-12 relative">
                
                <!-- Background decorative blob -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-64 bg-gradient-to-r from-blue-50 via-sky-50 to-indigo-50 rounded-full blur-3xl opacity-50 -z-10 pointer-events-none"></div>

                @foreach($kandidats as $index => $h)
                    <div class="w-full md:flex-1 relative flex flex-col mt-4 md:mt-0">
                        <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden flex flex-col h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-[#0091d5]/30">
                            
                            <!-- Card Header -->
                            <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-6 text-center relative overflow-hidden text-white">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl transform translate-x-10 -translate-y-10"></div>
                                
                                <div class="w-28 h-28 mx-auto bg-white rounded-full flex flex-col items-center justify-center shadow-lg border-4 border-white/20 mb-4 relative overflow-hidden">
                                    @if($h->kandidat->pegawai->foto_profil)
                                        <img src="{{ asset('storage/' . $h->kandidat->pegawai->foto_profil) }}" alt="{{ $h->kandidat->pegawai->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-br from-slate-500 to-slate-700">
                                            {{ substr($h->kandidat->pegawai->nama, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <h3 class="font-bold text-xl leading-tight mb-1">{{ $h->kandidat->pegawai->nama }}</h3>
                                <p class="text-sm font-medium opacity-90">{{ $h->kandidat->pegawai->jabatan }}</p>
                                <p class="text-xs opacity-75 mt-1">{{ $h->kandidat->pegawai->nip }}</p>
                            </div>
                            
                            <!-- Card Body / Scores -->
                            <div class="p-6 flex-grow flex flex-col">
                                <div class="space-y-4 flex-grow">
                                    <!-- CKP -->
                                    <div class="bg-gray-50/50 rounded-2xl p-3 border border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-[#0091d5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Nilai CKP
                                            </span>
                                            <span class="font-black text-gray-800">{{ number_format($h->kandidat->skor_ckp, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-[#0091d5] h-1.5 rounded-full" style="width: {{ $h->kandidat->skor_ckp }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Absensi -->
                                    <div class="bg-gray-50/50 rounded-2xl p-3 border border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Absensi
                                            </span>
                                            <span class="font-black text-gray-800">{{ number_format($h->kandidat->skor_absensi, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $h->kandidat->skor_absensi }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Survei -->
                                    <div class="bg-gray-50/50 rounded-2xl p-3 border border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                                                Voting Survei
                                            </span>
                                            <span class="font-black text-gray-800">{{ number_format($h->skor_survey_normalized ?? 0, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $h->skor_survey_normalized ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Final Score Header -->
                                    <div class="pt-5 mt-2 text-center">
                                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Skor Akhir Keseluruhan</p>
                                        <div class="text-5xl font-black text-[#0091d5]">
                                            {{ number_format($h->skor_akhir_voting, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Area -->
                                <div class="mt-6 pt-5 border-t border-gray-100">
                                    <form id="form-pilih-{{ $h->id }}" action="{{ route('kepala.review.pilih', $h->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-xs font-semibold text-gray-600 mb-2">Catatan Penetapan (Opsional)</label>
                                            <textarea name="catatan" rows="2" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#0091d5]/20 focus:border-[#0091d5] bg-gray-50 resize-none transition-all placeholder-gray-400" placeholder="Berikan ucapan selamat atau alasan penetapan kandidat ini..."></textarea>
                                        </div>
                                        <button type="button" onclick="openConfirmModal('{{ $h->id }}', '{{ addslashes($h->kandidat->pegawai->nama) }}')" class="w-full py-4 bg-gray-900 hover:bg-[#0091d5] text-white shadow-lg shadow-gray-900/20 font-bold rounded-xl transition-all hover:-translate-y-0.5 flex justify-center items-center group">
                                            <svg class="w-5 h-5 mr-2 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Tetapkan Sebagai Terbaik
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($kandidats->isEmpty())
                <div class="text-center py-16 text-gray-500 border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50 mt-6">
                    <p class="font-medium text-gray-600 text-lg">Data nominasi top 3 belum tersedia.</p>
                    <p class="text-sm mt-2">Silakan hubungi Administrator untuk memproses tahapan penilaian ini.</p>
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
    <div id="modalPanel" class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transition-all opacity-0 translate-y-8 w-full max-w-md mx-auto flex flex-col z-10 border border-gray-100">
        <div class="bg-white px-6 pt-8 pb-6">
            <div class="flex flex-col items-center text-center">
                <div class="flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-full bg-blue-50 mb-5 relative">
                    <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-50"></div>
                    <svg class="h-10 w-10 text-[#0091d5] relative z-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2" id="modal-title">
                    Konfirmasi Penetapan
                </h3>
                <p class="text-sm text-gray-500 leading-relaxed max-w-sm">
                    Anda akan menetapkan <br><strong id="modalKandidatName" class="text-[#0091d5] text-base mt-1 block"></strong>
                    sebagai Pegawai Terbaik.<br>
                    <span class="text-xs text-red-600 font-semibold mt-4 block bg-red-50 py-2 px-3 rounded-lg border border-red-100">Peringatan: Tindakan ini akan mengakhiri seluruh proses pemilihan dan tidak dapat dibatalkan.</span>
                </p>
            </div>
        </div>
        <div class="bg-gray-50/80 px-6 py-5 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-3">
            <button type="button" onclick="submitForm()" class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-md px-5 py-3.5 bg-[#0091d5] text-base font-bold text-white hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0091d5] transition-all transform hover:-translate-y-0.5">
                Ya, Tetapkan Sekarang
            </button>
            <button type="button" onclick="closeConfirmModal()" class="mt-3 sm:mt-0 w-full inline-flex justify-center items-center rounded-xl border border-gray-300 shadow-sm px-5 py-3.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
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

@extends(Auth::check() ? 'layouts.app' : 'layouts.guest')

@section('content')
@if(!Auth::check())
<main class="w-full max-w-7xl mx-auto flex-grow px-6 py-12 animate-fade-in relative z-10">
    <div class="mb-10 text-center flex flex-col items-center">
        <img alt="BPS Logo" class="h-12 md:h-16 w-auto mb-4 drop-shadow-sm" src="{{ asset('images/logo.svg') }}">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">FAQ / Bantuan</h1>
        <p class="text-gray-500 mt-2">
            Pertanyaan yang sering ditanyakan seputar Sistem Informasi Voting Terpadu
        </p>
        <a href="{{ route('beranda') }}" class="mt-4 text-bps-secondary hover:underline text-sm font-semibold flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
@endif

<div class="{{ Auth::check() ? 'mb-6 flex justify-between items-end' : 'hidden' }}">
    <div>
        <h1 class="text-2xl font-bold text-bps-text mb-1">Bantuan / FAQ</h1>
        <p class="text-sm text-gray-500">Kumpulan pertanyaan yang sering ditanyakan beserta jawabannya.</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm w-full">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($faqs as $item)
                <div onclick="openFaqModal('{{ htmlspecialchars($item->pertanyaan, ENT_QUOTES, 'UTF-8') }}', `{{ htmlspecialchars(nl2br(e($item->jawaban)), ENT_QUOTES, 'UTF-8') }}`)" class="bg-bps-bg p-5 rounded-lg border border-gray-100 hover:border-bps-secondary/50 hover:shadow-md cursor-pointer transition-all duration-200 flex flex-col h-full">
                    <h3 class="text-lg font-semibold text-bps-secondary mb-2">{{ $item->pertanyaan }}</h3>
                    <div class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                        {{ \Illuminate\Support\Str::limit($item->jawaban, 150) }}
                    </div>
                    @if(strlen($item->jawaban) > 150)
                    <div class="mt-auto pt-4 text-xs font-medium text-blue-600 flex items-center">
                        Baca selengkapnya
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                    @else
                    <div class="mt-auto pt-4"></div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-gray-500">
                    <p>Belum ada FAQ yang ditambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@if(!Auth::check())
</main>
@endif

<!-- Modal Detail FAQ -->
<div id="faqModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeFaqModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full relative">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeFaqModal()">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start w-full">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl leading-6 font-bold text-bps-secondary border-b pb-3 mb-4" id="modal-faq-pertanyaan">
                            <!-- Pertanyaan di sini -->
                        </h3>
                        <div class="mt-2 text-sm text-gray-700 leading-relaxed space-y-3" id="modal-faq-jawaban">
                            <!-- Jawaban di sini -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeFaqModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pindahkan modal ke akhir body agar terbebas dari constraint CSS parent
        const modal = document.getElementById('faqModal');
        if(modal && modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }
    });

    function openFaqModal(pertanyaan, jawabanHtml) {
        document.getElementById('modal-faq-pertanyaan').innerText = pertanyaan;
        
        // Buat div temporary untuk merender teks HTML yang di-decode
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = jawabanHtml;
        
        document.getElementById('modal-faq-jawaban').innerHTML = tempDiv.textContent || tempDiv.innerText || jawabanHtml;
        document.getElementById('faqModal').classList.remove('hidden');
    }

    function closeFaqModal() {
        document.getElementById('faqModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

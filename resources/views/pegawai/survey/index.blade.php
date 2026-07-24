@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Survey Penilaian Kandidat Pegawai Terbaik
</h2>
@endsection

@section('content')
<style>
.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    justify-content: center;
    align-items: center;
}

.star-rating input {
    display: none;
}

@keyframes starPop {
    0% { transform: scale(1); }
    40% { transform: scale(1.4); }
    100% { transform: scale(1); }
}

@keyframes starGlow {
    0% { filter: drop-shadow(0 0 0 rgba(251, 191, 36, 0)); }
    50% { filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.8)); }
    100% { filter: drop-shadow(0 0 3px rgba(251, 191, 36, 0.4)); }
}

.star-rating label {
    cursor: pointer;
    color: #d1d5db;
    transition: color 0.2s, transform 0.2s;
    padding: 2px;
}

.star-rating label svg {
    width: 28px;
    height: 28px;
    display: block;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Hover Effects */
.star-rating label:hover svg {
    transform: scale(1.2);
}

.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #fbbf24;
}

/* Selected State */
.star-rating input:checked ~ label {
    color: #f59e0b; 
}

/* Pop and Glow animation on the exact star that was clicked */
.star-rating input:checked + label svg {
    animation: starPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards,
               starGlow 0.4s ease-in-out forwards;
    color: #fbbf24;
}

/* Focus for accessibility */
.star-rating input:focus-visible + label svg {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
    border-radius: 9999px;
}

@keyframes popIn {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes pulseGlow {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
    70% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}
.success-card {
    animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}
.success-icon-wrapper {
    animation: pulseGlow 2s infinite;
}
</style>

<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if (session('info'))
            <div class="mb-4 rounded-md bg-blue-50 p-4 border border-blue-200">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($error))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-6 text-center text-gray-500">
                {{ $error }}
            </div>
        @elseif(isset($isVotingDitunda) && $isVotingDitunda)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-10 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Voting Ditunda Sementara</h3>
                <p class="text-gray-500">Pemilihan untuk periode <strong>{{ $periodeAktif->nama }}</strong> sedang ditunda karena Kepala Umum belum menyelesaikan kelengkapan data (Nilai CKP atau Absensi 3 bulan). Harap kembali lagi nanti.</p>
            </div>
        @elseif($sudahIsi)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-12 text-center success-card relative">
                <!-- Decorative background shapes -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-green-100 rounded-full opacity-50 blur-xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-blue-100 rounded-full opacity-50 blur-xl"></div>
                
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6 success-icon-wrapper relative z-10">
                    <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3 relative z-10">Terima Kasih!</h3>
                <p class="text-gray-500 text-lg relative z-10">Anda sudah menyelesaikan penilaian untuk periode <strong>{{ $periodeAktif->nama }}</strong>.</p>
                
                <div class="mt-8">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @elseif($kandidats->isEmpty())
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-6 text-center text-gray-500">
                Belum ada kandidat yang terpilih pada periode ini.
            </div>
        @else
            <!-- Header Informasi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
                <div class="p-4 border-b bg-bps-bg flex flex-col sm:flex-row sm:items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Periode: {{ $periodeAktif->nama }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Sistem akan menyimpan jawaban Anda secara otomatis di memori browser.</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-[#0091d5]">
                            Total Pertanyaan: {{ count($pertanyaans) }}
                        </span>
                    </div>
                </div>
            </div>

            <form id="surveyForm" action="{{ route('pegawai.survey.store') }}" method="POST">
                @csrf
                
                @foreach($pertanyaans as $index => $p)
                <div class="step-section" id="step-{{ $loop->iteration }}" style="{{ $loop->iteration == 1 ? '' : 'display: none;' }} transition: opacity 0.3s ease;">
                    <div class="bg-white shadow-sm sm:rounded-lg border mb-6">
                        
                        <div class="sticky md:relative -top-4 md:top-auto z-40 shadow-sm bg-white/95 backdrop-blur-sm border-b border-gray-200 sm:rounded-t-lg">
                            <div class="p-3 md:p-6 bg-sky-50/80 flex flex-col md:flex-row md:items-start justify-between sm:rounded-t-lg">
                                <div class="flex items-start">
                                    <span class="inline-flex items-center justify-center h-6 w-6 md:h-8 md:w-8 rounded-full bg-[#0091d5] text-white text-xs md:text-sm font-bold mr-3 md:mr-4 mt-0.5 shadow shrink-0">{{ $loop->iteration }}</span>
                                    <div>
                                        <div class="hidden md:block font-bold text-[#0091d5] text-xs md:text-sm tracking-wider uppercase mb-1">{{ $p->kategori }}</div>
                                        <div class="text-gray-900 text-sm md:text-lg font-medium leading-snug max-h-[20vh] overflow-y-auto pr-2">{{ $p->pertanyaan }}</div>
                                    </div>
                                </div>
                                <div class="hidden md:block mt-4 md:mt-0 whitespace-nowrap text-right">
                                    <span class="text-xs md:text-sm font-bold text-gray-500">Pertanyaan {{ $loop->iteration }} dari {{ count($pertanyaans) }}</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar per Step -->
                            <div class="w-full bg-gray-200 h-1.5">
                                <div class="main-progress-bar bg-[#0091d5] h-1.5 transition-all duration-300 ease-in-out" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Responsive Table/Card layout -->
                            <div class="w-full">
                                <table class="w-full text-left border-collapse rounded-lg overflow-hidden block md:table">
                                    <thead class="hidden md:table-header-group">
                                        <tr class="bg-gray-100 border-b">
                                            <th class="p-4 font-semibold text-sm w-1/2 text-gray-700">Nama Kandidat</th>
                                            <th class="p-4 font-semibold text-sm w-1/2 text-center text-gray-700">Penilaian (1-5)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="block md:table-row-group space-y-4 md:space-y-0 p-1 md:p-0">
                                        @foreach($kandidats as $k)
                                        <tr class="block md:table-row border border-gray-200 md:border-none md:border-b hover:bg-sky-50 transition-colors bg-white rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
                                            <td class="block md:table-cell p-0 md:p-4 text-sm md:border-r border-gray-200 mb-4 md:mb-0 flex justify-center md:justify-start">
                                                <div class="flex items-center space-x-3 w-full">
                                                    <div class="flex-shrink-0 w-12 h-12 md:w-10 md:h-10">
                                                        <img class="w-12 h-12 md:w-10 md:h-10 rounded-full object-cover border border-gray-200" src="{{ $k->pegawai->foto_profil_url }}" alt="{{ $k->pegawai->nama }}">
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900 text-sm md:text-base">{{ $k->pegawai->nama }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">{{ $k->pegawai->jabatan }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="block md:table-cell p-0 md:p-4 text-center align-middle border-t border-gray-100 md:border-t-0 pt-4 md:pt-0">
                                                <div class="star-rating transform scale-125 md:scale-100 mt-2 md:mt-0">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input
                                                            type="radio"
                                                            id="star-{{ $p->id }}-{{ $k->id }}-{{ $i }}"
                                                            name="jawaban[{{ $p->id }}][{{ $k->id }}]"
                                                            value="{{ $i }}"
                                                            required
                                                            onchange="saveToLocal('{{ $p->id }}', '{{ $k->id }}', '{{ $i }}')"
                                                            {{ !in_array(Auth::user()->role->tipe, ['Pegawai', 'Kepala Umum', 'Kepala_Umum']) ? 'disabled' : '' }}
                                                        >
                                                        <label
                                                            for="star-{{ $p->id }}-{{ $k->id }}-{{ $i }}"
                                                            title="{{ $i }} Bintang"
                                                        >
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        </label>
                                                    @endfor
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Navigation -->
                    <div class="flex flex-col-reverse md:flex-row justify-between items-center mt-6 mb-10 gap-4">
                        <div class="w-full md:w-auto">
                            @if(!$loop->first)
                            <button type="button" onclick="changeStep({{ $loop->iteration - 1 }})" class="w-full md:w-auto justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-bps-bg shadow-sm transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Sebelumnya
                            </button>
                            @endif
                        </div>
                        <div class="flex w-full md:w-auto space-x-0 md:space-x-4 flex-col md:flex-row gap-4 md:gap-0">
                            @if(!$loop->last)
                            <button type="button" onclick="changeStep({{ $loop->iteration + 1 }})" class="w-full md:w-auto justify-center px-6 py-3 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 shadow-sm transition-colors flex items-center">
                                Selanjutnya
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                            @endif

                            @if($loop->last)
                                @if(in_array(Auth::user()->role->tipe, ['Pegawai', 'Kepala Umum', 'Kepala_Umum']))
                                <button type="submit" id="submitBtn" class="w-full md:w-auto justify-center px-8 py-3 bg-[#0091d5] text-white font-bold rounded-md hover:bg-bps-secondary shadow-md transition-colors flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Kirim Semua Penilaian
                                </button>
                                @else
                                <button type="button" disabled class="w-full md:w-auto justify-center px-8 py-3 bg-gray-400 text-white font-bold rounded-md cursor-not-allowed shadow-md">
                                    Mode Read-Only
                                </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
            </form>
        @endif

    </div>
</div>

@if(isset($kandidats) && !$kandidats->isEmpty() && !$sudahIsi)
<script>
    let currentStep = 1;
    const totalSteps = {{ count($pertanyaans) }};
    const storagePrefix = 'survey_draft_periode_{{ $periodeAktif->id }}_';

    function saveToLocal(pertanyaanId, kandidatId, nilai) {
        const key = storagePrefix + pertanyaanId + '_' + kandidatId;
        localStorage.setItem(key, nilai);
    }

    function loadFromLocal() {
        const radios = document.querySelectorAll('input[type="radio"]');
        radios.forEach(radio => {
            const match = radio.name.match(/jawaban\[([^\]]+)\]\[([^\]]+)\]/);
            if(match) {
                const pId = match[1];
                const kId = match[2];
                const savedVal = localStorage.getItem(storagePrefix + pId + '_' + kId);
                
                if (savedVal && radio.value === savedVal) {
                    radio.checked = true;
                }
            }
        });
    }

    function clearLocalDrafts() {
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(storagePrefix)) {
                keysToRemove.push(key);
            }
        }
        keysToRemove.forEach(k => localStorage.removeItem(k));
    }

    function updateUI() {
        for(let i = 1; i <= totalSteps; i++) {
            let stepDiv = document.getElementById('step-' + i);
            if(stepDiv) stepDiv.style.display = (i === currentStep) ? 'block' : 'none';
        }

        const progress = (currentStep / totalSteps) * 100;
        document.querySelectorAll('.main-progress-bar').forEach(el => {
            el.style.width = progress + '%';
        });
    }

    const isReadOnly = {{ !in_array(Auth::user()->role->tipe, ['Pegawai', 'Kepala Umum', 'Kepala_Umum']) ? 'true' : 'false' }};

    function validateStep(step) {
        if (isReadOnly) return true;

        const stepDiv = document.getElementById('step-' + step);
        if(!stepDiv) return true;

        const radios = stepDiv.querySelectorAll('input[type="radio"]');
        const names = new Set();
        radios.forEach(r => names.add(r.name));

        for(let name of names) {
            const checked = stepDiv.querySelector(`input[name="${name}"]:checked`);
            if(!checked) {
                return false;
            }
        }
        return true;
    }

    function changeStep(newStep) {
        if(newStep > currentStep) {
            if(!validateStep(currentStep)) {
                showAlertModal('Silakan lengkapi penilaian untuk semua kandidat pada pertanyaan ini sebelum melanjutkan.');
                return;
            }
        }
        
        if(newStep >= 1 && newStep <= totalSteps) {
            currentStep = newStep;
            localStorage.setItem(storagePrefix + 'currentStep', currentStep);
            updateUI();
            window.scrollTo(0, 0);
        }
    }

    // Restore step and UI immediately (elements are already parsed)
    const savedStep = localStorage.getItem(storagePrefix + 'currentStep');
    if (savedStep) {
        const parsedStep = parseInt(savedStep, 10);
        if (parsedStep >= 1 && parsedStep <= totalSteps) {
            currentStep = parsedStep;
        }
    }

    loadFromLocal();
    updateUI();

    document.addEventListener('DOMContentLoaded', () => {
        // Attach event listener to the form to capture changes via event delegation
        document.getElementById('surveyForm').addEventListener('change', function(e) {
            if (e.target && e.target.type === 'radio') {
                const match = e.target.name.match(/jawaban\[([^\]]+)\]\[([^\]]+)\]/);
                if(match) {
                    saveToLocal(match[1], match[2], e.target.value);
                }
            }
        });

        // Also add click event just in case some browsers don't bubble change events for hidden radios
        document.getElementById('surveyForm').addEventListener('click', function(e) {
            if (e.target && e.target.type === 'radio') {
                const match = e.target.name.match(/jawaban\[([^\]]+)\]\[([^\]]+)\]/);
                if(match) {
                    saveToLocal(match[1], match[2], e.target.value);
                }
            }
        });

        document.getElementById('surveyForm').addEventListener('submit', function(e) {
            if(!validateStep(currentStep)) {
                e.preventDefault();
                showAlertModal('Silakan lengkapi penilaian terakhir sebelum mengirim.');
            } else {
                clearLocalDrafts();
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
                }
            }
        });
    });
</script>

@push('modals')
<!-- Alert Modal -->
<div id="alertModal" class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300" style="background-color: rgba(17, 24, 39, 0.5);">
    <div class="bg-white rounded-xl shadow-xl relative overflow-hidden transition-transform duration-300" style="width: 90%; max-width: 400px; transform: scale(0.95);">
        <div class="bg-red-500 h-2 w-full absolute top-0 left-0"></div>
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Penilaian Belum Lengkap</h3>
            <p id="alertModalMessage" class="text-sm text-gray-500 mb-6">Silakan lengkapi penilaian untuk semua kandidat pada pertanyaan ini sebelum melanjutkan.</p>
            <button type="button" onclick="closeAlertModal()" class="w-full bg-[#0091d5] hover:bg-sky-600 text-white font-semibold py-2.5 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                Mengerti
            </button>
        </div>
    </div>
</div>
@endpush

<script>
    function showAlertModal(message) {
        const modal = document.getElementById('alertModal');
        const modalContent = modal.querySelector('.bg-white');
        document.getElementById('alertModalMessage').textContent = message;
        
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modalContent.style.transform = 'scale(1)';
    }

    function closeAlertModal() {
        const modal = document.getElementById('alertModal');
        const modalContent = modal.querySelector('.bg-white');
        
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalContent.style.transform = 'scale(0.95)';
    }
</script>
@endif
@endsection

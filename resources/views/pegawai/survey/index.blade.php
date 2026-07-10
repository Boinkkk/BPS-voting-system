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

.star-rating label {
    cursor: pointer;
    color: #d1d5db;
    transition: color .2s;
    padding: 2px;
}

.star-rating label svg {
    width: 28px;
    height: 28px;
    display: block;
}

/* Hover */
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #fbbf24;
}

/* Setelah dipilih */
.star-rating input:checked ~ label {
    color: #fbbf24;
}

/* Focus */
.star-rating input:focus-visible + label svg {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
    border-radius: 9999px;
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
        @elseif($sudahIsi)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-10 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Terima Kasih!</h3>
                <p class="text-gray-500">Anda sudah menyelesaikan penilaian untuk periode <strong>{{ $periodeAktif->nama }}</strong>.</p>
            </div>
        @elseif($kandidats->isEmpty())
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-6 text-center text-gray-500">
                Belum ada kandidat yang terpilih pada periode ini.
            </div>
        @else
            <!-- Header Informasi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
                <div class="p-4 border-b bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between">
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
                
                <!-- Progress Bar Utama -->
                <div class="w-full bg-gray-200 h-2">
                    <div id="main-progress-bar" class="bg-[#0091d5] h-2 transition-all duration-300 ease-in-out" style="width: 0%"></div>
                </div>
            </div>

            <form id="surveyForm" action="{{ route('pegawai.survey.store') }}" method="POST">
                @csrf
                
                @foreach($pertanyaans as $index => $p)
                <div class="step-section" id="step-{{ $loop->iteration }}" style="display: none; transition: opacity 0.3s ease;">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
                        
                        <div class="p-6 border-b bg-sky-50 flex flex-col md:flex-row md:items-start justify-between">
                            <div class="flex items-start">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-[#0091d5] text-white text-sm font-bold mr-4 mt-0.5 shadow">{{ $loop->iteration }}</span>
                                <div>
                                    <div class="font-bold text-[#0091d5] text-sm tracking-wider uppercase mb-1">{{ $p->kategori }}</div>
                                    <div class="text-gray-900 text-lg font-medium">{{ $p->pertanyaan }}</div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-gray-500">Pertanyaan {{ $loop->iteration }} dari {{ count($pertanyaans) }}</span>
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
                                                            {{ Auth::user()->role->tipe !== 'Pegawai' ? 'disabled' : '' }}
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
                            <button type="button" onclick="changeStep({{ $loop->iteration - 1 }})" class="w-full md:w-auto justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 shadow-sm transition-colors flex items-center">
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
                                @if(Auth::user()->role->tipe === 'Pegawai')
                                <button type="submit" class="w-full md:w-auto justify-center px-8 py-3 bg-[#0091d5] text-white font-bold rounded-md hover:bg-blue-600 shadow-md transition-colors flex items-center">
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
        document.getElementById('main-progress-bar').style.width = progress + '%';
    }

    function validateStep(step) {
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
                alert('Silakan lengkapi penilaian untuk semua kandidat pada pertanyaan ini sebelum melanjutkan.');
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

    document.addEventListener('DOMContentLoaded', () => {
        // Restore step
        const savedStep = localStorage.getItem(storagePrefix + 'currentStep');
        if (savedStep) {
            const parsedStep = parseInt(savedStep, 10);
            if (parsedStep >= 1 && parsedStep <= totalSteps) {
                currentStep = parsedStep;
            }
        }

        loadFromLocal();
        updateUI();

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
                alert('Silakan lengkapi penilaian terakhir sebelum mengirim.');
            } else {
                clearLocalDrafts();
            }
        });
    });
</script>
@endif
@endsection

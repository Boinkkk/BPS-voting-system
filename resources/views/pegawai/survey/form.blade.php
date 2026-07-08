@extends('layouts.app')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('pegawai.survey.index') }}" class="text-gray-500 hover:text-gray-700">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
    </a>
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Survey Kandidat: {{ $kandidat->pegawai->nama }}
    </h2>
</div>
@endsection

@section('content')
<style>
.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
}
.star-rating input {
    display: none;
}
.star-rating label {
    cursor: pointer;
    color: #d1d5db; /* Tailwind gray-300 */
    transition: color 0.2s;
}
.star-rating input:checked ~ label,
.star-rating input:checked,
.star-rating label:hover ~ label,
.star-rating label:hover {
    color: #fbbf24; /* Tailwind yellow-400 */
}
/* Focus state for accessibility */
.star-rating input:focus-visible + svg {
    outline: 2px solid #3b82f6;
    border-radius: 9999px;
}
</style>

<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
            <div class="p-4 bg-sky-50 border-b border-sky-100 flex items-start space-x-4">
                <div class="flex-shrink-0 mt-1">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-[#0091d5]">
                        <span class="font-medium text-white leading-none">{{ substr($kandidat->pegawai->nama, 0, 1) }}</span>
                    </span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $kandidat->pegawai->nama }}</h3>
                    <p class="text-sm text-gray-600">{{ $kandidat->pegawai->jabatan }} | NIP: {{ $kandidat->pegawai->nip }}</p>
                </div>
            </div>
        </div>

        <form id="surveyForm" action="{{ route('pegawai.survey.store', $kandidat->id) }}" method="POST">
            @csrf
            
            <!-- Progress Bar -->
            <div class="mb-6 flex space-x-2">
                @foreach($pertanyaans as $grupKategori => $pertanyaanGroup)
                <div class="flex-1">
                    <div class="h-2 rounded-full step-bar" id="step-bar-{{ $loop->iteration }}" 
                         class="bg-gray-200"></div>
                    <div class="text-xs font-medium mt-2 text-center step-label text-gray-500" id="step-label-{{ $loop->iteration }}">
                        Tahap {{ $loop->iteration }}: {{ $grupKategori }}
                    </div>
                </div>
                @endforeach
            </div>

            @foreach($pertanyaans as $grupKategori => $pertanyaanGroup)
            <div class="step-section" id="step-{{ $loop->iteration }}" style="display: none; transition: opacity 0.3s ease;">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Kuesioner: {{ $grupKategori }}</h3>
                        <div class="text-sm font-medium text-gray-500">Pilih 1 (Sangat Tidak Setuju) hingga 5 (Sangat Setuju) Bintang</div>
                    </div>
                    
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-4 font-semibold text-sm w-12 text-center">No</th>
                                    <th class="p-4 font-semibold text-sm w-2/3 min-w-[300px]">Aspek Penilaian</th>
                                    <th class="p-4 font-semibold text-sm text-center w-1/3">Penilaian (Bintang)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pertanyaanGroup as $index => $p)
                                    <tr class="border-b hover:bg-gray-50 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="p-4 text-sm text-center font-medium">{{ $p->nomor_urut }}</td>
                                        <td class="p-4 text-sm">
                                            <div class="font-bold text-[#0091d5] mb-1">{{ $p->kategori }}</div>
                                            <div class="text-gray-600">{{ $p->pertanyaan }}</div>
                                        </td>
                                        <td class="p-4 text-center align-middle">
                                            <div class="star-rating">
                                                @for($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="star-{{ $p->id }}-{{ $i }}" name="jawaban[{{ $p->id }}]" value="{{ $i }}" required {{ Auth::user()->role->tipe !== 'Pegawai' ? 'disabled' : '' }}>
                                                    <label for="star-{{ $p->id }}-{{ $i }}" title="{{ $i }} Bintang">
                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
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
                
                <div class="flex justify-between items-center">
                    <div>
                        <a href="{{ route('pegawai.survey.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Batal & Kembali</a>
                    </div>
                    <div class="flex space-x-4">
                        @if(!$loop->first)
                        <button type="button" onclick="changeStep({{ $loop->iteration - 1 }})" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 shadow-sm transition-colors">
                            Kembali
                        </button>
                        @endif

                        @if(!$loop->last)
                        <button type="button" onclick="changeStep({{ $loop->iteration + 1 }})" class="px-6 py-3 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 shadow-sm transition-colors">
                            Selanjutnya
                        </button>
                        @endif

                        @if($loop->last)
                            @if(Auth::user()->role->tipe === 'Pegawai')
                            <button type="submit" class="px-6 py-3 bg-[#0091d5] text-white font-medium rounded-md hover:bg-blue-600 shadow-sm transition-colors">
                                Simpan Penilaian
                            </button>
                            @else
                            <button type="button" disabled class="px-6 py-3 bg-gray-400 text-white font-medium rounded-md cursor-not-allowed shadow-sm">
                                Mode Read-Only
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            
        </form>

    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = {{ count($pertanyaans) }};

    function updateUI() {
        for(let i = 1; i <= totalSteps; i++) {
            document.getElementById('step-' + i).style.display = (i === currentStep) ? 'block' : 'none';
            
            let bar = document.getElementById('step-bar-' + i);
            let label = document.getElementById('step-label-' + i);
            if(bar) {
                if(currentStep >= i) {
                    bar.className = 'h-2 rounded-full bg-[#0091d5]';
                    label.className = 'text-xs font-medium mt-2 text-center text-[#0091d5]';
                } else {
                    bar.className = 'h-2 rounded-full bg-gray-200';
                    label.className = 'text-xs font-medium mt-2 text-center text-gray-500';
                }
            }
        }
    }

    function changeStep(newStep) {
        if(newStep >= 1 && newStep <= totalSteps) {
            currentStep = newStep;
            updateUI();
            window.scrollTo(0, 0);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateUI();
    });
</script>
@endsection


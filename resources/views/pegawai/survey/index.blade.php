@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Survey Kandidat Pegawai Terbaik
</h2>
@endsection

@section('content')
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

        @if(isset($error))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-6 text-center text-gray-500">
                {{ $error }}
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                <div class="p-6 border-b bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Periode: {{ $periodeAktif->nama }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Silakan berikan penilaian objektif (Skala 1-5) untuk masing-masing kandidat di bawah ini berdasarkan Core Values BerAKHLAK.</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($kandidats as $k)
                            @php
                                $isDone = in_array($k->id, $jawabanSelesai);
                            @endphp
                            
                            <div class="border rounded-lg p-4 {{ $isDone ? 'bg-gray-50 border-gray-200' : 'bg-white border-sky-200 shadow-sm hover:shadow-md transition-shadow' }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $k->pegawai->nama }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">{{ $k->pegawai->jabatan }}</p>
                                    </div>
                                    @if($isDone)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Belum Dinilai
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mt-4">
                                    @if($isDone)
                                        <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-600 text-sm font-medium rounded-md cursor-not-allowed">
                                            Sudah Disurvey
                                        </button>
                                    @else
                                        <a href="{{ route('pegawai.survey.show', $k->id) }}" class="block w-full text-center px-4 py-2 bg-[#0091d5] text-white text-sm font-medium rounded-md hover:bg-blue-600">
                                            Mulai Survey
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($kandidats->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            Belum ada kandidat yang terpilih pada periode ini.
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

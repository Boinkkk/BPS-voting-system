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

        <form action="{{ route('pegawai.survey.store', $kandidat->id) }}" method="POST">
            @csrf
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Kuesioner Core Values BerAKHLAK</h3>
                    <div class="text-sm font-medium text-gray-500">Skala 1 (Sangat Tidak Setuju) - 5 (Sangat Setuju)</div>
                </div>
                
                <div class="p-0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-4 font-semibold text-sm w-12 text-center">No</th>
                                <th class="p-4 font-semibold text-sm w-1/3">Aspek Penilaian</th>
                                <th class="p-4 font-semibold text-sm text-center">1</th>
                                <th class="p-4 font-semibold text-sm text-center">2</th>
                                <th class="p-4 font-semibold text-sm text-center">3</th>
                                <th class="p-4 font-semibold text-sm text-center">4</th>
                                <th class="p-4 font-semibold text-sm text-center">5</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pertanyaans as $index => $p)
                                <tr class="border-b hover:bg-gray-50 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    <td class="p-4 text-sm text-center font-medium">{{ $p->nomor_urut }}</td>
                                    <td class="p-4 text-sm">
                                        <div class="font-bold text-[#0091d5] mb-1">{{ $p->kategori }}</div>
                                        <div class="text-gray-600">{{ $p->pertanyaan }}</div>
                                    </td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <td class="p-4 text-center align-middle">
                                            <input type="radio" name="jawaban[{{ $p->id }}]" value="{{ $i }}" required 
                                                class="w-5 h-5 text-[#0091d5] focus:ring-sky-500 border-gray-300"
                                                {{ Auth::user()->role->tipe !== 'Pegawai' ? 'disabled' : '' }}>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="{{ route('pegawai.survey.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 shadow-sm">
                    Batal
                </a>
                @if(Auth::user()->role->tipe === 'Pegawai')
                <button type="submit" class="px-6 py-3 bg-[#0091d5] text-white font-medium rounded-md hover:bg-blue-600 shadow-sm">
                    Simpan Penilaian
                </button>
                @else
                <button type="button" disabled class="px-6 py-3 bg-gray-400 text-white font-medium rounded-md cursor-not-allowed shadow-sm">
                    Mode Read-Only
                </button>
                @endif
            </div>
            
        </form>

    </div>
</div>
@endsection

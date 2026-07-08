@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Kandidat Terbaik (10 Besar)
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

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
            <!-- Header & Filter -->
            <div class="p-6 bg-white border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Kandidat Sistem</h3>
                    <p class="text-sm text-gray-500">Menampilkan 10 pegawai dengan skor kinerja tertinggi pada periode yang dipilih.</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <form action="{{ route('admin.kandidat.index') }}" method="GET" class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Filter Periode:</label>
                        <select name="periode_id" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin')
                    <form action="{{ route('admin.kandidat.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ $periode_id }}">
                        <button type="submit" class="px-4 py-2 bg-[#0091d5] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="return confirm('Proses ini akan mengkalkulasi ulang seluruh skor akhir pegawai berdasarkan nilai kinerja dan absen pada periode terpilih, lalu menimpa data 10 kandidat sebelumnya. Lanjutkan?')">
                            Kalkulasi 10 Kandidat
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b">
                            <th class="p-4 font-semibold text-sm text-center w-16">Peringkat</th>
                            <th class="p-4 font-semibold text-sm">Nama Pegawai</th>
                            <th class="p-4 font-semibold text-sm">Jabatan</th>
                            <th class="p-4 font-semibold text-sm text-center">Skor Akhir (Keseluruhan)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kandidats as $k)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-4 text-center font-bold text-lg {{ $k->ranking_sistem <= 3 ? 'text-amber-500' : 'text-gray-700' }}">
                                    #{{ $k->ranking_sistem }}
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-gray-900">{{ $k->pegawai->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $k->pegawai->nip }}</div>
                                </td>
                                <td class="p-4 text-sm text-gray-600">{{ $k->pegawai->jabatan }}</td>
                                <td class="p-4 text-center font-bold text-[#0091d5] text-lg">{{ number_format($k->skor, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500">
                                    <p class="mb-2">Belum ada kandidat yang dihasilkan untuk periode ini.</p>
                                    <p class="text-sm">Silakan klik tombol <strong>"Kalkulasi 10 Kandidat"</strong> di kanan atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

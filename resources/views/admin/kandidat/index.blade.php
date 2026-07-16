@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Kandidat Terbaik ({{ isset($is_fase_2_selesai) && $is_fase_2_selesai ? '3' : '10' }} Besar)
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0">
            <!-- Header & Filter -->
            <div class="p-6 bg-white border-b border-gray-200 flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Kandidat Sistem</h3>
                    <p class="text-sm text-gray-500">
                        @if(isset($is_fase_2_selesai) && $is_fase_2_selesai)
                            3 pegawai terbaik berdasarkan hasil voting survei pada periode yang dipilih.
                        @else
                            10 pegawai dengan skor tertinggi berdasarkan Nilai CKP dan Absensi pada periode yang dipilih.
                        @endif
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row  items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 w-full lg:w-auto">
                    <form action="{{ route('admin.kandidat.index') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                        <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Periode:</label>
                        <select name="periode_id" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 w-full sm:w-auto">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if(Auth::user() && Auth::user()->role && Auth::user()->role->tipe == 'Admin')
                        @php
                            $selectedPeriode = $periodes->firstWhere('id', $periode_id);
                            $isPenginputan = $selectedPeriode && $selectedPeriode->status == 'penginputan';
                            $isReviewKepala = $selectedPeriode && $selectedPeriode->status == 'review_kepala';
                        @endphp
                        
                        @if($isReviewKepala)
                            <form action="{{ route('admin.kandidat.generateTop3') }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <input type="hidden" name="periode_id" value="{{ $periode_id }}">
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-amber-500 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" onclick="return confirm('Proses ini akan mengkalkulasi ulang 3 Kandidat Terbaik berdasarkan Nilai CKP, Absensi, dan Hasil Survei pada periode ini, lalu menimpa data 3 besar sebelumnya. Lanjutkan?')">
                                    Kalkulasi Ulang 3 Terbaik
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.kandidat.generate') }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <input type="hidden" name="periode_id" value="{{ $periode_id }}">
                                @if($isPenginputan)
                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-[#0091d5] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-bps-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-secondary" onclick="return confirm('Proses ini akan mengkalkulasi ulang seluruh skor akhir pegawai berdasarkan Nilai CKP dan Absensi pada periode terpilih, lalu menimpa data 10 kandidat sebelumnya. Lanjutkan?')">
                                        Kalkulasi 10 Kandidat
                                    </button>
                                @else
                                    <button type="button" class="w-full sm:w-auto px-4 py-2 bg-gray-400 border border-transparent rounded-md shadow-sm text-sm font-medium text-white cursor-not-allowed" title="Kalkulasi ulang kandidat hanya dapat dilakukan pada masa penginputan atau review kepala.">
                                        Kalkulasi Kandidat
                                    </button>
                                @endif
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Data List -->
            <div>
                <!-- Mobile Card View -->
<div class="block md:hidden space-y-4">
    @forelse($kandidats as $k)

        @php
            $rankingColor = match($k->ranking_sistem){
                1 => 'bg-yellow-100 text-yellow-700',
                2 => 'bg-gray-100 text-gray-700',
                3 => 'bg-orange-100 text-orange-700',
                default => 'bg-slate-100 text-slate-600'
            };
        @endphp

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-5 transition hover:shadow-md">

            <!-- Header -->
            <div class="flex items-start justify-between">

                <div class="flex gap-3">

                    <!-- Ranking -->
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-full font-bold {{ $rankingColor }}">
                        {{ $k->ranking_sistem }}
                    </span>

                    <!-- Avatar -->
                    @if($k->pegawai->foto_profil)
                        <img
                            src="{{ $k->pegawai->foto_profil_url }}"
                            class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-200"
                            alt="{{ $k->pegawai->nama }}">
                    @else
                        <div class="w-12 h-12 rounded-full bg-[#0091d5] text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($k->pegawai->nama,0,1)) }}
                        </div>
                    @endif

                    <!-- Nama -->
                    <div>

                        <div class="font-semibold text-slate-800 leading-tight">
                            {{ $k->pegawai->nama }}
                        </div>

                        <div class="text-sm text-slate-500">
                            {{ $k->pegawai->nip }}
                        </div>

                        <span class="inline-flex mt-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-[#0091d5]">
                            {{ $k->pegawai->jabatan }}
                        </span>

                    </div>

                </div>

                <!-- Skor Akhir -->
                <div class="text-right">

                    <div class="text-[10px] uppercase tracking-wider text-slate-400">
                        Skor Akhir
                    </div>

                    <span class="inline-flex mt-1 rounded-full bg-green-100 px-3 py-1 text-base font-bold text-green-700">

                        @if(isset($is_fase_2_selesai) && $is_fase_2_selesai)
                            {{ number_format($k->skor_akhir_voting ?? $k->skor,2,',','.') }}
                        @else
                            {{ number_format($k->skor,2,',','.') }}
                        @endif

                    </span>

                </div>

            </div>

            <!-- Divider -->
            <div class="my-4 border-t border-slate-100"></div>

            <!-- Statistik -->
            <div class="grid grid-cols-3 gap-3">

                <div class="rounded-xl bg-slate-50 p-3 text-center">
                    <div class="text-[11px] uppercase tracking-wide text-slate-500">
                        CKP
                    </div>

                    <div class="mt-1 font-bold text-slate-700">
                        {{ number_format($k->skor_ckp,2,',','.') }}
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 text-center">
                    <div class="text-[11px] uppercase tracking-wide text-slate-500">
                        Absensi
                    </div>

                    <div class="mt-1 font-bold text-slate-700">
                        {{ number_format($k->skor_absensi,2,',','.') }}
                    </div>
                </div>

                @if(isset($is_fase_2_selesai) && $is_fase_2_selesai)

                    <div class="rounded-xl bg-slate-50 p-3 text-center">
                        <div class="text-[11px] uppercase tracking-wide text-slate-500">
                            Survei
                        </div>

                        <div class="mt-1 font-bold text-slate-700">
                            {{ number_format($k->skor_survey ?? 0,2,',','.') }}
                        </div>
                    </div>

                @else

                    <div class="rounded-xl bg-slate-50 p-3 flex items-center justify-center">
                        <span class="text-slate-400 text-sm">
                            —
                        </span>
                    </div>

                @endif

            </div>

        </div>

    @empty

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 p-10 text-center">

            <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-4xl">
                📋
            </div>

            <h3 class="mt-4 font-semibold text-slate-700">
                Belum Ada Kandidat
            </h3>

            <p class="mt-2 text-sm text-slate-500">
                Silakan klik tombol
                <span class="font-semibold text-[#0091d5]">
                    Kalkulasi Kandidat
                </span>
                untuk menghasilkan peringkat pegawai.
            </p>

        </div>

    @endforelse
</div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto rounded-2xl bg-white shadow-lg ring-1 ring-gray-200">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Peringkat
                </th>
                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Pegawai
                </th>
                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Jabatan
                </th>
                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">
                    CKP
                </th>
                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Absensi
                </th>

                @if(isset($is_fase_2_selesai) && $is_fase_2_selesai)
                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">
                        Survei
                    </th>
                @endif

                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Skor Akhir
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">

            @forelse($kandidats as $k)

                <tr class="hover:bg-slate-50 transition-colors duration-200">

                    {{-- Ranking --}}
                    <td class="px-6 py-5 text-center">
                        @php
                            $badge = match($k->ranking_sistem){
                                1 => 'bg-yellow-100 text-yellow-700',
                                2 => 'bg-gray-100 text-gray-700',
                                3 => 'bg-orange-100 text-orange-700',
                                default => 'bg-slate-100 text-slate-600'
                            };
                        @endphp

                        <span class="inline-flex items-center justify-center w-11 h-11 rounded-full font-bold {{ $badge }}">
                            {{ $k->ranking_sistem }}
                        </span>
                    </td>

                    {{-- Pegawai --}}
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">

                            @if($k->pegawai->foto_profil)
                                <img
                                    src="{{ $k->pegawai->foto_profil_url }}"
                                    alt="{{ $k->pegawai->nama }}"
                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-[#0091d5] text-white flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($k->pegawai->nama,0,1)) }}
                                </div>
                            @endif

                            <div>
                                <div class="font-semibold text-slate-800">
                                    {{ $k->pegawai->nama }}
                                </div>

                                <div class="text-sm text-slate-500">
                                    {{ $k->pegawai->nip }}
                                </div>
                            </div>

                        </div>
                    </td>

                    {{-- Jabatan --}}
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-[#0091d5]">
                            {{ $k->pegawai->jabatan }}
                        </span>
                    </td>

                    {{-- CKP --}}
                    <td class="px-6 py-5 text-center">
                        <span class="font-semibold text-slate-700">
                            {{ number_format($k->skor_ckp,2,',','.') }}
                        </span>
                    </td>

                    {{-- Absensi --}}
                    <td class="px-6 py-5 text-center">
                        <span class="font-semibold text-slate-700">
                            {{ number_format($k->skor_absensi,2,',','.') }}
                        </span>
                    </td>

                    {{-- Survey --}}
                    @if(isset($is_fase_2_selesai) && $is_fase_2_selesai)

                        <td class="px-6 py-5 text-center">
                            <span class="font-semibold text-slate-700">
                                {{ number_format($k->skor_survey ?? 0,2,',','.') }}
                            </span>
                        </td>

                        {{-- Skor Akhir --}}
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 font-bold text-green-700">
                                {{ number_format($k->skor_akhir_voting ?? $k->skor,2,',','.') }}
                            </span>
                        </td>

                    @else

                        {{-- Skor Akhir --}}
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 font-bold text-green-700">
                                {{ number_format($k->skor,2,',','.') }}
                            </span>
                        </td>

                    @endif

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="px-6 py-14 text-center">

                        <div class="flex flex-col items-center">

                            <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4 text-4xl">
                                📋
                            </div>

                            <h3 class="font-semibold text-slate-700 text-lg">
                                Belum Ada Kandidat . . .
                            </h3>

                        </div>

                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>
</div>
            </div>
        </div>

    </div>
</div>
@endsection

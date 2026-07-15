@extends('layouts.app')

@section('header')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Rekap Absensi Bulanan Pegawai
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

        @if ($errors->any() || session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm text-red-700 list-disc list-inside">
                            @if(session('error')) <li>{{ session('error') }}</li> @endif
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0 mb-6">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Upload Data Rekap Absensi</h3>
                    <p class="text-sm text-gray-500">Unggah file excel berisi rekap absen per bulan.</p>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="openManualModal()" class="px-4 py-2 bg-biru text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Input Data Absensi
                    </button>
                    <a href="{{ route('admin.absensi.template') }}" class="px-4 py-2 bg-hijau text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                        Download Format Absensi
                    </a>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.absensi.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
                    @csrf
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Penilaian</label>
                        <select name="periode_id" id="upload_periode" required class="w-full text-sm border-gray-300 rounded-md">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="bulan" id="upload_bulan" required class="w-full text-sm border-gray-300 rounded-md">
                            @php
                                $namaB = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                            @endphp
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $namaB[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx,.xls" required class="w-full text-sm border border-gray-300 rounded-md p-1.5">
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full px-4 py-2.5 bg-orange text-white text-sm font-medium rounded-md hover:bg-orange-600">
                            Upload Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Input Manual -->
        <div id="manualInputModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeManualModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full relative">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeManualModal()">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Input Data Absensi Manual
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Masukkan data absensi pegawai jika data belum ada atau ingin diperbarui secara manual. Data yang dimasukkan akan menimpa data sebelumnya pada periode dan bulan yang sama.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-6 sm:px-6">
                <form action="{{ route('admin.absensi.manual') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Penilaian</label>
                            <select name="periode_id" id="manual_periode" required class="w-full text-sm border-gray-300 rounded-md">
                                <option value="">-- Pilih Periode --</option>
                                @foreach ($periodes as $p)
                                    <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select name="bulan" id="manual_bulan" required class="w-full text-sm border-gray-300 rounded-md">
                                <option value="">-- Pilih Bulan --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pegawai</label>
                            <select name="pegawai_id" id="manual_pegawai" required class="w-full text-sm border-gray-300 rounded-md">
                                <option value="">-- Cari Nama Pegawai --</option>
                                @foreach ($semuaPegawai as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }} ({{ $pegawai->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">HK (Hari Kerja)</label>
                            <input type="number" name="hk" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">HD (Hadir)</label>
                            <input type="number" name="hd" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 text-red-600">TK (Tidak Kerja)</label>
                            <input type="number" name="tk" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PSW</label>
                            <input type="number" name="psw" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total TL</label>
                            <input type="number" name="tl" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">KJK HT</label>
                            <input type="number" name="kjk_ht" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">KJK PC</label>
                            <input type="number" name="kjk_pc" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 text-red-600">Total KJK</label>
                            <input type="number" name="kjk" min="0" required class="w-full text-sm border border-gray-300 rounded-md p-2">
                        </div>
                    </div>
                    <div class="flex justify-end border-t pt-4 mt-6">
                        <button type="button" onclick="closeManualModal()" class="mr-3 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
        </div>

        <!-- Modal Detail Absensi -->
        <div id="detailAbsensiModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full relative">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeDetailModal()">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-detail">
                                    Detail Absensi: <span id="detail-nama" class="font-bold text-[#0091d5]"></span>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1 mb-4">Rincian keseluruhan kode absensi pada bulan terpilih.</p>
                                
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 text-sm" id="detail-content">
                                    <!-- Konten detail akan dimuat via JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDetailModal()">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Data Absensi -->
            <div class="w-full">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0">
                    <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0">
                        <h3 class="text-lg font-medium text-gray-900">Data Absensi Tersimpan</h3>
                        
                        <form action="{{ route('admin.absensi.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIP" class="text-sm border-gray-300 rounded-md w-full sm:w-auto" onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                            <button type="submit" class="hidden">Cari</button>
                            <select name="per_page" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Baris</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris</option>
                            </select>
                            <select name="periode_id" id="filter_periode" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                                @foreach ($periodes as $p)
                                    <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            <select name="bulan" id="filter_bulan" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                                @php
                                    $namaB = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                                @endphp
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $namaB[$i] }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto rounded-2xl bg-white shadow-lg">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Pegawai</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">HK</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">HD</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-red-600">TK</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">PSW</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Total TL</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">KJK HT</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">KJK PC</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-red-600">KJK</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($absensis as $absen)
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-5 text-sm font-semibold text-slate-800">{{ $absen->pegawai->nama }}</td>
                                        <td class="px-4 py-5 text-sm text-center text-slate-600">{{ $absen->hk }}</td>
                                        <td class="px-4 py-5 text-sm text-center font-bold text-green-600">{{ $absen->hd }}</td>
                                        <td class="px-4 py-5 text-sm text-center font-bold text-red-600">{{ $absen->tk }}</td>
                                        <td class="px-4 py-5 text-sm text-center text-slate-600">{{ $absen->psw }}</td>
                                        <td class="px-4 py-5 text-sm text-center text-slate-600">{{ $absen->tl }}</td>
                                        <td class="px-4 py-5 text-sm text-center text-slate-600">{{ $absen->kjk_ht }}</td>
                                        <td class="px-4 py-5 text-sm text-center text-slate-600">{{ $absen->kjk_pc }}</td>
                                        <td class="px-4 py-5 text-sm text-center font-bold text-red-600">{{ $absen->kjk }}</td>
                                        <td class="px-6 py-5 text-center">
                                            <a href="javascript:void(0)" onclick="openDetailModal(this)" 
   data-absen="{{ json_encode($absen) }}" 
   class="inline-flex items-center justify-center p-2 text-sky-600 hover:text-sky-800 bg-sky-50 hover:bg-sky-100 rounded-md cursor-pointer transition-colors z-10 relative" 
   title="Lihat Detail Kode Absensi">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-14 text-center text-sm text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3 text-3xl">📋</div>
                                                Belum ada data absensi untuk periode dan bulan ini.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($absensis instanceof \Illuminate\Pagination\LengthAwarePaginator && $absensis->hasPages())
                        <div class="p-4 border-t">
                            {{ $absensis->links() }}
                        </div>
                    @endif
                </div>

                @if(isset($rekapTriwulanPage) && count($rekapTriwulanPage) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0 mt-6">
                    <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Rekap Nilai Presensi Akhir (Satu Triwulan)</h3>
                            <p class="text-sm text-gray-500">Nilai dihitung berdasarkan total TK dan KJK selama triwulan terpilih.</p>
                        </div>
                        <form action="{{ route('admin.absensi.index') }}" method="GET" class="flex space-x-2 items-center">
                            <input type="hidden" name="periode_id" value="{{ request('periode_id', $periode_id) }}">
                            <input type="hidden" name="bulan" value="{{ request('bulan', $bulan) }}">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIP" class="text-sm border-gray-300 rounded-md" onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                            <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Cari</button>
                        </form>
                    </div>
                    <div class="overflow-x-auto rounded-2xl bg-white shadow-lg border-0">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Pegawai</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Total TK (Triwulan)</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Total KJK (Triwulan)</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-blue-600">Nilai Presensi Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($rekapTriwulanPage as $rekap)
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-5 text-sm font-semibold text-slate-800">{{ $rekap->pegawai->nama }}</td>
                                        <td class="px-6 py-5 text-sm text-center font-bold {{ $rekap->total_tk > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $rekap->total_tk }}</td>
                                        <td class="px-6 py-5 text-sm text-center text-slate-600">{{ $rekap->total_kjk }} Menit</td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="inline-flex items-center rounded-full px-4 py-2 font-bold {{ $rekap->nilai_presensi < 100 ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-700' }}">
                                                {{ $rekap->nilai_presensi }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($rekapTriwulanPage instanceof \Illuminate\Pagination\LengthAwarePaginator && $rekapTriwulanPage->hasPages())
                        <div class="p-4 border-t">
                            {{ $rekapTriwulanPage->links() }}
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bulanNames = {
            1: 'Januari', 2: 'Februari', 3: 'Maret',
            4: 'April', 5: 'Mei', 6: 'Juni',
            7: 'Juli', 8: 'Agustus', 9: 'September',
            10: 'Oktober', 11: 'November', 12: 'Desember'
        };

        function updateBulanOptions(periodeSelectId, bulanSelectId) {

            console.log("update dipanggil");
            const periodeSelect = document.getElementById(periodeSelectId);
            const bulanSelect = document.getElementById(bulanSelectId);
            if(!periodeSelect || !bulanSelect) return;

            const selectedOption = periodeSelect.options[periodeSelect.selectedIndex];
            const triwulan = selectedOption.getAttribute('data-triwulan');
            console.log({
        triwulan,
        selected: selectedOption.text,
    });

            let allowedMonths = [1,2,3,4,5,6,7,8,9,10,11,12];
            if (triwulan == 1 || triwulan == '1') allowedMonths = [1, 2, 3];
            if (triwulan == 2 || triwulan == '2') allowedMonths = [4, 5, 6];
            if (triwulan == 3 || triwulan == '3') allowedMonths = [7, 8, 9];
            if (triwulan == 4 || triwulan == '4') allowedMonths = [10, 11, 12];

            const currentVal = parseInt(bulanSelect.value) || 0;
            bulanSelect.innerHTML = '';
            
            allowedMonths.forEach((m, index) => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = bulanNames[m];
                
                // Jika bulan yang dipilih sebelumnya ada di allowedMonths, pilih itu.
                // Jika tidak, pilih bulan pertama dari allowedMonths
                if (m === currentVal || (index === 0 && !allowedMonths.includes(currentVal))) {
                    opt.selected = true;
                }
                
                bulanSelect.appendChild(opt);
            });
        }

        const uploadPeriode = document.getElementById('upload_periode');
        if (uploadPeriode) {
            uploadPeriode.addEventListener('change', () => updateBulanOptions('upload_periode', 'upload_bulan'));
            updateBulanOptions('upload_periode', 'upload_bulan');
        }

        const filterPeriode = document.getElementById('filter_periode');
        if (filterPeriode) {
            // Kita tidak menambah listener change pada filter_periode 
            // karena ada onchange="this.form.submit()" bawaan form yang akan me-reload halaman
            updateBulanOptions('filter_periode', 'filter_bulan');
        }

        const manualPeriode = document.getElementById('manual_periode');
        if (manualPeriode) {
            manualPeriode.addEventListener('change', () => updateBulanOptions('manual_periode', 'manual_bulan'));
            updateBulanOptions('manual_periode', 'manual_bulan');
        }
    });

    function openManualModal() {
        document.getElementById('manualInputModal').classList.remove('hidden');
    }

    function closeManualModal() {
        document.getElementById('manualInputModal').classList.add('hidden');
    }

    function openDetailModal(button) {
        let data = {};
        try {
            data = JSON.parse(button.getAttribute('data-absen'));
        } catch (e) {
            console.error("Failed to parse absen data", e);
            return;
        }

        document.getElementById('detail-nama').innerText = data.pegawai ? data.pegawai.nama : '-';
        
        const details = [
            { label: 'HK', value: data.hk },
            { label: 'HD', value: data.hd },
            { label: 'TK', value: data.tk, highlight: true },
            { label: 'TB', value: data.tb },
            { label: 'PD', value: data.pd },
            { label: 'DK', value: data.dk },
            { label: 'KN', value: data.kn },
            { label: 'HT', value: data.ht },
            { label: 'PSW Total', value: data.psw, highlight: true },
            { label: 'PSW 1', value: data.psw1 },
            { label: 'PSW 2', value: data.psw2 },
            { label: 'PSW 3', value: data.psw3 },
            { label: 'PSW 4', value: data.psw4 },
            { label: 'TL Total', value: data.tl, highlight: true },
            { label: 'TL 1', value: data.tl1 },
            { label: 'TL 2', value: data.tl2 },
            { label: 'TL 3', value: data.tl3 },
            { label: 'TL 4', value: data.tl4 },
            { label: 'CB', value: data.cb },
            { label: 'CL', value: data.cl },
            { label: 'CM', value: data.cm },
            { label: 'CP', value: data.cp },
            { label: 'CS', value: data.cs },
            { label: 'KJK HT', value: data.kjk_ht },
            { label: 'KJK PC', value: data.kjk_pc },
            { label: 'Total KJK', value: data.kjk, highlight: true }
        ];

        let htmlContent = '';
        details.forEach(item => {
            const val = item.value || 0;
            const textColor = (item.highlight && val > 0) ? 'text-red-600 font-bold' : 'text-gray-900';
            htmlContent += `
                <div class="border rounded p-2 bg-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1">${item.label}</div>
                    <div class="text-base ${textColor}">${val}</div>
                </div>
            `;
        });

        document.getElementById('detail-content').innerHTML = htmlContent;
        document.getElementById('detailAbsensiModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailAbsensiModal').classList.add('hidden');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('manual_pegawai')) {
            new TomSelect("#manual_pegawai",{
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Cari Nama Pegawai..."
            });
        }
    });
</script>
@endpush

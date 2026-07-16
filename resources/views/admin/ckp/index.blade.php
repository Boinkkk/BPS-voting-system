@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">inpu
    Manajemen Data CKP Pegawai
</h2>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endpush

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

        <!-- Control Panel: Upload & Input Manual -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-0">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Upload Data CKP (Excel/CSV)</h3>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.ckp.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
                    @csrf
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <select name="periode_id" id="upload_periode" required class="w-full text-sm border-gray-300 rounded-md">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full text-sm border border-gray-300 rounded-md p-[7px] bg-white">
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full px-5 py-[9px] bg-[#0091d5] text-white text-sm font-medium rounded-md hover:bg-bps-secondary transition-colors">
                            Upload Data
                        </button>
                    </div>
                </form>
                <p class="text-[11px] text-gray-500 mt-3 mb-1">Catatan: Kolom Excel yang wajib ada adalah Nama, NIP, dan Nilai CKP.</p>
                <div class="mt-4 flex gap-2 border-t border-slate-200 pt-4">
                    <button onclick="openModal('manualModal')" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors">
                        + Input CKP Pegawai
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter & Tabel -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <h3 class="text-lg font-medium text-gray-900 whitespace-nowrap">Data CKP Tersimpan</h3>
                
                <form action="{{ route('admin.ckp.index') }}" method="GET" class="flex flex-wrap lg:flex-nowrap items-center gap-2 w-full justify-end">
                    
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama / NIP..." class="text-sm border-gray-300 rounded-md w-full lg:w-48">
                    
                    <select name="periode_id" id="filter_periode" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md w-full lg:w-48">
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>

                    <select name="per_page" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / Page</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / Page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / Page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / Page</option>
                    </select>
                    
                    <button type="submit" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm font-medium">Cari</button>
                </form>
            </div>
            
            <div class="overflow-x-auto rounded-2xl bg-white shadow-lg border-0">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Pegawai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">NIP</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Nilai CKP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ckps as $ckp)
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-5 text-sm font-semibold text-slate-800">{{ $ckp->pegawai->nama }}</td>
                                <td class="px-6 py-5 text-sm text-slate-600">{{ $ckp->pegawai->nip }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-4 py-2 font-bold text-[#0091d5]">
                                        {{ number_format($ckp->nilai, 2, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-14 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3 text-3xl">📋</div>
                                        Belum ada data CKP untuk kriteria ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $ckps->links() }}
            </div>
        </div>

    </div>
</div>

<!-- Manual Input Modal -->
<div id="manualModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Input Data CKP Manual</h3>
            <button onclick="closeModal('manualModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form action="{{ route('admin.ckp.manual') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Target Periode</label>
                <select name="periode_id" id="manual_periode" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach ($periodes as $p)
                        <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pegawai</label>
                <select name="id_pegawai" id="pegawai_select" required placeholder="Ketik nama atau NIP..." class="w-full">
                    <option value="">Cari Pegawai...</option>
                    @isset($semuaPegawai)
                        @foreach($semuaPegawai as $peg)
                            <option value="{{ $peg->id }}">{{ $peg->nama }} ({{ $peg->nip }})</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nilai CKP</label>
                <input type="number" step="0.01" min="0" max="100" name="nilai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 85.50">
            </div>
            
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded shadow hover:bg-sky-700">Simpan Nilai CKP</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Init TomSelect for Pegawai searchable dropdown
        new TomSelect("#pegawai_select",{
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        // Attach listeners and run initial setup
        // removed bulan listeners
    });
</script>
@endpush

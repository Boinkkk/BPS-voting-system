@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Manajemen Absensi Pegawai
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

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Pengaturan Tipe Absen -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Tipe Absen & Bobot</h3>
                        <button onclick="document.getElementById('modalTambahTipe').classList.remove('hidden')" class="px-3 py-1 bg-[#0091d5] text-white text-xs font-medium rounded-md hover:bg-blue-600">
                            + Tambah
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="space-y-4">
                            @foreach($tipeAbsens as $tipe)
                            <form action="{{ route('admin.absensi.tipe.update', $tipe->id) }}" method="POST" class="flex items-center space-x-2 p-2 border rounded hover:bg-gray-50">
                                @csrf
                                @method('PUT')
                                <div class="flex-1">
                                    <input type="text" name="status" value="{{ $tipe->status }}" class="w-full text-sm border-gray-300 rounded-md py-1" required>
                                </div>
                                <div class="w-20">
                                    <input type="number" step="0.01" name="bobot" value="{{ $tipe->bobot }}" class="w-full text-sm border-gray-300 rounded-md py-1 text-center" required>
                                </div>
                                <button type="submit" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded" title="Simpan Perubahan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Histori Absensi -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Data Riwayat Absensi</h3>
                        <p class="text-sm text-gray-500">Histori kehadiran pegawai yang telah terekam di sistem.</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-3 font-semibold text-sm">Waktu Absensi</th>
                                    <th class="p-3 font-semibold text-sm">Nama Pegawai</th>
                                    <th class="p-3 font-semibold text-sm">Tipe Absen</th>
                                    <th class="p-3 font-semibold text-sm text-center">Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensis as $absen)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-3 text-sm text-gray-600">{{ $absen->waktu_absensi->format('d M Y, H:i') }}</td>
                                        <td class="p-3 text-sm font-medium">{{ $absen->pegawai->nama }}</td>
                                        <td class="p-3 text-sm">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $absen->tipeAbsen->status }}</span>
                                        </td>
                                        <td class="p-3 text-sm text-center font-bold">{{ $absen->tipeAbsen->bobot }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-4 text-center text-sm text-gray-500">Belum ada data absensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($absensis->hasPages())
                    <div class="p-4 border-t">
                        {{ $absensis->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah Tipe Absen -->
<div id="modalTambahTipe" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Tambah Tipe Absen</h3>
            <button onclick="document.getElementById('modalTambahTipe').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="{{ route('admin.absensi.tipe.store') }}" method="POST">
            @csrf
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Tipe (Status)</label>
                    <input type="text" name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    <p class="mt-1 text-xs text-gray-500">Contoh: Cuti, Dinas Luar</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bobot Nilai</label>
                    <input type="number" step="0.01" name="bobot" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    <p class="mt-1 text-xs text-gray-500">Desimal dengan titik (misal: 0.5 atau 1.0)</p>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-right border-t">
                <button type="button" onclick="document.getElementById('modalTambahTipe').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 mr-2">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#0091d5] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-600">
                    Simpan Tipe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

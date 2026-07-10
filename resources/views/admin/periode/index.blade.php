@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
            {{ __('Manajemen Periode') }}
        </h2>
    </div>

    <div class="py-12" style="font-family: 'Hanken Grotesk', sans-serif;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <button onclick="openModal('addPeriodeModal')" class="bg-[#0D8ABC] hover:bg-sky-800 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors">
                    + Tambah Periode Baru
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 text-[#1d1d1b]">Daftar Periode Penilaian</h3>
                    
                    @if(count($periodes) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 border-b">
                                        <th class="p-3 font-semibold text-sm">Nama Periode</th>
                                        <th class="p-3 font-semibold text-sm">Tanggal Mulai</th>
                                        <th class="p-3 font-semibold text-sm">Tanggal Selesai</th>
                                        <th class="p-3 font-semibold text-sm">Status</th>
                                        <th class="p-3 font-semibold text-sm">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periodes as $p)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-sm font-medium">{{ $p->nama }}</td>
                                            <td class="p-3 text-sm">{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</td>
                                            <td class="p-3 text-sm">{{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}</td>
                                            <td class="p-3 text-sm">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $p->status == 'selesai' ? 'bg-gray-200 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-sm">
                                                <button onclick="openEditModal({{ $p->id }}, '{{ $p->triwulan }}', '{{ $p->tahun }}', '{{ $p->tanggal_mulai }}', '{{ $p->tanggal_selesai }}', '{{ $p->status }}')" class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                                                <form action="{{ route('admin.periode.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            Belum ada data periode.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Add Periode Modal -->
    <div id="addPeriodeModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl transition-all w-full sm:max-w-md mx-auto" style="min-width: min(100%, 400px);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Tambah Periode Penilaian</h3>
                <button onclick="closeModal('addPeriodeModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form action="{{ route('admin.periode.store') }}" method="POST">
                @csrf
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Triwulan</label>
                        <select name="triwulan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="1">Triwulan 1 (Jan - Mar)</option>
                            <option value="2">Triwulan 2 (Apr - Jun)</option>
                            <option value="3">Triwulan 3 (Jul - Sep)</option>
                            <option value="4">Triwulan 4 (Okt - Des)</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="tahun" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ date('Y') }}" min="2000">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="penginputan">Masa Penginputan Kinerja</option>
                            <option value="voting">Masa Voting Kandidat</option>
                            <option value="review_kepala">Review Kepala Bagian</option>
                            <option value="selesai">Selesai</option>
                        </select>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded shadow hover:bg-sky-700">Simpan</button>
                </div>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>

    <!-- Edit Periode Modal -->
    <div id="editPeriodeModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl transition-all w-full sm:max-w-md mx-auto" style="min-width: min(100%, 400px);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Edit Periode Penilaian</h3>
                <button onclick="closeModal('editPeriodeModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="editPeriodeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Triwulan</label>
                        <select name="triwulan" id="edit_triwulan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="1">Triwulan 1 (Jan - Mar)</option>
                            <option value="2">Triwulan 2 (Apr - Jun)</option>
                            <option value="3">Triwulan 3 (Jul - Sep)</option>
                            <option value="4">Triwulan 4 (Okt - Des)</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="tahun" id="edit_tahun" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="2000">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="edit_status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="penginputan">Masa Penginputan Kinerja</option>
                            <option value="voting">Masa Voting Kandidat</option>
                            <option value="review_kepala">Review Kepala Bagian</option>
                            <option value="selesai">Selesai</option>
                        </select>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Simpan Perubahan</button>
                </div>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
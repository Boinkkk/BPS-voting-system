@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Manajemen Pertanyaan Survey
</h2>
@endsection

@section('content')
<div class="py-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editData: { id: '', kategori: '', pertanyaan: '', nomor_urut: '' }
}">
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Daftar Pertanyaan Survey</h3>
                    <p class="text-sm text-gray-500">Master data pertanyaan untuk survey kinerja pegawai.</p>
                </div>
                <button @click="showAddModal = true" class="px-4 py-2 bg-[#0091d5] text-white text-sm font-medium rounded-md hover:bg-blue-600 transition-colors">
                    + Tambah Pertanyaan
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b">
                            <th class="p-3 font-semibold text-sm w-16 text-center">No</th>
                            <th class="p-3 font-semibold text-sm w-1/4">Kategori / Pilar</th>
                            <th class="p-3 font-semibold text-sm">Deskripsi Pertanyaan</th>
                            <th class="p-3 font-semibold text-sm w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pertanyaans as $p)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-sm text-center">{{ $p->nomor_urut }}</td>
                                <td class="p-3 text-sm font-semibold text-[#0091d5]">{{ $p->kategori }}</td>
                                <td class="p-3 text-sm text-gray-700 whitespace-normal">{{ $p->pertanyaan }}</td>
                                <td class="p-3 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button @click="editData = { id: '{{ $p->id }}', kategori: '{{ addslashes($p->kategori) }}', pertanyaan: '{{ addslashes($p->pertanyaan) }}', nomor_urut: '{{ $p->nomor_urut }}' }; showEditModal = true" class="text-amber-600 hover:text-amber-800" title="Edit">
                                            ✏️
                                        </button>
                                        <form action="{{ route('admin.survey.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-sm text-gray-500">Belum ada data pertanyaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showAddModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.survey.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tambah Pertanyaan Survey</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Urut</label>
                                <input type="number" name="nomor_urut" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori / Pilar</label>
                                <input type="text" name="kategori" placeholder="Contoh: Berorientasi Pelayanan" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi Pertanyaan</label>
                                <textarea name="pertanyaan" rows="4" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#0091d5] text-base font-medium text-white hover:bg-blue-600 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" @click="showAddModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showEditModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="'{{ url('admin/survey') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Pertanyaan Survey</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Urut</label>
                                <input type="number" name="nomor_urut" x-model="editData.nomor_urut" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori / Pilar</label>
                                <input type="text" name="kategori" x-model="editData.kategori" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi Pertanyaan</label>
                                <textarea name="pertanyaan" rows="4" x-model="editData.pertanyaan" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#0091d5] text-base font-medium text-white hover:bg-blue-600 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

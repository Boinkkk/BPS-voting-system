@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Manajemen Pertanyaan Survey
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Daftar Pertanyaan Survey</h3>
                    <p class="text-sm text-gray-500">Master data pertanyaan untuk survey kinerja pegawai.</p>
                </div>
                <button onclick="openAddModal()" class="px-4 py-2 bg-[#0091d5] text-white text-sm font-medium rounded-md hover:bg-bps-secondary transition-colors">
                    + Tambah Pertanyaan
                </button>
            </div>
            
            <div class="overflow-x-auto rounded-2xl bg-white shadow-lg border-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600 w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 w-1/4">Kategori / Pilar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Deskripsi Pertanyaan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600 w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pertanyaans as $p)
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-5 text-sm text-center text-slate-700 font-medium">{{ $p->nomor_urut }}</td>
                                <td class="px-6 py-5 text-sm font-bold text-[#0091d5]">{{ $p->kategori }}</td>
                                <td class="px-6 py-5 text-sm text-slate-700 whitespace-normal">{{ $p->pertanyaan }}</td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex justify-center space-x-2 whitespace-nowrap">
                                        <button onclick="openEditModal('{{ $p->id }}', '{{ addslashes($p->kategori) }}', '{{ addslashes($p->pertanyaan) }}', '{{ $p->nomor_urut }}')" class="text-sky-600 hover:text-sky-800 font-medium transition-colors p-2 bg-sky-50 rounded-md hover:bg-sky-100 text-sm px-3" title="Edit">
                                            Edit
                                        </button>
                                        <button onclick="confirmDelete('{{ $p->id }}')" class="text-red-600 hover:text-red-800 font-medium transition-colors p-2 bg-red-50 rounded-md hover:bg-red-100 text-sm px-3" title="Hapus">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3 text-3xl">📋</div>
                                        Belum ada data pertanyaan survey.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="addModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-bps-bg">
                <h3 class="text-lg font-bold text-gray-900">Tambah Pertanyaan Survey</h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none focus:outline-none">&times;</button>
            </div>
            <form action="{{ route('admin.survey.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
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
                <div class="px-6 py-4 bg-bps-bg border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-bps-bg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#0091d5] text-white rounded-md hover:bg-bps-secondary text-sm font-medium transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-bps-bg">
                <h3 class="text-lg font-bold text-gray-900">Edit Pertanyaan Survey</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none focus:outline-none">&times;</button>
            </div>
            <form id="edit_form" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Urut</label>
                        <input type="number" id="edit_nomor_urut" name="nomor_urut" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori / Pilar</label>
                        <input type="text" id="edit_kategori" name="kategori" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Pertanyaan</label>
                        <textarea id="edit_pertanyaan" name="pertanyaan" rows="4" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-bps-bg border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-bps-bg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#0091d5] text-white rounded-md hover:bg-bps-secondary text-sm font-medium transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Hapus Pertanyaan Survey</h3>
                <p class="text-sm text-gray-500 text-center">Apakah Anda yakin ingin menghapus pertanyaan ini? Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="px-6 py-4 bg-bps-bg border-t border-gray-200 flex justify-center space-x-3">
                <form id="delete_form" method="POST" class="w-full flex space-x-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-bps-bg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }
    
    function openEditModal(id, kategori, pertanyaan, nomor_urut) {
        document.getElementById('edit_form').action = "{{ url('admin/survey') }}/" + id;
        document.getElementById('edit_nomor_urut').value = nomor_urut;
        document.getElementById('edit_kategori').value = kategori;
        document.getElementById('edit_pertanyaan').value = pertanyaan;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmDelete(id) {
        document.getElementById('delete_form').action = "{{ url('admin/survey') }}/" + id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endpush

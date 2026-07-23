@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="font-semibold text-xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
            {{ __('Manajemen Pegawai') }}
        </h2>
        <div>
            <button onclick="openModal('addModal')" class="bg-[#0D8ABC] hover:bg-sky-800 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors whitespace-nowrap">
                + Tambah Pegawai
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @livewire('pegawai-table')

    <!-- Modal Tambah Pegawai -->
    <div id="addModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Tambah Pegawai Baru</h3>
                <button onclick="closeModal('addModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form action="{{ route('admin.pegawai.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">NIP</label>
                        <input type="text" name="nip" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="jabatan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Departemen</label>
                        <select name="departemen_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            @foreach($departemens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->tipe }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status_pegawai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded shadow hover:bg-sky-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pegawai -->
    <div id="editModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Edit Pegawai</h3>
                <button onclick="closeModal('editModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" id="edit_nama" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">NIP</label>
                        <input type="text" name="nip" id="edit_nip" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="edit_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="jabatan" id="edit_jabatan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Departemen</label>
                        <select name="departemen_id" id="edit_departemen_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            @foreach($departemens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role_id" id="edit_role_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->tipe }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status_pegawai" id="edit_status_pegawai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded shadow hover:bg-sky-700">Update Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Ubah Password <span id="pwd_nama" class="text-sky-600"></span></h3>
                <button onclick="closeModal('passwordModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="passwordForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="password" required minlength="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        function openEditModal(pegawai) {
            document.getElementById('edit_nama').value = pegawai.nama;
            document.getElementById('edit_nip').value = pegawai.nip;
            document.getElementById('edit_email').value = pegawai.email;
            document.getElementById('edit_jabatan').value = pegawai.jabatan;
            document.getElementById('edit_departemen_id').value = pegawai.departemen_id;
            document.getElementById('edit_role_id').value = pegawai.role_id;
            document.getElementById('edit_tanggal_masuk').value = pegawai.tanggal_masuk;
            document.getElementById('edit_status_pegawai').value = pegawai.status_pegawai;
            
            document.getElementById('editForm').action = '/admin/pegawai/' + pegawai.id;
            openModal('editModal');
        }
        function openPasswordModal(id, nama) {
            document.getElementById('pwd_nama').innerText = nama;
            document.getElementById('passwordForm').action = '/admin/pegawai/' + id + '/password';
            openModal('passwordModal');
        }
    </script>
@endsection

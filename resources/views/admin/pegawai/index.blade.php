@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="font-semibold text-xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
            {{ __('Manajemen Pegawai') }}
        </h2>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <form action="{{ route('admin.pegawai.index') }}"
                method="GET"
                class="relative w-full sm:w-80">

                <div class="relative">

                    <!-- Icon -->
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari NIP atau Nama..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm shadow-sm transition-all placeholder:text-slate-400 focus:border-[#0091d5] focus:ring-4 focus:ring-sky-100"
                    >

                </div>

            </form>
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

    <div class="bg-bps-bg overflow-hidden shadow-none sm:rounded-lg border-0">
        <div class="p-6">
            
            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse($pegawai as $p)
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-5 transition hover:shadow-md">
                        <!-- Header -->
                        <div class="flex items-start justify-between">
                            <div class="flex gap-3">
                                <!-- Avatar -->
                                @if($p->foto_profil ?? false)
                                    <img src="{{ $p->foto_profil_url ?? '' }}" alt="{{ $p->nama }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-200">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-[#0091d5] text-white flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($p->nama, 0, 1)) }}
                                    </div>
                                @endif

                                <!-- Nama & NIP -->
                                <div>
                                    <div class="font-semibold text-slate-800 leading-tight">{{ $p->nama }}</div>
                                    <div class="text-sm text-slate-500">{{ $p->nip }}</div>
                                    <span class="inline-flex mt-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-[#0091d5]">
                                        {{ $p->jabatan }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="my-4 border-t border-slate-100"></div>

                        <!-- Details -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <div class="text-[11px] uppercase tracking-wide text-slate-500">Departemen</div>
                                <div class="mt-1 text-sm font-medium text-slate-700">{{ $p->departemen->nama ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wide text-slate-500">Role</div>
                                <div class="mt-1 text-sm font-medium text-slate-700">{{ $p->role->tipe ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wide text-slate-500">Status</div>
                                <div class="mt-1">
                                    @if($p->status_pegawai == 'aktif')
                                        <span class="inline-flex px-2 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Aktif</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Non-Aktif</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <button onclick="openEditModal({{ json_encode($p) }})" class="flex-1 text-center py-2 text-sky-600 bg-sky-50 rounded-lg hover:bg-sky-100 font-medium text-sm transition-colors">Edit</button>
                            <button onclick="openPasswordModal('{{ $p->id }}', '{{ $p->nama }}')" class="flex-1 text-center py-2 text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 font-medium text-sm transition-colors">Password</button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 p-10 text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-4xl mb-4">📋</div>
                        <h3 class="font-semibold text-slate-700">Belum Ada Data Pegawai</h3>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto rounded-2xl bg-white shadow-lg ring-1 ring-gray-200">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">NIP</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Pegawai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Jabatan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Departemen</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Role</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pegawai as $p)
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-5 text-sm text-slate-500">{{ $p->nip }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        @if($p->foto_profil ?? false)
                                            <img src="{{ $p->foto_profil_url ?? '' }}" alt="{{ $p->nama }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-200">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-[#0091d5] text-white flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($p->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-slate-800">{{ $p->nama }}</div>
                                            <div class="text-sm text-slate-500">{{ $p->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-[#0091d5]">
                                        {{ $p->jabatan }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-700">
                                    {{ $p->departemen->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">{{ $p->role->tipe ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($p->status_pegawai == 'aktif')
                                        <span class="inline-flex px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Aktif</span>
                                    @else
                                        <span class="inline-flex px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center space-x-2 whitespace-nowrap">
                                    <button onclick="openEditModal({{ json_encode($p) }})" class="text-sky-600 hover:text-sky-800 font-medium transition-colors p-1 bg-sky-50 rounded-md hover:bg-sky-100 text-sm px-2">Edit</button>
                                    <button onclick="openPasswordModal('{{ $p->id }}', '{{ $p->nama }}')" class="text-orange-600 hover:text-orange-800 font-medium transition-colors p-1 bg-orange-50 rounded-md hover:bg-orange-100 text-sm px-2">Password</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $pegawai->links() }}
            </div>
        </div>
    </div>

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

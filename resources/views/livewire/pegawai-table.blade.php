<div>
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="pegawai_search" class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        id="pegawai_search" 
                        placeholder="Cari NIP, Nama, Email..." 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full py-2"
                        style="padding-left: 2.25rem; padding-right: 0.75rem;"
                    >
                </div>
            </div>

            <!-- Departemen -->
            <div>
                <label for="pegawai_dept" class="block text-xs font-medium text-gray-700 mb-1">Departemen</label>
                <select wire:model.live="departemen_id" id="pegawai_dept" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role -->
            <div>
                <label for="pegawai_role" class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                <select wire:model.live="role_id" id="pegawai_role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
                    <option value="">Semua Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->tipe }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status & Reset Button -->
            <div>
                <label for="pegawai_status" class="block text-xs font-medium text-gray-700 mb-1">Status Pegawai</label>
                <div class="flex gap-2">
                    <select wire:model.live="status_pegawai" id="pegawai_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                    <button wire:click="resetFilters" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg shadow-sm transition-colors flex items-center justify-center shrink-0" title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Container -->
    <div class="relative">
        <!-- Loading Overlay -->
        <div wire:loading.flex class="absolute inset-0 z-20 items-center justify-center bg-white/50 backdrop-blur-xs rounded-2xl">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-bps-primary"></div>
        </div>

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
                    @forelse($pegawai as $p)
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
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                Belum ada data pegawai yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pegawai->hasPages())
            <div class="mt-6">
                {{ $pegawai->links() }}
            </div>
        @endif
    </div>
</div>

@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 mb-6">
            Edit Pengumuman
        </h2>
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                
                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.pengumuman.update', $pengumuman->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Judul -->
                        <div class="col-span-2">
                            <label for="judul" class="block mb-2 text-sm font-medium text-gray-900">Judul</label>
                            <input type="text" id="judul" name="judul" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5" value="{{ old('judul', $pengumuman->judul) }}" required>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategori" class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                            <select id="kategori" name="kategori" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5" required>
                                <option value="Informasi" {{ $pengumuman->kategori == 'Informasi' ? 'selected' : '' }}>Informasi</option>
                                <option value="Pengumuman" {{ $pengumuman->kategori == 'Pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                <option value="Peringatan" {{ $pengumuman->kategori == 'Peringatan' ? 'selected' : '' }}>Peringatan</option>
                                <option value="Maintenance" {{ $pengumuman->kategori == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Hasil" {{ $pengumuman->kategori == 'Hasil' ? 'selected' : '' }}>Hasil</option>
                            </select>
                        </div>

                        <!-- Prioritas -->
                        <div>
                            <label for="prioritas" class="block mb-2 text-sm font-medium text-gray-900">Prioritas</label>
                            <select id="prioritas" name="prioritas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5" required>
                                <option value="Normal" {{ $pengumuman->prioritas == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Low" {{ $pengumuman->prioritas == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ $pengumuman->prioritas == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ $pengumuman->prioritas == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Critical" {{ $pengumuman->prioritas == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>

                        <!-- Target -->
                        <div class="col-span-2">
                            <label for="target" class="block mb-2 text-sm font-medium text-gray-900">Target User</label>
                            <select id="target" name="target" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5">
                                <option value="" {{ $pengumuman->target == '' ? 'selected' : '' }}>Semua User</option>
                                <option value="Admin" {{ $pengumuman->target == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Kepala BPS" {{ $pengumuman->target == 'Kepala BPS' ? 'selected' : '' }}>Kepala BPS</option>
                                <option value="Pegawai" {{ $pengumuman->target == 'Pegawai' ? 'selected' : '' }}>Pegawai</option>
                                <option value="Tim Penilai" {{ $pengumuman->target == 'Tim Penilai' ? 'selected' : '' }}>Tim Penilai</option>
                            </select>
                        </div>

                        <!-- Publish At -->
                        <div>
                            <label for="publish_at" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Publish (Kosongkan jika langsung)</label>
                            <input type="datetime-local" id="publish_at" name="publish_at" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5" value="{{ old('publish_at', $pengumuman->publish_at ? $pengumuman->publish_at->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <!-- Expire At -->
                        <div>
                            <label for="expire_at" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Berakhir (Kosongkan jika tidak pernah expire)</label>
                            <input type="datetime-local" id="expire_at" name="expire_at" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5" value="{{ old('expire_at', $pengumuman->expire_at ? $pengumuman->expire_at->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <!-- Konten -->
                        <div class="col-span-2">
                            <label for="konten" class="block mb-2 text-sm font-medium text-gray-900">Konten Pengumuman</label>
                            <textarea id="konten" name="konten" rows="5" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-bps-secondary focus:border-bps-secondary" required>{{ old('konten', $pengumuman->konten) }}</textarea>
                        </div>
                        
                        <!-- Lampiran -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Lampiran Saat Ini</label>
                            @if(is_array($pengumuman->lampiran) && count($pengumuman->lampiran) > 0)
                                <ul class="list-disc pl-5 mb-4 space-y-2 text-sm">
                                    @foreach($pengumuman->lampiran as $index => $lampiran)
                                        <li class="flex items-center gap-4">
                                            <a href="{{ Storage::url($lampiran) }}" target="_blank" class="text-blue-600 hover:underline flex-1 truncate">{{ basename($lampiran) }}</a>
                                            <label class="inline-flex items-center text-red-600">
                                                <input type="checkbox" name="remove_lampiran[{{ $index }}]" value="1" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                                <span class="ml-2">Hapus</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 mb-4">Tidak ada lampiran.</p>
                            @endif

                            <label class="block mb-2 text-sm font-medium text-gray-900">Tambah Lampiran Baru</label>
                            <input type="file" name="lampiran[]" multiple class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                            <p class="mt-1 text-sm text-gray-500">Format: PDF, Word, Excel, Gambar. Maks 5MB per file.</p>
                        </div>

                        <!-- Checkboxes -->
                        <div class="col-span-2 flex flex-col sm:flex-row gap-4 mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_sticky" class="w-4 h-4 text-bps-secondary bg-gray-100 border-gray-300 rounded focus:ring-bps-secondary" {{ $pengumuman->is_sticky ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-900">Sticky (Pin di atas dashboard)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_popup" class="w-4 h-4 text-bps-secondary bg-gray-100 border-gray-300 rounded focus:ring-bps-secondary" {{ $pengumuman->is_popup ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-900">Popup (Muncul sekali saat user login)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="kirim_notifikasi" class="w-4 h-4 text-bps-secondary bg-gray-100 border-gray-300 rounded focus:ring-bps-secondary" {{ $pengumuman->kirim_notifikasi ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-900">Kirim Notifikasi (Lagi)</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                        <a href="{{ route('admin.pengumuman.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">Batal</a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-bps-primary rounded-lg hover:bg-bps-secondary transition-colors">Simpan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-bps-text mb-1">Tambah Istilah Glosarium</h1>
        <p class="text-sm text-gray-500">Tambahkan istilah baru beserta definisinya.</p>
    </div>
    <div>
        <a href="{{ route('admin.glosarium.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-bps-bg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-secondary">
            Batal
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm max-w-3xl">
    <form action="{{ route('admin.glosarium.store') }}" method="POST">
        @csrf
        <div class="p-6 space-y-6">
            
            <div>
                <label for="istilah" class="block text-sm font-medium text-gray-700 mb-1">Istilah <span class="text-red-500">*</span></label>
                <input type="text" name="istilah" id="istilah" value="{{ old('istilah') }}" class="mt-1 focus:ring-bps-secondary focus:border-bps-secondary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required placeholder="Contoh: Absensi Pegawai">
                @error('istilah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="definisi" class="block text-sm font-medium text-gray-700 mb-1">Definisi <span class="text-red-500">*</span></label>
                <textarea name="definisi" id="definisi" rows="5" class="mt-1 focus:ring-bps-secondary focus:border-bps-secondary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required placeholder="Tuliskan definisi lengkap di sini...">{{ old('definisi') }}</textarea>
                @error('definisi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
        <div class="px-6 py-4 bg-bps-bg border-t border-gray-200 flex justify-end">
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-bps-secondary hover:bg-bps-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-secondary">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection

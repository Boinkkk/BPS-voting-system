@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-bps-text mb-1">Tambah FAQ Baru</h1>
        <p class="text-sm text-gray-500">Tambahkan pertanyaan dan jawaban baru ke daftar FAQ.</p>
    </div>
    <a href="{{ route('admin.faq.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6">
        <form action="{{ route('admin.faq.store') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <label for="pertanyaan" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                <input type="text" name="pertanyaan" id="pertanyaan" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-bps-primary focus:border-bps-primary sm:text-sm @error('pertanyaan') border-red-500 @enderror" value="{{ old('pertanyaan') }}" required>
                @error('pertanyaan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="jawaban" class="block text-sm font-medium text-gray-700 mb-1">Jawaban <span class="text-red-500">*</span></label>
                <textarea name="jawaban" id="jawaban" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-bps-primary focus:border-bps-primary sm:text-sm @error('jawaban') border-red-500 @enderror" required>{{ old('jawaban') }}</textarea>
                @error('jawaban')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <a href="{{ route('admin.faq.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-primary mr-3">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-bps-primary hover:bg-bps-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-[#1D1D1B] mb-1">Glosarium</h1>
        <p class="text-sm text-gray-500">Daftar istilah dan penjelasan terkait sistem pemilihan pegawai terbaik.</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($glosariums as $item)
                <div class="bg-gray-50 p-5 rounded-lg border border-gray-100 hover:border-[#0091DA]/30 transition-colors duration-200">
                    <h3 class="text-lg font-semibold text-[#0091DA] mb-2">{{ $item->istilah }}</h3>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ nl2br(e($item->definisi)) }}</p>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-gray-500">
                    <p>Belum ada istilah di glosarium.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

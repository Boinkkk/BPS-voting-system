@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold text-gray-900">Kalender Pemilihan</h1>
    <p class="text-gray-500 text-sm mt-1">Sistem Pemilihan Pegawai Terbaik BerAKHLAK BPS</p>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm w-full p-6 mt-4 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Jadwal Seluruh Periode</h3>
            <p class="text-sm text-gray-500 mt-1">Anda dapat melihat jadwal periode dari berbagai triwulan dengan menavigasikan bulan pada kalender di bawah ini.</p>
        </div>
    </div>

    @livewire('calendar-grid')
</div>
@endsection

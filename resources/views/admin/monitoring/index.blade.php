@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="font-semibold text-2xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
        Monitoring Survei Pegawai
    </h2>
    <p class="text-sm text-gray-500 mt-1">Pantau progress pengisian survei dan perolehan suara sementara (Live Score).</p>
</div>

<div class="py-6" style="font-family: 'Hanken Grotesk', sans-serif;">
    <div class="max-w-7xl mx-auto">
        
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filter Periode -->
        <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden mb-8">
            <div class="bg-orange px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter & Pengaturan Periode
                </h3>
            </div>
            <div class="p-6 bg-white">
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                    <form method="GET" action="{{ route('admin.monitoring.index') }}" class="flex flex-col sm:flex-row items-center gap-4 flex-grow">
                        <label for="periode_id" class="text-sm font-semibold text-gray-700">Pilih Periode Survei:</label>
                        <select name="periode_id" id="periode_id" class="border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm flex-grow w-full sm:w-auto">
                            @forelse ($periodes as $p)
                                <option value="{{ $p->id }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} (Status: {{ ucfirst($p->status) }})
                                </option>
                            @empty
                                <option value="">-- Belum Ada Periode --</option>
                            @endforelse
                        </select>
                        <button type="submit" class="w-full sm:w-auto bg-orange text-white font-semibold px-6 py-2 rounded-md shadow-md hover:opacity-90 transition-opacity">
                            Tampilkan
                        </button>
                    </form>


                </div>
            </div>
        </div>

        @if($periode_id)
        
        <!-- Progress Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Pegawai (Aktif)</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalPegawai }}</h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-[#0091d5]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Sudah Selesai Survei</p>
                    <h3 class="text-3xl font-bold text-[#76bc21]">{{ $pegawaiSelesai }}</h3>
                </div>
                <div class="p-3 bg-green-50 rounded-full text-[#76bc21]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Persentase Progress</p>
                    <h3 class="text-3xl font-bold text-orange-500">{{ $persentase }}%</h3>
                </div>
                <div class="p-3 bg-orange-50 rounded-full text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Live Score -->
            <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden">
                <div class="bg-biru px-6 py-4 text-white">
                    <h3 class="font-bold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Live Score
                    </h3>
                    <p class="text-white/80 text-xs mt-1">Skor Rata-Rata Survei BerAKHLAK</p>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-slate-600">Peringkat</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-slate-600">Kandidat</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-slate-600 text-right">Skor Final Gabungan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($kandidats as $index => $k)
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    @if($index == 0)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 font-bold border border-yellow-200 shadow-sm">1</span>
                                    @elseif($index == 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-600 font-bold border border-gray-300 shadow-sm">2</span>
                                    @elseif($index == 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-bold border border-orange-200 shadow-sm">3</span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-500 font-medium border border-gray-200">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $k->pegawai->nama }}</p>
                                    <p class="text-xs text-gray-500">NIP: {{ $k->pegawai->nip }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end justify-center">
                                        <span class="text-xl font-black text-biru">
                                            {{ number_format($k->skor_final, 2, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-medium">Rata-rata Survei: {{ number_format($k->live_skor, 2, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm italic">Belum ada kandidat di periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daftar Absen -->
            <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden">
                <div class="bg-hijau px-6 py-4 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="font-bold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Daftar Absensi Survei
                        </h3>
                        <p class="text-white/80 text-xs mt-1">Daftar Pegawai & Status Pengisian</p>
                    </div>
                    <div class="flex items-center gap-3 mt-3 md:mt-0">
                        <span class="text-xs text-white/90 font-medium whitespace-nowrap">Unduh TXT:</span>
                        <div class="inline-flex bg-white/10 rounded-lg p-1 shadow-sm">
                            <a href="{{ route('admin.monitoring.download_txt', ['periode_id' => $periode_id, 'filter' => 'semua']) }}" class="text-xs hover:bg-white/20 text-white font-medium px-4 py-1.5 rounded-md transition-colors whitespace-nowrap">Semua</a>
                            <a href="{{ route('admin.monitoring.download_txt', ['periode_id' => $periode_id, 'filter' => 'selesai']) }}" class="text-xs hover:bg-white/20 text-white font-medium px-4 py-1.5 rounded-md transition-colors whitespace-nowrap">Selesai</a>
                            <a href="{{ route('admin.monitoring.download_txt', ['periode_id' => $periode_id, 'filter' => 'belum']) }}" class="text-xs hover:bg-white/20 text-white font-medium px-4 py-1.5 rounded-md transition-colors whitespace-nowrap">Belum</a>
                        </div>
                    </div>
                </div>
                
                <div class="p-0 max-h-[600px] overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-slate-50 shadow-sm z-10 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-slate-600">Nama Pegawai</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-slate-600 text-center">Status</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase tracking-wider text-slate-600 text-right">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($progressPegawai as $p)
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 text-sm">{{ $p['nama'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($p['status'] == 'Selesai')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 shadow-sm border border-green-200">
                                            Selesai
                                        </span>
                                    @elseif($p['status'] == 'Proses')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 shadow-sm border border-orange-200">
                                            Belum Tuntas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 shadow-sm border border-gray-200">
                                            Belum Mulai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm text-gray-600 font-bold bg-slate-100 px-3 py-1 rounded-full">{{ $p['sudah'] }} / {{ $p['target'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        @endif
        
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4">
    <div class="flex justify-between items-center w-full">
        <div>
            <h1 class="text-2xl font-bold text-bps-text drop-shadow-sm">Audit Log</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau aktivitas sistem dan rekam jejak pengguna.</p>
        </div>
        <button form="filterForm" type="submit" name="export" value="1" class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export CSV
        </button>
    </div>
    
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form id="filterForm" action="{{ route('admin.audit.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Search -->
            <div class="lg:col-span-1">
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari log..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full pl-9 p-2">
                </div>
            </div>

            <!-- Modul -->
            <div>
                <label for="module" class="block text-xs font-medium text-gray-700 mb-1">Modul / Subjek</label>
                <select name="module" id="module" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
                    <option value="">Semua Modul</option>
                    @foreach($modulesList as $key => $val)
                        <option value="{{ $key }}" {{ request('module') == $key ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Aktor -->
            <div>
                <label for="causer_id" class="block text-xs font-medium text-gray-700 mb-1">Aktor</label>
                <select name="causer_id" id="causer_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
                    <option value="">Semua Pengguna</option>
                    @foreach($causersList as $causer)
                        <option value="{{ $causer['id'] }}" {{ request('causer_id') == $causer['id'] ? 'selected' : '' }}>{{ $causer['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label for="date_start" class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_start" id="date_start" value="{{ request('date_start') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
            </div>

            <!-- Tanggal Sampai & Tombol -->
            <div class="flex flex-col justify-end">
                <label for="date_end" class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <div class="flex gap-2">
                    <input type="date" name="date_end" id="date_end" value="{{ request('date_end') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2">
                    <button type="submit" class="px-3 py-2 bg-bps-primary hover:bg-bps-primary/90 text-white rounded-lg shadow-sm transition-colors" title="Terapkan Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </button>
                    <a href="{{ route('admin.audit.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg shadow-sm transition-colors flex items-center justify-center" title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="text-sm font-semibold text-gray-800">Menampilkan {{ $activities->total() }} Log Aktivitas</h3>
    </div>
    
    <div class="overflow-x-auto min-h-[50vh]">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-5 py-3 w-40">Waktu</th>
                    <th scope="col" class="px-5 py-3 w-56">Aktor</th>
                    <th scope="col" class="px-5 py-3 min-w-[200px]">Aktivitas</th>
                    <th scope="col" class="px-5 py-3 min-w-[250px]">Konteks / Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 align-top">
                            <div class="text-gray-800 font-medium">{{ $log['time_ago'] }}</div>
                            <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $log['timestamp'] }}</div>
                        </td>
                        <td class="px-5 py-4 align-top">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $log['causer_name'] }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">{{ $log['causer_role'] }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-top">
                            @php
                                $badgeColor = 'bg-gray-100 text-gray-800 border-gray-200';
                                if($log['type'] === 'success') $badgeColor = 'bg-green-100 text-green-800 border-green-200';
                                if($log['type'] === 'warning') $badgeColor = 'bg-blue-100 text-blue-800 border-blue-200';
                                if($log['type'] === 'danger') $badgeColor = 'bg-red-100 text-red-800 border-red-200';
                            @endphp
                            <div class="flex flex-col items-start gap-1.5">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $badgeColor }} uppercase tracking-wider">
                                    {{ $log['type'] === 'info' ? 'SISTEM' : ($log['type'] === 'success' ? 'TAMBAH' : ($log['type'] === 'warning' ? 'UBAH' : 'HAPUS/GAGAL')) }}
                                </span>
                                <span class="font-medium text-gray-800 mt-1">{{ $log['message'] }}</span>
                                @if($log['subject_type'])
                                    <span class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 w-fit">Modul: {{ $log['subject_type'] }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 align-top">
                            @if(!empty($log['context']))
                                <div class="text-xs space-y-2">
                                    @if(isset($log['context']['ip']))
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            <span class="font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">{{ $log['context']['ip'] }}</span>
                                        </div>
                                    @endif
                                    
                                    @php
                                        // Coba identifikasi jika ada perubahan data (attributes dan old)
                                        $hasChanges = isset($log['context']['attributes']) || isset($log['context']['old']);
                                        $hasOtherContext = false;
                                        foreach($log['context'] as $key => $value) {
                                            if(!in_array($key, ['ip', 'attributes', 'old'])) { 
                                                $hasOtherContext = true; 
                                                break; 
                                            }
                                        }
                                    @endphp
                                    
                                    @if($hasChanges || $hasOtherContext)
                                    <details class="group">
                                        <summary class="cursor-pointer text-[10px] font-semibold text-bps-secondary bg-blue-50 px-2 py-1 rounded inline-flex items-center gap-1 hover:bg-blue-100 transition-colors list-none select-none">
                                            <svg class="w-3 h-3 transform group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            Lihat Detail Perubahan
                                        </summary>
                                        <div class="mt-2 text-xs">
                                            @if($hasChanges)
                                                <div class="grid grid-cols-2 gap-2 mb-2">
                                                    @if(isset($log['context']['old']))
                                                        <div class="bg-red-50 p-2 rounded border border-red-100">
                                                            <div class="font-bold text-[10px] text-red-600 uppercase mb-1 border-b border-red-100 pb-1">Sebelum (Old)</div>
                                                            <pre class="whitespace-pre-wrap font-mono text-[10px] text-gray-700 max-h-32 overflow-y-auto custom-scrollbar">{{ json_encode($log['context']['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </div>
                                                    @endif
                                                    @if(isset($log['context']['attributes']))
                                                        <div class="bg-green-50 p-2 rounded border border-green-100 {{ !isset($log['context']['old']) ? 'col-span-2' : '' }}">
                                                            <div class="font-bold text-[10px] text-green-600 uppercase mb-1 border-b border-green-100 pb-1">Sesudah (New)</div>
                                                            <pre class="whitespace-pre-wrap font-mono text-[10px] text-gray-700 max-h-32 overflow-y-auto custom-scrollbar">{{ json_encode($log['context']['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($hasOtherContext)
                                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded text-xs space-y-1.5 mt-2">
                                                    @foreach($log['context'] as $key => $value)
                                                        @if(!in_array($key, ['ip', 'attributes', 'old']))
                                                            <div class="grid grid-cols-[90px_1fr] gap-2 items-start border-b border-gray-100 pb-1 last:border-0 last:pb-0">
                                                                <span class="text-gray-500 capitalize text-[10px]">{{ str_replace('_', ' ', $key) }}</span>
                                                                <span class="text-gray-800 font-medium font-mono text-[10px] break-all">
                                                                    {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Tidak ada detail</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-base font-medium text-gray-600">Tidak ada log aktivitas</p>
                                <p class="text-sm mt-1">Coba sesuaikan filter pencarian atau tanggal.</p>
                                <a href="{{ route('admin.audit.index') }}" class="mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Reset Filter</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($activities->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $activities->links() }}
        </div>
    @endif
</div>

<style>
/* Custom Scrollbar for Pre blocks */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection

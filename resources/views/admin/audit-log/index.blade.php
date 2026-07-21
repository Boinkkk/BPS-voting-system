@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-bps-text drop-shadow-sm">Audit Log Viewer</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau aktivitas sistem dan jejak audit (LUBER JURDIL).</p>
    </div>
    
    <form action="{{ route('admin.audit.index') }}" method="GET" class="flex items-center gap-2">
        <label for="date" class="text-sm font-medium text-gray-700">Pilih Tanggal:</label>
        <select name="date" id="date" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-bps-secondary focus:border-bps-secondary block w-full p-2.5 shadow-sm">
            @if(empty($availableDates))
                <option value="">Tidak ada log tersedia</option>
            @else
                @foreach($availableDates as $date)
                    <option value="{{ $date }}" {{ $selectedDate === $date ? 'selected' : '' }}>
                        {{ $date === 'today' ? 'Hari Ini' : \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                    </option>
                @endforeach
            @endif
        </select>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Log Aktivitas ({{ $selectedDate === 'today' ? 'Hari Ini' : \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }})</h3>
        <span class="text-xs font-medium bg-bps-secondary/10 text-bps-secondary px-3 py-1 rounded-full">
            {{ count($logs) }} Entri
        </span>
    </div>
    
    <div class="overflow-x-auto max-h-[70vh]">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200 sticky top-0 z-10 shadow-sm">
                <tr>
                    <th scope="col" class="px-6 py-3 w-48">Waktu</th>
                    <th scope="col" class="px-6 py-3 w-32">Level</th>
                    <th scope="col" class="px-6 py-3">Aktivitas & Pesan</th>
                    <th scope="col" class="px-6 py-3 w-64">Konteks / IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 whitespace-nowrap align-top">
                            {{ $log['timestamp'] }}
                        </td>
                        <td class="px-6 py-4 align-top">
                            @php
                                $level = strtolower($log['level']);
                                $color = 'bg-gray-100 text-gray-800 border-gray-200';
                                
                                if (in_array($level, ['info', 'notice'])) {
                                    $color = 'bg-blue-100 text-blue-800 border-blue-200';
                                } elseif (in_array($level, ['warning'])) {
                                    $color = 'bg-orange-100 text-orange-800 border-orange-200';
                                } elseif (in_array($level, ['error', 'critical', 'alert', 'emergency'])) {
                                    $color = 'bg-red-100 text-red-800 border-red-200';
                                }
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $color }} uppercase tracking-wider">
                                {{ $log['level'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top font-medium text-gray-800">
                            {{ $log['message'] }}
                        </td>
                        <td class="px-6 py-4 align-top">
                            @if(!empty($log['context']))
                                <div class="text-xs space-y-1">
                                    @if(isset($log['context']['ip']))
                                        <div class="flex items-center gap-1.5 mb-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            <span class="font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">{{ $log['context']['ip'] }}</span>
                                        </div>
                                    @endif
                                    @php
                                        $hasOtherContext = false;
                                        foreach($log['context'] as $key => $value) {
                                            if($key !== 'ip') { $hasOtherContext = true; break; }
                                        }
                                    @endphp
                                    
                                    @if($hasOtherContext)
                                    <details class="mt-2 group">
                                        <summary class="cursor-pointer text-[10px] font-semibold text-bps-secondary bg-blue-50 px-2 py-1 rounded inline-flex items-center gap-1 hover:bg-blue-100 transition-colors list-none select-none">
                                            <svg class="w-3 h-3 transform group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            Lihat Detail
                                        </summary>
                                        <div class="mt-2 p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs space-y-1.5 shadow-inner max-h-32 overflow-y-auto">
                                            @foreach($log['context'] as $key => $value)
                                                @if($key !== 'ip')
                                                    <div class="grid grid-cols-[90px_1fr] gap-2">
                                                        <span class="text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                                        <span class="text-gray-800 font-medium font-mono text-[10px] break-all">
                                                            {{ is_array($value) ? json_encode($value) : (string)$value }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </details>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Tidak ada konteks tambahan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm font-medium text-gray-500">Tidak ada log aktivitas untuk tanggal ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

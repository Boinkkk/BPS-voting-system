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
    
    @livewire('audit-log-table')

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

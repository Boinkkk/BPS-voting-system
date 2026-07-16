@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            Manajemen Tim Penilai & Surat Tugas
        </h2>
        <p class="text-sm text-gray-500 mt-1">Tunjuk 3 orang tim penilai dan cetak surat tugas untuk setiap periode kinerja.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Penunjukan -->
    <div class="lg:col-span-1 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 border-b border-gray-100 bg-bps-bg rounded-t-lg">
            <h3 class="font-medium text-gray-900">Penunjukan Tim Penilai Baru</h3>
        </div>
        <div class="p-5">
            <form action="{{ route('kepala.tim_penilai.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Penilaian</label>
                    <select name="periode_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->status }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab</label>
                    <select name="penanggung_jawab" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($pegawais as $peg)
                            <option value="{{ $peg->id }}">{{ $peg->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ketua Tim</label>
                    <select name="ketua" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($pegawais as $peg)
                            <option value="{{ $peg->id }}">{{ $peg->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anggota Tim</label>
                    <select name="anggota" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($pegawais as $peg)
                            <option value="{{ $peg->id }}">{{ $peg->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#0091d5] text-white py-2 px-4 rounded shadow-sm hover:bg-sky-600 font-medium transition-colors text-sm">
                    Simpan Penunjukan Tim
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Periode -->
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-4 border-b border-gray-100 bg-bps-bg rounded-t-lg">
            <h3 class="font-medium text-gray-900">Daftar Periode & Tim Penilai</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-bps-bg">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim Penilai (Role)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periodes as $p)
                        @php
                            $tim = \App\Models\TimPenilai::where('periode_id', $p->id)->with('pegawai')->get();
                        @endphp
                        <tr class="hover:bg-bps-bg">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($tim->count() > 0)
                                    <ul class="list-disc pl-4 text-xs space-y-1">
                                        @foreach($tim as $t)
                                            <li><span class="font-semibold">{{ $t->peran }}:</span> {{ $t->pegawai->nama }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-red-500 italic">Belum ditunjuk</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($tim->count() > 0)
                                    <a href="{{ route('kepala.tim_penilai.cetak', $p->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-bps-bg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0091d5]">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Surat Tugas
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Belum ada data periode.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

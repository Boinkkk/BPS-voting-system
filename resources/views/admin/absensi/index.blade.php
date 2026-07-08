@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Rekap Absensi Bulanan Pegawai
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any() || session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm text-red-700 list-disc list-inside">
                            @if(session('error')) <li>{{ session('error') }}</li> @endif
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Upload Data Rekap Absensi (Excel)</h3>
                    <p class="text-sm text-gray-500">Unggah file excel berisi rekap absen per bulan.</p>
                </div>
                <a href="{{ route('admin.absensi.template') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                    ⬇️ Download Template Excel
                </a>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.absensi.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
                    @csrf
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Penilaian</label>
                        <select name="periode_id" id="upload_periode" required class="w-full text-sm border-gray-300 rounded-md">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan (1-12)</label>
                        <select name="bulan" id="upload_bulan" required class="w-full text-sm border-gray-300 rounded-md">
                            @php
                                $namaB = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                            @endphp
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $namaB[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx,.xls" required class="w-full text-sm border border-gray-300 rounded-md p-1.5">
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full px-4 py-2.5 bg-[#0091d5] text-white text-sm font-medium rounded-md hover:bg-blue-600">
                            Upload & Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Form Pengaturan Bobot -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border mb-6">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Pengaturan Bobot Penalti</h3>
                        <p class="text-xs text-gray-500 mt-1">Sesuaikan besaran potongan poin untuk setiap jenis absensi.</p>
                    </div>
                    <div class="p-4">
                        <form action="{{ route('admin.absensi.bobot') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            @php
                                $groupedBobots = $bobots->groupBy('kategori');
                            @endphp
                            
                            @foreach($groupedBobots as $kategori => $items)
                                <div>
                                    <h4 class="font-semibold text-sm text-gray-700 border-b pb-1 mb-2">Penalti {{ $kategori }}</h4>
                                    @foreach($items as $bobot)
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700" title="{{ $bobot->keterangan }}">{{ $bobot->kode_absen }}</label>
                                        </div>
                                        <div class="w-16">
                                            <input type="number" step="0.01" min="0" name="bobots[{{ $bobot->id }}]" value="{{ $bobot->bobot }}" class="w-full text-xs border-gray-300 rounded py-1 px-2 text-right focus:ring-sky-500 focus:border-sky-500">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endforeach
                            
                            <div class="pt-2 border-t">
                                <button type="submit" class="w-full px-3 py-2 bg-amber-500 text-white text-sm font-medium rounded hover:bg-amber-600 transition-colors">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Data Absensi -->
            <div class="lg:col-span-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Data Absensi Tersimpan</h3>
                        
                        <form action="{{ route('admin.absensi.index') }}" method="GET" class="flex space-x-2">
                            <select name="periode_id" id="filter_periode" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                                @foreach ($periodes as $p)
                                    <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            <select name="bulan" id="filter_bulan" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                                @php
                                    $namaB = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                                @endphp
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $namaB[$i] }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-3 font-semibold text-sm">Nama Pegawai</th>
                                    <th class="p-3 font-semibold text-sm text-center">HK</th>
                                    <th class="p-3 font-semibold text-sm text-center">HD</th>
                                    <th class="p-3 font-semibold text-sm text-center text-red-600">TK</th>
                                    <th class="p-3 font-semibold text-sm text-center">TL</th>
                                    <th class="p-3 font-semibold text-sm text-center">PSW (Total)</th>
                                    <th class="p-3 font-semibold text-sm text-center text-red-600">KJK (Menit)</th>
                                    <th class="p-3 font-semibold text-sm text-center text-amber-600">Skor Absensi (Penalti)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensis as $absen)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-3 text-sm font-medium">{{ $absen->pegawai->nama }}</td>
                                        <td class="p-3 text-sm text-center">{{ $absen->hk }}</td>
                                        <td class="p-3 text-sm text-center font-bold text-green-600">{{ $absen->hd }}</td>
                                        <td class="p-3 text-sm text-center font-bold text-red-600">{{ $absen->tk }}</td>
                                        <td class="p-3 text-sm text-center">{{ $absen->tl }}</td>
                                        <td class="p-3 text-sm text-center">{{ $absen->psw }}</td>
                                        <td class="p-3 text-sm text-center font-bold text-red-600">{{ $absen->kjk }}</td>
                                        <td class="p-3 text-sm text-center font-bold {{ $absen->penalti < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($absen->penalti, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-sm text-gray-500">Belum ada data absensi untuk periode dan bulan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bulanNames = {
            1: 'Januari', 2: 'Februari', 3: 'Maret',
            4: 'April', 5: 'Mei', 6: 'Juni',
            7: 'Juli', 8: 'Agustus', 9: 'September',
            10: 'Oktober', 11: 'November', 12: 'Desember'
        };

        function updateBulanOptions(periodeSelectId, bulanSelectId) {
            const periodeSelect = document.getElementById(periodeSelectId);
            const bulanSelect = document.getElementById(bulanSelectId);
            if(!periodeSelect || !bulanSelect) return;

            const selectedOption = periodeSelect.options[periodeSelect.selectedIndex];
            const triwulan = selectedOption.getAttribute('data-triwulan');
            
            let allowedMonths = [1,2,3,4,5,6,7,8,9,10,11,12];
            if (triwulan == '1') allowedMonths = [1, 2, 3];
            if (triwulan == '2') allowedMonths = [4, 5, 6];
            if (triwulan == '3') allowedMonths = [7, 8, 9];
            if (triwulan == '4') allowedMonths = [10, 11, 12];

            const currentVal = bulanSelect.value;
            bulanSelect.innerHTML = '';
            
            allowedMonths.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = bulanNames[m];
                if (m == currentVal) opt.selected = true;
                bulanSelect.appendChild(opt);
            });
        }

        const uploadPeriode = document.getElementById('upload_periode');
        if (uploadPeriode) {
            uploadPeriode.addEventListener('change', () => updateBulanOptions('upload_periode', 'upload_bulan'));
            updateBulanOptions('upload_periode', 'upload_bulan');
        }

        const filterPeriode = document.getElementById('filter_periode');
        if (filterPeriode) {
            // Kita tidak menambah listener change pada filter_periode 
            // karena ada onchange="this.form.submit()" bawaan form yang akan me-reload halaman
            updateBulanOptions('filter_periode', 'filter_bulan');
        }
    });
</script>
@endpush

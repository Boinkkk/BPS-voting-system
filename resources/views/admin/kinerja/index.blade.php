@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
            {{ __('Manajemen Data Kinerja') }}
        </h2>
    </div>

    <div class="py-12" style="font-family: 'Hanken Grotesk', sans-serif;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Control Panel: Filter & Upload -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    
                    <!-- Filter Periode -->
                    <form method="GET" action="{{ route('admin.kinerja.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                        <label for="periode_id" class="text-sm font-medium text-gray-700">Periode:</label>
                        <select name="periode_id" id="periode_id" class="border-gray-300 rounded-md shadow-sm focus:border-[#0091d5] focus:ring focus:ring-[#0091d5] focus:ring-opacity-50 text-sm flex-grow">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('M Y') }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Filter</button>
                    </form>

                    <!-- Upload Excel Form -->
                    <form action="{{ route('admin.kinerja.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto bg-bps-bg p-4 rounded-md border">
                        @csrf
                        <div class="w-full sm:w-auto">
                            <label class="block text-xs text-gray-500 mb-1">Target Periode</label>
                            <select name="periode_id" required class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="sekarang">Periode Sekarang (Sesuai Tanggal Aktif)</option>
                                @foreach ($periodes as $p)
                                    <option value="{{ $p->id }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full">
                            <label class="block text-xs text-gray-500 mb-1">Upload Data (Excel/CSV)</label>
                            <input type="file" name="file" required class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-[#0091d5] file:text-white
                                hover:file:bg-[#007bba] cursor-pointer" />
                        </div>
                        <button type="submit" class="mt-2 sm:mt-5 bg-[#76bc21] hover:bg-[#629c1c] text-white px-4 py-2 rounded-md text-sm w-full sm:w-auto transition-colors font-medium">
                            Upload
                        </button>
                    </form>
                </div>
                
                <div class="mt-4 flex gap-2 border-t pt-4">
                    <button onclick="openModal('manualModal')" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors">
                        + Input Kinerja Pegawai (Manual)
                    </button>
                </div>
            </div>

            <!-- Data Kinerja Table / Cards -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 text-[#1d1d1b]">Data Kinerja Pegawai</h3>
                    
                    @if(count($kinerja) > 0)
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 border-b">
                                        <th class="p-3 font-semibold text-sm">Nama Pegawai</th>
                                        <th class="p-3 font-semibold text-sm">Bulan</th>
                                        <th class="p-3 font-semibold text-sm">Rata-rata Hasil Kerja</th>
                                        <th class="p-3 font-semibold text-sm">Rata-rata Perilaku</th>
                                        <th class="p-3 font-semibold text-sm">Nilai KJK</th>
                                        <th class="p-3 font-semibold text-sm">Nilai TL & PSW</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                                    @endphp
                                    @foreach($kinerja as $k)
                                        <tr class="border-b hover:bg-bps-bg">
                                            <td class="p-3 text-sm font-medium">{{ $k->pegawai->nama }}</td>
                                            <td class="p-3 text-sm">{{ $namaBulan[$k->bulan] ?? '-' }}</td>
                                            <td class="p-3 text-sm">{{ number_format($k->rata_rata_hasil_kerja, 2, ',', '.') }}</td>
                                            <td class="p-3 text-sm">{{ number_format($k->rata_rata_perilaku, 2, ',', '.') }}</td>
                                            <td class="p-3 text-sm">{{ $k->nilai_kjk !== null ? number_format($k->nilai_kjk, 2, ',', '.') : '-' }}</td>
                                            <td class="p-3 text-sm">{{ $k->nilai_tl_psw !== null ? number_format($k->nilai_tl_psw, 2, ',', '.') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="block md:hidden space-y-4">
                            @foreach($kinerja as $k)
                                <div class="bg-white border rounded-lg p-4 shadow-sm">
                                    <div class="font-bold text-[#0091d5] mb-2">{{ $k->pegawai->nama }} <span class="text-xs font-normal text-gray-500">({{ $namaBulan[$k->bulan] ?? '-' }})</span></div>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="text-gray-600">Hasil Kerja:</div>
                                        <div class="font-medium text-right">{{ number_format($k->rata_rata_hasil_kerja, 2, ',', '.') }}</div>
                                        
                                        <div class="text-gray-600">Perilaku:</div>
                                        <div class="font-medium text-right">{{ number_format($k->rata_rata_perilaku, 2, ',', '.') }}</div>
                                        
                                        <div class="text-gray-600">Nilai KJK:</div>
                                        <div class="font-medium text-right">{{ $k->nilai_kjk !== null ? number_format($k->nilai_kjk, 2, ',', '.') : '-' }}</div>
                                        
                                        <div class="text-gray-600">TL & PSW:</div>
                                        <div class="font-medium text-right">{{ $k->nilai_tl_psw !== null ? number_format($k->nilai_tl_psw, 2, ',', '.') : '-' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            Belum ada data kinerja untuk periode ini. Silakan upload file Excel.
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>


    <!-- Manual Kinerja Modal -->
    <div id="manualModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Input Data Kinerja Pegawai</h3>
                <button onclick="closeModal('manualModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form action="{{ route('admin.kinerja.manual') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Target Periode</label>
                    <select name="periode_id" id="manual_periode" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="sekarang" data-triwulan="">Periode Sekarang (Sesuai Tanggal Aktif)</option>
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}" data-triwulan="{{ $p->triwulan }}" {{ $periode_id == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Pilih Pegawai</label>
                    <select name="id_pegawai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih Pegawai --</option>
                        @isset($semuaPegawai)
                            @foreach($semuaPegawai as $peg)
                                <option value="{{ $peg->id }}">{{ $peg->nama }} ({{ $peg->nip }})</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Pilih Bulan</label>
                    <select name="bulan" id="manual_bulan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Rata-rata Hasil Kerja</label>
                        <input type="number" step="0.01" name="rata_rata_hasil_kerja" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Rata-rata Perilaku</label>
                        <input type="number" step="0.01" name="rata_rata_perilaku" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nilai KJK</label>
                        <input type="number" step="0.01" name="nilai_kjk" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nilai TL & PSW</label>
                        <input type="number" step="0.01" name="nilai_tl_psw" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Simpan Kinerja</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

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

            const manualPeriode = document.getElementById('manual_periode');
            if (manualPeriode) {
                manualPeriode.addEventListener('change', () => updateBulanOptions('manual_periode', 'manual_bulan'));
                updateBulanOptions('manual_periode', 'manual_bulan');
            }
        });
    </script>
@endsection

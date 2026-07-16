@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl leading-tight" style="color: #0091d5; font-family: 'Hanken Grotesk', sans-serif;">
            {{ __('Manajemen Periode') }}
        </h2>
    </div>

    <div class="py-12" style="font-family: 'Hanken Grotesk', sans-serif;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-md shadow-sm" role="alert">
                    <p class="font-bold mb-1">Terjadi Kesalahan Validasi</p>
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-md shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-md shadow-sm" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <button onclick="openModal('addPeriodeModal')" class="bg-[#0D8ABC] hover:bg-sky-800 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors">
                    + Tambah Periode Baru
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0">
                <div class="p-6">
                    
                    @if(count($periodes) > 0)
                        <div class="overflow-x-auto rounded-2xl bg-white shadow-lg ring-1 ring-gray-200">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Periode</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Masa Persiapan</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Masa Voting</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Tanggal Selesai</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($periodes as $p)
                                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                                            <td class="px-6 py-5 text-sm font-semibold text-slate-800">{{ $p->nama }}</td>
                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}<br>
                                                <span class="text-xs text-slate-400">s/d</span><br>
                                                {{ \Carbon\Carbon::parse($p->tanggal_selesai_persiapan ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(4))->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-5 text-sm text-slate-600">
                                                {{ \Carbon\Carbon::parse($p->tanggal_mulai_voting ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(5))->format('d M Y') }}<br>
                                                <span class="text-xs text-slate-400">s/d</span><br>
                                                {{ \Carbon\Carbon::parse($p->tanggal_selesai_voting ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(7))->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-5 text-sm text-slate-600">{{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}</td>
                                            <td class="px-6 py-5 text-center">
                                                @php
                                                    $statusColors = [
                                                        'penginputan' => 'bg-blue-100 text-blue-800',
                                                        'voting' => 'bg-orange-100 text-orange-800',
                                                        'review_kepala' => 'bg-purple-100 text-purple-800',
                                                        'selesai' => 'bg-green-100 text-green-800',
                                                        'menunggu' => 'bg-gray-100 text-gray-800'
                                                    ];
                                                    $colorClass = $statusColors[$p->status] ?? 'bg-slate-100 text-slate-800';
                                                @endphp
                                                <span class="inline-flex px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full {{ $colorClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-center space-x-2 whitespace-nowrap">
                                                <button onclick="openEditModal({{ $p->id }}, '{{ $p->triwulan }}', '{{ $p->tahun }}', '{{ $p->tanggal_mulai }}', '{{ $p->tanggal_selesai_persiapan ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(4)->format('Y-m-d') }}', '{{ $p->tanggal_mulai_voting ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(5)->format('Y-m-d') }}', '{{ $p->tanggal_selesai_voting ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(7)->format('Y-m-d') }}', '{{ $p->tanggal_review_kepala ?? \Carbon\Carbon::parse($p->tanggal_mulai)->addDays(8)->format('Y-m-d') }}', '{{ $p->tanggal_selesai }}')" class="text-sky-600 hover:text-sky-800 font-medium transition-colors p-2 bg-sky-50 rounded-md hover:bg-sky-100 text-sm px-3">Edit</button>
                                                <form action="{{ route('admin.periode.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition-colors p-2 bg-red-50 rounded-md hover:bg-red-100 text-sm px-3">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 p-10 text-center">
                            <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-4xl mb-4">📋</div>
                            <h3 class="font-semibold text-slate-700">Belum Ada Data Periode</h3>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Add Periode Modal -->
    <div id="addPeriodeModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl transition-all w-full sm:max-w-md mx-auto" style="min-width: min(100%, 400px);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Tambah Periode Penilaian</h3>
                <button onclick="closeModal('addPeriodeModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form action="{{ route('admin.periode.store') }}" method="POST">
                @csrf
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Triwulan</label>
                        <select name="triwulan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="1">Triwulan 1 (Jan - Mar)</option>
                            <option value="2">Triwulan 2 (Apr - Jun)</option>
                            <option value="3">Triwulan 3 (Jul - Sep)</option>
                            <option value="4">Triwulan 4 (Okt - Des)</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="tahun" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ date('Y') }}" min="2000">
                    </div>
                </div>
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Mulai Persiapan</label>
                        <input type="date" name="tanggal_mulai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Selesai Persiapan</label>
                        <input type="date" name="tanggal_selesai_persiapan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Mulai Voting</label>
                        <input type="date" name="tanggal_mulai_voting" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Selesai Voting</label>
                        <input type="date" name="tanggal_selesai_voting" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Review Kepala</label>
                        <input type="date" name="tanggal_review_kepala" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Pengumuman</label>
                        <input type="date" name="tanggal_selesai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded shadow hover:bg-sky-700">Simpan</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>

    <!-- Edit Periode Modal -->
    <div id="editPeriodeModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl transition-all w-full sm:max-w-md mx-auto" style="min-width: min(100%, 400px);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Edit Periode Penilaian</h3>
                <button onclick="closeModal('editPeriodeModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="editPeriodeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Triwulan</label>
                        <select name="triwulan" id="edit_triwulan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="1">Triwulan 1 (Jan - Mar)</option>
                            <option value="2">Triwulan 2 (Apr - Jun)</option>
                            <option value="3">Triwulan 3 (Jul - Sep)</option>
                            <option value="4">Triwulan 4 (Okt - Des)</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="tahun" id="edit_tahun" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="2000">
                    </div>
                </div>
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Mulai Persiapan</label>
                        <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Selesai Persiapan</label>
                        <input type="date" name="tanggal_selesai_persiapan" id="edit_tanggal_selesai_persiapan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Mulai Voting</label>
                        <input type="date" name="tanggal_mulai_voting" id="edit_tanggal_mulai_voting" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Selesai Voting</label>
                        <input type="date" name="tanggal_selesai_voting" id="edit_tanggal_selesai_voting" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="mb-4 flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Review Kepala</label>
                        <input type="date" name="tanggal_review_kepala" id="edit_tanggal_review_kepala" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Tgl Pengumuman</label>
                        <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-bps-secondary text-white px-4 py-2 rounded shadow hover:bg-bps-secondary/90">Simpan Perubahan</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        // setTimeout for transition
        setTimeout(() => {
            modal.children[0].classList.add('opacity-100');
            modal.children[1].children[0].classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 50);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.children[0].classList.remove('opacity-100');
        modal.children[1].children[0].classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openEditModal(id, triwulan, tahun, tanggal_mulai, tanggal_selesai_persiapan, tanggal_mulai_voting, tanggal_selesai_voting, tanggal_review_kepala, tanggal_selesai, status) {
        document.getElementById('edit_triwulan').value = triwulan;
        document.getElementById('edit_tahun').value = tahun;
        document.getElementById('edit_tanggal_mulai').value = tanggal_mulai;
        document.getElementById('edit_tanggal_selesai_persiapan').value = tanggal_selesai_persiapan;
        document.getElementById('edit_tanggal_mulai_voting').value = tanggal_mulai_voting;
        document.getElementById('edit_tanggal_selesai_voting').value = tanggal_selesai_voting;
        document.getElementById('edit_tanggal_review_kepala').value = tanggal_review_kepala;
        document.getElementById('edit_tanggal_selesai').value = tanggal_selesai;
        
        // Update form action
        const form = document.getElementById('editPeriodeForm');
        form.action = `/admin/periode/${id}`;
        
        openModal('editPeriodeModal');
    }

    // Real-time Date Validation Logic
    function setupDateValidation(formPrefix) {
        const tglMulai = document.getElementById(formPrefix + 'tanggal_mulai');
        const tglSelesaiPersiapan = document.getElementById(formPrefix + 'tanggal_selesai_persiapan');
        const tglMulaiVoting = document.getElementById(formPrefix + 'tanggal_mulai_voting');
        const tglSelesaiVoting = document.getElementById(formPrefix + 'tanggal_selesai_voting');
        const tglReviewKepala = document.getElementById(formPrefix + 'tanggal_review_kepala');
        const tglSelesai = document.getElementById(formPrefix + 'tanggal_selesai');
        
        if (!tglMulai) return; // If elements don't have IDs (like in add modal, we'll need to add IDs)

        function enforceMinDates() {
            if (tglMulai.value) {
                tglSelesaiPersiapan.min = tglMulai.value;
                if(tglSelesaiPersiapan.value < tglMulai.value) tglSelesaiPersiapan.value = tglMulai.value;
            }
            if (tglSelesaiPersiapan.value) {
                // Add 1 day for next phase
                let nextDate = new Date(tglSelesaiPersiapan.value);
                nextDate.setDate(nextDate.getDate() + 1);
                let nextDateStr = nextDate.toISOString().split('T')[0];
                tglMulaiVoting.min = nextDateStr;
                if(tglMulaiVoting.value < nextDateStr) tglMulaiVoting.value = nextDateStr;
            }
            if (tglMulaiVoting.value) {
                tglSelesaiVoting.min = tglMulaiVoting.value;
                if(tglSelesaiVoting.value < tglMulaiVoting.value) tglSelesaiVoting.value = tglMulaiVoting.value;
            }
            if (tglSelesaiVoting.value) {
                let nextDate = new Date(tglSelesaiVoting.value);
                nextDate.setDate(nextDate.getDate() + 1);
                let nextDateStr = nextDate.toISOString().split('T')[0];
                tglReviewKepala.min = nextDateStr;
                if(tglReviewKepala.value < nextDateStr) tglReviewKepala.value = nextDateStr;
            }
            if (tglReviewKepala.value) {
                let nextDate = new Date(tglReviewKepala.value);
                nextDate.setDate(nextDate.getDate() + 1);
                let nextDateStr = nextDate.toISOString().split('T')[0];
                tglSelesai.min = nextDateStr;
                if(tglSelesai.value < nextDateStr) tglSelesai.value = nextDateStr;
            }
        }

        tglMulai.addEventListener('change', enforceMinDates);
        tglSelesaiPersiapan.addEventListener('change', enforceMinDates);
        tglMulaiVoting.addEventListener('change', enforceMinDates);
        tglSelesaiVoting.addEventListener('change', enforceMinDates);
        tglReviewKepala.addEventListener('change', enforceMinDates);
        tglSelesai.addEventListener('change', enforceMinDates);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // We will assign IDs to add modal inputs dynamically for validation
        const addForm = document.querySelector('#addPeriodeModal form');
        if (addForm) {
            addForm.querySelector('input[name="tanggal_mulai"]').id = 'add_tanggal_mulai';
            addForm.querySelector('input[name="tanggal_selesai_persiapan"]').id = 'add_tanggal_selesai_persiapan';
            addForm.querySelector('input[name="tanggal_mulai_voting"]').id = 'add_tanggal_mulai_voting';
            addForm.querySelector('input[name="tanggal_selesai_voting"]').id = 'add_tanggal_selesai_voting';
            addForm.querySelector('input[name="tanggal_review_kepala"]').id = 'add_tanggal_review_kepala';
            addForm.querySelector('input[name="tanggal_selesai"]').id = 'add_tanggal_selesai';
            setupDateValidation('add_');
        }
        setupDateValidation('edit_');
    });
</script>
@endpush
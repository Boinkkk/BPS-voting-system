@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold leading-tight text-gray-800">
    Pengaturan Bobot Penilaian
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any() || session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Konfigurasi Bobot (Total harus 100%)</h3>
                <p class="text-sm text-gray-500">Persentase bobot ini akan menentukan perhitungan ranking Fase 1 dan Fase 3.</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.pengaturan-bobot.update') }}" method="POST" id="bobotForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bobot CKP (%)</label>
                            <input type="number" name="ckp" id="ckp" value="{{ $bobot->ckp }}" min="0" max="100" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bobot-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bobot Absensi (%)</label>
                            <input type="number" name="absensi" id="absensi" value="{{ $bobot->absensi }}" min="0" max="100" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bobot-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bobot Survei (%)</label>
                            <input type="number" name="survey" id="survey" value="{{ $bobot->survey }}" min="0" max="100" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bobot-input">
                        </div>
                    </div>

                    <div class="flex items-center mb-6">
                        <span class="text-sm font-medium text-gray-700 mr-2">Total Saat Ini:</span>
                        <span id="totalBadge" class="px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800">100%</span>
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Bobot Pengurangan Nilai Absensi Akhir</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Bobot HT Dihapus -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bobot TK</label>
                                <input type="number" step="0.01" name="bobot_tk" value="{{ $bobot->bobot_tk ?? 2 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Default 2.</p>
                            </div>
                            <div class="md:col-span-1 lg:col-span-2"></div>
                            
                            <!-- PSW -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bobot Total PSW</label>
                                <input type="number" step="0.01" name="bobot_psw" value="{{ $bobot->bobot_psw ?? 1 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Dipakai jika bobot PSW 1-4 bernilai 0.</p>
                            </div>
                            <div class="col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">PSW 1</label>
                                    <input type="number" step="0.01" name="bobot_psw1" value="{{ $bobot->bobot_psw1 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">PSW 2</label>
                                    <input type="number" step="0.01" name="bobot_psw2" value="{{ $bobot->bobot_psw2 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">PSW 3</label>
                                    <input type="number" step="0.01" name="bobot_psw3" value="{{ $bobot->bobot_psw3 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">PSW 4</label>
                                    <input type="number" step="0.01" name="bobot_psw4" value="{{ $bobot->bobot_psw4 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- TL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bobot Total TL</label>
                                <input type="number" step="0.01" name="bobot_tl" value="{{ $bobot->bobot_tl ?? 1 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Dipakai jika bobot TL 1-4 bernilai 0.</p>
                            </div>
                            <div class="col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">TL 1</label>
                                    <input type="number" step="0.01" name="bobot_tl1" value="{{ $bobot->bobot_tl1 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">TL 2</label>
                                    <input type="number" step="0.01" name="bobot_tl2" value="{{ $bobot->bobot_tl2 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">TL 3</label>
                                    <input type="number" step="0.01" name="bobot_tl3" value="{{ $bobot->bobot_tl3 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">TL 4</label>
                                    <input type="number" step="0.01" name="bobot_tl4" value="{{ $bobot->bobot_tl4 ?? 0 }}" min="0" class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t pt-4">
                        <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ckp = document.getElementById('ckp');
        const absensi = document.getElementById('absensi');
        const survey = document.getElementById('survey');
        const totalBadge = document.getElementById('totalBadge');
        const submitBtn = document.getElementById('submitBtn');

        function updateTotal() {
            const sum = (parseInt(ckp.value) || 0) + (parseInt(absensi.value) || 0) + (parseInt(survey.value) || 0);
            totalBadge.textContent = sum + '%';
            
            if (sum === 100) {
                totalBadge.classList.remove('bg-red-100', 'text-red-800');
                totalBadge.classList.add('bg-green-100', 'text-green-800');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                totalBadge.classList.remove('bg-green-100', 'text-green-800');
                totalBadge.classList.add('bg-red-100', 'text-red-800');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        ckp.addEventListener('input', updateTotal);
        absensi.addEventListener('input', updateTotal);
        survey.addEventListener('input', updateTotal);
        
        // Initial check
        updateTotal();
    });
</script>
@endpush

@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
@endif
@if($errors->any())
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
        <div class="flex items-center mb-2">
            <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <p class="text-sm font-bold text-red-800">Terdapat kesalahan:</p>
        </div>
        <ul class="list-disc list-inside text-sm text-red-700 ml-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Header Banner & Profile Info -->
<div class="bg-surface rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-surface-variant overflow-hidden mb-8 relative transition-all duration-300">
    <div class="h-48 bg-gradient-to-r from-primary via-primary-container to-biru w-full relative overflow-hidden">
        <!-- Abstract Decoration Pattern -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute top-12 left-1/4 w-32 h-32 bg-secondary-container opacity-20 rounded-full blur-2xl"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+CjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz4KPHBhdGggZD0iTTAgMGwyMCAyMEw0MCAwaC0xTDIwIDE5LjUgMSAwem0wIDQwbDIwLTIwTDAgMHYxbDE5LjUgMjBMMCAzOXptNDAgMGwtMjAtMjBMMCA0MGgxTDIwIDIwLjUgMzkgNDB6bTAtNDBMMjAgMjAgNDAgNDB2LTFsLTE5LjUtMjBMMzkgMHoiIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPgo8L3N2Zz4=')] opacity-30"></div>
    </div>
    
    <div class="px-6 md:px-10 pb-8 relative">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end -mt-20 md:-mt-16 mb-4 gap-4">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-5 md:gap-6">
                <div class="w-36 h-36 md:w-40 md:h-40 rounded-2xl bg-surface p-1.5 shadow-xl border border-surface-variant z-10 relative group">
                    <img src="{{ $user->foto_profil_url }}" alt="Profile" class="w-full h-full rounded-xl object-cover">
                    <!-- Overlay on hover for changing photo -->
                    <label for="foto_profil" class="absolute inset-1.5 rounded-xl bg-black/50 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center cursor-pointer transition-opacity backdrop-blur-sm text-white">
                        <svg class="w-8 h-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        <span class="text-xs font-semibold">Ubah Foto</span>
                    </label>
                </div>
                
                <div class="text-center md:text-left mb-2">
                    <h1 class="text-3xl font-extrabold text-on-surface mb-1">{{ $user->nama }}</h1>
                    <div class="flex items-center justify-center md:justify-start text-on-surface-variant bg-surface-container-low py-1.5 px-3 rounded-full border border-outline-variant/30 w-fit mx-auto md:mx-0">
                        <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <span class="text-sm font-semibold tracking-wide">NIP: {{ $user->nip ?? '-' }}</span>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="flex items-center justify-center mb-2" id="photoForm">
                @csrf
                <input type="file" name="foto_profil" id="foto_profil" class="hidden" accept="image/*">
                <!-- Desktop button (hidden on mobile, replaced by hover overlay) -->
                <label for="foto_profil" class="hidden md:flex bg-primary hover:bg-on-primary-fixed-variant text-on-primary px-5 py-2.5 rounded-xl shadow-md shadow-primary/20 text-sm font-bold transition-all hover:-translate-y-0.5 active:translate-y-0 cursor-pointer items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Unggah Foto
                </label>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    <!-- Employee Details -->
    <div class="lg:col-span-2 bg-surface rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-surface-variant p-6 md:p-8 hover:shadow-lg transition-shadow duration-300">
        <div class="flex justify-between items-center mb-8 border-b border-surface-container pb-4">
            <h2 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Detail Pegawai
            </h2>
            <span class="bg-secondary-container text-on-secondary-container text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 border border-secondary-fixed shadow-sm">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                Aktif
            </span>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-10 mb-2">
            <div class="group">
                <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1.5">Posisi / Jabatan</p>
                <div class="flex items-center gap-3 bg-surface-container-low p-3 rounded-xl border border-transparent group-hover:border-surface-variant transition-colors">
                    <div class="p-2 bg-surface-container rounded-lg text-primary">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <p class="text-base font-bold text-on-surface">{{ $user->jabatan ?? '-' }}</p>
                </div>
            </div>
            
            <div class="group">
                <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1.5">Departemen</p>
                <div class="flex items-center gap-3 bg-surface-container-low p-3 rounded-xl border border-transparent group-hover:border-surface-variant transition-colors">
                    <div class="p-2 bg-surface-container rounded-lg text-biru">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <p class="text-base font-bold text-on-surface">{{ $user->departemen ? $user->departemen->nama : '-' }}</p>
                </div>
            </div>
            
            <div class="group sm:col-span-2">
                <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1.5">Alamat Email</p>
                <div class="flex items-center gap-3 bg-surface-container-low p-3 rounded-xl border border-transparent group-hover:border-surface-variant transition-colors">
                    <div class="p-2 bg-surface-container rounded-lg text-orange">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <p class="text-base font-bold text-on-surface">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Preferences -->
    <div class="bg-surface rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-surface-variant p-6 md:p-8 hover:shadow-lg transition-shadow duration-300">
        <h2 class="text-xl font-extrabold text-on-surface mb-6 border-b border-surface-container pb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-on-surface-variant" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Keamanan Akun
        </h2>
        
        <div class="grid grid-cols-1">
            <!-- Card 1 -->
            <button type="button" onclick="document.getElementById('passwordModal').classList.remove('hidden')" class="w-full text-left p-5 rounded-xl border border-surface-variant bg-surface-container-low hover:border-primary hover:bg-primary-fixed/30 hover:shadow-md transition-all duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-surface-container p-2.5 rounded-lg text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <div class="bg-surface p-1.5 rounded-full border border-surface-variant text-outline-variant group-hover:text-primary group-hover:border-primary/30 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-base font-bold text-on-surface mb-1.5 group-hover:text-primary transition-colors">Ubah Password</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
            </button>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('passwordModal').classList.add('hidden')"></div>

    <!-- Modal Panel -->
    <div class="relative bg-surface rounded-2xl text-left overflow-hidden shadow-2xl w-full max-w-md mx-auto flex flex-col z-10 border border-surface-variant transform transition-all">
        <div class="flex justify-between items-center px-6 pt-6 pb-4 border-b border-surface-variant">
            <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2" id="modal-title">
                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Ubah Password
            </h3>
            <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface focus:outline-none bg-surface-container hover:bg-surface-variant p-2 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-5 bg-surface-container-low">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" required class="w-full bg-surface border border-outline-variant rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-on-surface placeholder:text-outline/50 shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-outline">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" required class="w-full bg-surface border border-outline-variant rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-on-surface placeholder:text-outline/50 shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-outline">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" required class="w-full bg-surface border border-outline-variant rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-on-surface placeholder:text-outline/50 shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-outline">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-surface px-6 py-5 sm:flex sm:flex-row-reverse border-t border-surface-variant gap-3">
                <button type="submit" class="w-full inline-flex justify-center rounded-xl shadow-md shadow-primary/20 px-5 py-3 bg-primary text-base font-bold text-on-primary hover:bg-on-primary-fixed-variant hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:w-auto sm:text-sm transition-all active:translate-y-0.5">
                    Simpan Perubahan
                </button>
                <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-outline-variant shadow-sm px-5 py-3 bg-surface text-base font-semibold text-on-surface hover:bg-surface-container hover:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-outline sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cropper Modal -->
<div id="cropperModal" class="fixed inset-0 z-[150] hidden flex items-center justify-center p-4 sm:p-6" aria-labelledby="cropper-modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-inverse-surface/60 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="closeCropperModal()"></div>

    <!-- Modal Panel -->
    <div class="relative bg-surface rounded-2xl text-left overflow-hidden shadow-2xl w-full max-w-2xl mx-auto flex flex-col z-10 border border-surface-variant" style="min-width: 280px; max-height: 90vh;">
        <div class="flex justify-between items-center px-6 pt-6 pb-4 border-b border-surface-variant">
            <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2" id="cropper-modal-title">
                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Sesuaikan Foto Profil
            </h3>
            <button type="button" onclick="closeCropperModal()" class="text-on-surface-variant hover:text-on-surface focus:outline-none bg-surface-container hover:bg-surface-variant p-2 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="p-6 bg-surface-container-highest flex justify-center items-center overflow-hidden border-b border-surface-variant" style="max-height: 60vh;">
            <div class="w-full max-w-md rounded-xl overflow-hidden shadow-inner">
                <img id="imageToCrop" src="" alt="Picture" class="max-w-full block">
            </div>
        </div>

        <div class="bg-surface px-6 py-5 sm:flex sm:flex-row-reverse gap-3">
            <button type="button" id="cropAndUploadBtn" class="w-full inline-flex justify-center items-center rounded-xl shadow-md shadow-primary/20 px-6 py-3 bg-primary text-base font-bold text-on-primary hover:bg-on-primary-fixed-variant hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:w-auto sm:text-sm transition-all active:translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Crop & Simpan</span>
            </button>
            <button type="button" onclick="closeCropperModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-outline-variant shadow-sm px-6 py-3 bg-surface text-base font-semibold text-on-surface hover:bg-surface-container hover:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-outline sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cropper;
    const fileInput = document.getElementById('foto_profil');
    const cropperModal = document.getElementById('cropperModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const photoForm = document.getElementById('photoForm');
    
    fileInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            
            reader.onload = function(event) {
                imageToCrop.src = event.target.result;
                cropperModal.classList.remove('hidden');
                
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1, // Profile pictures are usually square
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                });
            };
            
            reader.readAsDataURL(file);
        }
    });

    function closeCropperModal() {
        cropperModal.classList.add('hidden');
        fileInput.value = ''; // Reset input so same file can be selected again
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    document.getElementById('cropAndUploadBtn').addEventListener('click', function() {
        if (!cropper) return;
        
        // Show loading state
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Memproses...</span>';
        btn.disabled = true;

        cropper.getCroppedCanvas({
            width: 600,
            height: 600,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }).toBlob(function(blob) {
            // Create a new File object
            const croppedFile = new File([blob], "profile_cropped.jpg", {
                type: "image/jpeg",
                lastModified: new Date().getTime()
            });

            // Put it in the file input using DataTransfer
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            fileInput.files = dataTransfer.files;

            // Submit the form
            photoForm.submit();
        }, 'image/jpeg', 0.9);
    });
</script>
@endpush
@endsection

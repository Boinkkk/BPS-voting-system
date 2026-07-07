<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');

    // Admin Kinerja Management
    Route::get('/admin/kinerja', [\App\Http\Controllers\KinerjaAdminController::class, 'index'])->name('admin.kinerja.index');
    Route::post('/admin/kinerja/upload', [\App\Http\Controllers\KinerjaAdminController::class, 'upload'])->name('admin.kinerja.upload');
    Route::post('/admin/kinerja/manual', [\App\Http\Controllers\KinerjaAdminController::class, 'storeManual'])->name('admin.kinerja.manual');
    Route::post('/admin/kinerja/periode', [\App\Http\Controllers\KinerjaAdminController::class, 'storePeriode'])->name('admin.kinerja.periode');

    // Admin Pegawai Management
    Route::get('/admin/pegawai', [\App\Http\Controllers\PegawaiAdminController::class, 'index'])->name('admin.pegawai.index');
    Route::post('/admin/pegawai', [\App\Http\Controllers\PegawaiAdminController::class, 'store'])->name('admin.pegawai.store');
    Route::put('/admin/pegawai/{id}', [\App\Http\Controllers\PegawaiAdminController::class, 'update'])->name('admin.pegawai.update');
    Route::put('/admin/pegawai/{id}/password', [\App\Http\Controllers\PegawaiAdminController::class, 'updatePassword'])->name('admin.pegawai.password');

    // Admin Absensi Management
    Route::get('/admin/absensi', [\App\Http\Controllers\AbsensiAdminController::class, 'index'])->name('admin.absensi.index');
    Route::get('/admin/absensi/template', [\App\Http\Controllers\AbsensiAdminController::class, 'downloadTemplate'])->name('admin.absensi.template');
    Route::post('/admin/absensi/upload', [\App\Http\Controllers\AbsensiAdminController::class, 'upload'])->name('admin.absensi.upload');
    Route::post('/admin/absensi/bobot', [\App\Http\Controllers\AbsensiAdminController::class, 'updateBobot'])->name('admin.absensi.bobot');

    // Admin Kandidat Management
    Route::get('/admin/kandidat', [\App\Http\Controllers\KandidatAdminController::class, 'index'])->name('admin.kandidat.index');
    Route::post('/admin/kandidat/generate', [\App\Http\Controllers\KandidatAdminController::class, 'generate'])->name('admin.kandidat.generate');
});

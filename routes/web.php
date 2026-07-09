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

    // ==========================
    // ADMIN
    // ==========================
    Route::middleware('admin')->group(function () {

        // Survey
        Route::get('/admin/survey', [\App\Http\Controllers\SurveyAdminController::class, 'index'])->name('admin.survey.index');
        Route::post('/admin/survey', [\App\Http\Controllers\SurveyAdminController::class, 'store'])->name('admin.survey.store');
        Route::put('/admin/survey/{id}', [\App\Http\Controllers\SurveyAdminController::class, 'update'])->name('admin.survey.update');
        Route::delete('/admin/survey/{id}', [\App\Http\Controllers\SurveyAdminController::class, 'destroy'])->name('admin.survey.destroy');

        // Pegawai
        Route::get('/admin/pegawai', [\App\Http\Controllers\PegawaiAdminController::class, 'index'])->name('admin.pegawai.index');
        Route::post('/admin/pegawai', [\App\Http\Controllers\PegawaiAdminController::class, 'store'])->name('admin.pegawai.store');
        Route::put('/admin/pegawai/{id}', [\App\Http\Controllers\PegawaiAdminController::class, 'update'])->name('admin.pegawai.update');
        Route::put('/admin/pegawai/{id}/password', [\App\Http\Controllers\PegawaiAdminController::class, 'updatePassword'])->name('admin.pegawai.password');

        // Monitoring
        Route::get('/admin/monitoring', [\App\Http\Controllers\MonitoringSurveiController::class, 'index'])->name('admin.monitoring.index');
        Route::put('/admin/monitoring/{id}/status', [\App\Http\Controllers\MonitoringSurveiController::class, 'updateStatus'])->name('admin.monitoring.update_status');

        // Periode
        Route::get('/admin/periode', [\App\Http\Controllers\PeriodeController::class, 'index'])->name('admin.periode.index');
        Route::post('/admin/periode', [\App\Http\Controllers\PeriodeController::class, 'store'])->name('admin.periode.store');
        Route::put('/admin/periode/{id}', [\App\Http\Controllers\PeriodeController::class, 'update'])->name('admin.periode.update');
        Route::delete('/admin/periode/{id}', [\App\Http\Controllers\PeriodeController::class, 'destroy'])->name('admin.periode.destroy');

        // Pengaturan Bobot
        Route::get('/admin/pengaturan-bobot', [\App\Http\Controllers\PengaturanBobotController::class, 'index'])->name('admin.pengaturan-bobot.index');
        Route::post('/admin/pengaturan-bobot', [\App\Http\Controllers\PengaturanBobotController::class, 'update'])->name('admin.pengaturan-bobot.update');
    });

    // ==========================
    // ADMIN / KEPALA UMUM
    // ==========================
    Route::middleware('admin_or_kepala_umum')->group(function () {

        // Kinerja
        Route::get('/admin/kinerja', [\App\Http\Controllers\KinerjaAdminController::class, 'index'])->name('admin.kinerja.index');
        Route::post('/admin/kinerja/upload', [\App\Http\Controllers\KinerjaAdminController::class, 'upload'])->name('admin.kinerja.upload');
        Route::post('/admin/kinerja/manual', [\App\Http\Controllers\KinerjaAdminController::class, 'storeManual'])->name('admin.kinerja.manual');

        // Absensi
        Route::get('/admin/absensi', [\App\Http\Controllers\AbsensiAdminController::class, 'index'])->name('admin.absensi.index');
        Route::get('/admin/absensi/template', [\App\Http\Controllers\AbsensiAdminController::class, 'downloadTemplate'])->name('admin.absensi.template');
        Route::post('/admin/absensi/upload', [\App\Http\Controllers\AbsensiAdminController::class, 'upload'])->name('admin.absensi.upload');
        Route::post('/admin/absensi/manual', [\App\Http\Controllers\AbsensiAdminController::class, 'storeManual'])->name('admin.absensi.manual');
        Route::post('/admin/absensi/bobot', [\App\Http\Controllers\AbsensiAdminController::class, 'updateBobot'])->name('admin.absensi.bobot');

        // Nilai CKP
        Route::get('/admin/ckp', [\App\Http\Controllers\CkpController::class, 'index'])->name('admin.ckp.index');
        Route::post('/admin/ckp/upload', [\App\Http\Controllers\CkpController::class, 'upload'])->name('admin.ckp.upload');
        Route::post('/admin/ckp/manual', [\App\Http\Controllers\CkpController::class, 'manual'])->name('admin.ckp.manual');
    });

    // ==========================
    // KANDIDAT (SEMUA ROLE LOGIN)
    // ==========================
    Route::get('/admin/kandidat', [\App\Http\Controllers\KandidatAdminController::class, 'index'])->name('admin.kandidat.index');

    Route::middleware('admin')->group(function () {
        Route::post('/admin/kandidat/generate', [\App\Http\Controllers\KandidatAdminController::class, 'generate'])->name('admin.kandidat.generate');
        Route::post('/admin/kandidat/generate-top3', [\App\Http\Controllers\KandidatAdminController::class, 'generateTop3'])->name('admin.kandidat.generateTop3');
    });

    // ==========================
    // KEPALA BAGIAN
    // ==========================
    Route::middleware([\App\Http\Middleware\CheckRoleKepala::class])->group(function () {

        Route::get('/kepala/review', [\App\Http\Controllers\KepalaController::class, 'index'])->name('kepala.review.index');
        Route::post('/kepala/review/{id}/pilih', [\App\Http\Controllers\KepalaController::class, 'pilihPemenang'])->name('kepala.review.pilih');

        Route::get('/kepala/tim-penilai', [\App\Http\Controllers\TimPenilaiController::class, 'index'])->name('kepala.tim_penilai.index');
        Route::post('/kepala/tim-penilai', [\App\Http\Controllers\TimPenilaiController::class, 'store'])->name('kepala.tim_penilai.store');
        Route::get('/kepala/tim-penilai/{periode_id}/cetak', [\App\Http\Controllers\TimPenilaiController::class, 'cetak'])->name('kepala.tim_penilai.cetak');
    });

    // ==========================
    // SURVEY PEGAWAI
    // ==========================
    Route::get('/survey', [\App\Http\Controllers\SurveyPegawaiController::class, 'index'])->name('pegawai.survey.index');
    Route::post('/survey', [\App\Http\Controllers\SurveyPegawaiController::class, 'store'])->name('pegawai.survey.store');

}); // <-- auth
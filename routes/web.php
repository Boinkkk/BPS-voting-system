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
    Route::middleware('admin')->group(function () {
        // Admin Kinerja Management
        Route::get('/admin/kinerja', [\App\Http\Controllers\KinerjaAdminController::class, 'index'])->name('admin.kinerja.index');
        Route::post('/admin/kinerja/upload', [\App\Http\Controllers\KinerjaAdminController::class, 'upload'])->name('admin.kinerja.upload');
        Route::post('/admin/kinerja/manual', [\App\Http\Controllers\KinerjaAdminController::class, 'storeManual'])->name('admin.kinerja.manual');
        Route::post('/admin/kinerja/periode', [\App\Http\Controllers\KinerjaAdminController::class, 'storePeriode'])->name('admin.kinerja.periode');

        // Admin Survey (Master Data) Management
        Route::get('/admin/survey', [\App\Http\Controllers\SurveyAdminController::class, 'index'])->name('admin.survey.index');
        Route::post('/admin/survey', [\App\Http\Controllers\SurveyAdminController::class, 'store'])->name('admin.survey.store');
        Route::put('/admin/survey/{id}', [\App\Http\Controllers\SurveyAdminController::class, 'update'])->name('admin.survey.update');
        Route::delete('/admin/survey/{id}', [\App\Http\Controllers\SurveyAdminController::class, 'destroy'])->name('admin.survey.destroy');

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

        // Admin Monitoring Survei
        Route::get('/admin/monitoring', [\App\Http\Controllers\MonitoringSurveiController::class, 'index'])->name('admin.monitoring.index');
        Route::put('/admin/monitoring/{id}/status', [\App\Http\Controllers\MonitoringSurveiController::class, 'updateStatus'])->name('admin.monitoring.update_status');
    });

    // Pegawai Survey Voting
    Route::get('/survey', [\App\Http\Controllers\SurveyPegawaiController::class, 'index'])->name('pegawai.survey.index');
    Route::get('/survey/{kandidat_id}', [\App\Http\Controllers\SurveyPegawaiController::class, 'show'])->name('pegawai.survey.show');
    Route::post('/survey/{kandidat_id}', [\App\Http\Controllers\SurveyPegawaiController::class, 'store'])->name('pegawai.survey.store');
});

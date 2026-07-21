<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        $pemenangTerbaru = \App\Models\HasilAkhir::with(['kandidat.pegawai', 'periode'])
            ->where('is_terpilih', 1)
            ->orderBy('waktu_penetapan', 'desc')
            ->first();
            
        $statPegawai = \App\Models\Pegawai::where('status_pegawai', 'Aktif')->count();
        $statVoting = \App\Models\SurveyProgress::count();
        $statPeriode = \App\Models\PeriodePenilaian::count();
            
        return view('welcome', compact('pemenangTerbaru', 'statPegawai', 'statVoting', 'statPeriode'));
    })->name('beranda');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kalender', [\App\Http\Controllers\CalendarController::class, 'index'])->name('kalender');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo')->middleware('throttle:10,1');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/glosarium', [\App\Http\Controllers\GlosariumController::class, 'index'])->name('glosarium.index');
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

        // Audit Log
        Route::get('/admin/audit-log', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');


        // Periode
        Route::get('/admin/periode', [\App\Http\Controllers\PeriodeController::class, 'index'])->name('admin.periode.index');
        Route::post('/admin/periode', [\App\Http\Controllers\PeriodeController::class, 'store'])->name('admin.periode.store');
        Route::put('/admin/periode/{id}', [\App\Http\Controllers\PeriodeController::class, 'update'])->name('admin.periode.update');
        Route::delete('/admin/periode/{id}', [\App\Http\Controllers\PeriodeController::class, 'destroy'])->name('admin.periode.destroy');

        // Pengaturan Bobot
        Route::get('/admin/pengaturan-bobot', [\App\Http\Controllers\PengaturanBobotController::class, 'index'])->name('admin.pengaturan-bobot.index');
        Route::post('/admin/pengaturan-bobot', [\App\Http\Controllers\PengaturanBobotController::class, 'update'])->name('admin.pengaturan-bobot.update');

        // Glosarium Admin
        Route::get('/admin/glosarium', [\App\Http\Controllers\GlosariumAdminController::class, 'index'])->name('admin.glosarium.index');
        Route::get('/admin/glosarium/create', [\App\Http\Controllers\GlosariumAdminController::class, 'create'])->name('admin.glosarium.create');
        Route::post('/admin/glosarium', [\App\Http\Controllers\GlosariumAdminController::class, 'store'])->name('admin.glosarium.store');
        Route::get('/admin/glosarium/{id}/edit', [\App\Http\Controllers\GlosariumAdminController::class, 'edit'])->name('admin.glosarium.edit');
        Route::put('/admin/glosarium/{id}', [\App\Http\Controllers\GlosariumAdminController::class, 'update'])->name('admin.glosarium.update');
        Route::delete('/admin/glosarium/{id}', [\App\Http\Controllers\GlosariumAdminController::class, 'destroy'])->name('admin.glosarium.destroy');

        // Pengumuman
        Route::resource('/admin/pengumuman', \App\Http\Controllers\PengumumanController::class)->names('admin.pengumuman');
        
        // Dev Time Control
        if (!app()->environment('production')) {
            Route::post('/dev/time/set', [\App\Http\Controllers\DevTimeController::class, 'setTime'])->name('dev.time.set');
            Route::post('/dev/time/reset', [\App\Http\Controllers\DevTimeController::class, 'resetTime'])->name('dev.time.reset');
        }
    });

    // ==========================
    // ADMIN / KEPALA UMUM
    // ==========================
    Route::middleware('admin_or_kepala_umum')->group(function () {

        // Kinerja
        Route::get('/admin/kinerja', [\App\Http\Controllers\KinerjaAdminController::class, 'index'])->name('admin.kinerja.index');
        Route::post('/admin/kinerja/upload', [\App\Http\Controllers\KinerjaAdminController::class, 'upload'])->name('admin.kinerja.upload')->middleware('throttle:10,1');
        Route::post('/admin/kinerja/manual', [\App\Http\Controllers\KinerjaAdminController::class, 'storeManual'])->name('admin.kinerja.manual');

        // Absensi
        Route::get('/admin/absensi', [\App\Http\Controllers\AbsensiAdminController::class, 'index'])->name('admin.absensi.index');
        Route::get('/admin/absensi/template', [\App\Http\Controllers\AbsensiAdminController::class, 'downloadTemplate'])->name('admin.absensi.template');
        Route::post('/admin/absensi/upload', [\App\Http\Controllers\AbsensiAdminController::class, 'upload'])->name('admin.absensi.upload')->middleware('throttle:10,1');
        Route::post('/admin/absensi/manual', [\App\Http\Controllers\AbsensiAdminController::class, 'storeManual'])->name('admin.absensi.manual');
        Route::post('/admin/absensi/bobot', [\App\Http\Controllers\AbsensiAdminController::class, 'updateBobot'])->name('admin.absensi.bobot');

        // Nilai CKP
        Route::get('/admin/ckp', [\App\Http\Controllers\CkpController::class, 'index'])->name('admin.ckp.index');
        Route::post('/admin/ckp/upload', [\App\Http\Controllers\CkpController::class, 'upload'])->name('admin.ckp.upload')->middleware('throttle:10,1');
        Route::post('/admin/ckp/manual', [\App\Http\Controllers\CkpController::class, 'manual'])->name('admin.ckp.manual');
    });

    // ==========================
    // MONITORING SURVEI (ADMIN & KEPALA)
    // ==========================
    Route::get('/admin/monitoring', [\App\Http\Controllers\MonitoringSurveiController::class, 'index'])->name('admin.monitoring.index');
    Route::get('/admin/monitoring/download-txt', [\App\Http\Controllers\MonitoringSurveiController::class, 'downloadTxt'])->name('admin.monitoring.download_txt');
    Route::put('/admin/monitoring/{id}/status', [\App\Http\Controllers\MonitoringSurveiController::class, 'updateStatus'])->name('admin.monitoring.update_status');

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
    Route::post('/survey', [\App\Http\Controllers\SurveyPegawaiController::class, 'store'])->name('pegawai.survey.store')->middleware('throttle:10,1');

    // ==========================
    // PENGUMUMAN READ
    // ==========================
    Route::post('/pengumuman/{id}/read', [\App\Http\Controllers\PengumumanController::class, 'markAsRead'])->name('pengumuman.read');

}); // <-- auth
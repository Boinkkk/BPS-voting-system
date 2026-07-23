<?php

use App\Http\Controllers\AbsensiAdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CkpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevTimeController;
use App\Http\Controllers\FaqAdminController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GlosariumAdminController;
use App\Http\Controllers\GlosariumController;
use App\Http\Controllers\KandidatAdminController;
use App\Http\Controllers\KepalaController;
use App\Http\Controllers\KinerjaAdminController;
use App\Http\Controllers\MonitoringSurveiController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\PegawaiAdminController;
use App\Http\Controllers\PengaturanBobotController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyAdminController;
use App\Http\Controllers\SurveyPegawaiController;
use App\Http\Controllers\TimPenilaiController;
use App\Http\Middleware\CheckRoleKepala;
use App\Models\HasilAkhir;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\SurveyProgress;
use Illuminate\Support\Facades\Route;

Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        $pemenangTerbaru = HasilAkhir::with(['kandidat.pegawai', 'periode'])
            ->where('is_terpilih', 1)
            ->orderBy('waktu_penetapan', 'desc')
            ->first();

        $statPegawai = Pegawai::where('status_pegawai', 'Aktif')->count();
        $statVoting = SurveyProgress::count();
        $statPeriode = PeriodePenilaian::count();

        return view('welcome', compact('pemenangTerbaru', 'statPegawai', 'statVoting', 'statPeriode'));
    })->name('beranda');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kalender', [CalendarController::class, 'index'])->name('kalender');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo')->middleware('throttle:10,1');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/glosarium', [GlosariumController::class, 'index'])->name('glosarium.index');
    Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan.index');
    // ==========================
    // ADMIN
    // ==========================
    Route::middleware('admin')->group(function () {

        // Survey
        Route::get('/admin/survey', [SurveyAdminController::class, 'index'])->name('admin.survey.index');
        Route::post('/admin/survey', [SurveyAdminController::class, 'store'])->name('admin.survey.store');
        Route::put('/admin/survey/{id}', [SurveyAdminController::class, 'update'])->name('admin.survey.update');
        Route::delete('/admin/survey/{id}', [SurveyAdminController::class, 'destroy'])->name('admin.survey.destroy');

        // Pegawai
        Route::get('/admin/pegawai', [PegawaiAdminController::class, 'index'])->name('admin.pegawai.index');
        Route::post('/admin/pegawai', [PegawaiAdminController::class, 'store'])->name('admin.pegawai.store');
        Route::put('/admin/pegawai/{id}', [PegawaiAdminController::class, 'update'])->name('admin.pegawai.update');
        Route::put('/admin/pegawai/{id}/password', [PegawaiAdminController::class, 'updatePassword'])->name('admin.pegawai.password');

        // Audit Log
        Route::get('/admin/audit-log', [AuditLogController::class, 'index'])->name('admin.audit.index');

        // Periode
        Route::get('/admin/periode', [PeriodeController::class, 'index'])->name('admin.periode.index');
        Route::post('/admin/periode', [PeriodeController::class, 'store'])->name('admin.periode.store');
        Route::put('/admin/periode/{id}', [PeriodeController::class, 'update'])->name('admin.periode.update');
        Route::delete('/admin/periode/{id}', [PeriodeController::class, 'destroy'])->name('admin.periode.destroy');

        // Pengaturan Bobot
        Route::get('/admin/pengaturan-bobot', [PengaturanBobotController::class, 'index'])->name('admin.pengaturan-bobot.index');
        Route::post('/admin/pengaturan-bobot', [PengaturanBobotController::class, 'update'])->name('admin.pengaturan-bobot.update');

        // Glosarium Admin
        Route::get('/admin/glosarium', [GlosariumAdminController::class, 'index'])->name('admin.glosarium.index');
        Route::get('/admin/glosarium/create', [GlosariumAdminController::class, 'create'])->name('admin.glosarium.create');
        Route::post('/admin/glosarium', [GlosariumAdminController::class, 'store'])->name('admin.glosarium.store');
        Route::get('/admin/glosarium/{id}/edit', [GlosariumAdminController::class, 'edit'])->name('admin.glosarium.edit');
        Route::put('/admin/glosarium/{id}', [GlosariumAdminController::class, 'update'])->name('admin.glosarium.update');
        Route::delete('/admin/glosarium/{id}', [GlosariumAdminController::class, 'destroy'])->name('admin.glosarium.destroy');

        // FAQ Admin
        Route::resource('/admin/faq', FaqAdminController::class)->names('admin.faq');

        // Pengumuman
        Route::resource('/admin/pengumuman', PengumumanController::class)->names('admin.pengumuman');

        // Dev Time Control
        if (! app()->environment('production')) {
            Route::post('/dev/time/set', [DevTimeController::class, 'setTime'])->name('dev.time.set');
            Route::post('/dev/time/reset', [DevTimeController::class, 'resetTime'])->name('dev.time.reset');
        }
    });

    // ==========================
    // ADMIN / KEPALA UMUM
    // ==========================
    Route::middleware('admin_or_kepala_umum')->group(function () {

        // Kinerja
        Route::get('/admin/kinerja', [KinerjaAdminController::class, 'index'])->name('admin.kinerja.index');
        Route::post('/admin/kinerja/upload', [KinerjaAdminController::class, 'upload'])->name('admin.kinerja.upload')->middleware('throttle:10,1');
        Route::post('/admin/kinerja/manual', [KinerjaAdminController::class, 'storeManual'])->name('admin.kinerja.manual');

        // Absensi
        Route::get('/admin/absensi', [AbsensiAdminController::class, 'index'])->name('admin.absensi.index');
        Route::get('/admin/absensi/template', [AbsensiAdminController::class, 'downloadTemplate'])->name('admin.absensi.template');
        Route::post('/admin/absensi/upload', [AbsensiAdminController::class, 'upload'])->name('admin.absensi.upload')->middleware('throttle:10,1');
        Route::post('/admin/absensi/manual', [AbsensiAdminController::class, 'storeManual'])->name('admin.absensi.manual');
        Route::post('/admin/absensi/bobot', [AbsensiAdminController::class, 'updateBobot'])->name('admin.absensi.bobot');

        // Nilai CKP
        Route::get('/admin/ckp', [CkpController::class, 'index'])->name('admin.ckp.index');
        Route::post('/admin/ckp/upload', [CkpController::class, 'upload'])->name('admin.ckp.upload')->middleware('throttle:10,1');
        Route::post('/admin/ckp/manual', [CkpController::class, 'manual'])->name('admin.ckp.manual');
    });

    // ==========================
    // MONITORING SURVEI (ADMIN & KEPALA)
    // ==========================
    Route::get('/admin/monitoring', [MonitoringSurveiController::class, 'index'])->name('admin.monitoring.index');
    Route::get('/admin/monitoring/download-txt', [MonitoringSurveiController::class, 'downloadTxt'])->name('admin.monitoring.download_txt');
    Route::put('/admin/monitoring/{id}/status', [MonitoringSurveiController::class, 'updateStatus'])->name('admin.monitoring.update_status');

    // ==========================
    // KANDIDAT (SEMUA ROLE LOGIN)
    // ==========================
    Route::get('/admin/kandidat', [KandidatAdminController::class, 'index'])->name('admin.kandidat.index');

    Route::middleware('admin')->group(function () {
        Route::post('/admin/kandidat/generate', [KandidatAdminController::class, 'generate'])->name('admin.kandidat.generate');
        Route::post('/admin/kandidat/generate-top3', [KandidatAdminController::class, 'generateTop3'])->name('admin.kandidat.generateTop3');
    });

    // ==========================
    // KEPALA BAGIAN
    // ==========================
    Route::middleware([CheckRoleKepala::class])->group(function () {

        Route::get('/kepala/review', [KepalaController::class, 'index'])->name('kepala.review.index');
        Route::post('/kepala/review/{id}/pilih', [KepalaController::class, 'pilihPemenang'])->name('kepala.review.pilih');

        Route::get('/kepala/tim-penilai', [TimPenilaiController::class, 'index'])->name('kepala.tim_penilai.index');
        Route::post('/kepala/tim-penilai', [TimPenilaiController::class, 'store'])->name('kepala.tim_penilai.store');
        Route::get('/kepala/tim-penilai/{periode_id}/cetak', [TimPenilaiController::class, 'cetak'])->name('kepala.tim_penilai.cetak');
    });

    // ==========================
    // SURVEY PEGAWAI
    // ==========================
    Route::get('/survey', [SurveyPegawaiController::class, 'index'])->name('pegawai.survey.index');
    Route::post('/survey', [SurveyPegawaiController::class, 'store'])->name('pegawai.survey.store')->middleware('throttle:10,1');

    // ==========================
    // PENGUMUMAN READ
    // ==========================
    Route::post('/pengumuman/{id}/read', [PengumumanController::class, 'markAsRead'])->name('pengumuman.read');

}); // <-- auth

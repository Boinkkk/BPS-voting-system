<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Imports\AbsensiImport;
use App\Models\AbsensiPegawai;
use App\Models\BobotPenalti;
use App\Models\Pegawai;
use App\Models\PengaturanBobot;
use App\Models\PeriodePenilaian;
use App\Services\KandidatService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiAdminController extends Controller
{
    public function index(Request $request)
    {
        $requested_periode_id = $request->input('periode_id');
        $requested_bulan = $request->input('bulan');

        $periodeData = PeriodePenilaian::getRecentAndDefault($requested_periode_id);
        $periodes = $periodeData['periodes'];
        $periode_id = $periodeData['default_id'];
        $bulan = $requested_bulan;

        if ($periode_id) {
            $periode = $periodes->firstWhere('id', $periode_id);
            if ($periode) {
                $triwulan = $periode->triwulan;
                $bulanAwal = ($triwulan - 1) * 3 + 1;
                $bulanAkhir = $bulanAwal + 2;

                // Jika bulan tidak di-set atau berada di luar range triwulan ini
                if (! $bulan || $bulan < $bulanAwal || $bulan > $bulanAkhir) {
                    $bulan = $bulanAwal;
                }
            } elseif (! $bulan) {
                $bulan = date('n');
            }
        } elseif (! $bulan) {
            $bulan = date('n');
        }

        if ($periode_id != $requested_periode_id || $bulan != $requested_bulan) {
            return redirect()->route('admin.absensi.index', array_merge($request->query(), [
                'periode_id' => $periode_id,
                'bulan' => $bulan,
            ]));
        }

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $absensis = null;
        $rekapTriwulan = collect();
        $rekapTriwulanPage = null;

        if ($periode_id) {
            if ($bulan) {
                $query = AbsensiPegawai::with('pegawai')
                    ->where('periode_id', $periode_id)
                    ->where('bulan', $bulan);
                if ($search) {
                    $query->whereHas('pegawai', function ($q) use ($search) {
                        $q->where('nama', 'like', '%'.$search.'%')
                            ->orWhere('nip', 'like', '%'.$search.'%');
                    });
                }
                $absensis = $query->paginate($perPage, ['*'], 'absensi_page')
                    ->appends(request()->query());
            } else {
                // If somehow no month is selected, just return empty paginator
                $absensis = new LengthAwarePaginator([], 0, $perPage, 1, ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'absensi_page']);
            }

            // Rekap Triwulan (Semua bulan dalam periode ini)
            $allAbsenQuery = AbsensiPegawai::with('pegawai')
                ->where('periode_id', $periode_id);
            if ($search) {
                $allAbsenQuery->whereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('nip', 'like', '%'.$search.'%');
                });
            }
            $allAbsenPeriode = $allAbsenQuery->get();

            $grouped = $allAbsenPeriode->groupBy('pegawai_id');
            foreach ($grouped as $pegawai_id => $dataAbsen) {
                $totalKjk = $dataAbsen->sum('kjk');
                $totalTk = $dataAbsen->sum('tk');
                $pegawai = $dataAbsen->first()->pegawai;

                $pengaturan = PengaturanBobot::first();

                // Fase 1: Base Score dari KJK
                if ($totalKjk == 0) {
                    $nilaiPresensi = 100;
                } elseif ($totalKjk >= 1 && $totalKjk <= 60) {
                    $nilaiPresensi = 99;
                } elseif ($totalKjk >= 61 && $totalKjk <= 120) {
                    $nilaiPresensi = 98;
                } elseif ($totalKjk >= 121 && $totalKjk <= 450) {
                    $nilaiPresensi = 97;
                } else {
                    $nilaiPresensi = 96;
                }

                // Fase 2 & 3: Pengurangan dari TL, PSW, dan TK
                if ($pengaturan) {
                    $totalPenguranganTl = 0;
                    $totalPenguranganPsw = 0;

                    foreach ($dataAbsen as $rekap) {
                        if ($pengaturan->bobot_tl1 > 0 || $pengaturan->bobot_tl2 > 0 || $pengaturan->bobot_tl3 > 0 || $pengaturan->bobot_tl4 > 0) {
                            $totalPenguranganTl += ($rekap->tl1 * $pengaturan->bobot_tl1) +
                                                   ($rekap->tl2 * $pengaturan->bobot_tl2) +
                                                   ($rekap->tl3 * $pengaturan->bobot_tl3) +
                                                   ($rekap->tl4 * $pengaturan->bobot_tl4);
                        } else {
                            $totalPenguranganTl += ($rekap->tl * $pengaturan->bobot_tl);
                        }

                        if ($pengaturan->bobot_psw1 > 0 || $pengaturan->bobot_psw2 > 0 || $pengaturan->bobot_psw3 > 0 || $pengaturan->bobot_psw4 > 0) {
                            $totalPenguranganPsw += ($rekap->psw1 * $pengaturan->bobot_psw1) +
                                                    ($rekap->psw2 * $pengaturan->bobot_psw2) +
                                                    ($rekap->psw3 * $pengaturan->bobot_psw3) +
                                                    ($rekap->psw4 * $pengaturan->bobot_psw4);
                        } else {
                            $totalPenguranganPsw += ($rekap->psw * $pengaturan->bobot_psw);
                        }
                    }

                    $nilaiPresensi -= $totalPenguranganTl;
                    $nilaiPresensi -= $totalPenguranganPsw;
                    $nilaiPresensi -= ($totalTk * $pengaturan->bobot_tk);
                }

                $nilaiPresensi = max(0, $nilaiPresensi);

                $rekapTriwulan->push((object) [
                    'pegawai' => $pegawai,
                    'total_tk' => $totalTk,
                    'total_kjk' => $totalKjk,
                    'nilai_presensi' => $nilaiPresensi,
                ]);
            }

            $rekapPage = Paginator::resolveCurrentPage('rekap_page');
            $rekapTriwulanPage = new LengthAwarePaginator(
                $rekapTriwulan->forPage($rekapPage, $perPage),
                $rekapTriwulan->count(),
                $perPage,
                $rekapPage,
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query(), 'pageName' => 'rekap_page']
            );
        } else {
            $absensis = new LengthAwarePaginator([], 0, $perPage, 1, ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'absensi_page']);
            $rekapTriwulanPage = new LengthAwarePaginator([], 0, $perPage, 1, ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'rekap_page']);
        }

        $semuaPegawai = Pegawai::whereHas('role', function ($q) {
            $q->where('tipe', 'Pegawai');
        })->orderBy('nama')->get();

        return view('admin.absensi.index', compact('absensis', 'periodes', 'periode_id', 'bulan', 'rekapTriwulanPage', 'perPage', 'semuaPegawai'));
    }

    public function downloadTemplate()
    {
        return Excel::download(new AbsensiExport, 'Template_Rekap_Absensi.xlsx');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'bulan' => 'required|integer|min:1|max:12',
            'file' => ['required', File::types(['xlsx', 'xls', 'csv'])->max(10 * 1024), 'mimes:xlsx,xls,csv'],
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Upload data absensi hanya dapat dilakukan pada masa penginputan data.');
        }

        try {
            Excel::import(new AbsensiImport($request->periode_id, $request->bulan), $request->file('file'));

            // Re-kalkulasi 10 kandidat terbaik setelah update absensi
            KandidatService::generateTop10Kandidat($request->periode_id);

            return redirect()->back()->with('success', 'Data rekap absensi berhasil diunggah. Skor akhir kandidat telah dikalkulasi ulang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah data: '.$e->getMessage());
        }
    }

    public function updateBobot(Request $request)
    {
        $request->validate([
            'bobots' => 'required|array',
            'bobots.*' => 'numeric|min:0',
        ]);

        $periodeAktif = PeriodePenilaian::whereIn('status', ['voting', 'review_kepala'])->first();
        if ($periodeAktif) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah bobot karena sedang ada periode penilaian yang aktif pada fase voting atau review.');
        }

        foreach ($request->bobots as $id => $nilai) {
            BobotPenalti::where('id', $id)->update(['bobot' => $nilai]);
        }

        // Hapus cache agar perhitungan baru menggunakan nilai terbaru
        Cache::forget('bobot_penalti');

        // Opsional: Jika ingin trigger kalkulasi ulang 10 kandidat otomatis
        // \App\Services\KandidatService::generateTop10Kandidat($periode_id_aktif_jika_ada);

        return redirect()->back()->with('success', 'Pengaturan bobot penalti berhasil diperbarui.');
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'pegawai_id' => 'required|exists:pegawai,id',
            'bulan' => 'required|integer|min:1|max:12',
            'hk' => 'required|integer|min:0',
            'hd' => 'required|integer|min:0',
            'tk' => 'required|integer|min:0',
            'psw' => 'required|integer|min:0',
            'tl' => 'required|integer|min:0',
            'kjk_ht' => 'required|integer|min:0',
            'kjk_pc' => 'required|integer|min:0',
            'kjk' => 'required|integer|min:0',
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Input data absensi manual hanya dapat dilakukan pada masa penginputan data.');
        }

        try {
            AbsensiPegawai::updateOrCreate(
                [
                    'periode_id' => $request->periode_id,
                    'pegawai_id' => $request->pegawai_id,
                    'bulan' => $request->bulan,
                ],
                [
                    'hk' => $request->hk,
                    'hd' => $request->hd,
                    'tk' => $request->tk,
                    'psw' => $request->psw,
                    'tl' => $request->tl,
                    'kjk_ht' => $request->kjk_ht,
                    'kjk_pc' => $request->kjk_pc,
                    'kjk' => $request->kjk,
                    // set the rest to 0 to prevent errors
                    'tb' => 0, 'pd' => 0, 'dk' => 0, 'kn' => 0,
                    'psw1' => 0, 'psw2' => 0, 'psw3' => 0, 'psw4' => 0,
                    'ht' => 0, 'tl1' => 0, 'tl2' => 0, 'tl3' => 0, 'tl4' => 0,
                    'cb' => 0, 'cl' => 0, 'cm' => 0, 'cp' => 0, 'cs' => 0,
                    'ct10' => 0, 'ct11' => 0, 'ct12' => 0,
                    'cst1' => 0, 'cst2' => 0, 'cs1' => 0, 'cp1' => 0, 'cm1' => 0, 'cb1' => 0,
                ]
            );

            // Trigger otomatis kalkulasi kandidat
            KandidatService::generateTop10Kandidat($request->periode_id);

            return redirect()->back()->with('success', 'Data absensi manual berhasil ditambahkan/diperbarui. Skor akhir kandidat telah dikalkulasi ulang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Models\PeriodePenilaian;
use App\Imports\AbsensiImport;
use App\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiAdminController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->input('periode_id');
        $bulan = $request->input('bulan');

        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        if (!$periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        if (!$bulan) {
            $bulan = date('n');
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
                    $query->whereHas('pegawai', function($q) use ($search) {
                        $q->where('nama', 'like', '%' . $search . '%')
                          ->orWhere('nip', 'like', '%' . $search . '%');
                    });
                }
                $absensis = $query->paginate($perPage, ['*'], 'absensi_page')
                            ->appends(request()->query());
            } else {
                // If somehow no month is selected, just return empty paginator
                $absensis = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'absensi_page']);
            }
            
            // Rekap Triwulan (Semua bulan dalam periode ini)
            $allAbsenQuery = AbsensiPegawai::with('pegawai')
                            ->where('periode_id', $periode_id);
            if ($search) {
                $allAbsenQuery->whereHas('pegawai', function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('nip', 'like', '%' . $search . '%');
                });
            }
            $allAbsenPeriode = $allAbsenQuery->get();
                            
            $grouped = $allAbsenPeriode->groupBy('pegawai_id');
            foreach($grouped as $pegawai_id => $dataAbsen) {
                $totalKjk = $dataAbsen->sum('kjk');
                $totalTk = $dataAbsen->sum('tk');
                $pegawai = $dataAbsen->first()->pegawai;
                
                $nilaiPresensi = 100;
                if ($totalTk >= 1) {
                    $nilaiPresensi = 96;
                } else {
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
                }
                
                $rekapTriwulan->push((object)[
                    'pegawai' => $pegawai,
                    'total_tk' => $totalTk,
                    'total_kjk' => $totalKjk,
                    'nilai_presensi' => $nilaiPresensi
                ]);
            }
            
            $rekapPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('rekap_page');
            $rekapTriwulanPage = new \Illuminate\Pagination\LengthAwarePaginator(
                $rekapTriwulan->forPage($rekapPage, $perPage),
                $rekapTriwulan->count(),
                $perPage,
                $rekapPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query(), 'pageName' => 'rekap_page']
            );
        } else {
            $absensis = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'absensi_page']);
            $rekapTriwulanPage = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'rekap_page']);
        }

        return view('admin.absensi.index', compact('absensis', 'periodes', 'periode_id', 'bulan', 'rekapTriwulanPage', 'perPage'));
    }

    public function downloadTemplate()
    {
        return Excel::download(new AbsensiExport, 'Template_Rekap_Absensi.xlsx');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'bulan'      => 'required|integer|min:1|max:12',
            'file'       => 'required|mimes:xlsx,xls'
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Upload data absensi hanya dapat dilakukan pada masa penginputan data.');
        }

        try {
            Excel::import(new AbsensiImport($request->periode_id, $request->bulan), $request->file('file'));
            
            // Trigger otomatis kalkulasi kandidat
            \App\Services\KandidatService::generateTop10Kandidat($request->periode_id);

            return redirect()->back()->with('success', 'Data rekap absensi berhasil diunggah. Skor akhir kandidat telah dikalkulasi ulang berdasarkan data absensi terbaru.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah data: ' . $e->getMessage());
        }
    }

    public function updateBobot(Request $request)
    {
        $request->validate([
            'bobots' => 'required|array',
            'bobots.*' => 'numeric|min:0'
        ]);

        $periodeAktif = PeriodePenilaian::whereIn('status', ['voting', 'review_kepala'])->first();
        if ($periodeAktif) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah bobot karena sedang ada periode penilaian yang aktif pada fase voting atau review.');
        }

        foreach ($request->bobots as $id => $nilai) {
            \App\Models\BobotPenalti::where('id', $id)->update(['bobot' => $nilai]);
        }

        // Hapus cache agar perhitungan baru menggunakan nilai terbaru
        \Illuminate\Support\Facades\Cache::forget('bobot_penalti');

        // Opsional: Jika ingin trigger kalkulasi ulang 10 kandidat otomatis
        // \App\Services\KandidatService::generateTop10Kandidat($periode_id_aktif_jika_ada);

        return redirect()->back()->with('success', 'Pengaturan bobot penalti berhasil diperbarui.');
    }
}

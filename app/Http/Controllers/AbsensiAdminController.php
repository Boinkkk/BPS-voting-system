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

        $absensis = [];
        $rekapTriwulan = collect();
        
        if ($periode_id) {
            if ($bulan) {
                $absensis = AbsensiPegawai::with('pegawai')
                            ->where('periode_id', $periode_id)
                            ->where('bulan', $bulan)
                            ->get();
            }
            
            // Rekap Triwulan (Semua bulan dalam periode ini)
            $allAbsenPeriode = AbsensiPegawai::with('pegawai')
                            ->where('periode_id', $periode_id)
                            ->get();
                            
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
        }

        $bobots = \App\Models\BobotPenalti::orderBy('kategori')->get();

        return view('admin.absensi.index', compact('absensis', 'periodes', 'periode_id', 'bulan', 'bobots', 'rekapTriwulan'));
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

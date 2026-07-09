<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Services\KandidatService;

class KandidatAdminController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->input('periode_id');
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        
        if (!$periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        $kandidats = [];
        $is_fase_2_selesai = false;

        if ($periode_id) {
            $periode = $periodes->firstWhere('id', $periode_id);
            if ($periode && in_array($periode->status, ['review_kepala', 'selesai'])) {
                $is_fase_2_selesai = true;
                // Ambil 3 kandidat dari hasil_akhir
                $hasilAkhir = \App\Models\HasilAkhir::with('kandidat.pegawai')
                                ->where('periode_id', $periode_id)
                                ->orderBy('ranking_final', 'asc')
                                ->get();
                $pengaturan = \App\Models\PengaturanBobot::first();
                $ckpWeight = $pengaturan ? $pengaturan->ckp : 50;
                $absensiWeight = $pengaturan ? $pengaturan->absensi : 25;
                $surveyWeight = $pengaturan ? $pengaturan->survey : 25;

                $kandidats = $hasilAkhir->map(function ($ha) use ($periode_id, $ckpWeight, $absensiWeight, $surveyWeight) {
                    $k = $ha->kandidat;
                    $k->ranking_sistem = $ha->ranking_final; // override display ranking
                    
                    $rataRata = \App\Models\JawabanSurvei::where('periode_id', $periode_id)
                        ->where('kandidat_id', $k->id)
                        ->avg('nilai');
                    $surveyNormalized = $rataRata ? ($rataRata / 5) * 100 : 0;
                    
                    $finalScore = ($k->skor_ckp * ($ckpWeight / 100)) + 
                                  ($k->skor_absensi * ($absensiWeight / 100)) + 
                                  ($surveyNormalized * ($surveyWeight / 100));

                    $k->skor_survey = round($surveyNormalized, 2);
                    $k->skor_akhir_voting = round($finalScore, 2);
                    
                    return $k;
                });
            } else {
                $kandidats = Kandidat::with('pegawai')
                                ->where('periode_id', $periode_id)
                                ->orderBy('ranking_sistem', 'asc')
                                ->get();
            }
        }

        return view('admin.kandidat.index', compact('periodes', 'periode_id', 'kandidats', 'is_fase_2_selesai'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id'
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Kalkulasi ulang hanya dapat dilakukan pada masa penginputan data.');
        }

        try {
            KandidatService::generateTop10Kandidat($request->periode_id);
            return redirect()->back()->with('success', '10 Kandidat terbaik berhasil dikalkulasi ulang dan disimpan untuk periode ini.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghasilkan kandidat: ' . $e->getMessage());
        }
    }

    public function generateTop3(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id'
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'review_kepala') {
            return redirect()->back()->with('error', 'Kalkulasi ulang 3 Terbaik hanya dapat dilakukan pada masa Review Kepala.');
        }

        try {
            KandidatService::generateTop3Kandidat($request->periode_id);
            return redirect()->back()->with('success', '3 Kandidat terbaik berhasil dikalkulasi ulang berdasarkan Nilai CKP, Absensi, dan Hasil Voting Survei.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengkalkulasi ulang kandidat: ' . $e->getMessage());
        }
    }
}

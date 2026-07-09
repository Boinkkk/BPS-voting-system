<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use App\Models\HasilAkhir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KepalaController extends Controller
{
    public function index()
    {
        // Cari periode yang sedang review_kepala
        $periodeReview = PeriodePenilaian::where('status', 'review_kepala')->first();
        
        $kandidats = collect();
        if ($periodeReview) {
            $kandidats = HasilAkhir::with('kandidat.pegawai')
                ->where('periode_id', $periodeReview->id)
                ->orderBy('ranking_final', 'asc')
                ->get();
            
            $pengaturan = \App\Models\PengaturanBobot::first();
            $ckpWeight = $pengaturan ? $pengaturan->ckp : 50;
            $absensiWeight = $pengaturan ? $pengaturan->absensi : 25;
            $surveyWeight = $pengaturan ? $pengaturan->survey : 25;

            foreach ($kandidats as $ha) {
                $kandidat = $ha->kandidat;
                
                $rataRata = \App\Models\JawabanSurvei::where('periode_id', $periodeReview->id)
                    ->where('kandidat_id', $kandidat->id)
                    ->avg('nilai');
                $surveyNormalized = $rataRata ? ($rataRata / 5) * 100 : 0;
                
                $nilaiCkp = $kandidat->skor_ckp;
                $nilaiAbsensi = $kandidat->skor_absensi;
                
                $finalScore = ($nilaiCkp * ($ckpWeight / 100)) + 
                              ($nilaiAbsensi * ($absensiWeight / 100)) + 
                              ($surveyNormalized * ($surveyWeight / 100));
                              
                $ha->skor_survey_normalized = round($surveyNormalized, 2);
                $ha->skor_akhir_voting = round($finalScore, 2);
            }
        }

        return view('kepala.review.index', compact('periodeReview', 'kandidats'));
    }

    public function pilihPemenang(Request $request, $id)
    {
        $hasilAkhir = HasilAkhir::findOrFail($id);
        $periode = $hasilAkhir->periode;

        if ($periode->status !== 'review_kepala') {
            return redirect()->back()->with('error', 'Periode ini tidak dalam masa review Kepala Bagian.');
        }

        DB::beginTransaction();
        try {
            // Set semuanya menjadi false dulu di periode ini
            HasilAkhir::where('periode_id', $periode->id)->update(['is_terpilih' => false, 'dipilih_oleh' => null, 'waktu_penetapan' => null]);

            // Set yang terpilih
            $hasilAkhir->is_terpilih = true;
            $hasilAkhir->dipilih_oleh = Auth::id();
            $hasilAkhir->waktu_penetapan = now();
            $hasilAkhir->catatan_kepala = $request->input('catatan');
            $hasilAkhir->save();

            // Ubah status periode menjadi selesai
            $periode->status = 'selesai';
            $periode->save();

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Pegawai terbaik berhasil ditetapkan! Pemilihan periode ini telah selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

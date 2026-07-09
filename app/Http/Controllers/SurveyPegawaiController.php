<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\PertanyaanSurvei;
use App\Models\SurveyProgress;
use App\Models\JawabanSurvei;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\Auth;

class SurveyPegawaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $periodeAktif = PeriodePenilaian::where('status', 'voting')->first();
        if (!$periodeAktif) {
            return view('pegawai.survey.index', ['error' => 'Tidak ada periode penilaian yang aktif saat ini.']);
        }

        $kandidats = Kandidat::with('pegawai')
                        ->where('periode_id', $periodeAktif->id)
                        ->where('pegawai_id', '!=', $user->id)
                        ->orderBy('skor', 'desc')
                        ->take(10)
                        ->get();

        $sudahIsi = SurveyProgress::where('periode_id', $periodeAktif->id)
                                        ->where('user_id', $user->id)
                                        ->exists();

        $pertanyaans = PertanyaanSurvei::orderBy('nomor_urut')->get();

        return view('pegawai.survey.index', compact('periodeAktif', 'kandidats', 'sudahIsi', 'pertanyaans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role->tipe !== 'Pegawai') {
            return redirect()->route('pegawai.survey.index')->with('error', 'Hanya pegawai yang dapat mensubmit survei. Anda hanya memiliki akses pratinjau (read-only).');
        }

        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*.*' => 'required|integer|min:1|max:5',
        ]);

        $periodeAktif = PeriodePenilaian::where('status', 'voting')->first();

        // 1. Simpan nilai ke jawaban_survei tanpa id user atau session (100% anonim)
        foreach ($request->jawaban as $pertanyaan_id => $kandidatScores) {
            foreach ($kandidatScores as $kandidat_id => $nilai) {
                JawabanSurvei::create([
                    'periode_id' => $periodeAktif->id,
                    'kandidat_id' => $kandidat_id,
                    'pertanyaan_id' => $pertanyaan_id,
                    'nilai' => $nilai,
                    'waktu_jawab' => now(),
                ]);

                // Catat progress untuk setiap kandidat
                SurveyProgress::updateOrCreate([
                    'periode_id' => $periodeAktif->id,
                    'user_id' => $user->id,
                    'kandidat_id' => $kandidat_id,
                ]);
            }
        }

        return redirect()->route('pegawai.survey.index')->with('success', 'Survey berhasil disimpan!');
    }
}

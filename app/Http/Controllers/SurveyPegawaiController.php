<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandidat;
use App\Models\PertanyaanSurvei;
use App\Models\SurveyProgress;
use App\Models\JawabanSurvei;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $isVotingDitunda = !$periodeAktif->isDataLengkap();

        $pertanyaans = PertanyaanSurvei::orderBy('nomor_urut')->get();

        return view('pegawai.survey.index', compact('periodeAktif', 'kandidats', 'sudahIsi', 'pertanyaans', 'isVotingDitunda'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role->tipe, ['Pegawai', 'Kepala Umum', 'Kepala_Umum'])) {
            return redirect()->route('pegawai.survey.index')->with('error', 'Hanya pegawai dan Kepala Umum yang dapat mensubmit survei. Anda hanya memiliki akses pratinjau (read-only).');
        }

        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*.*' => 'required|integer|min:1|max:5',
        ]);

        $periodeAktif = PeriodePenilaian::where('status', 'voting')->first();
        if (!$periodeAktif || !$periodeAktif->isDataLengkap()) {
            return redirect()->route('pegawai.survey.index')->with('error', 'Pemilihan sedang ditunda. Data kandidat belum lengkap.');
        }

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

        Log::channel('audit')->info("Pegawai telah mensubmit evaluasi/voting", [
            'ip' => $request->ip(),
            'pegawai_id' => $user->id,
            'nama_pegawai' => $user->nama,
            'nip' => $user->nip,
            'periode_id' => $periodeAktif->id,
            'nama_periode' => $periodeAktif->nama
        ]);

        return redirect()->route('pegawai.survey.index')->with('success', 'Survey berhasil disimpan!');
    }
}

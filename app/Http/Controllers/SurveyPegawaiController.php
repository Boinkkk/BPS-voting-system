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

        $jawabanSelesai = SurveyProgress::where('periode_id', $periodeAktif->id)
                                        ->where('user_id', $user->id)
                                        ->pluck('kandidat_id')
                                        ->toArray();

        return view('pegawai.survey.index', compact('periodeAktif', 'kandidats', 'jawabanSelesai'));
    }

    public function show($kandidat_id)
    {
        $user = Auth::user();
        $periodeAktif = PeriodePenilaian::where('status', 'voting')->first();
        if (!$periodeAktif) {
            return redirect()->route('pegawai.survey.index')->with('error', 'Tidak ada periode aktif.');
        }

        $sudahIsi = SurveyProgress::where('periode_id', $periodeAktif->id)
                                ->where('user_id', $user->id)
                                ->where('kandidat_id', $kandidat_id)
                                ->exists();

        if ($sudahIsi) {
            return redirect()->route('pegawai.survey.index')->with('info', 'Anda sudah mensurvei kandidat ini.');
        }

        $kandidat = Kandidat::with('pegawai')->findOrFail($kandidat_id);
        $pertanyaans = PertanyaanSurvei::orderBy('nomor_urut')->get()->groupBy('grup_kategori');

        return view('pegawai.survey.form', compact('kandidat', 'pertanyaans', 'periodeAktif'));
    }

    public function store(Request $request, $kandidat_id)
    {
        $user = Auth::user();
        if ($user->role->tipe !== 'Pegawai') {
            return redirect()->route('pegawai.survey.index')->with('error', 'Hanya pegawai yang dapat mensubmit survei. Anda hanya memiliki akses pratinjau (read-only).');
        }

        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        $periodeAktif = PeriodePenilaian::where('status', 'voting')->first();

        // 1. Catat bahwa user ini sudah survei (tanpa mencatat nilai ke tabel progress)
        SurveyProgress::updateOrCreate([
            'periode_id' => $periodeAktif->id,
            'user_id' => $user->id,
            'kandidat_id' => $kandidat_id,
        ]);

        // 2. Simpan nilai ke jawaban_survei tanpa id user atau session (100% anonim)
        foreach ($request->jawaban as $pertanyaan_id => $nilai) {
            JawabanSurvei::create([
                'periode_id' => $periodeAktif->id,
                'kandidat_id' => $kandidat_id,
                'pertanyaan_id' => $pertanyaan_id,
                'nilai' => $nilai,
                'waktu_jawab' => now(),
            ]);
        }

        return redirect()->route('pegawai.survey.index')->with('success', 'Survey berhasil disimpan!');
    }
}

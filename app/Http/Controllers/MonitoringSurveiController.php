<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use App\Models\Pegawai;
use App\Models\Kandidat;
use App\Models\JawabanSurvei;
use App\Models\SurveyProgress;
use Illuminate\Support\Facades\DB;

class MonitoringSurveiController extends Controller
{
    public function index(Request $request)
    {
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        $periode_id = $request->input('periode_id');

        if (!$periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        $kandidats = collect();
        $progressPegawai = collect();
        $totalPegawai = Pegawai::count();
        $pegawaiSelesai = 0;

        if ($periode_id) {
            // 1. Live Score (Klasemen)
            // Hitung rata-rata nilai dari jawaban survei per kandidat untuk periode ini
            $pengaturan = \App\Models\PengaturanBobot::first();
            $ckpWeight = $pengaturan ? $pengaturan->ckp : 50;
            $absensiWeight = $pengaturan ? $pengaturan->absensi : 25;
            $surveyWeight = $pengaturan ? $pengaturan->survey : 25;

            $kandidats = Kandidat::with('pegawai')
                ->where('periode_id', $periode_id)
                ->get()
                ->map(function ($kandidat) use ($periode_id, $ckpWeight, $absensiWeight, $surveyWeight) {
                    // Survey Normalized
                    $rataRata = JawabanSurvei::where('periode_id', $periode_id)
                        ->where('kandidat_id', $kandidat->id)
                        ->avg('nilai');
                    $surveyNormalized = $rataRata ? ($rataRata / 5) * 100 : 0;
                    $kandidat->live_skor = $rataRata ? round($rataRata, 2) : 0;
                    $kandidat->survey_normalized = round($surveyNormalized, 2);

                    // CKP
                    $ckp = \App\Models\NilaiCkp::where('periode_id', $periode_id)
                            ->where('pegawai_id', $kandidat->pegawai_id)->first();
                    $nilaiCkp = $ckp ? $ckp->nilai : 0;
                    
                    // Absensi
                    $rekapsAbsen = \App\Models\AbsensiPegawai::where('periode_id', $periode_id)
                                        ->where('pegawai_id', $kandidat->pegawai_id)
                                        ->get();
                    $totalKjk = $rekapsAbsen->sum('kjk');
                    $totalTk = $rekapsAbsen->sum('tk');
                    
                    $nilaiAbsensi = 100;
                    if ($totalTk >= 1) {
                        $nilaiAbsensi = 96;
                    } else {
                        if ($totalKjk == 0) {
                            $nilaiAbsensi = 100;
                        } elseif ($totalKjk >= 1 && $totalKjk <= 60) {
                            $nilaiAbsensi = 99;
                        } elseif ($totalKjk >= 61 && $totalKjk <= 120) {
                            $nilaiAbsensi = 98;
                        } elseif ($totalKjk >= 121 && $totalKjk <= 450) {
                            $nilaiAbsensi = 97;
                        } else {
                            $nilaiAbsensi = 96;
                        }
                    }

                    // Skor Final Gabungan
                    $finalScore = ($nilaiCkp * ($ckpWeight / 100)) + 
                                  ($nilaiAbsensi * ($absensiWeight / 100)) + 
                                  ($surveyNormalized * ($surveyWeight / 100));
                    
                    $kandidat->skor_final = round($finalScore, 2);
                    return $kandidat;
                })
                ->sortByDesc('skor_final')
                ->values();

            $totalKandidat = $kandidats->count();

            // 2. Daftar Absen / Progress per Pegawai (Hanya role Pegawai)
            $semuaPegawai = Pegawai::whereHas('role', function ($query) {
                $query->where('tipe', 'Pegawai');
            })->orderBy('nama')->get();
            
            $progressPegawai = $semuaPegawai->map(function($pegawai) use ($periode_id, $kandidats, $totalKandidat) {
                // Berapa kandidat yang sudah disurvei oleh user ini?
                // Ingat: userId = pegawai_id. Karena 1 user = 1 pegawai.
                $jumlahSudahSurvei = 0;
                $targetSurvei = $totalKandidat;

                // Jika pegawai ini adalah salah satu kandidat, maka dia tidak bisa menilai dirinya sendiri
                $isKandidat = $kandidats->where('pegawai_id', $pegawai->id)->first();
                if ($isKandidat) {
                    $targetSurvei = max(0, $totalKandidat - 1);
                }

                $jumlahSudahSurvei = SurveyProgress::where('periode_id', $periode_id)
                    ->where('user_id', $pegawai->id)
                    ->count();

                $status = 'Belum';
                if ($targetSurvei > 0) {
                    if ($jumlahSudahSurvei >= $targetSurvei) {
                        $status = 'Selesai';
                    } elseif ($jumlahSudahSurvei > 0) {
                        $status = 'Proses';
                    }
                } else {
                     $status = 'Tidak ada target';
                }

                return [
                    'nama' => $pegawai->nama,
                    'nip' => $pegawai->nip,
                    'sudah' => $jumlahSudahSurvei,
                    'target' => $targetSurvei,
                    'status' => $status
                ];
            });

            $pegawaiSelesai = $progressPegawai->where('status', 'Selesai')->count();
        }

        $totalPegawai = Pegawai::whereHas('role', function ($query) {
            $query->where('tipe', 'Pegawai');
        })->count();

        $persentase = $totalPegawai > 0 ? round(($pegawaiSelesai / $totalPegawai) * 100, 1) : 0;

        return view('admin.monitoring.index', compact(
            'periodes', 
            'periode_id', 
            'kandidats', 
            'progressPegawai', 
            'totalPegawai', 
            'pegawaiSelesai', 
            'persentase'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        $periode = PeriodePenilaian::findOrFail($id);
        
        // Jika status diubah ke review_kepala, generate top 3 kandidat
        if ($request->status === 'review_kepala' && $periode->status !== 'review_kepala') {
            \App\Services\KandidatService::generateTop3Kandidat($id);
        }

        $periode->status = $request->status;
        $periode->save();

        return redirect()->back()->with('success', 'Status periode berhasil diubah menjadi ' . ucfirst(str_replace('_', ' ', $request->status)));
    }
}

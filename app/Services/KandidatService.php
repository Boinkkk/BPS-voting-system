<?php

namespace App\Services;

use App\Models\KinerjaPegawai;
use App\Models\Kandidat;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\DB;

class KandidatService
{
    /**
     * Hitung ulang kandidat untuk periode tertentu dan ambil 10 terbaik
     */
    public static function generateTop10Kandidat($periodeId)
    {
        // Pastikan periode ada
        $periode = PeriodePenilaian::find($periodeId);
        if (!$periode) return;

        // Ambil semua pegawai dengan role 'Pegawai'
        $pegawais = \App\Models\Pegawai::whereHas('role', function($q) {
            $q->where('tipe', 'Pegawai');
        })->get();

        $scores = [];

        $pengaturan = \App\Models\PengaturanBobot::first();
        $ckpWeight = $pengaturan ? $pengaturan->ckp : 50;
        $absensiWeight = $pengaturan ? $pengaturan->absensi : 25;
        
        $totalFase1Weight = $ckpWeight + $absensiWeight;
        $relativeCkpWeight = $totalFase1Weight > 0 ? ($ckpWeight / $totalFase1Weight) : 0;
        $relativeAbsensiWeight = $totalFase1Weight > 0 ? ($absensiWeight / $totalFase1Weight) : 0;

        foreach ($pegawais as $pegawai) {
            $idPegawai = $pegawai->id;

            // 1. Ambil Nilai CKP (0 jika tidak ada)
            $ckp = \App\Models\NilaiCkp::where('periode_id', $periodeId)
                        ->where('pegawai_id', $idPegawai)->first();
            $nilaiCkp = $ckp ? $ckp->nilai : 0;
            
            // 2. Hitung Nilai Absensi Triwulan
            $rekapsAbsen = \App\Models\AbsensiPegawai::where('periode_id', $periodeId)
                                ->where('pegawai_id', $idPegawai)
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

            // Jika tidak punya data CKP maupun Absensi sama sekali di periode ini, bisa dilewati
            if (!$ckp && $rekapsAbsen->isEmpty()) {
                continue;
            }

            // Skor Akhir Fase 1 = (Nilai CKP * Bobot Relatif CKP) + (Nilai Absensi * Bobot Relatif Absensi)
            $skorAkhir = ($nilaiCkp * $relativeCkpWeight) + ($nilaiAbsensi * $relativeAbsensiWeight);

            $scores[] = [
                'pegawai_id' => $idPegawai,
                'skor' => $skorAkhir
            ];
        }

        // Urutkan berdasarkan skor tertinggi ke terendah
        usort($scores, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Ambil 10 teratas
        $top10 = array_slice($scores, 0, 10);

        DB::beginTransaction();
        try {
            // Hapus data kandidat lama untuk periode ini
            Kandidat::where('periode_id', $periodeId)->delete();

            // Masukkan 10 kandidat baru
            $rank = 1;
            foreach ($top10 as $k) {
                Kandidat::create([
                    'periode_id' => $periodeId,
                    'pegawai_id' => $k['pegawai_id'],
                    'skor' => $k['skor'],
                    'ranking_sistem' => $rank,
                    'status' => 'aktif'
                ]);
                $rank++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

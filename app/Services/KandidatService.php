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

        foreach ($pegawais as $pegawai) {
            $idPegawai = $pegawai->id;

            // 1. Ambil Nilai CKP (0 jika tidak ada)
            $ckp = \App\Models\NilaiCkp::where('periode_id', $periodeId)
                        ->where('pegawai_id', $idPegawai)->first();
            $nilaiCkp = $ckp ? $ckp->nilai : 0;
            
            // 2. Hitung Bobot Absensi (Total Penalti)
            $bobotAbsensi = 0;
            
            // Ambil data absensi pegawai ini pada periode yang sama
            $rekapsAbsen = \App\Models\AbsensiPegawai::where('periode_id', $periodeId)
                                ->where('pegawai_id', $idPegawai)
                                ->get();
                                
            foreach ($rekapsAbsen as $absen) {
                // Gunakan accessor getPenaltiAttribute
                $bobotAbsensi += $absen->penalti;
            }

            // Jika tidak punya data sama sekali di periode ini, bisa dilewati agar tidak masuk ranking dengan skor 0
            if (!$ckp && $rekapsAbsen->isEmpty()) {
                continue;
            }

            // Skor Akhir = Nilai CKP dikurangi penalti absensi (penalti bernilai negatif)
            $skorAkhir = $nilaiCkp + $bobotAbsensi;

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

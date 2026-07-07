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

        // Ambil semua data kinerja pada periode tersebut
        // Di-group per pegawai
        $kinerjas = KinerjaPegawai::where('periode_id', $periodeId)->get()->groupBy('id_pegawai');

        $scores = [];

        foreach ($kinerjas as $idPegawai => $recordsBulan) {
            $totalSkorBulan = 0;
            $jumlahBulan = $recordsBulan->count();

            foreach ($recordsBulan as $record) {
                // Sesuai instruksi: jika null anggap 0. Pembagi selalu 4.
                $hk = $record->rata_rata_hasil_kerja ?? 0;
                $pr = $record->rata_rata_perilaku ?? 0;
                $kjk = $record->nilai_kjk ?? 0;
                $tl = $record->nilai_tl_psw ?? 0;

                $skorSatuBulan = ($hk + $pr + $kjk + $tl) / 4;
                $totalSkorBulan += $skorSatuBulan;
            }

            // Rata-rata dari seluruh bulan yang ada
            $rataRataKeseluruhan = $jumlahBulan > 0 ? ($totalSkorBulan / $jumlahBulan) : 0;
            
            // Bobot absensi diset 0 untuk saat ini (akan ditambahkan nanti)
            $bobotAbsensi = 0;

            $skorAkhir = $rataRataKeseluruhan + $bobotAbsensi;

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

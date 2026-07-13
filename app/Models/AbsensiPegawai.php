<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AbsensiPegawai extends Model
{
    use HasUuids;

    protected $table = 'absensi_pegawai';

    protected $fillable = [
        'periode_id',
        'pegawai_id',
        'bulan',
        'hk', 'hd', 'tk', 'tl', 'tb', 'pd', 'dk', 'kn', 
        'psw', 'psw1', 'psw2', 'psw3', 'psw4', 
        'ht', 'tl1', 'tl2', 'tl3', 'tl4', 
        'cb', 'cl', 'cm', 'cp', 'cs', 'ct10', 'ct11', 'ct12', 
        'cst1', 'cst2', 'cs1', 'cp1', 'cm1', 'cb1', 
        'kjk_ht', 'kjk_pc', 'kjk'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    /**
     * Hitung total skor penalti absensi per bulan ini.
     * Mengambil nilai bobot secara dinamis dari tabel bobot_penalti (dengan cache).
     */
    public function getPenaltiAttribute()
    {
        $penalti = 0;

        // Ambil bobot dari cache atau DB
        $bobot = \Illuminate\Support\Facades\Cache::rememberForever('bobot_penalti', function () {
            return \App\Models\BobotPenalti::all()->pluck('bobot', 'kode_absen')->toArray();
        });

        // Helper untuk memanggil bobot atau default jika terhapus
        $getBobot = function ($kode, $default) use ($bobot) {
            return isset($bobot[$kode]) ? (float)$bobot[$kode] : $default;
        };

        // Penalti Ringan
        $penalti -= $this->tl1 * $getBobot('TL1', 0.5);
        $penalti -= $this->tl2 * $getBobot('TL2', 0.5);
        $penalti -= $this->psw1 * $getBobot('PSW1', 0.5);
        $penalti -= $this->psw2 * $getBobot('PSW2', 0.5);
        
        // Penalti Sedang
        $penalti -= $this->tl3 * $getBobot('TL3', 1.0);
        $penalti -= $this->psw3 * $getBobot('PSW3', 1.0);
        
        // Penalti Berat
        $penalti -= $this->tk * $getBobot('TK', 2.5);
        $penalti -= $this->tl4 * $getBobot('TL4', 2.5);
        $penalti -= $this->psw4 * $getBobot('PSW4', 2.5);
        
        // KJK
        if ($this->kjk > 0) {
            $penalti -= ($this->kjk / 60) * $getBobot('KJK_PER_JAM', 0.5);
        }

        return $penalti;
    }

    /**
     * Hitung Nilai Presensi Akhir.
     * 1. Apabila TK >= 1, nilai 96
     * 2. Apabila TK = 0, hitung dari KJK:
     *    0 -> 100
     *    1-60 -> 99
     *    61-120 -> 98
     *    121-450 -> 97
     *    >450 -> 96
     */
    public function getNilaiPresensiAttribute()
    {
        // 1. Dapatkan Base Score berdasarkan KJK
        if ($this->kjk == 0) {
            $baseScore = 100;
        } elseif ($this->kjk >= 1 && $this->kjk <= 60) {
            $baseScore = 99;
        } elseif ($this->kjk >= 61 && $this->kjk <= 120) {
            $baseScore = 98;
        } elseif ($this->kjk >= 121 && $this->kjk <= 450) {
            $baseScore = 97;
        } else {
            $baseScore = 96;
        }

        // Ambil pengaturan bobot (jika kosong, gunakan default via dummy object atau array)
        $bobot = \Illuminate\Support\Facades\Cache::rememberForever('pengaturan_bobot_absensi', function () {
            return \App\Models\PengaturanBobot::first();
        });

        if (!$bobot) {
            // Jika belum ada di database, kembalikan baseScore (atau bisa set default value)
            return $baseScore;
        }

        // 2. Kurangi dengan TK
        $baseScore -= ($this->tk * $bobot->bobot_tk);

        // 3. Kurangi dengan HT (Dihapus karena HT = TL1 + TL2 + TL3 + TL4, cukup gunakan TL)
        // $baseScore -= ($this->ht * $bobot->bobot_ht);

        // 4. Kurangi dengan PSW
        if ($bobot->bobot_psw1 > 0 || $bobot->bobot_psw2 > 0 || $bobot->bobot_psw3 > 0 || $bobot->bobot_psw4 > 0) {
            $baseScore -= (
                ($this->psw1 * $bobot->bobot_psw1) + 
                ($this->psw2 * $bobot->bobot_psw2) + 
                ($this->psw3 * $bobot->bobot_psw3) + 
                ($this->psw4 * $bobot->bobot_psw4)
            );
        } else {
            $baseScore -= ($this->psw * $bobot->bobot_psw);
        }

        // 5. Kurangi dengan TL
        if ($bobot->bobot_tl1 > 0 || $bobot->bobot_tl2 > 0 || $bobot->bobot_tl3 > 0 || $bobot->bobot_tl4 > 0) {
            $baseScore -= (
                ($this->tl1 * $bobot->bobot_tl1) + 
                ($this->tl2 * $bobot->bobot_tl2) + 
                ($this->tl3 * $bobot->bobot_tl3) + 
                ($this->tl4 * $bobot->bobot_tl4)
            );
        } else {
            $baseScore -= ($this->tl * $bobot->bobot_tl);
        }

        // Pastikan skor tidak kurang dari 0
        return max(0, $baseScore);
    }
}

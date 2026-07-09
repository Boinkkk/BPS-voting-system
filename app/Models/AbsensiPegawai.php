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
        if ($this->tk >= 1) {
            return 96;
        }

        if ($this->kjk == 0) {
            return 100;
        } elseif ($this->kjk >= 1 && $this->kjk <= 60) {
            return 99;
        } elseif ($this->kjk >= 61 && $this->kjk <= 120) {
            return 98;
        } elseif ($this->kjk >= 121 && $this->kjk <= 450) {
            return 97;
        } else {
            return 96;
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KinerjaPegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kinerja_pegawai';

    protected $fillable = [
        'periode_id',
        'id_pegawai',
        'bulan',
        'rata_rata_hasil_kerja',
        'rata_rata_perilaku',
        'nilai_kjk',
        'nilai_tl_psw',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }
}

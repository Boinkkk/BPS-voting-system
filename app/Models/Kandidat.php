<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Kandidat extends Model
{
    use HasUuids;

    protected $table = 'kandidat';

    protected $fillable = [
        'periode_id',
        'pegawai_id',
        'skor_ckp',
        'skor_absensi',
        'skor',
        'ranking_sistem',
        'status',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }
}

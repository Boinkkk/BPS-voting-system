<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiCkp extends Model
{
    protected $table = 'nilai_ckp';

    protected $fillable = [
        'periode_id',
        'pegawai_id',
        'nilai',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}

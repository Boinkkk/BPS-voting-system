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
        'id_pegawai',
        'id_tipe_absensi',
        'waktu_absensi',
    ];

    protected $casts = [
        'waktu_absensi' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function tipeAbsen()
    {
        return $this->belongsTo(TipeAbsen::class, 'id_tipe_absensi');
    }
}

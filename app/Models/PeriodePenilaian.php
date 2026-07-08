<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodePenilaian extends Model
{
    use HasFactory;

    protected $table = 'periode_penilaian';

    protected $fillable = [
        'triwulan',
        'tahun',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_voting',
        'tanggal_selesai_voting',
        'status',
    ];
}

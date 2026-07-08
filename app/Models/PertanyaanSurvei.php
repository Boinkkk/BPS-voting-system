<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PertanyaanSurvei extends Model
{
    use HasUuids;

    protected $table = 'pertanyaan_survei';

    protected $fillable = [
        'nomor_urut',
        'grup_kategori',
        'kategori',
        'pertanyaan',
        'bobot'
    ];
}

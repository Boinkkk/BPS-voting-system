<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JawabanSurvei extends Model
{
    use HasUuids;

    protected $table = 'jawaban_survei';

    public $timestamps = false;

    protected $fillable = [
        'periode_id',
        'kandidat_id',
        'pertanyaan_id',
        'nilai',
        'waktu_jawab',
    ];

    protected $casts = [
        'waktu_jawab' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BobotPenalti extends Model
{
    protected $table = 'bobot_penalti';

    protected $fillable = [
        'kategori',
        'kode_absen',
        'keterangan',
        'bobot',
    ];
}

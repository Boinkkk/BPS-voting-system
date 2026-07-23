<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeAbsen extends Model
{
    protected $table = 'tipe_absen';

    public $timestamps = false; // Karena di migration tidak ada timestamps

    protected $fillable = [
        'status',
        'bobot',
    ];
}

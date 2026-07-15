<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Glosarium extends Model
{
    protected $table = 'glosariums';
    protected $fillable = ['istilah', 'definisi'];
}

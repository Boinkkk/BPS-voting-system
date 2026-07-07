<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyProgress extends Model
{
    protected $table = 'survey_progress';

    protected $fillable = [
        'periode_id',
        'user_id',
        'kandidat_id',
    ];
}

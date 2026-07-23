<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PengaturanBobot extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'ckp', 'absensi', 'survey',
        'bobot_ht', 'bobot_psw', 'bobot_psw1', 'bobot_psw2', 'bobot_psw3', 'bobot_psw4',
        'bobot_tl', 'bobot_tl1', 'bobot_tl2', 'bobot_tl3', 'bobot_tl4', 'bobot_tk',
    ];
}

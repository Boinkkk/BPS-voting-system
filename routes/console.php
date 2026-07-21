<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Pengumuman;

Schedule::call(function () {
    // Draft -> Published
    Pengumuman::where('status', 'Draft')
        ->whereNotNull('publish_at')
        ->where('publish_at', '<=', now())
        ->update(['status' => 'Published']);

    // Published -> Expired
    Pengumuman::where('status', 'Published')
        ->whereNotNull('expire_at')
        ->where('expire_at', '<=', now())
        ->update(['status' => 'Expired']);
})->everyMinute();

// Database Backup Schedule
Schedule::command('backup:run --only-db')->dailyAt('23:00');


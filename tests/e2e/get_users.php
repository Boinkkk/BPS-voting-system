<?php

use App\Models\Pegawai;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$pegawais = Pegawai::whereHas('role', function ($q) {
    $q->whereIn('tipe', ['Pegawai', 'Kepala Umum']);
})->get(['email', 'nip']);

echo "\n".json_encode($pegawais)."\n";

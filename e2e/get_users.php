<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawais = \App\Models\Pegawai::whereHas('role', function($q) {
    $q->where('tipe', 'Pegawai');
})->get(['email']);

echo "\n" . json_encode($pegawais) . "\n";

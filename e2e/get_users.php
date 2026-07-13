<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawais = \App\Models\Pegawai::whereHas('role', function($q) {
    $q->whereIn('tipe', ['Pegawai', 'Kepala Umum']);
})->get(['email', 'nip']);

echo "\n" . json_encode($pegawais) . "\n";

<?php

use App\Models\Pegawai;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap Laravel
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Fetch only Pegawai (because the test says "Pastikan ada user Pegawai di database")
$users = Pegawai::whereHas('role', function ($query) {
    $query->where('tipe', 'Pegawai');
})->select('email', 'nip')->get()->toArray();

// Output JSON
echo json_encode($users);

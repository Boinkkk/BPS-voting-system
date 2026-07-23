<?php

use App\Models\NilaiCkp;
use App\Models\Pegawai;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$periodeId = 1;
$pegawais = Pegawai::whereHas('role', function ($q) {
    $q->where('tipe', 'Pegawai');
})->get();
foreach ($pegawais as $pegawai) {
    $ckp = NilaiCkp::where('periode_id', $periodeId)->where('pegawai_id', $pegawai->id)->first();
    dump(['id' => $pegawai->id, 'ckp' => $ckp ? $ckp->nilai : 0]);
}

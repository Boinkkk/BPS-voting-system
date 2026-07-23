<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\AbsensiPegawai;
use App\Models\NilaiCkp;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

// Run migrations in memory sqlite
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
Artisan::call('migrate:fresh');

$role = Role::create(['tipe' => 'Pegawai']);
$pegawai = Pegawai::create([
    'id' => (string) Str::uuid(),
    'role_id' => $role->id,
    'nama' => 'P1',
    'nip' => '123',
    'email' => 'e@e.com',
    'password' => 'secret',
    'jabatan' => 'Staff',
    'tanggal_masuk' => '2020-01-01',
    'status_pegawai' => 'aktif',
]);

$periode = PeriodePenilaian::create([
    'nama' => 'P1',
    'triwulan' => 1,
    'tanggal_mulai' => '2026-01-01',
    'tanggal_selesai' => '2026-12-31',
    'status' => 'voting',
]);

NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 100]);
AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 1, 'kjk' => 0]);
AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 2, 'kjk' => 0]);
AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 3, 'kjk' => 0]);

$existingMonths = AbsensiPegawai::where('periode_id', $periode->id)
    ->select('bulan')
    ->distinct()
    ->pluck('bulan')
    ->map(function ($val) {
        return (int) $val;
    })
    ->toArray();

echo 'Existing months: '.json_encode($existingMonths)."\n";
echo 'isDataLengkap: '.($periode->isDataLengkap() ? 'true' : 'false')."\n";

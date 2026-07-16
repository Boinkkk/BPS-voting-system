<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\PeriodePenilaian::where('triwulan', 2)->where('tahun', 2026)->first();
echo "Current Status DB: " . $p->status . "\n";
echo "Start Date: " . $p->tanggal_mulai . "\n";
echo "Selesai Persiapan: " . $p->tanggal_selesai_persiapan . "\n";
echo "Mulai Voting: " . $p->tanggal_mulai_voting . "\n";
echo "Selesai Voting: " . $p->tanggal_selesai_voting . "\n";

\Carbon\Carbon::setTestNow('2026-07-19 12:00:00');
echo "\nTesting with fake time: 2026-07-19 12:00:00\n";
echo "Computed Status: " . $p->computeStatusBasedOnDate(true) . "\n";
echo "Computed Status (no force): " . $p->computeStatusBasedOnDate() . "\n";

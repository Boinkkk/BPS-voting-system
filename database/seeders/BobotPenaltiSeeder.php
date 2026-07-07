<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BobotPenalti;

class BobotPenaltiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Penalti Ringan (-0.5)
            ['kategori' => 'Ringan', 'kode_absen' => 'TL1', 'keterangan' => 'Keterlambatan <= 30 menit', 'bobot' => 0.5],
            ['kategori' => 'Ringan', 'kode_absen' => 'TL2', 'keterangan' => 'Keterlambatan 30 - 60 menit', 'bobot' => 0.5],
            ['kategori' => 'Ringan', 'kode_absen' => 'PSW1', 'keterangan' => 'Pulang Sebelum Waktunya <= 30 menit', 'bobot' => 0.5],
            ['kategori' => 'Ringan', 'kode_absen' => 'PSW2', 'keterangan' => 'Pulang Sebelum Waktunya 30 - 60 menit', 'bobot' => 0.5],
            
            // Penalti Sedang (-1.0)
            ['kategori' => 'Sedang', 'kode_absen' => 'TL3', 'keterangan' => 'Keterlambatan 60 - 90 menit', 'bobot' => 1.0],
            ['kategori' => 'Sedang', 'kode_absen' => 'PSW3', 'keterangan' => 'Pulang Sebelum Waktunya 60 - 90 menit', 'bobot' => 1.0],
            
            // Penalti Berat (-2.5)
            ['kategori' => 'Berat', 'kode_absen' => 'TK', 'keterangan' => 'Tanpa Kabar', 'bobot' => 2.5],
            ['kategori' => 'Berat', 'kode_absen' => 'TL4', 'keterangan' => 'Keterlambatan > 90 menit', 'bobot' => 2.5],
            ['kategori' => 'Berat', 'kode_absen' => 'PSW4', 'keterangan' => 'Pulang Sebelum Waktunya > 90 menit', 'bobot' => 2.5],
            
            // KJK
            ['kategori' => 'KJK', 'kode_absen' => 'KJK_PER_JAM', 'keterangan' => 'Pengurangan poin per 60 menit (Total KJK)', 'bobot' => 0.5],
        ];

        foreach ($data as $d) {
            BobotPenalti::updateOrCreate(
                ['kode_absen' => $d['kode_absen']],
                $d
            );
        }
    }
}

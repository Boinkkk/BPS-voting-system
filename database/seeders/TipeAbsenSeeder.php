<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipeAbsenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['status' => 'Masuk', 'bobot' => 1.0],
            ['status' => 'Izin', 'bobot' => 0.5],
            ['status' => 'Sakit', 'bobot' => 0.5],
            ['status' => 'Alpha', 'bobot' => 0.0],
        ];

        foreach ($data as $item) {
            DB::table('tipe_absen')->updateOrInsert(
                ['status' => $item['status']],
                ['bobot' => $item['bobot']]
            );
        }
    }
}

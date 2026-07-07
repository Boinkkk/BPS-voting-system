<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil seeder khusus pegawai dari Excel / Screenshot
        // Ini akan secara otomatis membuat Role, Departemen, dan Pegawai beserta Akun Testing dengan UUID
        $this->call(PegawaiSeeder::class);
    }
}

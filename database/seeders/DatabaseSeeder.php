<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Seed Role
        $roles = [
            ['tipe' => 'Admin'],
            ['tipe' => 'HRD'],
            ['tipe' => 'Pegawai'],
        ];
        DB::table('role')->insert($roles);

        // 2. Seed Departemen
        $departemens = [
            ['nama' => 'IT', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Keuangan', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Pemasaran', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('departemen')->insert($departemens);

        // 3. Seed Users
        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Budi HRD',
                'email' => 'hrd@gmail.com',
                'password' => Hash::make('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Andi Pegawai',
                'email' => 'andi@gmail.com',
                'password' => Hash::make('password123'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('users')->insert($users);

        // 4. Seed Pegawai
        $pegawais = [
            [
                'role_id' => 1, // Admin
                'departemen_id' => 1, // IT
                'jabatan' => 'System Administrator',
                'nama' => 'Admin Sistem',
                'nip' => '1001001',
                'email' => 'admin@gmail.com',
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 2, // HRD
                'departemen_id' => 2, // Keuangan (misal)
                'jabatan' => 'HR Manager',
                'nama' => 'Budi HRD',
                'nip' => '1001002',
                'email' => 'hrd@gmail.com',
                'tanggal_masuk' => '2021-02-15',
                'status_pegawai' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => 3, // Pegawai
                'departemen_id' => 3, // Pemasaran
                'jabatan' => 'Marketing Staff',
                'nama' => 'Andi Pegawai',
                'nip' => '1001003',
                'email' => 'andi@gmail.com',
                'tanggal_masuk' => '2022-03-20',
                'status_pegawai' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('pegawai')->insert($pegawais);
    }
}

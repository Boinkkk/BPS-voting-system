<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');
        $now = Carbon::now();

        // Ensure Departments exist
        $deptUmum = Departemen::firstOrCreate(['nama' => 'Umum']);
        $deptStatistik = Departemen::firstOrCreate(['nama' => 'Statistik']);
        $deptIT = Departemen::firstOrCreate(['nama' => 'IT / Pranata Komputer']);

        // Ensure Roles exist
        $roleAdmin = Role::firstOrCreate(['tipe' => 'Admin']);
        $roleKepalaKantor = Role::firstOrCreate(['tipe' => 'Kepala Kantor']);
        $rolePegawai = Role::firstOrCreate(['tipe' => 'Pegawai']);
        $roleKepalaUmum = Role::firstOrCreate(['tipe' => 'Kepala Umum']);

        // 1. Create Special Testing Accounts
        Pegawai::updateOrCreate(
            ['email' => 'admin@bps.go.id'],
            [
                'role_id' => $roleAdmin->id,
                'departemen_id' => $deptIT->id,
                'jabatan' => 'Admin Sistem',
                'nama' => 'Akun Admin',
                'nip' => '3899281',
                'password' => $password,
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
            ]
        );

        Pegawai::updateOrCreate(
            ['email' => 'kepala@bps.go.id'],
            [
                'role_id' => $roleKepalaKantor->id,
                'departemen_id' => $deptUmum->id,
                'jabatan' => 'Kepala BPS',
                'nama' => 'Akun Kepala',
                'nip' => '340921212',
                'password' => $password,
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
            ]
        );

        Pegawai::updateOrCreate(
            ['email' => 'kepalaumum@bps.go.id'],
            [
                'role_id' => $roleKepalaUmum->id,
                'departemen_id' => $deptUmum->id,
                'jabatan' => 'Kepala Umum BPS',
                'nama' => 'Akun Kepala Umum',
                'nip' => '340921922',
                'password' => $password,
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
            ]
        );

        Pegawai::updateOrCreate(
            ['email' => 'pegawai@bps.go.id'],
            [
                'role_id' => $rolePegawai->id,
                'departemen_id' => $deptStatistik->id,
                'jabatan' => 'Statistisi Ahli Pertama',
                'nama' => 'Akun Pegawai',
                'nip' => '3212031831',
                'password' => $password,
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
            ]
        );

        // 2. Read from CSV data_pegawai.csv
        $csvPath = base_path('data/data_pegawai.csv');
        if (file_exists($csvPath)) {
            $file = fopen($csvPath, 'r');
            $header = fgetcsv($file); // Read header: NIP, Nama

            $index = 0;
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) < 2) {
                    continue;
                }

                $nip = trim($row[0]);
                $nama = trim($row[1]);
                $jabatan = 'Pelaksana'; // Default jabatan since CSV only has NIP and Nama

                // Map to department
                $deptId = $deptUmum->id;

                // Generate Email
                $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $nama)[0])).($index + 1).'@bps.go.id';

                Pegawai::updateOrCreate(
                    ['nip' => $nip], // Match by NIP now since it's the real one
                    [
                        'email' => $email,
                        'role_id' => $rolePegawai->id,
                        'departemen_id' => $deptId,
                        'jabatan' => $jabatan,
                        'nama' => $nama,
                        'password' => $password,
                        'tanggal_masuk' => '2023-01-01',
                        'status_pegawai' => 'aktif',
                    ]
                );

                $index++;
            }
            fclose($file);
        } else {
            $this->command->error('File data_pegawai.csv tidak ditemukan di '.$csvPath);
        }
    }
}

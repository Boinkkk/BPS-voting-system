<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Pegawai;
use App\Models\PengaturanBobot;
use Illuminate\Support\Str;

class E2ESeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Bersihkan data lama
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pegawai::truncate();
        Role::truncate();
        PengaturanBobot::truncate();
        \App\Models\PeriodePenilaian::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Setup Roles
        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $roleTimPenilai = Role::create(['tipe' => 'Tim Penilai']);
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $roleKepalaBagian = Role::create(['tipe' => 'Kepala Bagian']);
        $roleKepalaUmum = Role::create(['tipe' => 'Kepala Umum']);
        $roleKepalaKantor = Role::create(['tipe' => 'Kepala Kantor']);

        // 2. Setup Pengaturan Bobot
        PengaturanBobot::create([
            'ckp' => 50,
            'absensi' => 25,
            'survey' => 25,
            'bobot_psw' => 1.5,
            'bobot_psw1' => 0,
            'bobot_psw2' => 0,
            'bobot_psw3' => 0,
            'bobot_psw4' => 0,
            'bobot_tl' => 1.0,
            'bobot_tl1' => 0,
            'bobot_tl2' => 0,
            'bobot_tl3' => 0,
            'bobot_tl4' => 0,
            'bobot_tk' => 2.0,
        ]);

        // 3. Setup Pegawai Khusus untuk E2E Testing
        
        // Admin
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Admin Testing',
            'nip' => '100000000000000001',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $roleAdmin->id,
            'jabatan' => 'Admin Sistem',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        // Kepala Kantor
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Kepala Kantor Testing',
            'nip' => '100000000000000002',
            'email' => 'kepala@bps.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $roleKepalaKantor->id,
            'jabatan' => 'Kepala BPS',
            'tanggal_masuk' => '2005-01-01',
            'status_pegawai' => 'aktif',
        ]);
        
        // Tim Penilai / Kepala Bagian
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Tim Penilai Testing',
            'nip' => '100000000000000003',
            'email' => 'timpenilai@bps.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $roleKepalaBagian->id,
            'jabatan' => 'Kepala Sub Bagian Umum',
            'tanggal_masuk' => '2012-01-01',
            'status_pegawai' => 'aktif',
        ]);

        // Pegawai Biasa (1)
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Pegawai Satu',
            'nip' => '200000000000000001',
            'email' => 'pegawai1@bps.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $rolePegawai->id,
            'jabatan' => 'Statistisi Ahli Pertama',
            'tanggal_masuk' => '2015-01-01',
            'status_pegawai' => 'aktif',
        ]);
        
        // Pegawai Biasa (2)
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Pegawai Dua',
            'nip' => '200000000000000002',
            'email' => 'pegawai2@bps.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $rolePegawai->id,
            'jabatan' => 'Prakom Ahli Pertama',
            'tanggal_masuk' => '2016-01-01',
            'status_pegawai' => 'aktif',
        ]);
        
        // Pegawai Biasa (3)
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Pegawai Tiga',
            'nip' => '200000000000000003',
            'email' => 'pegawai3@bps.go.id',
            'password' => bcrypt('password123'),
            'role_id' => $rolePegawai->id,
            'jabatan' => 'Statistisi Ahli Muda',
            'tanggal_masuk' => '2014-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }
}

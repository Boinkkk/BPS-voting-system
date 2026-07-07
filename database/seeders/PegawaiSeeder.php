<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\Role;
use Carbon\Carbon;

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
        $roleKepala = Role::firstOrCreate(['tipe' => 'Kepala']);
        $rolePegawai = Role::firstOrCreate(['tipe' => 'Pegawai']);

        // 1. Create Special Testing Accounts
        Pegawai::updateOrCreate(
            ['email' => 'admin@bps.go.id'],
            [
                'role_id' => $roleAdmin->id,
                'departemen_id' => $deptIT->id,
                'jabatan' => 'Admin Sistem',
                'nama' => 'Akun Admin',
                'nip' => 'admin001',
                'password' => $password,
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
            ]
        );

        Pegawai::updateOrCreate(
            ['email' => 'kepala@bps.go.id'],
            [
                'role_id' => $roleKepala->id,
                'departemen_id' => $deptUmum->id,
                'jabatan' => 'Kepala BPS',
                'nama' => 'Akun Kepala',
                'nip' => 'kepala001',
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
                'nip' => 'pegawai001',
                'password' => $password,
                'tanggal_masuk' => '2020-01-01',
                'status_pegawai' => 'aktif',
            ]
        );

        // 2. Insert Original Employees from Image
        $pegawaiData = [
            ["Afysha Diadara S.Tr.Stat.", "Pelaksana"],
            ["Agung Tika Wicaksono S.Tr.Stat", "Statistisi Ahli Pertama"],
            ["Amir Rifa'i", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Amoi Sandra Lesmana A.Md.Stat", "Pelaksana"],
            ["Amyroh Sintia Dewi A.Md.Stat.", "Pelaksana"],
            ["Ardin Feri Syukur Gultom S.Tr.Stat.", "Statistisi Ahli Pertama"],
            ["Daryatno", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Dicki Prayogi A.Md.Stat.", "Pelaksana"],
            ["Didit Kurniawan A.Md", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Dino Asmono S.Ak.", "Statistisi Ahli Pertama"],
            ["Elfira Meirosa", "Pelaksana"],
            ["Erlina Nur Syamsiyah S.Tr.Stat.", "Pelaksana"],
            ["Fajar Maulana", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Farikha Fia Fatmawati S.Tr.Stat", "Statistisi Ahli Pertama"],
            ["Fathanya Puja Anggaresa S.Tr.Stat.", "Statistisi Ahli Pertama"],
            ["Gideon Marpaung S.Tr.Stat.", "Statistisi Ahli Pertama"],
            ["Irfan Satriadi S.Si", "Statistisi Ahli Muda"],
            ["Joko", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Jovanka Marya Sitompul A.Md.Stat.", "Statistisi Pelaksana/Terampil"],
            ["Mohd. Arief Fadillah", "Statistisi Pelaksana/Terampil"],
            ["Muhammad Hafizh Eka Putra S.Tr.Stat.", "Pranata Komputer Ahli Pertama"],
            ["Mutia Hanifah A.Md.Stat.", "Statistisi Pelaksana/Terampil"],
            ["Nana Fitriana SST", "Pranata Komputer Ahli Muda"],
            ["Poernawan Zuhri", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Radhitya Noor Adhyaksani S.Tr.Stat.", "Statistisi Ahli Pertama"],
            ["Rahmadathul Wisdawati A.Md.Stat.", "Statistisi Pelaksana/Terampil"],
            ["Razali", "Statistisi Pelaksana Lanjutan/Mahir"],
            ["Rizka Mei Wulan SST", "Kepala Subbagian Umum"],
            ["Saor Hasudungan Sitompul SE", "Statistisi Ahli Muda"],
            ["Ulil Amri A.Md.Kom", "Pranata Komputer Pelaksana/Terampil"],
            ["Yudika Simatupang S.Tr.Stat.", "Statistisi Ahli Pertama"]
        ];

        foreach ($pegawaiData as $index => $p) {
            $nama = $p[0];
            $jabatan = $p[1];
            
            // Map to department
            $deptId = $deptStatistik->id;
            if (strpos(strtolower($jabatan), 'pranata komputer') !== false) {
                $deptId = $deptIT->id;
            } elseif (strpos(strtolower($jabatan), 'umum') !== false || $jabatan === 'Pelaksana') {
                $deptId = $deptUmum->id;
            }

            // Generate dummy NIP and Email
            $nip = '19' . rand(70, 99) . '010120' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $nama)[0])) . ($index + 1) . '@bps.go.id';

            Pegawai::updateOrCreate(
                ['email' => $email],
                [
                    'role_id' => $rolePegawai->id,
                    'departemen_id' => $deptId,
                    'jabatan' => $jabatan,
                    'nama' => $nama,
                    'nip' => $nip,
                    'password' => $password,
                    'tanggal_masuk' => '2023-01-01',
                    'status_pegawai' => 'aktif',
                ]
            );
        }
    }
}

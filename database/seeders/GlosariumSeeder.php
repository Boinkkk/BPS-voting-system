<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlosariumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $glosariums = [
            [
                'istilah' => 'Absensi Pegawai',
                'definisi' => 'Catatan kehadiran pegawai dalam suatu periode, termasuk jumlah alfa, sakit, izin, dan cuti yang dapat mempengaruhi penilaian akhir.'
            ],
            [
                'istilah' => 'Bobot Penalti',
                'definisi' => 'Nilai pengurangan (penalti) yang diberikan berdasarkan rekam jejak ketidakhadiran (seperti alfa, sakit, atau izin).'
            ],
            [
                'istilah' => 'CKP (Capaian Kinerja Pegawai)',
                'definisi' => 'Penilaian kinerja pegawai berdasarkan target kuantitas dan kualitas yang telah ditetapkan sebelumnya oleh atasan.'
            ],
            [
                'istilah' => 'Kandidat',
                'definisi' => 'Pegawai yang telah lolos kualifikasi dan masuk nominasi untuk dipilih sebagai pegawai terbaik (fase voting).'
            ],
            [
                'istilah' => 'Periode Penilaian',
                'definisi' => 'Rentang waktu (misalnya bulanan atau triwulan) dimana evaluasi kinerja dan pemilihan dilakukan dalam sistem ini.'
            ],
            [
                'istilah' => 'Alur Voting (Fase 1 - 3)',
                'definisi' => "Fase 1: Filter otomatis Kandidat berdasarkan nilai kuantitatif (CKP dan Absensi) yang memenuhi ambang batas.\nFase 2: Survei/Voting oleh rekan kerja dan atasan (kuantifikasi subjektif) terhadap kandidat hasil Fase 1.\nFase 3: Pleno/Keputusan akhir oleh Kepala Unit untuk memilih pemenang utama dari top kandidat hasil akumulasi seluruh nilai."
            ],
        ];

        foreach ($glosariums as $g) {
            \App\Models\Glosarium::create($g);
        }
    }
}

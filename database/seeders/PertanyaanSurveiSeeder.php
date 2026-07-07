<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PertanyaanSurvei;

class PertanyaanSurveiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nomor_urut' => 1,
                'kategori' => 'Berorientasi Pelayanan',
                'pertanyaan' => 'Pegawai memberikan pelayanan yang ramah, cepat, responsif, dan berorientasi pada kebutuhan pengguna layanan.'
            ],
            [
                'nomor_urut' => 2,
                'kategori' => 'Akuntabel',
                'pertanyaan' => 'Pegawai melaksanakan tugas secara jujur, bertanggung jawab, dan dapat dipertanggungjawabkan.'
            ],
            [
                'nomor_urut' => 3,
                'kategori' => 'Kompeten',
                'pertanyaan' => 'Pegawai memiliki pengetahuan dan kemampuan yang memadai dalam menjalankan tugas serta memberikan pelayanan.'
            ],
            [
                'nomor_urut' => 4,
                'kategori' => 'Harmonis',
                'pertanyaan' => 'Pegawai bersikap sopan, menghargai orang lain, serta mampu menjaga hubungan kerja yang baik.'
            ],
            [
                'nomor_urut' => 5,
                'kategori' => 'Loyal',
                'pertanyaan' => 'Pegawai menunjukkan komitmen, integritas, dan loyalitas terhadap organisasi serta melaksanakan tugas sesuai ketentuan.'
            ],
            [
                'nomor_urut' => 6,
                'kategori' => 'Adaptif',
                'pertanyaan' => 'Pegawai mampu menyesuaikan diri dengan perubahan, menerima masukan, dan terbuka terhadap inovasi.'
            ],
            [
                'nomor_urut' => 7,
                'kategori' => 'Kolaboratif',
                'pertanyaan' => 'Pegawai mampu bekerja sama dengan rekan kerja maupun pihak lain untuk mencapai tujuan bersama.'
            ],
        ];

        foreach ($data as $d) {
            PertanyaanSurvei::updateOrCreate(
                ['kategori' => $d['kategori']],
                $d
            );
        }
    }
}

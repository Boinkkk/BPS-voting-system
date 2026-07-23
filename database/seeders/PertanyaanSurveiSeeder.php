<?php

namespace Database\Seeders;

use App\Models\PertanyaanSurvei;
use Illuminate\Database\Seeder;

class PertanyaanSurveiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nomor_urut' => 1,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Berorientasi Pelayanan',
                'pertanyaan' => 'Pegawai memberikan pelayanan yang ramah, cepat, responsif, dan berorientasi pada kebutuhan pengguna layanan.',
            ],
            [
                'nomor_urut' => 2,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Akuntabel',
                'pertanyaan' => 'Pegawai melaksanakan tugas secara jujur, bertanggung jawab, dan dapat dipertanggungjawabkan.',
            ],
            [
                'nomor_urut' => 3,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Kompeten',
                'pertanyaan' => 'Pegawai memiliki pengetahuan dan kemampuan yang memadai dalam menjalankan tugas serta memberikan pelayanan.',
            ],
            [
                'nomor_urut' => 4,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Harmonis',
                'pertanyaan' => 'Pegawai bersikap sopan, menghargai orang lain, serta mampu menjaga hubungan kerja yang baik.',
            ],
            [
                'nomor_urut' => 5,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Loyal',
                'pertanyaan' => 'Pegawai menunjukkan komitmen, integritas, dan loyalitas terhadap organisasi serta melaksanakan tugas sesuai ketentuan.',
            ],
            [
                'nomor_urut' => 6,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Adaptif',
                'pertanyaan' => 'Pegawai mampu menyesuaikan diri dengan perubahan, menerima masukan, dan terbuka terhadap inovasi.',
            ],
            [
                'nomor_urut' => 7,
                'grup_kategori' => 'BerAKHLAK',
                'kategori' => 'Kolaboratif',
                'pertanyaan' => 'Pegawai mampu bekerja sama dengan rekan kerja maupun pihak lain untuk mencapai tujuan bersama.',
            ],
            [
                'nomor_urut' => 8,
                'grup_kategori' => 'Nilai Organisasi BPS',
                'kategori' => 'Be A Leader not A Boss',
                'pertanyaan' => 'Pegawai mampu memimpin, membimbing, dan menginspirasi rekan kerja, bukan sekadar memberikan perintah.',
            ],
            [
                'nomor_urut' => 9,
                'grup_kategori' => 'Nilai Organisasi BPS',
                'kategori' => 'Inovasi di setiap Lini',
                'pertanyaan' => 'Pegawai selalu berusaha menemukan cara baru yang lebih efektif dan efisien dalam menyelesaikan pekerjaan.',
            ],
            [
                'nomor_urut' => 10,
                'grup_kategori' => 'Nilai Organisasi BPS',
                'kategori' => 'Komunikasi, Koordinasi dan Diplomasi (KKD)',
                'pertanyaan' => 'Pegawai mampu berkomunikasi dengan baik, berkoordinasi antartim, dan menjalin diplomasi yang efektif.',
            ],
            [
                'nomor_urut' => 11,
                'grup_kategori' => 'Nilai Organisasi BPS',
                'kategori' => 'Kualitas Data',
                'pertanyaan' => 'Pegawai selalu menjaga akurasi, validitas, dan keandalan data dalam setiap tahap pekerjaan.',
            ],
            [
                'nomor_urut' => 12,
                'grup_kategori' => 'Nilai Organisasi BPS',
                'kategori' => 'Kerja Keras dan Kerja Cerdas',
                'pertanyaan' => 'Pegawai bekerja dengan tekun, pantang menyerah, dan menggunakan strategi yang cerdas untuk mencapai hasil optimal.',
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

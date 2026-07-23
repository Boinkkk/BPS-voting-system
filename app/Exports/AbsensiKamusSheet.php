<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AbsensiKamusSheet implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Kamus';
    }

    public function headings(): array
    {
        return [
            'Kode', 'Keterangan',
        ];
    }

    public function collection()
    {
        return collect([
            ['HK', 'Hari Kerja dalam 1 bulan'],
            ['HD', 'Hadir dalam 1 bulan'],
            ['TK', 'Tanpa Kabar'],
            ['TL', 'Tugas Luar'],
            ['TB', 'Tugas Belajar'],
            ['PD', 'Perjalanan Dinas'],
            ['DK', 'Diklat/Pelatihan'],
            ['KN', 'Konsinyasi'],
            ['PSW', 'Pulang Sebelum Waktunya'],
            ['PSW 1', 'PSW <= 30 menit'],
            ['PSW 2', 'PSW 30 - 60 menit'],
            ['PSW 3', 'PSW 60 - 90 menit'],
            ['PSW 4', 'PSW > 90 atau tidak melakukan absensi'],
            ['HT', 'Hadir Terlambat atau Jumlah dari TL1+TL2+TL3+TL4'],
            ['TL 1', 'Keterlambatan <= 30 menit'],
            ['TL 2', 'Keterlambatan 30 - 60 menit'],
            ['TL 3', 'Keterlambatan 60 - 90 menit'],
            ['TL 4', 'Keterlambatan > 90 menit atau tidak melakukan absensi'],
            ['CB', 'Cuti Besar'],
            ['CL', 'Cuti LTN'],
            ['CM', 'Cuti Melahirkan'],
            ['CP', 'Cuti Penting'],
            ['CS', 'Cuti Sakit'],
            ['CT 10', 'Cuti 2 Tahun Lalu'],
            ['CT 11', 'Cuti Tahun Sekarang'],
            ['CT 12', 'Cuti Tahun Lalu'],
            ['CST1', 'Cuti Setengah Hari Pagi'],
            ['CST2', 'Cuti Setengah Hari Siang'],
            ['CS1', 'Cuti Sakit Tanpa Potongan'],
            ['CP1', 'Cuti Penting Tanpa Potongan'],
            ['CM1', 'Cuti Melahirkan Tanpa Potongan'],
            ['CB1', 'Cuti Besar Tanpa Potongan'],
            ['KJK HT', 'Kekurangan Jam Kerja Hadir Terlambat (dalam HH:MM / menit)'],
            ['KJK PC', 'Kekurangan Jam Kerja Pulang Sebelum Waktunya (dalam HH:MM / menit)'],
            ['KJK', 'Kekurangan Jam Kerja (KJK HT + KJK PSW)'],
        ]);
    }
}

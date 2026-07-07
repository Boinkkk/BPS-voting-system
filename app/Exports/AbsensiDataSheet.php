<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AbsensiDataSheet implements WithTitle, WithHeadings, WithCustomStartCell, WithEvents
{
    public function title(): string
    {
        return 'Rekap Presensi Satker';
    }

    public function startCell(): string
    {
        return 'A7'; // Header mulai di baris ke-7
    }

    public function headings(): array
    {
        return [
            'NIP', 'Nama', 'HK', 'HD', 'TK', 'TL', 'TB', 'PD', 'DK', 'KN', 
            'PSW', 'PSW1', 'PSW2', 'PSW3', 'PSW4', 
            'HT', 'TL1', 'TL2', 'TL3', 'TL4', 
            'CB', 'CL', 'CM', 'CP', 'CS', 'CT 10', 'CT 11', 'CT 12', 
            'CST1', 'CST2', 'CS1', 'CP1', 'CM1', 'CB1', 
            'KJK HT', 'KJK PC', 'KJK'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tambahkan metadata laporan di baris 1-6
                $sheet = $event->sheet->getDelegate();
                $sheet->setCellValue('A1', 'LAPORAN REKAP PRESENSI SATKER');
                $sheet->setCellValue('A2', 'Badan Pusat Statistik');
                $sheet->setCellValue('A3', 'Tahun: 2026');
                $sheet->setCellValue('A4', 'Bulan: (Isi dengan angka bulan, misal: 10)');
                $sheet->setCellValue('A5', 'Periode Penilaian ID: (Isi dengan ID periode)');
                
                // Styling
                $sheet->getStyle('A7:AK7')->getFont()->setBold(true);
            },
        ];
    }
}

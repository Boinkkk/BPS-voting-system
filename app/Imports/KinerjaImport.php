<?php

namespace App\Imports;

use App\Models\KinerjaPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class KinerjaImport implements ToCollection
{
    protected $periodeId;

    public function __construct($periodeId)
    {
        $this->periodeId = $periodeId;
    }

    public function collection(Collection $rows)
    {
        // Peta nama bulan ke integer
        $bulanMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        // Temukan baris header utama (yang berisi nama bulan)
        // Biasanya berada di baris 0 atau 1
        $headerRow = null;
        $subHeaderRow = null;
        $dataStartIndex = 2; // Default jika header di 0 dan 1

        foreach ($rows as $index => $row) {
            $rowValues = array_map(function ($val) {
                return strtolower(trim((string) $val));
            }, $row->toArray());

            if (in_array('nama', $rowValues) || in_array('oktober', $rowValues)) {
                $headerRow = $rowValues;
                $subHeaderRow = array_map(function ($val) {
                    return strtolower(trim((string) $val));
                }, $rows[$index + 1]->toArray());
                $dataStartIndex = $index + 2;
                break;
            }
        }

        if (! $headerRow || ! $subHeaderRow) {
            throw new \Exception('Format header Excel tidak dikenali. Pastikan terdapat baris header yang berisi Nama dan Bulan.');
        }

        // Petakan indeks kolom untuk setiap bulan yang ditemukan di header pertama
        $monthColumns = [];
        $currentMonth = null;

        foreach ($headerRow as $colIndex => $val) {
            if (isset($bulanMap[$val])) {
                $currentMonth = $bulanMap[$val];
                $monthColumns[$currentMonth] = [
                    'start' => $colIndex,
                    'hasil_kerja' => null,
                    'perilaku' => null,
                    'kjk' => null,
                    'tl_psw' => null,
                ];
            }

            if ($currentMonth) {
                // Cek sub-header untuk bulan ini
                $subVal = $subHeaderRow[$colIndex] ?? '';
                if (str_contains($subVal, 'hasil kerja')) {
                    $monthColumns[$currentMonth]['hasil_kerja'] = $colIndex;
                } elseif (str_contains($subVal, 'perilaku')) {
                    $monthColumns[$currentMonth]['perilaku'] = $colIndex;
                } elseif (str_contains($subVal, 'kjk')) {
                    $monthColumns[$currentMonth]['kjk'] = $colIndex;
                } elseif (str_contains($subVal, 'tl')) {
                    $monthColumns[$currentMonth]['tl_psw'] = $colIndex;
                }
            }
        }

        // Cari index kolom Nama
        $namaIndex = array_search('nama', $headerRow);
        if ($namaIndex === false) {
            throw new \Exception("Kolom 'Nama' tidak ditemukan di header Excel.");
        }

        // Proses baris data
        for ($i = $dataStartIndex; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Skip baris kosong
            if (! isset($row[$namaIndex]) || trim($row[$namaIndex]) === '') {
                continue;
            }

            $nama = trim($row[$namaIndex]);
            $pegawai = Pegawai::where('nama', $nama)->first();

            // Sesuai instruksi: abaikan baris yang namanya salah/tidak cocok
            if (! $pegawai) {
                continue;
            }

            // Loop setiap bulan yang terpetakan dan simpan nilainya
            foreach ($monthColumns as $bulanInt => $cols) {
                // Jika kolom hasil_kerja atau perilaku tidak dipetakan, lewati bulan ini
                if ($cols['hasil_kerja'] === null && $cols['perilaku'] === null) {
                    continue;
                }

                $rawHasilKerja = $row[$cols['hasil_kerja']] ?? 0;
                $rawPerilaku = $row[$cols['perilaku']] ?? 0;
                $rawKjk = $row[$cols['kjk']] ?? null;
                $rawTlPsw = $row[$cols['tl_psw']] ?? null;

                // Format numbers (ganti koma dengan titik)
                $hasilKerja = (float) str_replace(',', '.', $rawHasilKerja);
                $perilaku = (float) str_replace(',', '.', $rawPerilaku);
                $kjk = $rawKjk !== null && $rawKjk !== '' ? (float) str_replace(',', '.', $rawKjk) : null;
                $tlPsw = $rawTlPsw !== null && $rawTlPsw !== '' ? (float) str_replace(',', '.', $rawTlPsw) : null;

                // Jangan simpan jika semuanya 0 atau kosong (berarti data belum diisi)
                if (empty($rawHasilKerja) && empty($rawPerilaku)) {
                    continue;
                }

                KinerjaPegawai::updateOrCreate(
                    [
                        'periode_id' => $this->periodeId,
                        'id_pegawai' => $pegawai->id,
                        'bulan' => $bulanInt,
                    ],
                    [
                        'rata_rata_hasil_kerja' => $hasilKerja,
                        'rata_rata_perilaku' => $perilaku,
                        'nilai_kjk' => $kjk,
                        'nilai_tl_psw' => $tlPsw,
                    ]
                );
            }
        }
    }
}

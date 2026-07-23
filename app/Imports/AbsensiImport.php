<?php

namespace App\Imports;

use App\Models\AbsensiPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AbsensiImport implements ToCollection, WithStartRow
{
    protected $periodeId;

    protected $bulan;

    public function __construct($periodeId, $bulan)
    {
        $this->periodeId = $periodeId;
        $this->bulan = $bulan;
    }

    public function startRow(): int
    {
        // Mulai baca data dari baris ke-8 (karena baris 7 adalah header, 1-6 adalah metadata)
        return 8;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Cek apakah NIP kosong, jika ya, skip
            if (! isset($row[0]) || empty(trim($row[0]))) {
                continue;
            }

            // Cari pegawai berdasarkan NIP
            $nip = trim($row[0]);
            $pegawai = Pegawai::where('nip', $nip)->first();

            if ($pegawai) {
                // Konversi data string ke integer
                $parseInt = function ($val) {
                    return intval(trim($val) ?: 0);
                };

                AbsensiPegawai::updateOrCreate(
                    [
                        'periode_id' => $this->periodeId,
                        'pegawai_id' => $pegawai->id,
                        'bulan' => $this->bulan,
                    ],
                    [
                        'hk' => $parseInt($row[2]),
                        'hd' => $parseInt($row[3]),
                        'tk' => $parseInt($row[4]),
                        'tl' => $parseInt($row[5]),
                        'tb' => $parseInt($row[6]),
                        'pd' => $parseInt($row[7]),
                        'dk' => $parseInt($row[8]),
                        'kn' => $parseInt($row[9]),

                        'psw' => $parseInt($row[10]),
                        'psw1' => $parseInt($row[11]),
                        'psw2' => $parseInt($row[12]),
                        'psw3' => $parseInt($row[13]),
                        'psw4' => $parseInt($row[14]),

                        'ht' => $parseInt($row[15]),
                        'tl1' => $parseInt($row[16]),
                        'tl2' => $parseInt($row[17]),
                        'tl3' => $parseInt($row[18]),
                        'tl4' => $parseInt($row[19]),

                        'cb' => $parseInt($row[20]),
                        'cl' => $parseInt($row[21]),
                        'cm' => $parseInt($row[22]),
                        'cp' => $parseInt($row[23]),
                        'cs' => $parseInt($row[24]),
                        'ct10' => $parseInt($row[25]),
                        'ct11' => $parseInt($row[26]),
                        'ct12' => $parseInt($row[27]),

                        'cst1' => $parseInt($row[28]),
                        'cst2' => $parseInt($row[29]),
                        'cs1' => $parseInt($row[30]),
                        'cp1' => $parseInt($row[31]),
                        'cm1' => $parseInt($row[32]),
                        'cb1' => $parseInt($row[33]),

                        'kjk_ht' => $parseInt($row[34]),
                        'kjk_pc' => $parseInt($row[35]),
                        'kjk' => $parseInt($row[36]),
                    ]
                );
            }
        }
    }
}

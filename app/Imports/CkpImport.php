<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\NilaiCkp;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CkpImport implements ToModel, WithHeadingRow
{
    protected $periode_id;

    public function __construct($periode_id)
    {
        $this->periode_id = $periode_id;
    }

    public function model(array $row)
    {
        // Cari pegawai berdasarkan NIP atau nama
        $pegawai = Pegawai::where('nip', $row['nip'] ?? null)
            ->orWhere('nama', $row['nama'] ?? null)
            ->first();

        if ($pegawai && isset($row['nilai_ckp'])) {
            NilaiCkp::updateOrCreate(
                [
                    'periode_id' => $this->periode_id,
                    'pegawai_id' => $pegawai->id,
                ],
                [
                    'nilai' => $row['nilai_ckp'],
                ]
            );
        }

        return null; // UpdateOrCreate sudah menyimpan ke database
    }
}

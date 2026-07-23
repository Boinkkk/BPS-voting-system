<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AbsensiExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AbsensiDataSheet,
            new AbsensiKamusSheet,
        ];
    }
}

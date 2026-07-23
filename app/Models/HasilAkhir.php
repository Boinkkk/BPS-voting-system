<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilAkhir extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'hasil_akhir';

    // Matikan timestamps bawaan jika tabel ini tidak memiliki created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'periode_id',
        'kandidat_id',
        'ranking_final',
        'is_terpilih',
        'dipilih_oleh',
        'waktu_penetapan',
        'catatan_kepala',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id');
    }

    public function pemilih()
    {
        return $this->belongsTo(Pegawai::class, 'dipilih_oleh');
    }
}

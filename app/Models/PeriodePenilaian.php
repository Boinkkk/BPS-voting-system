<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PeriodePenilaian extends Model
{
    use HasFactory;

    protected $table = 'periode_penilaian';

    protected $fillable = [
        'triwulan',
        'tahun',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai_persiapan',
        'tanggal_mulai_voting',
        'tanggal_selesai_voting',
        'tanggal_review_kepala',
        'tanggal_selesai',
        'status',
    ];

    protected static function booted()
    {
        $clearCache = function ($periode) {
            $tahun = $periode->tahun;
            for ($month = 1; $month <= 12; $month++) {
                Cache::forget('kalender_'.$month.'_'.$tahun);
                Cache::forget('kalender_'.$month.'_'.($tahun - 1));
                Cache::forget('kalender_'.$month.'_'.($tahun + 1));
            }
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    public function getPhaseDetailsAttribute()
    {
        $now = Carbon::now()->startOfDay();
        $mulai = Carbon::parse($this->tanggal_mulai)->startOfDay();

        $selesaiPersiapan = $this->tanggal_selesai_persiapan
            ? Carbon::parse($this->tanggal_selesai_persiapan)->startOfDay()
            : $mulai->copy()->addDays(4);

        $mulaiVoting = $this->tanggal_mulai_voting
            ? Carbon::parse($this->tanggal_mulai_voting)->startOfDay()
            : $selesaiPersiapan->copy()->addDay();

        $selesaiVoting = $this->tanggal_selesai_voting
            ? Carbon::parse($this->tanggal_selesai_voting)->startOfDay()
            : $mulaiVoting->copy()->addDays(2);

        $reviewKepala = $this->tanggal_review_kepala
            ? Carbon::parse($this->tanggal_review_kepala)->startOfDay()
            : $selesaiVoting->copy()->addDay();

        $selesai = Carbon::parse($this->tanggal_selesai)->startOfDay();

        if ($now->lt($mulai)) {
            return [
                'current_phase' => 'Belum Dimulai',
                'days_left' => (int) $now->diffInDays($mulai, false),
                'next_phase' => 'Masa Persiapan',
            ];
        } elseif ($now->between($mulai, $selesaiPersiapan)) {
            return [
                'current_phase' => 'Masa Persiapan',
                'days_left' => (int) $now->diffInDays($mulaiVoting, false),
                'next_phase' => 'Masa Voting',
            ];
        } elseif ($now->between($selesaiPersiapan->copy()->addDay(), $mulaiVoting->copy()->subDay())) {
            return [
                'current_phase' => 'Menunggu Voting',
                'days_left' => (int) $now->diffInDays($mulaiVoting, false),
                'next_phase' => 'Masa Voting',
            ];
        } elseif ($now->between($mulaiVoting, $selesaiVoting)) {
            return [
                'current_phase' => 'Masa Voting',
                'days_left' => (int) $now->diffInDays($reviewKepala, false),
                'next_phase' => 'Pemilihan Kepala',
            ];
        } elseif ($now->between($selesaiVoting->copy()->addDay(), $reviewKepala->copy()->subDay())) {
            return [
                'current_phase' => 'Menunggu Pemilihan',
                'days_left' => (int) $now->diffInDays($reviewKepala, false),
                'next_phase' => 'Pemilihan Kepala',
            ];
        } elseif ($now->between($reviewKepala, $selesai->copy()->subDay())) {
            return [
                'current_phase' => 'Pemilihan Kepala',
                'days_left' => (int) $now->diffInDays($selesai, false),
                'next_phase' => 'Pengumuman Pemenang',
            ];
        } else {
            return [
                'current_phase' => 'Pengumuman Pemenang',
                'days_left' => 0,
                'next_phase' => null,
            ];
        }
    }

    public function computeStatusBasedOnDate($forceRecalculate = false)
    {
        if (! $forceRecalculate && $this->status === 'selesai') {
            return 'selesai';
        }

        $now = Carbon::now()->startOfDay();
        $mulai = Carbon::parse($this->tanggal_mulai)->startOfDay();
        $selesaiPersiapan = $this->tanggal_selesai_persiapan
            ? Carbon::parse($this->tanggal_selesai_persiapan)->startOfDay()
            : $mulai->copy()->addDays(4);

        $mulaiVoting = $this->tanggal_mulai_voting
            ? Carbon::parse($this->tanggal_mulai_voting)->startOfDay()
            : $selesaiPersiapan->copy()->addDay();

        $selesaiVoting = $this->tanggal_selesai_voting
            ? Carbon::parse($this->tanggal_selesai_voting)->startOfDay()
            : $mulaiVoting->copy()->addDays(2);

        $reviewKepala = $this->tanggal_review_kepala
            ? Carbon::parse($this->tanggal_review_kepala)->startOfDay()
            : $selesaiVoting->copy()->addDay();

        $selesai = Carbon::parse($this->tanggal_selesai)->startOfDay();

        if ($now->between($mulai, $selesaiPersiapan)) {
            return 'penginputan';
        } elseif ($now->between($mulaiVoting, $selesaiVoting)) {
            return 'voting';
        } elseif ($now->between($reviewKepala, $selesai->copy()->subDay())) {
            return 'review_kepala';
        } elseif ($now->greaterThanOrEqualTo($selesai)) {
            return 'selesai';
        }

        return $this->status;
    }

    public function isDataLengkap()
    {
        // 1. Cek CKP
        $hasCkp = NilaiCkp::where('periode_id', $this->id)->exists();
        if (! $hasCkp) {
            return false;
        }

        // 2. Cek Absensi (harus ada di 3 bulan triwulan ini)
        $expectedMonths = [];
        switch ((int) $this->triwulan) {
            case 1: $expectedMonths = [1, 2, 3];
                break;
            case 2: $expectedMonths = [4, 5, 6];
                break;
            case 3: $expectedMonths = [7, 8, 9];
                break;
            case 4: $expectedMonths = [10, 11, 12];
                break;
        }

        $existingMonths = AbsensiPegawai::where('periode_id', $this->id)
            ->select('bulan')
            ->distinct()
            ->pluck('bulan')
            ->map(function ($val) {
                return (int) $val;
            })
            ->toArray();

        $intersect = array_intersect($expectedMonths, $existingMonths);

        if (count($intersect) < 3) {
            return false;
        }

        return true;
    }

    public static function getRecentAndDefault($requested_periode_id = null)
    {
        $currentYear = (int) date('Y');
        $periodes = self::where('tahun', '>=', $currentYear - 1)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $default_id = $requested_periode_id;

        if (! $default_id && $periodes->isNotEmpty()) {
            $now = now();
            $closestPeriode = $periodes->sortBy(function ($p) use ($now) {
                if ($p->tanggal_mulai_voting && $p->tanggal_selesai_voting) {
                    $start = Carbon::parse($p->tanggal_mulai_voting);
                    $end = Carbon::parse($p->tanggal_selesai_voting);

                    if ($now->between($start, $end)) {
                        return 0;
                    }

                    $diffStart = abs($now->diffInSeconds($start));
                    $diffEnd = abs($now->diffInSeconds($end));

                    return min($diffStart, $diffEnd);
                }

                return PHP_INT_MAX;
            })->first();

            $default_id = $closestPeriode ? $closestPeriode->id : $periodes->first()->id;
        }

        return [
            'periodes' => $periodes,
            'default_id' => $default_id,
        ];
    }
}

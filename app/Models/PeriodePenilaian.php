<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getPhaseDetailsAttribute()
    {
        $now = \Carbon\Carbon::now()->startOfDay();
        $mulai = \Carbon\Carbon::parse($this->tanggal_mulai)->startOfDay();
        
        $selesaiPersiapan = $this->tanggal_selesai_persiapan
            ? \Carbon\Carbon::parse($this->tanggal_selesai_persiapan)->startOfDay()
            : $mulai->copy()->addDays(4);

        $mulaiVoting = $this->tanggal_mulai_voting 
            ? \Carbon\Carbon::parse($this->tanggal_mulai_voting)->startOfDay()
            : $selesaiPersiapan->copy()->addDay();
            
        $selesaiVoting = $this->tanggal_selesai_voting
            ? \Carbon\Carbon::parse($this->tanggal_selesai_voting)->startOfDay()
            : $mulaiVoting->copy()->addDays(2);
            
        $reviewKepala = $this->tanggal_review_kepala
            ? \Carbon\Carbon::parse($this->tanggal_review_kepala)->startOfDay()
            : $selesaiVoting->copy()->addDay();

        $selesai = \Carbon\Carbon::parse($this->tanggal_selesai)->startOfDay();

        if ($now->lt($mulai)) {
            return [
                'current_phase' => 'Belum Dimulai',
                'days_left' => (int) $now->diffInDays($mulai, false),
                'next_phase' => 'Masa Persiapan'
            ];
        } elseif ($now->between($mulai, $selesaiPersiapan)) {
            return [
                'current_phase' => 'Masa Persiapan',
                'days_left' => (int) $now->diffInDays($mulaiVoting, false),
                'next_phase' => 'Masa Voting'
            ];
        } elseif ($now->between($selesaiPersiapan->copy()->addDay(), $mulaiVoting->copy()->subDay())) {
            return [
                'current_phase' => 'Menunggu Voting',
                'days_left' => (int) $now->diffInDays($mulaiVoting, false),
                'next_phase' => 'Masa Voting'
            ];
        } elseif ($now->between($mulaiVoting, $selesaiVoting)) {
            return [
                'current_phase' => 'Masa Voting',
                'days_left' => (int) $now->diffInDays($reviewKepala, false),
                'next_phase' => 'Pemilihan Kepala'
            ];
        } elseif ($now->between($selesaiVoting->copy()->addDay(), $reviewKepala->copy()->subDay())) {
            return [
                'current_phase' => 'Menunggu Pemilihan',
                'days_left' => (int) $now->diffInDays($reviewKepala, false),
                'next_phase' => 'Pemilihan Kepala'
            ];
        } elseif ($now->between($reviewKepala, $selesai->copy()->subDay())) {
            return [
                'current_phase' => 'Pemilihan Kepala',
                'days_left' => (int) $now->diffInDays($selesai, false),
                'next_phase' => 'Pengumuman Pemenang'
            ];
        } else {
            return [
                'current_phase' => 'Pengumuman Pemenang',
                'days_left' => 0,
                'next_phase' => null
            ];
        }
    }
}

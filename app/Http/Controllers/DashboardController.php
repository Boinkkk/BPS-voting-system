<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pemenangTerakhir = \App\Models\HasilAkhir::with(['periode'])
            ->where('is_terpilih', true)
            ->whereHas('periode', function ($q) {
                $q->where('status', 'selesai');
            })
            ->orderBy('waktu_penetapan', 'desc')
            ->first();

        $top3 = collect();
        $votingProgress = null;
        
        $activePeriode = \App\Models\PeriodePenilaian::where('status', '!=', 'selesai')->latest()->first();
        $phaseDetails = null;

        $selectedMonth = request('month', $activePeriode ? \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->month : \Carbon\Carbon::now()->month);
        $selectedYear = request('year', $activePeriode ? \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->year : \Carbon\Carbon::now()->year);

        $startOfMonth = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $periodesInMonth = \App\Models\PeriodePenilaian::where(function($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                  ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                      $q->where('tanggal_mulai', '<=', $startOfMonth)
                        ->where('tanggal_selesai', '>=', $endOfMonth);
                  });
        })->get();

        if ($activePeriode) {
            $phaseDetails = $activePeriode->phase_details;
            
            // Get all Pegawai users
            $semuaPegawai = \App\Models\Pegawai::whereHas('role', function($q) {
                $q->where('tipe', 'Pegawai');
            })->get();
            
            // Get who has voted in SurveyProgress
            $sudahVoting = \App\Models\SurveyProgress::where('periode_id', $activePeriode->id)
                ->pluck('user_id')
                ->toArray();
                
            $votingProgress = $semuaPegawai->map(function($user) use ($sudahVoting) {
                return [
                    'nama' => $user->nama,
                    'sudah_voting' => in_array($user->id, $sudahVoting),
                    'foto' => $user->foto_profil_url
                ];
            })->sortByDesc('sudah_voting')->values();
        }

        if ($pemenangTerakhir) {
            $periode = $pemenangTerakhir->periode;
            $semuaKandidat = \App\Models\HasilAkhir::with(['kandidat.pegawai', 'pemilih'])
                ->where('periode_id', $periode->id)
                ->get();
                
            $juara1 = $semuaKandidat->where('is_terpilih', true)->first();
            $lainnya = $semuaKandidat->where('is_terpilih', false)->sortBy('ranking_final')->values();
            
            $juara2 = $lainnya->get(0);
            $juara3 = $lainnya->get(1);
            
            $top3 = collect([$juara1, $juara2, $juara3])->filter();
        }

        return view('dashboard', compact('pemenangTerakhir', 'top3', 'votingProgress', 'activePeriode', 'phaseDetails', 'selectedMonth', 'selectedYear', 'periodesInMonth'));
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('departemen', 'role');
        
        return view('profile.show', compact('user'));
    }
}

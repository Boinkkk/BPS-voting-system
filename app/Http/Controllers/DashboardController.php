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

        return view('dashboard', compact('pemenangTerakhir', 'top3'));
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('departemen', 'role');
        
        return view('profile.show', compact('user'));
    }
}

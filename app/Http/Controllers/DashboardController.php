<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pemenangTerakhir = \App\Models\HasilAkhir::with(['kandidat.pegawai', 'periode'])
            ->where('is_terpilih', true)
            ->whereHas('periode', function ($q) {
                $q->where('status', 'selesai');
            })
            ->orderBy('waktu_penetapan', 'desc')
            ->first();

        return view('dashboard', compact('pemenangTerakhir'));
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('departemen', 'role');
        
        return view('profile.show', compact('user'));
    }
}

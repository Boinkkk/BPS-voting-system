<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAdminOrKepalaUmum
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role) {
            $tipe = Auth::user()->role->tipe;
            if ($tipe === 'Admin' || $tipe === 'Kepala Umum' || $tipe === 'Kepala_Umum') {
                return $next($request);
            }

            // Cek apakah user adalah Tim Penilai Kinerja di periode aktif mana pun
            if ($tipe === 'Pegawai') {
                $isTimPenilaiAktif = \App\Models\TimPenilai::where('pegawai_id', Auth::user()->id)
                    ->whereHas('periode', function ($q) {
                        $q->where('status', '!=', 'selesai');
                    })->exists();

                if ($isTimPenilaiAktif) {
                    return $next($request);
                }
            }
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}

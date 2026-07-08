<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role && Auth::user()->role->tipe === 'Admin') {
            return $next($request);
        }

        // Cek apakah user adalah Tim Penilai Kinerja di periode aktif mana pun
        if (Auth::check() && Auth::user()->role && Auth::user()->role->tipe === 'Pegawai') {
            $isTimPenilaiAktif = \App\Models\TimPenilai::where('pegawai_id', Auth::user()->id)
                ->whereHas('periode', function ($q) {
                    $q->where('status', '!=', 'selesai');
                })->exists();

            if ($isTimPenilaiAktif) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}

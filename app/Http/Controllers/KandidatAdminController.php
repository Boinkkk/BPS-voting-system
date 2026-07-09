<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Services\KandidatService;

class KandidatAdminController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->input('periode_id');
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        
        if (!$periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        $kandidats = [];
        if ($periode_id) {
            $kandidats = Kandidat::with('pegawai')
                            ->where('periode_id', $periode_id)
                            ->orderBy('ranking_sistem', 'asc')
                            ->get();
        }

        return view('admin.kandidat.index', compact('periodes', 'periode_id', 'kandidats'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id'
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Kalkulasi ulang hanya dapat dilakukan pada masa penginputan data.');
        }

        try {
            KandidatService::generateTop10Kandidat($request->periode_id);
            return redirect()->back()->with('success', '10 Kandidat terbaik berhasil dikalkulasi ulang dan disimpan untuk periode ini.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghasilkan kandidat: ' . $e->getMessage());
        }
    }
}

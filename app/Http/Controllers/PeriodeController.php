<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.periode.index', compact('periodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'triwulan' => 'required|integer|between:1,4',
            'tahun' => 'required|integer|min:2000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        $nama = 'Triwulan ' . $request->triwulan . ' Tahun ' . $request->tahun;

        PeriodePenilaian::create([
            'triwulan' => $request->triwulan,
            'tahun' => $request->tahun,
            'nama' => $nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.periode.index')->with('success', 'Periode Penilaian baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'triwulan' => 'required|integer|between:1,4',
            'tahun' => 'required|integer|min:2000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        $nama = 'Triwulan ' . $request->triwulan . ' Tahun ' . $request->tahun;

        $periode = PeriodePenilaian::findOrFail($id);
        $oldStatus = $periode->status;
        
        $periode->update([
            'triwulan' => $request->triwulan,
            'tahun' => $request->tahun,
            'nama' => $nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        if ($request->status === 'review_kepala' && $oldStatus !== 'review_kepala') {
            \App\Services\KandidatService::generateTop3Kandidat($id);
        }

        return redirect()->route('admin.periode.index')->with('success', 'Periode Penilaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        $periode->delete();

        return redirect()->route('admin.periode.index')->with('success', 'Periode Penilaian berhasil dihapus.');
    }
}

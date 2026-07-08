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
            'nama' => 'required|string|max:150',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        PeriodePenilaian::create([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.periode.index')->with('success', 'Periode Penilaian baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        $periode = PeriodePenilaian::findOrFail($id);
        $periode->update([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.periode.index')->with('success', 'Periode Penilaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        $periode->delete();

        return redirect()->route('admin.periode.index')->with('success', 'Periode Penilaian berhasil dihapus.');
    }
}

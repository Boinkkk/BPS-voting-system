<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipeAbsen;
use App\Models\AbsensiPegawai;

class AbsensiAdminController extends Controller
{
    public function index()
    {
        $tipeAbsens = TipeAbsen::orderBy('status')->get();
        // Mengambil daftar absensi terbaru, untuk saat ini hanya menampilkan saja
        $absensis = AbsensiPegawai::with(['pegawai', 'tipeAbsen'])
                        ->orderBy('waktu_absensi', 'desc')
                        ->paginate(20);

        return view('admin.absensi.index', compact('tipeAbsens', 'absensis'));
    }

    public function storeTipe(Request $request)
    {
        $request->validate([
            'status' => 'required|string|max:50|unique:tipe_absen,status',
            'bobot' => 'required|numeric|min:0',
        ]);

        TipeAbsen::create([
            'status' => $request->status,
            'bobot' => $request->bobot,
        ]);

        return redirect()->back()->with('success', 'Tipe Absen baru berhasil ditambahkan.');
    }

    public function updateTipe(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:50|unique:tipe_absen,status,' . $id,
            'bobot' => 'required|numeric|min:0',
        ]);

        $tipe = TipeAbsen::findOrFail($id);
        $tipe->update([
            'status' => $request->status,
            'bobot' => $request->bobot,
        ]);

        return redirect()->back()->with('success', 'Tipe Absen berhasil diperbarui.');
    }
}

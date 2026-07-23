<?php

namespace App\Http\Controllers;

use App\Models\PertanyaanSurvei;
use Illuminate\Http\Request;

class SurveyAdminController extends Controller
{
    public function index()
    {
        $pertanyaans = PertanyaanSurvei::orderBy('nomor_urut')->get();

        return view('admin.survey.index', compact('pertanyaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'pertanyaan' => 'required|string',
            'nomor_urut' => 'required|integer',
        ]);

        PertanyaanSurvei::create([
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'nomor_urut' => $request->nomor_urut,
            'bobot' => 1.0,
        ]);

        return redirect()->back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'pertanyaan' => 'required|string',
            'nomor_urut' => 'required|integer',
        ]);

        $pertanyaan = PertanyaanSurvei::findOrFail($id);
        $pertanyaan->update([
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'nomor_urut' => $request->nomor_urut,
        ]);

        return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pertanyaan = PertanyaanSurvei::findOrFail($id);
        $pertanyaan->delete();

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus.');
    }
}

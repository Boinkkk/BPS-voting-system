<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\TimPenilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimPenilaiController extends Controller
{
    public function index()
    {
        $periodes = PeriodePenilaian::orderBy('created_at', 'desc')->get();
        $pegawais = Pegawai::whereHas('role', function ($q) {
            $q->where('tipe', 'Pegawai');
        })->get();

        return view('kepala.tim_penilai.index', compact('periodes', 'pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'penanggung_jawab' => 'required|exists:pegawai,id',
            'ketua' => 'required|exists:pegawai,id',
            'anggota' => 'required|exists:pegawai,id',
        ]);

        $periode = PeriodePenilaian::findOrFail($request->periode_id);

        DB::beginTransaction();
        try {
            // Hapus tim penilai sebelumnya untuk periode ini
            TimPenilai::where('periode_id', $periode->id)->delete();

            // Insert role baru
            TimPenilai::create([
                'periode_id' => $periode->id,
                'pegawai_id' => $request->penanggung_jawab,
                'peran' => 'Penanggung Jawab',
            ]);

            TimPenilai::create([
                'periode_id' => $periode->id,
                'pegawai_id' => $request->ketua,
                'peran' => 'Ketua',
            ]);

            TimPenilai::create([
                'periode_id' => $periode->id,
                'pegawai_id' => $request->anggota,
                'peran' => 'Anggota',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Tim Penilai Kinerja berhasil ditetapkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function cetak($periode_id)
    {
        $periode = PeriodePenilaian::findOrFail($periode_id);
        $tim = TimPenilai::where('periode_id', $periode_id)->with('pegawai')->get();

        if ($tim->count() === 0) {
            return redirect()->back()->with('error', 'Tim Penilai belum ditetapkan untuk periode ini.');
        }

        $penanggungJawab = $tim->where('peran', 'Penanggung Jawab')->first();
        $ketua = $tim->where('peran', 'Ketua')->first();
        $anggota = $tim->where('peran', 'Anggota')->first();

        // Cari data kepala (yang menetapkan)
        $kepala = Pegawai::whereHas('role', function ($q) {
            $q->where('tipe', 'Kepala Kantor');
        })->first();

        return view('kepala.tim_penilai.cetak', compact('periode', 'penanggungJawab', 'ketua', 'anggota', 'kepala'));
    }
}

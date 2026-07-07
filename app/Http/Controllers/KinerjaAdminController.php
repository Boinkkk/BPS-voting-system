<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use App\Models\KinerjaPegawai;
use App\Imports\KinerjaImport;
use Maatwebsite\Excel\Facades\Excel;

class KinerjaAdminController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->input('periode_id');
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        
        if (!$periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        $kinerja = [];
        if ($periode_id) {
            $kinerja = KinerjaPegawai::with('pegawai')
                        ->where('periode_id', $periode_id)
                        ->get();
        }

        $semuaPegawai = \App\Models\Pegawai::orderBy('nama')->get();

        return view('admin.kinerja.index', compact('periodes', 'periode_id', 'kinerja', 'semuaPegawai'));
    }

    private function resolvePeriodeId($periode_id)
    {
        if ($periode_id === 'sekarang') {
            $now = \Carbon\Carbon::now()->toDateString();
            $periode = PeriodePenilaian::where('tanggal_mulai', '<=', $now)
                ->where('tanggal_selesai', '>=', $now)
                ->first();
            
            if (!$periode) {
                throw new \Exception('Tidak ada periode aktif untuk hari ini.');
            }
            return $periode->id;
        }
        return $periode_id;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'periode_id' => 'required',
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $realPeriodeId = $this->resolvePeriodeId($request->periode_id);
            
            // Validate if realPeriodeId exists
            if (!PeriodePenilaian::find($realPeriodeId)) {
                throw new \Exception('Periode tidak valid.');
            }

            Excel::import(new KinerjaImport($realPeriodeId), $request->file('file'));
            return redirect()->back()->with('success', 'Data kinerja berhasil diunggah dan diekstrak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah: ' . $e->getMessage());
        }
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'periode_id' => 'required',
            'id_pegawai' => 'required|exists:pegawai,id',
            'bulan' => 'required|integer|min:1|max:12',
            'rata_rata_hasil_kerja' => 'required|numeric',
            'rata_rata_perilaku' => 'required|numeric',
            'nilai_kjk' => 'nullable|numeric',
            'nilai_tl_psw' => 'nullable|numeric',
        ]);

        try {
            $realPeriodeId = $this->resolvePeriodeId($request->periode_id);
            
            if (!PeriodePenilaian::find($realPeriodeId)) {
                return redirect()->back()->with('error', 'Periode tidak valid.');
            }

            KinerjaPegawai::updateOrCreate(
                [
                    'periode_id' => $realPeriodeId,
                    'id_pegawai' => $request->id_pegawai,
                    'bulan' => $request->bulan,
                ],
                [
                    'rata_rata_hasil_kerja' => $request->rata_rata_hasil_kerja,
                    'rata_rata_perilaku' => $request->rata_rata_perilaku,
                    'nilai_kjk' => $request->nilai_kjk,
                    'nilai_tl_psw' => $request->nilai_tl_psw,
                ]
            );

            return redirect()->back()->with('success', 'Data kinerja manual berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:penginputan,voting,selesai',
        ]);

        PeriodePenilaian::create([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Periode Penilaian baru berhasil ditambahkan.');
    }
}

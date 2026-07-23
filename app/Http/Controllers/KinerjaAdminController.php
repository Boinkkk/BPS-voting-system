<?php

namespace App\Http\Controllers;

use App\Imports\KinerjaImport;
use App\Models\KinerjaPegawai;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Services\KandidatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;

class KinerjaAdminController extends Controller
{
    public function index(Request $request)
    {
        $periode_id = $request->input('periode_id');
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();

        if (! $periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        $kinerja = [];
        if ($periode_id) {
            $kinerja = KinerjaPegawai::with('pegawai')
                ->where('periode_id', $periode_id)
                ->get();
        }

        $semuaPegawai = Pegawai::orderBy('nama')->get();

        return view('admin.kinerja.index', compact('periodes', 'periode_id', 'kinerja', 'semuaPegawai'));
    }

    private function resolvePeriodeId($periode_id)
    {
        if ($periode_id === 'sekarang') {
            $now = Carbon::now()->toDateString();
            $periode = PeriodePenilaian::where('tanggal_mulai', '<=', $now)
                ->where('tanggal_selesai', '>=', $now)
                ->first();

            if (! $periode) {
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
            'file' => ['required', File::types(['xlsx', 'xls', 'csv'])->max(10 * 1024), 'mimes:xlsx,xls,csv'],
        ]);

        try {
            $realPeriodeId = $this->resolvePeriodeId($request->periode_id);

            $periode = PeriodePenilaian::find($realPeriodeId);
            // Validate if realPeriodeId exists
            if (! $periode) {
                throw new \Exception('Periode tidak valid.');
            }

            if ($periode->status !== 'penginputan') {
                return redirect()->back()->with('error', 'Upload data kinerja hanya dapat dilakukan pada masa penginputan data.');
            }

            Excel::import(new KinerjaImport($realPeriodeId), $request->file('file'));

            // Trigger otomatis kalkulasi kandidat setelah excel selesai diproses
            KandidatService::generateTop10Kandidat($realPeriodeId);

            return redirect()->back()->with('success', 'Data kinerja berhasil diunggah. 10 Kandidat telah otomatis dikalkulasi ulang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah: '.$e->getMessage());
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

            $periode = PeriodePenilaian::find($realPeriodeId);
            if (! $periode) {
                return redirect()->back()->with('error', 'Periode tidak valid.');
            }

            if ($periode->status !== 'penginputan') {
                return redirect()->back()->with('error', 'Input data kinerja hanya dapat dilakukan pada masa penginputan data.');
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

            // Trigger otomatis perhitungan 10 kandidat
            KandidatService::generateTop10Kandidat($realPeriodeId);

            return redirect()->back()->with('success', 'Data kinerja manual berhasil ditambahkan. 10 Kandidat otomatis dikalkulasi ulang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }
}

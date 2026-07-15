<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;

class PeriodeController extends Controller
{
    public function index()
    {
        $tahunSekarang = date('Y');
        
        for ($i = 1; $i <= 4; $i++) {
            $exists = PeriodePenilaian::where('tahun', $tahunSekarang)->where('triwulan', $i)->exists();
            if (!$exists) {
                $bulanPersiapan = ($i - 1) * 3 + 4; // T1=4, T2=7, T3=10, T4=13
                $tahun = $tahunSekarang;
                
                if ($bulanPersiapan > 12) {
                    $bulanPersiapan -= 12;
                    $tahun++;
                }

                $tanggalMulai = \Carbon\Carbon::create($tahun, $bulanPersiapan, 1)->format('Y-m-d');
                $tanggalSelesaiPersiapan = \Carbon\Carbon::create($tahun, $bulanPersiapan, 5)->format('Y-m-d');
                $tanggalMulaiVoting = \Carbon\Carbon::create($tahun, $bulanPersiapan, 6)->format('Y-m-d');
                $tanggalSelesaiVoting = \Carbon\Carbon::create($tahun, $bulanPersiapan, 8)->format('Y-m-d');
                $tanggalReviewKepala = \Carbon\Carbon::create($tahun, $bulanPersiapan, 9)->format('Y-m-d');
                $tanggalSelesai = \Carbon\Carbon::create($tahun, $bulanPersiapan, 10)->format('Y-m-d');
                
                PeriodePenilaian::create([
                    'triwulan' => $i,
                    'tahun' => $tahunSekarang,
                    'nama' => 'Triwulan ' . $i . ' Tahun ' . $tahunSekarang,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai_persiapan' => $tanggalSelesaiPersiapan,
                    'tanggal_mulai_voting' => $tanggalMulaiVoting,
                    'tanggal_selesai_voting' => $tanggalSelesaiVoting,
                    'tanggal_review_kepala' => $tanggalReviewKepala,
                    'tanggal_selesai' => $tanggalSelesai,
                    'status' => 'penginputan',
                ]);
            }
        }

        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.periode.index', compact('periodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'triwulan' => 'required|integer|between:1,4',
            'tahun' => 'required|integer|min:2000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai_persiapan' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_voting' => 'required|date|after:tanggal_selesai_persiapan',
            'tanggal_selesai_voting' => 'required|date|after_or_equal:tanggal_mulai_voting',
            'tanggal_review_kepala' => 'required|date|after:tanggal_selesai_voting',
            'tanggal_selesai' => 'required|date|after:tanggal_review_kepala',
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ], [
            'tanggal_selesai_persiapan.after_or_equal' => 'Selesai persiapan harus >= mulai persiapan.',
            'tanggal_mulai_voting.after' => 'Mulai voting harus setelah selesai persiapan (tidak boleh bertabrakan).',
            'tanggal_selesai_voting.after_or_equal' => 'Selesai voting harus >= mulai voting.',
            'tanggal_review_kepala.after' => 'Pemilihan Kepala harus setelah selesai voting (tidak boleh bertabrakan).',
            'tanggal_selesai.after' => 'Pengumuman harus setelah Pemilihan Kepala (tidak boleh bertabrakan).',
        ]);

        $nama = 'Triwulan ' . $request->triwulan . ' Tahun ' . $request->tahun;

        PeriodePenilaian::create([
            'triwulan' => $request->triwulan,
            'tahun' => $request->tahun,
            'nama' => $nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai_persiapan' => $request->tanggal_selesai_persiapan,
            'tanggal_mulai_voting' => $request->tanggal_mulai_voting,
            'tanggal_selesai_voting' => $request->tanggal_selesai_voting,
            'tanggal_review_kepala' => $request->tanggal_review_kepala,
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
            'tanggal_selesai_persiapan' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_voting' => 'required|date|after:tanggal_selesai_persiapan',
            'tanggal_selesai_voting' => 'required|date|after_or_equal:tanggal_mulai_voting',
            'tanggal_review_kepala' => 'required|date|after:tanggal_selesai_voting',
            'tanggal_selesai' => 'required|date|after:tanggal_review_kepala',
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ], [
            'tanggal_selesai_persiapan.after_or_equal' => 'Selesai persiapan harus >= mulai persiapan.',
            'tanggal_mulai_voting.after' => 'Mulai voting harus setelah selesai persiapan (tidak boleh bertabrakan).',
            'tanggal_selesai_voting.after_or_equal' => 'Selesai voting harus >= mulai voting.',
            'tanggal_review_kepala.after' => 'Pemilihan Kepala harus setelah selesai voting (tidak boleh bertabrakan).',
            'tanggal_selesai.after' => 'Pengumuman harus setelah Pemilihan Kepala (tidak boleh bertabrakan).',
        ]);

        $nama = 'Triwulan ' . $request->triwulan . ' Tahun ' . $request->tahun;

        $periode = PeriodePenilaian::findOrFail($id);
        $oldStatus = $periode->status;
        
        $periode->update([
            'triwulan' => $request->triwulan,
            'tahun' => $request->tahun,
            'nama' => $nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai_persiapan' => $request->tanggal_selesai_persiapan,
            'tanggal_mulai_voting' => $request->tanggal_mulai_voting,
            'tanggal_selesai_voting' => $request->tanggal_selesai_voting,
            'tanggal_review_kepala' => $request->tanggal_review_kepala,
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

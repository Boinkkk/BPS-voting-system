<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use App\Models\Pegawai;
use App\Models\Kandidat;
use App\Models\JawabanSurvei;
use App\Models\SurveyProgress;
use Illuminate\Support\Facades\DB;

class MonitoringSurveiController extends Controller
{
    public function index(Request $request)
    {
        $periodes = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        $periode_id = $request->input('periode_id');

        if (!$periode_id && $periodes->isNotEmpty()) {
            $periode_id = $periodes->first()->id;
        }

        $kandidats = collect();
        $progressPegawai = collect();
        $totalPegawai = Pegawai::count();
        $pegawaiSelesai = 0;

        if ($periode_id) {
            // 1. Live Score (Klasemen)
            // Hitung rata-rata nilai dari jawaban survei per kandidat untuk periode ini
            $kandidats = Kandidat::with('pegawai')
                ->where('periode_id', $periode_id)
                ->get()
                ->map(function ($kandidat) use ($periode_id) {
                    $rataRata = JawabanSurvei::where('periode_id', $periode_id)
                        ->where('kandidat_id', $kandidat->id)
                        ->avg('nilai');
                    $kandidat->live_skor = $rataRata ? round($rataRata, 2) : 0;
                    return $kandidat;
                })
                ->sortByDesc('live_skor')
                ->values();

            $totalKandidat = $kandidats->count();

            // 2. Daftar Absen / Progress per Pegawai (Hanya role Pegawai)
            $semuaPegawai = Pegawai::whereHas('role', function ($query) {
                $query->where('tipe', 'Pegawai');
            })->orderBy('nama')->get();
            
            $progressPegawai = $semuaPegawai->map(function($pegawai) use ($periode_id, $kandidats, $totalKandidat) {
                // Berapa kandidat yang sudah disurvei oleh user ini?
                // Ingat: userId = pegawai_id. Karena 1 user = 1 pegawai.
                $jumlahSudahSurvei = 0;
                $targetSurvei = $totalKandidat;

                // Jika pegawai ini adalah salah satu kandidat, maka dia tidak bisa menilai dirinya sendiri
                $isKandidat = $kandidats->where('pegawai_id', $pegawai->id)->first();
                if ($isKandidat) {
                    $targetSurvei = max(0, $totalKandidat - 1);
                }

                $jumlahSudahSurvei = SurveyProgress::where('periode_id', $periode_id)
                    ->where('user_id', $pegawai->id)
                    ->count();

                $status = 'Belum';
                if ($targetSurvei > 0) {
                    if ($jumlahSudahSurvei >= $targetSurvei) {
                        $status = 'Selesai';
                    } elseif ($jumlahSudahSurvei > 0) {
                        $status = 'Proses';
                    }
                } else {
                     $status = 'Tidak ada target';
                }

                return [
                    'nama' => $pegawai->nama,
                    'nip' => $pegawai->nip,
                    'sudah' => $jumlahSudahSurvei,
                    'target' => $targetSurvei,
                    'status' => $status
                ];
            });

            $pegawaiSelesai = $progressPegawai->where('status', 'Selesai')->count();
        }

        $totalPegawai = Pegawai::whereHas('role', function ($query) {
            $query->where('tipe', 'Pegawai');
        })->count();

        $persentase = $totalPegawai > 0 ? round(($pegawaiSelesai / $totalPegawai) * 100, 1) : 0;

        return view('admin.monitoring.index', compact(
            'periodes', 
            'periode_id', 
            'kandidats', 
            'progressPegawai', 
            'totalPegawai', 
            'pegawaiSelesai', 
            'persentase'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        $periode = PeriodePenilaian::findOrFail($id);
        
        // Jika status diubah ke review_kepala, generate top 3 kandidat
        if ($request->status === 'review_kepala' && $periode->status !== 'review_kepala') {
            // Hitung live score
            $kandidats = Kandidat::where('periode_id', $id)->get()->map(function ($kandidat) use ($id) {
                $rataRata = JawabanSurvei::where('periode_id', $id)
                    ->where('kandidat_id', $kandidat->id)
                    ->avg('nilai');
                $kandidat->live_skor = $rataRata ? round($rataRata, 2) : 0;
                return $kandidat;
            })->sortByDesc('live_skor')->values();

            // Hapus data lama jika ada
            \App\Models\HasilAkhir::where('periode_id', $id)->delete();

            // Ambil Top 3
            $top3 = $kandidats->take(3);
            $rank = 1;
            foreach ($top3 as $k) {
                \App\Models\HasilAkhir::create([
                    'periode_id' => $id,
                    'kandidat_id' => $k->id,
                    'ranking_final' => $rank,
                    'is_terpilih' => false
                ]);
                $rank++;
            }
        }

        $periode->status = $request->status;
        $periode->save();

        return redirect()->back()->with('success', 'Status periode berhasil diubah menjadi ' . ucfirst(str_replace('_', ' ', $request->status)));
    }
}

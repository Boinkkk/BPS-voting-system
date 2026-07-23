<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPegawai;
use App\Models\JawabanSurvei;
use App\Models\Kandidat;
use App\Models\NilaiCkp;
use App\Models\Pegawai;
use App\Models\PengaturanBobot;
use App\Models\PeriodePenilaian;
use App\Models\SurveyProgress;
use App\Services\KandidatService;
use Illuminate\Http\Request;

class MonitoringSurveiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (! $user || ! $user->role || ($user->role->tipe !== 'Admin' && $user->role->tipe !== 'Kepala Kantor')) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Admin dan Kepala Kantor.');
        }

        $requested_periode_id = $request->input('periode_id');
        $periodeData = PeriodePenilaian::getRecentAndDefault($requested_periode_id);

        $periodes = $periodeData['periodes'];
        $periode_id = $periodeData['default_id'];

        if ($periode_id != $requested_periode_id && ! $requested_periode_id) {
            return redirect()->route('admin.monitoring.index', array_merge($request->query(), [
                'periode_id' => $periode_id,
            ]));
        }

        $kandidats = collect();
        $progressPegawai = collect();
        $totalPegawai = Pegawai::count();
        $pegawaiSelesai = 0;

        if ($periode_id) {
            // 1. Live Score (Klasemen)
            // Hitung rata-rata nilai dari jawaban survei per kandidat untuk periode ini
            $pengaturan = PengaturanBobot::first();
            $ckpWeight = $pengaturan ? $pengaturan->ckp : 50;
            $absensiWeight = $pengaturan ? $pengaturan->absensi : 25;
            $surveyWeight = $pengaturan ? $pengaturan->survey : 25;

            $kandidats = Kandidat::with('pegawai')
                ->where('periode_id', $periode_id)
                ->get()
                ->map(function ($kandidat) use ($periode_id, $ckpWeight, $absensiWeight, $surveyWeight) {
                    // Survey Normalized
                    $rataRata = JawabanSurvei::where('periode_id', $periode_id)
                        ->where('kandidat_id', $kandidat->id)
                        ->avg('nilai');
                    $surveyNormalized = $rataRata ? ($rataRata / 5) * 100 : 0;
                    $kandidat->live_skor = $rataRata ? round($rataRata, 2) : 0;
                    $kandidat->survey_normalized = round($surveyNormalized, 2);

                    // CKP
                    $ckp = NilaiCkp::where('periode_id', $periode_id)
                        ->where('pegawai_id', $kandidat->pegawai_id)->first();
                    $nilaiCkp = $ckp ? $ckp->nilai : 0;

                    // Absensi
                    $rekapsAbsen = AbsensiPegawai::where('periode_id', $periode_id)
                        ->where('pegawai_id', $kandidat->pegawai_id)
                        ->get();
                    $totalKjk = $rekapsAbsen->sum('kjk');
                    $totalTk = $rekapsAbsen->sum('tk');

                    $nilaiAbsensi = 100;
                    if ($totalTk >= 1) {
                        $nilaiAbsensi = 96;
                    } else {
                        if ($totalKjk == 0) {
                            $nilaiAbsensi = 100;
                        } elseif ($totalKjk >= 1 && $totalKjk <= 60) {
                            $nilaiAbsensi = 99;
                        } elseif ($totalKjk >= 61 && $totalKjk <= 120) {
                            $nilaiAbsensi = 98;
                        } elseif ($totalKjk >= 121 && $totalKjk <= 450) {
                            $nilaiAbsensi = 97;
                        } else {
                            $nilaiAbsensi = 96;
                        }
                    }

                    // Skor Final Gabungan
                    $finalScore = ($nilaiCkp * ($ckpWeight / 100)) +
                                  ($nilaiAbsensi * ($absensiWeight / 100)) +
                                  ($surveyNormalized * ($surveyWeight / 100));

                    $kandidat->skor_final = round($finalScore, 2);

                    return $kandidat;
                })
                ->sortByDesc('skor_final')
                ->values();

            $totalKandidat = $kandidats->count();

            // 2. Daftar Absen / Progress per Pegawai (Hanya role Pegawai)
            $semuaPegawai = Pegawai::whereHas('role', function ($query) {
                $query->whereIn('tipe', ['Pegawai', 'Kepala Umum', 'Kepala_Umum']);
            })->orderBy('nama')->get();

            $progressPegawai = $semuaPegawai->map(function ($pegawai) use ($periode_id, $kandidats, $totalKandidat) {
                // Berapa kandidat yang sudah disurvei oleh user ini?
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
                    'status' => $status,
                ];
            });

            $pegawaiSelesai = $progressPegawai->where('status', 'Selesai')->count();
        }

        $totalPegawai = Pegawai::whereHas('role', function ($query) {
            $query->whereIn('tipe', ['Pegawai', 'Kepala Umum', 'Kepala_Umum']);
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

    public function downloadTxt(Request $request)
    {
        $user = auth()->user();
        if (! $user || ! $user->role || ($user->role->tipe !== 'Admin' && $user->role->tipe !== 'Kepala Kantor')) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $periode_id = $request->input('periode_id');
        $filter = $request->input('filter', 'semua'); // selesai, belum, semua

        if (! $periode_id) {
            return redirect()->back()->with('error', 'Periode tidak valid.');
        }

        $periode = PeriodePenilaian::find($periode_id);
        if (! $periode) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan.');
        }

        $kandidats = Kandidat::where('periode_id', $periode_id)->get();
        $totalKandidat = $kandidats->count();

        $semuaPegawai = Pegawai::whereHas('role', function ($query) {
            $query->whereIn('tipe', ['Pegawai', 'Kepala Umum', 'Kepala_Umum']);
        })->orderBy('nama')->get();

        $progressPegawai = $semuaPegawai->map(function ($pegawai) use ($periode_id, $kandidats, $totalKandidat) {
            $targetSurvei = $totalKandidat;
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

            return (object) [
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'sudah' => $jumlahSudahSurvei,
                'target' => $targetSurvei,
                'status' => $status,
            ];
        });

        if ($filter === 'selesai') {
            $progressPegawai = $progressPegawai->where('status', 'Selesai');
        } elseif ($filter === 'belum') {
            $progressPegawai = $progressPegawai->whereIn('status', ['Belum', 'Proses']);
        }

        $content = "Laporan Progress Survei\n";
        $content .= 'Periode: '.$periode->nama."\n";
        $content .= 'Filter: '.ucfirst($filter)."\n";
        $content .= 'Tanggal Unduh: '.now()->format('d M Y H:i:s')."\n";
        $content .= str_repeat('=', 80)."\n\n";

        $content .= str_pad('No', 5).str_pad('NIP', 20).str_pad('Nama Pegawai', 35).str_pad('Progress', 15)."Status\n";
        $content .= str_repeat('-', 80)."\n";

        $no = 1;
        foreach ($progressPegawai as $p) {
            $progressStr = $p->sudah.'/'.$p->target;
            $content .= str_pad($no++, 5).
                        str_pad($p->nip, 20).
                        str_pad(substr($p->nama, 0, 33), 35).
                        str_pad($progressStr, 15).
                        $p->status."\n";
        }

        $content .= str_repeat('=', 80)."\n";
        $content .= 'Total Data: '.$progressPegawai->count()." pegawai\n";

        $filename = "Progress_Survei_{$periode_id}_".ucfirst($filter).'_'.date('Ymd_His').'.txt';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()->role->tipe !== 'Admin') {
            return redirect()->back()->with('error', 'Hanya Admin yang memiliki akses untuk mengubah status periode.');
        }

        $request->validate([
            'status' => 'required|in:penginputan,voting,review_kepala,selesai',
        ]);

        $periode = PeriodePenilaian::findOrFail($id);

        // Jika status diubah ke review_kepala, generate top 3 kandidat
        if ($request->status === 'review_kepala' && $periode->status !== 'review_kepala') {
            KandidatService::generateTop3Kandidat($id);
        }

        $periode->status = $request->status;
        $periode->save();

        return redirect()->back()->with('success', 'Status periode berhasil diubah menjadi '.ucfirst(str_replace('_', ' ', $request->status)));
    }
}

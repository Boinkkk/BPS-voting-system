<?php

namespace App\Http\Controllers;

use App\Models\HasilAkhir;
use App\Models\Pegawai;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use App\Models\PeriodePenilaian;
use App\Models\SurveyProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleName = $user && $user->role ? $user->role->tipe : null;
        $pegawaiId = $user ? $user->id : null;

        // Base query for published announcements matching user target
        $basePengumumanQuery = Pengumuman::where('status', 'Published')
            ->where(function ($q) use ($roleName) {
                $q->whereNull('target')
                    ->orWhere('target', '')
                    ->orWhere('target', $roleName);
            });

        // Dashboard Sticky (Medium)
        $stickyPengumumans = (clone $basePengumumanQuery)
            ->where('is_sticky', true)
            ->where('prioritas', 'Medium')
            ->orderBy('created_at', 'desc')
            ->get();

        // Dashboard Banner Non-Sticky (Normal, Low)
        $bannerPengumumans = (clone $basePengumumanQuery)
            ->where('is_sticky', true)
            ->whereIn('prioritas', ['Normal', 'Low'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Regular (non-sticky/banner)
        $regularPengumumans = (clone $basePengumumanQuery)
            ->where('is_sticky', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Popup (find first unread)
        $popupPengumuman = null;
        if ($pegawaiId) {
            $popupPengumuman = (clone $basePengumumanQuery)
                ->where('is_popup', true)
                ->whereDoesntHave('reads', function ($q) use ($pegawaiId) {
                    $q->where('pegawai_id', $pegawaiId);
                })
                ->orderBy('created_at', 'asc') // Show oldest unread first
                ->first();

            // Fetch global sticky for auto-read marking (as they are displayed via layout)
            $globalStickyIds = (clone $basePengumumanQuery)
                ->where('is_sticky', true)
                ->whereIn('prioritas', ['High', 'Critical'])
                ->pluck('id');

            // Mark visible non-popup announcements as read automatically
            $visibleIds = $stickyPengumumans->pluck('id')
                ->merge($regularPengumumans->pluck('id'))
                ->merge($bannerPengumumans->pluck('id'))
                ->merge($globalStickyIds)
                ->unique();

            // Exclude the currently active popup from auto-read to force the user to click "Saya Mengerti"
            if ($popupPengumuman) {
                $visibleIds = $visibleIds->reject(function ($id) use ($popupPengumuman) {
                    return $id == $popupPengumuman->id;
                });
            }

            $alreadyReadIds = PengumumanRead::where('pegawai_id', $pegawaiId)
                ->whereIn('pengumuman_id', $visibleIds)
                ->pluck('pengumuman_id');

            $unreadIds = $visibleIds->diff($alreadyReadIds);

            $readsToInsert = [];
            foreach ($unreadIds as $uId) {
                $readsToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'pengumuman_id' => $uId,
                    'pegawai_id' => $pegawaiId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($readsToInsert)) {
                PengumumanRead::insert($readsToInsert);
            }
        }
        $pemenangTerakhir = HasilAkhir::with(['periode'])
            ->where('is_terpilih', true)
            ->whereHas('periode', function ($q) {
                $q->where('status', 'selesai');
            })
            ->orderBy('waktu_penetapan', 'desc')
            ->first();

        $top3 = collect();
        $votingProgress = null;
        $quorumWarning = false;
        $percentVoting = 0;

        $activePeriode = PeriodePenilaian::where('status', '!=', 'selesai')->latest()->first();
        $phaseDetails = null;

        if ($activePeriode) {
            $phaseDetails = $activePeriode->phase_details;

            // Get all Pegawai users
            $semuaPegawai = Pegawai::whereHas('role', function ($q) {
                $q->whereIn('tipe', ['Pegawai', 'Kepala Umum', 'Kepala_Umum']);
            })->get();

            // Get who has voted in SurveyProgress
            $sudahVoting = SurveyProgress::where('periode_id', $activePeriode->id)
                ->pluck('user_id')
                ->toArray();

            $votingProgress = $semuaPegawai->map(function ($user) use ($sudahVoting) {
                return [
                    'nama' => $user->nama,
                    'sudah_voting' => in_array($user->id, $sudahVoting),
                    'foto' => $user->foto_profil_url,
                ];
            })->sortByDesc('sudah_voting')->values();

            // Calculate Quorum
            $totalPegawaiVoter = $semuaPegawai->count();
            $sudahVotingCount = count(array_unique($sudahVoting));
            $percentVoting = $totalPegawaiVoter > 0 ? ($sudahVotingCount / $totalPegawaiVoter) * 100 : 0;

            if ($percentVoting < 50 && in_array($activePeriode->status, ['voting', 'review_kepala'])) {
                $quorumWarning = true;
            } else {
                $quorumWarning = false;
            }
        }

        if ($pemenangTerakhir && $activePeriode) {
            // Hanya sembunyikan banner jika waktu saat ini sudah masuk (atau lewat) tanggal_mulai dari periode baru tersebut.
            // Jika admin membuat periode untuk bulan depan, banner pemenang saat ini tidak boleh hilang dulu.
            $now = Carbon::now()->startOfDay();
            $mulaiPeriodeBaru = Carbon::parse($activePeriode->tanggal_mulai)->startOfDay();

            if ($now->greaterThanOrEqualTo($mulaiPeriodeBaru)) {
                $pemenangTerakhir = null;
            }
        }

        if ($pemenangTerakhir) {
            $periode = $pemenangTerakhir->periode;
            $semuaKandidat = HasilAkhir::with(['kandidat.pegawai', 'pemilih'])
                ->where('periode_id', $periode->id)
                ->get();

            $juara1 = $semuaKandidat->where('is_terpilih', true)->first();
            $lainnya = $semuaKandidat->where('is_terpilih', false)->sortBy('ranking_final')->values();

            $juara2 = $lainnya->get(0);
            $juara3 = $lainnya->get(1);

            $top3 = collect([$juara1, $juara2, $juara3])->filter();
        }

        return view('dashboard', compact('pemenangTerakhir', 'top3', 'votingProgress', 'activePeriode', 'phaseDetails', 'stickyPengumumans', 'bannerPengumumans', 'regularPengumumans', 'popupPengumuman', 'quorumWarning', 'percentVoting'));
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('departemen', 'role');

        return view('profile.show', compact('user'));
    }
}

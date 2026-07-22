<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Waktu uji coba sudah dihapus agar sistem menggunakan waktu aktual (waktu server lokal)
        
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $roleName = $user->role ? $user->role->tipe : null;
                $baseQuery = \App\Models\Pengumuman::where('status', 'Published')
                    ->where(function($q) use ($roleName) {
                        $q->whereNull('target')
                          ->orWhere('target', '')
                          ->orWhere('target', $roleName);
                    });

                $globalNotifications = (clone $baseQuery)->orderBy('created_at', 'desc')->take(5)->get();
                
                $globalSticky = (clone $baseQuery)
                    ->where('is_sticky', true)
                    ->whereIn('prioritas', ['High', 'Critical'])
                    ->get();
                    
                // Auto Generate Pengingat Voting
                $activePeriode = \App\Models\PeriodePenilaian::where('status', 'voting')->first();
                if ($activePeriode && in_array($roleName, ['Pegawai'])) { // Or roles that should vote
                    $hasVoted = \App\Models\SurveyProgress::where('periode_id', $activePeriode->id)
                        ->where('user_id', $user->id)
                        ->exists();
                        
                    $isVotingPage = request()->routeIs('pegawai.survey.*');
                        
                    if (!$hasVoted && !$isVotingPage) {
                        $votingReminder = new \App\Models\Pengumuman([
                            'id' => 'auto-voting-reminder-' . $activePeriode->id,
                            'judul' => 'Pengingat: Waktunya Voting!',
                            'konten' => "Periode voting \"{$activePeriode->nama}\" sedang berlangsung. Silakan berikan suara Anda sebelum " . \Carbon\Carbon::parse($activePeriode->tanggal_selesai_voting)->format('d M Y') . " dengan mengklik menu 'Voting Kandidat Terbaik'. Suara Anda sangat berarti!",
                            'kategori' => 'Peringatan',
                            'prioritas' => 'High',
                            'is_sticky' => true,
                            'created_at' => \Carbon\Carbon::parse($activePeriode->tanggal_mulai_voting)
                        ]);
                        $globalSticky->push($votingReminder);
                    }
                }

                // Auto Generate Pengingat Kelengkapan Data CKP & Absensi (Failsafe Layer 1)
                $persiapanPeriode = \App\Models\PeriodePenilaian::where('status', 'penginputan')->first();
                if ($persiapanPeriode && in_array($roleName, ['Admin', 'Kepala Umum', 'Kepala_Umum'])) {
                    $now = \Carbon\Carbon::now()->startOfDay();
                    // Gunakan batas persiapan yang dihitung ulang dari helper atau tanggal asli
                    $batasPersiapan = $persiapanPeriode->tanggal_selesai_persiapan
                        ? \Carbon\Carbon::parse($persiapanPeriode->tanggal_selesai_persiapan)->startOfDay()
                        : \Carbon\Carbon::parse($persiapanPeriode->tanggal_mulai)->startOfDay()->addDays(4);
                    
                    // Jika H-2 atau sudah lewat dan data belum lengkap
                    if ($now->diffInDays($batasPersiapan, false) <= 2 && !$persiapanPeriode->isDataLengkap()) {
                        $kelengkapanReminder = new \App\Models\Pengumuman([
                            'id' => 'auto-kelengkapan-reminder-' . $persiapanPeriode->id,
                            'judul' => 'Peringatan: Data CKP/Absensi Belum Lengkap!',
                            'konten' => "Masa persiapan untuk \"{$persiapanPeriode->nama}\" akan segera berakhir pada " . $batasPersiapan->format('d M Y') . ". Namun, data Nilai CKP atau Absensi (untuk 3 bulan pada triwulan ini) belum lengkap. Harap segera lengkapi data tersebut agar Masa Voting dapat berjalan dengan normal.",
                            'kategori' => 'Peringatan',
                            'prioritas' => 'Critical',
                            'is_sticky' => true,
                            'created_at' => \Carbon\Carbon::now()
                        ]);
                        $globalSticky->push($kelengkapanReminder);
                    }
                }
                
                // Auto Generate Pengingat Kuorum Partisipasi (Failsafe Layer 3)
                if ($activePeriode && in_array($roleName, ['Admin', 'Kepala Umum', 'Kepala_Umum'])) {
                    $now = \Carbon\Carbon::now()->startOfDay();
                    $batasVoting = \Carbon\Carbon::parse($activePeriode->tanggal_selesai_voting)->startOfDay();
                    
                    // Jika H-1 atau sudah lewat (menuju akhir voting)
                    if ($now->diffInDays($batasVoting, false) <= 1) {
                        $totalPegawaiVoter = \App\Models\Pegawai::whereHas('role', function($q) {
                            $q->whereIn('tipe', ['Pegawai', 'Kepala Umum', 'Kepala_Umum']);
                        })->count();
                        
                        $sudahVotingCount = \App\Models\SurveyProgress::where('periode_id', $activePeriode->id)
                            ->distinct('user_id')
                            ->count('user_id');
                            
                        $partisipasi = $totalPegawaiVoter > 0 ? ($sudahVotingCount / $totalPegawaiVoter) * 100 : 0;
                        
                        if ($partisipasi < 50) {
                            $quorumReminder = new \App\Models\Pengumuman([
                                'id' => 'auto-quorum-reminder-' . $activePeriode->id,
                                'judul' => 'Peringatan Kuorum: Partisipasi Voting < 50%!',
                                'konten' => "Masa Voting untuk \"{$activePeriode->nama}\" akan berakhir pada " . $batasVoting->format('d M Y') . ". Saat ini partisipasi masih di bawah 50% (" . round($partisipasi, 1) . "%). Harap pertimbangkan untuk memperpanjang batas waktu Masa Voting di menu Manajemen Periode.",
                                'kategori' => 'Peringatan',
                                'prioritas' => 'High',
                                'is_sticky' => true,
                                'created_at' => \Carbon\Carbon::now()
                            ]);
                            $globalSticky->push($quorumReminder);
                        }
                    }
                }
                
                // Sort by priority (Critical > High) then by created_at DESC
                $globalSticky = $globalSticky->sortByDesc(function ($item) {
                    $priorityScore = $item->prioritas == 'Critical' ? 2 : ($item->prioritas == 'High' ? 1 : 0);
                    $timestamp = $item->created_at ? (is_numeric($item->created_at) ? $item->created_at : strtotime($item->created_at)) : 0;
                    if ($item->created_at instanceof \Carbon\Carbon || $item->created_at instanceof \DateTime) {
                        $timestamp = $item->created_at->timestamp;
                    }
                    return sprintf("%d_%015d", $priorityScore, $timestamp);
                })->values();
                    
                $view->with('globalNotifications', $globalNotifications);
                $view->with('globalSticky', $globalSticky);
            }
        });
    }
}

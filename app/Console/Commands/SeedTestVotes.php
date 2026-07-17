<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Models\PertanyaanSurvei;
use App\Models\User;
use App\Models\JawabanSurvei;
use App\Models\SurveyProgress;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SeedTestVotes extends Command
{
    protected $signature = 'test:seed-votes';
    protected $description = 'Seed survey votes for active period for testing purposes';

    public function handle()
    {
        if (!app()->environment('local', 'testing')) {
            $this->error('This command can only be run in local or testing environments.');
            return 1;
        }

        $periodeAktif = PeriodePenilaian::where('status', 'voting')->first();
        if (!$periodeAktif) {
            $this->error('Tidak ada periode penilaian yang aktif saat ini.');
            return 1;
        }

        // Get candidates
        $kandidats = Kandidat::where('periode_id', $periodeAktif->id)
            ->orderBy('skor', 'desc')
            ->take(10)
            ->get();

        if ($kandidats->isEmpty()) {
            $this->error('Tidak ada kandidat untuk periode aktif.');
            return 1;
        }

        // Get questions
        $pertanyaans = PertanyaanSurvei::all();
        if ($pertanyaans->isEmpty()) {
            $this->error('Tidak ada pertanyaan survei.');
            return 1;
        }

        // Get eligible voters (Pegawai and Kepala Umum)
        $voters = User::whereHas('role', function ($query) {
            $query->whereIn('tipe', ['Pegawai', 'Kepala Umum', 'Kepala_Umum']);
        })->get();

        $jawabanData = [];
        $progressData = [];
        $now = Carbon::now();

        $this->info("Menyiapkan data vote untuk {$voters->count()} user dan {$kandidats->count()} kandidat...");

        foreach ($voters as $voter) {
            // Check if user already filled the survey for this period
            $hasFilled = SurveyProgress::where('periode_id', $periodeAktif->id)
                ->where('user_id', $voter->id)
                ->exists();
                
            if ($hasFilled) {
                continue;
            }

            foreach ($kandidats as $kandidat) {
                if ($kandidat->pegawai_id === $voter->id) {
                    continue; // skip self
                }

                // add progress
                $progressData[] = [
                    'periode_id' => $periodeAktif->id,
                    'user_id' => $voter->id,
                    'kandidat_id' => $kandidat->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // add answers
                foreach ($pertanyaans as $pertanyaan) {
                    $jawabanData[] = [
                        'id' => (string) Str::uuid(),
                        'periode_id' => $periodeAktif->id,
                        'kandidat_id' => $kandidat->id,
                        'pertanyaan_id' => $pertanyaan->id,
                        'nilai' => rand(3, 5), // generate random good scores for testing
                        'waktu_jawab' => $now,
                    ];
                }
            }
        }

        if (empty($jawabanData)) {
            $this->info("Tidak ada data jawaban baru yang perlu di-seed.");
            return 0;
        }

        $this->info("Menyimpan " . count($jawabanData) . " jawaban dan " . count($progressData) . " progress...");
        
        // Chunk insert to prevent memory/query limit issues
        foreach (array_chunk($progressData, 500) as $chunk) {
            SurveyProgress::insert($chunk);
        }
        foreach (array_chunk($jawabanData, 500) as $chunk) {
            JawabanSurvei::insert($chunk);
        }

        $this->info('Survey votes seeded successfully!');
        return 0;
    }
}

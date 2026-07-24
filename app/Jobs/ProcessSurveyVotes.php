<?php

namespace App\Jobs;

use App\Models\JawabanSurvei;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class ProcessSurveyVotes implements ShouldQueue
{
    use Queueable;

    public $periodeId;
    public $jawaban;

    /**
     * Create a new job instance.
     */
    public function __construct($periodeId, $jawaban)
    {
        $this->periodeId = $periodeId;
        $this->jawaban = $jawaban;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $insertData = [];
        $now = Carbon::now();

        foreach ($this->jawaban as $pertanyaan_id => $kandidatScores) {
            foreach ($kandidatScores as $kandidat_id => $nilai) {
                $insertData[] = [
                    'periode_id' => $this->periodeId,
                    'kandidat_id' => $kandidat_id,
                    'pertanyaan_id' => $pertanyaan_id,
                    'nilai' => $nilai,
                    'waktu_jawab' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($insertData)) {
            $chunks = array_chunk($insertData, 500);
            foreach ($chunks as $chunk) {
                JawabanSurvei::insert($chunk);
            }
        }
    }
}

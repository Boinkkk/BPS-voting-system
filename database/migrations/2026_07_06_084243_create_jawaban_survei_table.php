<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_survei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('voting_session')->cascadeOnDelete();
            $table->foreignId('kandidat_id')->constrained('kandidat')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('pertanyaan_survei')->cascadeOnDelete();
            $table->integer('nilai');
            $table->timestamp('waktu_jawab')->useCurrent();
            
            $table->unique(['session_id', 'kandidat_id', 'pertanyaan_id'], 'uq_jawaban_session_kandidat_pert');
            $table->index(['periode_id', 'kandidat_id']);
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_survei');
    }
};
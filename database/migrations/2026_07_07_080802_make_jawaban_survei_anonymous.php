<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah tabel jawaban_survei agar anonim
        Schema::table('jawaban_survei', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropUnique('uq_jawaban_session_kandidat_pert');
            $table->dropIndex(['session_id']);
            $table->dropColumn('session_id');
        });

        // 2. Buat tabel survey_progress
        Schema::create('survey_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignUuid('kandidat_id')->constrained('kandidat')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['periode_id', 'user_id', 'kandidat_id'], 'uq_survey_progress');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_progress');

        Schema::table('jawaban_survei', function (Blueprint $table) {
            $table->foreignUuid('session_id')->constrained('voting_session')->cascadeOnDelete();
            $table->unique(['session_id', 'kandidat_id', 'pertanyaan_id'], 'uq_jawaban_session_kandidat_pert');
            $table->index('session_id');
        });
    }
};

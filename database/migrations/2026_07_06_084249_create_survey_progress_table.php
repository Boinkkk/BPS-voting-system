<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};

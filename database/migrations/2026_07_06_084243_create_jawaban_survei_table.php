<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_survei', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('kandidat_id')->constrained('kandidat')->cascadeOnDelete();
            $table->foreignUuid('pertanyaan_id')->constrained('pertanyaan_survei')->cascadeOnDelete();
            $table->integer('nilai');
            $table->timestamp('waktu_jawab')->useCurrent();

            $table->index(['periode_id', 'kandidat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_survei');
    }
};

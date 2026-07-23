<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_akhir', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('kandidat_id')->constrained('kandidat')->cascadeOnDelete();
            $table->integer('ranking_final');
            $table->boolean('is_terpilih')->default(false);
            $table->foreignUuid('dipilih_oleh')->nullable()->constrained('pegawai')->restrictOnDelete();
            $table->timestamp('waktu_penetapan')->nullable();
            $table->text('catatan_kepala')->nullable();

            $table->unique(['periode_id', 'ranking_final']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_akhir');
    }
};

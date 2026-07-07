<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('id_tipe_absensi')->constrained('tipe_absen')->restrictOnDelete();
            $table->timestamp('waktu_absensi');
            $table->timestamps();
            
            $table->index(['periode_id', 'id_pegawai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawai');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinerja_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->integer('bulan')->default(1);
            $table->decimal('rata_rata_hasil_kerja', 5, 2);
            $table->decimal('rata_rata_perilaku', 5, 2);
            $table->decimal('nilai_kjk', 5, 2)->nullable();
            $table->decimal('nilai_tl_psw', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index(['periode_id', 'id_pegawai', 'bulan'], 'kinerja_pegawai_periode_pegawai_bulan_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_pegawai');
    }
};

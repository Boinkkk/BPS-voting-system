<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinerja_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->decimal('rata_rata_hasil_kerja', 4, 2);
            $table->decimal('rata_rata_perilaku', 4, 2);
            $table->decimal('nilai_kjk', 4, 2)->nullable();
            $table->decimal('nilai_tl_psw', 4, 2)->nullable();
            $table->timestamps();
            
            $table->index(['periode_id', 'id_pegawai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_pegawai');
    }
};
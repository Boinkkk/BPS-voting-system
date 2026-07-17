<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim_penilai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('peran'); // 'Penanggung Jawab', 'Ketua', 'Anggota'
            $table->timestamps();

            $table->unique(['periode_id', 'peran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tim_penilai');
    }
};

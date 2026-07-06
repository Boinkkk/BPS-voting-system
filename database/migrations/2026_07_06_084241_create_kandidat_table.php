<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kandidat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->decimal('skor', 5, 2)->default(0);
            $table->integer('ranking_sistem')->nullable();
            $table->enum('status', ['aktif', 'diskualifikasi'])->default('aktif');
            $table->timestamps();
            
            $table->unique(['periode_id', 'pegawai_id']);
            $table->index('periode_id');
            $table->index(['periode_id', 'skor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kandidat');
    }
};
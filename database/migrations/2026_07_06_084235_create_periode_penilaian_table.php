<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_penilaian', function (Blueprint $table) {
            $table->id();
            $table->integer('triwulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->string('nama', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai_persiapan')->nullable();
            $table->date('tanggal_selesai');
            $table->date('tanggal_mulai_voting')->nullable();
            $table->date('tanggal_selesai_voting')->nullable();
            $table->date('tanggal_review_kepala')->nullable();
            $table->enum('status', ['penginputan', 'voting', 'review_kepala', 'selesai'])->default('penginputan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_penilaian');
    }
};

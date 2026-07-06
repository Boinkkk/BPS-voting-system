<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->enum('tipe', ['voting_terbaik', 'kepuasan', 'lainnya'])->default('voting_terbaik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei');
    }
};
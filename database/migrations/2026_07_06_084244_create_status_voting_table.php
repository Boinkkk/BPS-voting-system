<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_voting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('pegawai')->cascadeOnDelete();
            $table->boolean('sudah_voting')->default(false);
            $table->timestamp('waktu_voting')->nullable();
            
            $table->unique(['periode_id', 'user_id']);
            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_voting');
    }
};

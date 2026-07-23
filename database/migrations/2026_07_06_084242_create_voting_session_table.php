<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_session', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('session_code', 64)->unique();
            $table->boolean('sudah_selesai')->default(false);
            $table->timestamp('waktu_mulai')->useCurrent();
            $table->timestamp('waktu_selesai')->nullable();

            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_session');
    }
};

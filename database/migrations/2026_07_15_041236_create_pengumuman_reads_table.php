<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengumuman_reads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pengumuman_id')->constrained('pengumuman')->cascadeOnDelete();
            $table->foreignUuid('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->dateTime('read_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['pengumuman_id', 'pegawai_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_reads');
    }
};

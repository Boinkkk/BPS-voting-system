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
        Schema::create('nilai_ckp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->onDelete('cascade');
            $table->foreignUuid('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->integer('bulan');
            $table->decimal('nilai', 8, 2)->default(0);
            $table->timestamps();
            
            // Optional: Ensure a unique combination
            // $table->unique(['periode_id', 'pegawai_id', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_ckp');
    }
};

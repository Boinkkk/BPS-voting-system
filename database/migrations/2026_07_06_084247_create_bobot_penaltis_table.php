<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bobot_penalti', function (Blueprint $table) {
            $table->id();
            $table->string('kategori')->comment('Ringan, Sedang, Berat, atau KJK');
            $table->string('kode_absen')->unique()->comment('Misal: TL1, TK, KJK_PER_JAM');
            $table->string('keterangan');
            $table->decimal('bobot', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_penalti');
    }
};

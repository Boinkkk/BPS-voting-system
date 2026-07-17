<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan_survei', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('nomor_urut');
            $table->string('grup_kategori')->default('BerAKHLAK');
            $table->text('pertanyaan');
            $table->string('kategori', 100)->nullable();
            $table->decimal('bobot', 3, 2)->default(1.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_survei');
    }
};

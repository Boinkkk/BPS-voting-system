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
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            $table->string('grup_kategori')->default('BerAKHLAK')->after('nomor_urut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            $table->dropColumn('grup_kategori');
        });
    }
};

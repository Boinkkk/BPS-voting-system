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
        Schema::table('hasil_akhir', function (Blueprint $table) {
            $table->uuid('dipilih_oleh')->nullable()->change();
            $table->timestamp('waktu_penetapan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_akhir', function (Blueprint $table) {
            $table->uuid('dipilih_oleh')->nullable(false)->change();
            $table->timestamp('waktu_penetapan')->useCurrent()->nullable(false)->change();
        });
    }
};

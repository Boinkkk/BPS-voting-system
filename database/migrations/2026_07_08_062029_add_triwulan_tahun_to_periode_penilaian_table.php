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
        Schema::table('periode_penilaian', function (Blueprint $table) {
            $table->integer('triwulan')->nullable()->after('id');
            $table->integer('tahun')->nullable()->after('triwulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_penilaian', function (Blueprint $table) {
            $table->dropColumn(['triwulan', 'tahun']);
        });
    }
};

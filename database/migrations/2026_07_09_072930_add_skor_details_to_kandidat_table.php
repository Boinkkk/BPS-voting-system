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
        Schema::table('kandidat', function (Blueprint $table) {
            $table->decimal('skor_ckp', 5, 2)->default(0)->after('skor');
            $table->decimal('skor_absensi', 5, 2)->default(0)->after('skor_ckp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kandidat', function (Blueprint $table) {
            $table->dropColumn(['skor_ckp', 'skor_absensi']);
        });
    }
};

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
            $table->boolean('is_terpilih')->default(false)->after('ranking_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_akhir', function (Blueprint $table) {
            $table->dropColumn('is_terpilih');
        });
    }
};

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
            $table->date('tanggal_selesai_persiapan')->nullable()->after('tanggal_mulai');
            $table->date('tanggal_review_kepala')->nullable()->after('tanggal_selesai_voting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_penilaian', function (Blueprint $table) {
            $table->dropColumn(['tanggal_selesai_persiapan', 'tanggal_review_kepala']);
        });
    }
};

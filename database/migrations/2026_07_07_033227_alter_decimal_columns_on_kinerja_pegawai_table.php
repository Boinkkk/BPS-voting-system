<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kinerja_pegawai', function (Blueprint $table) {
            $table->decimal('rata_rata_hasil_kerja', 5, 2)->change();
            $table->decimal('rata_rata_perilaku', 5, 2)->change();
            $table->decimal('nilai_kjk', 5, 2)->nullable()->change();
            $table->decimal('nilai_tl_psw', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kinerja_pegawai', function (Blueprint $table) {
            $table->decimal('rata_rata_hasil_kerja', 4, 2)->change();
            $table->decimal('rata_rata_perilaku', 4, 2)->change();
            $table->decimal('nilai_kjk', 4, 2)->nullable()->change();
            $table->decimal('nilai_tl_psw', 4, 2)->nullable()->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kinerja_pegawai', function (Blueprint $table) {
            $table->integer('bulan')->after('id_pegawai')->default(1);
            $table->index(['periode_id', 'id_pegawai', 'bulan'], 'kinerja_pegawai_periode_pegawai_bulan_index');
        });
    }

    public function down(): void
    {
        Schema::table('kinerja_pegawai', function (Blueprint $table) {
            $table->dropIndex('kinerja_pegawai_periode_pegawai_bulan_index');
            $table->dropColumn('bulan');
            $table->index(['periode_id', 'id_pegawai']);
        });
    }
};

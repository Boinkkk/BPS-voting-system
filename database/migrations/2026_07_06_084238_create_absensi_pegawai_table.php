<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_pegawai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignUuid('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->integer('bulan');
            
            $table->integer('hk')->default(0)->comment('Hari Kerja');
            $table->integer('hd')->default(0)->comment('Hadir');
            $table->integer('tk')->default(0)->comment('Tanpa Kabar');
            $table->integer('tl')->default(0)->comment('Tugas Luar');
            $table->integer('tb')->default(0)->comment('Tugas Belajar');
            $table->integer('pd')->default(0)->comment('Perjalanan Dinas');
            $table->integer('dk')->default(0)->comment('Diklat/Pelatihan');
            $table->integer('kn')->default(0)->comment('Konsinyasi');
            
            $table->integer('psw')->default(0);
            $table->integer('psw1')->default(0);
            $table->integer('psw2')->default(0);
            $table->integer('psw3')->default(0);
            $table->integer('psw4')->default(0);
            
            $table->integer('ht')->default(0);
            $table->integer('tl1')->default(0);
            $table->integer('tl2')->default(0);
            $table->integer('tl3')->default(0);
            $table->integer('tl4')->default(0);
            
            $table->integer('cb')->default(0);
            $table->integer('cl')->default(0);
            $table->integer('cm')->default(0);
            $table->integer('cp')->default(0);
            $table->integer('cs')->default(0);
            $table->integer('ct10')->default(0);
            $table->integer('ct11')->default(0);
            $table->integer('ct12')->default(0);
            
            $table->integer('cst1')->default(0);
            $table->integer('cst2')->default(0);
            $table->integer('cs1')->default(0);
            $table->integer('cp1')->default(0);
            $table->integer('cm1')->default(0);
            $table->integer('cb1')->default(0);
            
            $table->integer('kjk_ht')->default(0)->comment('KJK HT dalam menit');
            $table->integer('kjk_pc')->default(0)->comment('KJK PC dalam menit');
            $table->integer('kjk')->default(0)->comment('Total KJK dalam menit');

            $table->timestamps();

            $table->unique(['periode_id', 'pegawai_id', 'bulan'], 'absensi_pegawai_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawai');
    }
};

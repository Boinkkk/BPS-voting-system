<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            // Hapus index dan FK survei_id
            $table->dropForeign(['survei_id']);
            $table->dropIndex(['survei_id', 'nomor_urut']);
            $table->dropColumn('survei_id');
            
            // Kita biarkan kolom lain (id, nomor_urut, pertanyaan, kategori, bobot) karena masih bisa dipakai
            // kategori -> Berorientasi Pelayanan
            // pertanyaan -> Deskripsinya
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            $table->foreignUuid('survei_id')->nullable()->constrained('survei')->cascadeOnDelete();
            $table->index(['survei_id', 'nomor_urut']);
        });
    }
};

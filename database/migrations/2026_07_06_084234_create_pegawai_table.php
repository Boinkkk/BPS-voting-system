<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('role')->restrictOnDelete();
            $table->foreignId('departemen_id')->nullable()->constrained('departemen')->nullOnDelete();
            $table->string('jabatan', 100);
            $table->string('nama', 150);
            $table->string('nip', 50)->unique();
            $table->string('email', 150)->unique();
            $table->date('tanggal_masuk');
            $table->enum('status_pegawai', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
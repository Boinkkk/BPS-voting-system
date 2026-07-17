<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_bobots', function (Blueprint $table) {
            $table->id();
            $table->integer('ckp')->default(50);
            $table->integer('absensi')->default(25);
            $table->integer('survey')->default(25);
            $table->decimal('bobot_ht', 8, 2)->default(4);
            $table->decimal('bobot_psw', 8, 2)->default(1);
            $table->decimal('bobot_psw1', 8, 2)->default(0);
            $table->decimal('bobot_psw2', 8, 2)->default(0);
            $table->decimal('bobot_psw3', 8, 2)->default(0);
            $table->decimal('bobot_psw4', 8, 2)->default(0);
            $table->decimal('bobot_tl', 8, 2)->default(1);
            $table->decimal('bobot_tl1', 8, 2)->default(0);
            $table->decimal('bobot_tl2', 8, 2)->default(0);
            $table->decimal('bobot_tl3', 8, 2)->default(0);
            $table->decimal('bobot_tl4', 8, 2)->default(0);
            $table->decimal('bobot_tk', 8, 2)->default(2);
            $table->timestamps();
        });
        
        DB::table('pengaturan_bobots')->insert([
            'ckp' => 50,
            'absensi' => 25,
            'survey' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_bobots');
    }
};

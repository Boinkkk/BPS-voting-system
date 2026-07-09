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
        Schema::create('pengaturan_bobots', function (Blueprint $table) {
            $table->id();
            $table->integer('ckp')->default(50);
            $table->integer('absensi')->default(25);
            $table->integer('survey')->default(25);
            $table->timestamps();
        });
        
        // Insert default row
        DB::table('pengaturan_bobots')->insert([
            'ckp' => 50,
            'absensi' => 25,
            'survey' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_bobots');
    }
};

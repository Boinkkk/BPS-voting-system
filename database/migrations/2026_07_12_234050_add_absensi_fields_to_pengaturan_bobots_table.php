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
        Schema::table('pengaturan_bobots', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_bobots', function (Blueprint $table) {
            $table->dropColumn([
                'bobot_ht', 'bobot_psw', 'bobot_psw1', 'bobot_psw2', 'bobot_psw3', 'bobot_psw4',
                'bobot_tl', 'bobot_tl1', 'bobot_tl2', 'bobot_tl3', 'bobot_tl4', 'bobot_tk'
            ]);
        });
    }
};

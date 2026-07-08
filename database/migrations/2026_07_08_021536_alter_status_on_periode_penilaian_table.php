<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Because Doctrine DBAL doesn't support altering ENUM columns easily,
        // we can run a raw SQL statement to modify the ENUM.
        DB::statement("ALTER TABLE periode_penilaian MODIFY COLUMN status ENUM('penginputan', 'voting', 'review_kepala', 'selesai') DEFAULT 'penginputan'");
    }

    public function down(): void
    {
        // Reverting the enum (might fail if there are 'review_kepala' values)
        DB::statement("ALTER TABLE periode_penilaian MODIFY COLUMN status ENUM('penginputan', 'voting', 'selesai') DEFAULT 'penginputan'");
    }
};

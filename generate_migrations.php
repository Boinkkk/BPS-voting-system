<?php

$migrationsPath = __DIR__.'/database/migrations/';

$migrations = [
    [
        'name' => 'create_departemen_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};
PHP
    ],
    [
        'name' => 'create_role_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
PHP
    ],
    [
        'name' => 'create_tipe_absen_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipe_absen', function (Blueprint $table) {
            $table->id();
            $table->string('status', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_absen');
    }
};
PHP
    ],
    [
        'name' => 'create_pegawai_table',
        'content' => <<<'PHP'
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
PHP
    ],
    [
        'name' => 'create_periode_penilaian_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tanggal_mulai_voting')->nullable();
            $table->date('tanggal_selesai_voting')->nullable();
            $table->enum('status', ['penginputan', 'voting', 'selesai'])->default('penginputan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_penilaian');
    }
};
PHP
    ],
    [
        'name' => 'create_kriteria_penilaian_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->decimal('bobot', 3, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria_penilaian');
    }
};
PHP
    ],
    [
        'name' => 'create_kinerja_pegawai_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinerja_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->decimal('rata_rata_hasil_kerja', 4, 2);
            $table->decimal('rata_rata_perilaku', 4, 2);
            $table->decimal('nilai_kjk', 4, 2)->nullable();
            $table->decimal('nilai_tl_psw', 4, 2)->nullable();
            $table->timestamps();
            
            $table->index(['periode_id', 'id_pegawai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_pegawai');
    }
};
PHP
    ],
    [
        'name' => 'create_absensi_pegawai_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('id_tipe_absensi')->constrained('tipe_absen')->restrictOnDelete();
            $table->timestamp('waktu_absensi');
            $table->timestamps();
            
            $table->index(['periode_id', 'id_pegawai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawai');
    }
};
PHP
    ],
    [
        'name' => 'create_survei_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->enum('tipe', ['voting_terbaik', 'kepuasan', 'lainnya'])->default('voting_terbaik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei');
    }
};
PHP
    ],
    [
        'name' => 'create_pertanyaan_survei_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan_survei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survei_id')->constrained('survei')->cascadeOnDelete();
            $table->integer('nomor_urut');
            $table->text('pertanyaan');
            $table->string('kategori', 100)->nullable();
            $table->decimal('bobot', 3, 2)->default(1.00);
            $table->timestamps();
            
            $table->index(['survei_id', 'nomor_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_survei');
    }
};
PHP
    ],
    [
        'name' => 'create_kandidat_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kandidat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->decimal('skor', 5, 2)->default(0);
            $table->integer('ranking_sistem')->nullable();
            $table->enum('status', ['aktif', 'diskualifikasi'])->default('aktif');
            $table->timestamps();
            
            $table->unique(['periode_id', 'pegawai_id']);
            $table->index('periode_id');
            $table->index(['periode_id', 'skor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kandidat');
    }
};
PHP
    ],
    [
        'name' => 'create_voting_session_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_session', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('session_code', 64)->unique();
            $table->boolean('sudah_selesai')->default(false);
            $table->timestamp('waktu_mulai')->useCurrent();
            $table->timestamp('waktu_selesai')->nullable();
            
            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_session');
    }
};
PHP
    ],
    [
        'name' => 'create_jawaban_survei_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_survei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('voting_session')->cascadeOnDelete();
            $table->foreignId('kandidat_id')->constrained('kandidat')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('pertanyaan_survei')->cascadeOnDelete();
            $table->integer('nilai');
            $table->timestamp('waktu_jawab')->useCurrent();
            
            $table->unique(['session_id', 'kandidat_id', 'pertanyaan_id'], 'uq_jawaban_session_kandidat_pert');
            $table->index(['periode_id', 'kandidat_id']);
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_survei');
    }
};
PHP
    ],
    [
        'name' => 'create_status_voting_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_voting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('pegawai')->cascadeOnDelete();
            $table->boolean('sudah_voting')->default(false);
            $table->timestamp('waktu_voting')->nullable();
            
            $table->unique(['periode_id', 'user_id']);
            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_voting');
    }
};
PHP
    ],
    [
        'name' => 'create_hasil_akhir_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_penilaian')->cascadeOnDelete();
            $table->foreignId('kandidat_id')->constrained('kandidat')->cascadeOnDelete();
            $table->integer('ranking_final');
            $table->foreignId('dipilih_oleh')->constrained('pegawai')->restrictOnDelete();
            $table->timestamp('waktu_penetapan')->useCurrent();
            $table->text('catatan_kepala')->nullable();
            
            $table->unique(['periode_id', 'ranking_final']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_akhir');
    }
};
PHP
    ],
    [
        'name' => 'create_views_kandidat_progress_table',
        'content' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE VIEW v_ranking_kandidat_voting AS
            SELECT 
                k.id AS kandidat_id,
                k.periode_id,
                p.nama AS nama_pegawai,
                p.nip,
                COUNT(js.id) AS total_jawaban,
                SUM(js.nilai) AS total_skor,
                ROUND(AVG(js.nilai), 2) AS rata_rata_kumulatif
            FROM kandidat k
            JOIN pegawai p ON k.pegawai_id = p.id
            LEFT JOIN jawaban_survei js ON k.id = js.kandidat_id AND k.periode_id = js.periode_id
            GROUP BY k.id, k.periode_id, p.nama, p.nip
            ORDER BY rata_rata_kumulatif DESC;
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW v_progress_voting AS
            SELECT 
                pp.id AS periode_id,
                pp.nama AS nama_periode,
                p.id AS pegawai_id,
                p.nama AS nama_pegawai,
                COALESCE(sv.sudah_voting, FALSE) AS sudah_voting,
                sv.waktu_voting
            FROM periode_penilaian pp
            CROSS JOIN pegawai p
            LEFT JOIN status_voting sv ON pp.id = sv.periode_id AND p.id = sv.user_id
            WHERE p.status_pegawai = 'aktif';
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_progress_voting;");
        DB::unprepared("DROP VIEW IF EXISTS v_ranking_kandidat_voting;");
    }
};
PHP
    ],
];

$currentTime = time();

foreach ($migrations as $index => $migration) {
    $timestamp = date('Y_m_d_His', $currentTime + $index);
    $filename = $migrationsPath.$timestamp.'_'.$migration['name'].'.php';
    file_put_contents($filename, $migration['content']);
    echo 'Created: '.basename($filename)."\n";
}

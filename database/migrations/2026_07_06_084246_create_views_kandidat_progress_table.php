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
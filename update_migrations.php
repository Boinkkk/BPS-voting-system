<?php

$dir = 'database/migrations/';
$files = glob($dir.'*.php');

$uuidTables = [
    'pegawai', 'kinerja_pegawai', 'absensi_pegawai',
    'survei', 'pertanyaan_survei', 'jawaban_survei',
    'voting_session', 'kandidat', 'hasil_akhir',
];

$uuidForeignKeys = [
    'id_pegawai', 'survei_id', 'pertanyaan_id', 'voting_session_id', 'kandidat_id',
];

foreach ($files as $file) {
    $content = file_get_contents($file);

    // Check if table is in uuidTables (by searching for Schema::create('tablename')
    foreach ($uuidTables as $table) {
        if (preg_match("/Schema::create\(['\"]".$table."['\"]/", $content)) {
            $content = preg_replace("/\\\$table->id\(\);/", "\$table->uuid('id')->primary();", $content);
        }
    }

    // Change foreign keys to foreignUuid if they are in uuidForeignKeys
    foreach ($uuidForeignKeys as $fk) {
        $content = preg_replace("/\\\$table->foreignId\(['\"]".$fk."['\"]\)/", "\$table->foreignUuid('".$fk."')", $content);
    }

    file_put_contents($file, $content);
}
echo "Migration update complete.\n";

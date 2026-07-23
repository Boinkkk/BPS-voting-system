<?php

namespace Tests\Unit\Imports;

use App\Imports\AbsensiImport;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class AbsensiImportTest extends TestCase
{
    use RefreshDatabase;

    private $periode;

    private $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['tipe' => 'Pegawai']);
        $this->pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $role->id,
            'nama' => 'Test Pegawai',
            'nip' => '12345678',
            'email' => 'test@bps.go.id',
            'password' => 'secret',
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->periode = PeriodePenilaian::create([
            'nama' => 'Triwulan 1',
            'triwulan' => 1,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'penginputan',
        ]);
    }

    public function test_start_row_is_8()
    {
        $import = new AbsensiImport($this->periode->id, 1);
        $this->assertEquals(8, $import->startRow());
    }

    public function test_import_processes_valid_row_and_creates_absensi()
    {
        $import = new AbsensiImport($this->periode->id, 1);

        // Row matches the format:
        // [0] NIP, [1] Nama, [2] HK, [3] HD, [4] TK, [5] TL, [6] TB, [7] PD, [8] DK, [9] KN
        // [10]-[14] PSW, [15] HT, [16]-[19] TL1-4, [20]-[27] C...
        // [36] KJK
        $row = array_fill(0, 37, '0');
        $row[0] = '12345678'; // NIP
        $row[1] = 'Test Pegawai';
        $row[2] = '20'; // HK
        $row[3] = '18'; // HD
        $row[4] = '2';  // TK
        $row[36] = '120'; // KJK

        $rows = new Collection([$row]);

        $import->collection($rows);

        $this->assertDatabaseHas('absensi_pegawai', [
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai->id,
            'bulan' => 1,
            'hk' => 20,
            'hd' => 18,
            'tk' => 2,
            'kjk' => 120,
        ]);
    }

    public function test_import_skips_empty_nip()
    {
        $import = new AbsensiImport($this->periode->id, 1);

        $row = array_fill(0, 37, '0');
        $row[0] = ''; // Empty NIP

        $rows = new Collection([$row]);

        $import->collection($rows);

        $this->assertDatabaseCount('absensi_pegawai', 0);
    }

    public function test_import_skips_unknown_nip()
    {
        $import = new AbsensiImport($this->periode->id, 1);

        $row = array_fill(0, 37, '0');
        $row[0] = '99999999'; // Unknown NIP

        $rows = new Collection([$row]);

        $import->collection($rows);

        $this->assertDatabaseCount('absensi_pegawai', 0);
    }

    public function test_import_updates_existing_absensi()
    {
        $import = new AbsensiImport($this->periode->id, 1);

        // Insert first time
        $row1 = array_fill(0, 37, '0');
        $row1[0] = '12345678';
        $row1[2] = '20'; // HK

        $import->collection(new Collection([$row1]));

        $this->assertDatabaseHas('absensi_pegawai', [
            'pegawai_id' => $this->pegawai->id,
            'hk' => 20,
        ]);

        // Update with new data
        $row2 = array_fill(0, 37, '0');
        $row2[0] = '12345678';
        $row2[2] = '22'; // HK updated

        $import->collection(new Collection([$row2]));

        $this->assertDatabaseHas('absensi_pegawai', [
            'pegawai_id' => $this->pegawai->id,
            'hk' => 22,
        ]);
        $this->assertDatabaseCount('absensi_pegawai', 1);
    }
}

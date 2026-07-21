<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\PeriodePenilaian;
use App\Models\AbsensiPegawai;
use App\Models\TimPenilai;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class AbsensiAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $pegawai;
    private $periode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        
        $this->admin = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Test',
            'nip' => '11111111',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Admin',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Pegawai Test',
            'nip' => '22222222',
            'email' => 'pegawai@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->periode = PeriodePenilaian::create([
            'triwulan' => 3,
            'tahun' => 2026,
            'nama' => 'Triwulan 3 2026',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai_persiapan' => '2026-07-20',
            'tanggal_mulai_voting' => '2026-07-21',
            'tanggal_selesai_voting' => '2026-07-25',
            'tanggal_review_kepala' => '2026-07-26',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'penginputan',
        ]);
    }

    public function test_admin_can_view_absensi_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.absensi.index', [
            'periode_id' => $this->periode->id,
            'bulan' => 7
        ]));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.absensi.index');
    }

    public function test_admin_can_download_absensi_template()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get(route('admin.absensi.template'));
        
        $response->assertStatus(200);
        Excel::assertDownloaded('Template_Rekap_Absensi.xlsx');
    }

    public function test_admin_can_upload_absensi()
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('absensi.xlsx');

        $response = $this->actingAs($this->admin)->post(route('admin.absensi.upload'), [
            'periode_id' => $this->periode->id,
            'bulan' => 7,
            'file' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        Excel::assertImported('absensi.xlsx');
    }

    public function test_upload_absensi_fails_if_not_penginputan_status()
    {
        Excel::fake();

        $this->periode->update([
            'status' => 'voting',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-10',
            'tanggal_mulai_voting' => '2026-07-11',
            'tanggal_selesai_voting' => '2026-07-20',
        ]);

        $file = UploadedFile::fake()->create('absensi.xlsx');

        $response = $this->actingAs($this->admin)->post(route('admin.absensi.upload'), [
            'periode_id' => $this->periode->id,
            'bulan' => 7,
            'file' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_store_manual_absensi()
    {
        $payload = [
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai->id,
            'bulan' => 7,
            'hk' => 20,
            'hd' => 20,
            'tk' => 0,
            'psw' => 0,
            'tl' => 0,
            'kjk_ht' => 0,
            'kjk_pc' => 0,
            'kjk' => 0
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.absensi.manual'), $payload);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absensi_pegawai', [
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai->id,
            'bulan' => 7,
            'hk' => 20
        ]);
    }
}

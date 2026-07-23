<?php

namespace Tests\Feature\Controllers;

use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class KinerjaAdminControllerTest extends TestCase
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

    public function test_admin_can_view_kinerja_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kinerja.index', [
            'periode_id' => $this->periode->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.kinerja.index');
    }

    public function test_admin_can_store_kinerja_manual()
    {
        $payload = [
            'periode_id' => $this->periode->id,
            'id_pegawai' => $this->pegawai->id,
            'bulan' => 7,
            'rata_rata_hasil_kerja' => 85.5,
            'rata_rata_perilaku' => 90.0,
            'nilai_kjk' => 88.0,
            'nilai_tl_psw' => 0.0,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.kinerja.manual'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kinerja_pegawai', [
            'periode_id' => $this->periode->id,
            'id_pegawai' => $this->pegawai->id,
            'bulan' => 7,
            'rata_rata_hasil_kerja' => 85.5,
        ]);
    }

    public function test_store_kinerja_manual_fails_if_not_penginputan_status()
    {
        $this->periode->update([
            'status' => 'voting',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-10',
            'tanggal_mulai_voting' => '2026-07-11',
            'tanggal_selesai_voting' => '2026-07-20',
        ]);

        $payload = [
            'periode_id' => $this->periode->id,
            'id_pegawai' => $this->pegawai->id,
            'bulan' => 7,
            'rata_rata_hasil_kerja' => 85.5,
            'rata_rata_perilaku' => 90.0,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.kinerja.manual'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_upload_kinerja()
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('kinerja.xlsx');

        $response = $this->actingAs($this->admin)->post(route('admin.kinerja.upload'), [
            'periode_id' => $this->periode->id,
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Excel::assertImported('kinerja.xlsx');
    }

    public function test_upload_kinerja_fails_if_not_penginputan_status()
    {
        Excel::fake();

        $this->periode->update([
            'status' => 'voting',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-10',
            'tanggal_mulai_voting' => '2026-07-11',
            'tanggal_selesai_voting' => '2026-07-20',
        ]);

        $file = UploadedFile::fake()->create('kinerja.xlsx');

        $response = $this->actingAs($this->admin)->post(route('admin.kinerja.upload'), [
            'periode_id' => $this->periode->id,
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}

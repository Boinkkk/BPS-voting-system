<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Models\JawabanSurvei;
use App\Models\SurveyProgress;
use App\Models\PertanyaanSurvei;
use App\Models\NilaiCkp;
use App\Models\AbsensiPegawai;
use Illuminate\Support\Str;

class MonitoringSurveiControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $kepala;
    private $pegawai;
    private $periode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $roleKepala = Role::create(['tipe' => 'Kepala Kantor']);
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

        $this->kepala = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleKepala->id,
            'nama' => 'Kepala Test',
            'nip' => '22222222',
            'email' => 'kepala@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Kepala',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Pegawai Test',
            'nip' => '33333333',
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
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-10',
            'tanggal_mulai_voting' => '2026-07-11',
            'tanggal_selesai_voting' => '2026-07-20',
            'tanggal_review_kepala' => '2026-07-21',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'voting',
        ]);
        
        $this->kandidat = Kandidat::create([
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai->id,
            'skor' => 90,
            'ranking_sistem' => 1
        ]);
    }

    public function test_admin_can_view_monitoring_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitoring.index', [
            'periode_id' => $this->periode->id
        ]));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.monitoring.index');
        $response->assertViewHasAll(['kandidats', 'progressPegawai']);
    }

    public function test_kepala_can_view_monitoring_index()
    {
        $response = $this->actingAs($this->kepala)->get(route('admin.monitoring.index', [
            'periode_id' => $this->periode->id
        ]));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.monitoring.index');
    }

    public function test_pegawai_cannot_view_monitoring_index()
    {
        $response = $this->actingAs($this->pegawai)->get(route('admin.monitoring.index', [
            'periode_id' => $this->periode->id
        ]));
        
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_download_txt()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitoring.download_txt', [
            'periode_id' => $this->periode->id,
            'filter' => 'semua'
        ]));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function test_admin_can_update_status()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.monitoring.update_status', $this->periode->id), [
            'status' => 'review_kepala'
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('periode_penilaian', [
            'id' => $this->periode->id,
            'status' => 'review_kepala'
        ]);
    }

    public function test_kepala_cannot_update_status()
    {
        $response = $this->actingAs($this->kepala)->put(route('admin.monitoring.update_status', $this->periode->id), [
            'status' => 'review_kepala'
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}

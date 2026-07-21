<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Models\TimPenilai;
use App\Models\Departemen;
use App\Services\KandidatService;
use Illuminate\Support\Str;
use Mockery;

class KandidatAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $pegawai;

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
    }

    public function test_admin_can_view_kandidat_index()
    {
        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'penginputan',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kandidat.index'));
        
        // redirect occurs because it redirects to ?periode_id=
        if ($response->status() == 302) {
            $response = $this->actingAs($this->admin)->get(route('admin.kandidat.index', ['periode_id' => $periode->id]));
        }

        $response->assertStatus(200);
        $response->assertViewIs('admin.kandidat.index');
    }

    public function test_tim_penilai_can_view_kandidat_index()
    {
        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'penginputan',
        ]);

        TimPenilai::create([
            'periode_id' => $periode->id,
            'pegawai_id' => $this->pegawai->id,
            'peran' => 'Anggota'
        ]);

        $response = $this->actingAs($this->pegawai)->get(route('admin.kandidat.index', ['periode_id' => $periode->id]));
        $response->assertStatus(200);
    }

    public function test_pegawai_biasa_can_view_kandidat_index()
    {
        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'penginputan',
        ]);

        $response = $this->actingAs($this->pegawai)->get(route('admin.kandidat.index', ['periode_id' => $periode->id]));
        $response->assertStatus(200);
    }

    public function test_generate_top10_kandidat_fails_if_not_penginputan_status()
    {
        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'voting', // Invalid status for generate Top 10
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.kandidat.generate'), [
            'periode_id' => $periode->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_generate_top3_fails_if_not_review_kepala_status()
    {
        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'voting', // Invalid status for generate Top 3
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.kandidat.generateTop3'), [
            'periode_id' => $periode->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}

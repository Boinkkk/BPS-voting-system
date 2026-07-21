<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Models\HasilAkhir;
use Illuminate\Support\Str;

class KepalaControllerTest extends TestCase
{
    use RefreshDatabase;

    private $kepala;
    private $admin;
    private $pegawai;
    private $periode;
    private $hasilAkhir;

    protected function setUp(): void
    {
        parent::setUp();
        
        $roleKepala = Role::create(['tipe' => 'Kepala Kantor']);
        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        
        $this->kepala = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleKepala->id,
            'nama' => 'Kepala Test',
            'nip' => '22222222',
            'email' => 'kepala@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Kepala Kantor',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

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
            'tanggal_selesai_persiapan' => '2026-07-05',
            'tanggal_mulai_voting' => '2026-07-06',
            'tanggal_selesai_voting' => '2026-07-15',
            'tanggal_review_kepala' => '2026-07-16',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'review_kepala',
        ]);
        
        $kandidat = Kandidat::create([
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai->id,
            'skor' => 90,
            'ranking_sistem' => 1
        ]);

        $this->hasilAkhir = HasilAkhir::create([
            'periode_id' => $this->periode->id,
            'kandidat_id' => $kandidat->id,
            'ranking_final' => 1,
            'is_terpilih' => false,
        ]);
    }

    public function test_kepala_can_view_review_index()
    {
        $response = $this->actingAs($this->kepala)->get(route('kepala.review.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('kepala.review.index');
        $response->assertViewHasAll(['periodeReview', 'kandidats']);
    }

    public function test_admin_cannot_view_review_index()
    {
        $response = $this->actingAs($this->admin)->get(route('kepala.review.index'));
        
        $response->assertStatus(403);
    }

    public function test_kepala_can_pilih_pemenang()
    {
        $payload = [
            'catatan' => 'Kerja bagus'
        ];

        $response = $this->actingAs($this->kepala)->post(route('kepala.review.pilih', $this->hasilAkhir->id), $payload);
        
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('hasil_akhir', [
            'id' => $this->hasilAkhir->id,
            'is_terpilih' => 1,
            'dipilih_oleh' => $this->kepala->id,
            'catatan_kepala' => 'Kerja bagus'
        ]);
    }

    public function test_pilih_pemenang_fails_if_not_review_kepala_status()
    {
        $this->periode->update([
            'status' => 'voting',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-10',
            'tanggal_mulai_voting' => '2026-07-11',
            'tanggal_selesai_voting' => '2026-07-25',
            'tanggal_review_kepala' => '2026-07-26',
        ]);

        $payload = [
            'catatan' => 'Kerja bagus'
        ];

        $response = $this->actingAs($this->kepala)->post(route('kepala.review.pilih', $this->hasilAkhir->id), $payload);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('hasil_akhir', [
            'id' => $this->hasilAkhir->id,
            'is_terpilih' => 0,
        ]);
    }
}

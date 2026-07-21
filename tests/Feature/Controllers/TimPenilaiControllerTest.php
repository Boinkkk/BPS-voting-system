<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\PeriodePenilaian;
use App\Models\TimPenilai;
use Illuminate\Support\Str;

class TimPenilaiControllerTest extends TestCase
{
    use RefreshDatabase;

    private $kepala;
    private $pegawai1;
    private $pegawai2;
    private $pegawai3;
    private $periode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $roleKepala = Role::create(['tipe' => 'Kepala Kantor']);
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        
        $this->kepala = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleKepala->id,
            'nama' => 'Kepala Test',
            'nip' => '11111111',
            'email' => 'kepala@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Kepala',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->pegawai1 = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Pegawai 1',
            'nip' => '22222222',
            'email' => 'p1@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->pegawai2 = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Pegawai 2',
            'nip' => '33333333',
            'email' => 'p2@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->pegawai3 = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Pegawai 3',
            'nip' => '44444444',
            'email' => 'p3@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $this->periode = PeriodePenilaian::create([
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
    }

    public function test_kepala_can_view_tim_penilai_index()
    {
        $response = $this->actingAs($this->kepala)->get(route('kepala.tim_penilai.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('kepala.tim_penilai.index');
        $response->assertViewHasAll(['periodes', 'pegawais']);
    }

    public function test_kepala_can_store_tim_penilai()
    {
        $payload = [
            'periode_id' => $this->periode->id,
            'penanggung_jawab' => $this->pegawai1->id,
            'ketua' => $this->pegawai2->id,
            'anggota' => $this->pegawai3->id,
        ];

        $response = $this->actingAs($this->kepala)->post(route('kepala.tim_penilai.store'), $payload);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tim_penilai', [
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai1->id,
            'peran' => 'Penanggung Jawab'
        ]);

        $this->assertDatabaseHas('tim_penilai', [
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai2->id,
            'peran' => 'Ketua'
        ]);

        $this->assertDatabaseHas('tim_penilai', [
            'periode_id' => $this->periode->id,
            'pegawai_id' => $this->pegawai3->id,
            'peran' => 'Anggota'
        ]);
    }

    public function test_kepala_can_cetak_tim_penilai()
    {
        TimPenilai::create(['periode_id' => $this->periode->id, 'pegawai_id' => $this->pegawai1->id, 'peran' => 'Penanggung Jawab']);
        TimPenilai::create(['periode_id' => $this->periode->id, 'pegawai_id' => $this->pegawai2->id, 'peran' => 'Ketua']);
        TimPenilai::create(['periode_id' => $this->periode->id, 'pegawai_id' => $this->pegawai3->id, 'peran' => 'Anggota']);

        $response = $this->actingAs($this->kepala)->get(route('kepala.tim_penilai.cetak', $this->periode->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('kepala.tim_penilai.cetak');
        $response->assertViewHasAll(['periode', 'penanggungJawab', 'ketua', 'anggota', 'kepala']);
    }

    public function test_cetak_fails_if_tim_not_set()
    {
        $response = $this->actingAs($this->kepala)->get(route('kepala.tim_penilai.cetak', $this->periode->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_pegawai_cannot_access_tim_penilai_admin()
    {
        $response = $this->actingAs($this->pegawai1)->get(route('kepala.tim_penilai.index'));
        $response->assertStatus(403);
    }
}

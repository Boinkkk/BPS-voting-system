<?php

namespace Tests\Feature\Controllers;

use App\Models\Glosarium;
use App\Models\Pegawai;
use App\Models\PengaturanBobot;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GlosariumControllerTest extends TestCase
{
    use RefreshDatabase;

    private $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePegawai = Role::create(['tipe' => 'Pegawai']);

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

        PengaturanBobot::create([
            'ckp' => 50,
            'absensi' => 25,
            'survey' => 25,
            'bobot_psw' => 1.5,
            'bobot_psw1' => 0,
            'bobot_psw2' => 0,
            'bobot_psw3' => 0,
            'bobot_psw4' => 0,
            'bobot_tl' => 1.0,
            'bobot_tl1' => 0,
            'bobot_tl2' => 0,
            'bobot_tl3' => 0,
            'bobot_tl4' => 0,
            'bobot_tk' => 2.0,
        ]);
    }

    public function test_pegawai_can_view_glosarium_index()
    {
        Glosarium::create([
            'istilah' => 'CKP',
            'definisi' => 'Capaian Kinerja Pegawai',
        ]);

        $response = $this->actingAs($this->pegawai)->get(route('glosarium.index'));

        $response->assertStatus(200);
        $response->assertViewIs('glosarium.index');

        $response->assertSee('Capaian Kinerja Pegawai');
        // Check dynamic setting is also displayed
        $response->assertSee('Bobot Keseluruhan Penilaian (Sistem)');
        $response->assertSee('Bobot Absensi dan Base Score (Sistem)');
    }
}

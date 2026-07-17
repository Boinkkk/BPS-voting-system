<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\PengaturanBobot;
use Illuminate\Support\Str;

class PengaturanBobotControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        
        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $this->admin = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin User',
            'nip' => '11111111',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('password123'),
            'jabatan' => 'Admin',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif'
        ]);

        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $this->pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Test User',
            'nip' => '22222222',
            'email' => 'user@bps.go.id',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif'
        ]);
    }

    public function test_admin_can_view_pengaturan_bobot_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pengaturan-bobot.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.pengaturan-bobot.index');
        
        // Pastikan default bobot otomatis terbuat jika belum ada
        $this->assertDatabaseHas('pengaturan_bobots', [
            'ckp' => 50,
            'absensi' => 25,
            'survey' => 25
        ]);
    }

    public function test_admin_can_update_pengaturan_bobot()
    {
        PengaturanBobot::create([
            'ckp' => 50, 'absensi' => 25, 'survey' => 25,
            'bobot_psw' => 1, 'bobot_psw1' => 1, 'bobot_psw2' => 1, 'bobot_psw3' => 1, 'bobot_psw4' => 1,
            'bobot_tl' => 1, 'bobot_tl1' => 1, 'bobot_tl2' => 1, 'bobot_tl3' => 1, 'bobot_tl4' => 1,
            'bobot_tk' => 1
        ]);

        $data = [
            'ckp' => 60,
            'absensi' => 20,
            'survey' => 20,
            'bobot_psw' => 0.5, 'bobot_psw1' => 0.5, 'bobot_psw2' => 0.5, 'bobot_psw3' => 0.5, 'bobot_psw4' => 0.5,
            'bobot_tl' => 0.5, 'bobot_tl1' => 0.5, 'bobot_tl2' => 0.5, 'bobot_tl3' => 0.5, 'bobot_tl4' => 0.5,
            'bobot_tk' => 2.5
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan-bobot.update'), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('pengaturan_bobots', [
            'ckp' => 60,
            'absensi' => 20,
            'survey' => 20,
            'bobot_tk' => 2.5
        ]);
    }

    public function test_update_fails_if_total_is_not_100()
    {
        $data = [
            'ckp' => 50,
            'absensi' => 30, // Total 110%
            'survey' => 30,
            'bobot_psw' => 0.5, 'bobot_psw1' => 0.5, 'bobot_psw2' => 0.5, 'bobot_psw3' => 0.5, 'bobot_psw4' => 0.5,
            'bobot_tl' => 0.5, 'bobot_tl1' => 0.5, 'bobot_tl2' => 0.5, 'bobot_tl3' => 0.5, 'bobot_tl4' => 0.5,
            'bobot_tk' => 2.5
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan-bobot.update'), $data);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_pegawai_cannot_access_pengaturan_bobot()
    {
        $response = $this->actingAs($this->pegawai)->get(route('admin.pengaturan-bobot.index'));
        $response->assertRedirect(route('dashboard'));
    }
}

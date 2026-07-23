<?php

namespace Tests\Feature\Controllers;

use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PegawaiAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    private $rolePegawai;

    private $departemen;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $this->rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $this->departemen = Departemen::create([
            'nama' => 'IT',
        ]);

        $this->admin = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin User',
            'nip' => '11111111',
            'email' => 'admin@bps.go.id',
            'password' => bcrypt('password123'),
            'jabatan' => 'Admin',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_admin_can_view_pegawai_index()
    {
        $response = $this->actingAs($this->admin)->get('/admin/pegawai');

        $response->assertStatus(200);
        $response->assertViewIs('admin.pegawai.index');
        $response->assertViewHasAll(['pegawai', 'departemens', 'roles']);
    }

    public function test_admin_can_search_pegawai_by_nama_or_nip()
    {
        Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $this->rolePegawai->id,
            'nama' => 'Budi Santoso',
            'nip' => '55555555',
            'email' => 'budi@bps.go.id',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
            'departemen_id' => $this->departemen->id,
        ]);

        // Search by nama
        $responseNama = $this->actingAs($this->admin)->get('/admin/pegawai?search=Budi');
        $responseNama->assertStatus(200);
        $responseNama->assertSee('Budi Santoso');
        $responseNama->assertDontSee('John Doe'); // Assuming John Doe doesn't exist

        // Search by nip
        $responseNip = $this->actingAs($this->admin)->get('/admin/pegawai?search=55555555');
        $responseNip->assertStatus(200);
        $responseNip->assertSee('Budi Santoso');
    }

    public function test_admin_can_store_new_pegawai()
    {
        $data = [
            'nama' => 'New User',
            'nip' => '12341234',
            'email' => 'newuser@bps.go.id',
            'password' => 'password123',
            'jabatan' => 'Staff IT',
            'departemen_id' => $this->departemen->id,
            'role_id' => $this->rolePegawai->id,
            'tanggal_masuk' => '2023-01-01',
            'status_pegawai' => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/pegawai', $data);

        $response->assertRedirect(route('admin.pegawai.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pegawai', [
            'nama' => 'New User',
            'nip' => '12341234',
            'email' => 'newuser@bps.go.id',
            'jabatan' => 'Staff IT',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_store_fails_with_duplicate_nip_or_email()
    {
        // Try to create another user with admin's nip and email
        $data = [
            'nama' => 'Duplicate User',
            'nip' => $this->admin->nip,
            'email' => $this->admin->email,
            'password' => 'password123',
            'jabatan' => 'Staff IT',
            'departemen_id' => $this->departemen->id,
            'role_id' => $this->rolePegawai->id,
            'tanggal_masuk' => '2023-01-01',
            'status_pegawai' => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/pegawai', $data);

        $response->assertSessionHasErrors(['nip', 'email']);
    }

    public function test_admin_can_update_pegawai()
    {
        $pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $this->rolePegawai->id,
            'nama' => 'Old Name',
            'nip' => '22222222',
            'email' => 'old@bps.go.id',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
            'departemen_id' => $this->departemen->id,
        ]);

        $updateData = [
            'nama' => 'Updated Name',
            'nip' => '22222222', // Same NIP
            'email' => 'updated@bps.go.id',
            'jabatan' => 'Senior Staff',
            'departemen_id' => $this->departemen->id,
            'role_id' => $this->rolePegawai->id,
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'nonaktif',
        ];

        $response = $this->actingAs($this->admin)->put("/admin/pegawai/{$pegawai->id}", $updateData);

        $response->assertRedirect(route('admin.pegawai.index'));

        $this->assertDatabaseHas('pegawai', [
            'id' => $pegawai->id,
            'nama' => 'Updated Name',
            'email' => 'updated@bps.go.id',
            'jabatan' => 'Senior Staff',
            'status_pegawai' => 'nonaktif',
        ]);
    }

    public function test_admin_can_reset_pegawai_password()
    {
        $pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $this->rolePegawai->id,
            'nama' => 'Test User',
            'nip' => '33333333',
            'email' => 'test@bps.go.id',
            'password' => bcrypt('oldpassword'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/pegawai/{$pegawai->id}/password", [
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.pegawai.index'));

        $pegawai->refresh();
        $this->assertTrue(Hash::check('newpassword123', $pegawai->password));
    }
}

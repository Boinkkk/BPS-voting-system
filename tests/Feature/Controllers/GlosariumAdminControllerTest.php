<?php

namespace Tests\Feature\Controllers;

use App\Models\Glosarium;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GlosariumAdminControllerTest extends TestCase
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
            'status_pegawai' => 'aktif',
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
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_admin_can_view_glosarium_index()
    {
        Glosarium::create(['istilah' => 'CKP', 'definisi' => 'Capaian Kinerja Pegawai']);

        $response = $this->actingAs($this->admin)->get(route('admin.glosarium.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.glosarium.index');
        $response->assertSee('CKP');
    }

    public function test_admin_can_view_glosarium_create_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.glosarium.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.glosarium.create');
    }

    public function test_admin_can_store_glosarium()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.glosarium.store'), [
            'istilah' => 'IKP',
            'definisi' => 'Indeks Kepuasan Pelanggan',
        ]);

        $response->assertRedirect(route('admin.glosarium.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('glosariums', [
            'istilah' => 'IKP',
            'definisi' => 'Indeks Kepuasan Pelanggan',
        ]);
    }

    public function test_store_fails_with_duplicate_istilah()
    {
        Glosarium::create(['istilah' => 'CKP', 'definisi' => 'Lama']);

        $response = $this->actingAs($this->admin)->post(route('admin.glosarium.store'), [
            'istilah' => 'CKP',
            'definisi' => 'Baru',
        ]);

        $response->assertSessionHasErrors('istilah');
    }

    public function test_admin_can_view_glosarium_edit_page()
    {
        $glosarium = Glosarium::create(['istilah' => 'CKP', 'definisi' => 'Capaian Kinerja Pegawai']);

        $response = $this->actingAs($this->admin)->get(route('admin.glosarium.edit', $glosarium->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.glosarium.edit');
        $response->assertSee('CKP');
    }

    public function test_admin_can_update_glosarium()
    {
        $glosarium = Glosarium::create(['istilah' => 'CKP', 'definisi' => 'Lama']);

        $response = $this->actingAs($this->admin)->put(route('admin.glosarium.update', $glosarium->id), [
            'istilah' => 'CKP Updated',
            'definisi' => 'Baru',
        ]);

        $response->assertRedirect(route('admin.glosarium.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('glosariums', [
            'id' => $glosarium->id,
            'istilah' => 'CKP Updated',
            'definisi' => 'Baru',
        ]);
    }

    public function test_admin_can_delete_glosarium()
    {
        $glosarium = Glosarium::create(['istilah' => 'CKP', 'definisi' => 'Lama']);

        $response = $this->actingAs($this->admin)->delete(route('admin.glosarium.destroy', $glosarium->id));

        $response->assertRedirect(route('admin.glosarium.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('glosariums', [
            'id' => $glosarium->id,
        ]);
    }

    public function test_pegawai_cannot_access_glosarium_admin()
    {
        $response = $this->actingAs($this->pegawai)->get(route('admin.glosarium.index'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}

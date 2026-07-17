<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\Pengumuman;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PengumumanControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

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
    }

    public function test_admin_can_view_pengumuman_index()
    {
        Pengumuman::create([
            'judul' => 'Test Pengumuman',
            'konten' => 'Isi',
            'kategori' => 'Umum',
            'status' => 'Published',
            'prioritas' => 'Normal'
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pengumuman.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.pengumuman.index');
        $response->assertSee('Test Pengumuman');
    }

    public function test_admin_can_store_pengumuman_with_attachment()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('lampiran1.jpg');

        $data = [
            'judul' => 'Pengumuman Baru',
            'kategori' => 'Penting',
            'prioritas' => 'High',
            'konten' => 'Isi pengumuman',
            'lampiran' => [$file]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pengumuman.store'), $data);
        
        $response->assertRedirect(route('admin.pengumuman.index'));
        $response->assertSessionHas('success');

        $pengumuman = Pengumuman::where('judul', 'Pengumuman Baru')->first();
        $this->assertNotNull($pengumuman);
        $this->assertIsArray($pengumuman->lampiran);
        Storage::disk('public')->assertExists($pengumuman->lampiran[0]);
    }

    public function test_admin_can_update_pengumuman_and_remove_attachment()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('lampiran1.jpg');
        $path = $file->store('pengumuman', 'public');

        $pengumuman = Pengumuman::create([
            'judul' => 'Pengumuman Lama',
            'konten' => 'Isi',
            'kategori' => 'Umum',
            'status' => 'Published',
            'prioritas' => 'Normal',
            'lampiran' => [$path]
        ]);

        $updateData = [
            'judul' => 'Pengumuman Update',
            'kategori' => 'Penting',
            'prioritas' => 'High',
            'konten' => 'Isi update',
            'remove_lampiran' => [0 => 'on'] // Menghapus lampiran indeks 0
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.pengumuman.update', $pengumuman->id), $updateData);
        
        $response->assertRedirect(route('admin.pengumuman.index'));
        
        $pengumuman->refresh();
        $this->assertEquals('Pengumuman Update', $pengumuman->judul);
        $this->assertNull($pengumuman->lampiran);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_can_delete_pengumuman()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('lampiran1.jpg');
        $path = $file->store('pengumuman', 'public');

        $pengumuman = Pengumuman::create([
            'judul' => 'Test Pengumuman',
            'konten' => 'Isi',
            'kategori' => 'Umum',
            'status' => 'Published',
            'prioritas' => 'Normal',
            'lampiran' => [$path]
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.pengumuman.destroy', $pengumuman->id));
        
        $response->assertRedirect(route('admin.pengumuman.index'));
        $this->assertDatabaseMissing('pengumuman', ['id' => $pengumuman->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_pegawai_can_mark_pengumuman_as_read()
    {
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $pegawai = Pegawai::create([
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

        $pengumuman = Pengumuman::create([
            'judul' => 'Test Pengumuman',
            'konten' => 'Isi',
            'kategori' => 'Umum',
            'status' => 'Published',
            'prioritas' => 'Normal'
        ]);

        $response = $this->actingAs($pegawai)->post(route('pengumuman.read', $pengumuman->id));
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('pengumuman_reads', [
            'pengumuman_id' => $pengumuman->id,
            'pegawai_id' => $pegawai->id
        ]);
    }
}

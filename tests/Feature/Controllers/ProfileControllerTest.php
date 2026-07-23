<?php

namespace Tests\Feature\Controllers;

use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private $pegawai;

    private $password = 'password123';

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['tipe' => 'Pegawai']);
        $this->pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $role->id,
            'nama' => 'Test User',
            'nip' => '12345678',
            'email' => 'testuser@bps.go.id',
            'password' => bcrypt($this->password),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_user_can_update_profile_photo()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($this->pegawai)
            ->post('/profile/photo', [
                'foto_profil' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->pegawai->refresh();
        $this->assertNotNull($this->pegawai->foto_profil);
        Storage::disk('public')->assertExists($this->pegawai->foto_profil);
    }

    public function test_user_cannot_update_photo_with_invalid_file()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->pegawai)
            ->post('/profile/photo', [
                'foto_profil' => $file,
            ]);

        $response->assertSessionHasErrors(['foto_profil']);

        $this->pegawai->refresh();
        $this->assertNull($this->pegawai->foto_profil);
    }

    public function test_user_can_update_password()
    {
        $newPassword = 'newpassword123';

        $response = $this->actingAs($this->pegawai)
            ->put('/profile/password', [
                'current_password' => $this->password,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->pegawai->refresh();
        $this->assertTrue(Hash::check($newPassword, $this->pegawai->password));
    }

    public function test_user_cannot_update_password_with_wrong_current_password()
    {
        $newPassword = 'newpassword123';

        $response = $this->actingAs($this->pegawai)
            ->put('/profile/password', [
                'current_password' => 'wrongpassword',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response->assertSessionHasErrors(['current_password']);

        $this->pegawai->refresh();
        $this->assertTrue(Hash::check($this->password, $this->pegawai->password));
    }

    public function test_user_cannot_update_password_with_unmatched_confirmation()
    {
        $newPassword = 'newpassword123';

        $response = $this->actingAs($this->pegawai)
            ->put('/profile/password', [
                'current_password' => $this->password,
                'password' => $newPassword,
                'password_confirmation' => 'differentpassword',
            ]);

        $response->assertSessionHasErrors(['password']);

        $this->pegawai->refresh();
        $this->assertTrue(Hash::check($this->password, $this->pegawai->password));
    }
}

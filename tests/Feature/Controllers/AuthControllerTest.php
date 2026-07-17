<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Support\Str;

class AuthControllerTest extends TestCase
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
            'nip' => '198001012010011001',
            'email' => 'testuser@bps.go.id',
            'password' => bcrypt($this->password),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif'
        ]);
    }

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_pegawai_can_login_using_email()
    {
        $response = $this->post('/login', [
            'identifier' => $this->pegawai->email,
            'password' => $this->password,
        ]);

        $this->assertAuthenticatedAs($this->pegawai);
        $response->assertRedirect('dashboard');
    }

    public function test_pegawai_can_login_using_nip()
    {
        $response = $this->post('/login', [
            'identifier' => $this->pegawai->nip,
            'password' => $this->password,
        ]);

        $this->assertAuthenticatedAs($this->pegawai);
        $response->assertRedirect('dashboard');
    }

    public function test_login_fails_with_invalid_nip()
    {
        $response = $this->post('/login', [
            'identifier' => '999999999999999999', // Unknown NIP
            'password' => $this->password,
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['identifier' => 'NIP tidak ditemukan dalam sistem.']);
    }

    public function test_login_fails_with_invalid_password()
    {
        $response = $this->post('/login', [
            'identifier' => $this->pegawai->email,
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['identifier' => 'Kredensial yang diberikan tidak cocok dengan data kami.']);
    }

    public function test_pegawai_can_logout()
    {
        $this->actingAs($this->pegawai);
        
        $response = $this->post('/logout');
        
        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}

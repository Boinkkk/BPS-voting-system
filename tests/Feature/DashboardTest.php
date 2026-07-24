<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_dashboard_does_not_500()
    {
        $user = \App\Models\Pegawai::first();
        if (! $user) {
            $role = \App\Models\Role::firstOrCreate(['tipe' => 'Pegawai']);
            $user = \App\Models\Pegawai::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'role_id' => $role->id,
                'nama' => 'Pegawai Test',
                'nip' => '33333333',
                'email' => 'pegawai@test.com',
                'password' => bcrypt('password123'),
                'jabatan' => 'Staff',
                'tanggal_masuk' => '2010-01-01',
                'status_pegawai' => 'aktif',
            ]);
        }
        $response = $this->actingAs($user)->get('/dashboard?month=9&year=2026');

        if ($response->status() == 500) {
            echo 'ERROR 500! '.$response->exception->getMessage()."\n";
            echo $response->exception->getFile().':'.$response->exception->getLine()."\n";
        }

        $response->assertStatus(200);
    }
}

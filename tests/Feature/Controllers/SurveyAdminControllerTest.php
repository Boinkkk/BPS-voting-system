<?php

namespace Tests\Feature\Controllers;

use App\Models\Pegawai;
use App\Models\PertanyaanSurvei;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SurveyAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    private $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['tipe' => 'Admin']);
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);

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
            'nip' => '22222222',
            'email' => 'pegawai@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_admin_can_view_survey_index()
    {
        PertanyaanSurvei::create([
            'kategori' => 'Kinerja',
            'pertanyaan' => 'Seberapa baik kinerjanya?',
            'nomor_urut' => 1,
            'bobot' => 1.0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.survey.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.survey.index');
        $response->assertViewHas('pertanyaans');
    }

    public function test_admin_can_store_survey_question()
    {
        $payload = [
            'kategori' => 'Komunikasi',
            'pertanyaan' => 'Bagaimana komunikasinya?',
            'nomor_urut' => 2,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.survey.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pertanyaan_survei', [
            'kategori' => 'Komunikasi',
            'pertanyaan' => 'Bagaimana komunikasinya?',
            'nomor_urut' => 2,
            'bobot' => 1.0,
        ]);
    }

    public function test_admin_can_update_survey_question()
    {
        $pertanyaan = PertanyaanSurvei::create([
            'kategori' => 'Kinerja',
            'pertanyaan' => 'Lama',
            'nomor_urut' => 1,
            'bobot' => 1.0,
        ]);

        $payload = [
            'kategori' => 'Leadership',
            'pertanyaan' => 'Baru',
            'nomor_urut' => 3,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.survey.update', $pertanyaan->id), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pertanyaan_survei', [
            'id' => $pertanyaan->id,
            'kategori' => 'Leadership',
            'pertanyaan' => 'Baru',
            'nomor_urut' => 3,
        ]);
    }

    public function test_admin_can_destroy_survey_question()
    {
        $pertanyaan = PertanyaanSurvei::create([
            'kategori' => 'Kinerja',
            'pertanyaan' => 'Hapus ini',
            'nomor_urut' => 1,
            'bobot' => 1.0,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.survey.destroy', $pertanyaan->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('pertanyaan_survei', [
            'id' => $pertanyaan->id,
        ]);
    }
}

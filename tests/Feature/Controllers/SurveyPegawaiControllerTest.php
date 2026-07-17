<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PeriodePenilaian;
use App\Models\Kandidat;
use App\Models\Role;
use App\Models\Pegawai;
use App\Models\PertanyaanSurvei;
use App\Models\JawabanSurvei;
use App\Models\SurveyProgress;
use App\Models\NilaiCkp;
use App\Models\AbsensiPegawai;
use Illuminate\Support\Str;

class SurveyPegawaiControllerTest extends TestCase
{
    use RefreshDatabase;

    private $rolePegawai;
    private $roleTimPenilai;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $this->roleTimPenilai = Role::create(['tipe' => 'Tim Penilai']);
    }

    private function createPegawai($roleId, $overrides = [])
    {
        $nip = $overrides['nip'] ?? (string) rand(1000, 9999);
        return Pegawai::create(array_merge([
            'id' => (string) Str::uuid(),
            'role_id' => $roleId,
            'nama' => 'P_' . $nip,
            'nip' => $nip,
            'email' => "email_$nip@test.com",
            'password' => 'secret',
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ], $overrides));
    }

    private function createCompletePeriode($status = 'voting')
    {
        \Carbon\Carbon::setTestNow('2026-01-07 10:00:00');

        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'triwulan' => 1,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => $status
        ]);

        $pegawai = $this->createPegawai($this->rolePegawai->id);
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 100]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 1, 'kjk' => 0]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 2, 'kjk' => 0]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 3, 'kjk' => 0]);

        return $periode;
    }

    public function test_index_shows_error_when_no_active_voting_period()
    {
        $user = $this->createPegawai($this->rolePegawai->id);
        
        $this->actingAs($user)
             ->get(route('pegawai.survey.index'))
             ->assertSee('Tidak ada periode penilaian yang aktif saat ini.');
    }

    public function test_index_excludes_user_from_kandidat_list()
    {
        $periode = $this->createCompletePeriode();
        $user = $this->createPegawai($this->rolePegawai->id);
        $otherPegawai = $this->createPegawai($this->rolePegawai->id);

        Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $user->id, 'skor' => 90]);
        Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $otherPegawai->id, 'skor' => 80]);

        $this->actingAs($user)
             ->get(route('pegawai.survey.index'))
             ->assertViewHas('kandidats', function ($kandidats) use ($user) {
                 return $kandidats && !$kandidats->contains('pegawai_id', $user->id) && $kandidats->count() === 1;
             });
    }

    public function test_store_rejects_non_pegawai_users()
    {
        $periode = $this->createCompletePeriode();
        $admin = $this->createPegawai($this->roleTimPenilai->id);

        $this->actingAs($admin)
             ->post(route('pegawai.survey.store'), ['jawaban' => []])
             ->assertRedirect(route('pegawai.survey.index'))
             ->assertSessionHas('error', 'Hanya pegawai dan Kepala Umum yang dapat mensubmit survei. Anda hanya memiliki akses pratinjau (read-only).');
    }

    public function test_store_saves_survey_anonymously_and_records_progress()
    {
        $periode = $this->createCompletePeriode();
        $user = $this->createPegawai($this->rolePegawai->id);
        $kandidatPegawai = $this->createPegawai($this->rolePegawai->id);
        
        $kandidat = Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $kandidatPegawai->id, 'skor' => 90]);
        $pertanyaan = PertanyaanSurvei::create(['pertanyaan' => 'Q1', 'nomor_urut' => 1]);

        $payload = [
            'jawaban' => [
                $pertanyaan->id => [
                    $kandidat->id => 5
                ]
            ]
        ];

        $response = $this->actingAs($user)
             ->post(route('pegawai.survey.store'), $payload);
             
        $response->assertRedirect(route('pegawai.survey.index'))
                 ->assertSessionHas('success');

        // Verify Anonymous Jawaban
        $this->assertDatabaseHas('jawaban_survei', [
            'periode_id' => $periode->id,
            'kandidat_id' => $kandidat->id,
            'pertanyaan_id' => $pertanyaan->id,
            'nilai' => 5
        ]);

        // Verify Progress
        $this->assertDatabaseHas('survey_progress', [
            'periode_id' => $periode->id,
            'user_id' => $user->id,
            'kandidat_id' => $kandidat->id
        ]);
    }

    public function test_store_fails_when_data_is_incomplete()
    {
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'triwulan' => 1,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'voting'
        ]); // Incomplete data!

        $user = $this->createPegawai($this->rolePegawai->id);

        $payload = [
            'jawaban' => [
                1 => [
                    1 => 5
                ]
            ]
        ];

        $this->actingAs($user)
             ->post(route('pegawai.survey.store'), $payload)
             ->assertRedirect(route('pegawai.survey.index'))
             ->assertSessionHas('error', 'Pemilihan sedang ditunda. Data kandidat belum lengkap.');
    }
}

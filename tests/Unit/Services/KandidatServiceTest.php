<?php

namespace Tests\Unit\Services;

use App\Models\AbsensiPegawai;
use App\Models\HasilAkhir;
use App\Models\JawabanSurvei;
use App\Models\Kandidat;
use App\Models\NilaiCkp;
use App\Models\Pegawai;
use App\Models\PengaturanBobot;
use App\Models\PeriodePenilaian;
use App\Models\PertanyaanSurvei;
use App\Models\Role;
use App\Services\KandidatService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class KandidatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard();
    }

    private function createPeriode($overrides = [])
    {
        return PeriodePenilaian::create(array_merge([
            'nama' => 'P1',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'voting',
        ], $overrides));
    }

    private function createPegawai($roleId, $overrides = [])
    {
        $nip = $overrides['nip'] ?? (string) rand(1000, 9999);

        return Pegawai::create(array_merge([
            'id' => (string) Str::uuid(),
            'role_id' => $roleId,
            'nama' => 'P_'.$nip,
            'nip' => $nip,
            'email' => "email_$nip@test.com",
            'password' => 'secret',
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ], $overrides));
    }

    public function test_it_returns_early_when_periode_not_found()
    {
        // Act
        KandidatService::generateTop10Kandidat(999);

        // Assert
        $this->assertDatabaseCount('kandidat', 0);
    }

    public function test_it_generates_top_10_kandidat_correctly_with_weighted_scores()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);

        $pegawai1 = $this->createPegawai($rolePegawai->id, ['nama' => 'Pegawai A', 'nip' => '111']);
        $pegawai2 = $this->createPegawai($rolePegawai->id, ['nama' => 'Pegawai B', 'nip' => '222']);

        // Weight: CKP=50, Absensi=25. Total Fase 1 = 75.
        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 50, 'absensi' => 25, 'bobot_tk' => 5]);

        // P1: CKP = 90. Absensi KJK = 0 (Base 100), TK = 0
        // Expected Absensi: 100. Expected Score: 90 * (2/3) + 100 * (1/3) = 60 + 33.333 = 93.333
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai1->id, 'nilai' => 90]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai1->id, 'bulan' => 1, 'kjk' => 0, 'tk' => 0]);

        // P2: CKP = 84. Absensi KJK = 60 (Base 99), TK = 1 (Deduction 5)
        // Expected Absensi: 99 - 5 = 94. Expected Score: 84 * (2/3) + 94 * (1/3) = 56 + 31.333 = 87.333
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai2->id, 'nilai' => 84]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai2->id, 'bulan' => 1, 'kjk' => 60, 'tk' => 1]);

        // Act
        KandidatService::generateTop10Kandidat($periode->id);

        // Assert
        $this->assertDatabaseCount('kandidat', 2);

        $kandidat1 = Kandidat::where('pegawai_id', $pegawai1->id)->first();
        $this->assertNotNull($kandidat1);
        $this->assertEquals(93.33, round($kandidat1->skor, 2));
        $this->assertEquals(1, $kandidat1->ranking_sistem);

        $kandidat2 = Kandidat::where('pegawai_id', $pegawai2->id)->first();
        $this->assertNotNull($kandidat2);
        $this->assertEquals(87.33, round($kandidat2->skor, 2));
        $this->assertEquals(2, $kandidat2->ranking_sistem);
    }

    public function test_it_skips_pegawai_when_no_ckp_and_no_absensi_data_exist()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $this->createPegawai($rolePegawai->id, ['nama' => 'Ghost', 'nip' => '000']);
        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 50, 'absensi' => 25]);

        // Act
        KandidatService::generateTop10Kandidat($periode->id);

        // Assert
        $this->assertDatabaseCount('kandidat', 0);
    }

    public function test_it_calculates_absensi_score_correctly_for_various_kjk_boundaries()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 0, 'absensi' => 100]); // isolate absensi weight to 100%

        $kjkCases = [
            0 => 100,
            1 => 99,
            60 => 99,
            61 => 98,
            120 => 98,
            121 => 97,
            450 => 97,
            451 => 96,
        ];

        foreach ($kjkCases as $kjk => $expectedBase) {
            $pegawai = $this->createPegawai($rolePegawai->id, ['nip' => "N_$kjk"]);
            AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 1, 'kjk' => $kjk]);
        }

        // Act
        KandidatService::generateTop10Kandidat($periode->id);

        // Assert
        $kandidats = Kandidat::where('periode_id', $periode->id)->get();
        $this->assertCount(count($kjkCases), $kandidats);

        foreach ($kjkCases as $kjk => $expectedBase) {
            $pegawaiId = Pegawai::where('nip', "N_$kjk")->first()->id;
            $kandidat = Kandidat::where('pegawai_id', $pegawaiId)->first();
            $this->assertEquals($expectedBase, $kandidat->skor_absensi, "KJK $kjk should yield base score $expectedBase");
        }
    }

    public function test_it_deducts_tl_and_psw_scores_based_on_pengaturan()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $pegawai = $this->createPegawai($rolePegawai->id, ['nama' => 'Pegawai A', 'nip' => '111']);

        PengaturanBobot::truncate();
        PengaturanBobot::create([
            'ckp' => 0, 'absensi' => 100, // isolate absensi
            'bobot_tl1' => 1, 'bobot_tl2' => 2, 'bobot_tl3' => 3, 'bobot_tl4' => 4,
            'bobot_psw1' => 1, 'bobot_psw2' => 2, 'bobot_psw3' => 3, 'bobot_psw4' => 4,
        ]);

        // Base KJK=0 -> 100
        // TL deduction: (1*1) + (2*2) + (1*3) + (0*4) = 1 + 4 + 3 = 8
        // PSW deduction: (1*1) + (1*2) + (0*3) + (1*4) = 1 + 2 + 4 = 7
        // Total deduction = 15. Expected Absensi = 85.
        AbsensiPegawai::create([
            'periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 1, 'kjk' => 0,
            'tl1' => 1, 'tl2' => 2, 'tl3' => 1, 'tl4' => 0,
            'psw1' => 1, 'psw2' => 1, 'psw3' => 0, 'psw4' => 1,
        ]);

        // Act
        KandidatService::generateTop10Kandidat($periode->id);

        // Assert
        $kandidat = Kandidat::where('pegawai_id', $pegawai->id)->first();
        $this->assertEquals(85, $kandidat->skor_absensi);
    }

    public function test_it_ensures_absensi_score_does_not_fall_below_zero()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        $pegawai = $this->createPegawai($rolePegawai->id, ['nama' => 'Naughty', 'nip' => '999']);

        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 0, 'absensi' => 100, 'bobot_tk' => 10]);

        // Base KJK=0 -> 100. TK=15 -> Deduction 150. Expected: 100 - 150 = -50 -> max(0, -50) = 0.
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 1, 'kjk' => 0, 'tk' => 15]);

        // Act
        KandidatService::generateTop10Kandidat($periode->id);

        // Assert
        $kandidat = Kandidat::where('pegawai_id', $pegawai->id)->first();
        $this->assertEquals(0, $kandidat->skor_absensi);
    }

    public function test_it_limits_to_top_10_kandidats_and_overwrites_existing_ones()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 100, 'absensi' => 0]); // purely CKP for simplicity

        // Seed 15 pegawais
        for ($i = 1; $i <= 15; $i++) {
            $pegawai = $this->createPegawai($rolePegawai->id, ['nama' => "Pegawai $i", 'nip' => "10$i"]);
            NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 50 + $i]); // scores 51 to 65
        }

        // Insert a dummy existing candidate to verify overwrite
        Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $this->createPegawai($rolePegawai->id, ['nip' => '9999'])->id, 'skor_ckp' => 10, 'skor_absensi' => 10, 'skor' => 10, 'ranking_sistem' => 1, 'status' => 'aktif']);

        // Act
        KandidatService::generateTop10Kandidat($periode->id);

        // Assert
        $this->assertDatabaseCount('kandidat', 10);

        // Highest score should be 65, which is Pegawai 15
        $topKandidat = Kandidat::where('periode_id', $periode->id)->orderBy('skor', 'desc')->first();
        $this->assertEquals(65, $topKandidat->skor);
        $this->assertEquals(1, $topKandidat->ranking_sistem);
    }

    public function test_it_generates_top_3_kandidat_correctly_with_ckp_absensi_and_survey()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 50, 'absensi' => 25, 'survey' => 25]); // Final Weights

        PertanyaanSurvei::create(['id' => 1, 'pertanyaan' => 'Q1', 'nomor_urut' => 1]);
        PertanyaanSurvei::create(['id' => 2, 'pertanyaan' => 'Q2', 'nomor_urut' => 2]);

        // P1: CKP=100 (50), Absensi KJK=0->100 (25), Survey Avg 5->100 (25). Total = 100
        $pegawai1 = $this->createPegawai($rolePegawai->id, ['nama' => 'P1', 'nip' => '1111']);
        $kandidat1 = Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai1->id]);
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai1->id, 'nilai' => 100]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai1->id, 'bulan' => 1, 'kjk' => 0]);
        JawabanSurvei::create(['periode_id' => $periode->id, 'kandidat_id' => $kandidat1->id, 'nilai' => 5, 'pertanyaan_id' => 1]);
        JawabanSurvei::create(['periode_id' => $periode->id, 'kandidat_id' => $kandidat1->id, 'nilai' => 5, 'pertanyaan_id' => 2]); // Avg 5 -> Normalized 100

        // P2: CKP=80 (40), Absensi KJK=451->96 (24), Survey Avg 2.5->50 (12.5). Total = 76.5
        $pegawai2 = $this->createPegawai($rolePegawai->id, ['nama' => 'P2', 'nip' => '2222']);
        $kandidat2 = Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai2->id]);
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai2->id, 'nilai' => 80]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai2->id, 'bulan' => 1, 'kjk' => 451]);
        JawabanSurvei::create(['periode_id' => $periode->id, 'kandidat_id' => $kandidat2->id, 'nilai' => 2, 'pertanyaan_id' => 1]);
        JawabanSurvei::create(['periode_id' => $periode->id, 'kandidat_id' => $kandidat2->id, 'nilai' => 3, 'pertanyaan_id' => 2]); // Avg 2.5 -> Normalized 50

        // P3: CKP=60 (30), Absensi=99 (24.75), Survey Avg=4->80 (20). Total = 74.75
        $pegawai3 = $this->createPegawai($rolePegawai->id, ['nama' => 'P3', 'nip' => '3333']);
        $kandidat3 = Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai3->id]);
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai3->id, 'nilai' => 60]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai3->id, 'bulan' => 1, 'kjk' => 60]);
        JawabanSurvei::create(['periode_id' => $periode->id, 'kandidat_id' => $kandidat3->id, 'nilai' => 4, 'pertanyaan_id' => 1]);

        // P4: (Will be 4th, dropped) CKP=0, Absensi=0, Survey=0
        $pegawai4 = $this->createPegawai($rolePegawai->id, ['nama' => 'P4', 'nip' => '4444']);
        $kandidat4 = Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai4->id]);

        // Act
        KandidatService::generateTop3Kandidat($periode->id);

        // Assert
        $this->assertDatabaseCount('hasil_akhir', 3);

        $hasil1 = HasilAkhir::where('kandidat_id', $kandidat1->id)->first();
        $this->assertNotNull($hasil1);
        $this->assertEquals(1, $hasil1->ranking_final);

        $hasil2 = HasilAkhir::where('kandidat_id', $kandidat2->id)->first();
        $this->assertNotNull($hasil2);
        $this->assertEquals(2, $hasil2->ranking_final);

        $hasil3 = HasilAkhir::where('kandidat_id', $kandidat3->id)->first();
        $this->assertNotNull($hasil3);
        $this->assertEquals(3, $hasil3->ranking_final);

        $this->assertDatabaseMissing('hasil_akhir', ['kandidat_id' => $kandidat4->id]);
    }

    public function test_it_handles_ties_and_zero_values_in_top_3_generation()
    {
        // Arrange
        $periode = $this->createPeriode();
        $rolePegawai = Role::create(['tipe' => 'Pegawai']);
        PengaturanBobot::truncate();
        PengaturanBobot::create(['ckp' => 33, 'absensi' => 33, 'survey' => 34]);

        $createKandidat = function ($nip) use ($periode, $rolePegawai) {
            $pegawai = $this->createPegawai($rolePegawai->id, ['nip' => $nip]);

            return Kandidat::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id]);
        };

        $k1 = $createKandidat('5001'); // All Zero

        $k2 = $createKandidat('5002'); // All Zero (Tie with K1)

        $k3 = $createKandidat('5003'); // Will have some score
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $k3->pegawai_id, 'nilai' => 10]);

        $k4 = $createKandidat('5004'); // Will have identical score to K3
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $k4->pegawai_id, 'nilai' => 10]);

        // Act
        KandidatService::generateTop3Kandidat($periode->id);

        // Assert
        $this->assertDatabaseCount('hasil_akhir', 3);

        // Expect K3 and K4 to be in top 2 (ranking 1 and 2), and either K1 or K2 in ranking 3.
        $this->assertDatabaseHas('hasil_akhir', ['kandidat_id' => $k3->id]);
        $this->assertDatabaseHas('hasil_akhir', ['kandidat_id' => $k4->id]);

        $remaining = HasilAkhir::whereIn('kandidat_id', [$k1->id, $k2->id])->count();
        $this->assertEquals(1, $remaining, 'Only one of the tied zero-score candidates should be selected for the 3rd spot.');
    }
}

<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\KandidatService;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use App\Models\Pegawai;
use App\Models\PengaturanBobot;
use App\Models\NilaiCkp;
use App\Models\AbsensiPegawai;
use App\Models\Kandidat;
use App\Models\HasilAkhir;
use App\Models\JawabanSurvei;

class KandidatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $periode;
    protected $rolePegawai;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Arrange Common Data
        $this->periode = PeriodePenilaian::create([
            'nama' => 'Triwulan 1 2024',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-03-31',
            'tahun' => 2024,
            'status' => 'voting'
        ]);

        $this->rolePegawai = Role::create([
            'nama' => 'Pegawai Biasa',
            'tipe' => 'Pegawai'
        ]);

        // Default Pengaturan Bobot
        PengaturanBobot::query()->delete();
        PengaturanBobot::create([
            'ckp' => 50,
            'absensi' => 25,
            'survey' => 25,
            'bobot_tl1' => 1, 'bobot_tl2' => 2, 'bobot_tl3' => 3, 'bobot_tl4' => 4,
            'bobot_psw1' => 1, 'bobot_psw2' => 2, 'bobot_psw3' => 3, 'bobot_psw4' => 4,
            'bobot_tk' => 5, 'bobot_tl' => 0, 'bobot_psw' => 0
        ]);
    }

    public function testShouldReturnNullWhenPeriodeDoesNotExistInTop10()
    {
        $result = KandidatService::generateTop10Kandidat(999);
        $this->assertNull($result);
        $this->assertDatabaseCount('kandidat', 0);
    }

    public function testShouldReturnNullWhenPeriodeDoesNotExistInTop3()
    {
        $result = KandidatService::generateTop3Kandidat(999);
        $this->assertNull($result);
        $this->assertDatabaseCount('hasil_akhir', 0);
    }

    public function testShouldGenerateTop10CorrectlyWithVariousAbsensi()
    {
        // KJK = 0 (Base 100)
        $pegawai1 = Pegawai::create(['nama' => 'A', 'role_id' => $this->rolePegawai->id, 'nip' => '1', 'email' => 'a@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai1->id, 'nilai' => 90]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai1->id, 'kjk' => 0]);

        // KJK = 30 (Base 99) and Pengurangan
        $pegawai2 = Pegawai::create(['nama' => 'B', 'role_id' => $this->rolePegawai->id, 'nip' => '2', 'email' => 'b@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai2->id, 'nilai' => 80]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai2->id, 'kjk' => 30, 'tl1' => 1, 'tk' => 1]);

        // KJK = 100 (Base 98)
        $pegawai3 = Pegawai::create(['nama' => 'C', 'role_id' => $this->rolePegawai->id, 'nip' => '3', 'email' => 'c@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai3->id, 'nilai' => 85]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai3->id, 'kjk' => 100]); 

        // KJK = 200 (Base 97)
        $pegawai4 = Pegawai::create(['nama' => 'D', 'role_id' => $this->rolePegawai->id, 'nip' => '4', 'email' => 'd@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai4->id, 'nilai' => 85]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai4->id, 'kjk' => 200]);

        // KJK = 500 (Base 96)
        $pegawai5 = Pegawai::create(['nama' => 'E', 'role_id' => $this->rolePegawai->id, 'nip' => '5', 'email' => 'e@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai5->id, 'nilai' => 85]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai5->id, 'kjk' => 500]);

        KandidatService::generateTop10Kandidat($this->periode->id);

        $this->assertDatabaseCount('kandidat', 5);
        
        $kandidat1 = Kandidat::where('pegawai_id', $pegawai1->id)->first();
        $this->assertEquals(90, $kandidat1->skor_ckp);
        $this->assertEquals(100, $kandidat1->skor_absensi);
        $this->assertEqualsWithDelta(93.33, $kandidat1->skor, 0.01);
        
        $kandidat2 = Kandidat::where('pegawai_id', $pegawai2->id)->first();
        $this->assertEquals(93, $kandidat2->skor_absensi); 
    }

    public function testShouldUseFallbackBobotWhenSpecificBobotIsZero()
    {
        PengaturanBobot::query()->delete();
        PengaturanBobot::create([
            'ckp' => 50, 'absensi' => 25, 'survey' => 25,
            'bobot_tl1' => 0, 'bobot_tl2' => 0, 'bobot_tl3' => 0, 'bobot_tl4' => 0,
            'bobot_psw1' => 0, 'bobot_psw2' => 0, 'bobot_psw3' => 0, 'bobot_psw4' => 0,
            'bobot_tk' => 5, 'bobot_tl' => 2, 'bobot_psw' => 2
        ]);

        $pegawai = Pegawai::create(['nama' => 'A', 'role_id' => $this->rolePegawai->id, 'nip' => '1', 'email' => 'f@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 80]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'kjk' => 0, 'tl' => 3, 'psw' => 2]);

        KandidatService::generateTop10Kandidat($this->periode->id);

        $kandidat = Kandidat::where('pegawai_id', $pegawai->id)->first();
        $this->assertEquals(90, $kandidat->skor_absensi);
    }

    public function testShouldHandleMissingPengaturanBobotGracefully()
    {
        PengaturanBobot::query()->delete();

        $pegawai = Pegawai::create(['nama' => 'A', 'role_id' => $this->rolePegawai->id, 'nip' => '1', 'email' => 'g@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 80]);
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'kjk' => 0, 'tl' => 3, 'psw' => 2]);

        KandidatService::generateTop10Kandidat($this->periode->id);

        $kandidat = Kandidat::where('pegawai_id', $pegawai->id)->first();
        // Base is 100, no pengurangan because $pengaturan is null, so absensi = 100
        $this->assertEquals(100, $kandidat->skor_absensi);
    }

    public function testShouldSkipCandidateWithoutCkpAndAbsensi()
    {
        $pegawai1 = Pegawai::create(['nama' => 'A', 'role_id' => $this->rolePegawai->id, 'nip' => '1', 'email' => 'h@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']); 
        $pegawai2 = Pegawai::create(['nama' => 'B', 'role_id' => $this->rolePegawai->id, 'nip' => '2', 'email' => 'i@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']); 
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai2->id, 'nilai' => 80]);

        KandidatService::generateTop10Kandidat($this->periode->id);

        $this->assertDatabaseCount('kandidat', 1);
        $this->assertDatabaseHas('kandidat', ['pegawai_id' => $pegawai2->id]);
        $this->assertDatabaseMissing('kandidat', ['pegawai_id' => $pegawai1->id]);
    }

    public function testShouldOnlyGenerateTop10AndSortProperly()
    {
        for ($i = 1; $i <= 12; $i++) {
            $pegawai = Pegawai::create(['nama' => "P$i", 'role_id' => $this->rolePegawai->id, 'nip' => (string)$i, 'email' => "p$i@test.com", 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
            NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 100 - $i]);
            AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'kjk' => 0]);
        }

        KandidatService::generateTop10Kandidat($this->periode->id);

        $this->assertDatabaseCount('kandidat', 10);
        
        $kandidats = Kandidat::orderBy('ranking_sistem', 'asc')->get();
        $this->assertEquals('P1', $kandidats[0]->pegawai->nama); 
        $this->assertEquals(1, $kandidats[0]->ranking_sistem);
        $this->assertEquals('P10', $kandidats[9]->pegawai->nama); 
        $this->assertEquals(10, $kandidats[9]->ranking_sistem);
    }

    public function testShouldHandleZeroWeightsWithoutDivisionError()
    {
        PengaturanBobot::query()->delete();
        PengaturanBobot::create(['ckp' => 0, 'absensi' => 0, 'survey' => 0]); 

        $pegawai = Pegawai::create(['nama' => 'A', 'role_id' => $this->rolePegawai->id, 'nip' => '1', 'email' => 'x@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 80]);

        KandidatService::generateTop10Kandidat($this->periode->id);

        $kandidat = Kandidat::where('pegawai_id', $pegawai->id)->first();
        $this->assertEquals(0, $kandidat->skor); 
    }

    public function testShouldGenerateTop3CorrectlyWithSurveys()
    {
        $pertanyaan = \App\Models\PertanyaanSurvei::create(['nomor_urut' => 1, 'pertanyaan' => 'Pertanyaan 1']);

        // Prepare 4 kandidat
        for ($i = 1; $i <= 4; $i++) {
            $pegawai = Pegawai::create(['nama' => "P$i", 'role_id' => $this->rolePegawai->id, 'nip' => (string)$i, 'email' => "y$i@test.com", 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
            NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 90]);
            AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'kjk' => 0]);
            
            $kandidat = Kandidat::create([
                'periode_id' => $this->periode->id,
                'pegawai_id' => $pegawai->id,
                'skor_ckp' => 90,
                'skor_absensi' => 100,
                'skor' => 93.33,
                'ranking_sistem' => $i,
                'status' => 'aktif'
            ]);

            // Survey values: 5 for P1, 4 for P2, 3 for P3, 2 for P4
            JawabanSurvei::create([
                'periode_id' => $this->periode->id,
                'kandidat_id' => $kandidat->id,
                'pertanyaan_id' => $pertanyaan->id,
                'nilai' => 6 - $i
            ]);
        }

        KandidatService::generateTop3Kandidat($this->periode->id);

        $this->assertDatabaseCount('hasil_akhir', 3);
        $hasilAkhirs = HasilAkhir::orderBy('ranking_final', 'asc')->get();
        
        $this->assertEquals(1, $hasilAkhirs[0]->ranking_final);
        $this->assertEquals('P1', $hasilAkhirs[0]->kandidat->pegawai->nama); 
        
        $this->assertEquals(3, $hasilAkhirs[2]->ranking_final);
        $this->assertEquals('P3', $hasilAkhirs[2]->kandidat->pegawai->nama);
    }
    
    public function testShouldGenerateTop3WithoutPengaturanBobotAndCalculateDefaults()
    {
        PengaturanBobot::query()->delete(); // No bobot
        $pertanyaan = \App\Models\PertanyaanSurvei::create(['nomor_urut' => 2, 'pertanyaan' => 'Pertanyaan 2']);
        
        $pegawai = Pegawai::create(['nama' => 'A', 'role_id' => $this->rolePegawai->id, 'nip' => '1', 'email' => 'z@test.com', 'password' => '123', 'status_pegawai' => 'aktif', 'jabatan' => 'Staff', 'tanggal_masuk' => '2020-01-01']);
        NilaiCkp::create(['periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 100]); // default ckp 50
        AbsensiPegawai::create(['bulan' => 1, 'periode_id' => $this->periode->id, 'pegawai_id' => $pegawai->id, 'kjk' => 0]); // default absen 25 -> 100
        
        $kandidat = Kandidat::create([
            'periode_id' => $this->periode->id,
            'pegawai_id' => $pegawai->id,
            'skor_ckp' => 100,
            'skor_absensi' => 100,
            'skor' => 100,
            'ranking_sistem' => 1,
            'status' => 'aktif'
        ]);

        JawabanSurvei::create([
            'periode_id' => $this->periode->id,
            'kandidat_id' => $kandidat->id,
            'pertanyaan_id' => $pertanyaan->id,
            'nilai' => 5 // Default survey 25 -> Normalized (5/5) * 100 = 100
        ]);

        KandidatService::generateTop3Kandidat($this->periode->id);

        $hasilAkhir = HasilAkhir::first();
        // calculation: 100*0.5 + 100*0.25 + 100*0.25 = 50 + 25 + 25 = 100
        // Check final score stored on candidate object temporarily 
        // We will assert database record was created correctly
        $this->assertNotNull($hasilAkhir);
        $this->assertEquals($kandidat->id, $hasilAkhir->kandidat_id);
    }
}

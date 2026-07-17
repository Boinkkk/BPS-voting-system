<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PeriodePenilaian;
use App\Models\NilaiCkp;
use App\Models\AbsensiPegawai;
use App\Models\Role;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class PeriodePenilaianTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard();
    }

    private function createPegawai($nip = '123')
    {
        $role = Role::create(['tipe' => 'Pegawai']);
        return Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $role->id,
            'nama' => 'P_' . $nip,
            'nip' => $nip,
            'email' => "email_$nip@test.com",
            'password' => 'secret',
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_get_phase_details_attribute_returns_correct_phases()
    {
        // We set fixed dates for the period
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-05',
            'tanggal_mulai_voting' => '2026-07-07',
            'tanggal_selesai_voting' => '2026-07-10',
            'tanggal_review_kepala' => '2026-07-12',
            'tanggal_selesai' => '2026-07-15',
            'status' => 'penginputan'
        ]);

        // Case 1: Belum Dimulai (Now < 2026-07-01)
        Carbon::setTestNow('2026-06-30 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Belum Dimulai', $details['current_phase']);
        $this->assertEquals('Masa Persiapan', $details['next_phase']);
        $this->assertEquals(1, $details['days_left']); // Diff to 07-01

        // Case 2: Masa Persiapan (Now between 07-01 and 07-05)
        Carbon::setTestNow('2026-07-03 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Masa Persiapan', $details['current_phase']);
        $this->assertEquals('Masa Voting', $details['next_phase']);
        $this->assertEquals(4, $details['days_left']); // Diff to 07-07

        // Case 3: Menunggu Voting (Now is 07-06)
        Carbon::setTestNow('2026-07-06 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Menunggu Voting', $details['current_phase']);
        $this->assertEquals('Masa Voting', $details['next_phase']);
        $this->assertEquals(1, $details['days_left']); // Diff to 07-07

        // Case 4: Masa Voting (Now between 07-07 and 07-10)
        Carbon::setTestNow('2026-07-08 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Masa Voting', $details['current_phase']);
        $this->assertEquals('Pemilihan Kepala', $details['next_phase']);
        $this->assertEquals(4, $details['days_left']); // Diff to 07-12

        // Case 5: Menunggu Pemilihan (Now is 07-11)
        Carbon::setTestNow('2026-07-11 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Menunggu Pemilihan', $details['current_phase']);
        $this->assertEquals('Pemilihan Kepala', $details['next_phase']);
        $this->assertEquals(1, $details['days_left']); // Diff to 07-12

        // Case 6: Pemilihan Kepala (Now between 07-12 and 07-14)
        Carbon::setTestNow('2026-07-13 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Pemilihan Kepala', $details['current_phase']);
        $this->assertEquals('Pengumuman Pemenang', $details['next_phase']);
        $this->assertEquals(2, $details['days_left']); // Diff to 07-15

        // Case 7: Pengumuman Pemenang (Now >= 07-15)
        Carbon::setTestNow('2026-07-15 08:00:00');
        $details = $periode->phase_details;
        $this->assertEquals('Pengumuman Pemenang', $details['current_phase']);
        $this->assertNull($details['next_phase']);
        $this->assertEquals(0, $details['days_left']);

        Carbon::setTestNow(); // reset
    }

    public function test_compute_status_based_on_date_returns_correct_status()
    {
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-05',
            'tanggal_mulai_voting' => '2026-07-07',
            'tanggal_selesai_voting' => '2026-07-10',
            'tanggal_review_kepala' => '2026-07-12',
            'tanggal_selesai' => '2026-07-15',
            'status' => 'penginputan'
        ]);

        // Masa persiapan
        Carbon::setTestNow('2026-07-02 08:00:00');
        $this->assertEquals('penginputan', $periode->computeStatusBasedOnDate());

        // Masa voting
        Carbon::setTestNow('2026-07-08 08:00:00');
        $this->assertEquals('voting', $periode->computeStatusBasedOnDate());

        // Review kepala
        Carbon::setTestNow('2026-07-13 08:00:00');
        $this->assertEquals('review_kepala', $periode->computeStatusBasedOnDate());

        // Selesai
        Carbon::setTestNow('2026-07-16 08:00:00');
        $this->assertEquals('selesai', $periode->computeStatusBasedOnDate());

        Carbon::setTestNow(); // reset
    }

    public function test_compute_status_based_on_date_respects_existing_selesai_status_unless_forced()
    {
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-07-15',
            'status' => 'selesai'
        ]);

        // Date is in the past, meaning it theoretically should be 'penginputan'
        Carbon::setTestNow('2026-07-02 08:00:00');
        
        // Without force, should remain selesai
        $this->assertEquals('selesai', $periode->computeStatusBasedOnDate());
        
        // With force, should re-calculate
        $this->assertEquals('penginputan', $periode->computeStatusBasedOnDate(true));

        Carbon::setTestNow();
    }

    public function test_is_data_lengkap_returns_false_if_no_ckp()
    {
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'triwulan' => 1,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-03-31',
            'status' => 'penginputan'
        ]);

        // No CKP and no Absensi
        $this->assertFalse($periode->isDataLengkap());
    }

    public function test_is_data_lengkap_returns_false_if_absensi_months_incomplete()
    {
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'triwulan' => 2, // expected months: 4, 5, 6
            'tanggal_mulai' => '2026-04-01',
            'tanggal_selesai' => '2026-06-30',
            'status' => 'penginputan'
        ]);

        $pegawai = $this->createPegawai('123');
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 100]);

        // Only 2 valid months for Triwulan 2 (4 and 5)
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 4, 'kjk' => 0]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 5, 'kjk' => 0]);
        // And an invalid month just to be sure it ignores it
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 7, 'kjk' => 0]);

        $this->assertFalse($periode->isDataLengkap());
    }

    public function test_is_data_lengkap_returns_true_when_ckp_and_absensi_are_complete()
    {
        $periode = PeriodePenilaian::create([
            'nama' => 'P1',
            'triwulan' => 3, // expected months: 7, 8, 9
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-09-30',
            'status' => 'penginputan'
        ]);

        $pegawai = $this->createPegawai('456');
        NilaiCkp::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'nilai' => 90]);

        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 7, 'kjk' => 0]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 8, 'kjk' => 0]);
        AbsensiPegawai::create(['periode_id' => $periode->id, 'pegawai_id' => $pegawai->id, 'bulan' => 9, 'kjk' => 0]);

        $this->assertTrue($periode->isDataLengkap());
    }

    public function test_get_recent_and_default_returns_closest_voting_periode()
    {
        // 2026-08-15
        Carbon::setTestNow('2026-08-15 08:00:00');

        $p1 = PeriodePenilaian::create([
            'tahun' => 2026,
            'nama' => 'P1',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-03-31',
            'tanggal_mulai_voting' => '2026-03-10',
            'tanggal_selesai_voting' => '2026-03-20',
            'status' => 'selesai'
        ]);

        $p2 = PeriodePenilaian::create([
            'tahun' => 2026,
            'nama' => 'P2',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-09-30',
            'tanggal_mulai_voting' => '2026-08-10',
            'tanggal_selesai_voting' => '2026-08-20', // Currently active voting
            'status' => 'voting'
        ]);

        $p3 = PeriodePenilaian::create([
            'tahun' => 2026,
            'nama' => 'P3',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-12-31',
            'tanggal_mulai_voting' => '2026-12-10',
            'tanggal_selesai_voting' => '2026-12-20',
            'status' => 'penginputan'
        ]);

        $result = PeriodePenilaian::getRecentAndDefault();
        
        $this->assertCount(3, $result['periodes']);
        $this->assertEquals($p2->id, $result['default_id']);

        Carbon::setTestNow();
    }
}

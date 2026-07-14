<?php

namespace Tests\Unit\Models;

use App\Models\AbsensiPegawai;
use App\Models\PengaturanBobot;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AbsensiPegawaiTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function shouldCalculatePenaltyBasedOnTkTlPswAndKjk()
    {
        // Arrange
        Cache::shouldReceive('rememberForever')
            ->with('bobot_penalti', \Mockery::any())
            ->andReturn([
                'TL1' => 0.5,
                'TL2' => 0.5,
                'TL3' => 1.0,
                'TL4' => 2.5,
                'PSW1' => 0.5,
                'PSW2' => 0.5,
                'PSW3' => 1.0,
                'PSW4' => 2.5,
                'TK' => 2.5,
                'KJK_PER_JAM' => 0.5
            ]);

        $absensi = new AbsensiPegawai([
            'tl1' => 2, // 2 * 0.5 = 1
            'psw3' => 1, // 1 * 1.0 = 1
            'tk' => 1, // 1 * 2.5 = 2.5
            'kjk' => 120, // (120/60) * 0.5 = 1
        ]);

        // Act
        $penalti = $absensi->penalti;

        // Assert
        // Expected penalty: -1 - 1 - 2.5 - 1 = -5.5
        $this->assertEquals(-5.5, $penalti);
    }

    #[Test]
    public function shouldCalculateNilaiPresensiCorrectlyWithZeroKjk()
    {
        // Arrange
        Cache::shouldReceive('rememberForever')
            ->with('pengaturan_bobot_absensi', \Mockery::any())
            ->andReturn(null);

        $absensi = new AbsensiPegawai(['kjk' => 0]);

        // Act
        $nilai = $absensi->nilai_presensi;

        // Assert
        $this->assertEquals(100, $nilai);
    }

    #[Test]
    public function shouldCalculateNilaiPresensiCorrectlyWithPenalties()
    {
        // Arrange
        $mockBobot = new PengaturanBobot([
            'bobot_tk' => 3,
            'bobot_tl1' => 1,
            'bobot_tl2' => 1,
            'bobot_tl3' => 2,
            'bobot_tl4' => 3,
            'bobot_psw1' => 1,
            'bobot_psw2' => 1,
            'bobot_psw3' => 2,
            'bobot_psw4' => 3,
            'bobot_tl' => 0, 
            'bobot_psw' => 0, 
        ]);

        Cache::shouldReceive('rememberForever')
            ->with('pengaturan_bobot_absensi', \Mockery::any())
            ->andReturn($mockBobot);

        // KJK between 1-60 gives base score 99
        $absensi = new AbsensiPegawai([
            'kjk' => 30, // Base: 99
            'tk' => 1,   // -3
            'tl1' => 2,  // -2
            'psw3' => 1, // -2
        ]);

        // Act
        $nilai = $absensi->nilai_presensi;

        // Assert
        // 99 - 3 - 2 - 2 = 92
        $this->assertEquals(92, $nilai);
    }

    #[Test]
    public function shouldCalculateNilaiPresensiCorrectlyWithLegacyPenalties()
    {
        // Arrange
        $mockBobot = new PengaturanBobot([
            'bobot_tk' => 2,
            'bobot_tl1' => 0,
            'bobot_tl2' => 0,
            'bobot_tl3' => 0,
            'bobot_tl4' => 0,
            'bobot_psw1' => 0,
            'bobot_psw2' => 0,
            'bobot_psw3' => 0,
            'bobot_psw4' => 0,
            'bobot_tl' => 5, 
            'bobot_psw' => 5, 
        ]);

        Cache::shouldReceive('rememberForever')
            ->with('pengaturan_bobot_absensi', \Mockery::any())
            ->andReturn($mockBobot);

        // KJK > 450 gives base score 96
        $absensi = new AbsensiPegawai([
            'kjk' => 500, // Base: 96
            'tk' => 2,   // -4
            'tl' => 1,  // -5
            'psw' => 1, // -5
        ]);

        // Act
        $nilai = $absensi->nilai_presensi;

        // Assert
        // 96 - 4 - 5 - 5 = 82
        $this->assertEquals(82, $nilai);
    }
    
    #[Test]
    public function shouldNotReturnNegativeNilaiPresensi()
    {
        // Arrange
        $mockBobot = new PengaturanBobot([
            'bobot_tk' => 50,
            'bobot_tl' => 0,
            'bobot_tl1' => 0,
            'bobot_tl2' => 0,
            'bobot_tl3' => 0,
            'bobot_tl4' => 0,
            'bobot_psw' => 0,
            'bobot_psw1' => 0,
            'bobot_psw2' => 0,
            'bobot_psw3' => 0,
            'bobot_psw4' => 0,
        ]);

        Cache::shouldReceive('rememberForever')
            ->with('pengaturan_bobot_absensi', \Mockery::any())
            ->andReturn($mockBobot);

        $absensi = new AbsensiPegawai([
            'kjk' => 0,  // Base: 100
            'tk' => 5,   // -250
        ]);

        // Act
        $nilai = $absensi->nilai_presensi;

        // Assert
        // Expected: 0, not -150
        $this->assertEquals(0, $nilai);
    }
}

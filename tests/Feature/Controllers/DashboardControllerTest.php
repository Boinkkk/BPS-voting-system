<?php

namespace Tests\Feature\Controllers;

use App\Models\Pegawai;
use App\Models\Pengumuman;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private $pegawai;

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
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_dashboard_renders_successfully()
    {
        $response = $this->actingAs($this->pegawai)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_dashboard_shows_announcements_and_auto_reads_them()
    {
        // Buat pengumuman sticky dan regular
        $sticky = Pengumuman::create([
            'judul' => 'Sticky Announcement',
            'konten' => 'Content',
            'kategori' => 'Umum',
            'status' => 'Published',
            'prioritas' => 'Medium',
            'is_sticky' => true,
            'is_popup' => false,
            'target' => null,
        ]);

        $regular = Pengumuman::create([
            'judul' => 'Regular Announcement',
            'konten' => 'Content',
            'kategori' => 'Umum',
            'status' => 'Published',
            'prioritas' => 'Low',
            'is_sticky' => false,
            'is_popup' => false,
            'target' => null,
        ]);

        // Belum dibaca
        $this->assertDatabaseMissing('pengumuman_reads', [
            'pengumuman_id' => $sticky->id,
            'pegawai_id' => $this->pegawai->id,
        ]);

        $response = $this->actingAs($this->pegawai)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stickyPengumumans');
        $response->assertViewHas('regularPengumumans');

        // Pastikan setelah load dashboard, otomatis masuk ke read
        $this->assertDatabaseHas('pengumuman_reads', [
            'pengumuman_id' => $sticky->id,
            'pegawai_id' => $this->pegawai->id,
        ]);
        $this->assertDatabaseHas('pengumuman_reads', [
            'pengumuman_id' => $regular->id,
            'pegawai_id' => $this->pegawai->id,
        ]);
    }

    public function test_dashboard_shows_active_periode_and_quorum_warning()
    {
        Carbon::setTestNow('2026-06-15 10:00:00');

        $periode = PeriodePenilaian::create([
            'triwulan' => 2,
            'tahun' => 2026,
            'nama' => 'Triwulan 2 2026',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai_persiapan' => '2026-06-05',
            'tanggal_mulai_voting' => '2026-06-10',
            'tanggal_selesai_voting' => '2026-06-20',
            'tanggal_review_kepala' => '2026-06-25',
            'tanggal_selesai' => '2026-06-30',
            'status' => 'voting',
        ]);

        $response = $this->actingAs($this->pegawai)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('activePeriode', function ($viewPeriode) use ($periode) {
            return $viewPeriode->id === $periode->id;
        });

        // Quorum warning should be true since percentVoting is 0 (< 50%) and status is 'voting'
        $response->assertViewHas('quorumWarning', true);
    }
}

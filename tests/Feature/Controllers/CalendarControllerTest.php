<?php

namespace Tests\Feature\Controllers;

use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    private $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePegawai = Role::create(['tipe' => 'Pegawai']);

        $this->pegawai = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $rolePegawai->id,
            'nama' => 'Pegawai Test',
            'nip' => '33333333',
            'email' => 'pegawai@test.com',
            'password' => bcrypt('password123'),
            'jabatan' => 'Staff',
            'tanggal_masuk' => '2010-01-01',
            'status_pegawai' => 'aktif',
        ]);

        PeriodePenilaian::create([
            'triwulan' => 3,
            'tahun' => 2026,
            'nama' => 'Triwulan 3 2026',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai_persiapan' => '2026-07-05',
            'tanggal_mulai_voting' => '2026-07-06',
            'tanggal_selesai_voting' => '2026-07-15',
            'tanggal_review_kepala' => '2026-07-16',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'voting',
        ]);
    }

    public function test_pegawai_can_view_calendar_index()
    {
        $response = $this->actingAs($this->pegawai)->get(route('kalender', [
            'month' => 7,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('kalender.index');
        $response->assertViewHasAll(['selectedMonth', 'selectedYear', 'periodesInMonth']);

        $response->assertSee('Triwulan 3 2026');
    }

    public function test_calendar_index_without_params_uses_current_month_and_year()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $response = $this->actingAs($this->pegawai)->get(route('kalender'));

        $response->assertStatus(200);
        $response->assertViewHas('selectedMonth', $currentMonth);
        $response->assertViewHas('selectedYear', $currentYear);
    }
}

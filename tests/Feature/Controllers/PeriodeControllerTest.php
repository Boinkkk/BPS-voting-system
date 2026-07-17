<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PeriodePenilaian;
use App\Models\Role;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PeriodeControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $roleAdmin = Role::create(['tipe' => 'Admin']);
        
        $this->admin = Pegawai::create([
            'id' => (string) Str::uuid(),
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Test',
            'nip' => '12345',
            'email' => 'admin@test.com',
            'password' => 'secret',
            'jabatan' => 'Admin',
            'tanggal_masuk' => '2020-01-01',
            'status_pegawai' => 'aktif',
        ]);
    }

    public function test_index_auto_creates_four_quarters_for_current_year()
    {
        $currentYear = date('Y');
        
        $this->assertEquals(0, PeriodePenilaian::where('tahun', $currentYear)->count());

        $this->actingAs($this->admin)
             ->get(route('admin.periode.index'))
             ->assertStatus(200)
             ->assertViewHas('periodes');

        // Verify 4 quarters are created
        $this->assertEquals(4, PeriodePenilaian::where('tahun', $currentYear)->count());
        $this->assertDatabaseHas('periode_penilaian', ['tahun' => $currentYear, 'triwulan' => 1]);
        $this->assertDatabaseHas('periode_penilaian', ['tahun' => $currentYear, 'triwulan' => 4]);
    }

    public function test_store_validates_dates_and_computes_status()
    {
        Carbon::setTestNow('2026-06-15 10:00:00');

        $payload = [
            'triwulan' => 2,
            'tahun' => 2026,
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai_persiapan' => '2026-06-05',
            'tanggal_mulai_voting' => '2026-06-10',
            'tanggal_selesai_voting' => '2026-06-20',
            'tanggal_review_kepala' => '2026-06-25',
            'tanggal_selesai' => '2026-06-30',
        ];

        $this->actingAs($this->admin)
             ->post(route('admin.periode.store'), $payload)
             ->assertRedirect(route('admin.periode.index'))
             ->assertSessionHas('success');

        // Verify it was created and status is correctly computed
        $periode = PeriodePenilaian::where('tahun', 2026)->where('triwulan', 2)->first();
        $this->assertNotNull($periode);
        $this->assertEquals('voting', $periode->status); // Because test now is 2026-06-15, which is between voting start/end
    }

    public function test_store_fails_when_dates_overlap()
    {
        $payload = [
            'triwulan' => 2,
            'tahun' => 2026,
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai_persiapan' => '2026-05-01', // INVALID: before mulai
            'tanggal_mulai_voting' => '2026-06-10',
            'tanggal_selesai_voting' => '2026-06-20',
            'tanggal_review_kepala' => '2026-06-25',
            'tanggal_selesai' => '2026-06-30',
        ];

        $this->actingAs($this->admin)
             ->post(route('admin.periode.store'), $payload)
             ->assertSessionHasErrors(['tanggal_selesai_persiapan']);
    }

    public function test_update_modifies_periode_and_triggers_generate_top3()
    {
        Carbon::setTestNow('2026-06-26 10:00:00'); // Between review_kepala and selesai

        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'voting' // initial status
        ]);

        // Mocking the KandidatAdminController to avoid needing full DB setup for generateTop3
        $this->mock(\App\Http\Controllers\KandidatAdminController::class, function ($mock) use ($periode) {
            $mock->shouldReceive('generateTop3ForPeriode')
                 ->once()
                 ->with($periode->id);
        });

        $payload = [
            'triwulan' => 1,
            'tahun' => 2026,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-06-25', // Adjust so testNow falls into review_kepala
            'tanggal_selesai' => '2026-06-30',
        ];

        $this->actingAs($this->admin)
             ->put(route('admin.periode.update', $periode->id), $payload)
             ->assertRedirect(route('admin.periode.index'))
             ->assertSessionHas('success');

        $periode->refresh();
        $this->assertEquals('review_kepala', $periode->status);
    }

    public function test_destroy_deletes_periode()
    {
        $periode = PeriodePenilaian::create([
            'triwulan' => 1,
            'tahun' => 2026,
            'nama' => 'Triwulan 1',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai_persiapan' => '2026-01-05',
            'tanggal_mulai_voting' => '2026-01-06',
            'tanggal_selesai_voting' => '2026-01-10',
            'tanggal_review_kepala' => '2026-01-11',
            'tanggal_selesai' => '2026-01-15',
            'status' => 'penginputan'
        ]);

        $this->actingAs($this->admin)
             ->delete(route('admin.periode.destroy', $periode->id))
             ->assertRedirect(route('admin.periode.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseMissing('periode_penilaian', ['id' => $periode->id]);
    }
}

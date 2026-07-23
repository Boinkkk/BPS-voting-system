# Graph Report - c:/BPS/voting/web  (2026-07-23)

## Corpus Check
- 42 files · ~135,222 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1198 nodes · 1829 edges · 169 communities (121 shown, 48 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 248 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Presensi Januari 2026
- Presensi Februari 2026
- Presensi Maret 2026
- Presensi April 2026
- Presensi Mei 2026
- Presensi Juni 2026
- Database Migrations
- Laravel Best Practices
- Graphify Documentation
- Testing Rules
- Notification Mail Events
- Queued Mail Events
- Form Requests Rules
- Profile Images
- HTML View Bugs
- Add Watch Docs
- Graphify Exports
- Git Commit Hooks
- Event Discovery
- HTTP Client
- Migrations
- Queue Jobs
- Scheduling Rules
- Mass Assignment Security
- Code Style
- Workflow Config
- GitHub Workflows
- Laravel Boost
- Pengaturan Bobot Migration
- Pengumuman Migration
- Logo Resource
- BPS Readme
- Login Admin Test
- Login Kepala Kantor
- Login Kepala Umum
- Login Pegawai
- Logout Test
- Screenshots Util
- Illuminate\Database\Eloquent\Model
- Kandidat
- PeriodePenilaian
- Pengumuman
- Pegawai
- scripts
- Glosarium
- PertanyaanSurvei
- User.php
- Illuminate\Database\Seeder
- AbsensiDataSheet
- 2026_07_06_084234_create_pegawai_table.php
- 2026_07_06_084235_create_periode_penilaian_table.php
- 2026_07_06_084236_create_kriteria_penilaian_table.php
- 2026_07_06_084237_create_kinerja_pegawai_table.php
- 2026_07_06_084238_create_absensi_pegawai_table.php
- 2026_07_06_084239_create_survei_table.php
- 2026_07_06_084240_create_pertanyaan_survei_table.php
- 2026_07_06_084241_create_kandidat_table.php
- 2026_07_06_084242_create_voting_session_table.php
- 2026_07_06_084243_create_jawaban_survei_table.php
- 2026_07_06_084245_create_hasil_akhir_table.php
- 2026_07_06_084246_create_views_kandidat_progress_table.php
- 2026_07_06_084247_create_bobot_penaltis_table.php
- 2026_07_06_084248_create_tim_penilais_table.php
- 2026_07_06_084249_create_survey_progress_table.php
- 2026_07_06_084250_create_nilai_ckp_table.php
- 2026_07_06_084251_create_pengaturan_bobots_table.php
- 2026_07_06_084252_create_glosaria_table.php
- 2026_07_06_084253_create_pengumuman_table.php
- 2026_07_06_084254_create_pengumuman_reads_table.php
- 2026_07_21_040031_create_faqs_table.php
- playwright.config.ts
- ExampleTest
- queue.php
- services.php
- get_failed.php
- inject_css.py
- index.php
- app.js
- audit-log/index.blade.php
- ckp/index.blade.php
- Community 165

## God Nodes (most connected - your core abstractions)
1. `TestCase` - 50 edges
2. `Rekap Presensi Januari 2026` - 47 edges
3. `Rekap Presensi Februari 2026` - 47 edges
4. `Rekap Presensi Maret 2026` - 47 edges
5. `Rekap Presensi April 2026` - 47 edges
6. `Rekap Presensi Mei 2026` - 47 edges
7. `Rekap Presensi Juni 2026` - 47 edges
8. `Pegawai` - 44 edges
9. `PeriodePenilaian` - 43 edges
10. `Role` - 34 edges

## Surprising Connections (you probably didn't know these)
- `PHPUnit Core Rules` --semantically_similar_to--> `Factory States and Sequences`  [INFERRED] [semantically similar]
  AGENTS.md → .agents/skills/laravel-best-practices/rules/testing.md
- `PHPUnit Core Rules` --conceptually_related_to--> `PHPUnit Test Coverage Passed`  [INFERRED]
  AGENTS.md → coverage.txt
- `Undefined variable $errors in View` --conceptually_related_to--> `To-Do List Bugs`  [INFERRED]
  test_view.html → to-do-list.md
- `Excel Facade Missing Error` --references--> `maatwebsite/excel`  [INFERRED]
  error.txt → README.md
- `ExcelServiceProvider Missing Error` --references--> `maatwebsite/excel`  [INFERRED]
  error.txt → README.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Role Base** — readme_admin, readme_pegawai, readme_tim_penilai, readme_kepala_bps [EXTRACTED 1.00]
- **Siklus Pemilihan** — readme_fase_penginputan, readme_fase_survei, readme_fase_voting, readme_fase_review [EXTRACTED 1.00]
- **Excel Package Errors** — error_phpspreadsheet_missing, error_excel_service_provider_missing, error_excel_facade_missing [INFERRED 0.85]
- **BPS Core Entities** — migrations_dump_pegawai, migrations_dump_periode_penilaian, migrations_dump_kinerja_pegawai, migrations_dump_absensi_pegawai, migrations_dump_kandidat, migrations_dump_hasil_akhir [EXTRACTED 1.00]
- **BPS Voting Entities** — migrations_dump_survei, migrations_dump_pertanyaan_survei, migrations_dump_kandidat, migrations_dump_voting_session, migrations_dump_jawaban_survei [EXTRACTED 1.00]
- **Keterangan Kode Presensi** — data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_ht, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_tl, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_psw, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_kjk, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_ct, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cs, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cm, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cp, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cb, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_pd, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_tl_luar, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_tb, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_dk, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_kn [EXTRACTED 1.00]
- **Daftar Pegawai** — data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_ach__haris_sidik, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mohammad_sakir, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_aris_kuswantoro, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_tedy_wahyudi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_zhoemaroh, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_yeni_arisanti, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_tulus_soebagijo, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_hendra_adhikara, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mohammad_soleh_tc, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_dwi_widianis, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_hizbullah_gunawan, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_dhony_susfantori, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_tatok_mulyo_mintartok, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_heru_priambodo, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_ridnu_witardi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_erlisa_wahyu_pratiwi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_whistra_pariata_utama, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mohlis, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_fajar_fatahillah, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_istian_hendriyanto, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_citra_dian_etika, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_muhammad_sayyidin_syadad, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_nia_nurma_faiza, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_afifan_ainur_rofiq, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_radita_nareswari_mumpuni_putri, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_indah_putri_rahayu, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_linda_kuncasari, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_alfin_niam_habibi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_anggraini_nur_agustina, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_akhmad_santoso, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mega_citranda_utami, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_yevi_tania [EXTRACTED 1.00]
- **Keterangan Kode Presensi** — data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_ht, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_tl, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_psw, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_kjk, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_ct, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cs, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cm, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cp, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cb, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_pd, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_tl_luar, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_tb, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_dk, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_kn [EXTRACTED 1.00]

## Communities (169 total, 48 thin omitted)

### Community 0 - "Presensi Januari 2026"
Cohesion: 0.06
Nodes (22): SeedTestVotes, BobotPenalti, Faq, HasilAkhir, JawabanSurvei, KinerjaPegawai, LogOptions, SurveyProgress (+14 more)

### Community 1 - "Presensi Februari 2026"
Cohesion: 0.05
Nodes (18): AbsensiPegawai, LogOptions, Departemen, Kandidat, NilaiCkp, LogOptions, Pegawai, PengaturanBobot (+10 more)

### Community 2 - "Presensi Maret 2026"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 3 - "Presensi April 2026"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 4 - "Presensi Mei 2026"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 5 - "Presensi Juni 2026"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 6 - "Database Migrations"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 7 - "Laravel Best Practices"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 8 - "Graphify Documentation"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 9 - "Testing Rules"
Cohesion: 0.05
Nodes (39): concurrently, cropperjs, dotenv, @fontsource/hanken-grotesk, @fontsource/plus-jakarta-sans, gsap, laravel-vite-plugin, dependencies (+31 more)

### Community 10 - "Notification Mail Events"
Cohesion: 0.10
Nodes (5): PengumumanController, Pengumuman, PengumumanRead, DashboardControllerTest, PengumumanControllerTest

### Community 11 - "Queued Mail Events"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 12 - "Form Requests Rules"
Cohesion: 0.12
Nodes (4): GlosariumAdminController, GlosariumController, Glosarium, GlosariumAdminControllerTest

### Community 13 - "Profile Images"
Cohesion: 0.15
Nodes (4): SurveyAdminController, PertanyaanSurvei, SurveyAdminControllerTest, SurveyPegawaiControllerTest

### Community 14 - "HTML View Bugs"
Cohesion: 0.19
Nodes (8): AutoUpdatePeriodeStatus, CheckRoleAdmin, CheckRoleAdminOrKepalaUmum, CheckRoleKepala, SecurityHeaders, SetTestTime, Closure, Symfony\Component\HttpFoundation\Response

### Community 15 - "Add Watch Docs"
Cohesion: 0.13
Nodes (8): User, TelescopeServiceProvider, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Telescope\TelescopeApplicationServiceProvider, static

### Community 16 - "Graphify Exports"
Cohesion: 0.16
Nodes (5): CkpController, DevTimeController, KandidatAdminController, PeriodeController, Illuminate\Http\Request

### Community 17 - "Git Commit Hooks"
Cohesion: 0.16
Nodes (3): PeriodePenilaian, PeriodeControllerTest, PeriodePenilaianTest

### Community 18 - "Event Discovery"
Cohesion: 0.15
Nodes (7): BobotPenaltiSeeder, DatabaseSeeder, E2ESeeder, GlosariumSeeder, PertanyaanSurveiSeeder, TipeAbsenSeeder, Illuminate\Database\Seeder

### Community 19 - "HTTP Client"
Cohesion: 0.12
Nodes (18): Excel Facade Missing Error, ExcelServiceProvider Missing Error, PhpSpreadsheet Missing Error, Admin, Fase Penginputan, Fase Review, Fase Survei, Fase Voting (+10 more)

### Community 20 - "Migrations"
Cohesion: 0.19
Nodes (7): AbsensiDataSheet, AbsensiKamusSheet, Maatwebsite\Excel\Concerns\FromCollection, Maatwebsite\Excel\Concerns\WithCustomStartCell, Maatwebsite\Excel\Concerns\WithEvents, Maatwebsite\Excel\Concerns\WithHeadings, Maatwebsite\Excel\Concerns\WithTitle

### Community 21 - "Queue Jobs"
Cohesion: 0.14
Nodes (5): AbsensiAdminController, AuditLogController, KepalaController, SurveyPegawaiController, Controller

### Community 22 - "Scheduling Rules"
Cohesion: 0.13
Nodes (5): CalendarController, Controller, FaqController, MonitoringSurveiController, PengaturanBobotController

### Community 23 - "Mass Assignment Security"
Cohesion: 0.14
Nodes (3): AuthController, DashboardController, ProfileController

### Community 24 - "Code Style"
Cohesion: 0.19
Nodes (4): CreateActivityLogTable, AddEventColumnToActivityLogTable, AddBatchUuidColumnToActivityLogTable, Illuminate\Database\Migrations\Migration

### Community 25 - "Workflow Config"
Cohesion: 0.24
Nodes (13): Absensi Pegawai Table, Hasil Akhir Table, Jawaban Survei Table, Kandidat Table, Kinerja Pegawai Table, Kriteria Penilaian Table, Nilai CKP Table, Pegawai Table (+5 more)

### Community 27 - "Laravel Boost"
Cohesion: 0.26
Nodes (5): AbsensiImport, KinerjaImport, Illuminate\Support\Collection, Maatwebsite\Excel\Concerns\ToCollection, Maatwebsite\Excel\Concerns\WithStartRow

### Community 28 - "Pengaturan Bobot Migration"
Cohesion: 0.20
Nodes (7): addSelect() Subqueries, Action Classes, Blade Components, Eager Loading, Local Scopes, Exception Rendering, laravel-best-practices

### Community 32 - "Login Admin Test"
Cohesion: 0.29
Nodes (7): Graphify Agent Rule, Extraction Subagent Prompt, GitHub Clone, graphify query, Transcribe Video, --update flag, graphify

### Community 33 - "Login Kepala Kantor"
Cohesion: 0.52
Nodes (5): loginAdmin(), loginKepalaKantor(), loginKepalaUmum(), loginPegawai(), logout()

### Community 42 - "Pegawai"
Cohesion: 0.60
Nodes (3): CkpImport, Maatwebsite\Excel\Concerns\ToModel, Maatwebsite\Excel\Concerns\WithHeadingRow

### Community 46 - "User.php"
Cohesion: 0.83
Nodes (3): down(), getConnection(), up()

### Community 48 - "AbsensiDataSheet"
Cohesion: 0.67
Nodes (3): Factory States and Sequences, PHPUnit Core Rules, PHPUnit Test Coverage Passed

## Knowledge Gaps
- **423 isolated node(s):** `__filename`, `__dirname`, `components.calendar-grid`, `Graphify Agent Rule`, `/graphify add` (+418 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **48 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Pegawai` connect `Presensi Februari 2026` to `Presensi Januari 2026`, `Login Kepala Umum`, `Login Pegawai`, `Logout Test`, `Screenshots Util`, `Kandidat`, `Notification Mail Events`, `Form Requests Rules`, `Profile Images`, `Add Watch Docs`, `Git Commit Hooks`, `Scheduling Rules`, `Laravel Boost`, `Pengumuman Migration`, `BPS Readme`?**
  _High betweenness centrality (0.022) - this node is a cross-community bridge._
- **Why does `TestCase` connect `Presensi Januari 2026` to `Presensi Februari 2026`, `Login Kepala Umum`, `Login Pegawai`, `Logout Test`, `Screenshots Util`, `Illuminate\Database\Eloquent\Model`, `Kandidat`, `Notification Mail Events`, `Form Requests Rules`, `Glosarium`, `Profile Images`, `Git Commit Hooks`, `BPS Readme`?**
  _High betweenness centrality (0.018) - this node is a cross-community bridge._
- **Why does `Pengumuman` connect `Notification Mail Events` to `Presensi Januari 2026`, `Mass Assignment Security`?**
  _High betweenness centrality (0.011) - this node is a cross-community bridge._
- **What connects `__filename`, `__dirname`, `components.calendar-grid` to the rest of the system?**
  _423 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Presensi Januari 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.061541448262473306 - nodes in this community are weakly interconnected._
- **Should `Presensi Februari 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.051189400782896716 - nodes in this community are weakly interconnected._
- **Should `Presensi Maret 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
# Graph Report - c:/BPS/voting/web  (2026-07-21)

## Corpus Check
- 259 files · ~105,391 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 354 nodes · 323 edges · 38 communities (10 shown, 28 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 6 edges (avg confidence: 0.88)
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

## God Nodes (most connected - your core abstractions)
1. `Rekap Presensi Januari 2026` - 47 edges
2. `Rekap Presensi Februari 2026` - 47 edges
3. `Rekap Presensi Maret 2026` - 47 edges
4. `Rekap Presensi April 2026` - 47 edges
5. `Rekap Presensi Mei 2026` - 47 edges
6. `Rekap Presensi Juni 2026` - 47 edges
7. `laravel-best-practices` - 9 edges
8. `Periode Penilaian Table` - 8 edges
9. `graphify` - 6 edges
10. `Pegawai Table` - 6 edges

## Surprising Connections (you probably didn't know these)
- `PHPUnit Core Rules` --semantically_similar_to--> `Factory States and Sequences`  [INFERRED] [semantically similar]
  AGENTS.md → .agents/skills/laravel-best-practices/rules/testing.md
- `PHPUnit Core Rules` --conceptually_related_to--> `PHPUnit Test Coverage Passed`  [INFERRED]
  AGENTS.md → coverage.txt
- `Undefined variable $errors in View` --conceptually_related_to--> `To-Do List Bugs`  [INFERRED]
  test_view.html → to-do-list.md
- `ShouldQueue for Notifications` --semantically_similar_to--> `ShouldQueue for Mailables`  [INFERRED] [semantically similar]
  .agents/skills/laravel-best-practices/rules/events-notifications.md → .agents/skills/laravel-best-practices/rules/mail.md
- `afterCommit for Notifications` --semantically_similar_to--> `afterCommit for Mailables`  [INFERRED] [semantically similar]
  .agents/skills/laravel-best-practices/rules/events-notifications.md → .agents/skills/laravel-best-practices/rules/mail.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **BPS Core Entities** — migrations_dump_pegawai, migrations_dump_periode_penilaian, migrations_dump_kinerja_pegawai, migrations_dump_absensi_pegawai, migrations_dump_kandidat, migrations_dump_hasil_akhir [EXTRACTED 1.00]
- **BPS Voting Entities** — migrations_dump_survei, migrations_dump_pertanyaan_survei, migrations_dump_kandidat, migrations_dump_voting_session, migrations_dump_jawaban_survei [EXTRACTED 1.00]
- **Keterangan Kode Presensi** — data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_ht, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_tl, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_psw, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_kjk, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_ct, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cs, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cm, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cp, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_cb, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_pd, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_tl_luar, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_tb, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_dk, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_kode_kn [EXTRACTED 1.00]
- **Daftar Pegawai** — data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_ach__haris_sidik, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mohammad_sakir, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_aris_kuswantoro, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_tedy_wahyudi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_zhoemaroh, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_yeni_arisanti, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_tulus_soebagijo, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_hendra_adhikara, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mohammad_soleh_tc, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_dwi_widianis, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_hizbullah_gunawan, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_dhony_susfantori, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_tatok_mulyo_mintartok, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_heru_priambodo, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_ridnu_witardi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_erlisa_wahyu_pratiwi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_whistra_pariata_utama, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mohlis, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_fajar_fatahillah, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_istian_hendriyanto, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_citra_dian_etika, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_muhammad_sayyidin_syadad, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_nia_nurma_faiza, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_afifan_ainur_rofiq, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_radita_nareswari_mumpuni_putri, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_indah_putri_rahayu, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_linda_kuncasari, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_alfin_niam_habibi, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_anggraini_nur_agustina, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_akhmad_santoso, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_mega_citranda_utami, data_rekap_presensi_kab__bangkalan_01_rekap_presensi_januari_2026_yevi_tania [EXTRACTED 1.00]
- **Keterangan Kode Presensi** — data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_ht, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_tl, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_psw, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_kjk, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_ct, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cs, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cm, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cp, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_cb, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_pd, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_tl_luar, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_tb, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_dk, data_rekap_presensi_kab__bangkalan_02_rekap_presensi_februari_2026_kode_kn [EXTRACTED 1.00]

## Communities (38 total, 28 thin omitted)

### Community 0 - "Presensi Januari 2026"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

### Community 1 - "Presensi Februari 2026"
Cohesion: 0.04
Nodes (48): Ach. Haris Sidik, Afifan Ainur Rofiq, Akhmad Santoso, Alfin Niam Habibi, Anggraini Nur Agustina, Aris Kuswantoro, BPS KAB. BANGKALAN, Citra Dian Etika (+40 more)

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
Cohesion: 0.24
Nodes (13): Absensi Pegawai Table, Hasil Akhir Table, Jawaban Survei Table, Kandidat Table, Kinerja Pegawai Table, Kriteria Penilaian Table, Nilai CKP Table, Pegawai Table (+5 more)

### Community 7 - "Laravel Best Practices"
Cohesion: 0.20
Nodes (7): addSelect() Subqueries, Action Classes, Blade Components, Eager Loading, Local Scopes, Exception Rendering, laravel-best-practices

### Community 8 - "Graphify Documentation"
Cohesion: 0.29
Nodes (7): Graphify Agent Rule, Extraction Subagent Prompt, GitHub Clone, graphify query, Transcribe Video, --update flag, graphify

### Community 9 - "Testing Rules"
Cohesion: 0.67
Nodes (3): Factory States and Sequences, PHPUnit Core Rules, PHPUnit Test Coverage Passed

## Knowledge Gaps
- **330 isolated node(s):** `loginAdmin`, `loginKepalaUmum`, `loginKepalaKantor`, `loginPegawai`, `logout` (+325 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **28 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What connects `loginAdmin`, `loginKepalaUmum`, `loginKepalaKantor` to the rest of the system?**
  _330 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Presensi Januari 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Should `Presensi Februari 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Should `Presensi Maret 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Should `Presensi April 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Should `Presensi Mei 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Should `Presensi Juni 2026` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
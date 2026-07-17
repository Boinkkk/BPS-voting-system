<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Glosarium;

class GlosariumController extends Controller
{
    public function index()
    {
        $glosariums = Glosarium::orderBy('istilah', 'asc')->get();
        
        $pengaturan = \App\Models\PengaturanBobot::first();
        if ($pengaturan) {
            $bobotKeseluruhanDef = "Bobot Keseluruhan Penilaian ditentukan oleh:\n" . 
                                   "- Capaian Kinerja Pegawai (CKP): {$pengaturan->ckp}%\n" .
                                   "- Nilai Absensi: {$pengaturan->absensi}%\n" .
                                   "- Survei Kepuasan: {$pengaturan->survey}%";
                                   
            $bobotAbsensiDef = "Perhitungan Nilai Absensi dimulai dengan Base Score berdasarkan KJK (Kekurangan Jam Kerja):\n" .
                               "- KJK 0 = 100\n" .
                               "- KJK 1-60 = 99\n" .
                               "- KJK 61-120 = 98\n" .
                               "- KJK 121-450 = 97\n" .
                               "- KJK > 450 = 96\n\n" .
                               "Selanjutnya Base Score tersebut dikurangi oleh faktor penalti berikut:\n\n";
                               
            if ($pengaturan->bobot_psw1 == 0 && $pengaturan->bobot_psw2 == 0 && $pengaturan->bobot_psw3 == 0 && $pengaturan->bobot_psw4 == 0) {
                $bobotAbsensiDef .= "- PSW (Pulang Sebelum Waktu): Penilaian PSW diambil dari nilai total PSW keseluruhan yang dikalikan dengan bobot {$pengaturan->bobot_psw} per kejadian.\n";
            } else {
                $bobotAbsensiDef .= "- PSW (Pulang Sebelum Waktu): Penilaian PSW dibagi menjadi beberapa tingkat:\n" .
                                    "  - PSW 1: Bobot {$pengaturan->bobot_psw1}\n" .
                                    "  - PSW 2: Bobot {$pengaturan->bobot_psw2}\n" .
                                    "  - PSW 3: Bobot {$pengaturan->bobot_psw3}\n" .
                                    "  - PSW 4: Bobot {$pengaturan->bobot_psw4}\n";
            }
            
            if ($pengaturan->bobot_tl1 == 0 && $pengaturan->bobot_tl2 == 0 && $pengaturan->bobot_tl3 == 0 && $pengaturan->bobot_tl4 == 0) {
                $bobotAbsensiDef .= "- TL (Terlambat): Penilaian TL diambil dari nilai total TL keseluruhan yang dikalikan dengan bobot {$pengaturan->bobot_tl} per kejadian.\n";
            } else {
                $bobotAbsensiDef .= "- TL (Terlambat): Penilaian TL dibagi menjadi beberapa tingkat:\n" .
                                    "  - TL 1: Bobot {$pengaturan->bobot_tl1}\n" .
                                    "  - TL 2: Bobot {$pengaturan->bobot_tl2}\n" .
                                    "  - TL 3: Bobot {$pengaturan->bobot_tl3}\n" .
                                    "  - TL 4: Bobot {$pengaturan->bobot_tl4}\n";
            }
            
            $bobotAbsensiDef .= "- TK (Tanpa Keterangan): Dikurangi berdasarkan total TK dikalikan dengan bobot {$pengaturan->bobot_tk} per kejadian.";
            
            $dynamicItem1 = new \stdClass();
            $dynamicItem1->istilah = "Bobot Keseluruhan Penilaian (Sistem)";
            $dynamicItem1->definisi = $bobotKeseluruhanDef;
            
            $dynamicItem2 = new \stdClass();
            $dynamicItem2->istilah = "Bobot Absensi dan Base Score (Sistem)";
            $dynamicItem2->definisi = $bobotAbsensiDef;

            $glosariums->push($dynamicItem1);
            $glosariums->push($dynamicItem2);
            
            $glosariums = $glosariums->sortBy('istilah')->values();
        }

        return view('glosarium.index', compact('glosariums'));
    }
}

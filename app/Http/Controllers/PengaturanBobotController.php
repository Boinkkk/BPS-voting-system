<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengaturanBobot;

class PengaturanBobotController extends Controller
{
    public function index()
    {
        $bobot = PengaturanBobot::first();
        if (!$bobot) {
            $bobot = PengaturanBobot::create([
                'ckp' => 50,
                'absensi' => 25,
                'survey' => 25,
            ]);
        }
        return view('admin.pengaturan-bobot.index', compact('bobot'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ckp' => 'required|numeric|min:0|max:100',
            'absensi' => 'required|numeric|min:0|max:100',
            'survey' => 'required|numeric|min:0|max:100',
            'bobot_ht' => 'required|numeric|min:0',
            'bobot_psw' => 'required|numeric|min:0',
            'bobot_psw1' => 'required|numeric|min:0',
            'bobot_psw2' => 'required|numeric|min:0',
            'bobot_psw3' => 'required|numeric|min:0',
            'bobot_psw4' => 'required|numeric|min:0',
            'bobot_tl' => 'required|numeric|min:0',
            'bobot_tl1' => 'required|numeric|min:0',
            'bobot_tl2' => 'required|numeric|min:0',
            'bobot_tl3' => 'required|numeric|min:0',
            'bobot_tl4' => 'required|numeric|min:0',
            'bobot_tk' => 'required|numeric|min:0',
        ]);

        $total = $request->ckp + $request->absensi + $request->survey;
        if ($total != 100) {
            return redirect()->back()->with('error', 'Total bobot CKP, Absensi, dan Survei harus tepat 100%. Saat ini totalnya adalah ' . $total . '%.');
        }

        $bobot = PengaturanBobot::first();
        $data = $request->only([
            'ckp', 'absensi', 'survey',
            'bobot_ht', 'bobot_psw', 'bobot_psw1', 'bobot_psw2', 'bobot_psw3', 'bobot_psw4',
            'bobot_tl', 'bobot_tl1', 'bobot_tl2', 'bobot_tl3', 'bobot_tl4', 'bobot_tk'
        ]);

        if (!$bobot) {
            PengaturanBobot::create($data);
        } else {
            $bobot->update($data);
        }

        \Illuminate\Support\Facades\Cache::forget('pengaturan_bobot_absensi');

        return redirect()->back()->with('success', 'Pengaturan bobot berhasil diperbarui!');
    }
}

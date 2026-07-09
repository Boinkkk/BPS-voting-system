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
        ]);

        $total = $request->ckp + $request->absensi + $request->survey;
        if ($total != 100) {
            return redirect()->back()->with('error', 'Total bobot harus tepat 100%. Saat ini totalnya adalah ' . $total . '%.');
        }

        $bobot = PengaturanBobot::first();
        if (!$bobot) {
            PengaturanBobot::create($request->only('ckp', 'absensi', 'survey'));
        } else {
            $bobot->update($request->only('ckp', 'absensi', 'survey'));
        }

        return redirect()->back()->with('success', 'Pengaturan bobot berhasil diperbarui!');
    }
}

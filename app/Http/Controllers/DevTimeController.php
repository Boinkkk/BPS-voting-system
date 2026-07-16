<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DevTimeController extends Controller
{
    public function setTime(Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Hanya tersedia di mode development.');
        }

        $request->validate([
            'test_time' => 'required|date'
        ]);

        \Illuminate\Support\Facades\Cache::put('global_test_time', $request->test_time);
        
        // Tetap simpan di session sebagai fallback untuk UI widget
        session(['test_time' => $request->test_time]);

        return back()->with('success', 'Waktu aplikasi berhasil diubah secara global ke ' . $request->test_time);
    }

    public function resetTime(Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Hanya tersedia di mode development.');
        }

        \Illuminate\Support\Facades\Cache::forget('global_test_time');
        session()->forget('test_time');

        return back()->with('success', 'Waktu aplikasi berhasil dikembalikan ke waktu aktual secara global.');
    }
}

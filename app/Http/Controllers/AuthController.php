<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Pegawai;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->identifier;
        $password = $request->password;
        
        $email = $identifier;
        
        // Cek apakah identifier adalah NIP (berupa angka)
        if (is_numeric($identifier)) {
            $pegawai = Pegawai::where('nip', $identifier)->first();
            if ($pegawai) {
                $email = $pegawai->email;
            } else {
                return back()->withErrors([
                    'identifier' => 'NIP tidak ditemukan dalam sistem.',
                ])->withInput();
            }
        }

        if (Auth::attempt(['email' => $email, 'password' => $password], $request->has('remember'))) {
            $request->session()->regenerate();
            
            Log::channel('audit')->info("Pegawai berhasil login", [
                'ip' => $request->ip(),
                'email' => $email
            ]);
            
            return redirect()->intended('dashboard');
        }

        Log::channel('audit')->warning("Percobaan login gagal", [
            'ip' => $request->ip(),
            'identifier' => $identifier
        ]);

        return back()->withErrors([
            'identifier' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Log::channel('audit')->info("Pegawai logout", [
                'ip' => $request->ip(),
                'email' => $user->email
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

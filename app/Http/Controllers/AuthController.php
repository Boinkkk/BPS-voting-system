<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string', 'regex:/^[a-zA-Z0-9@\.\_\-]+$/'],
            'password' => 'required|string',
        ], [
            'identifier.regex' => 'Format identifier tidak valid. Gunakan hanya huruf, angka, @, titik, garis bawah, atau strip.',
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

            activity()->causedBy(Auth::user())->withProperties([
                'ip' => $request->ip(),
                'email' => $email,
            ])->log('Pegawai berhasil login');

            return redirect()->intended('dashboard');
        }

        activity()->withProperties([
            'ip' => $request->ip(),
            'identifier' => $identifier,
        ])->log('Percobaan login gagal');

        return back()->withErrors([
            'identifier' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            activity()->causedBy($user)->withProperties([
                'ip' => $request->ip(),
                'email' => $user->email,
            ])->log('Pegawai logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

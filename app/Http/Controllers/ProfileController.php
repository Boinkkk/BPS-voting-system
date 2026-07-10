<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('profiles', 'public');
            $user->foto_profil = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('pegawai.departemen', 'pegawai.role');
        
        return view('profile.show', compact('user'));
    }
}

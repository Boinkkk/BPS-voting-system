<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PanduanController extends Controller
{
    public function index()
    {
        $path = base_path('USER_MANUAL.md');
        if (!file_exists($path)) {
            abort(404, 'Dokumen Panduan belum tersedia.');
        }

        $markdown = file_get_contents($path);
        
        // Laravel's Str::markdown uses CommonMark
        $html = Str::markdown($markdown);

        return view('panduan.index', compact('html'));
    }
}

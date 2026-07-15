<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Glosarium;

class GlosariumController extends Controller
{
    public function index()
    {
        $glosariums = Glosarium::orderBy('istilah', 'asc')->get();
        return view('glosarium.index', compact('glosariums'));
    }
}

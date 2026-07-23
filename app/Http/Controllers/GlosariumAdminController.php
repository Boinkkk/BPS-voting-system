<?php

namespace App\Http\Controllers;

use App\Models\Glosarium;
use Illuminate\Http\Request;

class GlosariumAdminController extends Controller
{
    public function index()
    {
        $glosariums = Glosarium::orderBy('istilah', 'asc')->get();

        return view('admin.glosarium.index', compact('glosariums'));
    }

    public function create()
    {
        return view('admin.glosarium.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'istilah' => 'required|string|max:255|unique:glosariums,istilah',
            'definisi' => 'required|string',
        ]);

        Glosarium::create($request->all());

        return redirect()->route('admin.glosarium.index')->with('success', 'Istilah glosarium berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $glosarium = Glosarium::findOrFail($id);

        return view('admin.glosarium.edit', compact('glosarium'));
    }

    public function update(Request $request, $id)
    {
        $glosarium = Glosarium::findOrFail($id);

        $request->validate([
            'istilah' => 'required|string|max:255|unique:glosariums,istilah,'.$id,
            'definisi' => 'required|string',
        ]);

        $glosarium->update($request->all());

        return redirect()->route('admin.glosarium.index')->with('success', 'Istilah glosarium berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $glosarium = Glosarium::findOrFail($id);
        $glosarium->delete();

        return redirect()->route('admin.glosarium.index')->with('success', 'Istilah glosarium berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with(['departemen', 'role']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%");
        }

        $pegawai = $query->orderBy('nama')->paginate(10)->withQueryString();
        $departemens = Departemen::all();
        $roles = Role::all();

        return view('admin.pegawai.index', compact('pegawai', 'departemens', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:50|unique:pegawai,nip',
            'email' => 'required|string|email|max:150|unique:pegawai,email',
            'password' => 'required|string|min:6',
            'jabatan' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemen,id',
            'role_id' => 'required|exists:role,id',
            'tanggal_masuk' => 'required|date',
            'status_pegawai' => 'required|in:aktif,nonaktif',
        ]);

        $pegawai = Pegawai::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'departemen_id' => $request->departemen_id,
            'role_id' => $request->role_id,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_pegawai' => $request->status_pegawai,
        ]);

        $admin = Auth::user();
        activity()->causedBy($admin)->withProperties([
            'ip' => $request->ip(),
            'admin_id' => $admin ? $admin->id : null,
            'nama_admin' => $admin ? $admin->nama : null,
            'pegawai_id' => $pegawai->id,
            'nama_pegawai' => $pegawai->nama,
            'nip_pegawai' => $pegawai->nip,
        ])->log('Admin menambahkan pegawai baru');

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => ['required', 'string', 'max:50', Rule::unique('pegawai')->ignore($pegawai->id)],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('pegawai')->ignore($pegawai->id)],
            'jabatan' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemen,id',
            'role_id' => 'required|exists:role,id',
            'tanggal_masuk' => 'required|date',
            'status_pegawai' => 'required|in:aktif,nonaktif',
        ]);

        $pegawai->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'departemen_id' => $request->departemen_id,
            'role_id' => $request->role_id,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_pegawai' => $request->status_pegawai,
        ]);

        $admin = Auth::user();
        activity()->causedBy($admin)->withProperties([
            'ip' => $request->ip(),
            'admin_id' => $admin ? $admin->id : null,
            'nama_admin' => $admin ? $admin->nama : null,
            'pegawai_id' => $pegawai->id,
            'nama_pegawai' => $pegawai->nama,
            'nip_pegawai' => $pegawai->nip,
        ])->log('Admin mengubah data pegawai');

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function updatePassword(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $pegawai->update([
            'password' => Hash::make($request->password),
        ]);

        $admin = Auth::user();
        activity()->causedBy($admin)->withProperties([
            'ip' => $request->ip(),
            'admin_id' => $admin ? $admin->id : null,
            'nama_admin' => $admin ? $admin->nama : null,
            'pegawai_id' => $pegawai->id,
            'nama_pegawai' => $pegawai->nama,
            'nip_pegawai' => $pegawai->nip,
        ])->log('Admin mengubah password pegawai');

        return redirect()->route('admin.pegawai.index')->with('success', 'Password pegawai berhasil diubah.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::withCount('reads')->orderBy('created_at', 'desc')->get();

        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'prioritas' => 'required|string',
            'target' => 'nullable|string',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date|after_or_equal:publish_at',
            'konten' => 'required|string',
            'lampiran.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        $data = $request->except('lampiran');

        $data['is_sticky'] = $request->has('is_sticky');
        $data['is_popup'] = $request->has('is_popup');
        $data['kirim_notifikasi'] = $request->has('kirim_notifikasi');

        // Handle file uploads
        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $path = $file->store('pengumuman', 'public');
                $lampiranPaths[] = $path;
            }
        }

        if (! empty($lampiranPaths)) {
            $data['lampiran'] = $lampiranPaths;
        }

        // Determine initial status based on schedule
        if ($request->publish_at) {
            if (now()->greaterThanOrEqualTo($request->publish_at)) {
                $data['status'] = 'Published';
            } else {
                $data['status'] = 'Draft';
            }
        } else {
            // If no publish date is set, publish immediately
            $data['status'] = 'Published';
            $data['publish_at'] = now();
        }

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(string $id)
    {
        // Usually not needed for admin, they can edit
    }

    public function edit(string $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, string $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'prioritas' => 'required|string',
            'target' => 'nullable|string',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date|after_or_equal:publish_at',
            'konten' => 'required|string',
            'lampiran.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['lampiran', 'remove_lampiran']);

        $data['is_sticky'] = $request->has('is_sticky');
        $data['is_popup'] = $request->has('is_popup');
        $data['kirim_notifikasi'] = $request->has('kirim_notifikasi');

        $lampiranPaths = is_array($pengumuman->lampiran) ? $pengumuman->lampiran : [];

        // Check for removed attachments
        if ($request->has('remove_lampiran')) {
            foreach ($request->remove_lampiran as $index => $remove) {
                if (isset($lampiranPaths[$index])) {
                    Storage::disk('public')->delete($lampiranPaths[$index]);
                    unset($lampiranPaths[$index]);
                }
            }
            // Re-index array
            $lampiranPaths = array_values($lampiranPaths);
        }

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $path = $file->store('pengumuman', 'public');
                $lampiranPaths[] = $path;
            }
        }

        $data['lampiran'] = empty($lampiranPaths) ? null : $lampiranPaths;

        if ($request->publish_at) {
            if (now()->greaterThanOrEqualTo($request->publish_at)) {
                $data['status'] = 'Published';
            } else {
                $data['status'] = 'Draft';
            }
            if ($request->expire_at && now()->greaterThanOrEqualTo($request->expire_at)) {
                $data['status'] = 'Expired';
            }
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if (is_array($pengumuman->lampiran)) {
            foreach ($pengumuman->lampiran as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function markAsRead(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $user = Auth::user();

        if ($user) {
            PengumumanRead::firstOrCreate([
                'pengumuman_id' => $pengumuman->id,
                'pegawai_id' => $user->id,
            ]);
        }

        return response()->json(['success' => true]);
    }
}

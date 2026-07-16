<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NilaiCkp;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Imports\CkpImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Services\KandidatService;

class CkpController extends Controller
{
    public function index(Request $request)
    {
        $requested_periode_id = $request->input('periode_id');
        $periodeData = PeriodePenilaian::getRecentAndDefault($requested_periode_id);
        
        $periodes = $periodeData['periodes'];
        $periode_id = $periodeData['default_id'];
        
        if ($periode_id != $requested_periode_id && !$requested_periode_id) {
            return redirect()->route('admin.ckp.index', array_merge($request->query(), [
                'periode_id' => $periode_id
            ]));
        }
        
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = NilaiCkp::with('pegawai')
            ->where('periode_id', $periode_id);

        if ($search) {
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $ckps = $query->paginate($perPage)->withQueryString();

        $semuaPegawai = Pegawai::whereHas('role', function($q){
            $q->where('tipe', 'Pegawai');
        })->get();

        return view('admin.ckp.index', compact('periodes', 'periode_id', 'ckps', 'semuaPegawai', 'search', 'perPage'));
    }

    public function manual(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'id_pegawai' => 'required|exists:pegawai,id',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Input nilai CKP hanya dapat dilakukan pada masa penginputan data.');
        }

        NilaiCkp::updateOrCreate(
            [
                'periode_id' => $request->periode_id,
                'pegawai_id' => $request->id_pegawai,
            ],
            [
                'nilai' => $request->nilai,
            ]
        );

        try {
            KandidatService::generateTop10Kandidat($request->periode_id);
        } catch (\Exception $e) {
            // Log error if needed, but don't stop the flow
        }

        return redirect()->back()->with('success', 'Nilai CKP berhasil disimpan dan ranking kandidat telah diperbarui.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'periode_id' => 'required|exists:periode_penilaian,id',
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        if ($periode->status !== 'penginputan') {
            return redirect()->back()->with('error', 'Upload data CKP hanya dapat dilakukan pada masa penginputan data.');
        }

        try {
            Excel::import(new CkpImport($request->periode_id), $request->file('file'));
            
            try {
                KandidatService::generateTop10Kandidat($request->periode_id);
            } catch (\Exception $e) {
                // Log error if needed
            }

            return redirect()->back()->with('success', 'Data CKP berhasil diunggah dan ranking kandidat telah diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }
    }
}

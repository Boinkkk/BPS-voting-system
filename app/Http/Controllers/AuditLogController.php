<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        // Filter: Search Text
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        // Filter: Causer (Siapa)
        if ($request->filled('causer_id')) {
            // Because causer could be User or Pegawai, we might need a composite value like "App\Models\Pegawai:1"
            // But if we assume standard causer_id is enough (if IDs don't overlap or if we filter by causer_type too)
            // Let's assume the causer_id format is "type:id" e.g., "App\Models\User:1"
            $parts = explode(':', $request->causer_id);
            if (count($parts) === 2) {
                $query->where('causer_type', $parts[0])
                    ->where('causer_id', $parts[1]);
            }
        }

        // Filter: Modul/Subjek (Di Mana / Tentang Apa)
        if ($request->filled('module')) {
            $module = $request->module;
            $query->where(function ($q) use ($module) {
                $q->where('subject_type', 'like', "%{$module}%")
                    ->orWhere('description', 'like', "%{$module}%");
            });
        }

        // Filter: Rentang Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Export CSV if requested
        if ($request->has('export') && $request->export == '1') {
            return $this->exportCsv($query);
        }

        $activities = $query->paginate(50)->withQueryString();

        $logs = $activities->map(function ($activity) {
            $context = $activity->properties->toArray();

            $causer_name = 'Sistem';
            $causer_role = '-';

            if ($activity->causer) {
                $causer_name = $activity->causer->nama ?? ($activity->causer->name ?? $activity->causer->email);
                if (isset($activity->causer->role)) {
                    $causer_role = $activity->causer->role->tipe ?? $activity->causer->role->nama ?? '-';
                }
            }

            // Determine Action Color & Type
            $type = 'info';
            $desc = strtolower($activity->description);
            if (str_contains($desc, 'tambah') || str_contains($desc, 'create') || str_contains($desc, 'baru')) {
                $type = 'success';
            } elseif (str_contains($desc, 'ubah') || str_contains($desc, 'update')) {
                $type = 'warning';
            } elseif (str_contains($desc, 'hapus') || str_contains($desc, 'delete') || str_contains($desc, 'gagal')) {
                $type = 'danger';
            }

            return [
                'id' => $activity->id,
                'timestamp' => $activity->created_at->format('Y-m-d H:i:s'),
                'time_ago' => $activity->created_at->diffForHumans(),
                'type' => $type,
                'message' => $activity->description,
                'causer_name' => $causer_name,
                'causer_role' => $causer_role,
                'context' => $context,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
            ];
        });

        // Get unique causers for dropdown
        // To be safe and performant, we can just fetch all distinct causer_type and causer_id from the table
        $causersList = Activity::select('causer_type', 'causer_id')
            ->whereNotNull('causer_id')
            ->groupBy('causer_type', 'causer_id')
            ->get()
            ->map(function ($act) {
                if ($act->causer) {
                    $name = $act->causer->nama ?? ($act->causer->name ?? $act->causer->email);

                    return [
                        'id' => $act->causer_type.':'.$act->causer_id,
                        'name' => $name.' ('.class_basename($act->causer_type).')',
                    ];
                }

                return null;
            })->filter()->sortBy('name')->values();

        $modulesList = [
            'Pegawai' => 'Pegawai',
            'Kandidat' => 'Kandidat',
            'Periode' => 'Periode Penilaian',
            'Bobot' => 'Pengaturan Bobot',
            'Survei' => 'Survei',
            'Auth' => 'Autentikasi (Login/Logout)',
        ];

        return view('admin.audit-log.index', compact('logs', 'activities', 'causersList', 'modulesList'));
    }

    private function exportCsv($query)
    {
        $fileName = 'audit_log_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Waktu', 'Aktor', 'Peran', 'Aktivitas', 'Modul', 'Detail Perubahan / IP'];

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(500, function ($activities) use ($file) {
                foreach ($activities as $activity) {
                    $causer_name = 'Sistem';
                    $causer_role = '-';
                    if ($activity->causer) {
                        $causer_name = $activity->causer->nama ?? ($activity->causer->name ?? $activity->causer->email);
                        if (isset($activity->causer->role)) {
                            $causer_role = $activity->causer->role->tipe ?? $activity->causer->role->nama ?? '-';
                        }
                    }

                    $module = $activity->subject_type ? class_basename($activity->subject_type) : '-';
                    $properties = json_encode($activity->properties->toArray(), JSON_UNESCAPED_UNICODE);

                    fputcsv($file, [
                        $activity->id,
                        $activity->created_at->format('Y-m-d H:i:s'),
                        $causer_name,
                        $causer_role,
                        $activity->description,
                        $module,
                        $properties,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

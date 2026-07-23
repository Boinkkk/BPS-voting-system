<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class AuditLogTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $module = '';

    public string $causer_id = '';

    public string $date_start = '';

    public string $date_end = '';

    protected string $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedModule()
    {
        $this->resetPage();
    }

    public function updatedCauser_id()
    {
        $this->resetPage();
    }

    public function updatedDateStart()
    {
        $this->resetPage();
    }

    public function updatedDateEnd()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'module', 'causer_id', 'date_start', 'date_end']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Activity::with('causer')->latest();

        if (! empty(trim($this->search))) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        if (! empty($this->causer_id)) {
            $parts = explode(':', $this->causer_id);
            if (count($parts) === 2) {
                $query->where('causer_type', $parts[0])
                    ->where('causer_id', $parts[1]);
            }
        }

        if (! empty($this->module)) {
            $module = $this->module;
            $query->where(function ($q) use ($module) {
                $q->where('subject_type', 'like', "%{$module}%")
                    ->orWhere('description', 'like', "%{$module}%");
            });
        }

        if (! empty($this->date_start)) {
            $query->whereDate('created_at', '>=', $this->date_start);
        }

        if (! empty($this->date_end)) {
            $query->whereDate('created_at', '<=', $this->date_end);
        }

        $activities = $query->paginate(25);

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

        return view('livewire.audit-log-table', [
            'logs' => $logs,
            'activities' => $activities,
            'causersList' => $causersList,
            'modulesList' => $modulesList,
        ]);
    }
}

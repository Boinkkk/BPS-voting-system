<?php

namespace App\Livewire;

use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class PegawaiTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $departemen_id = '';

    public string $role_id = '';

    public string $status_pegawai = '';

    protected string $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDepartemenId()
    {
        $this->resetPage();
    }

    public function updatedRoleId()
    {
        $this->resetPage();
    }

    public function updatedStatusPegawai()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'departemen_id', 'role_id', 'status_pegawai']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Pegawai::with(['departemen', 'role']);

        if (! empty(trim($this->search))) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($this->departemen_id)) {
            $query->where('departemen_id', $this->departemen_id);
        }

        if (! empty($this->role_id)) {
            $query->where('role_id', $this->role_id);
        }

        if (! empty($this->status_pegawai)) {
            $query->where('status_pegawai', $this->status_pegawai);
        }

        $pegawai = $query->orderBy('nama')->paginate(10);
        $departemens = Departemen::orderBy('nama')->get();
        $roles = Role::orderBy('tipe')->get();

        return view('livewire.pegawai-table', [
            'pegawai' => $pegawai,
            'departemens' => $departemens,
            'roles' => $roles,
        ]);
    }
}

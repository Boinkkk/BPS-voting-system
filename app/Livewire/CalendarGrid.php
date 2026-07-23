<?php

namespace App\Livewire;

use App\Models\PeriodePenilaian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CalendarGrid extends Component
{
    public $selectedMonth;

    public $selectedYear;

    public function mount($initialMonth = null, $initialYear = null)
    {
        $this->selectedMonth = $initialMonth ?? Carbon::now()->month;
        $this->selectedYear = $initialYear ?? Carbon::now()->year;
    }

    public function previousMonth()
    {
        $this->selectedMonth--;
        if ($this->selectedMonth < 1) {
            $this->selectedMonth = 12;
            $this->selectedYear--;
        }
    }

    public function nextMonth()
    {
        $this->selectedMonth++;
        if ($this->selectedMonth > 12) {
            $this->selectedMonth = 1;
            $this->selectedYear++;
        }
    }

    public function render()
    {
        $cacheKey = 'kalender_'.$this->selectedMonth.'_'.$this->selectedYear;

        // Store as plain PHP array to avoid serialization issues with Eloquent Collection or stdClass.
        // wrap with collect() after retrieval so the blade view can use object-arrow syntax.
        $cachedData = Cache::remember($cacheKey, now()->addDay(), function () {
            $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfDay();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            return PeriodePenilaian::where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('tanggal_mulai', '<=', $startOfMonth)
                            ->where('tanggal_selesai', '>=', $endOfMonth);
                    });
            })->get()->map(fn ($model) => $model->toArray())->all();
        });

        // Convert each plain array back to stdClass so blade can use $periode->property syntax.
        $periodesInMonth = collect($cachedData)->map(fn ($item) => (object) $item);

        return view('livewire.calendar-grid', [
            'periodesInMonth' => $periodesInMonth,
        ]);
    }
}

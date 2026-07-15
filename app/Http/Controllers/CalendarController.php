<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PeriodePenilaian;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $startOfMonth = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $periodesInMonth = PeriodePenilaian::where(function($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                  ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                      $q->where('tanggal_mulai', '<=', $startOfMonth)
                        ->where('tanggal_selesai', '>=', $endOfMonth);
                  });
        })->get();

        return view('kalender.index', compact('selectedMonth', 'selectedYear', 'periodesInMonth'));
    }
}

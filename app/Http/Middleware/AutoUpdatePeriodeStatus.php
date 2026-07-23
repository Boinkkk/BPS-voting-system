<?php

namespace App\Http\Middleware;

use App\Models\PeriodePenilaian;
use App\Services\KandidatService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoUpdatePeriodeStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activePeriods = PeriodePenilaian::where('status', '!=', 'selesai')->get();

        foreach ($activePeriods as $periode) {
            $oldStatus = $periode->status;
            $newStatus = $periode->computeStatusBasedOnDate();

            if ($oldStatus !== $newStatus) {
                $periode->status = $newStatus;
                $periode->save();

                // Jika status berubah ke review_kepala, otomatis generate top 3 kandidat
                if ($newStatus === 'review_kepala') {
                    KandidatService::generateTop3Kandidat($periode->id);
                }
            }
        }

        return $next($request);
    }
}

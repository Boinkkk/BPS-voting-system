<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SetTestTime
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production')) {
            $testTimeStr = Cache::get('global_test_time') ?? session('test_time');

            if ($testTimeStr) {
                try {
                    $testTime = Carbon::parse($testTimeStr);
                    Carbon::setTestNow($testTime);

                    // Mencegah cookie session terhapus otomatis oleh browser karena expires di masa lalu
                    config(['session.expire_on_close' => true]);
                } catch (\Exception $e) {
                    // If parsing fails, just clear it
                    Cache::forget('global_test_time');
                    session()->forget('test_time');
                }
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SetTestTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!app()->environment('production')) {
            $testTimeStr = \Illuminate\Support\Facades\Cache::get('global_test_time') ?? session('test_time');
            
            if ($testTimeStr) {
                try {
                    $testTime = Carbon::parse($testTimeStr);
                    Carbon::setTestNow($testTime);
                    
                    // Mencegah cookie session terhapus otomatis oleh browser karena expires di masa lalu
                    config(['session.expire_on_close' => true]);
                } catch (\Exception $e) {
                    // If parsing fails, just clear it
                    \Illuminate\Support\Facades\Cache::forget('global_test_time');
                    session()->forget('test_time');
                }
            }
        }

        return $next($request);
    }
}

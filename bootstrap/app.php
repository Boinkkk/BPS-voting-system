<?php

use App\Http\Middleware\AutoUpdatePeriodeStatus;
use App\Http\Middleware\CheckRoleAdmin;
use App\Http\Middleware\CheckRoleAdminOrKepalaUmum;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetTestTime;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            SecurityHeaders::class,
            SetTestTime::class,
            AutoUpdatePeriodeStatus::class,
        ]);
        $middleware->alias([
            'admin' => CheckRoleAdmin::class,
            'admin_or_kepala_umum' => CheckRoleAdminOrKepalaUmum::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

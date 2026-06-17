<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('oficina')
                ->name('oficina.')
                ->group(base_path('routes/oficina.php'));

            Route::middleware('web')
                ->prefix('cliente')
                ->name('cliente.')
                ->group(base_path('routes/cliente.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.oficina' => \App\Http\Middleware\EnsureOficinaAuth::class,
            'auth.cliente' => \App\Http\Middleware\EnsureClienteAuth::class,
            'tenant'       => \App\Http\Middleware\TenantMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

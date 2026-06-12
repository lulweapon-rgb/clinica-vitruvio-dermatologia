<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // <-- DEBES AGREGAR ESTA LÍNEA
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // ... resto de tu configuración
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'superadmin' => \App\Http\Middleware\CheckSuperAdmin::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
    

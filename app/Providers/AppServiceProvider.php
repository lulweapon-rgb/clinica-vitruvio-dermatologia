<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- 1. AGREGA ESTA LÍNEA AQUÍ ARRIBA

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. AGREGA ESTA CONDICIÓN PARA FORZAR HTTPS EN PRODUCCIÓN
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
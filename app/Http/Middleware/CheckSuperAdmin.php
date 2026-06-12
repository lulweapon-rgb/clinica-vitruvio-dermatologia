<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si no está logueado, o si está logueado pero NO es super admin
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            // Lanza el error 403 (la pantalla negra que mostraste en la imagen)
            abort(403, 'ESTA ACCIÓN NO ESTÁ AUTORIZADA PARA TU ROL.'); 
        }

        return $next($request);
    }
}
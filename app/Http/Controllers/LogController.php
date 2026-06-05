<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAcceso;

class LogController extends Controller
{
    public function index()
    {
        // Traemos todos los logs junto con los datos del usuario y su rol
        $logs = LogAcceso::with('usuario.rol')
            ->orderBy('fecha_acceso', 'desc')
            ->get();

        return view('admin.logs.index', compact('logs'));
    }
}
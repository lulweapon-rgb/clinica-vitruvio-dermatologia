<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\LogAcceso;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    // NUEVO MÉTODO: Bypass automático para entorno de pruebas
    public function devLogin()
    {
        $admin = Usuario::where('rol_id', 1)->first();
        
        if ($admin) {
            Auth::login($admin);
            return redirect()->route('dashboard')->with('success', 'Sesión de desarrollo iniciada.');
        }

        return redirect('/login')->withErrors([
            'correo' => 'Debe registrar al menos un usuario Administrador en Tinker para usar el bypass.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $credentials = [
            'correo' => $request->correo,
            'password' => $request->contrasena, 
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Models\Usuario;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $user = Usuario::where('correo', $request->correo)->first();

        if ($user && Hash::check($request->contrasena, $user->contrasena)) {
            
            // Verificación de Equipo de Confianza (Hito 3)
            $cookieName = 'trusted_device_' . $user->id;
            $trustedToken = $request->cookie($cookieName);

            if ($trustedToken) {
                // Creamos el hash del fingerprint actual para comparar
                $fingerprint = hash('sha256', $request->userAgent() . '_' . $trustedToken);

                if ($user->trusted_device_hash === $fingerprint && now()->lessThan($user->trusted_device_expires_at)) {
                    // El equipo es de confianza y está vigente, saltamos la 2FA
                    Auth::login($user);
                    $request->session()->regenerate();
                   return redirect()->intended('admin/dashboard');
                }
            }

            // Si no es un equipo de confianza, sigue el flujo normal de la 2FA (Hito 2)
            $request->session()->put('2fa_user_id', $user->id);
            return redirect()->route('2fa.index');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    public function show2faForm(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect('/login');
        }

        return view('auth.2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'totp_code' => 'required|numeric|digits:6',
        ]);

        $userId = $request->session()->get('2fa_user_id');
        $user = Usuario::find($userId);

        if (!$user) {
            return redirect('/login');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->totp_code);

        if ($valid) {
            $request->session()->forget('2fa_user_id');
            Auth::login($user);
            $request->session()->regenerate();

            // Si el usuario marcó "Confiar en este equipo"
            if ($request->has('trust_device')) {
                $randomToken = bin2hex(random_bytes(32));
                
                // Generamos el hash combinando el user-agent y el token persistente
                $fingerprint = hash('sha256', $request->userAgent() . '_' . $randomToken);

                // Guardamos en la base de datos con expiración de 30 días
                $user->trusted_device_hash = $fingerprint;
                $user->trusted_device_expires_at = now()->addDays(30);
                $user->save();

                // Creamos la cookie segura exigida por la rúbrica (HttpOnly, Secure, SameSite=Lax)
                $cookieName = 'trusted_device_' . $user->id;
                Cookie::queue(
                    $cookieName,
                    $randomToken,
                    43200, // Duración en minutos (30 días)
                    null,
                    null,
                    true,  // Secure (HTTPS)
                    true,  // HttpOnly
                    false,
                    'Lax'  // SameSite
                );
            }

            return redirect()->intended('admin/dashboard');
        }

        return back()->withErrors(['totp_code' => 'El código es incorrecto o ha expirado.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
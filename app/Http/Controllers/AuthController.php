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

    /**
     * MÉTDO GET: Muestra la pantalla del Código OTP o el Código QR
     */
    public function show2faForm(Request $request)
    {
        $userId = session('2fa_user_id');
        if (!$userId) return redirect('/login');
        
        $user = \App\Models\Usuario::find($userId);
        $google2fa = new Google2FA();

        $qrCodeUrl = null;
        $llaveManual = null;
        $esPrimerIngreso = false;

        // Si el usuario NO tiene llave en la base de datos, es su primer ingreso
        if (empty($user->two_factor_secret)) {
            $esPrimerIngreso = true;
            
            // Generamos una llave y la guardamos temporalmente en SESIÓN para evitar bloqueos
            if (!session()->has('temp_2fa_secret')) {
                session(['temp_2fa_secret' => $google2fa->generateSecretKey()]);
            }
            
            $llaveManual = session('temp_2fa_secret');
            
            // Generamos la URL nativa de Google Authenticator
            $qrCodeUrl = $google2fa->getQRCodeUrl('Clínica Vitruvio', $user->correo, $llaveManual);
        }

        return view('auth.2fa', compact('esPrimerIngreso', 'qrCodeUrl', 'llaveManual'));
    }

    /**
     * MÉTODO POST: Verifica el código de 6 dígitos
     */
    public function verify2fa(Request $request)
    {
        $userId = session('2fa_user_id');
        if (!$userId) return redirect('/login');
        
        $user = \App\Models\Usuario::find($userId);
        $google2fa = new Google2FA();

        // Si el usuario ya tiene llave, usamos esa. Si es nuevo, usamos la temporal de la sesión.
        $llaveAValidar = $user->two_factor_secret ?: session('temp_2fa_secret');

        // Validamos el código de 6 dígitos
        $valid = $google2fa->verifyKey($llaveAValidar, $request->totp_code);

        if ($valid) {
            // Si el código es correcto y era su primer ingreso, ¡Ahors sí guardamos la llave en la BD!
            if (empty($user->two_factor_secret)) {
                $user->two_factor_secret = session('temp_2fa_secret');
                $user->save();
                session()->forget('temp_2fa_secret'); // Limpiamos la basura temporal
            }

            // Autenticamos al usuario finalmente
            $request->session()->forget('2fa_user_id');
            \Illuminate\Support\Facades\Auth::login($user);
            $request->session()->regenerate();

            // Lógica de "confiar en este equipo" si la tienes...
            
            return redirect()->intended('/evaluaciones'); 
        }

        // Si el código falla
        return back()->withErrors(['totp_code' => 'El código de seguridad ingresado es incorrecto.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Dos Pasos - Clínica Vitruvio</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

    <div class="p-8">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Verificación de Seguridad</h2>
        <p class="text-sm text-slate-500 mt-1">Autenticación de Dos Factores (2FA)</p>
    </div>

    @if($esPrimerIngreso)
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-6 text-center">
            <h3 class="text-sm font-bold text-blue-800 uppercase mb-2"><i class="fa-solid fa-mobile-screen-button"></i> Configuración Inicial Requerida</h3>
            <p class="text-xs text-blue-600 mb-4">Abre Google Authenticator en tu celular y escanea este código QR para vincular tu cuenta.</p>
            
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="Código QR" class="mx-auto rounded-lg shadow-sm border-2 border-white mb-4">
            
            <p class="text-[10px] text-slate-500 uppercase font-bold">O ingresa esta llave manualmente:</p>
            <p class="text-xs font-mono font-black text-slate-800 bg-white inline-block px-3 py-1 rounded border mt-1">{{ $llaveManual }}</p>
        </div>
    @endif

    <form action="{{ url('/2fa') }}" method="POST" class="space-y-5">
        @csrf
        
        @if($errors->any())
            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-xs font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                {{ $esPrimerIngreso ? 'Ingresa el primer código generado' : 'Código de Autenticador' }}
            </label>
            <input type="text" name="totp_code" required autofocus maxlength="6" autocomplete="off" placeholder="Ej. 123456" 
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-center text-2xl tracking-[0.5em] font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-sm shadow-md transition-all uppercase tracking-wide flex items-center justify-center gap-2">
            <span>Validar y Entrar</span> <i class="fa-solid fa-shield-halved"></i>
        </button>
    </form>
</div>

</body>
</html>
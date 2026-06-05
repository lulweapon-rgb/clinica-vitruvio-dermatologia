<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Dos Pasos - Clínica Vitruvio</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

    <div style="background-color: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; width: 100%;">
        <h2 style="text-align: center; color: #1f2937;">Verificación de Seguridad</h2>
        <p style="text-align: center; color: #4b5563; font-size: 14px;">Ingrese el código de 6 dígitos generado por su aplicación de autenticación (Google Authenticator, Authy, etc.).</p>

        <form method="POST" action="{{ route('2fa.verify') }}" style="margin-top: 1.5rem;">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label for="totp_code" style="display: block; font-size: 14px; color: #374151; margin-bottom: 0.5rem;">Código TOTP:</label>
                <input type="text" name="totp_code" id="totp_code" maxlength="6" pattern="\d{6}" required autocomplete="off" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 16px; text-align: center; letter-spacing: 0.5rem;">
                
                @error('totp_code')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem; display: flex; align-items: center;">
                <input type="checkbox" name="trust_device" id="trust_device" value="1" style="margin-right: 0.5rem;">
                <label for="trust_device" style="font-size: 13px; color: #4b5563;">Confiar en este equipo (No volver a pedir código por 30 días)</label>
            </div>

            <button type="submit" style="width: 100%; padding: 0.75rem; background-color: #2563eb; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                Verificar y Entrar
            </button>
        </form>
    </div>

</body>
</html>
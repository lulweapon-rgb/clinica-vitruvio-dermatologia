<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Vitruvio - Autenticación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="p-6 bg-slate-800 text-center relative">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-user-shield text-white text-xl"></i>
            </div>
            <h2 class="text-white font-bold text-xl tracking-wide uppercase">Clínica Vitruvio</h2>
            <p class="text-blue-400 text-xs font-mono uppercase tracking-wider mt-1">Plataforma Oncológica IA</p>
        </div>

        <div class="p-8">
            
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="correo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
                    <input 
                        type="email" 
                        name="correo" 
                        id="correo" 
                        value="{{ old('correo', 'admin@vitruvio.com') }}" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                        required
                    >
                </div>

                <div>
                    <label for="contrasena" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contraseña</label>
                    <input 
                        type="password" 
                        name="contrasena" 
                        id="contrasena" 
                        value="secreta123"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                        required
                    >
                </div>

               <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2"
                    >
                        <span>Ingresar con Credenciales</span>
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </div>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-slate-400 font-bold tracking-wider">Entorno de Pruebas</span></div>
            </div>

            <div>
    <a 
        href="{{ route('dev.login') }}" 
        class="w-full bg-slate-950 hover:bg-slate-800 text-slate-200 font-bold py-2.5 px-4 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2 border border-slate-700 text-center"
    >
        <span>Ingresar con Credenciales</span>
        <i class="fa-solid fa-bolt text-amber-400"></i>
    </a>
</div>

        </div>

        <div class="px-8 py-3 bg-slate-50 border-t border-slate-100 text-center text-[10px] text-slate-400 font-medium">
            Soporte Técnico Interno v6.1
        </div>

    </div>

</body>
</html>
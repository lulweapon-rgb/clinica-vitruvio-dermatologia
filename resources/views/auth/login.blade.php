<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Vitruvio - Autenticación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        
        <div class="p-6 bg-slate-800 text-center relative">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-user-shield text-white text-xl"></i>
            </div>
            <h2 class="text-white font-bold text-xl tracking-wide uppercase">Clínica Vitruvio</h2>
            <p class="text-blue-400 text-xs font-mono uppercase tracking-wider mt-1">Seguimiento Dermatológico IA</p>
        </div>

        <div class="p-8">
            
            @if($errors->any())
                <div class="mb-5 p-3 bg-red-50 border-l-4 border-red-500 rounded-r text-red-700 text-xs font-medium flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="correo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-400"></i>
                        </div>
                        <input 
                            type="email" 
                            name="correo" 
                            id="correo" 
                            placeholder="usuario@vitruvio.com"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                    </div>
                </div>

                <div>
                    <label for="contrasena" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                        <input 
                            type="password" 
                            name="contrasena" 
                            id="contrasena" 
                            placeholder="••••••••"
                            required
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-3">
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5"
                    >
                        <span>Ingresar con Credenciales</span>
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </div>
            </form>

        </div>

        <div class="px-8 py-3 bg-slate-50 border-t border-slate-100 text-center text-[10px] text-slate-400 font-medium">
            Soporte Técnico Interno v6.1
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('contrasena');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Vitruvio - @yield('title', 'Panel')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 font-sans text-slate-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col shadow-2xl shrink-0">
        
        <div class="h-16 flex items-center justify-center border-b border-slate-800 px-4">
            <i class="fa-solid fa-shield-virus text-blue-500 text-2xl"></i>
            <div class="ml-3 overflow-hidden whitespace-nowrap">
                <h1 class="font-bold text-white text-lg tracking-wide">Clínica Vitruvio</h1>
                <p class="text-[10px] text-blue-400 font-mono uppercase">IA Dermatológica</p>
            </div>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-2 mt-4">Principal</p>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600/20 text-blue-400 font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span class="text-sm">Dashboard / Inicio</span>
            </a>

            <a href="{{ route('pacientes.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('pacientes.*') ? 'bg-blue-900/50 text-white font-bold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-hospital-user w-5 text-center text-lg"></i>
                <span class="text-sm font-medium">Pacientes</span>
            </a>
            
            <a href="{{ route('especialidades.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('especialidades.*') ? 'bg-blue-900/50 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-notes-medical w-5 text-center"></i>
                <span class="text-sm">Especialidades</span>
            </a>

            <a href="{{ route('roles.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'bg-blue-900/50 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-user-shield w-5 text-center"></i>
                <span class="text-sm">Roles</span>
            </a>

            <a href="{{ route('usuarios.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('usuarios.*') ? 'bg-blue-900/50 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-users w-5 text-center"></i>
                <span class="text-sm">Personal y Accesos</span>
            </a>

            <a href="{{ route('evaluaciones.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('evaluaciones.*') ? 'bg-blue-900/50 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-notes-medical w-5 text-center"></i>
                <span class="text-sm">Historial Clinico / Evaluacion</span>
            </a>

            <a href="{{ route('analisis.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('analisis.*') ? 'bg-indigo-900/50 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-microchip w-5 text-center text-indigo-400"></i>
                <span class="text-sm">Motor Análisis CNN</span>
            </a>

            <a href="{{ route('seguimientos.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('seguimientos.*') ? 'bg-amber-900/50 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-timeline w-5 text-center text-amber-400"></i>
                <span class="text-sm">Seguimiento Evolutivo</span>
            </a>
<a href="{{ route('logs.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('logs.*') ? 'bg-slate-700 text-white font-bold border-l-4 border-slate-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-server w-5 text-center text-slate-400"></i>
                <span class="text-sm">Auditoría y Logs</span>
            </a>

            <a href="{{ route('reportes.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('reportes.*') ? 'bg-emerald-900/50 text-white font-bold border-l-4 border-emerald-500' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-file-pdf w-5 text-center text-emerald-400"></i>
                <span class="text-sm">Reportes Dinámicos</span>
            </a>
            
        </nav>
        
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 text-slate-400 hover:text-red-400 transition-colors p-2 rounded-lg hover:bg-slate-800">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="text-sm font-medium">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full relative overflow-hidden">
        
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-6 shadow-sm shrink-0">
            <div class="flex items-center gap-4">
                <span class="text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-user-circle text-slate-400 mr-2 text-lg"></i>
                    {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
                </span>
            </div>
        </header>

        <div class="flex-1 overflow-auto p-6 lg:p-8">
            @yield('content')
        </div>

    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>
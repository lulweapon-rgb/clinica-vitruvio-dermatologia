@extends('layouts.app')

@section('title', 'Mantenimiento Pacientes')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Mantenimiento Pacientes</h2>
            <p class="text-slate-500 text-sm mt-0.5">Control de registros médicos, auditoría cronológica y reportería avanzada con paginación integrada.</p>
        </div>
        <div class="text-sm font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg border">
            Inicio / Pacientes
        </div>
    </div>

    

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-700 text-white space-y-4">
        <div class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-400">
            <i class="fa-solid fa-filter text-blue-400"></i> Filtros de Auditoría para Reportes
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Buscar Paciente (C.I. / Nombre)</label>
                <input type="text" id="filtroTexto" oninput="resetearPaginaYFiltrar()" placeholder="Ej. 8374923 o Juan Perez..." class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Estado en Sistema</label>
                <select id="filtroEstado" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">AMBOS (Todos)</option>
                    <option value="ACTIVO">SOLO ACTIVOS</option>
                    <option value="INACTIVO">SOLO INACTIVOS</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Sexo del Paciente</label>
                <select id="filtroGenero" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">AMBOS</option>
                    <option value="MASCULINO">MASCULINO</option>
                    <option value="FEMENINO">FEMENINO</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end border-t border-slate-600 pt-4 mt-2">
            <div class="lg:col-span-2 flex gap-4">
                <div class="w-full">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Registrados Desde:</label>
                    <input type="date" id="filtroFechaDesde" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Registrados Hasta:</label>
                    <input type="date" id="filtroFechaHasta" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="lg:col-start-5">
                <button onclick="limpiarFiltros()" class="w-full bg-slate-600 hover:bg-slate-500 text-white text-sm font-bold py-2 px-3 rounded-lg transition-colors uppercase tracking-wide flex items-center justify-center gap-2">
                    <i class="fa-solid fa-eraser"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="p-4 border-b border-slate-200 flex flex-wrap justify-between items-center gap-4 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <button onclick="exportarExcelNativo()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm uppercase">
                    <i class="fa-solid fa-file-excel"></i> Exportar Excel
                </button>
                <button onclick="imprimirReporteNativo()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm uppercase">
                    <i class="fa-solid fa-file-pdf"></i> Exportar PDF / Imprimir
                </button>
            </div>
            
            <button onclick="document.getElementById('modalRegistro').classList.remove('hidden')" class="bg-[#dc3545] hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2 uppercase tracking-wide">
                <i class="fa-solid fa-plus text-sm"></i> NUEVO REGISTRO
            </button>
        </div>

        <div class="p-4 bg-white">
            <table class="w-full text-left text-sm border-collapse table-auto border border-slate-200" id="tablaMaestraPacientes">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider border-b border-slate-200 text-xs">
                        <th class="p-3 font-bold text-center border border-slate-200">#</th>
                        <th class="p-3 font-bold border border-slate-200">Paciente</th>
                        <th class="p-3 font-bold border border-slate-200">CI</th>
                        <th class="p-3 font-bold border border-slate-200">Celular</th>
                        <th class="p-3 font-bold border border-slate-200">Correo Electrónico</th>
                        <th class="p-3 font-bold border border-slate-200">F. Nac. (Edad)</th>
                        <th class="p-3 font-bold border border-slate-200">Sexo</th>
                        <th class="p-3 font-bold border border-slate-200">Hora Registro</th>
                        <th class="p-3 font-bold border border-slate-200">Hora Inactivación</th>
                        <th class="p-3 font-bold text-center border border-slate-200">Estado</th>
                        <th class="p-3 font-bold text-center border border-slate-200">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="cuerpoTablaPacientes">
                    @forelse($pacientes as $index => $pac)
                    @php
                        // Sincronización de huso horario restando 4 horas para Bolivia de forma manual y explícita
                        $horaRegistro = $pac->created_at ? \Carbon\Carbon::parse($pac->created_at)->subHours(4)->format('d/m/Y H:i') : 'No registrada';
                        $fechaFiltro = $pac->created_at ? \Carbon\Carbon::parse($pac->created_at)->subHours(4)->format('Y-m-d') : '';
                        $horaInactivacion = $pac->trashed() ? \Carbon\Carbon::parse($pac->deleted_at)->subHours(4)->format('d/m/Y H:i') : '';
                        $estadoFiltro = $pac->trashed() ? 'INACTIVO' : 'ACTIVO';
                        $textoBusqueda = strtolower($pac->ci . ' ' . $pac->nombre . ' ' . $pac->apellido_paterno . ' ' . $pac->apellido_materno);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $pac->trashed() ? 'bg-slate-50 opacity-60' : '' }}"
                        data-busqueda="{{ $textoBusqueda }}"
                        data-fecha="{{ $fechaFiltro }}"
                        data-estado="{{ $estadoFiltro }}"
                        data-genero="{{ strtoupper($pac->genero) }}">
                        
                        <td class="p-3 text-center text-slate-400 font-mono border border-slate-100">{{ $index + 1 }}</td>
                        <td class="p-3 font-bold text-slate-800 uppercase break-words border border-slate-100">{{ $pac->apellido_paterno }} {{ $pac->apellido_materno }} {{ $pac->nombre }}</td>
                        <td class="p-3 font-mono text-slate-600 border border-slate-100">{{ $pac->ci }}</td>
                        <td class="p-3 text-slate-500 border border-slate-100">{{ $pac->celular ?? 'Sin registro' }}</td>
                        <td class="p-3 text-slate-500 font-mono text-xs border border-slate-100">{{ $pac->correo }}</td>
                        <td class="p-3 text-slate-600 border border-slate-100">
                            {{ \Carbon\Carbon::parse($pac->fecha_nacimiento)->format('d/m/Y') }}
                            <span class="block text-xs text-blue-600 font-bold uppercase mt-1">{{ \Carbon\Carbon::parse($pac->fecha_nacimiento)->age }} AÑOS</span>
                        </td>
                        <td class="p-3 border border-slate-100">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ strtoupper($pac->genero) === 'MASCULINO' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-pink-50 text-pink-700 border border-pink-100' }}">
                                {{ strtoupper($pac->genero) }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-600 font-mono border border-slate-100 text-xs">{{ $horaRegistro }}</td>
                        <td class="p-3 font-mono border border-slate-100 text-xs">
                            @if($pac->trashed())
                                <span class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded border border-red-100">{{ $horaInactivacion }}</span>
                            @else
                                <span class="text-slate-400 italic">Vigente</span>
                            @endif
                        </td>
                        
                        <td class="p-3 text-center border border-slate-100">
                            @if($pac->trashed())
                                <form action="{{ route('pacientes.restore', $pac->id) }}" method="POST" id="form-restore-{{ $pac->id }}">
                                    @csrf
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" class="sr-only peer" onchange="document.getElementById('form-restore-{{ $pac->id }}').submit()">
                                        <div class="w-9 h-5 bg-slate-300 rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                        <span class="ml-2 text-xs font-bold uppercase text-slate-400">Inactivo</span>
                                    </label>
                                </form>
                            @else
                                <label class="relative inline-flex items-center cursor-not-allowed select-none opacity-90">
                                    <input type="checkbox" class="sr-only peer" checked disabled>
                                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                                    <span class="ml-2 text-xs font-bold uppercase text-emerald-600">Activo</span>
                                </label>
                            @endif
                        </td>

                        <td class="p-3 text-center border border-slate-100">
    <div class="flex justify-center gap-1.5">
        
        <button onclick='abrirModalVer(@json($pac))' class="bg-slate-500 hover:bg-slate-600 text-white p-2 rounded-lg shadow-sm transition-colors" title="Ver Detalles">
            <i class="fa-solid fa-eye text-xs"></i>
        </button>
        
        @if(!$pac->trashed())
            <button onclick='abrirModalEditar(@json($pac))' class="bg-[#007bff] hover:bg-blue-700 text-white p-2 rounded-lg shadow-sm transition-colors" title="Editar">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
            </button>
            
            <button onclick="abrirModalEliminar({{ $pac->id }})" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-sm transition-colors" title="Inactivar">
                <i class="fa-solid fa-trash text-xs"></i>
            </button>
        @else
            <form action="{{ route('pacientes.force_delete', $pac->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('⚠️ ¡ADVERTENCIA EXTREMA!\n\n¿Estás absolutamente seguro de eliminar a este paciente FÍSICAMENTE?\n\nEsta acción NO se puede deshacer y borrará permanentemente sus datos.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-800 hover:bg-red-950 text-white p-2 rounded-lg shadow-sm transition-colors" title="Destrucción Total (Borrado Físico)">
                    <i class="fa-solid fa-fire-flame-curved text-xs"></i> 
                </button>
            </form>
        @endif
        
    </div>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="p-8 text-center text-slate-400 font-medium text-sm">
                            <i class="fa-solid fa-folder-open text-3xl block mb-2 opacity-30"></i> Sin registros médicos de pacientes indexados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div id="infoRegistros" class="text-sm font-bold text-slate-500 uppercase tracking-wide"></div>
            <div id="paginacionControles" class="flex items-center gap-1.5 select-none"></div>
        </div>

    </div>
</div>

<div id="modalRegistro" class="fixed inset-0 z-50 bg-slate-900/50 justify-center items-center p-20 {{ $errors->any() ? 'flex' : 'hidden' }}">
    
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-auto max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center p-5 border-b">
             <h3 class="text-xl font-bold text-slate-800">Registro de Paciente</h3>
            <button onclick="document.getElementById('modalRegistro').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>
        <form action="{{ route('pacientes.store') }}" method="POST" autocomplete="off">
            @csrf
           <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-5">
    
    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">CI (Carnet de Identidad)</label>
        <input type="text" name="ci" value="{{ old('ci') }}" placeholder="Solo números" pattern="[0-9]+" inputmode="numeric" class="w-full border {{ $errors->has('ci') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
        @error('ci')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Nombres</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Solo letras" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border {{ $errors->has('nombre') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
        @error('nombre')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Apellido Paterno</label>
        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" placeholder="Solo letras" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border {{ $errors->has('apellido_paterno') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
        @error('apellido_paterno')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Apellido Materno</label>
        <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" placeholder="Solo letras" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border {{ $errors->has('apellido_materno') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors">
        @error('apellido_materno')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Celular</label>
        <input type="text" name="celular" value="{{ old('celular') }}" placeholder="Solo números" pattern="[0-9]+" inputmode="numeric" class="w-full border {{ $errors->has('celular') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors">
        @error('celular')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Correo (Gmail)</label>
        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="usuario@gmail.com" pattern="[a-zA-Z0-9._%+-]+@gmail\.com" title="Debe ser @gmail.com" class="w-full border {{ $errors->has('correo') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
        @error('correo')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Fecha de Nacimiento</label>
        <input type="date" name="fecha_nacimiento" id="reg_fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="w-full border {{ $errors->has('fecha_nacimiento') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm text-slate-600 focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
        @error('fecha_nacimiento')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Dirección / Zona</label>
        <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" class="w-full border {{ $errors->has('direccion') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
        @error('direccion')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-slate-600 uppercase mb-1">SEXO</label>
        <select name="genero" class="w-full border {{ $errors->has('genero') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-colors" required>
            <option value="">SELECCIONAR SEXO DEL PACIENTE</option>
            <option value="MASCULINO" {{ old('genero') == 'MASCULINO' ? 'selected' : '' }}>MASCULINO</option>
            <option value="FEMENINO" {{ old('genero') == 'FEMENINO' ? 'selected' : '' }}>FEMENINO</option>
        </select>
        @error('genero')
            <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span>
        @enderror
    </div>

</div>
            <div class="flex justify-end gap-2 p-5 border-t bg-slate-50">
                <a href="{{ route('pacientes.index') }}" class="px-5 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg text-sm font-bold shadow-sm transition-colors text-center cursor-pointer">
                 Cerrar
              </a>
              <button type="submit" class="px-5 py-2 bg-[#007bff] hover:bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm transition-colors">
                Registrar
            </button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditar" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in">
        <div class="flex justify-between items-center p-5 border-b bg-slate-50">
            <h3 class="text-base font-bold text-slate-700">Editar Paciente</h3>
            <button onclick="document.getElementById('modalEditar').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold">&times;</button>
        </div>
        <form id="formEditar" method="POST" autocomplete="off">
            @csrf
            @method('PUT') 
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-bold text-slate-600 mb-1">CI</label><input type="text" name="ci" id="edit_ci" pattern="[0-9]+" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">Nombres</label><input type="text" name="nombre" id="edit_nombre" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">Apellido Paterno</label><input type="text" name="apellido_paterno" id="edit_paterno" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">Apellido Materno</label><input type="text" name="apellido_materno" id="edit_materno" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">Celular</label><input type="text" name="celular" id="edit_celular" pattern="[0-9]+" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">Correo Electrónico</label><input type="email" name="correo" id="edit_correo" pattern="[a-zA-Z0-9._%+-]+@gmail\.com" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">F. Nacimiento</label><input type="date" name="fecha_nacimiento" id="edit_fecha" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
                <div><label class="block text-sm font-bold text-slate-600 mb-1">Dirección</label><input type="text" name="direccion" id="edit_direccion" class="w-full border rounded-lg px-3 py-2 text-sm" required></div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-600 mb-1">Sexo</label>
                    <select name="genero" id="edit_genero" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                        <option value="MASCULINO">MASCULINO</option>
                        <option value="FEMENINO">FEMENINO</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-5 bg-slate-50 border-t">
                <button type="button" onclick="document.getElementById('modalEditar').classList.add('hidden')" class="bg-slate-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Cerrar</button>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalVer" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
        <div class="flex justify-between items-center p-5 border-b bg-slate-800 text-white">
            <h3 class="text-base font-bold"><i class="fa-solid fa-address-card mr-2"></i> Detalles del Paciente</h3>
            <button onclick="document.getElementById('modalVer').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <div class="p-6 space-y-4 text-sm text-slate-700">
            <p><strong>Paciente:</strong> <span id="ver_nombre_completo"></span></p>
            <p><strong>CI:</strong> <span id="ver_ci"></span></p>
            <p><strong>Celular:</strong> <span id="ver_celular"></span></p>
            <p><strong>Correo:</strong> <span id="ver_correo"></span></p>
            <p><strong>F. Nacimiento:</strong> <span id="ver_fecha"></span></p>
            <p><strong>Dirección:</strong> <span id="ver_direccion"></span></p>
            <p><strong>Sexo:</strong> <span id="ver_genero"></span></p>
        </div>
        <div class="flex justify-end p-5 bg-slate-50 border-t">
            <button onclick="document.getElementById('modalVer').classList.add('hidden')" class="bg-slate-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Cerrar</button>
        </div>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">¿Inactivar Paciente?</h3>
            <p class="text-sm text-slate-500">El expediente pasará a estado inactivo y registrará la hora exacta de la acción.</p>
        </div>
        <form id="formEliminar" method="POST" class="flex justify-center gap-3 p-5 bg-slate-50 border-t">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modalEliminar').classList.add('hidden')" class="bg-slate-300 text-slate-700 px-5 py-2 rounded-lg text-sm font-bold">Cancelar</button>
            <button type="submit" class="bg-red-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Sí, Inactivar</button>
        </form>
    </div>
</div>

<script>
    // Variables globales para el motor de paginación nativa
    let paginaActual = 1;
    const registrosPorPagina = 10;

    window.addEventListener('DOMContentLoaded', function() {
        const hoy = new Date();
        const anioLimite = hoy.getFullYear() - 18;
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');
        const fechaMaxima = `${anioLimite}-${mes}-${dia}`;
        
        document.getElementById('reg_fecha_nacimiento').setAttribute('max', fechaMaxima);
        if(document.getElementById('edit_fecha')) {
            document.getElementById('edit_fecha').setAttribute('max', fechaMaxima);
        }

        // Ejecutar primer filtrado y paginación inicial
        ejecutarFiltrosCombinados();
    });

    function resetearPaginaYFiltrar() {
        paginaActual = 1; 
        ejecutarFiltrosCombinados();
    }

    // --- SISTEMA INTERACTIVO DE BÚSQUEDA CRUZADA CON PAGINACIÓN NATIVA ---
    function ejecutarFiltrosCombinados() {
        const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
        const estadoBuscado = document.getElementById('filtroEstado').value;
        const generoBuscado = document.getElementById('filtroGenero').value;
        const fechaDesde = document.getElementById('filtroFechaDesde').value;
        const fechaHasta = document.getElementById('filtroFechaHasta').value;

        const filas = document.querySelectorAll('#cuerpoTablaPacientes tr');
        let filasFiltradas = [];

        filas.forEach(fila => {
            if (!fila.hasAttribute('data-busqueda')) return;

            const dataBusqueda = fila.getAttribute('data-busqueda');
            const dataEstado = fila.getAttribute('data-estado');
            const dataGenero = fila.getAttribute('data-genero');
            const dataFecha = fila.getAttribute('data-fecha');

            const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
            const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;
            const coincideGenero = generoBuscado === "TODOS" || dataGenero === generoBuscado;
            const coincideDesde = fechaDesde === "" || dataFecha >= fechaDesde;
            const coincideHasta = fechaHasta === "" || dataFecha <= fechaHasta;

            if (coincideTexto && coincideEstado && coincideGenero && coincideDesde && coincideHasta) {
                filasFiltradas.push(fila);
            } else {
                fila.style.display = "none"; 
            }
        });

        const totalRegistros = filasFiltradas.length;
        const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina) || 1;

        if (paginaActual > totalPaginas) paginaActual = totalPaginas;
        if (paginaActual < 1) paginaActual = 1;

        const indiceInicio = (paginaActual - 1) * registrosPorPagina;
        const indiceFin = indiceInicio + registrosPorPagina;

        filasFiltradas.forEach((fila, index) => {
            if (index >= indiceInicio && index < indiceFin) {
                fila.style.display = ""; 
            } else {
                fila.style.display = "none";
            }
        });

        renderizarControlesPaginacion(totalRegistros, totalPaginas, indiceInicio, indiceFin);
    }

    function renderizarControlesPaginacion(totalRegistros, totalPaginas, inicio, fin) {
        const info = document.getElementById('infoRegistros');
        if (totalRegistros === 0) {
            info.innerText = "Mostrando 0 a 0 de 0 registros";
        } else {
            info.innerText = `Mostrando ${inicio + 1} a ${Math.min(fin, totalRegistros)} de ${totalRegistros} registros`;
        }

        const contenedorControles = document.getElementById('paginacionControles');
        contenedorControles.innerHTML = "";

        let btnAnterior = document.createElement('button');
        btnAnterior.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
        btnAnterior.className = `px-3 py-1.5 rounded-lg border text-xs font-bold uppercase transition-all ${paginaActual === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white hover:bg-slate-50 text-slate-700 shadow-sm'}`;
        if (paginaActual > 1) {
            btnAnterior.onclick = function() { paginaActual--; ejecutarFiltrosCombinados(); };
        }
        contenedorControles.appendChild(btnAnterior);

        for (let i = 1; i <= totalPaginas; i++) {
            let btnPagina = document.createElement('button');
            btnPagina.innerText = i;
            btnPagina.className = `px-3 py-1.5 rounded-lg border text-xs font-bold transition-all shadow-sm ${paginaActual === i ? 'bg-blue-600 text-white border-blue-600' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
            btnPagina.onclick = function() { paginaActual = i; ejecutarFiltrosCombinados(); };
            contenedorControles.appendChild(btnPagina);
        }

        let btnSiguiente = document.createElement('button');
        btnSiguiente.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
        btnSiguiente.className = `px-3 py-1.5 rounded-lg border text-xs font-bold uppercase transition-all ${paginaActual === totalPaginas ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white hover:bg-slate-50 text-slate-700 shadow-sm'}`;
        if (paginaActual < totalPaginas) {
            btnSiguiente.onclick = function() { paginaActual++; ejecutarFiltrosCombinados(); };
        }
        contenedorControles.appendChild(btnSiguiente);
    }

    function limpiarFiltros() {
        document.getElementById('filtroTexto').value = "";
        document.getElementById('filtroEstado').value = "TODOS";
        document.getElementById('filtroGenero').value = "TODOS";
        document.getElementById('filtroFechaDesde').value = "";
        document.getElementById('filtroFechaHasta').value = "";
        resetearPaginaYFiltrar();
    }

    // --- REPORTERÍA A EXCEL NATIVA ---
    function exportarExcelNativo() {
        let tabla = document.getElementById("tablaMaestraPacientes");
        let csv = [];
        let filas = tabla.querySelectorAll("tr");
        
        let celdasHeader = filas[0].querySelectorAll("th");
        let headerData = [];
        for(let m = 0; m < 9; m++) {
            headerData.push('"' + celdasHeader[m].innerText.trim() + '"');
        }
        csv.push(headerData.join(";"));

        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaPacientes tr');
        cuerpoFilas.forEach(fila => {
            if (fila.style.display === "none" && !fila.hasAttribute('data-busqueda')) return;
            
            const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
            const estadoBuscado = document.getElementById('filtroEstado').value;
            const generoBuscado = document.getElementById('filtroGenero').value;
            const fechaDesde = document.getElementById('filtroFechaDesde').value;
            const fechaHasta = document.getElementById('filtroFechaHasta').value;

            const dataBusqueda = fila.getAttribute('data-busqueda');
            const dataEstado = fila.getAttribute('data-estado');
            const dataGenero = fila.getAttribute('data-genero');
            const dataFecha = fila.getAttribute('data-fecha');

            if (dataBusqueda) {
                const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
                const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;
                const coincideGenero = generoBuscado === "TODOS" || dataGenero === generoBuscado;
                const coincideDesde = fechaDesde === "" || dataFecha >= fechaDesde;
                const coincideHasta = fechaHasta === "" || dataFecha <= fechaHasta;

                if (coincideTexto && coincideEstado && coincideGenero && coincideDesde && coincideHasta) {
                    let filaData = [];
                    let celdas = fila.querySelectorAll("td");
                    for (let j = 0; j < 9; j++) {
                        let texto = celdas[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                        filaData.push('"' + texto + '"');
                    }
                    csv.push(filaData.join(";"));
                }
            }
        });
        
        let contenidoCSV = "\ufeff" + csv.join("\n");
        let blob = new Blob([contenidoCSV], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Reporte_Auditoria_Pacientes_" + new Date().toISOString().slice(0,10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.style.display = "none";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // --- REPORTERÍA A PDF / IMPRESIÓN ---
    function imprimirReporteNativo() {
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaPacientes tr');
        let ventanaImpresion = window.open('', '', 'height=800,width=1300');
        
        ventanaImpresion.document.write('<html><head><title>Reporte Clínico - Clínica Vitruvio</title>');
        ventanaImpresion.document.write('<style>');
        ventanaImpresion.document.write('body { font-family: Arial, sans-serif; padding: 25px; color: #0f172a; }');
        ventanaImpresion.document.write('h2 { text-transform: uppercase; font-size: 15px; margin-bottom: 4px; font-weight: bold; }');
        ventanaImpresion.document.write('p { font-size: 12px; color: #475569; margin-bottom: 20px; }');
        ventanaImpresion.document.write('table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; }');
        ventanaImpresion.document.write('th, td { border: 1px solid #94a3b8; padding: 8px; text-align: left; }');
        ventanaImpresion.document.write('th { background-color: #f8fafc; font-weight: bold; text-transform: uppercase; }');
        ventanaImpresion.document.write('</style></head><body>');
        ventanaImpresion.document.write('<h2>Clínica Vitruvio - Reporte de Auditoría de Pacientes</h2>');
        ventanaImpresion.document.write('<p>Variables filtradas y consolidadas. Emitido el: ' + new Date().toLocaleString() + '</p>');
        
        ventanaImpresion.document.write('<table>');
        ventanaImpresion.document.write('<thead>' + document.querySelector('#tablaMaestraPacientes thead tr').innerHTML + '</thead>');
        ventanaImpresion.document.write('<tbody>');

        cuerpoFilas.forEach(fila => {
            if (!fila.hasAttribute('data-busqueda')) return;

            const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
            const estadoBuscado = document.getElementById('filtroEstado').value;
            const generoBuscado = document.getElementById('filtroGenero').value;
            const fechaDesde = document.getElementById('filtroFechaDesde').value;
            const fechaHasta = document.getElementById('filtroFechaHasta').value;

            const dataBusqueda = fila.getAttribute('data-busqueda');
            const dataEstado = fila.getAttribute('data-estado');
            const dataGenero = fila.getAttribute('data-genero');
            const dataFecha = fila.getAttribute('data-fecha');

            const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
            const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;
            const coincideGenero = generoBuscado === "TODOS" || dataGenero === generoBuscado;
            const coincideDesde = fechaDesde === "" || dataFecha >= fechaDesde;
            const coincideHasta = fechaHasta === "" || dataFecha <= fechaHasta;

            if (coincideTexto && coincideEstado && coincideGenero && coincideDesde && coincideHasta) {
                ventanaImpresion.document.write('<tr>' + fila.innerHTML + '</tr>');
            }
        });

        ventanaImpresion.document.write('</tbody></table>');
        
        ventanaImpresion.document.write('<script>');
        ventanaImpresion.document.write('document.querySelectorAll("tr").forEach(tr => {');
        ventanaImpresion.document.write('  if(tr.children.length >= 11) {');
        ventanaImpresion.document.write('    tr.children[10].style.display = "none";'); 
        ventanaImpresion.document.write('    tr.children[9].style.display = "none";');  
        ventanaImpresion.document.write('  }');
        ventanaImpresion.document.write('});');
        ventanaImpresion.document.write('<\/script>');

        ventanaImpresion.document.write('</body></html>');
        ventanaImpresion.document.close();
        
        setTimeout(function() {
            ventanaImpresion.print();
            ventanaImpresion.close();
        }, 500);
    }

    // --- MODALES GESTIÓN ---
    function abrirModalVer(paciente) {
        document.getElementById('ver_nombre_completo').innerText = paciente.nombre + ' ' + paciente.apellido_paterno + ' ' + (paciente.apellido_materno || '');
        document.getElementById('ver_ci').innerText = paciente.ci;
        document.getElementById('ver_celular').innerText = paciente.celular || 'No registrado';
        document.getElementById('ver_correo').innerText = paciente.correo || 'No registrado';
        document.getElementById('ver_fecha').innerText = paciente.fecha_nacimiento;
        document.getElementById('ver_direccion').innerText = paciente.direccion;
        document.getElementById('ver_genero').innerText = paciente.genero;
        document.getElementById('modalVer').classList.remove('hidden');
    }

    function abrirModalEditar(paciente) {
        document.getElementById('edit_ci').value = paciente.ci;
        document.getElementById('edit_nombre').value = paciente.nombre;
        document.getElementById('edit_paterno').value = paciente.apellido_paterno;
        document.getElementById('edit_materno').value = paciente.apellido_materno;
        document.getElementById('edit_celular').value = paciente.celular;
        document.getElementById('edit_correo').value = paciente.correo;
        document.getElementById('edit_fecha').value = paciente.fecha_nacimiento;
        document.getElementById('edit_direccion').value = paciente.direccion;
        document.getElementById('edit_genero').value = paciente.genero;
        
        document.getElementById('formEditar').action = "/pacientes/" + paciente.id;
        document.getElementById('modalEditar').classList.remove('hidden');
    }

    function abrirModalEliminar(id) {
        document.getElementById('formEliminar').action = "/pacientes/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
    }
</script>
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Si Laravel detecta algún error de validación...
        @if($errors->any())
            // Reemplaza 'modalNuevoPaciente' con el ID real de tu ventana modal
            // Si usas Bootstrap:
            // $('#modalNuevoPaciente').modal('show');
            
            // Si usas Tailwind/JS puro (dependerá de cómo lo programaste):
            document.getElementById('modalNuevoPaciente').classList.remove('hidden');
        @endif
    });
</script>
@endpush
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Verifica si Laravel devolvió errores de validación
        @if($errors->any())
            // Simula un clic automático en el botón para volver a abrir el modal al instante
            let btnNuevo = document.getElementById('btnNuevoRegistro');
            if(btnNuevo) {
                btnNuevo.click();
            }
        @endif
    });
</script>
@endpush

@endsection
@extends('layouts.app')

@section('title', 'Gestión de Personal')

@section('content')

<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Mantenimiento de Personal Clínico</h2>
            <p class="text-slate-500 text-sm mt-0.5">Control de registros médicos, auditoría cronológica y reportería avanzada.</p>
        </div>
        <div class="text-sm font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg border">
            Inicio / Personal
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2 mb-4">
        <i class="fa-solid fa-circle-xmark text-red-500 text-base"></i> {{ session('error') }}
    </div>
    @endif

    <div class="bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-700 text-white space-y-4">
        <div class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-blue-400">
            <i class="fa-solid fa-filter"></i> Filtros de Auditoría para Reportes
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 border-b border-slate-600 pb-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Buscar Personal (C.I. / Nombre)</label>
                <input type="text" id="filtroTexto" oninput="resetearPaginaYFiltrar()" placeholder="Ej. 8374923 o Juan Perez..." class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Filtrar por Rol</label>
                <select id="filtroRol" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">TODOS LOS ROLES</option>
                    @foreach($roles as $rol)
                        <option value="{{ strtoupper($rol->nombre) }}">{{ strtoupper($rol->nombre) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Estado en Sistema</label>
                <select id="filtroEstado" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">AMBOS (Todos)</option>
                    <option value="ACTIVO">SOLO ACTIVOS</option>
                    <option value="INACTIVO">SOLO INACTIVOS</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Registrados Desde:</label>
                <input type="date" id="filtroDesde" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Registrados Hasta:</label>
                <input type="date" id="filtroHasta" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="lg:col-start-4">
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
            
            <button onclick="document.getElementById('modalRegistro').classList.remove('hidden'); document.getElementById('modalRegistro').classList.add('flex');" class="bg-[#dc3545] hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2 uppercase tracking-wide">
                <i class="fa-solid fa-plus text-sm"></i> Nuevo Registro
            </button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto whitespace-nowrap" id="tablaMaestraUsuarios">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <th class="p-3 font-bold text-center border-b border-r border-slate-200 w-8">#</th>
                        <th class="p-3 font-bold border-b border-r border-slate-200">Personal</th>
                        <th class="p-3 font-bold border-b border-r border-slate-200">CI</th>
                        <th class="p-3 font-bold border-b border-r border-slate-200">Perfil / Rol</th>
                        <th class="p-3 font-bold border-b border-r border-slate-200">Correo Electrónico</th>
                        <th class="p-3 font-bold border-b border-r border-slate-200">Celular</th>
                        <th class="p-3 font-bold border-b border-r border-slate-200">F. Nac. (Edad)</th>
                        <th class="p-3 font-bold text-center border-b border-r border-slate-200">Hora Registro</th>
                        <th class="p-3 font-bold text-center border-b border-r border-slate-200">Hora Inactivación</th>
                        <th class="p-3 font-bold text-center border-b border-r border-slate-200">Estado</th>
                        <th class="p-3 font-bold text-center border-b border-slate-200">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs" id="cuerpoTablaUsuarios">
                    @forelse($usuarios as $index => $usu)
                        @php
                            $fechaFiltro = $usu->creado_at ? \Carbon\Carbon::parse($usu->creado_at)->format('Y-m-d') : '';
                            $edad = $usu->fecha_nacimiento ? \Carbon\Carbon::parse($usu->fecha_nacimiento)->age . ' AÑOS' : 'S/N';
                            $fechaNacFormato = $usu->fecha_nacimiento ? \Carbon\Carbon::parse($usu->fecha_nacimiento)->format('d/m/Y') : 'S/N';
                            $estadoFiltro = $usu->trashed() ? 'INACTIVO' : 'ACTIVO';
                            $rolNombre = $usu->rol ? strtoupper($usu->rol->nombre) : 'SIN ROL';
                            $esMedico = $usu->personalMedico ? true : false;
                            $especialidadNombre = $esMedico && $usu->personalMedico->especialidad ? strtoupper($usu->personalMedico->especialidad->nombre) : '';
                            $textoBusqueda = strtolower($usu->nombre . ' ' . $usu->apellido_paterno . ' ' . $usu->ci . ' ' . $usu->correo);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $usu->trashed() ? 'bg-slate-50 opacity-60' : '' }}" 
                            data-busqueda="{{ $textoBusqueda }}" 
                            data-estado="{{ $estadoFiltro }}" 
                            data-rol="{{ $rolNombre }}" 
                            data-fecha="{{ $fechaFiltro }}">
                            
                            <td class="p-3 text-center text-slate-400 font-mono border-r border-slate-100 w-8">{{ $index + 1 }}</td>
                            
                            <td class="p-3 font-bold text-slate-800 uppercase border-r border-slate-100 break-words">
                                {{ $usu->nombre }} {{ $usu->apellido_paterno }} {{ $usu->apellido_materno }}
                            </td>
                            
                            <td class="p-3 font-mono text-slate-600 border-r border-slate-100">{{ $usu->ci ?? 'S/N' }}</td>
                            
                            <td class="p-3 border-r border-slate-100">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold {{ $usu->rol ? 'text-slate-600' : 'text-red-500' }} uppercase">
                                        <i class="fa-solid fa-user-shield mr-1 {{ $usu->rol ? 'text-slate-400' : 'text-red-400' }}"></i>{{ $rolNombre }}
                                    </span>
                                    @if($esMedico)
                                        <span class="text-[9px] text-emerald-600 font-bold uppercase mt-0.5">
                                            <i class="fa-solid fa-stethoscope mr-1"></i>{{ $especialidadNombre }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="p-3 border-r border-slate-100 break-words">
                                <span class="font-bold text-blue-700"><i class="fa-solid fa-envelope mr-1 text-blue-400"></i>{{ $usu->correo }}</span>
                            </td>
                            
                            <td class="p-3 font-mono text-slate-600 border-r border-slate-100">{{ $usu->celular ?? 'S/N' }}</td>
                            
                            <td class="p-3 border-r border-slate-100">
                                <div class="flex flex-col">
                                    <span class="text-slate-700">{{ $fechaNacFormato }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">{{ $edad }}</span>
                                </div>
                            </td>

                            <td class="p-3 text-slate-600 font-mono border-r border-slate-100 text-[11px] text-center">
                                {{ $usu->creado_at ? \Carbon\Carbon::parse($usu->creado_at)->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="p-3 border-r border-slate-100 text-center">
                                @if($usu->trashed())
                                    <span class="text-[10px] font-mono font-bold text-red-500 bg-red-50 px-2 py-1 rounded border border-red-100">
                                        {{ $usu->eliminado_at ? \Carbon\Carbon::parse($usu->eliminado_at)->format('d/m/Y H:i') : '-' }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic font-mono">-</span>
                                @endif
                            </td>

                            <td class="p-3 text-center border-r border-slate-100">
                                @if($usu->trashed())
                                    <form action="{{ route('usuarios.restore', $usu->id) }}" method="POST" id="form-restore-{{ $usu->id }}" class="m-0">
                                        @csrf
                                        <label class="relative inline-flex items-center cursor-pointer select-none">
                                            <input type="checkbox" class="sr-only peer" onchange="document.getElementById('form-restore-{{ $usu->id }}').submit()">
                                            <div class="w-8 h-4 bg-slate-300 rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all"></div>
                                        </label>
                                    </form>
                                @else
                                    <label class="relative inline-flex items-center cursor-not-allowed select-none opacity-90 m-0">
                                        <input type="checkbox" class="sr-only peer" checked disabled>
                                        <div class="w-8 h-4 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                @endif
                            </td>

                            <td class="p-3 text-center">
                                <div class="flex justify-center gap-1">
                                    <button onclick='abrirModalVer(@json($usu), @json($usu->rol), @json($usu->personalMedico), @json($usu->personalMedico ? $usu->personalMedico->especialidad : null))' class="bg-slate-500 hover:bg-slate-600 text-white p-1.5 rounded shadow-sm transition-colors" title="Ver Detalles">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                    </button>
                                    
                                    @if(!$usu->trashed())
                                        <button onclick='abrirModalEditar(@json($usu), @json($usu->personalMedico))' class="bg-[#007bff] hover:bg-blue-700 text-white p-1.5 rounded shadow-sm transition-colors" title="Editar Personal">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        </button>
                                        <button onclick="abrirModalEliminar({{ $usu->id }})" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded shadow-sm transition-colors" title="Inactivar Acceso">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('usuarios.force_delete', $usu->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('⚠️ ¡ADVERTENCIA EXTREMA!\n\n¿Estás absolutamente seguro de eliminar a este USUARIO FÍSICAMENTE?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-800 hover:bg-red-950 text-white p-1.5 rounded shadow-sm transition-colors" title="Destrucción Total (Borrado Físico)">
                                                <i class="fa-solid fa-fire-flame-curved text-[10px]"></i> 
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="filaTablaVacia">
                            <td colspan="11" class="p-8 text-center text-slate-400 font-medium">
                                <i class="fa-solid fa-users-slash text-3xl block mb-2 opacity-30"></i> No hay personal clínico registrado en estas fechas.
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

<div id="modalRegistro" class="fixed inset-0 z-[100] bg-slate-900/60 p-4 backdrop-blur-sm justify-center items-center {{ $errors->any() ? 'flex' : 'hidden' }}">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative animate-fade-in">
        
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-700">Registrar Nuevo Personal</h3>
            <a href="{{ route('usuarios.index') }}" class="text-slate-400 hover:text-red-500 text-2xl font-semibold leading-none cursor-pointer">&times;</a>
        </div>

        <form action="{{ route('usuarios.store') }}" method="POST" autocomplete="off" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4 mb-6">
                <div>
    <label class="block text-xs font-bold text-slate-600 mb-1">CI</label>
    <input type="text" name="ci" value="{{ old('ci') }}" placeholder="Solo números" pattern="[0-9]+" inputmode="numeric" class="w-full border {{ $errors->has('ci') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
    @error('ci') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span> @enderror
</div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Nombres</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Nombres del personal" class="w-full border {{ $errors->has('nombre') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    @error('nombre') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" placeholder="Apellido Paterno" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" placeholder="Apellido Materno" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Celular</label>
                    <input type="number" name="celular" value="{{ old('celular') }}" placeholder="Ej. 77712345" class="w-full border {{ $errors->has('celular') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    @error('celular') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="reg_fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="w-full border rounded-lg px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección del personal" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
            </div>

            <div class="text-center mb-4 border-t pt-4">
                <span class="text-sm font-black text-slate-600 uppercase tracking-widest px-4">Credenciales de Sistema</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Email de Acceso</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" placeholder="correo@clinica.com" class="w-full border {{ $errors->has('correo') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    @error('correo') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Contraseña</label>
                    <input type="password" name="contrasena" placeholder="Mínimo 6 caracteres" minlength="6" class="w-full border {{ $errors->has('contrasena') ? 'border-red-500 bg-red-50' : 'border-slate-200' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                    @error('contrasena') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Rol Asignado</label>
                    <select name="rol_id" id="reg_rol" onchange="verificarRolMedico('reg')" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                        <option value="">Seleccione el rol...</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>{{ strtoupper($rol->nombre) }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="reg_div_especialidad" class="hidden">
                    <label class="block text-xs font-bold text-blue-600 mb-1">Especialidad</label>
                    <select name="especialidad_id" id="reg_especialidad" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50">
                        <option value="">Seleccione especialidad...</option>
                        @foreach ($especialidades as $esp)
                            <option value="{{ $esp->id }}" {{ old('especialidad_id') == $esp->id ? 'selected' : '' }}>{{ strtoupper($esp->nombre) }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="reg_div_matricula" class="hidden">
                    <label class="block text-xs font-bold text-blue-600 mb-1">Matrícula Profesional</label>
                    <input type="text" name="matricula_profesional" id="reg_matricula" value="{{ old('matricula_profesional') }}" placeholder="Ej. MAT-12345" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50 uppercase">
                    @error('matricula_profesional') <span class="text-xs text-red-500 font-bold mt-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-slate-100 bg-slate-50 -mx-6 -mb-6 px-6 pb-6 rounded-b-xl">
                <a href="{{ route('usuarios.index') }}" class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-2 rounded-lg text-sm font-bold text-center transition-colors">Cerrar</a>
                <button type="submit" class="bg-[#007bff] hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold transition-colors">Registrar Personal</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditar" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-slate-100 animate-fade-in my-auto">
        
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-700">Editar Datos del Personal</h3>
            <button type="button" onclick="document.getElementById('modalEditar').classList.remove('flex'); document.getElementById('modalEditar').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>

        <form id="formEditar" method="POST" autocomplete="off" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-5">
                <div>
    <label class="block text-sm font-bold text-slate-600 mb-1">CI</label>
    <input type="text" name="ci" id="edit_ci" placeholder="Solo números" pattern="[0-9]+" inputmode="numeric" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
</div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Nombres</label>
                    <input type="text" name="nombre" id="edit_nombre" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" id="edit_paterno" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Apellido Materno</label>
                    <input type="text" name="apellido_materno" id="edit_materno" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Celular</label>
                    <input type="text" name="celular" id="edit_celular" placeholder="Ej. 77712345" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-600 mb-1">Dirección</label>
                    <input type="text" name="direccion" id="edit_direccion" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
                
                <div class="md:col-span-2 text-center mt-4 border-t pt-4">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Credenciales de Sistema</h3>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-600 mb-1">Email de Acceso</label>
                    <input type="email" name="correo" id="edit_correo" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Contraseña (Opcional)</label>
                    <input type="password" name="contrasena" placeholder="Dejar en blanco para mantener actual" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Rol Asignado</label>
                    <select name="rol_id" id="edit_rol" onchange="verificarRolMedico('edit')" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-slate-200" required>
                        <option value="">Seleccione el rol...</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">{{ strtoupper($rol->nombre) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div id="edit_div_especialidad" class="hidden">
                    <label class="block text-xs font-bold text-blue-600 mb-1">Especialidad</label>
                    <select name="especialidad_id" id="edit_especialidad" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50">
                        <option value="">Seleccione especialidad...</option>
                        @foreach ($especialidades as $esp)
                            <option value="{{ $esp->id }}">{{ strtoupper($esp->nombre) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div id="edit_div_matricula" class="hidden">
                    <label class="block text-xs font-bold text-blue-600 mb-1">Matrícula Profesional</label>
                    <input type="text" name="matricula_profesional" id="edit_matricula" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50 uppercase">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditar').classList.remove('flex'); document.getElementById('modalEditar').classList.add('hidden')" class="bg-slate-500 text-white px-6 py-2 rounded-lg text-sm font-bold">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="modalVer" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
        <div class="flex justify-between items-center p-5 border-b bg-slate-800 text-white">
            <h3 class="text-base font-bold"><i class="fa-solid fa-id-badge mr-2 text-blue-400"></i> Ficha del Personal</h3>
            <button onclick="document.getElementById('modalVer').classList.remove('flex'); document.getElementById('modalVer').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <div class="p-6 space-y-4 text-sm text-slate-700">
            <div class="text-center mb-4">
                <div class="w-20 h-20 bg-slate-200 rounded-full mx-auto flex items-center justify-center text-3xl text-slate-400 mb-2"><i class="fa-solid fa-user"></i></div>
                <p id="ver_nombre_completo" class="text-lg font-black text-slate-800 uppercase"></p>
                <p id="ver_ci" class="text-xs font-mono text-slate-500 mt-1"></p>
            </div>
            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-100">
                <div><p class="text-[10px] text-slate-400 font-bold uppercase">Rol de Sistema</p><p id="ver_rol" class="font-bold"></p></div>
                <div><p class="text-[10px] text-slate-400 uppercase font-bold">Correo</p><p id="ver_correo" class="font-bold text-blue-600"></p></div>
                <div><p class="text-[10px] text-slate-400 uppercase font-bold">Celular</p><p id="ver_celular"></p></div>
                <div><p class="text-[10px] text-slate-400 font-bold uppercase">Estado Actual</p><p id="ver_estado" class="font-bold"></p></div>
            </div>
            <div id="ver_seccion_medica" class="hidden bg-blue-50 p-4 rounded-lg border border-blue-100">
                <p class="text-xs font-black text-blue-600 uppercase tracking-wider mb-2"><i class="fa-solid fa-stethoscope mr-2"></i>Datos Médicos</p>
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-[10px] text-slate-400 font-bold uppercase">Especialidad</p><p id="ver_especialidad" class="font-bold text-blue-700"></p></div>
                    <div><p class="text-[10px] text-slate-400 font-bold uppercase">Matrícula</p><p id="ver_matricula" class="font-bold text-blue-700"></p></div>
                </div>
            </div>
        </div>
        <div class="flex justify-end p-5 bg-slate-50 border-t border-slate-200">
            <button onclick="document.getElementById('modalVer').classList.remove('flex'); document.getElementById('modalVer').classList.add('hidden')" class="bg-slate-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Cerrar Ficha</button>
        </div>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fa-solid fa-user-lock"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">¿Bloquear Acceso?</h3>
            <p class="text-sm text-slate-500">El usuario pasará a estado inactivo y no podrá iniciar sesión en el sistema.</p>
        </div>
        <form id="formEliminar" method="POST" class="flex justify-center gap-3 p-5 bg-slate-50 border-t">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modalEliminar').classList.remove('flex'); document.getElementById('modalEliminar').classList.add('hidden')" class="bg-slate-300 text-slate-700 px-5 py-2 rounded-lg text-sm font-bold">Cancelar</button>
            <button type="submit" class="bg-red-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Sí, Bloquear</button>
        </form>
    </div>
</div>

<script>
    let paginaActual = 1;
    const registrosPorPagina = 10;
    
    window.addEventListener('DOMContentLoaded', function() {
        const hoy = new Date();
        
        // Límite Máximo: 18 años atrás (No pueden ser menores de edad)
        const anioMax = hoy.getFullYear() - 18;
        const maxDate = `${anioMax}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
        
        // Límite Mínimo: 85 años atrás (Evita fechas absurdas como 1700)
        const anioMin = hoy.getFullYear() - 85;
        const minDate = `${anioMin}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;

        // Aplicamos los límites al input de Registro
        const regFecha = document.getElementById('reg_fecha_nacimiento');
        if (regFecha) {
            regFecha.setAttribute('max', maxDate);
            regFecha.setAttribute('min', minDate);
        }
        
        // Aplicamos los límites al input de Edición
        const editFecha = document.getElementById('edit_fecha_nacimiento');
        if (editFecha) {
            editFecha.setAttribute('max', maxDate);
            editFecha.setAttribute('min', minDate);
        }
        
        // Disparar validación si hay old() guardado
        @if($errors->any())
            verificarRolMedico('reg');
        @endif
    });
    ejecutarFiltrosCombinados();

    function verificarRolMedico(prefijo) {
        const selectRol = document.getElementById(prefijo + '_rol');
        const divEspecialidad = document.getElementById(prefijo + '_div_especialidad');
        const divMatricula = document.getElementById(prefijo + '_div_matricula');
        const inputEspecialidad = document.getElementById(prefijo + '_especialidad');
        const inputMatricula = document.getElementById(prefijo + '_matricula');
        
        let textoRol = "";
        if (selectRol && selectRol.selectedIndex !== -1) {
            textoRol = selectRol.options[selectRol.selectedIndex].text.toUpperCase();
        }
        
        if (textoRol.includes('MEDICO') || textoRol.includes('MÉDICO') || textoRol.includes('DOCTOR') || textoRol.includes('ESPECIALISTA')) {
            divEspecialidad.classList.remove('hidden');
            divMatricula.classList.remove('hidden');
            inputEspecialidad.required = true;
            inputMatricula.required = true;
        } else {
            divEspecialidad.classList.add('hidden');
            divMatricula.classList.add('hidden');
            inputEspecialidad.required = false;
            inputMatricula.required = false;
            inputEspecialidad.value = "";
            inputMatricula.value = "";
        }
    }

    function resetearPaginaYFiltrar() { 
        paginaActual = 1;
        ejecutarFiltrosCombinados(); 
    }

    function ejecutarFiltrosCombinados() {
        const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
        const estadoBuscado = document.getElementById('filtroEstado').value;
        const rolBuscado = document.getElementById('filtroRol').value;
        const fechaDesde = document.getElementById('filtroDesde').value;
        const fechaHasta = document.getElementById('filtroHasta').value;
        const filas = document.querySelectorAll('#cuerpoTablaUsuarios tr');
        
        let filasFiltradas = [];
        
        filas.forEach(fila => {
            if (!fila.hasAttribute('data-busqueda')) return;
            const dataBusqueda = fila.getAttribute('data-busqueda');
            const dataEstado = fila.getAttribute('data-estado');
            const dataFecha = fila.getAttribute('data-fecha');
            const dataRol = fila.getAttribute('data-rol');
            
            const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
            const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;
            const coincideRol = rolBuscado === "TODOS" || dataRol === rolBuscado;
            
            let coincideFecha = true;
            if (fechaDesde && dataFecha < fechaDesde) coincideFecha = false;
            if (fechaHasta && dataFecha > fechaHasta) coincideFecha = false;
            
            if (coincideTexto && coincideEstado && coincideRol && coincideFecha) {
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
            fila.style.display = (index >= indiceInicio && index < indiceFin) ? "table-row" : "none";
        });
        
        renderizarControlesPaginacion(totalRegistros, totalPaginas, indiceInicio, indiceFin);
    }

    function renderizarControlesPaginacion(totalRegistros, totalPaginas, inicio, fin) {
        document.getElementById('infoRegistros').innerText = totalRegistros === 0 ? "Mostrando 0 registros" : `Mostrando ${inicio + 1} a ${Math.min(fin, totalRegistros)} de ${totalRegistros} registros`;
        const controles = document.getElementById('paginacionControles');
        controles.innerHTML = "";
        
        let btnAnt = document.createElement('button');
        btnAnt.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
        btnAnt.className = `px-3 py-1.5 rounded-lg border text-xs font-bold transition-all ${paginaActual === 1 ? 'bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        if (paginaActual > 1) { 
            btnAnt.onclick = function() { paginaActual--; ejecutarFiltrosCombinados(); }; 
        }
        controles.appendChild(btnAnt);
        
        for (let i = 1; i <= totalPaginas; i++) {
            let btn = document.createElement('button');
            btn.innerText = i;
            btn.className = `px-3 py-1.5 rounded-lg border text-xs font-bold shadow-sm transition-all ${paginaActual === i ? 'bg-blue-600 text-white border-blue-600' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
            btn.onclick = function() { paginaActual = i; ejecutarFiltrosCombinados(); };
            controles.appendChild(btn);
        }
        
        let btnSig = document.createElement('button');
        btnSig.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
        btnSig.className = `px-3 py-1.5 rounded-lg border text-xs font-bold transition-all ${paginaActual === totalPaginas ? 'bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        if (paginaActual < totalPaginas) { 
            btnSig.onclick = function() { paginaActual++; ejecutarFiltrosCombinados(); }; 
        }
        controles.appendChild(btnSig);
    }

    function limpiarFiltros() {
        document.getElementById('filtroTexto').value = "";
        document.getElementById('filtroEstado').value = "TODOS";
        document.getElementById('filtroRol').value = "TODOS";
        document.getElementById('filtroDesde').value = "";
        document.getElementById('filtroHasta').value = "";
        resetearPaginaYFiltrar();
    }

    /* ACCIONES MODALES */
    function abrirModalVer(usuario, rol, personalMedico, especialidad) {
        document.getElementById('ver_nombre_completo').innerText = usuario.nombre + ' ' + usuario.apellido_paterno + ' ' + (usuario.apellido_materno || '');
        document.getElementById('ver_ci').innerText = 'CI: ' + (usuario.ci || 'S/N');
        document.getElementById('ver_rol').innerText = rol ? rol.nombre.toUpperCase() : 'NO ASIGNADO';
        document.getElementById('ver_correo').innerText = usuario.correo;
        document.getElementById('ver_celular').innerText = usuario.celular || 'No registrado';
        
        const estadoLabel = document.getElementById('ver_estado');
        if (usuario.eliminado_at) {
            estadoLabel.innerText = 'INACTIVO'; 
            estadoLabel.className = 'font-bold text-red-600';
        } else {
            estadoLabel.innerText = 'ACTIVO'; 
            estadoLabel.className = 'font-bold text-emerald-600';
        }
        
        const seccionMedica = document.getElementById('ver_seccion_medica');
        if (personalMedico) {
            seccionMedica.classList.remove('hidden');
            document.getElementById('ver_especialidad').innerText = especialidad ? especialidad.nombre.toUpperCase() : 'NO ASIGNADA';
            document.getElementById('ver_matricula').innerText = personalMedico.matricula_profesional || 'S/N';
        } else {
            seccionMedica.classList.add('hidden');
        }
        
        document.getElementById('modalVer').classList.remove('hidden');
        document.getElementById('modalVer').classList.add('flex');
    }

    function abrirModalEditar(usuario, personalMedico) {
        document.getElementById('edit_ci').value = usuario.ci || '';
        document.getElementById('edit_nombre').value = usuario.nombre;
        document.getElementById('edit_paterno').value = usuario.apellido_paterno;
        document.getElementById('edit_materno').value = usuario.apellido_materno || '';
        document.getElementById('edit_celular').value = usuario.celular || '';
        document.getElementById('edit_fecha_nacimiento').value = usuario.fecha_nacimiento ? usuario.fecha_nacimiento.split(' ')[0] : '';
        document.getElementById('edit_direccion').value = usuario.direccion || '';
        document.getElementById('edit_correo').value = usuario.correo;
        document.getElementById('edit_rol').value = usuario.rol_id || '';
        
        verificarRolMedico('edit');
        
        if (personalMedico) {
            document.getElementById('edit_especialidad').value = personalMedico.especialidad_id || '';
            document.getElementById('edit_matricula').value = personalMedico.matricula_profesional || '';
        } else {
            document.getElementById('edit_especialidad').value = '';
            document.getElementById('edit_matricula').value = '';
        }
        
        document.getElementById('formEditar').action = "{{ url('usuarios') }}/" + usuario.id;
        document.getElementById('modalEditar').classList.remove('hidden');
        document.getElementById('modalEditar').classList.add('flex');
    }

    function abrirModalEliminar(id) {
        document.getElementById('formEliminar').action = "{{ url('usuarios') }}/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
        document.getElementById('modalEliminar').classList.add('flex');
    }

    function exportarExcelNativo() {
        let csv = [];
        let headerData = ["#", "Personal", "CI", "Perfil y Rol", "Correo Electrónico", "Celular", "F. Nac. (Edad)", "Hora Registro", "Hora Inactivacion"];
        csv.push(headerData.join(";"));
        
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaUsuarios tr');
        cuerpoFilas.forEach(fila => {
            if (fila.style.display === "none" || !fila.hasAttribute('data-busqueda')) return;
            let filaData = [];
            let celdas = fila.querySelectorAll("td");
            if (celdas.length >= 9) {
                for (let j = 0; j <= 8; j++) {
                    let texto = celdas[j].innerText.replace(/(\n|\r)/gm, " ").trim();
                    filaData.push('"' + texto + '"');
                }
                csv.push(filaData.join(";"));
            }
        });
        
        let contenidoCSV = "\ufeff" + csv.join("\n");
        let blob = new Blob([contenidoCSV], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Auditoria_Personal_" + new Date().toISOString().slice(0, 10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    function imprimirReporteNativo() {
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaUsuarios tr');
        let ventanaImpresion = window.open('', '', 'height=800,width=1200');
        ventanaImpresion.document.write('<html><head><title>Auditoría de Personal</title>');
        ventanaImpresion.document.write('<style>body{font-family: Arial, sans-serif;padding:25px;color:#0f172a;}h2{text-transform:uppercase; font-size:15px;}table{width:100%;border-collapse:collapse;font-size:10px;margin-top:15px;}th, td{border:1px solid #94a3b8;padding:6px;text-align:left;} th{background-color:#f8fafc;} th:nth-last-child(1),td:nth-last-child(1),th:nth-last-child(2),td:nth-last-child(2){display:none;}</style>');
        ventanaImpresion.document.write('</head><body><h2>Clínica Vitruvio - Auditoría de Personal Clínico</h2><table><thead>' + document.querySelector('#tablaMaestraUsuarios thead tr').innerHTML + '</thead><tbody>');
        
        cuerpoFilas.forEach(fila => {
            if (fila.hasAttribute('data-busqueda') && fila.style.display !== "none") {
                ventanaImpresion.document.write('<tr>' + fila.innerHTML + '</tr>');
            }
        });
        
        ventanaImpresion.document.write('</tbody></table><script>document.querySelectorAll("tr").forEach(tr=>{if(tr.children.length>=10){tr.children[10].style.display="none"; tr.children[9].style.display="none";}});<\/script></body></html>');
        ventanaImpresion.document.close();
        setTimeout(function() { ventanaImpresion.print(); ventanaImpresion.close(); }, 500);
    }
</script>
@endsection
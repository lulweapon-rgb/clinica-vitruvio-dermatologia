@extends('layouts.app')
@section('title', 'Historiales Clínicos Dermatológicos')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-800 font-medium text-sm">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-5 rounded-md border border-slate-300 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Historias Clínicas</h2>
            <p class="text-slate-600 text-xs font-bold uppercase mt-0.5">Gestión de Historiales Médicos</p>
        </div>
        <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded border border-slate-300 uppercase">
            Módulo Médico / Historiales
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-600 text-red-900 p-4 shadow-sm text-sm font-semibold">
            <div class="font-bold flex items-center gap-2 uppercase"><i class="fa-solid fa-circle-xmark text-red-600"></i> Ocurrieron errores de validación al guardar:</div>
            <ul class="list-disc list-inside pl-2 text-xs mt-2">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
        <script>alert("ATENCIÓN: Existen errores en el formulario.\nRevise el recuadro rojo en la parte superior de la pantalla.");</script>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-600 text-red-900 p-4 shadow-sm text-sm font-semibold uppercase flex items-center gap-2 mb-4">
            <i class="fa-solid fa-circle-xmark text-red-600"></i> {{ session('error') }}
        </div>
        <script>alert("ERROR DEL SISTEMA:\n{{ session('error') }}");</script>
    @endif

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-900 p-4 shadow-sm text-sm font-semibold uppercase flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-slate-100 p-4 rounded-md border border-slate-300 space-y-4">
        <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-700 border-b border-slate-300 pb-2">
            <i class="fa-solid fa-filter"></i> Búsqueda y Filtrado de Expedientes
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Paciente (CI/Nombre)</label>
                <input type="text" id="filtroTexto" oninput="resetearPaginaYFiltrar()" placeholder="Ej. 8374923 o Juan..." class="w-full bg-white border border-slate-400 rounded-sm px-3 py-1.5 text-xs text-slate-800 outline-none focus:border-blue-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Médico Tratante</label>
                <select id="filtroMedico" onchange="resetearPaginaYFiltrar()" class="w-full bg-white border border-slate-400 rounded-sm px-3 py-1.5 text-xs text-slate-800 outline-none focus:border-blue-600 uppercase">
                    <option value="TODOS">- TODOS -</option>
                    @php $medicosConPacientes = $evaluaciones->pluck('medico')->filter()->unique('id'); @endphp
                    @foreach($medicosConPacientes as $med)
                        @if($med->usuario)
                            <option value="{{ $med->id }}">DR/A. {{ strtoupper($med->usuario->apellido_paterno) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Estado</label>
                <select id="filtroEstado" onchange="resetearPaginaYFiltrar()" class="w-full bg-white border border-slate-400 rounded-sm px-3 py-1.5 text-xs text-slate-800 outline-none focus:border-blue-600 uppercase">
                    <option value="TODOS">- AMBOS -</option>
                    <option value="ACTIVO">Activos</option>
                    <option value="INACTIVO">Anulados</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Desde</label>
                <input type="date" id="filtroDesde" onchange="resetearPaginaYFiltrar()" class="w-full bg-white border border-slate-400 rounded-sm px-3 py-1.5 text-xs text-slate-800 outline-none focus:border-blue-600">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Hasta</label>
                    <input type="date" id="filtroHasta" onchange="resetearPaginaYFiltrar()" class="w-full bg-white border border-slate-400 rounded-sm px-3 py-1.5 text-xs text-slate-800 outline-none focus:border-blue-600">
                </div>
                <button onclick="limpiarFiltros()" class="bg-slate-300 hover:bg-slate-400 text-slate-800 h-[30px] px-3 rounded-sm transition-colors" title="Limpiar Filtros"><i class="fa-solid fa-eraser"></i></button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-md border border-slate-300 shadow-sm overflow-hidden">
        <div class="p-3 border-b border-slate-300 flex flex-wrap justify-between items-center gap-4 bg-slate-50">
            <div class="flex items-center gap-2">
                <button onclick="exportarExcelNativo()" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-1.5 rounded-sm text-xs font-bold transition-all uppercase shadow-sm"><i class="fa-solid fa-file-excel mr-1"></i> Excel General</button>
                <button onclick="imprimirReporteNativo()" class="bg-red-700 hover:bg-red-800 text-white px-4 py-1.5 rounded-sm text-xs font-bold transition-all uppercase shadow-sm"><i class="fa-solid fa-file-pdf mr-1"></i> PDF General</button>
            </div>
            
            <button onclick="document.getElementById('modalRegistro').classList.remove('hidden'); document.getElementById('modalRegistro').classList.add('flex');" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-sm text-xs font-bold shadow-md transition-colors flex items-center gap-2 uppercase">
                <i class="fa-solid fa-file-medical"></i> Aperturar Historia Clínica
            </button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tablaMaestraEvaluaciones">
                <thead>
                    <tr class="bg-slate-200 text-slate-700 uppercase tracking-wider border-b border-slate-400 text-[10px]">
                        <th class="p-2 border-r border-slate-300 font-black text-center w-8">#</th>
                        <th class="p-2 border-r border-slate-300 font-black text-center">Apertura</th>
                        <th class="p-2 border-r border-slate-300 font-black">Paciente</th>
                        <th class="p-2 border-r border-slate-300 font-black">Morfología / Ubicación</th>
                        <th class="p-2 border-r border-slate-300 font-black text-center">Foto Adjunta</th>
                        <th class="p-2 border-r border-slate-300 font-black text-center">Estado</th>
                        <th class="p-2 border-slate-300 font-black text-center w-32">Gestión CRUD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800 text-xs" id="cuerpoTablaEvaluaciones">
                    @forelse($evaluaciones as $index => $eval)
                    @php
                        $fechaHora = $eval->creado_at ? \Carbon\Carbon::parse($eval->creado_at)->format('d/m/Y H:i') : 'S/N';
                        $fechaFiltro = $eval->creado_at ? \Carbon\Carbon::parse($eval->creado_at)->format('Y-m-d') : '';
                        $estadoFiltro = $eval->trashed() ? 'INACTIVO' : 'ACTIVO';
                        $ciPaciente = $eval->paciente->ci ?? 'S/N';
                        $nombrePacienteCompleto = ($eval->paciente->nombre ?? '') . ' ' . ($eval->paciente->apellido_paterno ?? '');
                        $nombreMedico = ($eval->medico && $eval->medico->usuario) ? strtoupper($eval->medico->usuario->nombre . ' ' . $eval->medico->usuario->apellido_paterno) : 'S/N';
                        $urlImagen = asset('storage/' . $eval->imagen_lesion);
                        
                        // Combinamos CI y Nombre para que el buscador encuentre a ambos
                        $textoBusqueda = strtolower($ciPaciente . ' ' . $nombrePacienteCompleto);
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $eval->trashed() ? 'bg-slate-100 opacity-60' : '' }}" data-busqueda="{{ $textoBusqueda }}" data-estado="{{ $estadoFiltro }}" data-medico="{{ $eval->medico_id }}" data-fecha="{{ $fechaFiltro }}">
                        
                        <td class="p-2 text-center text-slate-500 font-mono border-r border-slate-200">{{ $index + 1 }}</td>
                        <td class="p-2 font-mono font-bold text-slate-700 border-r border-slate-200 text-center">{{ $fechaHora }}</td>
                        
                        <td class="p-2 border-r border-slate-200 uppercase">
                            <div class="font-bold text-slate-800">{{ $nombrePacienteCompleto ?: 'ELIMINADO' }}</div>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">CI: {{ $ciPaciente }}</div>
                            <div class="text-[9px] text-slate-500 font-bold mt-1">DR/A. {{ $nombreMedico }}</div>
                        </td>

                        <td class="p-2 border-r border-slate-200 uppercase">
                            <div class="font-bold text-slate-900 text-[10px]">{{ $eval->tipo_lesion_morfologia ?? 'S/N' }}</div>
                            <div class="text-[9px] text-slate-600 mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i>{{ $eval->ubicacion_anatomica ?? 'S/N' }}</div>
                        </td>
                        
                        <td class="p-2 text-center border-r border-slate-200">
                            <img src="{{ $urlImagen }}" alt="Lesión" class="w-10 h-10 object-cover mx-auto border border-slate-400 shadow-sm rounded-sm hover:scale-150 transition-transform cursor-pointer">
                        </td>
                        
                        <td class="p-2 text-center border-r border-slate-200">
                            @if($eval->trashed())
                                <span class="bg-red-200 text-red-800 text-[9px] px-2 py-0.5 font-bold uppercase border border-red-300">Anulada</span>
                            @else
                                @if($eval->estado_validacion === 'PENDIENTE_ESPECIALISTA')
                                    <span class="bg-amber-200 text-amber-800 text-[9px] px-2 py-0.5 font-bold uppercase border border-amber-300">En Revisión</span>
                                @elseif($eval->estado_validacion === 'RESUELTO_REMOTO')
                                    <span class="bg-emerald-200 text-emerald-800 text-[9px] px-2 py-0.5 font-bold uppercase border border-emerald-300">Resuelto</span>
                                @elseif($eval->estado_validacion === 'DERIVADO_PRESENCIAL')
                                    <span class="bg-orange-200 text-orange-800 text-[9px] px-2 py-0.5 font-bold uppercase border border-orange-300">Derivado</span>
                                @else
                                    <span class="bg-emerald-200 text-emerald-800 text-[9px] px-2 py-0.5 font-bold uppercase border border-emerald-300">Activo</span>
                                @endif
                            @endif
                        </td>
                        
                        <td class="p-2 text-center">
                            <div class="flex justify-center items-center gap-1">
                                <button onclick='abrirModalVer(@json($eval), @json($eval->paciente), @json($eval->paciente->antecedentes ?? null), @json($eval->medico), "{{ $urlImagen }}")' class="bg-slate-600 hover:bg-slate-700 text-white px-2 py-1.5 rounded-sm transition-colors shadow-sm" title="Ver Expediente (Imprimir)"><i class="fa-solid fa-folder-open text-[10px]"></i></button>
                                
                                @if(!$eval->trashed())
                                    <button onclick='abrirModalEditar(@json($eval), @json($eval->paciente->antecedentes ?? null))' class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1.5 rounded-sm transition-colors shadow-sm" title="Editar"><i class="fa-solid fa-pen text-[10px]"></i></button>
                                    <button onclick="abrirModalEliminar({{ $eval->id }})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1.5 rounded-sm transition-colors shadow-sm" title="Eliminar"><i class="fa-solid fa-trash text-[10px]"></i></button>
                                @else
                                    <form action="{{ route('evaluaciones.restore', $eval->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-2 py-1.5 rounded-sm transition-colors shadow-sm" title="Restaurar"><i class="fa-solid fa-rotate-left text-[10px]"></i></button>
                                    </form>
                                    <form action="{{ route('evaluaciones.force_delete', $eval->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Destruir físicamente de la base de datos?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-800 hover:bg-red-900 text-white px-2 py-1.5 rounded-sm transition-colors shadow-sm" title="Destruir Total"><i class="fa-solid fa-fire text-[10px]"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="filaTablaVacia">
                        <td colspan="7" class="p-8 text-center text-slate-500 font-bold uppercase text-xs">No hay historiales registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 bg-slate-100 border-t border-slate-300 flex justify-between items-center text-[10px] font-bold uppercase text-slate-600">
            <div id="infoRegistros"></div>
            <div id="paginacionControles" class="flex gap-1"></div>
        </div>
    </div>
</div>

<div id="modalRegistro" class="fixed inset-0 z-[100] bg-slate-900/80 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white w-full max-w-4xl border border-slate-500 shadow-2xl animate-fade-in my-auto max-h-[95vh] flex flex-col">
        
        <div class="bg-slate-800 text-white p-4 flex justify-between items-center sticky top-0 z-20 border-b-4 border-slate-600">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-hospital text-3xl opacity-80"></i>
                <div>
                    <h2 class="text-xl font-black uppercase tracking-widest leading-tight">Historia Clínica Dermatológica</h2>
                    <p class="text-[10px] text-slate-300 uppercase tracking-widest">Documento Médico Oficial - Clínica Vitruvio</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalRegistro').classList.remove('flex'); document.getElementById('modalRegistro').classList.add('hidden')" class="text-slate-300 hover:text-white text-3xl font-bold leading-none">&times;</button>
        </div>
        
        <form action="{{ route('evaluaciones.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="flex-1 overflow-y-auto bg-slate-100 p-6">
            @csrf
            
            <div class="bg-white border border-slate-400 shadow-md">
                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1.5 font-black text-xs uppercase text-slate-800 tracking-wide">I. Datos Demográficos y Filiación</div>
                <div class="grid grid-cols-2 divide-x border-b border-slate-400">
                    <div class="p-3">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Paciente / Nombres y Apellidos</label>
                        <select name="paciente_id" class="w-full bg-transparent border-b border-slate-300 text-xs font-bold text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                            <option value="">Seleccione...</option>
                            @foreach ($pacientes as $pac)
                                <option value="{{ $pac->id }}">{{ $pac->ci }} - {{ strtoupper($pac->nombre . ' ' . $pac->apellido_paterno) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-3">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Médico Tratante</label>
                        <select name="medico_id" class="w-full bg-transparent border-b border-slate-300 text-xs font-bold text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                            <option value="">Seleccione al médico...</option>
                            @foreach($medicos as $med)
                                @if($med->usuario)
                                    <option value="{{ $med->id }}" {{ (auth()->id() == $med->usuario_id) ? 'selected' : '' }}>
                                        DR/A. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1.5 font-black text-xs uppercase text-slate-800 tracking-wide">II. Motivo de la Consulta y Enfermedad Actual</div>
                <div class="grid grid-cols-12 divide-x border-b border-slate-400">
                    <div class="col-span-9 p-3">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Descripción del cuadro (Anamnesis)</label>
                        <input type="text" name="motivo_consulta" placeholder="Razones por las que acude..." class="w-full bg-transparent border-b border-slate-300 text-xs font-semibold text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                    </div>
                    <div class="col-span-3 p-3">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Duración del cuadro</label>
                        <input type="text" name="duracion_lesion" placeholder="Ej: 3 semanas" class="w-full bg-transparent border-b border-slate-300 text-xs font-semibold text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1.5 font-black text-xs uppercase text-slate-800 tracking-wide">III. Antecedentes Personales y Familiares</div>
                <div class="p-3 border-b border-slate-400 space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Enfermedades previas, alergias, operaciones</label>
                            <input type="text" name="enfermedades_piel_previas" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Patologías Familiares (Diabetes, Hipertensión)</label>
                            <input type="text" name="otras_patologias_familiares" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Exposición a tóxicos o exposición solar frecuente</label>
                            <input type="text" name="exposicion_sol" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600">
                        </div>
                        <div class="flex gap-4 items-end pb-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="quemaduras_previas" class="w-4 h-4 text-blue-600">
                                <span class="text-[10px] font-bold uppercase text-slate-700">Quemaduras Graves</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="historial_familiar_melanoma" class="w-4 h-4 text-red-600">
                                <span class="text-[10px] font-bold uppercase text-slate-700">Melanoma Familiar</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1.5 font-black text-xs uppercase text-slate-800 tracking-wide">IV. Síntomas y Factores Asociados</div>
                <div class="p-3 border-b border-slate-400 bg-slate-50 grid grid-cols-12 gap-4">
                    <div class="col-span-5">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-2">Síntomas Locales de la Lesión</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2"><input type="checkbox" name="sintoma_dolor" class="w-4 h-4 text-blue-600"><span class="text-[10px] font-bold uppercase text-slate-700">Dolor</span></label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="sintoma_prurito" class="w-4 h-4 text-blue-600"><span class="text-[10px] font-bold uppercase text-slate-700">Prurito (Picor)</span></label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="sintoma_parestesia" class="w-4 h-4 text-blue-600"><span class="text-[10px] font-bold uppercase text-slate-700">Parestesia</span></label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="sintoma_sangrado" class="w-4 h-4 text-red-600"><span class="text-[10px] font-bold uppercase text-slate-700">Sangrado</span></label>
                        </div>
                    </div>
                    <div class="col-span-7 space-y-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Síntomas Sistémicos (Fiebre, fatiga, malestar, pérdida de peso)</label>
                            <input type="text" name="otros_sintomas_sistemicos" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Relación con Factores Externos (Clima, alimentos, etc.)</label>
                            <input type="text" name="factores_externos" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1.5 font-black text-xs uppercase text-slate-800 tracking-wide">V. Exploración Física y Visual de la Piel</div>
                <div class="p-3 border-b border-slate-400 grid grid-cols-3 gap-x-4 gap-y-3">
                    <div class="col-span-3">
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Aspecto del Paciente y Estado General</label>
                        <input type="text" name="aspecto_general" placeholder="Ej. Afección crónica localizada..." class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Ubicación y Simetría</label>
                        <input type="text" name="ubicacion_anatomica" placeholder="Rostro, cuello, extremidades..." class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Tipo de Lesión (Morfología)</label>
                        <select name="tipo_lesion_morfologia" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                            <option value="Mácula">Mácula</option><option value="Pápula">Pápula</option><option value="Nódulo">Nódulo</option>
                            <option value="Placa">Placa</option><option value="Úlcera">Úlcera</option><option value="Ampolla">Ampolla</option>
                            <option value="Costra">Costra</option><option value="Quiste">Quiste</option><option value="Atrofia">Atrofia</option>
                            <option value="Cicatriz">Cicatriz</option><option value="Descamación">Descamación</option><option value="Esclerosis">Esclerosis</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Coloración de la Lesión</label>
                        <select name="coloracion_lesion" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                            <option value="Hiperpigmentada / Morena">Hiperpigmentada / Morena</option><option value="Roja">Roja</option>
                            <option value="Pálida">Pálida</option><option value="Verdosa">Verdosa</option><option value="Amarillo paja">Amarillo paja</option>
                            <option value="Cianótica (Azulada)">Cianótica (Azulada)</option><option value="Gris pizarrosa">Gris pizarrosa</option>
                            <option value="Cloasma">Cloasma</option><option value="Hipopigmentada">Hipopigmentada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Palpación (Consistencia, sensibilidad)</label>
                        <input type="text" name="consistencia_palpacion" placeholder="Blanda, firme..." class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Disposición de Lesiones</label>
                        <select name="disposicion_lesiones" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                            <option value="Agrupadas">Agrupadas</option><option value="Diseminadas">Diseminadas</option><option value="Única">Única</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Distribución (Extensión)</label>
                        <input type="text" name="distribucion_lesiones" class="w-full bg-transparent border-b border-slate-300 text-xs text-slate-800 outline-none pb-1 focus:border-blue-600" required>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1.5 font-black text-xs uppercase text-slate-800 tracking-wide">VI. Evidencia Fotográfica y Análisis Triage (IA CNN)</div>
                <div class="p-4 bg-slate-50">
                    <div class="border border-slate-400 bg-white p-4 flex justify-between items-center">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2"><i class="fa-solid fa-camera"></i> Adjuntar Fotografía Dermatoscópica</label>
                            <p class="text-[10px] text-slate-500 mb-3">La imagen será enviada automáticamente a la bandeja algorítmica para análisis de riesgo.</p>
                            <input type="file" name="imagen_lesion" accept="image/*" onchange="previewImage(event)" required class="text-xs w-full">
                        </div>
                        <div class="w-32 h-32 border border-slate-300 bg-slate-100 flex items-center justify-center text-slate-300 text-xs text-center p-2 relative">
                            <span id="upload_placeholder">VISTA PREVIA</span>
                            <img id="image_preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalRegistro').classList.add('hidden')" class="border border-slate-400 text-slate-700 px-6 py-2 bg-white text-xs font-bold uppercase hover:bg-slate-100">Cancelar</button>
                <button type="submit" class="bg-slate-800 text-white px-8 py-2 text-xs font-bold uppercase tracking-wider hover:bg-slate-900 border border-slate-900 shadow-sm"><i class="fa-solid fa-save mr-2"></i> Registrar Historia Clínica</button>
            </div>
        </form>
    </div>
</div>

<div id="modalVer" class="fixed inset-0 z-[100] bg-slate-900/80 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white w-full max-w-4xl border border-slate-500 shadow-2xl animate-fade-in my-auto max-h-[95vh] flex flex-col" id="contenedorFichaImprimible">
        
        <div class="bg-slate-800 text-white p-4 flex justify-between items-center sticky top-0 z-20 border-b-4 border-slate-600 no-print">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-hospital text-3xl opacity-80"></i>
                <div>
                    <h2 class="text-xl font-black uppercase tracking-widest leading-tight">Historia Clínica Dermatológica</h2>
                    <p class="text-[10px] text-slate-300 uppercase tracking-widest">Documento Médico Oficial - Vista de Lectura</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="exportarFichaExcel()" class="bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-sm text-xs font-bold transition-colors uppercase shadow-sm"><i class="fa-solid fa-file-excel mr-1"></i> Bajar Excel</button>
                <button onclick="document.getElementById('modalVer').classList.add('hidden')" class="text-slate-300 hover:text-white text-3xl font-bold leading-none ml-4">&times;</button>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto bg-slate-100 p-6">
            <div class="bg-white border border-slate-400 shadow-md">
                
                <div class="p-4 border-b border-slate-400 flex justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Paciente</p>
                        <h3 class="text-lg font-black uppercase text-slate-800" id="ver_paciente"></h3>
                        <p class="text-xs font-bold text-slate-600 uppercase" id="ver_medico"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Fecha Apertura</p>
                        <p class="text-sm font-mono font-bold text-slate-800" id="ver_fecha"></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-2">Estado</p>
                        <p class="text-xs font-bold uppercase" id="ver_estado"></p>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1 font-black text-[10px] uppercase text-slate-700 tracking-wide">Anamnesis y Antecedentes</div>
                <div class="p-4 border-b border-slate-400 text-xs grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-bold uppercase text-slate-500 mb-1 border-b pb-1 text-[9px]">Enfermedad Actual</p>
                        <p><strong>Motivo:</strong> <span id="ver_motivo"></span></p>
                        <p><strong>Duración:</strong> <span id="ver_duracion"></span></p>
                        <p><strong>Síntomas Locales:</strong> <span id="ver_triage_sintomas" class="font-bold text-red-600"></span></p>
                        <p><strong>Sínt. Sistémicos:</strong> <span id="ver_sistemicos"></span></p>
                    </div>
                    <div>
                        <p class="font-bold uppercase text-slate-500 mb-1 border-b pb-1 text-[9px]">Antecedentes Base</p>
                        <ul class="space-y-1" id="ver_antecedentes_lista"></ul>
                        <p class="mt-2"><strong>Factores Externos:</strong> <span id="ver_externos"></span></p>
                    </div>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1 font-black text-[10px] uppercase text-slate-700 tracking-wide">Exploración Física</div>
                <div class="p-4 border-b border-slate-400 text-xs">
                    <p class="mb-2 text-slate-700"><strong>Aspecto General:</strong> <span id="ver_aspecto" class="italic"></span></p>
                    <table class="w-full text-left border border-slate-300">
                        <tr class="divide-x divide-slate-300 border-b border-slate-300 bg-slate-50">
                            <td class="p-1 px-2"><strong>Ubicación:</strong> <span id="ver_ubicacion"></span></td>
                            <td class="p-1 px-2"><strong>Morfología:</strong> <span id="ver_morfologia"></span></td>
                        </tr>
                        <tr class="divide-x divide-slate-300 border-b border-slate-300">
                            <td class="p-1 px-2"><strong>Coloración:</strong> <span id="ver_coloracion"></span></td>
                            <td class="p-1 px-2"><strong>Consistencia:</strong> <span id="ver_consistencia"></span></td>
                        </tr>
                        <tr class="divide-x divide-slate-300 bg-slate-50">
                            <td class="p-1 px-2"><strong>Disposición:</strong> <span id="ver_disposicion"></span></td>
                            <td class="p-1 px-2"><strong>Distribución:</strong> <span id="ver_distribucion"></span></td>
                        </tr>
                    </table>
                </div>

                <div class="bg-slate-200 border-b border-slate-400 px-3 py-1 font-black text-[10px] uppercase text-slate-700 tracking-wide">Dictamen Especializado</div>
                <div class="p-4">
                    <p id="ver_diagnostico" class="text-sm font-bold text-slate-800 italic"></p>
                </div>

            </div>
            
            <div class="mt-4 flex justify-end no-print">
                <button onclick="imprimirFichaNativa()" class="bg-red-700 text-white px-8 py-2 text-xs font-bold uppercase hover:bg-red-800 shadow-sm"><i class="fa-solid fa-file-pdf mr-1"></i> Imprimir a PDF</button>
            </div>
        </div>
    </div>
</div>

<div id="modalEditar" class="fixed inset-0 z-[100] bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden border border-slate-100 animate-fade-in my-auto max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50 sticky top-0 z-10">
            <h3 class="text-base font-bold text-slate-700 uppercase tracking-tight">Editar Datos del Historial</h3>
            <button type="button" onclick="document.getElementById('modalEditar').classList.remove('flex'); document.getElementById('modalEditar').classList.add('hidden');" class="text-slate-400 hover:text-red-500 text-2xl font-semibold leading-none">&times;</button>
        </div>
        
        <form id="formEditar" method="POST" enctype="multipart/form-data" autocomplete="off" class="flex-1 overflow-y-auto p-6 flex flex-col space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider border-b pb-1">1. Asignaciones</p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Médico Responsable</label>
                    <select name="medico_id" id="edit_medico" class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 shadow-sm" required>
                        @foreach($medicos as $med)
                            @if($med->usuario)
                                <option value="{{ $med->id }}">Dr/a. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
                
            <div class="space-y-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider border-b pb-1">2. Antecedentes Personales</p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Exposición Solar Base</label>
                    <input type="text" name="exposicion_sol" id="edit_sol" class="w-full border rounded-lg px-3 py-2 text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Alergias / Enfermedades Previas</label>
                    <input type="text" name="enfermedades_piel_previas" id="edit_enfermedades" class="w-full border rounded-lg px-3 py-2 text-xs bg-slate-50">
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider border-b pb-1">3. Exploración Física</p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Ubicación Anatómica</label>
                    <input type="text" name="ubicacion_anatomica" id="edit_ubicacion" required class="w-full border rounded-lg px-3 py-2 text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Tipo de Lesión (Morfología)</label>
                    <input type="text" name="tipo_lesion_morfologia" id="edit_morfologia" required class="w-full border rounded-lg px-3 py-2 text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-blue-600 mb-1"><i class="fa-solid fa-camera mr-1"></i> Reemplazar Fotografía (Opcional)</label>
                    <input type="file" name="imagen_lesion" accept="image/*" class="w-full border border-blue-200 rounded-lg px-3 py-1.5 text-xs bg-blue-50 text-blue-700">
                </div>
            </div>
            
            <div class="sticky bottom-0 flex justify-end gap-2 pt-4 border-t border-slate-100 bg-white">
                <button type="button" onclick="document.getElementById('modalEditar').classList.remove('flex'); document.getElementById('modalEditar').classList.add('hidden');" class="bg-slate-300 text-slate-700 px-6 py-2 rounded-lg text-xs font-bold uppercase transition-colors hover:bg-slate-400">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-xs font-bold uppercase shadow-sm transition-colors">Guardar Modificaciones</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 z-[100] bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fa-solid fa-file-circle-xmark"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">¿Enviar a Papelera?</h3>
            <p class="text-sm text-slate-500">Este registro pasará al estado inactivo.</p>
        </div>
        <form id="formEliminar" method="POST" class="flex justify-center gap-3 p-5 bg-slate-50 border-t">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modalEliminar').classList.remove('flex'); document.getElementById('modalEliminar').classList.add('hidden');" class="bg-slate-300 text-slate-700 px-5 py-2 rounded-lg text-sm font-bold transition-colors hover:bg-slate-400">Cancelar</button>
            <button type="submit" class="bg-red-500 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-red-600 transition-colors">Sí, Anular</button>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image_preview');
            output.src = reader.result;
            output.classList.remove('hidden');
            document.getElementById('upload_placeholder').classList.add('hidden');
        };
        if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
    }

    function abrirModalVer(evaluacion, paciente, antecedentes, medico, urlImagen) {
        document.getElementById('ver_paciente').innerText = paciente ? paciente.ci + ' - ' + paciente.nombre + ' ' + (paciente.apellido_paterno || '') : 'ELIMINADO';
        document.getElementById('ver_medico').innerText = (medico && medico.usuario) ? 'Médico: DR/A. ' + medico.usuario.apellido_paterno : 'No Asignado';
        
        document.getElementById('ver_fecha').innerText = new Date(evaluacion.creado_at).toLocaleDateString('es-ES');
        document.getElementById('ver_diagnostico').innerText = evaluacion.diagnostico_clinico || 'Sin dictamen emitido.';
        
        document.getElementById('ver_motivo').innerText = evaluacion.motivo_consulta || 'S/N';
        document.getElementById('ver_duracion').innerText = evaluacion.duracion_lesion || 'S/N';
        document.getElementById('ver_sistemicos').innerText = evaluacion.otros_sintomas_sistemicos || 'Ninguno';
        document.getElementById('ver_externos').innerText = evaluacion.factores_externos || 'S/N';
        document.getElementById('ver_aspecto').innerText = evaluacion.aspecto_general || 'S/N';
        
        document.getElementById('ver_ubicacion').innerText = evaluacion.ubicacion_anatomica || 'S/N';
        document.getElementById('ver_morfologia').innerText = evaluacion.tipo_lesion_morfologia || 'S/N';
        document.getElementById('ver_coloracion').innerText = evaluacion.coloracion_lesion || 'S/N';
        document.getElementById('ver_consistencia').innerText = evaluacion.consistencia_palpacion || 'S/N';
        document.getElementById('ver_disposicion').innerText = evaluacion.disposicion_lesiones || 'S/N';
        document.getElementById('ver_distribucion').innerText = evaluacion.distribucion_lesiones || 'S/N';

        const boxAntecedentes = document.getElementById('ver_antecedentes_lista');
        boxAntecedentes.innerHTML = "";
        if (antecedentes) {
            boxAntecedentes.innerHTML += `<li>- Enf. Previas: ${antecedentes.enfermedades_piel_previas || 'No'}</li>`;
            boxAntecedentes.innerHTML += `<li>- Familiares: ${antecedentes.otras_patologias_familiares || 'No'}</li>`;
            boxAntecedentes.innerHTML += `<li>- Exp. Solar: ${antecedentes.exposicion_sol || 'No'}</li>`;
            boxAntecedentes.innerHTML += `<li>- Alertas Genéticas: ${antecedentes.historial_familiar_melanoma ? 'Melanoma familiar' : 'Ninguna'}</li>`;
        } else {
            boxAntecedentes.innerHTML = `<li>Sin antecedentes capturados.</li>`;
        }

        let sintomas = [];
        if(evaluacion.sintoma_prurito === true || evaluacion.sintoma_prurito === 1 || evaluacion.sintoma_prurito === '1' || evaluacion.sintoma_prurito === 'SI') sintomas.push('Prurito');
        if(evaluacion.sintoma_dolor === true || evaluacion.sintoma_dolor === 1 || evaluacion.sintoma_dolor === '1' || evaluacion.sintoma_dolor === 'SI') sintomas.push('Dolor');
        if(evaluacion.sintoma_sangrado === true || evaluacion.sintoma_sangrado === 1 || evaluacion.sintoma_sangrado === '1' || evaluacion.sintoma_sangrado === 'SI') sintomas.push('Sangrado');
        if(evaluacion.sintoma_parestesia === true || evaluacion.sintoma_parestesia === 1 || evaluacion.sintoma_parestesia === '1' || evaluacion.sintoma_parestesia === 'SI') sintomas.push('Parestesia');
        
        document.getElementById('ver_triage_sintomas').innerText = sintomas.length > 0 ? sintomas.join(', ') : 'Ninguno reportado';

        const estado = document.getElementById('ver_estado');
        estado.innerText = evaluacion.estado_validacion;
        
        document.getElementById('modalVer').classList.remove('hidden');
        document.getElementById('modalVer').classList.add('flex');
    }

    function abrirModalEditar(evaluacion, antecedentes) {
        document.getElementById('edit_medico').value = evaluacion.medico_id;
        document.getElementById('edit_ubicacion').value = evaluacion.ubicacion_anatomica || '';
        document.getElementById('edit_morfologia').value = evaluacion.tipo_lesion_morfologia || '';
        
        if (antecedentes) {
            document.getElementById('edit_sol').value = antecedentes.exposicion_sol || '';
            document.getElementById('edit_enfermedades').value = antecedentes.enfermedades_piel_previas || '';
        }

        document.getElementById('formEditar').action = "{{ url('evaluaciones') }}/" + evaluacion.id;
        document.getElementById('modalEditar').classList.remove('hidden');
        document.getElementById('modalEditar').classList.add('flex');
    }

    function abrirModalEliminar(id) {
        document.getElementById('formEliminar').action = "{{ url('evaluaciones') }}/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
        document.getElementById('modalEliminar').classList.add('flex');
    }

    // Funciones PDF/EXCEL Individual
    function imprimirFichaNativa() {
        let contenido = document.getElementById('contenedorFichaImprimible').innerHTML;
        let ventana = window.open('', '', 'height=800,width=800');
        ventana.document.write('<html><head><title>Historia Clínica Paciente</title>');
        ventana.document.write('<style>body{font-family:sans-serif; color:#333; padding:20px;} .no-print { display:none !important; } .border{border:1px solid #ccc; padding:10px; margin-bottom:10px;} h3{font-size:16px;} p{font-size:12px;}</style>');
        ventana.document.write('</head><body>');
        ventana.document.write(contenido);
        ventana.document.write('</body></html>');
        ventana.document.close();
        setTimeout(function() { ventana.print(); ventana.close(); }, 500);
    }

    function exportarFichaExcel() {
        let nombre = document.getElementById('ver_paciente').innerText;
        let fecha = document.getElementById('ver_fecha').innerText;
        let csv = "Paciente;Fecha;Motivo\n" + nombre + ";" + fecha + ";" + document.getElementById('ver_motivo').innerText;
        let blob = new Blob(["\ufeff" + csv], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Ficha_" + nombre + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    // LÓGICA DE FILTROS CENTRALIZADA
    function filaCumpleFiltros(fila) {
        if (!fila.hasAttribute('data-busqueda')) return false;

        const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
        const estadoBuscado = document.getElementById('filtroEstado').value;
        const medicoBuscado = document.getElementById('filtroMedico').value;
        const fechaDesde = document.getElementById('filtroDesde').value;
        const fechaHasta = document.getElementById('filtroHasta').value;

        const dataBusqueda = fila.getAttribute('data-busqueda');
        const dataEstado = fila.getAttribute('data-estado');
        const dataMedico = fila.getAttribute('data-medico');
        const dataFecha = fila.getAttribute('data-fecha');

        const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
        const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;
        const coincideMedico = medicoBuscado === "TODOS" || dataMedico == medicoBuscado;
        
        let coincideFecha = true;
        if (fechaDesde && dataFecha < fechaDesde) coincideFecha = false;
        if (fechaHasta && dataFecha > fechaHasta) coincideFecha = false;

        return coincideTexto && coincideEstado && coincideMedico && coincideFecha;
    }

    // Funciones Generales Excel/PDF
    function exportarExcelNativo() {
        let csv = [];
        let headerData = ["#", "Fecha/Hora", "Paciente", "Ubicación", "Estado"];
        csv.push(headerData.join(";"));
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaEvaluaciones tr');
        
        cuerpoFilas.forEach(fila => {
            // Se usa la función centralizada para verificar filtros reales (y así exportar todo)
            if (!filaCumpleFiltros(fila)) return;
            
            let filaData = [];
            let celdas = fila.querySelectorAll("td");
            if (celdas.length >= 5) {
                let indices = [0, 1, 2, 3, 5]; 
                indices.forEach(idx => {
                    let texto = celdas[idx].innerText.replace(/(\n|\r)/gm, " ").trim();
                    filaData.push('"' + texto + '"');
                });
            }
            csv.push(filaData.join(";"));
        });

        let blob = new Blob(["\ufeff" + csv.join("\n")], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Auditoria_Expedientes_" + new Date().toISOString().slice(0, 10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    function imprimirReporteNativo() {
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaEvaluaciones tr');
        let ventanaImpresion = window.open('', '', 'height=800,width=1200');
        ventanaImpresion.document.write('<html><head><title>Reporte de Evaluaciones</title>');
        ventanaImpresion.document.write('<style>body{font-family:Arial,sans-serif;padding:25px;color:#0f172a;}h2{text-transform:uppercase;font-size:14px;font-weight:bold;}table{width:100%;border-collapse:collapse;font-size:10px;margin-top:15px;}th,td{border:1px solid #94a3b8;padding:6px;text-align:left;}th{background-color:#f8fafc;}th:nth-child(5),td:nth-child(5),th:nth-last-child(1),td:nth-last-child(1){display:none;}</style>');
        ventanaImpresion.document.write('</head><body><h2>Clínica Vitruvio - Registro de Consultas Dermatológicas</h2><table><thead>' + document.querySelector('#tablaMaestraEvaluaciones thead tr').innerHTML + '</thead><tbody>');
        
        cuerpoFilas.forEach(fila => {
            // Se usa la función centralizada para imprimir todas las filas filtradas
            if (filaCumpleFiltros(fila)) {
                ventanaImpresion.document.write('<tr>' + fila.innerHTML + '</tr>');
            }
        });
        ventanaImpresion.document.write('</tbody></table></body></html>');
        ventanaImpresion.document.close();
        setTimeout(function() { ventanaImpresion.print(); ventanaImpresion.close(); }, 500);
    }

    // Paginación y Filtrado
    let paginaActual = 1;
    const registrosPorPagina = 10;
    window.addEventListener('DOMContentLoaded', function() { ejecutarFiltrosCombinados(); });
    function resetearPaginaYFiltrar() { paginaActual = 1; ejecutarFiltrosCombinados(); }
    
    function ejecutarFiltrosCombinados() {
        const filas = document.querySelectorAll('#cuerpoTablaEvaluaciones tr');
        let filasFiltradas = [];

        filas.forEach(fila => {
            if (filaCumpleFiltros(fila)) {
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
        document.getElementById('infoRegistros').innerText = totalRegistros === 0 ? "Mostrando 0 registros" : `Mostrando ${inicio + 1} a ${Math.min(fin, totalRegistros)} de ${totalRegistros}`;
        const controles = document.getElementById('paginacionControles');
        controles.innerHTML = "";
        let btnAnt = document.createElement('button');
        btnAnt.innerHTML = '<'; btnAnt.className = `px-2 py-1 rounded border text-xs font-bold ${paginaActual === 1 ? 'bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        if (paginaActual > 1) { btnAnt.onclick = function() { paginaActual--; ejecutarFiltrosCombinados(); }; }
        controles.appendChild(btnAnt);

        for (let i = 1; i <= totalPaginas; i++) {
            let btn = document.createElement('button');
            btn.innerText = i; btn.className = `px-2 py-1 rounded border text-xs font-bold ${paginaActual === i ? 'bg-blue-600 text-white' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
            btn.onclick = function() { paginaActual = i; ejecutarFiltrosCombinados(); };
            controles.appendChild(btn);
        }

        let btnSig = document.createElement('button');
        btnSig.innerHTML = '>'; btnSig.className = `px-2 py-1 rounded border text-xs font-bold ${paginaActual === totalPaginas ? 'bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        if (paginaActual < totalPaginas) { btnSig.onclick = function() { paginaActual++; ejecutarFiltrosCombinados(); }; }
        controles.appendChild(btnSig);
    }

    function limpiarFiltros() {
        document.getElementById('filtroTexto').value = "";
        document.getElementById('filtroEstado').value = "TODOS";
        document.getElementById('filtroMedico').value = "TODOS";
        document.getElementById('filtroDesde').value = "";
        document.getElementById('filtroHasta').value = "";
        resetearPaginaYFiltrar();
    }
</script>
@endsection
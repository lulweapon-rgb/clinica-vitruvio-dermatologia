@extends('layouts.app')
@section('title', 'Registro de Evaluaciones Dermatológicas')
@section('content')

<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Registro de Evaluaciones Dermatológicas</h2>
            <p class="text-slate-500 text-sm mt-0.5">Gestión de Consultas.</p>
        </div>
        <div class="text-sm font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg border">
            Evaluaciones / Inicio
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold space-y-1">
        <div class="font-bold flex items-center gap-2"><i class="fa-solid fa-circle-xmark text-red-500 text-base"></i> Errores de validación detectados:</div>
        <ul class="list-disc list-inside pl-2 font-medium text-xs text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

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
            <i class="fa-solid fa-filter"></i> Filtros de Auditoria para Historiales
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-slate-600 pb-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Buscar Paciente (Por C.I.)</label>
                <input type="text" id="filtroTexto" oninput="resetearPaginaYFiltrar()" placeholder="Ej. 8374923..." class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Filtrar por Médico Tratante</label>
                <select id="filtroMedico" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">TODOS LOS MÉDICOS</option>
                    @php
                        $medicosConPacientes = $evaluaciones->pluck('medico')->filter()->unique('id');
                    @endphp
                    @foreach($medicosConPacientes as $med)
                        @if($med->usuario)
                            <option value="{{ $med->id }}">Dr/a. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Estado del Registro</label>
                <select id="filtroEstado" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">AMBOS (Todos)</option>
                    <option value="ACTIVO">SOLO ACTIVAS</option>
                    <option value="INACTIVO">SOLO ANULADAS</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Evaluaciones Desde: </label>
                <input type="date" id="filtroDesde" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Evaluaciones Hasta:</label>
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
                    <i class="fa-solid fa-file-pdf"></i> Exportar PDF
                </button>
            </div>
            <button onclick="document.getElementById('modalRegistro').classList.remove('hidden'); document.getElementById('modalRegistro').classList.add('flex');" class="bg-[#dc3545] hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2 uppercase tracking-wide">
                <i class="fa-solid fa-plus text-sm"></i> Nueva Evaluación
            </button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto whitespace-nowrap" id="tablaMaestraEvaluaciones">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <th class="p-3 border-r border-slate-200 font-bold text-center w-8">#</th>
                        <th class="p-3 border-r border-slate-200 font-bold text-center">Fecha / Hora</th>
                        <th class="p-3 border-r border-slate-200 font-bold">Paciente</th>
                        <th class="p-3 border-r border-slate-200 font-bold">Médico Evaluador</th>
                        <th class="p-3 border-r border-slate-200 font-bold">Síntomas Clínicos</th>
                        <th class="p-3 border-r border-slate-200 font-bold text-center">Muestra Foto</th>
                        <th class="p-3 border-r border-slate-200 bg-blue-50 text-blue-700 font-bold text-center">Métrica IA (CNN)</th>
                        <th class="p-3 border-r border-slate-200 font-bold text-center">Estado</th>
                        <th class="p-3 border-slate-200 font-bold text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs" id="cuerpoTablaEvaluaciones">
                    @forelse($evaluaciones as $index => $eval)
                    @php
                        $fechaHora = $eval->creado_at ? \Carbon\Carbon::parse($eval->creado_at)->format('d/m/Y H:i') : 'S/N';
                        $fechaFiltro = $eval->creado_at ? \Carbon\Carbon::parse($eval->creado_at)->format('Y-m-d') : '';
                        $estadoFiltro = $eval->trashed() ? 'INACTIVO' : 'ACTIVO';
                        $ciPaciente = $eval->paciente->ci ?? 'S/N';
                        $nombreMedico = ($eval->medico && $eval->medico->usuario) ? strtoupper($eval->medico->usuario->nombre . ' ' . $eval->medico->usuario->apellido_paterno) : 'MÉDICO ELIMINADO';
                        $textoBusqueda = strtolower($ciPaciente);
                        $urlImagen = asset('storage/' . $eval->imagen_lesion);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $eval->trashed() ? 'bg-slate-50 opacity-60' : '' }}" data-busqueda="{{ $textoBusqueda }}" data-estado="{{ $estadoFiltro }}" data-medico="{{ $eval->medico_id }}" data-fecha="{{ $fechaFiltro }}">
                        <td class="p-3 text-center text-slate-400 font-mono border-r border-slate-100">{{ $index + 1 }}</td>
                        <td class="p-3 font-mono font-bold text-slate-600 border-r border-slate-100 text-center">{{ $fechaHora }}</td>
                        <td class="p-3 border-r border-slate-100 uppercase">
                            <div class="font-bold text-slate-800">
                                {{ $eval->paciente->nombre ?? 'PACIENTE ELIMINADO' }} {{ $eval->paciente->apellido_paterno ?? '' }}
                                @if($eval->paciente && $eval->paciente->trashed())
                                    <span class="ml-1 bg-red-100 text-red-600 text-[9px] px-1.5 py-0.5 rounded border border-red-200">INACTIVO</span>
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">CI: {{ $ciPaciente }}</div>
                            <div class="mt-1 text-[9px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 inline-block uppercase">
                                <i class="fa-solid fa-location-dot"></i> {{ $eval->ubicacion_anatomica ?? 'No especificada' }}
                            </div>
                        </td>
                        <td class="p-3 font-bold text-slate-600 uppercase border-r border-slate-100">
                            <div class="flex flex-col">
                                <span>Dr/a. {{ $nombreMedico }}</span>
                                <span class="text-[9px] text-blue-600 font-mono mt-0.5"><i class="fa-solid fa-envelope mr-0.5"></i> {{ $eval->medico->usuario->correo ?? 'S/N' }}</span>
                            </div>
                        </td>
                        <td class="p-3 border-r border-slate-100">
                            <div class="flex flex-wrap gap-1">
                                @if($eval->sintoma_picazon) <span class="bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded font-bold uppercase text-[8px]">Picazón</span> @endif
                                @if($eval->sintoma_sangrado) <span class="bg-red-50 text-red-700 border border-red-200 px-1.5 py-0.5 rounded font-bold uppercase text-[8px]">Sangrado</span> @endif
                                @if($eval->sintoma_crecimiento) <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-1.5 py-0.5 rounded font-bold uppercase text-[8px]">Crecimiento</span> @endif
                                @if(!$eval->sintoma_picazon && !$eval->sintoma_sangrado && !$eval->sintoma_crecimiento) <span class="text-slate-400 italic text-[10px]">Sin síntomas</span> @endif
                            </div>
                        </td>
                        <td class="p-3 text-center border-r border-slate-100">
                            <img src="{{ $urlImagen }}" alt="Lesión" class="w-10 h-10 object-cover rounded mx-auto border border-slate-200 shadow-sm hover:scale-150 transition-transform cursor-pointer">
                        </td>
                        <td class="p-3 text-center border-r border-slate-100 bg-blue-50/30">
                            @if($eval->ia_porcentaje)
                                <span class="font-black text-red-600 text-sm">{{ $eval->ia_porcentaje }}%</span>
                                <p class="text-[9px] font-bold uppercase text-red-500 tracking-tight">{{ $eval->ia_diagnostico }}</p>
                            @else
                                <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider block"><i class="fa-solid fa-microchip animate-pulse mr-1"></i>Pendiente</span>
                            @endif
                        </td>
                        <td class="p-3 text-center border-r border-slate-100">
                            @if($eval->trashed())
                            <form action="{{ route('evaluaciones.restore', $eval->id) }}" method="POST" id="form-restore-{{ $eval->id }}" class="m-0">
                                @csrf
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" class="sr-only peer" onchange="document.getElementById('form-restore-{{ $eval->id }}').submit()">
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
                                <button onclick='abrirModalVer(@json($eval), @json($eval->paciente), @json($eval->medico), "{{ $urlImagen }}")' class="bg-slate-500 hover:bg-slate-600 text-white p-1.5 rounded shadow-sm transition-colors" title="Ficha Clínica"><i class="fa-solid fa-eye text-[10px]"></i></button>
                                @if(!$eval->trashed())
                                <button onclick='abrirModalEditar(@json($eval))' class="bg-[#007bff] hover:bg-blue-700 text-white p-1.5 rounded shadow-sm transition-colors" title="Modificar Datos"><i class="fa-solid fa-pen-to-square text-[10px]"></i></button>
                                <button onclick="abrirModalEliminar({{ $eval->id }})" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded shadow-sm transition-colors" title="Anular Consulta"><i class="fa-solid fa-trash text-[10px]"></i></button>
                                @else
                                <form action="{{ route('evaluaciones.force_delete', $eval->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('¡ADVERTENCIA EXTREMA!\n\n¿Estás absolutamente seguro de eliminar esta EVALUACIÓN FÍSICAMENTE?\n\nEsta acción borrará la consulta permanentemente.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-800 hover:bg-red-950 text-white p-1.5 rounded shadow-sm transition-colors" title="Destrucción Total"><i class="fa-solid fa-fire-flame-curved text-[10px]"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="filaTablaVacia">
                        <td colspan="9" class="p-8 text-center text-slate-400 font-medium">
                            <i class="fa-solid fa-notes-medical text-3xl block mb-2 opacity-30"></i> No se encuentran evaluaciones clínicas registradas.
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

<div id="modalRegistro" class="fixed inset-0 z-[100] bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden border border-slate-100 animate-fade-in my-auto max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-700 uppercase tracking-tight">Formulario de Triage Pre-Análisis Dermatológico</h3>
                <p class="text-xs text-slate-400 mt-0.5">Recolección de sintomatología previa a la estimación de IA.</p>
            </div>
            <button type="button" onclick="document.getElementById('modalRegistro').classList.remove('flex'); document.getElementById('modalRegistro').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>

        <form action="{{ route('evaluaciones.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="p-6">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                
                <div class="space-y-4 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-blue-500"></i> 1. Identificación y Captura
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Paciente Solicitante</label>
                            <select name="paciente_id" class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700 bg-white shadow-sm" required>
                                <option value="">Seleccione al paciente...</option>
                                @foreach ($pacientes as $pac)
                                    <option value="{{ $pac->id }}">{{ strtoupper($pac->ci . ' - ' . $pac->nombre . ' ' . $pac->apellido_paterno) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Médico Responsable</label>
                            @if(auth()->user()->isSuperAdmin())
                                <select name="medico_id" class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm" required>
                                    <option value="">Seleccione al médico...</option>
                                    @foreach($medicos as $med)
                                        <option value="{{ $med->id }}">Dr/a. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}</option>
                                    @endforeach
                                </select>
                            @else
                                @php $miMedico = $medicos->where('usuario_id', auth()->id())->first(); @endphp
                                <input type="text" value="DR/A. {{ strtoupper(auth()->user()->nombre . ' ' . auth()->user()->apellido) }}" readonly class="w-full border rounded-lg px-3 py-2 text-xs bg-slate-100 text-slate-500 font-bold cursor-not-allowed shadow-sm">
                                <input type="hidden" name="medico_id" value="{{ $miMedico ? $miMedico->id : '' }}">
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Ubicación Anatómica</label>
                        <select name="ubicacion_anatomica" required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                            <option value="">Seleccione ubicación...</option>
                            <option value="Rostro">Rostro</option>
                            <option value="Cuello">Cuello</option>
                            <option value="Tórax">Tórax</option>
                            <option value="Espalda">Espalda</option>
                            <option value="Brazo Derecho">Brazo Derecho</option>
                            <option value="Brazo Izquierdo">Brazo Izquierdo</option>
                            <option value="Pierna Derecha">Pierna Derecha</option>
                            <option value="Pierna Izquierda">Pierna Izquierda</option>
                            <option value="Otra zona">Otra zona...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-blue-600 mb-1"><i class="fa-solid fa-camera mr-1"></i> Captura Dermatológica (HU-05)</label>
                        <input type="file" name="imagen_lesion" id="foto_lesion" accept="image/*" onchange="previewImage(event)" required class="w-full border border-blue-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 outline-none font-bold bg-blue-50/50 text-blue-700">
                        <p class="text-[9px] text-slate-400 mt-1 leading-normal">Fotografía nítida obtenida por el dispositivo capturador clínico.</p>
                        
                        <div id="preview_container" class="hidden mt-3 p-2 border border-slate-200 rounded-lg bg-slate-50 w-full text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Vista Previa:</p>
                            <img id="image_preview" src="" alt="Vista previa de lesión" class="h-32 object-contain rounded shadow-sm border border-slate-300 mx-auto">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Observaciones Clínicas Generales (Anamnesis)</label>
                        <textarea name="diagnostico_clinico" rows="4" placeholder="Detalle la morfología observada en la lesión..." class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none resize-none bg-white shadow-sm" required></textarea>
                    </div>
                </div>

                <div class="space-y-4 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-emerald-500"></i> 2. Sintomatologia de la Lesión
                    </h4>
                    <p class="text-[11px] text-slate-400 leading-normal mb-2">Marcar las condiciones observadas o reportadas por el paciente. <br> <span class="font-semibold text-indigo-500">El nivel de riesgo y clasificación morfológica serán estimados por la IA.</span></p>
                    
                    <label class="flex items-center justify-between p-3 bg-white rounded-lg border border-slate-200 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center"><i class="fa-solid fa-hand-dots text-sm"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Prurito o Picazón</p>
                                <p class="text-[10px] text-slate-400">La lesión genera comezón persistente</p>
                            </div>
                        </div>
                        <input type="checkbox" name="sintoma_picazon" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                    </label>

                    <label class="flex items-center justify-between p-3 bg-white rounded-lg border border-slate-200 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center"><i class="fa-solid fa-droplet text-sm"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Sangrado o Exudación</p>
                                <p class="text-[10px] text-slate-400">La lesión sangra espontáneamente</p>
                            </div>
                        </div>
                        <input type="checkbox" name="sintoma_sangrado" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                    </label>

                    <label class="flex items-center justify-between p-3 bg-white rounded-lg border border-slate-200 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center"><i class="fa-solid fa-arrows-maximize text-sm"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Crecimiento Rápido</p>
                                <p class="text-[10px] text-slate-400">Ha cambiado de tamaño o forma recientemente</p>
                            </div>
                        </div>
                        <input type="checkbox" name="sintoma_crecimiento" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 bg-slate-50 -mx-6 -mb-6 p-4 rounded-b-xl">
                <button type="button" onclick="document.getElementById('modalRegistro').classList.remove('flex'); document.getElementById('modalRegistro').classList.add('hidden');" class="bg-slate-500 text-white px-6 py-2 rounded-lg text-xs font-bold shadow-sm hover:bg-slate-600 transition-colors uppercase tracking-wide">Cerrar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-xs font-bold shadow-sm transition-colors uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-microchip"></i> Procesar y Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalVer" class="fixed inset-0 z-[100] bg-slate-900/80 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden animate-fade-in flex flex-col md:flex-row my-auto max-h-[90vh]">
        <div class="w-full md:w-5/12 bg-slate-900 p-6 flex flex-col justify-center items-center relative">
            <h3 class="absolute top-4 left-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-700 pb-1">Muestra Fotográfica</h3>
            <img id="ver_imagen" src="" alt="Lesión" class="w-full max-h-64 object-contain rounded-lg border-2 border-slate-700 shadow-lg mt-8">
            <div class="mt-6 w-full bg-slate-800 border border-slate-700 p-4 rounded-lg text-center">
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest mb-1"><i class="fa-solid fa-microchip"></i> Análisis Inteligencia Artificial (CNN)</p>
                <p id="ver_ia_resultado" class="text-xl font-black text-white">-</p>
            </div>
        </div>
        <div class="w-full md:w-7/12 p-6 flex flex-col justify-between overflow-y-auto">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight" id="ver_paciente"></h2>
                        <p class="text-sm font-bold text-blue-600" id="ver_medico"></p>
                    </div>
                    <button onclick="document.getElementById('modalVer').classList.remove('flex'); document.getElementById('modalVer').classList.add('hidden');" class="text-slate-400 hover:text-slate-800 text-2xl leading-none">&times;</button>
                </div>
                <div class="grid grid-cols-3 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-100 mb-4">
                    <div><p class="text-[10px] text-slate-400 font-bold uppercase">Ubicación Anatómica</p><p id="ver_ubicacion" class="font-bold text-indigo-700 text-xs uppercase"></p></div>
                    <div><p class="text-[10px] text-slate-400 font-bold uppercase">Fecha y Hora</p><p id="ver_fecha" class="font-bold text-slate-700 text-xs font-mono"></p></div>
                    <div><p class="text-[10px] text-slate-400 font-bold uppercase">Estado Sistema</p><p id="ver_estado" class="font-bold text-xs"></p></div>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 mb-4 space-y-2">
                    <p class="text-[10px] text-slate-400 font-bold uppercase border-b pb-1">Sintomatología Recolectada</p>
                    <div class="flex flex-wrap gap-1.5" id="ver_triage_sintomas"></div>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mb-2 border-b pb-1">Anotaciones de Anamnesis / Observación Médica</p>
                    <p id="ver_diagnostico" class="text-sm text-slate-600 italic bg-slate-50 p-4 rounded-lg border border-slate-100 leading-relaxed max-h-40 overflow-y-auto"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('modalVer').classList.remove('flex'); document.getElementById('modalVer').classList.add('hidden');" class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">Cerrar Historial</button>
            </div>
        </div>
    </div>
</div>

<div id="modalEditar" class="fixed inset-0 z-[100] bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden border border-slate-100 animate-fade-in my-auto">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-700">Editar Evaluación Clínica</h3>
            <button type="button" onclick="document.getElementById('modalEditar').classList.remove('flex'); document.getElementById('modalEditar').classList.add('hidden');" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>
        <form id="formEditar" method="POST" enctype="multipart/form-data" autocomplete="off" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Paciente Atendido</label>
                        <select name="paciente_id" id="edit_paciente" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-bold select-none cursor-not-allowed bg-slate-50" readonly required>
                            @foreach ($pacientes as $pac)
                                <option value="{{ $pac->id }}">{{ strtoupper($pac->ci . ' - ' . $pac->nombre . ' ' . $pac->apellido_paterno) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Médico Responsable</label>
                        <select name="medico_id" id="edit_medico" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm" required>
                            @foreach($medicos as $med)
                                <option value="{{ $med->id }}">Dr/a. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Ubicación Anatómica</label>
                        <input type="text" name="ubicacion_anatomica" id="edit_ubicacion" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-blue-600 mb-1"><i class="fa-solid fa-camera mr-1"></i> Reemplazar Fotografía (Opcional)</label>
                        <input type="file" name="imagen_lesion" accept="image/*" class="w-full border border-blue-200 rounded-lg px-3 py-1.5 text-xs bg-blue-50 text-blue-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Diagnóstico Clínico / Notas de Anamnesis</label>
                        <textarea name="diagnostico_clinico" id="edit_diagnostico" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" required></textarea>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditar').classList.remove('flex'); document.getElementById('modalEditar').classList.add('hidden');" class="bg-slate-500 text-white px-6 py-2 rounded-lg text-sm font-bold">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 z-[100] bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fa-solid fa-file-circle-xmark"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">¿Anular Evaluación?</h3>
            <p class="text-sm text-slate-500">Este registro clínico pasará al estado inactivo y no será analizado por la IA.</p>
        </div>
        <form id="formEliminar" method="POST" class="flex justify-center gap-3 p-5 bg-slate-50 border-t">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modalEliminar').classList.remove('flex'); document.getElementById('modalEliminar').classList.add('hidden');" class="bg-slate-300 text-slate-700 px-5 py-2 rounded-lg text-sm font-bold">Cancelar</button>
            <button type="submit" class="bg-red-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Sí, Anular</button>
        </form>
    </div>
</div>

<script>
    let paginaActual = 1;
    const registrosPorPagina = 10;

    window.addEventListener('DOMContentLoaded', function() {
        ejecutarFiltrosCombinados();
    });

    function resetearPaginaYFiltrar() {
        paginaActual = 1;
        ejecutarFiltrosCombinados();
    }

    function ejecutarFiltrosCombinados() {
        const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
        const estadoBuscado = document.getElementById('filtroEstado').value;
        const medicoBuscado = document.getElementById('filtroMedico').value;
        const fechaDesde = document.getElementById('filtroDesde').value;
        const fechaHasta = document.getElementById('filtroHasta').value;
        const filas = document.querySelectorAll('#cuerpoTablaEvaluaciones tr');
        let filasFiltradas = [];

        filas.forEach(fila => {
            if (!fila.hasAttribute('data-busqueda')) return;
            const dataBusqueda = fila.getAttribute('data-busqueda');
            const dataEstado = fila.getAttribute('data-estado');
            const dataMedico = fila.getAttribute('data-medico');
            const dataFecha = fila.getAttribute('data-fecha');

            const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
            const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;
            const coincideMedico = medicoBuscado === "TODOS" || dataMedico === medicoBuscado;
            let coincideFecha = true;
            if (fechaDesde && dataFecha < fechaDesde) coincideFecha = false;
            if (fechaHasta && dataFecha > fechaHasta) coincideFecha = false;

            if (coincideTexto && coincideEstado && coincideMedico && coincideFecha) {
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
        document.getElementById('infoRegistros').innerText = totalRegistros === 0 ? "Mostrando 0 registros" : `Mostrando ${inicio + 1} a ${Math.min(fin, totalRegistros)} de ${totalRegistros} consultas`;
        const controles = document.getElementById('paginacionControles');
        controles.innerHTML = "";

        let btnAnt = document.createElement('button');
        btnAnt.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
        btnAnt.className = `px-3 py-1.5 rounded-lg border text-xs font-bold transition-all ${paginaActual === 1 ? 'bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
        if (paginaActual > 1) { btnAnt.onclick = function() { paginaActual--; ejecutarFiltrosCombinados(); }; }
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

    // PREVISUALIZADOR DE IMAGEN
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image_preview');
            output.src = reader.result;
            document.getElementById('preview_container').classList.remove('hidden');
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function abrirModalVer(evaluacion, paciente, medico, urlImagen) {
        document.getElementById('ver_paciente').innerText = paciente ? paciente.nombre.toUpperCase() + ' ' + (paciente.apellido_paterno || '').toUpperCase() : 'PACIENTE ELIMINADO';
        document.getElementById('ver_medico').innerText = (medico && medico.usuario) ? 'Evaluador: Dr/a. ' + medico.usuario.nombre.toUpperCase() + ' ' + medico.usuario.apellido_paterno.toUpperCase() : 'Médico No Asignado';
        
        let dateObj = new Date(evaluacion.creado_at);
        document.getElementById('ver_fecha').innerText = dateObj.toLocaleString('es-ES');
        document.getElementById('ver_diagnostico').innerText = evaluacion.diagnostico_clinico;
        document.getElementById('ver_imagen').src = urlImagen;
        
        // UBICACIÓN
        document.getElementById('ver_ubicacion').innerText = evaluacion.ubicacion_anatomica || 'No especificada';

        const contenedorSintomas = document.getElementById('ver_triage_sintomas');
        contenedorSintomas.innerHTML = "";
        if (evaluacion.sintoma_picazon) contenedorSintomas.innerHTML += '<span class="bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Picazón</span>';
        if (evaluacion.sintoma_sangrado) contenedorSintomas.innerHTML += '<span class="bg-red-50 text-red-700 border border-red-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Sangrado</span>';
        if (evaluacion.sintoma_crecimiento) contenedorSintomas.innerHTML += '<span class="bg-indigo-50 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Crecimiento</span>';
        if (contenedorSintomas.innerHTML === "") contenedorSintomas.innerHTML = '<span class="text-slate-400 italic text-[11px]">Ningún síntoma reportado.</span>';

        if (evaluacion.ia_porcentaje) {
            document.getElementById('ver_ia_resultado').innerHTML = `<span class="text-red-500">${evaluacion.ia_porcentaje}%</span><br><span class="text-xs font-black text-slate-300 uppercase">${evaluacion.ia_diagnostico}</span>`;
        } else {
            document.getElementById('ver_ia_resultado').innerHTML = '<span class="text-[11px] text-slate-500 font-normal">Algoritmo CNN Pendiente</span>';
        }

        const estadoLabel = document.getElementById('ver_estado');
        if (evaluacion.eliminado_at) {
            estadoLabel.innerText = 'ANULADA / INACTIVA';
            estadoLabel.className = 'font-bold text-red-600 text-xs';
        } else {
            estadoLabel.innerText = 'VIGENTE / ACTIVA';
            estadoLabel.className = 'font-bold text-emerald-600 text-xs';
        }

        document.getElementById('modalVer').classList.remove('hidden');
        document.getElementById('modalVer').classList.add('flex');
    }

    function abrirModalEditar(evaluacion) {
        document.getElementById('edit_paciente').value = evaluacion.paciente_id;
        document.getElementById('edit_medico').value = evaluacion.medico_id;
        document.getElementById('edit_ubicacion').value = evaluacion.ubicacion_anatomica || '';
        document.getElementById('edit_diagnostico').value = evaluacion.diagnostico_clinico;
        document.getElementById('formEditar').action = "{{ url('evaluaciones') }}/" + evaluacion.id;
        document.getElementById('modalEditar').classList.remove('hidden');
        document.getElementById('modalEditar').classList.add('flex');
    }

    function abrirModalEliminar(id) {
        document.getElementById('formEliminar').action = "{{ url('evaluaciones') }}/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
        document.getElementById('modalEliminar').classList.add('flex');
    }

    function exportarExcelNativo() {
        let csv = [];
        let headerData = ["#", "Fecha/Hora", "Paciente", "Ubicación", "Evaluador", "Sintomas", "Diagnostico Clinico", "Estado"];
        csv.push(headerData.join(";"));
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaEvaluaciones tr');
        cuerpoFilas.forEach(fila => {
            if (fila.style.display === "none" || !fila.hasAttribute('data-busqueda')) return;
            let filaData = [];
            let celdas = fila.querySelectorAll("td");
            if (celdas.length >= 8) {
                let indices = [0, 1, 2, 3, 4, 7];
                indices.forEach(idx => {
                    let texto = celdas[idx].innerText.replace(/(\n|\r)/gm, " ").trim();
                    filaData.push('"' + texto + '"');
                });
            }
            csv.push(filaData.join(";"));
        });
        let blob = new Blob(["\ufeff" + csv.join("\n")], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Auditoria_Evaluaciones_" + new Date().toISOString().slice(0, 10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    function imprimirReporteNativo() {
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaEvaluaciones tr');
        let ventanaImpresion = window.open('', '', 'height=800,width=1200');
        ventanaImpresion.document.write('<html><head><title>Reporte de Evaluaciones</title>');
        ventanaImpresion.document.write('<style>body{font-family:Arial,sans-serif;padding:25px;color:#0f172a;}h2{text-transform:uppercase;font-size:14px;font-weight:bold;}table{width:100%;border-collapse:collapse;font-size:10px;margin-top:15px;}th,td{border:1px solid #94a3b8;padding:6px;text-align:left;}th{background-color:#f8fafc;}th:nth-child(6),td:nth-child(6),th:nth-child(7),td:nth-child(7),th:nth-last-child(1),td:nth-last-child(1){display:none;}</style>');
        ventanaImpresion.document.write('</head><body><h2>Clínica Vitruvio - Registro de Consultas Dermatológicas</h2><table><thead>' + document.querySelector('#tablaMaestraEvaluaciones thead tr').innerHTML + '</thead><tbody>');
        cuerpoFilas.forEach(fila => {
            if (fila.hasAttribute('data-busqueda') && fila.style.display !== "none") {
                ventanaImpresion.document.write('<tr>' + fila.innerHTML + '</tr>');
            }
        });
        ventanaImpresion.document.write('</tbody></table></body></html>');
        ventanaImpresion.document.close();
        setTimeout(function() { ventanaImpresion.print(); ventanaImpresion.close(); }, 500);
    }
</script>
@endsection
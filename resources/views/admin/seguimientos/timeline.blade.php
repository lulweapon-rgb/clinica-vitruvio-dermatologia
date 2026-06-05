@extends('layouts.app')

@section('title', 'Línea de Tiempo Evolutiva')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-wrap items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('seguimientos.index') }}" class="w-10 h-10 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-full flex items-center justify-center transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Expediente Clínico Evolutivo</h2>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest font-mono">Paciente: {{ $evaluacion->paciente->nombre }} {{ $evaluacion->paciente->apellido_paterno }} | C.I. {{ $evaluacion->paciente->ci }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="imprimirExpedienteEvolutivo()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir Reporte
            </button>
            <button onclick="document.getElementById('modalControl').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition-colors shadow-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Registrar Control
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-base"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-1 sticky top-6 space-y-4" id="seccionDiaCero">
            <div class="bg-slate-900 rounded-xl border border-slate-800 shadow-lg overflow-hidden text-white relative">
                <div class="absolute top-4 left-4 bg-red-500 text-white text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-md z-10">Día Cero (Base)</div>
                <img src="{{ asset('storage/' . $evaluacion->imagen_lesion) }}" id="imgDiaCero" class="w-full h-56 object-cover border-b border-slate-700 brightness-90 hover:brightness-100 transition-all">
                
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Predicción IA Inicial</p>
                        <h3 class="text-lg font-black text-amber-400 uppercase leading-tight" id="diagDiaCero">{{ $evaluacion->ia_diagnostico }}</h3>
                        <p class="text-sm font-bold text-red-400 font-mono mt-1" id="porcDiaCero">Riesgo: {{ $evaluacion->ia_porcentaje }}%</p>
                    </div>
                    
                    <div class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1"><i class="fa-solid fa-calendar-day mr-1"></i>Fecha de Captura</p>
                        <p class="text-xs font-bold text-slate-200" id="fechaDiaCero">{{ \Carbon\Carbon::parse($evaluacion->creado_at)->format('d/m/Y - H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Notas Originales</p>
                        <p class="text-xs text-slate-300 italic">"{{ $evaluacion->diagnostico_clinico }}"</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 relative">
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-slate-200"></div>

            <div class="space-y-8" id="seccionTimeline">
                @forelse($evaluacion->seguimientos as $index => $seg)
                    <div class="relative pl-20 pr-4 nodo-timeline">
                        
                        <div class="absolute left-[23px] top-4 w-5 h-5 {{ $seg->trashed() ? 'bg-red-500 border-red-200' : 'bg-amber-500 border-white' }} border-4 rounded-full shadow z-10 transition-colors"></div>
                        <div class="absolute left-0 top-4 text-[10px] font-black {{ $seg->trashed() ? 'text-red-400 line-through' : 'text-slate-400' }} font-mono w-16 text-right">#{{ $index + 1 }}</div>

                        <div class="rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row transition-all duration-300 {{ $seg->trashed() ? 'bg-red-50/40 border-2 border-dashed border-red-200 grayscale hover:grayscale-0 opacity-80' : 'bg-white border border-slate-200 hover:border-amber-300' }}">
                            
                            <div class="md:w-2/5 relative">
                                <img src="{{ asset('storage/' . $seg->imagen_control) }}" class="img-control w-full h-full object-cover min-h-[200px]">
                                <div class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-1 rounded uppercase tracking-wider tiempo-humano">
                                    {{ \Carbon\Carbon::parse($seg->fecha_control)->diffForHumans() }}
                                </div>
                                
                                @if($seg->trashed())
                                    <div class="absolute inset-0 bg-red-900/40 flex items-center justify-center">
                                        <span class="border-2 border-white text-white bg-red-600/80 px-3 py-1 rounded shadow-lg text-xs font-black uppercase tracking-widest rotate-[-10deg] backdrop-blur-sm">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Anulado
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5 md:w-3/5 flex flex-col justify-between relative">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-black {{ $seg->trashed() ? 'text-red-800' : 'text-slate-800' }} uppercase text-sm fecha-control">{{ \Carbon\Carbon::parse($seg->fecha_control)->format('d M. Y') }}</h4>
                                        <span class="bg-blue-50 text-blue-700 text-[9px] font-black px-2 py-1 rounded uppercase tracking-widest border border-blue-100 estado-tratamiento">{{ $seg->estado_tratamiento }}</span>
                                    </div>
                                    
                                    <p class="text-xs text-slate-500 font-bold uppercase mb-3"><i class="fa-solid fa-user-doctor text-slate-400 mr-1"></i> Especialista: Dr/a. <span class="medico-nombre">{{ $seg->medico?->usuario?->nombre ?? 'No asignado' }} {{ $seg->medico?->usuario?->apellido_paterno ?? '' }}</span></p>
                                    
                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                        <div class="bg-slate-50 p-2 rounded border {{ $seg->trashed() ? 'border-red-100' : 'border-slate-100' }}">
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">Tamaño</p>
                                            <p class="text-xs font-black cambio-tamano {{ $seg->cambio_tamano == 'Creció' ? 'text-red-600' : 'text-slate-700' }}">{{ $seg->cambio_tamano }}</p>
                                        </div>
                                        <div class="bg-slate-50 p-2 rounded border {{ $seg->trashed() ? 'border-red-100' : 'border-slate-100' }}">
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">Coloración</p>
                                            <p class="text-xs font-black text-slate-700 cambio-color">{{ $seg->cambio_color }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-amber-50/50 border border-amber-100 p-3 rounded-lg mb-3">
                                        <p class="text-[9px] text-amber-600 font-black uppercase tracking-widest mb-1"><i class="fa-solid fa-stethoscope mr-1"></i>Diagnóstico Definitivo (Especialista)</p>
                                        <p class="text-xs font-bold {{ $seg->trashed() ? 'text-red-700 line-through opacity-70' : 'text-slate-800' }} uppercase diag-definitivo">{{ $seg->diagnostico_definitivo }}</p>
                                    </div>

                                    <p class="text-xs text-slate-600 italic bg-slate-50 p-3 rounded-lg border border-slate-100 line-clamp-2 obs-control" title="{{ $seg->observaciones }}">"{{ $seg->observaciones }}"</p>
                                </div>

                                <div class="flex justify-end items-center gap-2 mt-4 pt-3 border-t border-slate-100">
                                    @if($seg->trashed())
                                        <form action="{{ route('seguimientos.restore', $seg->id) }}" method="POST" class="m-0 flex w-full">
                                            @csrf
                                            <button type="submit" class="w-full bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-2 rounded text-[10px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2 shadow-sm" title="Recuperar este control e integrarlo al flujo primario">
                                                <i class="fa-solid fa-trash-arrow-up text-sm"></i> Restaurar Registro
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="verFotoAmpliada('{{ asset('storage/' . $seg->imagen_control) }}', '{{ \Carbon\Carbon::parse($seg->fecha_control)->format('d/m/Y') }}')" class="h-[28px] bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 rounded text-[10px] font-bold uppercase transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-expand"></i> Ver
                                        </button>
                                        
                                        <button onclick='abrirModalEditarSeguimiento(@json($seg))' class="h-[28px] bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 rounded text-[10px] font-bold uppercase transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </button>

                                        <form action="{{ route('seguimientos.destroy', $seg->id) }}" method="POST" onsubmit="return confirm('¿Enviar este control a la papelera del sistema?');" class="m-0 flex items-center">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-[28px] bg-red-50 hover:bg-red-100 text-red-600 px-3 rounded text-[10px] font-bold uppercase transition-colors flex items-center gap-1">
                                                <i class="fa-solid fa-trash"></i> Borrar
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="pl-20 pr-4">
                        <div class="bg-slate-50 rounded-xl border border-dashed border-slate-300 p-10 text-center">
                            <i class="fa-solid fa-timeline text-4xl text-slate-300 mb-3"></i>
                            <h4 class="font-bold text-slate-600 uppercase text-sm">Sin historial evolutivo</h4>
                            <p class="text-xs text-slate-400 mt-1">Registre el primer control médico para iniciar la línea de tiempo del gemelo digital.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
<div id="modalControl" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden border border-slate-100 animate-fade-in my-auto">
        
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-700 uppercase tracking-tight" id="tituloModalControl">Registrar Control Evolutivo</h3>
                <p class="text-xs text-slate-400 mt-0.5" id="descModalControl">Añadir nueva fotografía y parámetros clínicos a la línea de tiempo.</p>
            </div>
            <button type="button" onclick="cerrarModalControl()" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>

        <form id="formControl" action="{{ route('seguimientos.store', $evaluacion->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="p-6">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Médico Especialista</label>
                        <select name="medico_id" id="medico_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 outline-none" required>
                            <option value="">Seleccione al especialista...</option>
                            @foreach($medicos as $med)
                                <option value="{{ $med->id }}">Dr/a. {{ strtoupper($med->usuario?->nombre . ' ' . $med->usuario?->apellido_paterno) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Fecha de Control</label>
                        <input type="date" name="fecha_control" id="fecha_control" value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 outline-none" required>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg">
                        <label class="block text-xs font-black text-amber-700 uppercase mb-1"><i class="fa-solid fa-camera mr-1"></i> Fotografía Digital</label>
                        <input type="file" name="imagen_control" id="imagen_control" accept="image/*" class="w-full text-xs font-bold text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer outline-none" required>
                        <p class="text-[10px] text-amber-600 mt-1 italic" id="txtAyudaFoto">Obligatorio para nuevos controles.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tamaño</label>
                            <select name="cambio_tamano" id="cambio_tamano" class="w-full border border-slate-300 rounded-lg px-2 py-2 text-xs focus:ring-2 focus:ring-amber-500 font-bold outline-none" required>
                                <option value="Sin cambios">Sin cambios</option>
                                <option value="Creció" class="text-red-600">Creció (Alerta)</option>
                                <option value="Redujo">Redujo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Color</label>
                            <select name="cambio_color" id="cambio_color" class="w-full border border-slate-300 rounded-lg px-2 py-2 text-xs focus:ring-2 focus:ring-amber-500 outline-none font-bold" required>
                                <option value="Sin cambios">Sin cambios</option>
                                <option value="Más oscuro">Más oscuro</option>
                                <option value="Más claro">Más claro</option>
                                <option value="Múltiples tonos" class="text-red-600">Múltiples tonos (Alerta)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Diagnóstico Definitivo</label>
                        <input type="text" name="diagnostico_definitivo" id="diagnostico_definitivo" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 outline-none font-bold text-slate-800" required>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Plan / Estado</label>
                        <select name="estado_tratamiento" id="estado_tratamiento" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 outline-none font-bold text-blue-700 bg-blue-50" required>
                            <option value="EN OBSERVACIÓN">Continuar en Observación</option>
                            <option value="PROGRAMAR BIOPSIA">Programar Biopsia</option>
                            <option value="DERIVACIÓN CIRUGÍA">Derivación Urgente a Cirugía</option>
                            <option value="ALTA MÉDICA">Alta Médica</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 outline-none resize-none" required></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 bg-slate-50/50 -mx-6 -mb-6 p-4">
                <button type="button" onclick="cerrarModalControl()" class="bg-slate-500 text-white px-6 py-2 rounded-lg text-xs font-bold shadow-sm uppercase tracking-wide">Cancelar</button>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg text-xs font-bold shadow-sm transition-colors uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalVerFoto" class="fixed inset-0 z-50 bg-black/90 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="relative max-w-4xl w-full">
        <button onclick="document.getElementById('modalVerFoto').classList.add('hidden')" class="absolute -top-10 right-0 text-white hover:text-slate-300 text-3xl font-black">&times;</button>
        <p id="lblFechaFoto" class="absolute -top-8 left-0 text-white font-mono text-sm tracking-widest uppercase"></p>
        <img id="imgAmpliacion" src="" class="w-full max-h-[80vh] object-contain rounded-lg shadow-2xl border border-slate-700">
    </div>
</div>

@push('scripts')
<script>
    function abrirModalEditarSeguimiento(seguimiento) {
        document.getElementById('tituloModalControl').innerText = "Modificar Control Clínico";
        document.getElementById('descModalControl').innerText = "Actualice los hallazgos médicos u observaciones del seguimiento seleccionado.";
        
        document.getElementById('formControl').action = `/seguimientos/update/${seguimiento.id}`;
        document.getElementById('formMethod').value = "PUT";
        
        document.getElementById('medico_id').value = seguimiento.medico_id;
        document.getElementById('fecha_control').value = seguimiento.fecha_control;
        document.getElementById('cambio_tamano').value = seguimiento.cambio_tamano;
        document.getElementById('cambio_color').value = seguimiento.cambio_color;
        document.getElementById('diagnostico_definitivo').value = seguimiento.diagnostico_definitivo;
        document.getElementById('estado_tratamiento').value = seguimiento.estado_tratamiento;
        document.getElementById('observaciones').value = seguimiento.observaciones;

        document.getElementById('imagen_control').removeAttribute('required');
        document.getElementById('txtAyudaFoto').innerText = "Opcional. Seleccione un archivo si desea sustituir la imagen actual.";

        document.getElementById('modalControl').classList.remove('hidden');
    }

    function cerrarModalControl() {
        document.getElementById('modalControl').classList.add('hidden');
        document.getElementById('formControl').reset();
        document.getElementById('tituloModalControl').innerText = "Registrar Control Evolutivo";
        document.getElementById('descModalControl').innerText = "Añadir nueva fotografía y parámetros clínicos a la línea de tiempo.";
        document.getElementById('formControl').action = "{{ route('seguimientos.store', $evaluacion->id) }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('imagen_control').setAttribute('required', 'required');
        document.getElementById('txtAyudaFoto').innerText = "Obligatorio para nuevos controles.";
    }

    function verFotoAmpliada(url, fecha) {
        document.getElementById('imgAmpliacion').src = url;
        document.getElementById('lblFechaFoto').innerText = "Captura de Control - " + fecha;
        document.getElementById('modalVerFoto').classList.remove('hidden');
    }

    function imprimirExpedienteEvolutivo() {
        let win = window.open('', '', 'height=850,width=900');
        let nombrePaciente = "{{ $evaluacion->paciente->nombre }} {{ $evaluacion->paciente->apellido_paterno }}";
        let ciPaciente = "{{ $evaluacion->paciente->ci }}";
        let imgBase = document.getElementById('imgDiaCero').src;
        let diagBase = document.getElementById('diagDiaCero').innerText;
        let porcBase = document.getElementById('porcDiaCero').innerText;
        let fechaBase = document.getElementById('fechaDiaCero').innerText;

        win.document.write('<html><head><title>Expediente Evolutivo - ' + nombrePaciente + '</title>');
        win.document.write('<style>body{font-family:"Segoe UI",Arial,sans-serif;padding:35px;color:#1e293b;} .header{border-bottom:3px solid #4f46e5;padding-bottom:10px;margin-bottom:20px;} .header h1{margin:0;font-size:22px;text-transform:uppercase;letter-spacing:0.5px;} .grid-base{display:flex; gap:20px; background:#f8fafc; border:1px solid #e2e8f0; padding:15px; border-radius:8px; margin-bottom:30px;} .grid-base img{width:220px; height:150px; object-fit:cover; border-radius:6px; border:1px solid #cbd5e1;} .nodo{display:flex; gap:20px; border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:15px; page-break-inside: avoid;} .nodo img{width:180px; height:130px; object-fit:cover; border-radius:6px; border:1px solid #cbd5e1;} .data h4{margin:0 0 5px 0; font-size:13px; color:#1e293b; text-transform:uppercase;} .tag{display:inline-block; padding:3px 6px; background:#e2e8f0; border-radius:4px; font-size:9px; font-weight:bold; margin-right:5px; text-transform:uppercase; color:#475569;} </style>');
        win.document.write('</head><body>');
        
        win.document.write('<div class="header"><h1>Clínica Vitruvio</h1><p style="font-size:11px; font-weight:bold; color:#64748b; text-transform:uppercase;">Expediente de Rastreo Cronológico Dermatológico</p><p style="font-size:12px; font-weight:bold; color:#1e293b; margin-top:5px;">PACIENTE: ' + nombrePaciente + ' | C.I. ' + ciPaciente + '</p></div>');
        win.document.write('<h3 style="font-size:12px; color:#dc2626; text-transform:uppercase; margin-bottom:8px;">Punto de Partida (Día Cero Analizado por CNN)</h3>');
        win.document.write('<div class="grid-base"><img src="' + imgBase + '"><div><h2 style="margin:0; color:#1e1b4b; text-transform:uppercase; font-size:15px;">' + diagBase + '</h2><p style="color:#ef4444; font-weight:bold; font-size:13px; margin:4px 0;">Índice de Riesgo: ' + porcBase + '</p><p style="font-size:11px; color:#64748b;">Fecha Captura: ' + fechaBase + '</p></div></div>');
        win.document.write('<h3 style="font-size:12px; color:#475569; text-transform:uppercase; margin-bottom:12px;">Historial de Evolución Fotográfica</h3>');
        
        let nodos = document.querySelectorAll('.nodo-timeline');
        let contadorControlesImpresos = 0;

        nodos.forEach((nodo) => {
            let esAnulado = nodo.querySelector('.bg-red-600\\/80') !== null;
            if (esAnulado) return; 

            contadorControlesImpresos++;
            let img = nodo.querySelector('.img-control').src;
            let fecha = nodo.querySelector('.fecha-control').innerText;
            let estado = nodo.querySelector('.estado-tratamiento').innerText;
            let tamano = nodo.querySelector('.cambio-tamano').innerText;
            let color = nodo.querySelector('.cambio-color').innerText;
            let diag = nodo.querySelector('.diag-definitivo').innerText;
            let obs = nodo.querySelector('.obs-control').innerText;

            win.document.write('<div class="nodo"><img src="' + img + '"><div class="data"><h4>Control #' + contadorControlesImpresos + ' - ' + fecha + '</h4><span class="tag" style="background:#dbeafe; color:#1e40af;">' + estado + '</span><span class="tag">Tamaño: ' + tamano + '</span><span class="tag">Color: ' + color + '</span><p style="font-size:12px; font-weight:bold; margin:10px 0 4px 0; color:#0f172a;">DIAGNÓSTICO MÉDICO: ' + diag + '</p><p style="font-size:11px; color:#475569; margin:0; font-style:italic;">' + obs + '</p></div></div>');
        });
        
        if(contadorControlesImpresos === 0) {
            win.document.write('<p style="font-size:11px; color:#94a3b8; font-style:italic;">No se registran controles médicos activos.</p>');
        }
        win.document.write('</body></html>');
        win.document.close();
        setTimeout(() => { win.print(); win.close(); }, 800);
    }
</script>
@endpush
@endsection
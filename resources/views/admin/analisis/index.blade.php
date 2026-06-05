@extends('layouts.app')

@section('title', 'Motor de Análisis CNN')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-lg text-white">
        <div>
            <h2 class="text-2xl font-black text-indigo-400 tracking-tight uppercase flex items-center gap-2">
                <i class="fa-solid fa-microchip animate-pulse"></i> Centro de Procesamiento Algorítmico (CNN)
            </h2>
            <p class="text-slate-400 text-sm mt-0.5">Bandeja de inferencia dedicada para el diagnóstico asistido por computadora y seguimiento cronológico.</p>
        </div>
        <div class="text-xs font-mono font-bold text-emerald-400 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700 flex items-center gap-2">
            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div> Red Inferencia Activa
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

    <div class="bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-700 text-white space-y-4">
        <div class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-indigo-400">
            <i class="fa-solid fa-filter"></i> Criterios de Trazabilidad Analítica
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-slate-600 pb-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Buscar por C.I. del Paciente</label>
                <input type="text" id="filtroTexto" oninput="filtrarCasos()" placeholder="Ej. 8374923..." class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Filtrar por Médico Tratante</label>
                <select id="filtroMedico" onchange="filtrarCasos()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="TODOS">TODOS LOS MÉDICOS CON CASOS</option>
                    @php
                        // Extracción dinámica de médicos únicos con presencia en la carga transaccional
                        $medicosConCasos = $pendientes->merge($procesados)->pluck('medico')->filter()->unique('id');
                    @endphp
                    @foreach($medicosConCasos as $med)
                        <option value="{{ $med->id }}">DR/A. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Rango Cronológico (Desde)</label>
                <input type="date" id="filtroDesde" onchange="filtrarCasos()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="text-xs text-slate-400 italic">Los filtros de búsqueda aplican en tiempo real sobre ambas bandejas de trabajo.</div>
            <button onclick="limpiarFiltros()" class="bg-slate-600 hover:bg-slate-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors uppercase tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-eraser"></i> Restaurar Tablero
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 bg-amber-50 border-b border-amber-100 flex justify-between items-center">
                <h3 class="font-black text-amber-800 uppercase tracking-tight text-sm flex items-center gap-1.5"><i class="fa-solid fa-inbox"></i> Casos Remitidos (Esperando Inferencia)</h3>
                <span class="bg-amber-200 text-amber-800 text-[10px] px-2 py-0.5 rounded font-bold" id="contadorPendientes">{{ $pendientes->count() }} Muestras</span>
            </div>
            
            <div class="p-4 space-y-4 max-h-[600px] overflow-y-auto bg-slate-50/50" id="contenedorPendientes">
                @forelse($pendientes as $pen)
                    @php
                        $ciPacientePen = $pen->paciente->ci ?? '';
                        $medicoIdPen = $pen->medico_id;
                        $fechaFiltroPen = $pen->creado_at ? \Carbon\Carbon::parse($pen->creado_at)->subHours(4)->format('Y-m-d') : '';
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col sm:flex-row gap-4 items-center card-analisis"
                         data-ci="{{ strtolower($ciPacientePen) }}"
                         data-medico="{{ $medicoIdPen }}"
                         data-fecha="{{ $fechaFiltroPen }}">
                        
                        <img src="{{ asset('storage/' . $pen->imagen_lesion) }}" class="w-20 h-20 object-cover rounded-lg border border-slate-200 shadow-sm hover:scale-110 transition-transform cursor-zoom-in">
                        
                        <div class="flex-1 w-full space-y-1">
                            <div class="flex justify-between items-start">
                                <span class="text-[9px] font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-500 font-bold">REG: #{{ str_pad($pen->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-[9px] text-slate-400 font-mono"><i class="fa-solid fa-clock mr-0.5"></i>{{ \Carbon\Carbon::parse($pen->creado_at)->subHours(4)->format('d/m/Y H:i') }}</span>
                            </div>
                            <h4 class="font-black text-slate-800 uppercase text-xs">{{ $pen->paciente->nombre }} {{ $pen->paciente->apellido_paterno }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium">C.I. Paciente: <span class="font-mono text-slate-700 font-bold">{{ $ciPacientePen }}</span></p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide">Remitido por: Dr/a. {{ $pen->medico->usuario->nombre ?? 'Clínica' }}</p>
                            
                            <form action="{{ route('analisis.ejecutar', $pen->id) }}" method="POST" class="pt-2">
                                @csrf
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-sm uppercase tracking-wider">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Ejecutar Diagnóstico CNN
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-slate-400 bg-white border border-dashed rounded-xl">
                        <i class="fa-solid fa-circle-check text-4xl mb-3 text-emerald-400 opacity-60"></i>
                        <p class="font-black text-sm uppercase text-slate-600">Bandeja Vacía</p>
                        <p class="text-xs text-slate-400 mt-0.5">Todas las muestras cargadas han sido analizadas por la IA.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 bg-emerald-50 border-b border-emerald-100 flex flex-wrap justify-between items-center gap-2">
                <h3 class="font-black text-emerald-800 uppercase tracking-tight text-sm flex items-center gap-1.5"><i class="fa-solid fa-database"></i> Historial de Resultados (Inferencia Guardada)</h3>
                <div class="flex gap-1.5">
                    <button onclick="exportarExcelIA()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1.5 rounded text-[10px] font-bold transition-all flex items-center gap-1 shadow-sm uppercase">
                        <i class="fa-solid fa-file-excel"></i> Excel
                    </button>
                    <button onclick="imprimirHistorialIA()" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1.5 rounded text-[10px] font-bold transition-all flex items-center gap-1 shadow-sm uppercase">
                        <i class="fa-solid fa-file-pdf"></i> Imprimir Historial
                    </button>
                </div>
            </div>
            
            <div class="p-4 space-y-4 max-h-[600px] overflow-y-auto" id="contenedorProcesados">
                @forelse($procesados as $proc)
                    @php
                        $ciPacienteProc = $proc->paciente->ci ?? '';
                        $medicoIdProc = $proc->medico_id;
                        $fechaFiltroProc = $proc->creado_at ? \Carbon\Carbon::parse($proc->creado_at)->subHours(4)->format('Y-m-d') : '';
                    @endphp
                    <div class="border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50 hover:bg-white transition-colors card-analisis card-procesada"
                         data-ci="{{ strtolower($ciPacienteProc) }}"
                         data-medico="{{ $medicoIdProc }}"
                         data-fecha="{{ $fechaFiltroProc }}">
                        
                        <div class="space-y-1 flex-1 w-full">
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-mono bg-emerald-100 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded font-black">PROCESADO</span>
                                <p class="text-[10px] text-slate-400 font-mono fecha-text">{{ \Carbon\Carbon::parse($proc->actualizado_at)->format('d/m/Y H:i') }}</p>
                            </div>
                           <h4 class="font-black text-slate-800 uppercase text-xs paciente-text flex items-center gap-2">
    @if($proc->paciente)
        {{ $proc->paciente->nombre }} {{ $proc->paciente->apellido_paterno }}
        
        @if($proc->paciente->trashed())
            <span class="bg-red-100 text-red-600 px-1.5 py-0.5 rounded text-[9px] border border-red-200 leading-none">
                PACIENTE INACTIVO
            </span>
        @endif
    @else
        PACIENTE DESCONOCIDO
        <span class="bg-red-800 text-white px-1.5 py-0.5 rounded text-[9px] leading-none shadow-sm">
            ELIMINADO DEL SISTEMA
        </span>
    @endif
</h4>
<p class="text-[10px] text-slate-400 font-mono ci-text">
    CI: {{ $proc->paciente->ci ?? 'S/N' }}
</p>
                            <div class="bg-white border border-slate-100 p-2 rounded mt-2">
                                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wide"><i class="fa-solid fa-robot mr-1"></i>Hallazgo Detectado:</p>
                                <p class="text-xs font-black text-slate-700 uppercase diag-text mt-0.5">{{ $proc->ia_diagnostico }}</p>
                                <p class="text-[9px] text-slate-400 font-medium italic mt-1 flex items-center gap-1"><i class="fa-solid fa-timeline"></i> Estimación de Edad/Evolución por IA: <span class="text-slate-600 font-bold">{{ $proc->tiempo_evolucion ?? 'No calculada' }}</span></p>
                            </div>
                        </div>

                        <div class="text-right flex flex-col items-center sm:items-end justify-between h-full min-w-[100px] border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-200 w-full sm:w-auto">
                            <div>
                                <span class="font-black text-red-600 text-xl block tracking-tight porc-text">{{ $proc->ia_porcentaje }}%</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest border border-slate-200 px-1.5 py-0.5 rounded bg-white mt-0.5 inline-block">Confianza</span>
                            </div>
                            
                            <button onclick='imprimirFichaPaciente("{{ $proc->paciente->nombre }} {{ $proc->paciente->apellido_paterno }}", "{{ $ciPacienteProc }}", "{{ $proc->ia_diagnostico }}", "{{ $proc->ia_porcentaje }}", "{{ \Carbon\Carbon::parse($proc->actualizado_at)->format("d/m/Y H:i") }}", "{{ asset("storage/" . $proc->imagen_lesion) }}", "{{ $proc->tiempo_evolucion ?? "No calculada" }}")' 
                                    class="mt-4 bg-white hover:bg-slate-100 text-blue-600 border border-slate-200 shadow-sm px-2.5 py-1.5 rounded text-[10px] font-bold uppercase tracking-wide flex items-center gap-1 transition-colors w-full sm:w-auto justify-center">
                                <i class="fa-solid fa-print"></i> Ficha Clínica
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-slate-400">
                        <i class="fa-solid fa-folder-open text-3xl opacity-30 block mb-2"></i>
                        <p class="font-bold text-xs uppercase">Historial de procesamiento vacío.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script>
    /**
     * Motor JavaScript de filtrado combinado en tiempo real.
     * Evalúa las entradas de CI, Médico asignado y Rango de Fechas en ambas bandejas.
     */
    function filtrarCasos() {
        const ciBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
        const medicoBuscado = document.getElementById('filtroMedico').value;
        const fechaDesde = document.getElementById('filtroDesde').value;

        const tarjetas = document.querySelectorAll('.card-analisis');

        tarjetas.forEach(card => {
            const dataCi = card.getAttribute('data-ci');
            const dataMedico = card.getAttribute('data-medico');
            const dataFecha = card.getAttribute('data-fecha');

            const coincideCi = ciBuscado === "" || dataCi.includes(ciBuscado);
            const coincideMedico = medicoBuscado === "TODOS" || dataMedico === medicoBuscado;
            
            let coincideFecha = true;
            if (fechaDesde && dataFecha < fechaDesde) coincideFecha = false;

            if (coincideCi && coincideMedico && coincideFecha) {
                card.style.setProperty('display', 'flex', 'important');
            } else {
                card.style.setProperty('display', 'none', 'important');
            }
        });
    }

    function limpiarFiltros() {
        document.getElementById('filtroTexto').value = "";
        document.getElementById('filtroMedico').value = "TODOS";
        document.getElementById('filtroDesde').value = "";
        filtrarCasos();
    }

    /**
     * Reporte Gerencial Masivo: Exportación estructurada a formato de hojas de cálculo
     */
    function exportarExcelIA() {
        let csv = ["Fecha Registro;Paciente Evaluado;Cédula Identidad;Diagnóstico Algorítmico CNN;Confianza Calculada"];
        document.querySelectorAll('.card-procesada').forEach(card => {
            if (card.style.display === "none") return;
            let fecha = card.querySelector('.fecha-text').innerText;
            let paciente = card.querySelector('.paciente-text').innerText;
            let ci = card.querySelector('.ci-text').innerText.replace('CI: ', '');
            let diag = card.querySelector('.diag-text').innerText;
            let porc = card.querySelector('.porc-text').innerText;
            csv.push(`"${fecha}";"${paciente}";"${ci}";"${diag}";"${porc}"`);
        });

        let blob = new Blob(["\ufeff" + csv.join("\n")], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Auditoria_Procesamiento_IA_" + new Date().toISOString().slice(0,10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    /**
     * Reporte Masivo Imprimible: Generación de la bitácora técnica de procesamiento de imágenes
     */
    function imprimirHistorialIA() {
        let tarjetas = document.querySelectorAll('.card-procesada');
        let win = window.open('', '', 'height=800,width=1000');
        win.document.write('<html><head><title>Bitácora de Procesamiento CNN</title>');
        win.document.write('<style>body{font-family:Arial,sans-serif;padding:30px;color:#1e293b;}h2{text-transform:uppercase;font-size:14px;letter-spacing:0.5px;color:#0f172a;border-bottom:2px solid #cbd5e1;padding-bottom:8px;}table{width:100%;border-collapse:collapse;font-size:11px;margin-top:15px;}th,td{border:1px solid #cbd5e1;padding:8px;text-align:left;}th{background-color:#f8fafc;color:#475569;font-weight:bold;text-transform:uppercase;}</style>');
        win.document.write('</head><body><h2>Clínica Vitruvio - Auditoría de Inferencia de Inteligencia Artificial</h2><table><thead><tr><th>Fecha/Hora Inferencia</th><th>Paciente Solicitante</th><th>C.I.</th><th>Hallazgo Diagnóstico CNN</th><th>Porcentaje Confianza</th></tr></thead><tbody>');
        
        tarjetas.forEach(card => {
            if (card.style.display === "none") return;
            let fecha = card.querySelector('.fecha-text').innerText;
            let paciente = card.querySelector('.paciente-text').innerText;
            let ci = card.querySelector('.ci-text').innerText.replace('CI: ', '');
            let diag = card.querySelector('.diag-text').innerText;
            let porc = card.querySelector('.porc-text').innerText;
            win.document.write(`<tr><td>${fecha}</td><td style="font-weight:bold;">${paciente}</td><td>${ci}</td><td>${diag}</td><td style="color:#ef4444;font-weight:bold;">${porc}</td></tr>`);
        });
        
        win.document.write('</tbody></table><p style="font-size:9px;color:#94a3b8;margin-top:30px;text-align:center;">Reporte de trazabilidad gerencial emitido de forma automatizada por el sistema.</p></body></html>');
        win.document.close();
        setTimeout(() => { win.print(); win.close(); }, 500);
    }

    /**
     * Ficha Analítica Unificada: Diseñada para impresión limpia de un único expediente clínico
     * Incluye datos del paciente, diagnóstico predictivo e imagenología de la lesión.
     */
    function imprimirFichaPaciente(nombre, ci, diagnostico, porcentaje, fecha, imagenUrl, tiempoEvolucion) {
        let win = window.open('', '', 'height=850,width=850');
        win.document.write('<html><head><title>Ficha Clínica Diagnóstica - ' + nombre + '</title>');
        win.document.write('<style>body{font-family:"Segoe UI",Arial,sans-serif;padding:45px;color:#1e293b;line-height:1.5;} .header{border-bottom:3px solid #4f46e5;padding-bottom:12px;margin-bottom:25px;} .header h1{margin:0;font-size:22px;text-transform:uppercase;color:#0f172a;letter-spacing:0.5px;} .header p{margin:4px 0 0;font-size:11px;text-transform:uppercase;font-weight:600;color:#64748b;} .grid{display:table;width:100%;margin-bottom:15px;} .col{display:table-cell;width:50%;padding-bottom:10px;} .label{font-size:9px;text-transform:uppercase;font-weight:bold;color:#94a3b8;margin-bottom:2px;letter-spacing:0.5px;} .value{font-size:13px;font-weight:bold;color:#334155;margin:0;} .result-box{background:#f8fafc;border:1px solid #e2e8f0;padding:20px;border-radius:10px;text-align:center;margin:25px 0;} .risk{font-size:38px;font-weight:900;color:#dc2626;margin:8px 0;letter-spacing:-1px;} .img-box{text-align:center;margin-top:25px;} img{max-height:260px;border-radius:8px;border:1px solid #cbd5e1;box-shadow:0 2px 4px rgba(0,0,0,0.05);}</style>');
        win.document.write('</head><body>');
        
        win.document.write('<div class="header"><h1>Clínica Vitruvio</h1><p>Sistema de Rastreo Dermatológico y Gemelo Digital</p></div>');
        
        win.document.write('<div class="grid"><div class="col"><p class="label">Paciente Solicitante</p><p class="value">' + nombre + '</p></div><div class="col"><p class="label">Cédula de Identidad</p><p class="value">' + ci + '</p></div></div>');
        win.document.write('<div class="grid"><div class="col"><p class="label">Fecha y Hora de Procesamiento</p><p class="value">' + fecha + '</p></div><div class="col"><p class="label">Arquitectura de Inferencia</p><p class="value">Red Neuronal Convolucional (CNN)</p></div></div>');
        
        win.document.write('<div class="result-box"><p class="label">Hallazgo Predictivo del Modelo</p><h2 style="margin:4px 0 8px 0;text-transform:uppercase;color:#1e1b4b;font-size:16px;">' + diagnostico + '</h2><p class="label">Tiempo Estimado de Evolución (Cálculo IA)</p><p class="value" style="margin-bottom:10px;color:#4f46e5;">' + tiempoEvolucion + '</p><p class="label">Índice de Confianza de Malignidad</p><p class="risk">' + porcentaje + '%</p></div>');
        
        win.document.write('<div class="img-box"><p class="label">Fotografía Dermatológica Analizada</p><img src="' + imagenUrl + '" alt="Lesión Objeto"></div>');
        
        win.document.write('<p style="margin-top:50px;font-size:9px;text-align:center;color:#94a3b8;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;">Aviso Informativo: Este reporte constituye una herramienta de soporte asistido por computadora. La validación del resultado es responsabilidad del médico especialista.</p>');
        win.document.write('</body></html>');
        
        win.document.close();
        setTimeout(() => { win.print(); win.close(); }, 700);
    }
</script>
@endsection
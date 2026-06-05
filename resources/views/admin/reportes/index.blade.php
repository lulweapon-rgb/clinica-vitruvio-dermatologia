@extends('layouts.app')

@section('title', 'Constructor de Reportes Combinados')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-[#0f172a] p-6 rounded-xl border border-slate-800 shadow-lg text-white">
        <div>
            <h2 class="text-2xl font-black text-emerald-400 tracking-tight uppercase flex items-center gap-2">
                <i class="fa-solid fa-table-columns"></i> Generador Dinámico de Reportes
            </h2>
            <p class="text-slate-400 text-sm mt-0.5">Seleccione los filtros y personalice las tablas para armar su reporte a medida (JOIN).</p>
        </div>
        
        <div class="flex items-center gap-2">
            <button onclick="exportarExcelDinamico()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 shadow-sm uppercase tracking-wider">
                <i class="fa-solid fa-file-excel text-sm"></i> Excel
            </button>
            <button onclick="imprimirReporteDinamico()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 shadow-sm uppercase tracking-wider">
                <i class="fa-solid fa-file-pdf text-sm"></i> PDF / Imprimir
            </button>
        </div>
    </div>

    <form action="{{ route('reportes.index') }}" method="GET" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Especialista</label>
                <select name="medico_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="todos">Todos los Especialistas</option>
                    @foreach($medicos as $med)
                        @if($med->usuario)
                            <option value="{{ $med->id }}" {{ $medico_id == $med->id ? 'selected' : '' }}>Dr. {{ $med->usuario->nombre }} {{ $med->usuario->apellido_paterno }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 border-t border-slate-100 pt-4">Fuentes de Datos a Combinar</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            
            <label class="flex items-start gap-3 p-4 rounded-lg border-2 border-emerald-500 bg-emerald-50 cursor-not-allowed">
                <input type="checkbox" name="tablas[]" value="pacientes" checked readonly class="mt-1 w-4 h-4 text-emerald-600 rounded border-slate-300 pointer-events-none">
                <div>
                    <span class="block font-bold text-emerald-800 text-sm">Datos Demográficos</span>
                    <span class="block text-xs text-emerald-600 mt-0.5">Tabla: pacientes (Base Requerida)</span>
                    <span class="block text-[10px] text-emerald-500 font-mono mt-1">Columnas: C.I., Nombre, Edad</span>
                </div>
            </label>

            <label class="flex items-start gap-3 p-4 rounded-lg border-2 transition-colors cursor-pointer {{ in_array('ia', $tablas_seleccionadas) ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-indigo-300' }}">
                <input type="checkbox" name="tablas[]" value="ia" {{ in_array('ia', $tablas_seleccionadas) ? 'checked' : '' }} class="mt-1 w-4 h-4 text-indigo-600 rounded border-slate-300">
                <div>
                    <span class="block font-bold text-slate-800 text-sm {{ in_array('ia', $tablas_seleccionadas) ? 'text-indigo-800' : '' }}">Análisis Motor CNN (IA)</span>
                    <span class="block text-xs text-slate-500 mt-0.5">Tabla: evaluaciones_clinicas</span>
                    <span class="block text-[10px] text-slate-400 font-mono mt-1">Columnas: Diagnóstico IA, Nivel Riesgo</span>
                </div>
            </label>

            <label class="flex items-start gap-3 p-4 rounded-lg border-2 transition-colors cursor-pointer {{ in_array('seguimientos', $tablas_seleccionadas) ? 'border-amber-500 bg-amber-50' : 'border-slate-200 hover:border-amber-300' }}">
                <input type="checkbox" name="tablas[]" value="seguimientos" {{ in_array('seguimientos', $tablas_seleccionadas) ? 'checked' : '' }} class="mt-1 w-4 h-4 text-amber-600 rounded border-slate-300">
                <div>
                    <span class="block font-bold text-slate-800 text-sm {{ in_array('seguimientos', $tablas_seleccionadas) ? 'text-amber-800' : '' }}">Controles Evolutivos</span>
                    <span class="block text-xs text-slate-500 mt-0.5">Tabla: seguimientos_evolutivos</span>
                    <span class="block text-[10px] text-slate-400 font-mono mt-1">Columnas: Diag. Humano, Evolución Tamaño</span>
                </div>
            </label>

        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-[#0f172a] hover:bg-slate-800 text-white px-6 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-code-merge"></i> Cargar y Combinar Datos
            </button>
        </div>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6" id="areaReporte">
        <div class="hidden print-header mb-6 pb-4 border-b-2 border-emerald-600">
            <h2 class="text-xl font-black text-slate-800 uppercase">Clínica Vitruvio - Reporte Personalizado</h2>
            <p class="text-sm text-slate-500">Periodo: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table id="tablaReportes" class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest border-b border-slate-200">
                        <th class="p-3">ID</th>
                        <th class="p-3">Paciente</th>
                        <th class="p-3">C.I.</th>
                        <th class="p-3 text-center">Edad</th>
                        
                        @if(in_array('ia', $tablas_seleccionadas))
                            <th class="p-3 border-l border-slate-200 bg-indigo-50/30">Diagnóstico IA</th>
                            <th class="p-3 bg-indigo-50/30">Riesgo IA</th>
                            <th class="p-3 bg-indigo-50/30">Médico Evaluador</th>
                        @endif

                        @if(in_array('seguimientos', $tablas_seleccionadas))
                            <th class="p-3 border-l border-slate-200 bg-amber-50/30">Último Control</th>
                            <th class="p-3 bg-amber-50/30">Diagnóstico Humano</th>
                            <th class="p-3 bg-amber-50/30">Cambio Tamaño</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-700">
                    @foreach($resultados as $rep)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-3 font-mono text-slate-400">#{{ $rep->id }}</td>
                            <td class="p-3 font-bold uppercase">
                                {{ $rep->paciente->nombre ?? 'PACIENTE ELIMINADO' }} {{ $rep->paciente->apellido_paterno ?? '' }}
                            </td>
                            <td class="p-3 font-mono text-slate-500">{{ $rep->paciente->ci ?? 'S/N' }}</td>
                            <td class="p-3 text-center">{{ $rep->paciente && $rep->paciente->fecha_nacimiento ? \Carbon\Carbon::parse($rep->paciente->fecha_nacimiento)->age : '-' }}</td>
                            
                            @if(in_array('ia', $tablas_seleccionadas))
                                <td class="p-3 font-bold text-indigo-900 border-l border-slate-100">{{ $rep->ia_diagnostico ?? 'Sin procesar' }}</td>
                                <td class="p-3 font-bold {{ $rep->ia_porcentaje > 75 ? 'text-red-500' : 'text-emerald-500' }}">{{ $rep->ia_porcentaje ? $rep->ia_porcentaje . '%' : '-' }}</td>
                                <td class="p-3 uppercase text-[10px] text-slate-500">Dr. {{ $rep->medico->usuario->nombre ?? 'N/A' }}</td>
                            @endif

                            @if(in_array('seguimientos', $tablas_seleccionadas))
                                @php $ultimo_seg = $rep->seguimientos->last(); @endphp
                                <td class="p-3 font-mono border-l border-slate-100">{{ $ultimo_seg ? \Carbon\Carbon::parse($ultimo_seg->fecha_control)->format('d/m/Y') : 'Sin controles' }}</td>
                                <td class="p-3 font-bold text-amber-900">{{ $ultimo_seg->diagnostico_definitivo ?? '-' }}</td>
                                <td class="p-3">{{ $ultimo_seg->cambio_tamano ?? '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inicialización simple por si necesitas agregar lógica al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Módulo de Reportes Cargado.");
    });

    function exportarExcelDinamico() {
        let tabla = document.getElementById('tablaReportes');
        
        if (!tabla) {
            alert("No hay ninguna tabla para exportar.");
            return;
        }

        let csv = [];
        
        // 1. Extraemos las cabeceras directamente del HTML (TH)
        let headers = [];
        tabla.querySelectorAll('thead th').forEach(th => {
            headers.push('"' + th.innerText.trim() + '"');
        });
        csv.push(headers.join(";"));

        // 2. Extraemos los datos de las filas (TD)
        let rows = tabla.querySelectorAll('tbody tr');
        
        rows.forEach(tr => {
            // Ignoramos la fila vacía si dice "No se encontraron datos..." (colSpan alto)
            let primerTd = tr.querySelector('td');
            if (primerTd && primerTd.colSpan > 1) return;
            
            // Solo exportamos las filas visibles
            if (tr.style.display !== 'none') {
                let rowData = [];
                tr.querySelectorAll('td').forEach(td => {
                    let texto = td.innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                    rowData.push('"' + texto + '"');
                });
                csv.push(rowData.join(";"));
            }
        });

        // 3. Generamos la descarga
        let contenidoCSV = "\ufeff" + csv.join("\n"); // \ufeff para los acentos
        let blob = new Blob([contenidoCSV], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Reporte_Cruzado_IA_" + new Date().toISOString().slice(0, 10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function imprimirReporteDinamico() {
        let contenido = document.getElementById('areaReporte').innerHTML;
        
        let ventanaImpresion = window.open('', '', 'height=800,width=1200');
        
        ventanaImpresion.document.write('<html><head><title>Imprimir Reporte</title>');
        ventanaImpresion.document.write('<style>');
        ventanaImpresion.document.write('body { font-family: Arial, sans-serif; padding: 25px; color: #0f172a; }');
        ventanaImpresion.document.write('.print-header { display: block !important; margin-bottom: 20px; }');
        ventanaImpresion.document.write('table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 15px; }');
        ventanaImpresion.document.write('th, td { border-bottom: 1px solid #e2e8f0; padding: 8px; text-align: left; }');
        ventanaImpresion.document.write('th { text-transform: uppercase; font-size: 10px; color: #64748b; background-color: #f8fafc; font-weight: bold; }');
        ventanaImpresion.document.write('</style></head><body>');
        
        ventanaImpresion.document.write(contenido);
        
        ventanaImpresion.document.write('</body></html>');
        ventanaImpresion.document.close();
        
        setTimeout(function() { 
            ventanaImpresion.print(); 
            ventanaImpresion.close(); 
        }, 500);
    }
</script>
@endpush
@endsection 
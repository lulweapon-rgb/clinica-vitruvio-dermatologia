@extends('layouts.app')

@section('title', 'Constructor de Reportes')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="bg-slate-900 p-6 rounded-xl border border-slate-800 shadow-lg text-white flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-2xl font-black text-emerald-400 tracking-tight uppercase flex items-center gap-2">
            <i class="fa-solid fa-puzzle-piece"></i> Constructor de Cruce de Datos
        </h2>
        
        <div class="flex items-center gap-2">
            <button onclick="exportarExcelDinamico()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm uppercase">
                <i class="fa-solid fa-file-excel"></i> Exportar Excel
            </button>
            <button onclick="imprimirReporteDinamico()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm uppercase">
                <i class="fa-solid fa-file-pdf"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    <form action="{{ route('reportes.index') }}" method="GET" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase">Especialista</label>
                <select name="medico_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="todos">Todos los Especialistas</option>
                    @foreach($medicos as $med)
                        @if($med->usuario)
                            <option value="{{ $med->id }}" {{ $medico_id == $med->id ? 'selected' : '' }}>
                                Dr. {{ strtoupper($med->usuario->nombre . ' ' . $med->usuario->apellido_paterno) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-4 mb-6">
            <label class="flex items-center gap-2 font-bold cursor-pointer">
                <input type="checkbox" name="tablas[]" value="pacientes" checked disabled> Datos Demográficos
            </label>
            <label class="flex items-center gap-2 font-bold cursor-pointer">
                <input type="checkbox" name="tablas[]" value="ia" {{ in_array('ia', $tablas_seleccionadas) ? 'checked' : '' }}> Análisis IA
            </label>
            <label class="flex items-center gap-2 font-bold cursor-pointer">
                <input type="checkbox" name="tablas[]" value="seguimientos" {{ in_array('seguimientos', $tablas_seleccionadas) ? 'checked' : '' }}> Controles
            </label>
        </div>

        <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-xs font-bold uppercase hover:bg-slate-700 transition-colors">
            Cargar y Combinar Datos
        </button>
    </form>

    <div class="bg-white rounded-xl border p-6 overflow-x-auto">
        <table id="tablaReportes" class="w-full text-left border-collapse table-auto">
            <thead>
                <tr class="bg-slate-100 text-[10px] uppercase border-b border-slate-200">
                    <th class="p-3 border-r border-slate-200">Paciente</th>
                    @if(in_array('ia', $tablas_seleccionadas)) 
                        <th class="p-3 border-r border-slate-200">Diag. IA</th> 
                        <th class="p-3 border-r border-slate-200 text-center">Nivel de Riesgo</th> 
                    @endif
                    @if(in_array('seguimientos', $tablas_seleccionadas)) 
                        <th class="p-3">Diag. Clínico Humano</th> 
                    @endif
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($resultados as $fila)
                    <tr class="hover:bg-slate-50">
                        <td class="p-3 font-bold uppercase border-r border-slate-100">
                            {{ $fila->paciente->nombre ?? 'PACIENTE DESCONOCIDO' }} {{ $fila->paciente->apellido_paterno ?? '' }}
                        </td>
                        
                        @if(in_array('ia', $tablas_seleccionadas))
                            <td class="p-3 border-r border-slate-100">{{ $fila->ia_diagnostico ?? 'Sin procesar' }}</td>
                            <td class="p-3 border-r border-slate-100 text-center font-bold {{ $fila->ia_porcentaje > 75 ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $fila->ia_porcentaje ? $fila->ia_porcentaje.'%' : '-' }}
                            </td>
                        @endif

                        @if(in_array('seguimientos', $tablas_seleccionadas))
                            @php $ultimo_seg = $fila->seguimientos ? $fila->seguimientos->last() : null; @endphp
                            <td class="p-3">
                                {{ $ultimo_seg ? $ultimo_seg->diagnostico_definitivo : 'Sin controles registrados' }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-8 text-center text-slate-400 font-medium">
                            No se encontraron datos cruzados con los filtros actuales.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function exportarExcelDinamico() {
        let csv = [];
        let tabla = document.getElementById('tablaReportes');
        
        if (!tabla) {
            alert("No hay ninguna tabla para exportar.");
            return;
        }

        // 1. Extraer dinámicamente las cabeceras (TH)
        let headers = [];
        let thead = tabla.querySelectorAll('thead th');
        thead.forEach(th => {
            headers.push('"' + th.innerText.trim() + '"');
        });
        csv.push(headers.join(";"));

        // 2. Extraer dinámicamente los datos de las filas (TD)
        let rows = tabla.querySelectorAll('tbody tr');
        rows.forEach(tr => {
            // Ignoramos la fila de "No se encontraron datos"
            if (tr.querySelector('td').colSpan > 1) return;
            
            if (tr.style.display !== 'none') {
                let rowData = [];
                tr.querySelectorAll('td').forEach(td => {
                    let texto = td.innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                    rowData.push('"' + texto + '"');
                });
                csv.push(rowData.join(";"));
            }
        });

        // 3. Generar y descargar el archivo
        let contenidoCSV = "\ufeff" + csv.join("\n"); 
        let blob = new Blob([contenidoCSV], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Reporte_Cruzado_IA_" + new Date().toISOString().slice(0, 10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    function imprimirReporteDinamico() {
        let tabla = document.getElementById('tablaReportes');
        
        if (!tabla) {
            alert("No hay ninguna tabla para imprimir.");
            return;
        }

        let ventanaImpresion = window.open('', '', 'height=800,width=1200');
        
        ventanaImpresion.document.write('<html><head><title>Reporte Cruzado Inteligente</title>');
        ventanaImpresion.document.write('<style>');
        ventanaImpresion.document.write('body { font-family: Arial, sans-serif; padding: 25px; color: #0f172a; }');
        ventanaImpresion.document.write('h2 { text-transform: uppercase; font-size: 18px; font-weight: 900; margin-bottom: 2px; }');
        ventanaImpresion.document.write('.fecha { font-size: 11px; color: #64748b; margin-bottom: 20px; }');
        ventanaImpresion.document.write('table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 15px; }');
        ventanaImpresion.document.write('th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }');
        ventanaImpresion.document.write('th { background-color: #f8fafc; font-weight: bold; color: #334155; text-transform: uppercase; }');
        ventanaImpresion.document.write('td { color: #1e293b; }'); 
        ventanaImpresion.document.write('</style></head><body>');
        
        ventanaImpresion.document.write('<h2>Clínica Vitruvio - Reporte Cruzado Dermatológico</h2>');
        ventanaImpresion.document.write('<div class="fecha">Fecha de generación: ' + new Date().toLocaleString('es-ES') + '</div>');
        
        ventanaImpresion.document.write('<table>' + tabla.innerHTML + '</table>');
        
        ventanaImpresion.document.write('</body></html>');
        ventanaImpresion.document.close();
        
        setTimeout(function() { 
            ventanaImpresion.print(); 
            ventanaImpresion.close(); 
        }, 500);
    }
</script>
@endsection
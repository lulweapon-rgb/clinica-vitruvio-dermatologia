@extends('layouts.app')

@section('title', 'Catálogo de Especialidades')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Catálogo Especialidades Médicas</h2>
            <p class="text-slate-500 text-sm mt-0.5">Gestión de áreas clínicas, auditoría cronológica y reportería avanzada con combinación de filtros.</p>
        </div>
        <div class="text-sm font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg border">
            Inicio / Especialidades
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

    <div class="bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-700 text-white space-y-4">
        <div class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-400">
            <i class="fa-solid fa-filter text-blue-400"></i> Filtros de Auditoría y Trazabilidad
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Buscar Especialidad (Nombre / Descripción)</label>
                <input type="text" id="filtroTexto" oninput="resetearPaginaYFiltrar()" placeholder="Escribe el área médica..." class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-400 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Estado en Catálogo</label>
                <select id="filtroEstado" onchange="resetearPaginaYFiltrar()" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-sm text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="TODOS">AMBOS (Todos)</option>
                    <option value="ACTIVO">SOLO ACTIVAS</option>
                    <option value="INACTIVO">SOLO INACTIVAS</option>
                </select>
            </div>

            <div class="flex items-end">
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
                <i class="fa-solid fa-plus text-sm"></i> Nuevo Registro
            </button>
        </div>

        <div class="p-4 bg-white">
            <table class="w-full text-left border-collapse table-auto border border-slate-200" id="tablaMaestraEspecialidades">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider border-b border-slate-200 text-xs">
                        <th class="p-4 font-bold text-center border border-slate-200 w-[5%]">#</th>
                        <th class="p-4 font-bold border border-slate-200 w-[25%]">Especialidad</th>
                        <th class="p-4 font-bold border border-slate-200 w-[35%]">Descripción Funcional</th>
                        <th class="p-4 font-bold border border-slate-200 w-[12%]">Hora Registro</th>
                        <th class="p-4 font-bold border border-slate-200 w-[12%]">Hora Inactivación</th>
                        <th class="p-4 font-bold text-center border border-slate-200 w-[11%]">Estado</th>
                        <th class="p-4 font-bold text-center border border-slate-200 w-[12%]">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700" id="cuerpoTablaEspecialidades">
                    @forelse($especialidades as $index => $esp)
                    @php
                        $horaRegistro = $esp->created_at ? \Carbon\Carbon::parse($esp->created_at)->subHours(4)->format('d/m/Y H:i') : 'No registrada';
                        $horaInactivacion = $esp->trashed() ? \Carbon\Carbon::parse($esp->deleted_at)->subHours(4)->format('d/m/Y H:i') : '';
                        $estadoFiltro = $esp->trashed() ? 'INACTIVO' : 'ACTIVO';
                        $textoBusqueda = strtolower($esp->nombre . ' ' . $esp->descripcion);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $esp->trashed() ? 'bg-slate-50 opacity-60' : '' }}"
                        data-busqueda="{{ $textoBusqueda }}"
                        data-estado="{{ $estadoFiltro }}">
                        
                        <td class="p-4 text-center text-slate-400 font-mono border border-slate-100">{{ $index + 1 }}</td>
                        <td class="p-4 font-bold text-blue-700 uppercase border border-slate-100">{{ $esp->nombre }}</td>
                        <td class="p-4 text-slate-500 border border-slate-100 italic break-words whitespace-normal">{{ $esp->descripcion ?? 'Sin descripción detallada registrada.' }}</td>
                        <td class="p-4 text-slate-600 font-mono border border-slate-100 text-xs">{{ $horaRegistro }}</td>
                        <td class="p-4 font-mono border border-slate-100 text-xs">
                            @if($esp->trashed())
                                <span class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded border border-red-100">{{ $horaInactivacion }}</span>
                            @else
                                <span class="text-slate-400 italic">Vigente / Activa</span>
                            @endif
                        </td>
                        
                        <td class="p-4 text-center border border-slate-100">
                            @if($esp->trashed())
                                <form action="{{ route('especialidades.restore', $esp->id) }}" method="POST" id="form-restore-{{ $esp->id }}">
                                    @csrf
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" class="sr-only peer" onchange="document.getElementById('form-restore-{{ $esp->id }}').submit()">
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

                        <td class="p-4 text-center border border-slate-100">
                            <div class="flex justify-center gap-1.5">
                                <button onclick='abrirModalVer(@json($esp))' class="bg-slate-500 hover:bg-slate-600 text-white p-2 rounded-lg shadow-sm transition-colors" title="Ver Detalles">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                @if(!$esp->trashed())
                                    <button onclick='abrirModalEditar(@json($esp))' class="bg-[#007bff] hover:bg-blue-700 text-white p-2 rounded-lg shadow-sm transition-colors" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button onclick="abrirModalEliminar({{ $esp->id }})" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-sm transition-colors" title="Inactivar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                @else
                                    <span class="w-8"></span> 
                                    <span class="w-8"></span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="filaTablaVacia">
                        <td colspan="7" class="p-8 text-center text-slate-400 font-medium">
                            <i class="fa-solid fa-notes-medical text-3xl block mb-2 opacity-30"></i> No se encuentran especialidades médicas indexadas.
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

<div id="modalVer" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
        <div class="flex justify-between items-center p-5 border-b bg-slate-800 text-white">
            <h3 class="text-base font-bold"><i class="fa-solid fa-notes-medical mr-2 text-blue-400"></i> Detalles de Especialidad</h3>
            <button onclick="document.getElementById('modalVer').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <div class="p-6 space-y-4 text-sm text-slate-700">
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Nombre del Área Médica</p>
                <p id="ver_nombre" class="text-lg font-black text-blue-700 uppercase"></p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Descripción de Funciones</p>
                <p id="ver_descripcion" class="text-slate-600 bg-slate-50 p-4 rounded-lg border border-slate-100 leading-relaxed"></p>
            </div>
        </div>
        <div class="flex justify-end p-5 bg-slate-50 border-t border-slate-200">
            <button onclick="document.getElementById('modalVer').classList.add('hidden')" class="bg-slate-500 hover:bg-slate-600 transition-colors text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm">Cerrar Detalles</button>
        </div>
    </div>
</div>

<div id="modalRegistro" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 animate-fade-in">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-bold text-slate-700">Nueva Especialidad Clínica</h3>
            <button onclick="document.getElementById('modalRegistro').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>
        <form action="{{ route('especialidades.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Nombre de la Especialidad</label>
                    <input type="text" name="nombre" placeholder="Ej. Dermatología Quirúrgica" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 uppercase mb-1">Descripción de Funciones</label>
                    <textarea name="descripcion" rows="3" placeholder="Detalles operativos o alcance de la especialidad..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-5 border-t bg-slate-50">
                <button type="button" onclick="document.getElementById('modalRegistro').classList.add('hidden')" class="bg-slate-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Cancelar</button>
                <button type="submit" class="bg-[#007bff] text-white px-5 py-2 rounded-lg text-sm font-bold">Guardar Registro</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditar" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
        <div class="flex justify-between items-center p-5 border-b bg-slate-50">
            <h3 class="text-base font-bold text-slate-700">Modificar Especialidad</h3>
            <button onclick="document.getElementById('modalEditar').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold">&times;</button>
        </div>
        <form id="formEditar" method="POST" autocomplete="off">
            @csrf
            @method('PUT') 
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Nombre de la Especialidad</label>
                    <input type="text" name="nombre" id="edit_nombre" pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" class="w-full border rounded-lg px-3 py-2 text-sm uppercase" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-600 mb-1">Descripción</label>
                    <textarea name="descripcion" id="edit_descripcion" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-5 bg-slate-50 border-t">
                <button type="button" onclick="document.getElementById('modalEditar').classList.add('hidden')" class="bg-slate-500 text-white px-5 py-2 rounded-lg text-sm font-bold">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 z-50 bg-slate-900/60 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">¿Inactivar Especialidad?</h3>
            <p class="text-sm text-slate-500">El área pasará a estado inactivo y no se podrá heredar en nuevos registros médicos.</p>
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
    let paginaActual = 1;
    const registrosPorPagina = 10;

    window.addEventListener('DOMContentLoaded', function() {
        ejecutarFiltrosCombinados();
    });

    function resetearPaginaYFiltrar() {
        paginaActual = 1; 
        ejecutarFiltrosCombinados();
    }

    // --- MOTOR DE BÚSQUEDA DINÁMICA COMBINADA ---
    function ejecutarFiltrosCombinados() {
        const textoBuscado = document.getElementById('filtroTexto').value.trim().toLowerCase();
        const estadoBuscado = document.getElementById('filtroEstado').value;

        const filas = document.querySelectorAll('#cuerpoTablaEspecialidades tr');
        let filasFiltradas = [];

        filas.forEach(fila => {
            if (!fila.hasAttribute('data-busqueda')) return;

            const dataBusqueda = fila.getAttribute('data-busqueda');
            const dataEstado = fila.getAttribute('data-estado');

            const coincideTexto = textoBuscado === "" || dataBusqueda.includes(textoBuscado);
            const coincideEstado = estadoBuscado === "TODOS" || dataEstado === estadoBuscado;

            if (coincideTexto && coincideEstado) {
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
            info.innerText = "Mostrando 0 de 0 registros";
        } else {
            info.innerText = `Mostrando ${inicio + 1} a ${Math.min(fin, totalRegistros)} de ${totalRegistros} registros`;
        }

        const contenedorControles = document.getElementById('paginacionControles');
        contenedorControles.innerHTML = "";

        let btnAnterior = document.createElement('button');
        btnAnterior.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
        btnAnterior.className = `px-3 py-1.5 rounded-lg border text-xs font-bold transition-all ${paginaActual === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white hover:bg-slate-50 text-slate-700 shadow-sm'}`;
        if (paginaActual > 1) { btnAnterior.onclick = function() { paginaActual--; ejecutarFiltrosCombinados(); }; }
        contenedorControles.appendChild(btnAnterior);

        for (let i = 1; i <= totalPaginas; i++) {
            let btnPagina = document.createElement('button');
            btnPagina.innerText = i;
            btnPagina.className = `px-3 py-1.5 rounded-lg border text-xs font-bold shadow-sm transition-all ${paginaActual === i ? 'bg-blue-600 text-white border-blue-600' : 'bg-white hover:bg-slate-50 text-slate-700'}`;
            btnPagina.onclick = function() { paginaActual = i; ejecutarFiltrosCombinados(); };
            contenedorControles.appendChild(btnPagina);
        }

        let btnSiguiente = document.createElement('button');
        btnSiguiente.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
        btnSiguiente.className = `px-3 py-1.5 rounded-lg border text-xs font-bold transition-all ${paginaActual === totalPaginas ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white hover:bg-slate-50 text-slate-700 shadow-sm'}`;
        if (paginaActual < totalPaginas) { btnSiguiente.onclick = function() { paginaActual++; ejecutarFiltrosCombinados(); }; }
        contenedorControles.appendChild(btnSiguiente);
    }

    function limpiarFiltros() {
        document.getElementById('filtroTexto').value = "";
        document.getElementById('filtroEstado').value = "TODOS";
        resetearPaginaYFiltrar();
    }

    // --- REPORTERÍA A EXCEL ---
    function exportarExcelNativo() {
        let csv = [];
        let headerData = ["#", "Especialidad", "Descripcion Funcional", "Hora Registro", "Hora Inactivacion"];
        csv.push(headerData.join(";"));

        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaEspecialidades tr');
        cuerpoFilas.forEach(fila => {
            if (fila.style.display === "none" && !fila.hasAttribute('data-busqueda')) return;
            
            let filaData = [];
            let celdas = fila.querySelectorAll("td");
            if (celdas.length >= 5) {
                for (let j = 0; j < 5; j++) {
                    let texto = celdas[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                    filaData.push('"' + texto + '"');
                }
                csv.push(filaData.join(";"));
            }
        });
        
        let contenidoCSV = "\ufeff" + csv.join("\n");
        let blob = new Blob([contenidoCSV], { type: "text/csv;charset=utf-8;" });
        let link = document.createElement("a");
        link.download = "Reporte_Catálogo_Especialidades_" + new Date().toISOString().slice(0,10) + ".csv";
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }

    // --- REPORTERÍA A PDF / IMPRESIÓN ---
    function imprimirReporteNativo() {
        let cuerpoFilas = document.querySelectorAll('#cuerpoTablaEspecialidades tr');
        let ventanaImpresion = window.open('', '', 'height=800,width=1000');
        
        ventanaImpresion.document.write('<html><head><title>Reporte - Especialidades</title>');
        ventanaImpresion.document.write('<style>');
        ventanaImpresion.document.write('body { font-family: Arial, sans-serif; padding: 25px; color: #0f172a; }');
        ventanaImpresion.document.write('h2 { text-transform: uppercase; font-size: 15px; font-weight: bold; margin-bottom: 4px; }');
        ventanaImpresion.document.write('p { font-size: 12px; color: #475569; margin-bottom: 20px; }');
        ventanaImpresion.document.write('table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; }');
        ventanaImpresion.document.write('th, td { border: 1px solid #94a3b8; padding: 8px; text-align: left; }');
        ventanaImpresion.document.write('th { background-color: #f8fafc; font-weight: bold; text-transform: uppercase; }');
        ventanaImpresion.document.write('th:nth-last-child(1), td:nth-last-child(1), th:nth-last-child(2), td:nth-last-child(2) { display: none; }');
        ventanaImpresion.document.write('</style></head><body>');
        ventanaImpresion.document.write('<h2>Clínica Vitruvio - Catálogo de Especialidades Médicas</h2>');
        ventanaImpresion.document.write('<p>Reporte de auditoría emitido el: ' + new Date().toLocaleString() + '</p>');
        
        ventanaImpresion.document.write('<table>');
        ventanaImpresion.document.write('<thead>' + document.querySelector('#tablaMaestraEspecialidades thead tr').innerHTML + '</thead>');
        ventanaImpresion.document.write('<tbody>');

        cuerpoFilas.forEach(fila => {
            if (fila.hasAttribute('data-busqueda') && fila.style.display !== "none") {
                ventanaImpresion.document.write('<tr>' + fila.innerHTML + '</tr>');
            }
        });

        ventanaImpresion.document.write('</tbody></table>');
        ventanaImpresion.document.write('<script>');
        ventanaImpresion.document.write('document.querySelectorAll("tr").forEach(tr => {');
        ventanaImpresion.document.write('  if(tr.children.length >= 7) {');
        ventanaImpresion.document.write('    tr.children[6].style.display = "none";'); 
        ventanaImpresion.document.write('    tr.children[5].style.display = "none";');  
        ventanaImpresion.document.write('  }');
        ventanaImpresion.document.write('});');
        ventanaImpresion.document.write('<\/script>');

        ventanaImpresion.document.write('</body></html>');
        ventanaImpresion.document.close();
        
        setTimeout(function() { ventanaImpresion.print(); ventanaImpresion.close(); }, 500);
    }

    // --- ACCIONES MODALES ---

    // 1. FUNCIÓN NUEVA PARA EL OJITO (VISUALIZAR)
    function abrirModalVer(especialidad) {
        document.getElementById('ver_nombre').innerText = especialidad.nombre;
        document.getElementById('ver_descripcion').innerText = especialidad.descripcion || 'Sin descripción funcional registrada.';
        document.getElementById('modalVer').classList.remove('hidden');
    }

    function abrirModalEditar(especialidad) {
        document.getElementById('edit_nombre').value = especialidad.nombre;
        document.getElementById('edit_descripcion').value = especialidad.descripcion;
        document.getElementById('formEditar').action = "/especialidades/" + especialidad.id;
        document.getElementById('modalEditar').classList.remove('hidden');
    }

    function abrirModalEliminar(id) {
        document.getElementById('formEliminar').action = "/especialidades/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
    }
</script>
@endsection
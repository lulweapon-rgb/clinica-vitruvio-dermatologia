@extends('layouts.app')
@section('title', 'Control de Historiales Clínicos')
@section('content')

<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Control de Historiales Clínicos</h2>
            <p class="text-slate-500 text-sm mt-0.5">Gestión centralizada de antecedentes médicos y factores de riesgo.</p>
        </div>
        <div class="text-sm font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg border">
            Historial Clínico / Inicio
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-blue-500 text-base"></i> {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-xmark text-red-500 text-base"></i> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500">
                <i class="fa-solid fa-users-viewfinder text-blue-500"></i> Directorio de Pacientes
            </div>
            <div class="relative w-full max-w-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="buscadorHistorial" onkeyup="filtrarTabla()" placeholder="Buscar por CI o Nombre..." class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto whitespace-nowrap" id="tablaHistoriales">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <th class="p-3 border-r border-slate-200 font-bold text-center w-8">#</th>
                        <th class="p-3 border-r border-slate-200 font-bold">Paciente Identificado</th>
                        <th class="p-3 border-r border-slate-200 font-bold">C.I. Documento</th>
                        <th class="p-3 border-r border-slate-200 font-bold text-center">Estado del Expediente Base</th>
                        <th class="p-3 border-r border-slate-200 font-bold text-center">Última Actualización</th>
                        <th class="p-3 border-slate-200 font-bold text-center">Acciones Médicas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @forelse($pacientes as $index => $paciente)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3 text-center text-slate-400 font-mono border-r border-slate-100">{{ $index + 1 }}</td>
                        
                        <td class="p-3 font-bold text-slate-800 uppercase border-r border-slate-100 name-col">
                            {{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}
                        </td>
                        
                        <td class="p-3 font-mono text-slate-600 border-r border-slate-100 ci-col">
                            {{ $paciente->ci }}
                        </td>

                        <td class="p-3 text-center border-r border-slate-100">
                            @if($paciente->antecedentes)
                                <span class="bg-emerald-100 text-emerald-700 text-[10px] px-2.5 py-1 rounded-md font-bold uppercase tracking-wider border border-emerald-200 shadow-sm">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Registrado
                                </span>
                            @else
                                <span class="bg-red-50 text-red-600 text-[10px] px-2.5 py-1 rounded-md font-bold uppercase tracking-wider border border-red-200 shadow-sm animate-pulse">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Falta Aperturar
                                </span>
                            @endif
                        </td>

                        <td class="p-3 font-mono text-slate-500 border-r border-slate-100 text-center">
                            @if($paciente->antecedentes)
                                {{ $paciente->antecedentes->updated_at->format('d/m/Y H:i') }}
                            @else
                                <span class="italic text-slate-300">-</span>
                            @endif
                        </td>

                        <td class="p-3 text-center">
                            <div class="flex justify-center gap-2">
                                @if($paciente->antecedentes)
                                    <a href="{{ route('historiales.show', $paciente->id) }}" class="bg-slate-500 hover:bg-slate-600 text-white p-2 rounded-lg shadow-sm transition-colors" title="Ver Expediente">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('historiales.edit', $paciente->id) }}" class="bg-[#007bff] hover:bg-blue-700 text-white p-2 rounded-lg shadow-sm transition-colors" title="Actualizar Antecedentes">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                @else
                                    <a href="{{ route('historiales.create', $paciente->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg shadow-sm transition-colors font-bold text-xs uppercase tracking-wide flex items-center gap-2">
                                        <i class="fa-solid fa-folder-plus"></i> Aperturar Ficha
                                    </a>
                                    <a href="{{ route('historiales.edit', $paciente->id) }}" class="bg-[#007bff] hover:bg-blue-700 text-white p-2 rounded-lg shadow-sm transition-colors" title="Actualizar Antecedentes">
    <i class="fa-solid fa-pen-to-square text-xs"></i>
</a>

<button onclick="abrirModalEliminar({{ $paciente->id }})" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-sm transition-colors" title="Eliminar Expediente Base">
    <i class="fa-solid fa-trash text-xs"></i>
</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                            <i class="fa-solid fa-users-slash text-3xl block mb-2 opacity-30"></i> No hay pacientes registrados en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Filtro simple para buscar pacientes rápido en la tabla
    function filtrarTabla() {
        let input = document.getElementById("buscadorHistorial").value.toUpperCase();
        let trs = document.getElementById("tablaHistoriales").getElementsByTagName("tr");

        for (let i = 1; i < trs.length; i++) { // Ignorar el thead
            let tdNombre = trs[i].getElementsByClassName("name-col")[0];
            let tdCi = trs[i].getElementsByClassName("ci-col")[0];
            
            if (tdNombre || tdCi) {
                let txtValueNombre = tdNombre.textContent || tdNombre.innerText;
                let txtValueCi = tdCi.textContent || tdCi.innerText;
                
                if (txtValueNombre.toUpperCase().indexOf(input) > -1 || txtValueCi.toUpperCase().indexOf(input) > -1) {
                    trs[i].style.display = "";
                } else {
                    trs[i].style.display = "none";
                }
            }       
        }
    }

    <div id="modalEliminar" class="fixed inset-0 z-[100] bg-slate-900/60 hidden items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fa-solid fa-file-circle-xmark"></i></div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">¿Borrar Expediente Base?</h3>
            <p class="text-sm text-slate-500">Esto eliminará los antecedentes médicos de este paciente. Esta acción no afecta sus evaluaciones.</p>
        </div>
        <form id="formEliminar" method="POST" class="flex justify-center gap-3 p-5 bg-slate-50 border-t">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modalEliminar').classList.add('hidden'); document.getElementById('modalEliminar').classList.remove('flex');" class="bg-slate-300 text-slate-700 px-5 py-2 rounded-lg text-sm font-bold transition-colors hover:bg-slate-400">Cancelar</button>
            <button type="submit" class="bg-red-500 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-red-600 transition-colors">Sí, Borrar</button>
        </form>
    </div>
</div>

<script>
    function abrirModalEliminar(paciente_id) {
        document.getElementById('formEliminar').action = "/historiales/eliminar/" + paciente_id;
        document.getElementById('modalEliminar').classList.remove('hidden');
        document.getElementById('modalEliminar').classList.add('flex');
    }
</script>
</script>
@endsection
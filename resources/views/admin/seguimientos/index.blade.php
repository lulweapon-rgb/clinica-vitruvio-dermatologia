@extends('layouts.app')

@section('title', 'Directorio de Seguimiento Evolutivo')

@section('content')
<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-lg text-white">
        <div>
            <h2 class="text-2xl font-black text-amber-400 tracking-tight uppercase flex items-center gap-2">
                <i class="fa-solid fa-users-viewfinder"></i> Directorio de Pacientes Oncológicos
            </h2>
            <p class="text-slate-400 text-sm mt-0.5">Gestión de casos analizados por Inteligencia Artificial para seguimiento del Gemelo Digital.</p>
        </div>
        <div class="text-xs font-mono font-bold text-amber-400 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
            Módulo Clínico Activo
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-1/2 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400"></i>
            <input type="text" id="buscadorPacientes" onkeyup="filtrarDirectorio()" placeholder="Buscar expediente por C.I. o Apellido..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none font-bold text-slate-700">
        </div>
        <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">
            <i class="fa-solid fa-chart-line text-amber-500 mr-1"></i> {{ $evaluaciones->count() }} Casos en Observación
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="gridPacientes">
        @forelse($evaluaciones as $eval)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col tarjeta-paciente" data-busqueda="{{ strtolower($eval->paciente->ci . ' ' . $eval->paciente->apellido_paterno . ' ' . $eval->paciente->nombre) }}">
                
                <div class="p-4 flex gap-4 items-start border-b border-slate-100 bg-slate-50/50">
                    <img src="{{ asset('storage/' . $eval->imagen_lesion) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 shadow-sm">
                    <div>
<h3 class="font-black text-slate-800 uppercase text-sm leading-tight">
    {{ $eval->paciente->nombre }} {{ $eval->paciente->apellido_paterno }}
    
    @if($eval->paciente->trashed())
        <span class="ml-2 bg-red-100 text-red-600 text-[9px] px-2 py-0.5 rounded border border-red-200">INACTIVO</span>
    @endif
</h3>                        
<p class="text-xs text-slate-500 font-mono mt-1">C.I. {{ $eval->paciente->ci }}</p>
                    </div>
                </div>

                <div class="p-4 flex-1 space-y-3">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Diagnóstico Base (IA CNN)</p>
                        <p class="text-xs font-black text-slate-700 uppercase">{{ $eval->ia_diagnostico }}</p>
                    </div>
                    
                    <div class="flex justify-between items-end bg-slate-50 p-2 rounded border border-slate-100">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Riesgo Inicial</p>
                            <p class="text-lg font-black text-red-500 leading-none">{{ $eval->ia_porcentaje }}%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Fecha Captura</p>
                            <p class="text-xs font-mono text-slate-600">{{ \Carbon\Carbon::parse($eval->creado_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-0">
                    <a href="{{ route('seguimientos.timeline', $eval->id) }}" class="w-full block text-center bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors shadow-sm">
                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Abrir Historial Evolutivo
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400">
                <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-50"></i>
                <p class="font-bold uppercase text-sm">No hay pacientes listos para seguimiento</p>
                <p class="text-xs mt-1">Debe procesar al menos una imagen en el motor de IA primero.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    function filtrarDirectorio() {
        const texto = document.getElementById('buscadorPacientes').value.toLowerCase();
        const tarjetas = document.querySelectorAll('.tarjeta-paciente');
        
        tarjetas.forEach(tarjeta => {
            const dataBusqueda = tarjeta.getAttribute('data-busqueda');
            if (dataBusqueda.includes(texto)) {
                tarjeta.style.display = "flex";
            } else {
                tarjeta.style.display = "none";
            }
        });
    }
</script>
@endsection
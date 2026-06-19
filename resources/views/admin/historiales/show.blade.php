@extends('layouts.app')
@section('title', 'Ficha Médica Base')
@section('content')

<div class="space-y-6 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 animate-fade-in text-slate-700 font-medium text-sm">
    
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Ficha de Expediente Base</h2>
            <p class="text-slate-500 text-sm mt-0.5">Vista de solo lectura. Para modificar, regrese al panel y seleccione Editar.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-4 rounded-lg text-xs uppercase tracking-wide transition-colors shadow-sm">
                <i class="fa-solid fa-print mr-1"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="bg-indigo-600 border border-indigo-700 rounded-xl p-6 flex justify-between items-center text-white shadow-md">
        <div>
            <p class="text-xs text-indigo-200 font-bold uppercase tracking-widest mb-1">Paciente Identificado</p>
            <h3 class="text-xl font-black uppercase">{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}</h3>
        </div>
        <div class="text-right">
            <p class="text-xs text-indigo-200 font-bold uppercase tracking-widest mb-1">Documento C.I.</p>
            <span class="bg-indigo-800 border border-indigo-500 px-4 py-1.5 rounded-lg text-base font-mono font-bold">{{ $paciente->ci }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
        <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b pb-2">
            <i class="fa-solid fa-file-medical text-blue-500 mr-2"></i> Anamnesis Registrada
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Exposición Solar / UV</p>
                <p class="text-sm text-slate-800 font-semibold">{{ $paciente->antecedentes->exposicion_sol ?? 'Ninguna registrada.' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Otras Enfermedades de la Piel</p>
                <p class="text-sm text-slate-800 font-semibold">{{ $paciente->antecedentes->enfermedades_piel_previas ?? 'Ninguna registrada.' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Patologías Familiares Generales</p>
                <p class="text-sm text-slate-800 font-semibold">{{ $paciente->antecedentes->otras_patologias_familiares ?? 'Ninguna registrada.' }}</p>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 p-3 rounded-lg border {{ $paciente->antecedentes->quemaduras_previas ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                    <i class="fa-solid {{ $paciente->antecedentes->quemaduras_previas ? 'fa-triangle-exclamation' : 'fa-check-circle' }} text-lg"></i>
                    <div>
                        <p class="text-[10px] font-bold uppercase">Quemaduras Solares Graves</p>
                        <p class="text-xs font-black uppercase">{{ $paciente->antecedentes->quemaduras_previas ? 'Sí (Alerta)' : 'No reporta' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg border {{ $paciente->antecedentes->historial_familiar_melanoma ? 'bg-red-50 border-red-200 text-red-700' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                    <i class="fa-solid {{ $paciente->antecedentes->historial_familiar_melanoma ? 'fa-dna' : 'fa-check-circle' }} text-lg"></i>
                    <div>
                        <p class="text-[10px] font-bold uppercase">Melanoma en Familiares</p>
                        <p class="text-xs font-black uppercase">{{ $paciente->antecedentes->historial_familiar_melanoma ? 'Sí (Riesgo Genético)' : 'No reporta' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-start">
        <a href="{{ route('historiales.index') }}" class="bg-slate-300 hover:bg-slate-400 text-slate-700 font-bold py-2 px-6 rounded-lg text-xs uppercase tracking-wide transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Historiales
        </a>
    </div>
</div>
@endsection
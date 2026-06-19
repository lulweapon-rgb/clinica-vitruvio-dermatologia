@extends('layouts.app')
@section('title', 'Aperturar Historial Clínico')
@section('content')

<div class="space-y-6 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 animate-fade-in text-slate-700 font-medium text-sm">
    
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Apertura de Expediente Base</h2>
            <p class="text-slate-500 text-sm mt-0.5">Registro de antecedentes estáticos y factores de riesgo.</p>
        </div>
        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-sm border border-indigo-100">
            <i class="fa-solid fa-folder-plus"></i>
        </div>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 flex justify-between items-center text-white shadow-md">
        <div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Paciente Identificado</p>
            <h3 class="text-lg font-black uppercase">{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}</h3>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Documento C.I.</p>
            <span class="bg-slate-700 border border-slate-600 px-3 py-1 rounded-lg text-sm font-mono font-bold">{{ $paciente->ci }}</span>
        </div>
    </div>

    <form action="{{ route('historiales.store', $paciente->id) }}" method="POST" autocomplete="off" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Anamnesis y Antecedentes Previos
            </h4>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Historial de Exposición Solar / Radiación UV</label>
                    <textarea name="exposicion_sol" rows="2" placeholder="Detalle la frecuencia de exposición al sol por trabajo, uso de cámaras de bronceado, deportes al aire libre..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none bg-slate-50 shadow-inner"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center text-lg"><i class="fa-solid fa-sun"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase">¿Quemaduras Solares?</p>
                                <p class="text-[10px] text-slate-400 leading-tight">Paciente reporta quemaduras graves previas</p>
                            </div>
                        </div>
                        <input type="checkbox" name="quemaduras_previas" class="w-5 h-5 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 shadow-sm cursor-pointer">
                    </label>

                    <label class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-50 text-red-500 rounded-lg flex items-center justify-center text-lg"><i class="fa-solid fa-dna"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase">¿Melanoma Familiar?</p>
                                <p class="text-[10px] text-slate-400 leading-tight">Parientes directos con cáncer de piel</p>
                            </div>
                        </div>
                        <input type="checkbox" name="historial_familiar_melanoma" class="w-5 h-5 rounded text-red-600 border-slate-300 focus:ring-red-500 shadow-sm cursor-pointer">
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Otras Enfermedades de la Piel</label>
                        <input type="text" name="enfermedades_piel_previas" placeholder="Ej: Psoriasis, Dermatitis atópica, Rosácea..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Patologías Sistémicas Familiares</label>
                        <input type="text" name="otras_patologias_familiares" placeholder="Ej: Diabetes Tipo 2, Hipertensión arterial..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 p-5 bg-slate-50 border-t border-slate-200">
            <a href="{{ route('historiales.index') }}" class="bg-slate-300 hover:bg-slate-400 text-slate-700 font-bold py-2.5 px-6 rounded-lg text-xs uppercase tracking-wide transition-colors">
                Cancelar
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-8 rounded-lg text-xs uppercase tracking-wide transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Guardar Expediente Base
            </button>
        </div>
    </form>
</div>
@endsection
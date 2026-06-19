@extends('layouts.app')
@section('title', 'Historial y Triage Dermatológico')
@section('content')

<div class="space-y-6 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 animate-fade-in text-slate-700 font-medium text-sm">
    
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Historial y Triage Clínico</h2>
            <p class="text-slate-500 text-sm mt-0.5">Gestión unificada de antecedentes, sintomatología y extracción de imagen (CNN).</p>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-sm border border-blue-100">
            <i class="fa-solid fa-file-medical"></i>
        </div>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center text-white shadow-md gap-4">
        <div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Paciente en Evaluación</p>
            <h3 class="text-lg font-black uppercase">{{ $paciente->nombre }} {{ $paciente->apellido_paterno }}</h3>
        </div>
        <div class="flex gap-4 text-right">
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">C.I.</p>
                <span class="bg-slate-700 px-3 py-1 rounded border border-slate-600 font-mono text-sm">{{ $paciente->ci }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('evaluaciones.store_por_paciente', $paciente->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
            <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Fase 1: Historial Clínico de Base
            </h4>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Historial de Exposición Solar / Radiación UV</label>
                    <textarea name="exposicion_sol" rows="2" placeholder="Frecuencia de exposición al sol por trabajo, deportes..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none bg-slate-50 shadow-inner">{{ $paciente->antecedentes->exposicion_sol ?? old('exposicion_sol') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:border-amber-300 hover:bg-amber-50/30 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center text-lg"><i class="fa-solid fa-sun"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase">¿Quemaduras Solares Graves?</p>
                            </div>
                        </div>
                        <input type="checkbox" name="quemaduras_previas" class="w-5 h-5 rounded text-blue-600 border-slate-300 focus:ring-blue-500" {{ (isset($paciente->antecedentes) && $paciente->antecedentes->quemaduras_previas) ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-50 text-red-500 rounded-lg flex items-center justify-center text-lg"><i class="fa-solid fa-dna"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase">¿Melanoma Familiar?</p>
                            </div>
                        </div>
                        <input type="checkbox" name="historial_familiar_melanoma" class="w-5 h-5 rounded text-red-600 border-slate-300 focus:ring-red-500" {{ (isset($paciente->antecedentes) && $paciente->antecedentes->historial_familiar_melanoma) ? 'checked' : '' }}>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Otras Enfermedades de la Piel</label>
                        <input type="text" name="enfermedades_piel_previas" value="{{ $paciente->antecedentes->enfermedades_piel_previas ?? old('enfermedades_piel_previas') }}" placeholder="Ej: Psoriasis..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Patologías Familiares Generales</label>
                        <input type="text" name="otras_patologias_familiares" value="{{ $paciente->antecedentes->otras_patologias_familiares ?? old('otras_patologias_familiares') }}" placeholder="Ej: Diabetes Tipo 2..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-5">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-question text-amber-500"></i> Fase 2: Sintomatología Actual
                </h4>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Motivo de Consulta</label>
                        <input type="text" name="motivo_consulta" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Duración (Evolución)</label>
                        <input type="text" name="duracion_lesion" required placeholder="Ej: Hace 2 meses..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">Síntomas Locales de la Lesión</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="sintoma_prurito" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-xs font-bold text-slate-700">Prurito (Picazón)</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="sintoma_dolor" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-xs font-bold text-slate-700">Dolor/Sensibilidad</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="sintoma_sangrado" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-xs font-bold text-slate-700">Sangrado/Exudación</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="sintoma_parestesia" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-xs font-bold text-slate-700">Parestesia</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Otros Síntomas Sistémicos</label>
                    <input type="text" name="otros_sintomas_sistemicos" placeholder="Fiebre, fatiga..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-slate-50">
                </div>
            </div>

            <div class="space-y-5">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-eye text-blue-500"></i> Exploración y Captura CNN
                </h4>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Ubicación Anatómica</label>
                        <select name="ubicacion_anatomica" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Rostro">Rostro</option>
                            <option value="Cuello">Cuello</option>
                            <option value="Tórax">Tórax</option>
                            <option value="Espalda">Espalda</option>
                            <option value="Extremidades">Extremidades</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tipo Morfología</label>
                        <select name="tipo_lesion_morfologia" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Mácula">Mácula (Plana)</option>
                            <option value="Pápula">Pápula (< 1cm)</option>
                            <option value="Nódulo">Nódulo (Profunda)</option>
                            <option value="Placa">Placa (> 1cm)</option>
                            <option value="Úlcera">Úlcera (Desgaste)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Coloración</label>
                        <select name="coloracion_lesion" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Oscura">Oscura / Hiperpigmentada</option>
                            <option value="Roja">Roja / Eritematosa</option>
                            <option value="Cianótica">Cianótica (Azulada)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Consistencia</label>
                        <input type="text" name="consistencia_palpacion" required placeholder="Firme, blanda..." class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Distribución</label>
                    <input type="text" name="distribucion_lesiones" required placeholder="Única, asimétrica..." class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                </div>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl shadow-inner">
                    <label class="block text-sm font-black text-blue-700 mb-2 uppercase tracking-tight"><i class="fa-solid fa-camera mr-1"></i> Fotografía Clínica</label>
                    <input type="file" name="imagen_lesion" required accept="image/*" class="w-full border border-blue-300 rounded-lg px-3 py-2 text-xs bg-white text-blue-700 font-bold cursor-pointer">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 p-5 bg-slate-50 border border-slate-200 rounded-xl shadow-sm">
            <a href="{{ route('evaluaciones.index') }}" class="bg-slate-300 hover:bg-slate-400 text-slate-700 font-bold py-2.5 px-6 rounded-lg text-xs uppercase tracking-wide transition-colors">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg text-xs uppercase tracking-wide shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-microchip"></i> Guardar Historial e Iniciar IA
            </button>
        </div>
    </form>
</div>
@endsection
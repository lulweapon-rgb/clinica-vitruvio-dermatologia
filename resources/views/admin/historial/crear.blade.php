@extends('layouts.app')
@section('title', 'Expediente Clínico Dermatológico')
@section('content')

<div class="space-y-6 w-full px-4 sm:px-6 lg:px-8 mx-auto animate-fade-in text-slate-700 font-medium text-sm">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-sm border border-indigo-100">
                <i class="fa-solid fa-folder-medical"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Expediente Clínico Dermatológico</h2>
                <p class="text-slate-500 text-sm mt-0.5">Clínica Vitruvio • Fase de Recolección de Datos</p>
            </div>
        </div>
        <div class="flex flex-col items-end text-right">
            <span class="text-xs font-bold text-slate-400 uppercase">Paciente a Evaluar</span>
            <span class="font-black text-slate-800 text-base uppercase">{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}</span>
            <span class="text-xs font-mono text-indigo-600 font-bold bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded mt-1">C.I. {{ $paciente->ci }}</span>
        </div>
    </div>

    <div class="bg-slate-100 border border-slate-200 rounded-xl p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-bold">
        <div>
            <p class="text-slate-400 uppercase text-[10px]">F. Nacimiento (Edad)</p>
            <p class="text-slate-700 mt-0.5 text-sm">
                {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }} 
                ({{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} Años)
            </p>
        </div>
        <div>
            <p class="text-slate-400 uppercase text-[10px]">Sexo Biológico</p>
            <p class="text-slate-700 mt-0.5 text-sm uppercase">{{ $paciente->genero ?? 'No registrado' }}</p>
        </div>
        <div>
            <p class="text-slate-400 uppercase text-[10px]">Celular de Contacto</p>
            <p class="text-slate-700 mt-0.5 text-sm font-mono">{{ $paciente->celular ?? 'S/N' }}</p>
        </div>
        <div>
            <p class="text-slate-400 uppercase text-[10px]">Apertura de Expediente</p>
            <p class="text-slate-700 mt-0.5 text-sm font-mono">{{ $paciente->created_at->format('d/m/Y H:i') }}</p>
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

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-xl shadow-sm text-sm font-semibold flex items-center gap-2 mb-4">
            <i class="fa-solid fa-circle-xmark text-red-500 text-base"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('historial.store', $paciente->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2 font-black text-slate-700 uppercase tracking-wide">
                <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs shadow-sm">1</span>
                Fase Pre-Consulta: Anamnesis y Triage Inicial
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-1.5"><i class="fa-solid fa-history text-slate-400 mr-1"></i> Antecedentes Personales y Familiares</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Historial de Exposición Solar / Radiación</label>
                        <textarea name="exposicion_sol" rows="2" placeholder="Detalle la frecuencia de exposición al sol, uso de cámaras solares, etc..." class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none resize-none bg-white shadow-sm">{{ $paciente->antecedentes->exposicion_sol ?? old('exposicion_sol') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-lg border border-slate-200/60">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                            <input type="checkbox" name="quemaduras_previas" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500" {{ (isset($paciente->antecedentes) && $paciente->antecedentes->quemaduras_previas) || old('quemaduras_previas') ? 'checked' : '' }}>
                            ¿Quemaduras graves?
                        </label>
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                            <input type="checkbox" name="historial_familiar_melanoma" class="w-4 h-4 rounded text-red-600 border-slate-300 focus:ring-red-500" {{ (isset($paciente->antecedentes) && $paciente->antecedentes->historial_familiar_melanoma) || old('historial_familiar_melanoma') ? 'checked' : '' }}>
                            ¿Parientes con Cáncer?
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Otras Enfermedades Cutáneas Previas</label>
                        <input type="text" name="enfermedades_previas" value="{{ $paciente->antecedentes->enfermedades_piel_previas ?? old('enfermedades_previas') }}" placeholder="Ej: Psoriasis, Dermatitis, Diabetes..." class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-1.5"><i class="fa-solid fa-virus text-slate-400 mr-1"></i> Estado Inicial de la Lesión</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Motivo de la Consulta</label>
                            <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}" placeholder="Ej: Mancha irregular en la espalda..." required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Duración Estimada</label>
                            <input type="text" name="duracion_lesion" value="{{ old('duracion_lesion') }}" placeholder="Ej: Hace 3 meses, 1 año..." required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Síntomas Locales (Reportados por Paciente)</label>
                        <div class="flex flex-wrap gap-4 bg-slate-50 p-3 rounded-lg border border-slate-200/60">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" name="sintoma_prurito" class="w-4 h-4 text-blue-600 rounded"> Prurito (Picazón)</label>
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" name="sintoma_dolor" class="w-4 h-4 text-blue-600 rounded"> Dolor/Sensibilidad</label>
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" name="sintoma_sangrado" class="w-4 h-4 text-blue-600 rounded"> Sangrado</label>
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" name="sintoma_parestesia" class="w-4 h-4 text-blue-600 rounded"> Parestesia</label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Otros Síntomas Generales Asociados</label>
                        <input type="text" name="otros_sintomas" value="{{ $paciente->antecedentes->otras_patologias_familiares ?? old('otros_sintomas') }}" placeholder="Ej: Fiebre, Fatiga, Pérdida de peso reciente..." class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none bg-white shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2 font-black text-slate-700 uppercase tracking-wide">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs shadow-sm">2</span>
                Fase Intra-Consulta: Exploración Física y Diagnóstico Digital CNN
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="space-y-4 lg:col-span-2">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-1.5"><i class="fa-solid fa-eye-medical text-slate-400 mr-1"></i> Inspección Semiológica de la Lesión</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Ubicación Anatómica Precisa</label>
                            <select name="ubicacion_anatomica" required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                                <option value="Rostro">Rostro</option>
                                <option value="Cuello">Cuello</option>
                                <option value="Tórax">Tórax</option>
                                <option value="Espalda">Espalda</option>
                                <option value="Brazo">Extremidad Superior</option>
                                <option value="Pierna">Extremidad Inferior</option>
                                <option value="Otra">Otra Zona...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tipo de Lesión Primaria/Secundaria</label>
                            <select name="tipo_lesion_morfologia" required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                                <option value="Mácula">Mácula (Cambio de color plano)</option>
                                <option value="Pápula">Pápula (Elevación < 1cm)</option>
                                <option value="Nódulo">Nódulo (Masa profunda)</option>
                                <option value="Placa">Placa (Elevación > 1cm)</option>
                                <option value="Úlcera">Úlcera (Pérdida de sustancia)</option>
                                <option value="Costra">Costra / Exudado Seco</option>
                                <option value="Ampolla">Ampolla / Vesícula Líquida</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Coloración Predominante de la Lesión</label>
                            <select name="coloracion_lesion" required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                                <option value="Hiperpigmentada">Hiperpigmentada / Oscura</option>
                                <option value="Roja">Roja / Eritematosa</option>
                                <option value="Amarillo-paja">Amarillo-paja (Neoplásica)</option>
                                <option value="Cianótica">Cianótica / Azulada</option>
                                <option value="Hipopigmentada">Hipopigmentada / Pálida</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Palpación y Consistencia Textural</label>
                            <input type="text" name="consistencia_palpacion" value="{{ old('consistencia_palpacion') }}" placeholder="Ej: Firme, infiltrada, blanda..." required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Disposición, Patrón y Simetría General</label>
                        <input type="text" name="distribucion_lesiones" value="{{ old('distribucion_lesiones') }}" placeholder="Ej: Lesión asimétrica, bordes difusos, patrón diseminado..." required class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                    </div>
                </div>

                <div class="space-y-4 bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between shadow-inner">
                    <div>
                        <h4 class="text-xs font-black text-blue-600 uppercase tracking-wider border-b border-blue-200 pb-1.5"><i class="fa-solid fa-camera mr-1"></i> Captura Dermatológica (HU-05)</h4>
                        <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">Suba la fotografía nítida obtenida por el dispositivo capturador clínico de la Clínica Vitruvio.</p>
                        
                        <input type="file" name="imagen_lesion" required accept="image/*" class="w-full border border-blue-200 rounded-lg px-3 py-2 text-xs bg-white text-blue-700 mt-3 font-bold cursor-pointer">
                    </div>

                    <div class="bg-blue-600 text-white rounded-lg p-4 shadow-sm border border-blue-700 text-center mt-4">
                        <p class="text-[10px] uppercase font-black tracking-widest text-blue-200"><i class="fa-solid fa-microchip animate-pulse mr-1"></i> Automatización CNN</p>
                        <p class="text-[11px] font-bold mt-1.5 leading-normal opacity-90">Al guardar, la muestra fotográfica viajará al motor de Python para estimar la tasa predictiva de malignidad.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2 font-black text-slate-700 uppercase tracking-wide">
                <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs shadow-sm">3</span>
                Fase Post-Consulta: Pruebas Diagnósticas y Plan Terapéutico
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="space-y-3">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-1.5"><i class="fa-solid fa-vial text-slate-400 mr-1"></i> Exámenes Complementarios</h4>
                    <p class="text-[10px] text-slate-400 leading-normal">Seleccione si la lesión requiere una prueba de laboratorio complementaria:</p>
                    
                    <select name="prueba_diagnostica" class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 bg-white font-bold shadow-sm">
                        <option value="NINGUNA">No requiere (Solo monitoreo IA)</option>
                        <option value="BIOPSIA">Biopsia Cutánea Histopatológica</option>
                        <option value="LAMPARA_WOOD">Lámpara de Wood (Fluorescencia)</option>
                        <option value="RASPADO">Raspado Tisular / Citología</option>
                        <option value="DIASCOPIA">Diascopia (Blanqueamiento)</option>
                    </select>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider border-b pb-1.5"><i class="fa-solid fa-notes-medical text-slate-400 mr-1"></i> Plan de Tratamiento Clínico de Seguimiento</h4>
                    <div>
                        <textarea name="plan_tratamiento" rows="3" placeholder="Diseñe el plan terapéutico de control según los hallazgos morfológicos preliminares (Ej: Derivación a cirugía oncológica para extirpación total, crioterapia, o seguimiento fotográfico estricto a las 4 semanas)..." class="w-full border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none resize-none bg-white shadow-sm" required>{{ old('plan_tratamiento') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 bg-slate-100 p-5 rounded-xl border border-slate-200 shadow-inner">
            <a href="{{ route('pacientes.index') }}" class="bg-slate-500 hover:bg-slate-600 text-white font-bold py-2.5 px-6 rounded-lg text-xs uppercase tracking-wide transition-colors flex items-center justify-center shadow-sm">
                Cancelar Registro
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg text-xs uppercase tracking-wide transition-all flex items-center justify-center gap-2 shadow-md">
                <i class="fa-solid fa-microchip"></i> Consolidar Ficha e Iniciar IA
            </button>
        </div>

    </form>
</div>

@endsection
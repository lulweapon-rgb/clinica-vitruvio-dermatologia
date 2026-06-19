<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\EvaluacionClinica;
use App\Models\Paciente;
use App\Models\PersonalMedico;
use App\Models\AntecedenteMedico; // <--- AQUÍ ESTÁ LA SOLUCIÓN AL ERROR
use App\Models\LogAcceso;

class EvaluacionClinicaController extends Controller
{
    public function index()
    {
        // Traemos evaluaciones incluyendo las eliminadas para la papelera lógica
        $evaluaciones = EvaluacionClinica::withTrashed()->with(['paciente.antecedentes', 'medico.usuario'])->orderBy('creado_at', 'desc')->get();
        $pacientes = Paciente::all();
        $medicos = PersonalMedico::with('usuario')->get();

        return view('admin.evaluaciones.index', compact('evaluaciones', 'pacientes', 'medicos'));
    }

    public function store(Request $request)
    {
        $paciente = Paciente::findOrFail($request->paciente_id);

        $request->validate([
            'motivo_consulta'          => 'required|string|max:500',
            'duracion_lesion'          => 'required|string|max:100',
            'ubicacion_anatomica'      => 'required|string',
            'tipo_lesion_morfologia'   => 'required|string',
            'coloracion_lesion'        => 'required|string',
            'consistencia_palpacion'   => 'required|string|max:255',
            'distribucion_lesiones'    => 'required|string|max:255',
            'aspecto_general'          => 'required|string|max:255',
            'disposicion_lesiones'     => 'required|string|max:100',
            'imagen_lesion'            => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        DB::beginTransaction();
        try {
            // 1. ANTECEDENTES
            AntecedenteMedico::updateOrCreate(
                ['paciente_id' => $paciente->id],
                [
                    'exposicion_sol'              => $request->exposicion_sol,
                    'quemaduras_previas'          => $request->has('quemaduras_previas'),
                    'enfermedades_piel_previas'   => $request->enfermedades_piel_previas,
                    'historial_familiar_melanoma' => $request->has('historial_familiar_melanoma'),
                    'otras_patologias_familiares' => $request->otras_patologias_familiares, 
                ]
            );

            // 2. FOTO
            $rutaImagen = $request->file('imagen_lesion')->store('lesiones', 'public');

            // 3. CONSOLIDACIÓN
            EvaluacionClinica::create([
                'paciente_id'               => $paciente->id,
                'medico_id'                 => $request->medico_id,
                'imagen_lesion'             => $rutaImagen,
                'ia_diagnostico'            => 'PENDIENTE',
                'ia_porcentaje'             => null,
                'estado_validacion'         => EvaluacionClinica::ESTADO_PENDIENTE,
                'estado'                    => 'ACTIVO',
                
                'motivo_consulta'           => $request->motivo_consulta,
                'duracion_lesion'           => $request->duracion_lesion,
                'sintoma_prurito'           => $request->has('sintoma_prurito'),
    'sintoma_dolor'             => $request->has('sintoma_dolor'),
    'sintoma_sangrado'          => $request->has('sintoma_sangrado'),
    'sintoma_parestesia'        => $request->has('sintoma_parestesia'),
                'otros_sintomas_sistemicos' => $request->otros_sintomas_sistemicos,
                'factores_externos'         => $request->factores_externos,
                'aspecto_general'           => $request->aspecto_general,
                'ubicacion_anatomica'       => $request->ubicacion_anatomica,
                'tipo_lesion_morfologia'    => $request->tipo_lesion_morfologia,
                'coloracion_lesion'         => $request->coloracion_lesion,
                'consistencia_palpacion'    => $request->consistencia_palpacion,
                'disposicion_lesiones'      => $request->disposicion_lesiones,
                'distribucion_lesiones'     => $request->distribucion_lesiones,
            ]);

            LogAcceso::registrar("Historia Clínica creada para paciente ID: " . $paciente->id);
            DB::commit();

            return redirect()->route('evaluaciones.index')->with('success', 'Historia Clínica guardada correctamente. Muestra enviada a Análisis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error en procesamiento: ' . $e->getMessage());
        }
    }

    // ==========================================
    // LÓGICA CRUD RESTAURADA
    // ==========================================
    public function update(Request $request, $id)
    {
        $evaluacion = EvaluacionClinica::findOrFail($id);
        
        $evaluacion->medico_id = $request->medico_id;
        $evaluacion->ubicacion_anatomica = $request->ubicacion_anatomica;
        $evaluacion->tipo_lesion_morfologia = $request->tipo_lesion_morfologia;

        if ($request->hasFile('imagen_lesion')) {
            $rutaImagen = $request->file('imagen_lesion')->store('lesiones', 'public');
            $evaluacion->imagen_lesion = $rutaImagen;
        }
        $evaluacion->save();

        if ($evaluacion->paciente && $evaluacion->paciente->antecedentes) {
            $evaluacion->paciente->antecedentes->update([
                'exposicion_sol' => $request->exposicion_sol,
                'enfermedades_piel_previas' => $request->enfermedades_piel_previas,
            ]);
        }

        return redirect()->route('evaluaciones.index')->with('success', 'Expediente actualizado correctamente.');
    }

    public function destroy($id)
    {
        EvaluacionClinica::findOrFail($id)->delete();
        return redirect()->route('evaluaciones.index')->with('success', 'El expediente fue anulado (Papelera).');
    }

    public function restore($id)
    {
        EvaluacionClinica::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('evaluaciones.index')->with('success', 'Expediente restaurado con éxito.');
    }

    public function forceDelete($id)
    {
        EvaluacionClinica::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('evaluaciones.index')->with('success', 'Expediente destruido físicamente de la base de datos.');
    }

    public function resolverPorEspecialista(Request $request, $evaluacion_id)
    {
        $evaluacion = EvaluacionClinica::findOrFail($evaluacion_id);
        $evaluacion->update([
            'diagnostico_clinico' => $request->diagnostico_clinico,
            'plan_tratamiento'    => $request->plan_treatment,
            'prueba_diagnostica'  => $request->prueba_diagnostica,
            'estado_validacion'   => $request->resolucion_caso,
        ]);
        return redirect()->back()->with('success', 'Dictamen firmado y cerrado con éxito.');
    }
}
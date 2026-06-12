<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EvaluacionClinica;
use App\Models\Paciente;
use App\Models\PersonalMedico;
use Illuminate\Support\Facades\Http;

class EvaluacionClinicaController extends Controller
{
    /**
     * Muestra el listado de evaluaciones con filtros de auditoría.
     */
    public function index()
    {
        // Traemos las evaluaciones junto a sus relaciones
        $evaluaciones = EvaluacionClinica::with([
            'paciente' => function($q) { $q->withTrashed(); },
            'medico.usuario' => function($q) { $q->withTrashed(); }
        ])->withTrashed()->orderBy('id', 'desc')->get();
        
        // Catálogos necesarios para los formularios
        $pacientes = Paciente::orderBy('nombre', 'asc')->get();
        
        // Consulta corregida para médicos activos
        $medicos = PersonalMedico::with(['usuario' => function($query) {
            $query->whereNull('eliminado_at'); 
        }])
        ->whereHas('usuario', function($query) {
            $query->whereNull('eliminado_at'); 
        })
        ->get();

        return view('admin.evaluaciones.index', compact('evaluaciones', 'pacientes', 'medicos'));
    }

    /**
     * Registra una nueva evaluación clínica (Triage + Imagen).
     */
    public function store(Request $request)
    {
        // 1. Validaciones (REGLAS EN TEXTO, NUNCA VARIABLES AQUÍ)
        $request->validate([
            'paciente_id'         => 'required|exists:pacientes,id',
            'medico_id'           => 'required|exists:personal_medico,id',
            'ubicacion_anatomica' => 'required|string|max:255', 
            
            'diagnostico_clinico' => 'required|string',
            'imagen_lesion'       => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            // Guardado de la imagen en 'storage/app/public/lesiones'
            $rutaImagen = $request->file('imagen_lesion')->store('lesiones', 'public');

            // 2. Guardado en la Base de Datos (AQUÍ SÍ USAMOS LAS VARIABLES DEL REQUEST)
            EvaluacionClinica::create([
                'paciente_id'         => $request->paciente_id,
                'medico_id'           => $request->medico_id,
                'ubicacion_anatomica' => $request->ubicacion_anatomica,
              
                'diagnostico_clinico' => $request->diagnostico_clinico,
                'sintoma_picazon'     => $request->has('sintoma_picazon'),
                'sintoma_sangrado'    => $request->has('sintoma_sangrado'),
                'sintoma_crecimiento' => $request->has('sintoma_crecimiento'),
                'imagen_lesion'       => $rutaImagen,
                'estado_validacion'   => 'PENDIENTE',
                'estado'              => 'ACTIVO' // Aseguramos que se cree como ACTIVO
            ]);

            return redirect()->back()->with('success', 'Triage e imagen cargados. Esperando análisis IA.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza una evaluación clínica existente.
     */
    public function update(Request $request, $id)
    {
        $evaluacion = EvaluacionClinica::withTrashed()->findOrFail($id);

        // También agregamos la validación de los campos nuevos al editar
        $request->validate([
            'paciente_id'         => 'required|exists:pacientes,id',
            'medico_id'           => 'required|exists:personal_medico,id',
            'ubicacion_anatomica' => 'required|string|max:255',
            
            'diagnostico_clinico' => 'required|string',
            'imagen_lesion'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            $datosActualizar = [
                'paciente_id'         => $request->paciente_id,
                'medico_id'           => $request->medico_id,
                'ubicacion_anatomica' => $request->ubicacion_anatomica,
                
                'diagnostico_clinico' => $request->diagnostico_clinico
            ];

            // Reemplazo de imagen si se selecciona una nueva
            if ($request->hasFile('imagen_lesion')) {
                if ($evaluacion->imagen_lesion && Storage::disk('public')->exists($evaluacion->imagen_lesion)) {
                    Storage::disk('public')->delete($evaluacion->imagen_lesion);
                }
                $datosActualizar['imagen_lesion'] = $request->file('imagen_lesion')->store('lesiones', 'public');
            }

            $evaluacion->update($datosActualizar);

            return redirect()->back()->with('success', 'Registro de seguimiento clínico actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Inactivación lógica del registro.
     */
    public function destroy($id)
    {
        try {
            $evaluacion = EvaluacionClinica::findOrFail($id);
            $evaluacion->update(['estado' => 'INACTIVO']); // Cambio en BD
            $evaluacion->delete(); // Borrado lógico
            return redirect()->back()->with('success', 'Evaluación inactivada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Restauración del registro.
     */
    public function restore($id)
    {
        try {
            $evaluacion = EvaluacionClinica::withTrashed()->findOrFail($id);
            $evaluacion->update(['estado' => 'ACTIVO']); // Cambio en BD
            $evaluacion->restore(); // Quitar borrado lógico
            return redirect()->back()->with('success', 'Evaluación reactivada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Borrado Físico Definitivo
     */
    public function forceDelete($id)
    {
        try {
            $evaluacion = EvaluacionClinica::withTrashed()->findOrFail($id);
            
            // Borrar físicamente la foto del servidor para liberar espacio y no dejar basura
            if ($evaluacion->imagen_lesion && Storage::disk('public')->exists($evaluacion->imagen_lesion)) {
                Storage::disk('public')->delete($evaluacion->imagen_lesion);
            }

            $evaluacion->forceDelete(); // Borrado Físico Definitivo
            return redirect()->back()->with('success', 'Evaluación eliminada físicamente del sistema.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluacionClinica;
use App\Models\SeguimientoEvolutivo;
use App\Models\PersonalMedico;
use Illuminate\Support\Facades\Storage;
use App\Models\LogAcceso;

class SeguimientoEvolutivoController extends Controller
{
    // Muestra la lista de casos que ya fueron analizados por IA
    public function index()
    {
        // CORRECCIÓN: Usamos withTrashed() en la relación paciente para que no devuelva null
        $evaluaciones = EvaluacionClinica::with([
            'paciente' => function ($query) {
                $query->withTrashed();
            }, 
            'medico.usuario'
        ])
            ->whereNotNull('ia_porcentaje') // Solo casos que ya tienen predicción
            ->orderBy('actualizado_at', 'desc')
            ->get();

        return view('admin.seguimientos.index', compact('evaluaciones'));
    }

    // Abre la Línea de Tiempo de un paciente en específico
    public function timeline($id)
    {
        $evaluacion = EvaluacionClinica::with([
            // CORRECCIÓN: Traemos al paciente aunque esté en papelera
            'paciente' => function ($query) {
                $query->withTrashed();
            }, 
            'medico.usuario',
            'seguimientos' => function ($query) {
                $query->withTrashed()->with('medico.usuario');
            }
        ])->findOrFail($id);

        $medicos = PersonalMedico::with('usuario')->get();

        return view('admin.seguimientos.timeline', compact('evaluacion', 'medicos'));
    }

    // Guarda un nuevo control en la línea de tiempo
    public function store(Request $request, $evaluacion_id)
    {
        $request->validate([
            'medico_id' => 'required|exists:personal_medico,id',
            'fecha_control' => 'required|date',
            'diagnostico_definitivo' => 'required|string',
            'cambio_tamano' => 'required|string',
            'cambio_color' => 'required|string',
            'estado_tratamiento' => 'required|string',
            'observaciones' => 'required|string',
            'imagen_control' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            // Guardamos la nueva foto evolutiva
            $rutaImagen = $request->file('imagen_control')->store('controles_evolutivos', 'public');

            SeguimientoEvolutivo::create([
                'evaluacion_id' => $evaluacion_id,
                'medico_id' => $request->medico_id,
                'fecha_control' => $request->fecha_control,
                'diagnostico_definitivo' => $request->diagnostico_definitivo,
                'cambio_tamano' => $request->cambio_tamano,
                'cambio_color' => $request->cambio_color,
                'estado_tratamiento' => $request->estado_tratamiento,
                'observaciones' => $request->observaciones,
                'imagen_control' => $rutaImagen
            ]);

            // CORRECCIÓN: El log debe ir ANTES del return, sino nunca se ejecuta
            LogAcceso::registrar("Registró un control evolutivo para la evaluación ID: " . $evaluacion_id);

            return redirect()->back()->with('success', 'Control clínico agregado a la línea de tiempo evolutiva.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar el seguimiento: ' . $e->getMessage());
        }
    }

    // Actualiza un control evolutivo existente
    public function update(Request $request, $id)
    {
        $seguimiento = SeguimientoEvolutivo::findOrFail($id);

        $request->validate([
            'medico_id' => 'required|exists:personal_medico,id',
            'fecha_control' => 'required|date',
            'diagnostico_definitivo' => 'required|string',
            'cambio_tamano' => 'required|string',
            'cambio_color' => 'required|string',
            'estado_tratamiento' => 'required|string',
            'observaciones' => 'required|string',
            'imagen_control' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            $datosActualizar = $request->except(['_token', '_method', 'imagen_control']);

            // Si el médico decide reemplazar la foto de control
            if ($request->hasFile('imagen_control')) {
                if (Storage::disk('public')->exists($seguimiento->imagen_control)) {
                    Storage::disk('public')->delete($seguimiento->imagen_control);
                }
                $datosActualizar['imagen_control'] = $request->file('imagen_control')->store('controles_evolutivos', 'public');
            }

            $seguimiento->update($datosActualizar);

            return redirect()->back()->with('success', 'El control evolutivo ha sido actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el control: ' . $e->getMessage());
        }
    }

    // Elimina (anula) un control de la línea de tiempo
    public function destroy($id)
    {
        try {
            SeguimientoEvolutivo::findOrFail($id)->delete();
            
            // Log extra opcional para mantener registro
            LogAcceso::registrar("Envió a papelera el control evolutivo ID: " . $id);
            
            return redirect()->back()->with('success', 'Control clínico retirado del expediente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            SeguimientoEvolutivo::withTrashed()->findOrFail($id)->restore();
            
            // CORRECCIÓN: El log debe ir ANTES del return, sino nunca se ejecuta
            LogAcceso::registrar("Restauró de la papelera el control evolutivo ID: " . $id);

            return redirect()->back()->with('success', 'El control ha sido recuperado de la papelera.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }
}
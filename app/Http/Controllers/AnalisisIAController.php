<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\EvaluacionClinica;
use App\Models\LogAcceso;

class AnalisisIAController extends Controller
{
    public function index()
    {
        // 1. Jalamos las evaluaciones PENDIENTES de análisis
        $pendientes = EvaluacionClinica::with([
            'paciente' => function ($query) {
                $query->withTrashed(); // Traemos al paciente aunque esté inactivo
            },
            'medico.usuario' => function ($query) {
                $query->withTrashed(); // Traemos al médico aunque esté inactivo
            }
        ])
            ->whereNull('ia_porcentaje')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Jalamos el historial de evaluaciones ya PROCESADAS
        $procesados = EvaluacionClinica::with([
            'paciente' => function ($query) {
                $query->withTrashed(); // Traemos al paciente aunque esté inactivo
            },
            'medico.usuario' => function ($query) {
                $query->withTrashed(); // Traemos al médico aunque esté inactivo
            }
        ])
            ->whereNotNull('ia_porcentaje')
            ->orderBy('actualizado_at', 'desc')
            ->get();

        return view('admin.analisis.index', compact('pendientes', 'procesados'));
    }

    public function ejecutarAnalisis($id)
    {
        $evaluacion = EvaluacionClinica::findOrFail($id);
        $rutaAbsoluta = storage_path('app/public/' . $evaluacion->imagen_lesion);

        if (!file_exists($rutaAbsoluta)) {
            return redirect()->back()->with('error', 'Error: La imagen de la lesión no se encuentra en el servidor.');
        }

        try {
            // Petición al microservicio de Python (Puerto 5000)
            $response = Http::timeout(30)->attach(
                'imagen', file_get_contents($rutaAbsoluta), 'lesion.jpg'
            )->post('http://127.0.0.1:5000/api/predict');

            if ($response->successful()) {
                $resultado = $response->json();
                
                // Guardamos el resultado del algoritmo CNN (SIN tiempo_evolucion)
                $evaluacion->update([
                    'ia_diagnostico' => $resultado['clase_detectada'],
                    'ia_porcentaje' => $resultado['confianza_porcentaje'],
                    'estado_validacion' => 'ANALIZADO'
                ]);

                LogAcceso::registrar("Ejecutó modelo CNN predictivo en evaluación ID: " . $id);

                return redirect()->back()->with('success', 'Análisis CNN exitoso. Resultados guardados en el historial del paciente.');
            }

            return redirect()->back()->with('error', 'El motor CNN rechazó la solicitud.');
        } catch (\Exception $e) {
            // Ahora si hay un error SQL o de código, te dirá el error exacto en lugar de culpar a Python
            return redirect()->back()->with('error', 'Error en el proceso: ' . $e->getMessage());
        }
    }
}
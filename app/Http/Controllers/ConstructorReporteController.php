<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluacionClinica;

class ConstructorReporteController extends Controller
{
    public function index(Request $request)
    {
        // 1. Recibir las tablas seleccionadas
        $tablas_seleccionadas = $request->input('tablas', ['pacientes', 'ia', 'seguimientos']);
        
        // 2. Iniciar la consulta base Inteligente
        $query = EvaluacionClinica::with([
            'paciente' => function($q) { $q->withTrashed(); }
        ]);

        // 3. Cargas dinámicas de relaciones según lo que el usuario quiere exportar
        if (in_array('ia', $tablas_seleccionadas)) {
            $query->with(['medico.usuario' => function($q) { $q->withTrashed(); }]);
        }

        if (in_array('seguimientos', $tablas_seleccionadas)) {
            $query->with(['seguimientos' => function($q) { 
                $q->withTrashed(); 
            }]);
        }

        // 4. Ejecutar la consulta final
        // Limitamos a 100 para no saturar la vista previa del reporte
        $resultados = $query->orderBy('id', 'desc')->limit(100)->get();

        return view('admin.reportes.constructor', compact('resultados', 'tablas_seleccionadas'));
    }
}
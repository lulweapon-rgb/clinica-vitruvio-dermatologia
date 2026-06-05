<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluacionClinica; // <-- IMPORTANTE: Usamos el modelo inteligente
use App\Models\PersonalMedico;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // 1. Recibir filtros
        $fecha_inicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fecha_fin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $medico_id = $request->input('medico_id', 'todos');
        
        // Si no mandan nada, por defecto cargamos todo para que no se rompa la vista
        $tablas_seleccionadas = $request->input('tablas', ['pacientes', 'ia', 'seguimientos']); 

        // 2. Consulta Inteligente (Eloquent) basada en la Evaluación
        // Siempre cargamos al paciente (incluso inactivos) porque la vista lo requiere: $rep->paciente->nombre
        $query = EvaluacionClinica::with([
            'paciente' => function($q) { $q->withTrashed(); }
        ]);

        // 3. Cargas Dinámicas (Si el usuario seleccionó la tabla en el reporte)
        if (in_array('ia', $tablas_seleccionadas)) {
            $query->with(['medico.usuario' => function($q) { $q->withTrashed(); }]);
        }

        if (in_array('seguimientos', $tablas_seleccionadas)) {
            $query->with(['seguimientos' => function($q) { 
                $q->withTrashed(); 
            }]);
        }

        // 4. Filtros de Búsqueda
        $query->whereDate('creado_at', '>=', $fecha_inicio)
              ->whereDate('creado_at', '<=', $fecha_fin);

        if ($medico_id !== 'todos') {
            $query->where('medico_id', $medico_id);
        }

        // 5. Ejecutamos (Esto devuelve Modelos Inteligentes, no stdClass)
        $resultados = $query->orderBy('id', 'desc')->get();

        // Lista de médicos para el filtro de la vista
        $medicos = PersonalMedico::whereHas('usuario', function($query) {
            $query->whereNull('eliminado_at');
        })->with('usuario')->get();

        return view('admin.reportes.index', compact('resultados', 'fecha_inicio', 'fecha_fin', 'medico_id', 'medicos', 'tablas_seleccionadas'));
    }
}
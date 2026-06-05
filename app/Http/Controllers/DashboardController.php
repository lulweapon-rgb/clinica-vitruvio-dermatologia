<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\EvaluacionClinica;
use App\Models\SeguimientoEvolutivo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Recopilación de KPIs principales
        $totalPacientes = Paciente::count();
        $totalEvaluaciones = EvaluacionClinica::count();
        $totalSeguimientos = SeguimientoEvolutivo::count();
        $casosCriticos = EvaluacionClinica::where('ia_porcentaje', '>', 75)->count();

        // 2. Distribución Algorítmica (Gráfico Circular)
        $diagnosticosData = EvaluacionClinica::whereNotNull('ia_diagnostico')
            ->select('ia_diagnostico', DB::raw('count(*) as total'))
            ->groupBy('ia_diagnostico')
            ->get();

        $labelsDiagnosticos = $diagnosticosData->pluck('ia_diagnostico');
        $valoresDiagnosticos = $diagnosticosData->pluck('total');

        // 3. Flujo Temporal de Evaluaciones (Gráfico de Barras - Últimos 6 meses)
        $meses = [];
        $evaluacionesPorMes = [];
        $nombresMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $meses[] = $nombresMeses[$fecha->month - 1] . ' ' . $fecha->year;
            
            $evaluacionesPorMes[] = EvaluacionClinica::whereYear('creado_at', $fecha->year)
                ->whereMonth('creado_at', $fecha->month)
                ->count();
        }

        return view('admin.dashboard', compact(
            'totalPacientes', 
            'totalEvaluaciones', 
            'totalSeguimientos', 
            'casosCriticos',
            'labelsDiagnosticos', 
            'valoresDiagnosticos',
            'meses', 
            'evaluacionesPorMes'
        ));
    }
}
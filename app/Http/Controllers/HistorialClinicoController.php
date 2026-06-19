<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\AntecedenteMedico;
use App\Models\LogAcceso;

class HistorialClinicoController extends Controller
{
    /**
     * INDEX: Muestra la tabla principal de todos los pacientes y el estado de su Historial
     */
    public function index()
    {
        // Traemos a todos los pacientes (activos) junto con su relación de antecedentes
        $pacientes = Paciente::with('antecedentes')->orderBy('created_at', 'desc')->get();
        return view('admin.historiales.index', compact('pacientes'));
    }

    /**
     * CREATE: Muestra el formulario para registrar un nuevo Historial Clínico (Antecedentes)
     */
    public function create($paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);
        
        // Si el paciente ya tiene historial, lo redirigimos a editar para evitar duplicados
        if ($paciente->antecedentes) {
            return redirect()->route('historiales.edit', $paciente->id)
                             ->with('info', 'El paciente ya cuenta con un historial clínico abierto. Puede modificarlo aquí.');
        }

        return view('admin.historiales.create', compact('paciente'));
    }

    /**
     * STORE: Guarda el nuevo Historial Clínico en la base de datos
     */
    public function store(Request $request, $paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);

        $request->validate([
            'exposicion_sol'              => 'nullable|string|max:1000',
            'enfermedades_piel_previas'   => 'nullable|string|max:1000',
            'otras_patologias_familiares' => 'nullable|string|max:1000',
        ]);

        AntecedenteMedico::create([
            'paciente_id'                 => $paciente->id,
            'exposicion_sol'              => $request->exposicion_sol,
            'quemaduras_previas'          => $request->has('quemaduras_previas'),
            'enfermedades_piel_previas'   => $request->enfermedades_piel_previas,
            'historial_familiar_melanoma' => $request->has('historial_familiar_melanoma'),
            'otras_patologias_familiares' => $request->otras_patologias_familiares, 
        ]);

        LogAcceso::registrar("Apertura de Historial Clínico Base para paciente ID: " . $paciente->id);

        return redirect()->route('historiales.index')->with('success', 'Historial Clínico aperturado exitosamente.');
    }

    /**
     * EDIT: Muestra el formulario para actualizar un Historial existente
     */
    public function edit($paciente_id)
    {
        $paciente = Paciente::with('antecedentes')->findOrFail($paciente_id);

        if (!$paciente->antecedentes) {
            return redirect()->route('historiales.create', $paciente->id)
                             ->with('error', 'El paciente no tiene un historial para editar. Por favor, aperture uno nuevo.');
        }

        return view('admin.historiales.edit', compact('paciente'));
    }

    /**
     * UPDATE: Actualiza los datos del Historial en PostgreSQL
     */
    public function update(Request $request, $paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);
        $antecedente = AntecedenteMedico::where('paciente_id', $paciente->id)->firstOrFail();

        $request->validate([
            'exposicion_sol'              => 'nullable|string|max:1000',
            'enfermedades_piel_previas'   => 'nullable|string|max:1000',
            'otras_patologias_familiares' => 'nullable|string|max:1000',
        ]);

        $antecedente->update([
            'exposicion_sol'              => $request->exposicion_sol,
            'quemaduras_previas'          => $request->has('quemaduras_previas'),
            'enfermedades_piel_previas'   => $request->enfermedades_piel_previas,
            'historial_familiar_melanoma' => $request->has('historial_familiar_melanoma'),
            'otras_patologias_familiares' => $request->otras_patologias_familiares, 
        ]);

        LogAcceso::registrar("Actualización de Historial Clínico Base para paciente ID: " . $paciente->id);

        return redirect()->route('historiales.index')->with('success', 'Historial Clínico actualizado exitosamente.');
    }

    /**
     * SHOW: Muestra la Ficha Clínica completa (Ideal para imprimir o solo lectura)
     */
    public function show($paciente_id)
    {
        $paciente = Paciente::with('antecedentes')->findOrFail($paciente_id);
        return view('admin.historiales.show', compact('paciente'));
    }
    public function destroy($paciente_id)
    {
        $antecedente = AntecedenteMedico::where('paciente_id', $paciente_id)->firstOrFail();
        
        $antecedente->delete();

        LogAcceso::registrar("Eliminación de Historial Clínico Base para paciente ID: " . $paciente_id);

        return redirect()->route('historiales.index')->with('success', 'El expediente base fue eliminado correctamente del sistema.');
    }
}
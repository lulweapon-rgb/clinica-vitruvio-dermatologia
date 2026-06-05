<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use Carbon\Carbon;

class PacienteController extends Controller
{
    public function index()
    {
        // Orden cronológico inverso para ver los registros más recientes primero
        $pacientes = Paciente::withTrashed()->orderBy('id', 'desc')->get();
        return view('admin.pacientes.index', compact('pacientes'));
    }

    public function store(Request $request)
    {
        // Cálculo dinámico de la fecha límite para garantizar mayoría de edad (mínimo 18 años atrás)
        $fechaLimiteAdulto = Carbon::now()->subYears(18)->format('Y-m-d');
        // Fecha base para evitar años incoherentes en el pasado extremo (ej. máximo 120 años)
        $fechaLimitePasado = Carbon::now()->subYears(120)->format('Y-m-d');

        $request->validate([
            'ci' => 'required|numeric|unique:pacientes,ci',
            // Solo letras con espacios, incluyendo tildes y eñes
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255',
            'apellido_paterno' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255',
            'apellido_materno' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255',
            // Solo números enteros
            'celular' => 'nullable|numeric|digits_between:7,15|unique:pacientes,celular',
            // Obliga a que la extensión sea estrictamente @gmail.com
            'correo' => 'required|email|max:255|unique:pacientes,correo',
            // Validación de rango de fecha de nacimiento coherente
            'fecha_nacimiento' => 'required|date|before_or_equal:' . $fechaLimiteAdulto . '|after:' . $fechaLimitePasado,
            'direccion' => 'required|string|max:255',
            'genero' => 'required|string',
        ], [
            // TRADUCCIONES Y PERSONALIZACIÓN DE ERRORES AL CREAR
            'ci.unique' => 'Este número de C.I. ya está registrado en otro paciente.',
            'correo.unique' => 'Este correo electrónico ya está en uso. Intente con otro.',
            'nombre.regex' => 'El nombre sólo debe contener letras.',
            'apellido_paterno.regex' => 'El apellido paterno sólo debe contener letras.',
            'apellido_materno.regex' => 'El apellido materno sólo debe contener letras.',
            'celular.unique' => 'Este número de celular ya está registrado en otro paciente.',
            'celular.numeric' => 'El número de celular debe contener únicamente números.',
            'correo.regex' => 'El correo electrónico debe pertenecer obligatoriamente al dominio @gmail.com.',
            'fecha_nacimiento.before_or_equal' => 'El paciente debe ser obligatoriamente mayor de edad (mínimo 18 años).',
            'fecha_nacimiento.after' => 'La fecha de nacimiento introducida es incoherente.',
        ]);

        try {
            Paciente::create($request->all());
            return redirect()->back()->with('success', 'Paciente registrado con éxito y debidamente validado.');
        } catch (\Exception $e) {
            // withInput() devuelve lo que el usuario escribió si hay un error de base de datos
            return redirect()->back()->withInput()->with('error', 'Error en la consistencia de datos: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::withTrashed()->findOrFail($id);
        
        $fechaLimiteAdulto = Carbon::now()->subYears(18)->format('Y-m-d');
        $fechaLimitePasado = Carbon::now()->subYears(120)->format('Y-m-d');

        $request->validate([
            'ci' => 'required|numeric|unique:pacientes,ci,' . $paciente->id,
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255',
            'apellido_paterno' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255',
            'apellido_materno' => 'nullable|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255',
            'celular' => 'nullable|numeric|digits_between:7,15|unique:pacientes,celular,' . $paciente->id,
            'correo' => 'required|email|max:255|unique:pacientes,correo,' . $paciente->id,
            'fecha_nacimiento' => 'required|date|before_or_equal:' . $fechaLimiteAdulto . '|after:' . $fechaLimitePasado,
            'direccion' => 'required|string|max:255',
            'genero' => 'required|string',
        ], [
            // TRADUCCIONES Y PERSONALIZACIÓN DE ERRORES AL EDITAR
            'ci.unique' => 'Este número de C.I. ya está registrado en otro paciente.',
            'correo.unique' => 'Este correo electrónico ya está en uso. Intente con otro.',    
            'nombre.regex' => 'El nombre sólo debe contener letras.',
            'apellido_paterno.regex' => 'El apellido paterno sólo debe contener letras.',
            'apellido_materno.regex' => 'El apellido materno sólo debe contener letras.',
            'celular.unique' => 'Este número de celular ya está registrado en otro paciente.',
            'celular.numeric' => 'El número de celular debe contener únicamente números.',
            'correo.regex' => 'El correo electrónico debe pertenecer obligatoriamente al dominio @gmail.com.',
            'fecha_nacimiento.before_or_equal' => 'El paciente debe ser obligatoriamente mayor de edad (mínimo 18 años).',
            'fecha_nacimiento.after' => 'La fecha de nacimiento introducida es incoherente.',
        ]);

        try {
            $paciente->update($request->all());
            return redirect()->back()->with('success', 'Expediente clínico actualizado y validado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el expediente: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $paciente = Paciente::findOrFail($id);
            
            // 1. Cambiamos el texto en la base de datos físicamente
            $paciente->update(['estado' => 'INACTIVO']); 
            
            // 2. Aplicamos el eliminado lógico (ocultarlo del sistema)
            $paciente->delete(); 
            
            return redirect()->back()->with('success', 'Paciente inactivado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al inactivar: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $paciente = Paciente::withTrashed()->findOrFail($id);
            
            // 1. Volvemos a colocar el texto físicamente en ACTIVO
            $paciente->update(['estado' => 'ACTIVO']); 
            
            // 2. Quitamos el eliminado lógico
            $paciente->restore(); 
            
            return redirect()->back()->with('success', 'Paciente reactivado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            // withTrashed() es crucial aquí para poder encontrar al paciente que ya estaba inactivo
            $paciente = Paciente::withTrashed()->findOrFail($id);
            
            // forceDelete() borra la fila físicamente de la base de datos. ¡No hay vuelta atrás!
            $paciente->forceDelete(); 
            
            return redirect()->back()->with('success', '¡Paciente eliminado permanentemente del sistema de forma física!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar eliminar físicamente: ' . $e->getMessage());
        }
    }
}
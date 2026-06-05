<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialidad;

class EspecialidadController extends Controller
{
    // Listado general estructurado de forma cronológica ascendente
    public function index()
    {
        $especialidades = Especialidad::withTrashed()->orderBy('id', 'asc')->get();
        return view('admin.especialidades.index', compact('especialidades'));
    }

    // Procesar e insertar una nueva especialidad debidamente validada
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255|unique:especialidades,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.regex' => 'El nombre de la especialidad médica sólo debe contener letras.',
            'nombre.unique' => 'Esta especialidad ya se encuentra registrada en el sistema de la clínica.'
        ]);

        try {
            Especialidad::create($request->all());
            return redirect()->back()->with('success', 'Especialidad médica registrada y validada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error en la consistencia de datos: ' . $e->getMessage());
        }
    }

    // Actualizar los datos del catálogo médico
    public function update(Request $request, $id)
    {
        $especialidad = Especialidad::withTrashed()->findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255|unique:especialidades,nombre,' . $especialidad->id,
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.regex' => 'El nombre de la especialidad sólo debe contener letras.',
            'nombre.unique' => 'El nombre de la especialidad ya está en uso.'
        ]);

        try {
            $especialidad->update($request->all());
            return redirect()->back()->with('success', 'Catálogo de especialidad actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }

    // Inactivar registro (Borrado lógico - SoftDelete)
    public function destroy($id)
    {
        try {
            $especialidad = Especialidad::findOrFail($id);
            $especialidad->delete();
            return redirect()->back()->with('success', 'La especialidad ha sido inactivada del sistema.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al inactivar el registro: ' . $e->getMessage());
        }
    }

    // Reactivar registro (Restore)
    public function restore($id)
    {
        try {
            $especialidad = Especialidad::withTrashed()->findOrFail($id);
            $especialidad->restore();
            return redirect()->back()->with('success', 'Especialidad médica reactivada y reincorporada al catálogo.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al reactivar el registro: ' . $e->getMessage());
        }
    }
}
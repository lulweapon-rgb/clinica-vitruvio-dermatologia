<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::withTrashed()->orderBy('id', 'asc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.regex' => 'El nombre del rol solo debe contener letras.',
            'nombre.unique' => 'Este rol de acceso ya se encuentra registrado en el sistema.'
        ]);

        try {
            Rol::create($request->all());
            return redirect()->back()->with('success', 'Rol de sistema registrado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::withTrashed()->findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/|max:255|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.regex' => 'El nombre del rol solo debe contener letras.',
            'nombre.unique' => 'El nombre del rol ya está en uso.'
        ]);

        try {
            $rol->update($request->all());
            return redirect()->back()->with('success', 'Rol de sistema actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            $rol->update(['estado' => 'INACTIVO']);
            $rol->delete();
            return redirect()->back()->with('success', 'El rol ha sido inactivado del sistema.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al inactivar el registro: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $rol = Rol::withTrashed()->findOrFail($id);
            $rol->update(['estado' => 'ACTIVO']);
            $rol->restore();
            return redirect()->back()->with('success', 'Rol reactivado y reincorporado al catálogo.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al reactivar el registro: ' . $e->getMessage());
        }
    }
    public function forceDelete($id)
    {
        try {
            $rol = Rol::withTrashed()->findOrFail($id);
            // Borra la fila de PostgreSQL para siempre
            $rol->forceDelete(); 
            
            return redirect()->back()->with('success', '¡Rol eliminado permanentemente del sistema de forma física!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al intentar eliminar físicamente: ' . $e->getMessage());
        }
    }
}
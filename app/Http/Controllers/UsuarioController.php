<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // <-- IMPORTANTE: Añadimos esta clase para validaciones complejas
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Especialidad;
use App\Models\PersonalMedico;
use App\Models\LogAcceso;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with(['rol', 'personalMedico.especialidad'])->withTrashed()->orderBy('id', 'asc')->get();
        $roles = Rol::orderBy('nombre', 'asc')->get();
        $especialidades = Especialidad::orderBy('nombre', 'asc')->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
           'ci' => 'required|numeric|unique:usuarios,ci', // Cambiamos string|max:50 por numeric
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            // El personal debe tener entre 18 y 85 años
'fecha_nacimiento' => 'required|date|after_or_equal:' . now()->subYears(85)->format('Y-m-d') . '|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'direccion' => 'required|string|max:255',
            'celular' => 'required|numeric|digits:8|unique:usuarios,celular',
            'rol_id' => 'required|exists:roles,id',
            'correo' => 'required|email|unique:usuarios,correo|max:255',
            'contrasena' => 'required|string|min:6',
            'matricula_profesional' => 'nullable|string|unique:personal_medico,matricula_profesional'
        ], [
            // Traducciones y mensajes personalizados
            'ci.unique' => 'Este número de C.I. ya está registrado para otro usuario.',
            'ci.numeric' => 'El C.I. debe contener únicamente números.',
            'fecha_nacimiento.after_or_equal' => 'La fecha de nacimiento no es válida (excede el límite de edad permitido).',
'fecha_nacimiento.before_or_equal' => 'El personal registrado debe ser mayor de 18 años.',
            'correo.unique' => 'Este correo electrónico ya está en uso.',
            'celular.unique' => 'Este número de celular ya pertenece a otro usuario.',
            'matricula_profesional.unique' => 'Esta matrícula médica ya está registrada.'
        ]);

        try {
            DB::beginTransaction();

            // BUSCAMOS EL NOMBRE DEL ROL PARA GUARDARLO EN TEXTO
            $rol = Rol::find($request->rol_id);

            $usuario = Usuario::create([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion,
                'celular' => $request->celular,
                'rol_id' => $request->rol_id,
                'rol_nombre' => $rol ? $rol->nombre : 'SIN ROL', // <-- AGREGADO
                'correo' => $request->correo,
                'contrasena' => Hash::make($request->contrasena)
            ]);

            if ($request->filled('especialidad_id')) {
                PersonalMedico::create([
                    'usuario_id' => $usuario->id,
                    'especialidad_id' => $request->especialidad_id,
                    'matricula_profesional' => $request->matricula_profesional
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Personal registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al guardar en BD: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::withTrashed()->findOrFail($id);

        $request->validate([
            'ci' => 'required|numeric|unique:usuarios,ci,' . $usuario->id, // Cambiamos string|max:50 por numeric
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            // El personal debe tener entre 18 y 85 años
'fecha_nacimiento' => 'required|date|after_or_equal:' . now()->subYears(85)->format('Y-m-d') . '|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'direccion' => 'required|string|max:255',
            // Agregamos unique e ignoramos el ID
            'celular' => 'required|numeric|digits:8|unique:usuarios,celular,' . $usuario->id,
            'rol_id' => 'required|exists:roles,id',
            'correo' => 'required|email|unique:usuarios,correo,' . $usuario->id,
            'matricula_profesional' => [
                'nullable', 'string',
                Rule::unique('personal_medico', 'matricula_profesional')->ignore($usuario->id, 'usuario_id')
            ]
        ], [
            // Traducciones y mensajes personalizados
            'ci.unique' => 'Este número de C.I. ya está registrado para otro usuario.',
            'ci.numeric' => 'El C.I. debe contener únicamente números.',
            'correo.unique' => 'Este correo electrónico ya está en uso.',
            'celular.unique' => 'Este número de celular ya pertenece a otro usuario.',
            'matricula_profesional.unique' => 'Esta matrícula médica ya está registrada.'
        ]);

        try {
            DB::beginTransaction();

            // BUSCAMOS EL NOMBRE DEL ROL PARA ACTUALIZARLO
            $rol = Rol::find($request->rol_id);

            $usuario->update([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion,
                'celular' => $request->celular,
                'rol_id' => $request->rol_id,
                'rol_nombre' => $rol ? $rol->nombre : 'SIN ROL', // <-- AGREGADO
                'correo' => $request->correo,
            ]);

            if ($request->filled('contrasena')) {
                $usuario->update(['contrasena' => Hash::make($request->contrasena)]);
            }

            if ($request->filled('especialidad_id')) {
                PersonalMedico::updateOrCreate(
                    ['usuario_id' => $usuario->id],
                    [
                        'especialidad_id' => $request->especialidad_id,
                        'matricula_profesional' => $request->matricula_profesional
                    ]
                );
            } else {
                if ($usuario->personalMedico) {
                    $usuario->personalMedico->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Datos del personal actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            
            // 1. Cambiamos estado físicamente
            $usuario->update(['estado' => 'INACTIVO']);
            
            // 2. Borrado Lógico
            $usuario->delete(); 
            
            return redirect()->back()->with('success', 'Acceso inactivado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $usuario = Usuario::withTrashed()->findOrFail($id);
            
            // 1. Restauramos estado físico
            $usuario->update(['estado' => 'ACTIVO']);
            
            // 2. Quitamos borrado lógico
            $usuario->restore();
            
            return redirect()->back()->with('success', 'Acceso reactivado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // BORRADO FÍSICO DEFINITIVO CON ELIMINACIÓN EN CASCADA
    public function forceDelete($id)
    {
        try {
            $usuario = Usuario::withTrashed()->findOrFail($id);
            
            // 1. Primero destruimos a su "hijo" en la tabla personal_medico (si es que tiene uno)
            if ($usuario->personalMedico) {
                // Usamos forceDelete por si esa tabla también tiene borrado lógico
                $usuario->personalMedico->forceDelete(); 
            }
            
            // 2. Ahora sí, destruimos al "padre" en la tabla usuarios
            $usuario->forceDelete();
            
            return redirect()->back()->with('success', 'Usuario y su perfil médico fueron eliminados físicamente del sistema.');
            
        } catch (\Exception $e) {
            // Manejamos el error en caso de que el usuario tenga otras llaves foráneas restrictivas
            return redirect()->back()->with('error', 'Error al eliminar físicamente: ' . $e->getMessage());
        }
    }
}
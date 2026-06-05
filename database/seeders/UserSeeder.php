<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener el rol ADMIN si existe, o crearlo dejando que PostgreSQL asigne el ID
        $rolAdmin = DB::table('roles')->where('nombre', 'ADMIN')->first();
        if (!$rolAdmin) {
            $adminId = DB::table('roles')->insertGetId([
                'nombre' => 'ADMIN',
                'descripcion' => 'Administrador General del Sistema',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $adminId = $rolAdmin->id;
        }

        // 2. Obtener el rol USER si existe, o crearlo automáticamente
        $rolMedico = DB::table('roles')->where('nombre', 'USER')->first();
        if (!$rolMedico) {
            $medicoId = DB::table('roles')->insertGetId([
                'nombre' => 'USER',
                'descripcion' => 'Médico Evaluador Clínico',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $medicoId = $rolMedico->id;
        }

        // 3. Crear al Administrador asignándole el ID dinámico que generó la base de datos
        Usuario::updateOrCreate(
            ['correo' => 'admin@vitruvio.com'], 
            [
                'nombre' => 'Administrador Vitruvio',
                'contrasena' => Hash::make('Admin123!'),
                'rol_id' => $adminId, 
                'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
                'two_factor_enabled' => true
            ] 
        );

        // 4. Crear al Médico Evaluador 
        Usuario::updateOrCreate(
            ['correo' => 'medico@vitruvio.com'],
            [
                'nombre' => 'Médico Evaluador',
                'contrasena' => Hash::make('Medico123!'),
                'rol_id' => $medicoId,
                'two_factor_secret' => 'KNRW24TMMJQXEZLJ',
                'two_factor_enabled' => true
            ]
        );
    }
}
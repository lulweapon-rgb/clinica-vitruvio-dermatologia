<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <-- Necesario para manejar la tabla de roles

class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. CREAR LOS ROLES SI NO EXISTEN
        // ==========================================
        // Esto evita el error de llave foránea (Foreign key violation)
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            ['nombre' => 'Administrador'] // Nota: Si tu columna no se llama 'nombre', cámbiala aquí
        );

        DB::table('roles')->updateOrInsert(
            ['id' => 2],
            ['nombre' => 'Medico']
        );

        // ==========================================
        // 2. CREAR LOS USUARIOS DE PRUEBA
        // ==========================================
        
        // Administrador
        Usuario::updateOrCreate(
            ['correo' => 'admin@ejemplo.com'],
            [
                'nombre' => 'Admin',
                'apellido_paterno' => 'Prueba',
                'contrasena' => Hash::make('Admin123'),
                'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
                'estado' => 'ACTIVO',
                'rol_id' => 1,
                'rol_nombre' => 'Administrador'
            ]
        );

        // Usuario Regular (Médico)
        Usuario::updateOrCreate(
            ['correo' => 'user@ejemplo.com'],
            [
                'nombre' => 'Usuario',
                'apellido_paterno' => 'Regular',
                'contrasena' => Hash::make('User123'),
                'two_factor_secret' => 'KNRW24TMMJQXEZLJ',
                'estado' => 'ACTIVO',
                'rol_id' => 2,
                'rol_nombre' => 'Medico'
            ]
        );
    }
}
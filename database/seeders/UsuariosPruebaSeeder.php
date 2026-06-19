<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. CREAR ROLES SIN FORZAR EL ID (Modo estricto PostgreSQL)
        // ==========================================
        
        // Creamos o actualizamos buscando por NOMBRE, dejando que Postgres decida el ID
        DB::table('roles')->updateOrInsert(
            ['nombre' => 'Administrador']
        );
        // Rescatamos el ID que Postgres le haya asignado
        $idAdmin = DB::table('roles')->where('nombre', 'Administrador')->value('id');

        DB::table('roles')->updateOrInsert(
            ['nombre' => 'Medico']
        );
        // Rescatamos el ID que Postgres le haya asignado
        $idMedico = DB::table('roles')->where('nombre', 'Medico')->value('id');

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
                'rol_id' => $idAdmin, // <-- Usamos el ID rescatado
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
                'rol_id' => $idMedico, // <-- Usamos el ID rescatado
                'rol_nombre' => 'Medico'
            ]
        );
    }
}
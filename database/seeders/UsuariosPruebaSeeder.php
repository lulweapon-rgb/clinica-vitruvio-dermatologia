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
        
        \App\Models\Usuario::updateOrCreate(
    ['correo' => 'admin@prueba.com'], // Correo exacto del requisito
    [
        'nombre' => 'Admin',
        'apellido_paterno' => 'Prueba',
        'contrasena' => \Illuminate\Support\Facades\Hash::make('Admin123!'), // Contraseña exacta
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'estado' => 'ACTIVO',
        'rol_id' => $idAdmin,
        'rol_nombre' => 'Administrador'
    ]
);

\App\Models\Usuario::updateOrCreate(
    ['correo' => 'user@prueba.com'], // Correo exacto del requisito
    [
        'nombre' => 'Usuario',
        'apellido_paterno' => 'Regular',
        'contrasena' => \Illuminate\Support\Facades\Hash::make('User123!'), // Contraseña exacta
        'two_factor_secret' => encrypt('KNRW24TMMJQXEZLJ'),
        'estado' => 'ACTIVO',
        'rol_id' => $idMedico,
        'rol_nombre' => 'Medico'
    ]
);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario; // Llamamos a tu modelo en español
use Illuminate\Support\Facades\Hash;

class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Administrador
        Usuario::updateOrCreate(
            ['correo' => 'admin@ejemplo.com'], // Mapeado a tu columna 'correo'
            [
                'nombre' => 'Admin',           // Mapeado a tu columna 'nombre'
                'apellido_paterno' => 'Prueba', 
                'contrasena' => Hash::make('Admin123'), // Mapeado a 'contrasena'
                'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
                'estado' => 'ACTIVO',
                'rol_id' => 1, // Cambia este número si tu rol de Admin tiene otro ID
                'rol_nombre' => 'Administrador'
            ]
        );

        // 2. Usuario Regular (Médico)
        Usuario::updateOrCreate(
            ['correo' => 'user@ejemplo.com'],
            [
                'nombre' => 'Usuario',
                'apellido_paterno' => 'Regular',
                'contrasena' => Hash::make('User123'),
                'two_factor_secret' => 'KNRW24TMMJQXEZLJ',
                'estado' => 'ACTIVO',
                'rol_id' => 2, // Cambia este número si tu rol de Médico tiene otro ID
                'rol_nombre' => 'Medico' 
            ]
        );
    }
}
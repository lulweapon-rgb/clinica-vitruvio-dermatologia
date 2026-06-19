<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuariosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuario Administrador
        $admin = Usuario::updateOrCreate(
            ['correo' => 'admin@prueba.com'],
            [
                'nombre'            => 'Admin',
                'apellido'          => 'Prueba',
                'contrasena'        => Hash::make('Admin123!'),
                'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            ]
        );
        
        // Si usas Spatie para los roles, se asigna así:
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('ADMINISTRADOR'); 
        }

        // 2. Usuario Regular
        $user = Usuario::updateOrCreate(
            ['correo' => 'user@prueba.com'],
            [
                'nombre'            => 'Usuario',
                'apellido'          => 'Regular',
                'contrasena'        => Hash::make('User123!'),
                'two_factor_secret' => 'KNRW24TMMJQXEZLJ',
            ]
        );

        // Si usas Spatie para los roles, se asigna así:
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('MEDICO'); 
        }
    }
}
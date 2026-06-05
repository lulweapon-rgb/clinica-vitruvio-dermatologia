<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Usamos Authenticatable para el Login futuro
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'usuarios';
    public $timestamps = true;
    const DELETED_AT = 'eliminado_at';
    const CREATED_AT = 'creado_at';         // <--- AGREGAR ESTO
    const UPDATED_AT = 'actualizado_at';

    protected $fillable = [
        'rol_id',
        'contrasena',
        'correo',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'ci',                 // NUEVO
        'fecha_nacimiento',   // NUEVO
        'direccion',
        'celular',
        'telefono',
        'foto_base64',
        'codigo_2fa',
        'estado',
        'rol_nombre'
    ];

    // Ocultar la contraseña al devolver datos en JSON o Arrays
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    // Laravel espera que la columna de contraseña se llame 'password', 
    // le decimos que en tu BD se llama 'contrasena'
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // RELACIONES DE BASE DE DATOS
    
    // Un usuario pertenece a un Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Un usuario PUEDE SER personal médico (Relación 1 a 1)
    public function personalMedico()
    {
        return $this->hasOne(PersonalMedico::class, 'usuario_id');
    }
}
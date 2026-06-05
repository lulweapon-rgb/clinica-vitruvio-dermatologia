<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use SoftDeletes;

    protected $table = 'pacientes';
    public $timestamps = true; 
    const DELETED_AT = 'eliminado_at';

    protected $fillable = [
        'ci',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'celular',
        'correo',
        'estado' // <--- NUEVO CAMPO AÑADIDO
    ];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
}
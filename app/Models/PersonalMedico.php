<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalMedico extends Model
{
    use SoftDeletes;

    protected $table = 'personal_medico';
    public $timestamps = true;
    
    // Le indicamos a Laravel el nombre exacto de tus columnas
    const DELETED_AT = 'eliminado_at';
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = 'actualizado_at';

    protected $fillable = [
        'usuario_id',
        'especialidad_id',
        'matricula_profesional'
    ];

    // RELACIONES DE BASE DE DATOS
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }
}
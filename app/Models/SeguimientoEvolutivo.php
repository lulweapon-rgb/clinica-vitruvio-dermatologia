<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeguimientoEvolutivo extends Model
{
    use SoftDeletes;

    protected $table = 'seguimientos_evolutivos';
    public $timestamps = true;
    
    // Estandarización al español
    const DELETED_AT = 'eliminado_at';
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = 'actualizado_at';

    protected $fillable = [
        'evaluacion_id',
        'medico_id',
        'fecha_control',
        'diagnostico_definitivo',
        'cambio_tamano',
        'cambio_color',
        'imagen_control',
        'observaciones',
        'estado_tratamiento'
    ];
    
    
    public function paciente()
    {
        // El ->withTrashed() obliga a Laravel a traer al paciente aunque esté inactivo
        return $this->belongsTo(Paciente::class)->withTrashed();
    }

    // RELACIONES
    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionClinica::class, 'evaluacion_id');
    }

    public function medico()
    {
        return $this->belongsTo(PersonalMedico::class, 'medico_id');
    }
}
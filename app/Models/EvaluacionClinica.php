<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluacionClinica extends Model
{
    use SoftDeletes;

    protected $table = 'evaluaciones_clinicas';
    public $timestamps = true;
    
    // Estandarización al español
    const DELETED_AT = 'eliminado_at';
    const CREATED_AT = 'creado_at';
    const UPDATED_AT = 'actualizado_at';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'diagnostico_clinico',
        'ia_diagnostico',
        'ia_porcentaje',
        'imagen_lesion',
        'estado_validacion',
        'estado',
        
        'ubicacion_anatomica',
        'tiempo_evolucion',     // NUEVO
        'sintoma_picazon',      // NUEVO
        'sintoma_sangrado',     // NUEVO
        'sintoma_crecimiento'   // NUEVO
    ];

    // RELACIONES DE BASE DE DATOS
    
    // Una evaluación pertenece a un Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // Una evaluación fue realizada por un Médico
    public function medico()
    {
        return $this->belongsTo(PersonalMedico::class, 'medico_id');
    }
    // Dentro de app/Models/EvaluacionClinica.php

// Una evaluación tiene muchos seguimientos evolutivos
public function seguimientos()
{
    return $this->hasMany(SeguimientoEvolutivo::class, 'evaluacion_id')->orderBy('fecha_control', 'asc');
}
}
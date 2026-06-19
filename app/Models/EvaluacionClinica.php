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

    // ==========================================================
    // ESTADOS DEL FLUJO DE TELEMEDICINA / TRIAGE ASÍNCRONO
    // ==========================================================
    const ESTADO_PENDIENTE = 'PENDIENTE_ESPECIALISTA'; // Fase 1 y 2
    const ESTADO_RESUELTO  = 'RESUELTO_REMOTO';        // Fase 3 y 4 (Resolución a distancia)
    const ESTADO_DERIVADO  = 'DERIVADO_PRESENCIAL';    // Fase 3 y 4 (Requiere ver al paciente)

   protected $fillable = [
        'paciente_id',
        'medico_id',
        'imagen_lesion',
        'ia_diagnostico',
        'ia_porcentaje',
        'estado_validacion',
        'estado',
        
        // Fase 1: Motivo
        'motivo_consulta',
        'duracion_lesion',
        
        // Fase 2: Síntomas locales y sistémicos
        'sintoma_prurito',
        'sintoma_dolor',
        'sintoma_sangrado',
        'sintoma_parestesia',
        'otros_sintomas_sistemicos',
        
        // Fase 3: Exploración Física
        'ubicacion_anatomica',
        'tipo_lesion_morfologia',
        'coloracion_lesion',
        'consistencia_palpacion',
        'distribucion_lesiones',
        
        // Fase 4: Dictamen del Especialista
        'diagnostico_clinico',
        'prueba_diagnostica',
        'plan_tratamiento',
        'factores_externos',
    'aspecto_general',
    'disposicion_lesiones'
        
    ];

    // ==========================================
    // RELACIONES DE BASE DE DATOS
    // ==========================================
    
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico()
    {
        return $this->belongsTo(PersonalMedico::class, 'medico_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoEvolutivo::class, 'evaluacion_id')->orderBy('fecha_control', 'asc');
    }
}
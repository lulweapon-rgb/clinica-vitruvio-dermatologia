<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Especialidad extends Model
{
    use SoftDeletes;

    protected $table = 'especialidades';
    public $timestamps = true; 
    const DELETED_AT = 'eliminado_at';

    protected $fillable = ['nombre', 'descripcion'];
}
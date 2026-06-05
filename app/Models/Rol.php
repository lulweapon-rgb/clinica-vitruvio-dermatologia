<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use SoftDeletes;

    protected $table = 'roles';
    public $timestamps = true; 
    const DELETED_AT = 'eliminado_at';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $dates = ['eliminado_at', 'created_at', 'updated_at'];
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'grupo_interes_id', 'nombre', 'descripcion', 'fecha', 'duracion', 'costo', 'estado'
    ];
}

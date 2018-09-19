<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudIngreso extends Model
{
    protected $table = 'solicitudes_ingreso';

    protected $fillable = [
        'persona_id', 'grupo_interes_id', 'estado'
    ];
}

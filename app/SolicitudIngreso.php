<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudIngreso extends Model
{
    protected $fillable = [
        'persona_id', 'grupo_interes_id', 'estado'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'codigo', 'administrador', 'nombre_usuario', 'nombres', 'apellidos'
    ];

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoInteres extends Model
{
    protected $table = 'grupos_interes';

    protected $fillable = [
        'tema_id', 'nombre'
    ];

}

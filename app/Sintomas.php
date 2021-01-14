<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sintomas extends Model
{
    protected $table = 'Sintoma';
    public $timestamps = false;

    protected $fillable = [
        'idSintoma',
        'nombreSintoma',
        'preguntaSintoma',
        'peso',
        'image64',
        'imageType'
    ];
}

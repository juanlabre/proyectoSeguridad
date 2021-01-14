<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCabezera extends Model
{


    protected $table = 'Diagnostico_Cabecera';
    public $timestamps = false;

    protected $fillable = [
        'idDiagnostico_Cabecera',
        'fechaDiagnostico',
        'resultadoDiagnostico',
        'sumatoriaDiagnostico',
        'idUsuarioPertenece',
        'activo',

    ];

   
   
}

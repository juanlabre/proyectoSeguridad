<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleSintomas extends Model
{


    protected $table = 'Diagnostico_Detalle';
    public $timestamps = false;

    protected $fillable = [
        'idDiagnostico_Detalle',
        'idSintomaPertenece',
        'RespuestaSintoma',
        'idDiagnosticoPertenece',
    ];

   
   
}

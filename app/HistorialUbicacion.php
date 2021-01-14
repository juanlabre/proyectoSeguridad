<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class HistorialUbicacion extends Model
{
    use SpatialTrait;

    protected $table = 'Historial_Ubicaciones';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'idUsuario',
        'ubicacionGeografica',
        'FechaHora'
    ];

    protected $hidden = [
        'id',
        'idUsuario',
    ];

    protected $spatialFields = [
        'ubicacionGeografica'
    ];
}

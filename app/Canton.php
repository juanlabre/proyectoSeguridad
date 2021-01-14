<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Canton extends Model
{
    protected $table = 'Canton';
    protected $primaryKey   = 'idCanton';
    protected $fillable = [
        'nombreCanton',
        'idProvinciaPertenece',
    ];

    public function provincia()
    {
        return $this->belongsTo('App\Provincia','idProvincia','idProvinciaPertence');
    }
}

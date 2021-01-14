<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'Provincia';
    protected $primaryKey   = 'idProvincia';
    protected $fillable = [
        'nombreProvincia',
    ];

    public $timestamps = false;
    
    public function cantones()
    {
        return $this->hasMany('App\Canton','idProvinciaPertenece','idProvincia');
    }
}

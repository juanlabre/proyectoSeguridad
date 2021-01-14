<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'Perfil';
    protected $primaryKey   = 'idPerfil';
    protected $fillable = [
        'nombrePerfil',
        'descripcionPerfil',
    ];

    public $timestamps = false;

    public function usuarios()
    {
        return $this->belongsToMany('App\User', 'Usuario_Perfil', 'idPerfilPertenece','idUsuarioPertenece');
    }
}

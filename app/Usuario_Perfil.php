<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario_Perfil extends Model
{
    protected $table = 'Usuario_Perfil';
    protected $primaryKey   = 'idUsuario_Perfil';
    protected $fillable = [
        'idPerfilPertenece',
        'idUsuarioPertenece',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User','idUsuarioPertenece','idUsuario');
    }

    public function perfil()
    {
        return $this->belongsTo('App\Perfil','idPerfilPertenece','idPerfil');
    }
}

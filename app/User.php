<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SpatialTrait;

    protected $table = 'Usuario';
    public $timestamps = false;
    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'idUsuario',
        'cedulaUsuario',
        'nombresUsuario',
        'apellidosUsuario',
        'direccionUsuario',
        'telefonoUsuario',
        'correoUsuario',
        'numFamiliares',
        'fechaNacimiento',
        'ubicacionGeografica',
        'idCantonPertenece',
        'fechaRegistro',
        'user',
        'password',
    ];
    /**
    *protected $hidden = [
    *    'user',
    *    'password',
    *];
    */
    protected $spatialFields = ['ubicacionGeografica'];

    public function perfiles()
    {
        return $this->belongsToMany('App\Perfil', 'Usuario_Perfil', 'idUsuarioPertenece', 'idPerfilPertenece');
    }
}

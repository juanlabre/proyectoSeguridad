<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');

Route::middleware('auth:api')->group(function () {
    Route::get('user/info', 'AuthController@userInfo');
    Route::post('user/logout', 'AuthController@logout');
    Route::post('user/changePassword', 'AuthController@changePassword');
    Route::post('user/updateUserInfo', 'UsuarioController@updateUserInfo');

    //CRUD Encuentas Sintomas
    Route::get('getPreguntas', 'EncuestasController@getPreguntas');
    Route::post('getCabecerasByUser', 'EncuestasController@getCabecerasByUser');
    Route::post('getLastCabeceraByUser', 'EncuestasController@getLastCabeceraByUser');
    Route::post('registerDetalle', 'EncuestasController@postDetalle');
    Route::post('registerCabecera', 'EncuestasController@registerCabecera');
    Route::post('updateCabeceraSuma', 'EncuestasController@updateCabeceraSuma');
    Route::post('updateCabeceraState', 'EncuestasController@updateCabeceraState');
    Route::post('deleteCabezera', 'EncuestasController@deleteCabezera');
    Route::post('DeleteDetail', 'EncuestasController@DeleteDetail');
    Route::get('listaUsuariosByIdPerfil/{perfil}', 'PerfilUsuarioController@listaUsuariosByIdPerfil');
    Route::post('agregarUsuarioByIdPerfil/{perfil}','PerfilUsuarioController@agregarUsuarioByIdPerfil');
    Route::put('actualizarUsuario/{user}','PerfilUsuarioController@actualizarUsuario');
    Route::delete('eliminarUsuario/{user}','PerfilUsuarioController@eliminarUsuario');
    
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('historialCantidadRegistrosDiarios', 'HistorialUbicacionController@getHistorial_CantidadRegistrosDiarios');
Route::get('conteoPorTipoDiagnostico', 'HistorialUbicacionController@getCantidadPorCadaDiagnostico');
Route::get('historialPorTipoDiagnostico', 'HistorialUbicacionController@getHistorial_CantidadPorTipoDiagnostico');
Route::get('cantidadPorSintoma', 'HistorialUbicacionController@getCantidadPorSintoma');

/* Usuario */
Route::resource('usuario', 'UsuarioController');
Route::get('/existcedula/{cedula}',  ['as' => 'existcedula', 'uses' => 'UsuarioController@getUsuarioIfExistByCedula']);
Route::get('/existcorreo/{correo}',  ['as' => 'existcorreo', 'uses' => 'UsuarioController@getUsuarioIfExistByCorreo']);
Route::get('/existusuario/{usuario}',  ['as' => 'existusuario', 'uses' => 'UsuarioController@getUsuarioIfExistByUserName']);
/**/

//Manejo de Mapas
Route::get('todasLasUbicacionesUsuario', 'PosicionesController@getTodasLasUbicacionesUsuario');
Route::get('showpositionsdate/{valuetype}', 'PosicionesController@showpositionsdate');
Route::get('listaUbicacionesByLimitSintomas', 'PerfilUsuarioController@listaUbicacionesByLimitSintomas');

/**
 * SERVICIOS EQUIPO 2
 */
/**
 * Perfil
 */
//Route::resource('perfiles', 'PerfilController', ['except' => ['create', 'edit']]);
/**
 * Usuario_Perfil
 */
Route::get('totalesUsuariosByIdPerfil/{perfil}', 'PerfilUsuarioController@totalesUsuariosByIdPerfil');
Route::get('totalesRegistrosUltimos6Meses', 'PerfilUsuarioController@totalesRegistrosUltimos6Meses');
Route::get('totalesRegistrosUltimosMeses', 'PerfilUsuarioController@totalesRegistrosUltimosMeses');
Route::get('totalesRegistersByProvincia', 'PerfilUsuarioController@totalesRegistersByProvincia');
Route::get('totalesRegisterBySintoma', 'PerfilUsuarioController@totalesRegisterBySintoma');
Route::get('existeUsuario','PerfilUsuarioController@getUsuarioByUserName');
Route::get('existeCorreo','PerfilUsuarioController@getUsuarioByCorreo');
Route::get('existeCedula','PerfilUsuarioController@getUsuarioByCedula');
/**
 * Provincia
 */
//Route::resource('provincias', 'ProvinciaController', ['except' => ['create', 'edit']]);
/**
 * Canton_Provincia
 */
Route::get('listaCantonesByIdProvincia/{provincia}', 'ProvinciaCantonController@listaCantonesByIdProvincia');
/**
 * Canton
 */
//Route::resource('cantones', 'CantonController', ['except' => ['create', 'edit']]);
//});

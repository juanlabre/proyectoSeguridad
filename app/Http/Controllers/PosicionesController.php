<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;
class PosicionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**public function index()
    {
    }*/

    public function getTodasLasUbicacionesUsuario()
    {
        //Obtener todas la posiciones
        $posiciones=DB::table('usuario')->join('diagnostico_cabecera', 'usuario.idUsuario', '=', 'diagnostico_cabecera.idUsuarioPertenece')
        ->selectRaw('st_x(usuario.ubicacionGeografica) as lat, st_y(usuario.ubicacionGeografica) as lng, diagnostico_cabecera.sumatoriaDiagnostico')
        ->whereRaw("diagnostico_cabecera.fechaDiagnostico=(SELECT MAX(fechaDiagnostico) FROM diagnostico_cabecera WHERE idUsuarioPertenece = usuario.idUsuario)")->get();
        return response()->json([
            'posiciones' => $posiciones,
            'HttpResponse' => [
                'ok' => true,
                'status' => 200,
            ]
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $date
     * @return \Illuminate\Http\Response
     */
    public function showpositionsdate($date)
    {
        //Obtener fecha menos los días especificados
        $date = Carbon::now()->subDays((int)$date);
        $datetime = $date->toDateString();
        //Obtener la fecha actual
        $today=Carbon::now()->toDateString();        
        //Obtener posiciones por fechas
        $posiciones=DB::table('usuario')->join('diagnostico_cabecera', 'usuario.idUsuario', '=', 'diagnostico_cabecera.idUsuarioPertenece')
        ->selectRaw('st_x(usuario.ubicacionGeografica) as lat, st_y(usuario.ubicacionGeografica) as lng, diagnostico_cabecera.sumatoriaDiagnostico')
        ->whereBetween('fechaRegistro',[$datetime,$today])
        ->whereRaw("diagnostico_cabecera.fechaDiagnostico=(SELECT MAX(fechaDiagnostico) FROM diagnostico_cabecera WHERE idUsuarioPertenece = usuario.idUsuario)")->get();
        return response()->json([
            'posiciones' => $posiciones,
            'HttpResponse' => [
                'ok' => true,
                'status' => 200,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

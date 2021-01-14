<?php

namespace App\Http\Controllers;

use App\Perfil;
use App\User;
use App\Usuario_Perfil;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerfilUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listaUsuariosByIdPerfil(Perfil $perfil)
    {
        /**
         * Lista de Usuarios
         * Filtrado por Perfil
         */
        $listaUsuarios = $perfil->usuarios;
        return response()->json(["ListaUsuariosByIdPerfil" => $listaUsuarios], 200);
    }

    public function totalesUsuariosByIdPerfil(Perfil $perfil)
    {
        $today=Carbon::now();
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;
        $idPerfil = $perfil->idPerfil;
        /**
         * Total de Usuarios registrados
         * Filtrado por Perfil
         */
        $totalUsuarios = $perfil->usuarios->count();
        /**
         * Total de Usuarios registrados
         * En el Mes Actual
         * Filtrado por Perfil
         */
        $resultsMesActual = DB::select("SELECT  COUNT(idUsuario) AS total
                                        FROM    Usuario u, Usuario_Perfil up, Perfil p 
                                        WHERE   Year(fechaRegistro) = $year and
                                                MONTH(fechaRegistro) = $month and
                                                u.idUsuario = up.idUsuarioPertenece and
                                                up.idPerfilPertenece=p.idPerfil and
                                                p.idPerfil=$idPerfil");
        
        foreach ($resultsMesActual as $resultMesActual) {
            $totalUsuariosMes = $resultMesActual->total;
        }
        /**
         * Total de Usuarios registrados
         * En el Dia Actual
         * Filtrado por Perfil
         */
        $resultsDiaActual = DB::select("SELECT  COUNT(idUsuario) AS total
                                        FROM    Usuario u, Usuario_Perfil up, Perfil p 
                                        WHERE   Year(fechaRegistro) = $year and
                                                MONTH(fechaRegistro) = $month and
                                                DAY(fechaRegistro) = $day and
                                                u.idUsuario = up.idUsuarioPertenece and
                                                up.idPerfilPertenece=p.idPerfil and
                                                p.idPerfil=$idPerfil");

        foreach ($resultsDiaActual as $resultDiaActual) {
            $totalUsuariosDia = $resultDiaActual->total;
        }

        return response()->json(["TotalRegistros" => $totalUsuarios,
                                 "RegistrosMes" => $totalUsuariosMes,
                                 "RegistrosDia" => $totalUsuariosDia], 200);
    }

    public function agregarUsuarioByIdPerfil(Request $request,Perfil $perfil)
    {
        if(!$request->input('cedulaUsuario') || !$request->input('nombresUsuario') || !$request->input('apellidosUsuario') || !$request->input('fechaNacimiento')
        || !$request->input('direccionUsuario') || !$request->input('telefonoUsuario') || !$request->input('correoUsuario') || !$request->input('user')
        || !$request->input('password')){
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Comprobar los campos.'])],422);
        }
        $nuevoUsuario = new User([
            'user' => $request->user,
            'password' => Hash::make($request->password),
            'cedulaUsuario' => $request->cedulaUsuario,
            'nombresUsuario' => $request->nombresUsuario,
            'apellidosUsuario' => $request->apellidosUsuario,
            'direccionUsuario' => $request->direccionUsuario,
            'telefonoUsuario' => $request->telefonoUsuario,
            'correoUsuario' => $request->correoUsuario,
            'fechaNacimiento' => $request->fechaNacimiento,
        ]);

        $nuevoUsuario->save();
        if($perfil->idPerfil != 2){
        $nuevoUsuarioPerfil = Usuario_Perfil::create(['idPerfilPertenece' => 2, 
                                                      'idUsuarioPertenece' => $nuevoUsuario->idUsuario,
                                                     ]);
        }

        $nuevoUsuarioPerfil = Usuario_Perfil::create(['idPerfilPertenece' => $perfil->idPerfil, 
                                                     'idUsuarioPertenece' => $nuevoUsuario->idUsuario,
                                                    ]);

        return response()->json(['UsuarioAgregado' => $nuevoUsuario],201);
    }

    public function actualizarUsuario(Request $request, User $user)
    {
        $actuliza = false;
        $actulizaPerfil = false;
        if($user->user == 'admin'){
            return response()->json(['error' => 'El recurso al que se está teniendo acceso está bloqueado.', 'code' => 423], 423);
        }
        if($request->has('cedulaUsuario')){
            if($user->cedulaUsuario != $request->cedulaUsuario)
            {
                $user->cedulaUsuario = $request->cedulaUsuario;
                $actuliza = true;
            }
        }
        if($request->has('nombresUsuario')){
            if($user->nombresUsuario != $request->nombresUsuario)
            {
                $user->nombresUsuario = $request->nombresUsuario;
                $actuliza = true;
            }
        }
        if($request->has('apellidosUsuario')){
            if($user->apellidosUsuario != $request->apellidosUsuario)
            {
                $user->apellidosUsuario = $request->apellidosUsuario;
                $actuliza = true;
            }
        }
        if($request->has('fechaNacimiento')){
            if($user->fechaNacimiento != $request->fechaNacimiento)
            {
                $user->fechaNacimiento = $request->fechaNacimiento;
                $actuliza = true;
            }
        }
        if($request->has('direccionUsuario')){
            if($user->direccionUsuario != $request->direccionUsuario)
            {
                $user->direccionUsuario = $request->direccionUsuario;
                $actuliza = true;
            }
        }
        if($request->has('telefonoUsuario')){
            if($user->telefonoUsuario != $request->telefonoUsuario)
            {
                $user->telefonoUsuario = $request->telefonoUsuario;
                $actuliza = true;
            }
        }
        if($request->has('correoUsuario')){
            if($user->correoUsuario != $request->correoUsuario)
            {
                $user->correoUsuario = $request->correoUsuario;
                $actuliza = true;
            }
        }
        
        if($request->has('user')){
            if($user->user != $request->user)
            {
                $user->user = $request->user;
                $actuliza = true;
            }
        }
        
        if($request->has('password')){
                $user->password = Hash::make($request->password);
                $actuliza = true;
        }
        
        if($request->has('administrador'))
        {
            $perfilesUsuario = DB::select("SELECT  *
                                            FROM    Usuario_Perfil 
                                            WHERE   idUsuarioPertenece=$user->idUsuario
                                                    AND idPerfilPertenece = 3");
            $esAdmin =  !empty($perfilesUsuario);

            if($esAdmin and $request->administrador == 0)
            {
                $actualizar = DB::table('Usuario_Perfil')
                                ->where('idUsuarioPertenece',$user->idUsuario)
                                ->where('idPerfilPertenece',3)
                                ->update(['idPerfilPertenece'  => 4]);
                $actulizaPerfil =  true;
            }
            elseif(!$esAdmin and $request->administrador == 1)
            {
                $actualizar = DB::table('Usuario_Perfil')
                                ->where('idUsuarioPertenece',$user->idUsuario)
                                ->where('idPerfilPertenece',4)
                                ->update(['idPerfilPertenece'  => 3]);
                $actulizaPerfil = true;
            }
        }
        if($actuliza || $actulizaPerfil)
        {
            if($actulizaPerfil)
            {
                return response()->json(['UsuarioActualizado' => $user],200);
            }
            else
            {
                $user->save();
                return response()->json(['UsuarioActualizado' => $user],200);
            }
        }
        else
        {
            return response()->json(['error' => 'Ingrese al menos un valor diferente para actualizar.', 'code' => 422], 422);
        }
        
    }

    public function eliminarUsuario(User $user)
    {
        if($user->user == 'admin'){
            return response()->json(['error' => 'El recurso al que se está teniendo acceso está bloqueado.', 'code' => 423], 423);
        }
        $perfilesUsuario = Usuario_Perfil::where('idUsuarioPertenece','=',$user->idUsuario)->delete();
        $user->delete();
        return response()->json(['UsuarioEliminado' => $user],200);
        
    }

    public function totalesRegistrosUltimos6Meses()
    {
        $today=Carbon::now();
        $year = $today->year;
        $month = $today->month;
        $totalsCiudadanosUltimosMeses = array();

        /**
         * Lista en Orden
         * Últimos 6 Meses
         */

        if($month < 6)
        {
            $previousYear = $year -1;
            $differenceMonths = 6 - $month;
            $startMonthPreviousYear = 13 - $differenceMonths;
            for ($i=$startMonthPreviousYear; $i <= 12; $i++) {
                $months[] = $this->monthIntToName($i)." ".$previousYear;
                $totals[] =  $this->totalRegistersByYearMonth($previousYear, $i);
            }
            for ($i=1; $i <= $month; $i++) { 
                $months[] = $this->monthIntToName($i)." ".$year;
                $totals[] =  $this->totalRegistersByYearMonth($year, $i);
            }
        }
        else
        {
            $startMonth = $month - 5;
            for ($i=$startMonth; $i <= $month; $i++) {
                $months[] = $this->monthIntToName($i)." ".$year;
                $totals[] =  $this->totalRegistersByYearMonth($year, $i);
            }
        }

        for ($i=0; $i <= 5; $i++) {
            $totalMonth["mes"] = $months[$i];
            $totalMonth["total"] = $totals[$i];
            
            $totalsCiudadanosUltimosMeses[] = $totalMonth;
        }
        
        return response()->json(['TotalesCiudadanos' => $totalsCiudadanosUltimosMeses], 200);
    }

    protected function totalRegistersByYearMonth($year, $month)
    {
        $resultsMesActual = DB::select("SELECT  COUNT(idUsuario) AS total
                                        FROM    Usuario u, Usuario_Perfil up, Perfil p 
                                        WHERE   Year(fechaRegistro) = $year and
                                                MONTH(fechaRegistro) = $month and
                                                u.idUsuario = up.idUsuarioPertenece and
                                                up.idPerfilPertenece=p.idPerfil and
                                                p.idPerfil=2");
        
        foreach ($resultsMesActual as $resultMesActual) {
            $totalRegisters = $resultMesActual->total;
        }
        return $totalRegisters;
    }

    protected function monthIntToName($number)
    {
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $meses[$number-1];
    }

    public function totalesRegistersByProvincia()
    {
        $resultsMesActual = DB::select("SELECT  c.nombreCanton as canton,COUNT(*) as total
                                        FROM    Usuario u, Canton c 
                                        WHERE   u.idCantonPertenece = c.idCanton
                                        GROUP BY u.idCantonPertenece");
        
        return response()->json(['TotalesPorProvincia' => $resultsMesActual],200);
    }

    public function totalesRegisterBySintoma()
    {
        $result = DB::select("SELECT s.idSintoma as ID,s.nombreSintoma as Sintoma, COUNT(*) as Total 
                                        FROM Diagnostico_Cabecera dc,Diagnostico_Detalle dd, Sintoma s 
                                        WHERE dc.activo=1 and dc.idDiagnostico_Cabecera = dd.idDiagnosticoPertenece 
                                                and dd.idSintomaPertenece = s.idSintoma and dd.RespuestaSintoma > 0 
                                        GROUP BY s.nombreSintoma 
                                        ORDER BY s.idSintoma;");
        
        return response()->json(['TotalesPorSintoma' => $result],200);
    }

    protected function totalRegistersByParroquia($year, $month)
    {
        $results = DB::select("SELECT c.idCanton as ID, c.nombreCanton as Nombre, COUNT(u.idUsuario) as Total 
                                        FROM Usuario u, Canton c, Usuario_Perfil up 
                                        WHERE u.idUsuario = up.idUsuarioPertenece and u.idCantonPertenece = c.idCanton 
                                            and up.idPerfilPertenece = 2 and YEAR(fechaRegistro)=$year 
                                            and MONTH(fechaRegistro)=$month 
                                        GROUP BY c.idCanton;");
        return $results;
    }

    public function totalesRegistrosUltimosMeses()
    {
        $today=Carbon::now();
        $year = $today->year;
        $month = $today->month;
        $totalsCiudadanosUltimosMeses = array();

        /**
         * Lista en Orden
         * Últimos 6 Meses
         */

        if($month < 6)
        {
            $previousYear = $year -1;
            $differenceMonths = 6 - $month;
            $startMonthPreviousYear = 13 - $differenceMonths;
            for ($i=$startMonthPreviousYear; $i <= 12; $i++) {
                $months[] = $this->monthIntToName($i)." ".$previousYear;
                $totals[] =  $this->totalRegistersByParroquia($previousYear, $i);
            }
            for ($i=1; $i <= $month; $i++) { 
                $months[] = $this->monthIntToName($i)." ".$year;
                $totals[] =  $this->totalRegistersByParroquia($year, $i);
            }
        }
        else
        {
            $startMonth = $month - 5;
            for ($i=$startMonth; $i <= $month; $i++) {
                $months[] = $this->monthIntToName($i)." ".$year;
                $totals[] =  $this->totalRegistersByParroquia($year, $i);
            }
        }

        for ($i=0; $i <= 5; $i++) {
            $totalMonth["mes"] = $months[$i];
            $totalMonth["totales"] = $totals[$i];
            
            $totalsCiudadanosUltimosMeses[] = $totalMonth;
        }
        
        return response()->json(['TotalesCiudadanosPorParroquia' => $totalsCiudadanosUltimosMeses], 200);
    }

    public function listaUbicacionesByLimitSintomas(Request $request)
    {
        $min = $request->minimo;
        $max = $request->maximo;
        $results = DB::select(" SELECT ST_X(ubicacionGeografica) as x, ST_Y(ubicacionGeografica) as y
                                FROM Diagnostico_Detalle,
                                     Diagnostico_Cabecera,
                                     Usuario
                                WHERE idUsuario=idUsuarioPertenece
                                      and idDiagnostico_Cabecera=idDiagnosticoPertenece
                                      and activo=1
                                      and RespuestaSintoma > 0
                                GROUP BY idUsuario
                                HAVING COUNT(RespuestaSintoma) >=$min and COUNT(RespuestaSintoma) <=$max;");
        

        return response()->json(['ListaCiudadanosPorLimiteSintomas' => $results],200);
    }

    public function getUsuarioByUserName(Request $request)
    {
        $usuario = $request->usuario;

        $results = DB::select("SELECT u.idUsuario, u.user
        FROM Usuario u
        WHERE u.user = '$usuario';");
        $idUsuario = 0;
        if (!$results) {
            return response()->json(['existe' => false, 'ID' => $idUsuario], 200);
        }
        foreach($results as $result){
            $idUsuario = $result->idUsuario;
        }
        return response()->json(['existe' => true, 'ID' => $idUsuario], 200);
    }

    public function getUsuarioByCorreo(Request $request)
    {
        $correo = $request->correo;

        $results = DB::select("SELECT u.idUsuario, u.correoUsuario
        FROM Usuario u
        WHERE u.correoUsuario = '$correo';");
        $idUsuario = 0;
        if (!$results) {
            return response()->json(['existe' => false, 'ID' => $idUsuario], 200);
        }
        foreach($results as $result){
            $idUsuario = $result->idUsuario;
        }
        return response()->json(['existe' => true, 'ID' => $idUsuario], 200);
    }

    public function getUsuarioByCedula(Request $request)
    {
        $cedula = $request->cedula;

        $results = DB::select("SELECT u.idUsuario, u.cedulaUsuario
        FROM Usuario u
        WHERE u.cedulaUsuario = '$cedula';");
        $idUsuario = 0;
        if (!$results) {
            return response()->json(['existe' => false, 'ID' => $idUsuario], 200);
        }
        foreach($results as $result){
            $idUsuario = $result->idUsuario;
        }
        return response()->json(['existe' => true, 'ID' => $idUsuario], 200);
    }
}

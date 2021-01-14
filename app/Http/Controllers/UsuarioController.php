<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Usuario_Perfil;

class UsuarioController extends Controller
{

    public function index()
    {
        $listaUsuarios = User::all();

        return response()->json(['ListaUsuarios' => $listaUsuarios], 200);
    }

    public function store(Request $request)
    {

        if (
            !$request->input('cedulaUsuario') || !$request->input('nombresUsuario') || !$request->input('apellidosUsuario') || !$request->input('fechaNacimiento')
            || !$request->input('direccionUsuario') || !$request->input('telefonoUsuario') || !$request->input('correoUsuario') || !$request->input('user')
            || !$request->input('password') || !$request->input('numFamiliares') || !$request->input('idCantonPertenece') || !$request->input('fechaRegistro')
            || !$request->input('ubicacionGeografica')
        ) {
            return response()->json(['errors' => array(['code' => 422, 'message' => 'No se permiten campos vacios'])], 422);
        }

        $nuevoUsuario = new User([
            'cedulaUsuario' => $request['cedulaUsuario'],
            'nombresUsuario' => $request['nombresUsuario'],
            'apellidosUsuario' => $request['apellidosUsuario'],
            'direccionUsuario' => $request['direccionUsuario'],
            'telefonoUsuario' => $request['telefonoUsuario'],
            'correoUsuario' => $request['correoUsuario'],
            'numFamiliares' => $request['numFamiliares'],
            'fechaNacimiento' => $request['fechaNacimiento'],
            'ubicacionGeografica' => DB::raw("(GeomFromText($request->ubicacionGeografica))"),
            'idCantonPertenece' => $request['idCantonPertenece'],
            'fechaRegistro' => $request['fechaRegistro'],
            'user' => $request['user'],
            'password' => Hash::make($request['password']),
        ]);

        $nuevoUsuario->save();

        $nuevoUsuarioPerfil = Usuario_Perfil::create([
            'idPerfilPertenece' => 2,
            'idUsuarioPertenece' => $nuevoUsuario->idUsuario,
        ]);

        return response()->json(['data' => true], 201);
    }

    public function show($idUsuario)
    {
        $usuario = User::find($idUsuario);

        if (!$usuario) {
            return response()->json(['errors' => array(['code' => 404, 'message' => 'No se encuentra un usuario con ese id.'])], 404);
        }
        return response()->json(['status' => 'ok', 'data' => $usuario], 200);
    }

    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['errors' => array(['code' => 404, 'message' => 'No tiene permisos para realizar esta acción'])], 404);
        }

        $user_id = $user->idUsuario;

        $usuario = User::find($user_id);

        $cedulaUsuario = $request->input('cedulaUsuario');
        $nombresUsuario = $request->input('nombresUsuario');
        $apellidosUsuario = $request->input('apellidosUsuario');
        $direccionUsuario = $request->input('direccionUsuario');
        $telefonoUsuario = $request->input('telefonoUsuario');
        $correoUsuario = $request->input('correoUsuario');
        $numFamiliares = $request->input('numFamiliares');
        $fechaNacimiento = $request->input('fechaNacimiento');
        $ubicacionGeografica = $request->input('ubicacionGeografica');
        $idCantonPertenece = $request->input('idCantonPertenece');
        $user = $request->input('user');
        $password = $request->input('password');

        if ($request->method() === 'POST') {
            $bandera = false;
            if ($cedulaUsuario) {
                $usuario->cedulaUsuario = $cedulaUsuario;
                $bandera = true;
            }
            if ($nombresUsuario) {
                $usuario->nombresUsuario = $nombresUsuario;
                $bandera = true;
            }
            if ($apellidosUsuario) {
                $usuario->apellidosUsuario = $apellidosUsuario;
                $bandera = true;
            }
            if ($direccionUsuario) {
                $usuario->direccionUsuario = $direccionUsuario;
                $bandera = true;
            }
            if ($telefonoUsuario) {
                $usuario->telefonoUsuario = $telefonoUsuario;
                $bandera = true;
            }
            if ($correoUsuario) {
                $usuario->correoUsuario = $correoUsuario;
                $bandera = true;
            }
            if ($numFamiliares) {
                $usuario->numFamiliares = $numFamiliares;
                $bandera = true;
            }
            if ($fechaNacimiento) {
                $usuario->fechaNacimiento = $fechaNacimiento;
                $bandera = true;
            }
            if ($ubicacionGeografica) {
                $usuario->ubicacionGeografica = DB::raw("(GeomFromText($ubicacionGeografica))");
                $bandera = true;
            }
            if ($idCantonPertenece) {
                $usuario->idCantonPertenece = $idCantonPertenece;
                $bandera = true;
            }
            if ($user) {
                $usuario->user = $user;
                $bandera = true;
            }
            if ($password) {
                $usuario->password = Hash::make($password);
                $bandera = true;
            }

            if ($bandera) {
                $usuario->save();
                return response()->json(['status' => 'ok', 'data' => $usuario], 200);
            } else {
                return response()->json(['errors' => array(['code' => 304, 'message' => 'No se ha modificado ningún dato de fabricante.'])], 304);
            }
        }

        // Si el método no es PATCH entonces es PUT y tendremos que actualizar todos los datos.
        if (
            !$cedulaUsuario ||
            !$nombresUsuario ||
            !$apellidosUsuario ||
            !$direccionUsuario ||
            !$telefonoUsuario ||
            !$correoUsuario ||
            !$numFamiliares ||
            !$fechaNacimiento ||
            !$ubicacionGeografica ||
            !$idCantonPertenece ||
            !$user ||
            !$password
        ) {
            return response()->json(['errors' => array(['code' => 422, 'message' => 'Faltan valores para completar el procesamiento.'])], 422);
        }

        $usuario->cedulaUsuario = $cedulaUsuario;
        $usuario->nombresUsuario = $nombresUsuario;
        $usuario->apellidosUsuario = $apellidosUsuario;
        $usuario->direccionUsuario = $direccionUsuario;
        $usuario->telefonoUsuario = $telefonoUsuario;
        $usuario->correoUsuario = $correoUsuario;
        $usuario->numFamiliares = $numFamiliares;
        $usuario->fechaNacimiento = $fechaNacimiento;
        $usuario->ubicacionGeografica = DB::raw("(GeomFromText($ubicacionGeografica))");
        $usuario->idCantonPertenece = $idCantonPertenece;
        $usuario->user = $user;
        $usuario->password = Hash::make($password);


        // Almacenamos en la base de datos el registro.
        $usuario->save();
        return response()->json(['status' => 'ok', 'data' => $usuario], 200);
    }

    public function getUsuarioIfExistByCedula(Request $request)
    {
        $cedula = $request->cedula;

        $results = DB::select("SELECT u.cedulaUsuario
        FROM Usuario u
        WHERE u.cedulaUsuario = '$cedula';");

        if (!$results) {
            return response()->json(['status' => 'ok', 'data' => false], 200);
        }

        return response()->json(['status' => 'ok', 'data' => true], 200);
    }

    public function getUsuarioIfExistByCorreo(Request $request)
    {
        $correo = $request->correo;

        $results = DB::select("SELECT u.correoUsuario
        FROM Usuario u
        WHERE u.correoUsuario = '$correo';");

        if (!$results) {
            return response()->json(['status' => 'ok', 'data' => false], 200);
        }

        return response()->json(['status' => 'ok', 'data' => true], 200);
    }

    public function getUsuarioIfExistByUserName(Request $request)
    {
        $usuario = $request->usuario;

        $results = DB::select("SELECT u.user
        FROM Usuario u
        WHERE u.user = '$usuario';");

        if (!$results) {
            return response()->json(['status' => 'ok', 'data' => false], 200);
        }

        return response()->json(['status' => 'ok', 'data' => true], 200);
    }

    public function listaUsuarios()
    {
        $listaUsuarios = User::all();

        return response()->json(['ListaUsuarios' => $listaUsuarios], 200);
    }
}

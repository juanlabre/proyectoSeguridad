<?php

namespace App\Http\Controllers;

use App\User;
use App\DetalleCabezera;
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user' => 'required|string',
                'password' => 'required|string',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'error' => 'Check fields'
            ], 400);
        }

        $user = new User([
            'user' => $request->user,
            'password' => Hash::make($request->password),
            'cedulaUsuario' => $request->cedulaUsuario,
            'nombresUsuario' => $request->nombresUsuario,
            'apellidosUsuario' => $request->apellidosUsuario,
            'direccionUsuario' => $request->direccionUsuario,
            'telefonoUsuario' => $request->telefonoUsuario,
            'correoUsuario' => $request->correoUsuario,
            'numFamiliares' => $request->numFamiliares,
            'fechaNacimiento' => $request->fechaNacimiento,
            'ubicacionGeografica' => $request->ubicacionGeografica,
            'tipoUsuario' => $request->tipoUsuario,
            'idCantonPertenece' => $request->idCantonPertenece,
        ]);

        $user->save();

        return response()->json(
            [
                'message' => 'Successfully created user!'
            ],
            201
        );
    }


    public function login(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'user' => 'required|string',
                'password' => 'required|string'
            ]
        );

        if ($validation->fails()) {
            return response()->json(
                [
                    'HttpResponse' => [
                        'message' => 'Check fields',
                        'status' => 400,
                        'statusText' => 'Bad Request',
                        'ok' => false
                    ],
                ]
            );
        }

        $credentials = [
            'user' => $request->user,
            'password' => $request->password
        ];

        if (!(Auth::attempt($credentials))) {
            return response()->json(
                [
                    'message' => 'Invalid credentials',
                    'status' => 401,
                    'statusText' => 'Unauthorized',
                    'ok' => false
                ]
            );
        }

        //Token
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return response()->json(
            [
                'access_token' => $tokenResult->accessToken,
                'expires' => Carbon::parse($tokenResult->token->expires_at->toDateTimeString()),
                'roles' => DB::table('Usuario_Perfil')
                    ->join('Perfil', 'Usuario_Perfil.idPerfilPertenece', '=', 'Perfil.idPerfil')
                    ->where('Usuario_Perfil.idUsuarioPertenece', '=', $request->user()->idUsuario)
                    ->pluck('Perfil.nombrePerfil')
            ]
        );
    }

    public function userInfo(Request $request)
    {
        try {

            $user = $request->user();
            return response()->json(
                [
                    'data' => [
                        'idUsuario' =>  $user->idUsuario,
                        'cedulaUsuario' =>  $user->cedulaUsuario,
                        'nombresUsuario' =>  $user->nombresUsuario,
                        'apellidosUsuario' =>  $user->apellidosUsuario,
                        'direccionUsuario' =>  $user->direccionUsuario,
                        'telefonoUsuario' =>  $user->telefonoUsuario,
                        'correoUsuario' =>  $user->correoUsuario,
                        'numFamiliares' =>  $user->numFamiliares,
                        'fechaNacimiento' =>  $user->fechaNacimiento,
                        'ubicacionGeografica' =>  $user->ubicacionGeografica,
                        'tipoUsuario' =>  $user->tipoUsuario,
                        'idCantonPertenece' =>  $user->idCantonPertenece,

                    ],
                    'HttpResponse' => [
                        'status' => 200,
                        'statusText' => 'OK',
                        'ok' => true
                    ]
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'Error' => $th->errorInfo,
                    'HttpResponse' => [
                        'status' => 400,
                        'statusText' => 'Bad Request',
                        'ok' => false
                    ]
                ]
            );
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
            'HttpResponse' => [
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
    }

    //
    public function changePassword(Request $request)
    {
        // $user = $request->user();
        $user = Auth::user();

        if (!$user) {
            return response()->json(["result" => false]);
        }

        $curPassword = $request->curPassword;
        $newPassword = $request->newPassword;

        if (Hash::check($curPassword, $user->password)) {
            $user_id = $user->idUsuario;
            $obj_user = User::find($user_id);
            $obj_user->password = Hash::make($newPassword);
            // $result = DB::update("UPDATE usuario set password='$obj_user->password' WHERE idUsuario=$user_id;");
            $obj_user->save();
            // if (!$result > 0) {
            //     return response()->json(["result" => false]);
            // }
            return response()->json(["result" => true]);
        } else {
            return response()->json(["result" => false]);
        }
    }
    //

    // public function updatePassword()
    // {
    //     $usersArray=User::all();
    //     foreach($usersArray as $userItem ){
    //         $user=User::find($userItem->idUsuario);
    //         $user->password=Hash::make($userItem->user);
    //         $user->save();
    //     }
    //     return response()->json(
    //         [
    //     'Response' => $usersArray[0]->password

    //     ]
    // );
    // }

    // public function check()
    // {

    //     // $pw = '2048215447';
    //     // $hashed = Hash::make($pw);
    //     // $hashed2 = '$2y$10$kYgkS1APLT0YzYnOxL.S6.lpby1QWyv63OSDbNeY7m5lKcSfdS8Ca';

    //     //Hash::check($pw, $hashed);
    //     $usersArray=User::all();
    //     $hashedPassword= $usersArray[0]->password;
    //     return response()->json(
    //         [
    //             Hash::check($usersArray[0]->user, $hashedPassword),
    //            $usersArray[0]->password
    //         // Hash::check($pw, $hashed),
    //         // $hashed
    //     ]
    // );
    // }
}

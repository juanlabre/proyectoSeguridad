<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\DetalleSintomas;
use App\DetalleCabezera;
use App\Sintomas;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EncuestasController extends Controller
{
     function getPreguntas(){
        $lastRegister = Sintomas::orderBy('idSintoma')->get()->all();
        return $lastRegister;
    }
    function getImg(){

    }
    public function postDetalle(Request $request)
                 {
                 $idU=$request->idUsuario;
                 $idlastCabecera = DetalleCabezera::where('idUsuarioPertenece',$idU)->select('idDiagnostico_Cabecera')->get()->last();
                        $idCab=$idlastCabecera->idDiagnostico_Cabecera;
                foreach($request['Detalles'] as $Detail){
                        $lastRegister = DetalleSintomas::orderBy('idDiagnostico_Detalle')->get()->last();
                                      if($lastRegister){
                                      $id=$lastRegister->idDiagnostico_Detalle+1;
                                      $registerDetalle = new DetalleSintomas([
                                                  'idDiagnostico_Detalle'=>$id,
                                                  'idSintomaPertenece' => $Detail['idSintomaPertenece'],
                                                  'RespuestaSintoma' => $Detail['RespuestaSintoma'],
                                                  'idDiagnosticoPertenece' => $idCab,
                                              ]);
                                              $registerDetalle->save();

                                      }else{
                                         $registerDetalle = new DetalleSintomas([
                                                 'idDiagnostico_Detalle'=>1,
                                                 'idSintomaPertenece' => $Detail->idSintomaPertenece,
                                                 'RespuestaSintoma' => $Detail->RespuestaSintoma,
                                                 'idDiagnosticoPertenece' => $idCab,
                                              ]);

                                              $registerDetalle->save();


                                      };
                }
                return response()->json(
                                                                   [
                                                                       'message' => 'Successfully created DetalleSintomas!'
                                                                   ],
                                                                   201
                                                               );


                 }

         public function registerCabecera(Request $request)
             {
          $lastRegister = DetalleCabezera::orderBy('idDiagnostico_Cabecera')->get()->last();
          $suma=$request->sumatoriaDiagnostico;
          settype($suma,"integer");
          if($suma>=50){
             $resultado='Positivo';
          }else
          {
             $resultado='Negativo';
          };
          if($lastRegister){
          $id=$lastRegister->idDiagnostico_Cabecera+1;
          $registerCabecera = new DetalleCabezera([
                      'idDiagnostico_Cabecera'=>$id,
                      'fechaDiagnostico' => $request->fechaDiagnostico,
                      'resultadoDiagnostico' => $resultado,
                      'sumatoriaDiagnostico' => $request->sumatoriaDiagnostico,
                      'idUsuarioPertenece' => $request->idUsuarioPertenece,
                      'activo' => 1,
                  ]);

                  $registerCabecera->save();

                  return response()->json(
                      [
                          'message' => 'Successfully created DetalleCabezera!',
                      ],
                      201
                  );
          }else{
             $registerCabecera = new DetalleCabezera([
                      'idDiagnostico_Cabecera'=>1,
                      'fechaDiagnostico' => $request->fechaDiagnostico,
                      'resultadoDiagnostico' =>$resultado,
                      'sumatoriaDiagnostico' => $request->sumatoriaDiagnostico,
                      'idUsuarioPertenece' => $request->idUsuarioPertenece,
                      'activo' => 1,
                  ]);

                  $registerCabecera->save();

                  return response()->json(
                      [
                          'message' => 'Successfully created DetalleCabezera!',
                      ],
                      201
                  );
          };

             }

             public function updateCabeceraSuma(Request $request)
             {
             $idU=$request->id;
                              $idlastCabecera = DetalleCabezera::where('idUsuarioPertenece',$idU)->select('idDiagnostico_Cabecera')->get()->last();
                                     $idCab=$idlastCabecera->idDiagnostico_Cabecera;
             $suma = $request->suma;
             if($suma>=50){
             DetalleCabezera::where('idDiagnostico_Cabecera',$idCab )->update(
                ['sumatoriaDiagnostico' => $request->suma,
                  'resultadoDiagnostico' => 'Positivo',
                  'activo'=>1
                 ]);
                               return response()->json(
                                   [
                                       'message' => 'Successfully Update DetalleCabezera!',

                                   ],
                                   201
                               );

             }else{

                          DetalleCabezera::where('idDiagnostico_Cabecera',$id )->update(
                          ['sumatoriaDiagnostico' => $request->suma,
                          'resultadoDiagnostico' => 'Negativo',
                          'activo'=>1
                          ]);
                               return response()->json(
                                   [
                                       'message' => 'Successfully Update DetalleCabezera!',

                                   ],
                                   201
                               );
             };

          }
          public function updateCabeceraState(Request $request)
                       {
                       $id = $request->id;
                       DetalleCabezera::where('idUsuarioPertenece',$id )->update(
                              ['activo' =>0
                                ]);
         $lastRegister = DetalleCabezera::orderBy('idDiagnostico_Cabecera')->where('idUsuarioPertenece',$id)->select('idDiagnostico_Cabecera')->get()->last();
                            $idCabe=$lastRegister->idDiagnostico_Cabecera;
                            DetalleCabezera::where('idDiagnostico_Cabecera',$idCabe)->update(['activo' =>1]);
                                         return response()->json(
                                             [
                                                 'message' => 'Successfully Update DetalleCabezera!'
                                             ],
                                             201
                                         );
                    }
          public function DeleteDetail(Request $request){
                $id = $request->id;

               DetalleSintomas::where('idDiagnosticoPertenece',$id)->delete();

                 return response()->json(
                              [
                   'message' => 'Successfully Delete DetalleSintomas!'
                             ],
                               201
                                 );
          }
         function deleteCabezera(Request $request) {
               $id = $request->id;

                DetalleCabezera::where('idDiagnostico_Cabecera',$id)->delete();

                 return response()->json(
                 [
              'message' => 'Successfully Delete Cabecera!'
                 ], 201 );
         }
         function getCabecerasByUser(Request $request){
         $id = $request->id;
         $lastRegister = DetalleCabezera::OrderBy('activo')->where('idUsuarioPertenece',$id)
         ->select('fechaDiagnostico','resultadoDiagnostico')->get();
         return response()->json(
             [
             'message' => 'Successfully Get Cabeceras!',
             'data'=> $lastRegister
              ], 201 );
         }
         function getLastCabeceraByUser(Request $request){
                  $id = $request->id;
                  $lastRegister = DetalleCabezera::OrderBy('activo')
                  ->where('idUsuarioPertenece',$id)->select('fechaDiagnostico','idDiagnostico_Cabecera','resultadoDiagnostico')->get()->last();

                  return response()->json(
                      [
                      'message' => 'Successfully Get Last Cabecera!',
                      'data'=> $lastRegister
                       ], 201 );
                  }

}

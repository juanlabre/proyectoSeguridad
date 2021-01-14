<?php

namespace App\Http\Controllers;

use App\HistorialUbicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class HistorialUbicacionController extends Controller
{

    public function getCantidadPorSintoma()
    {
        $result = DB::select("SELECT COUNT(dd.idSintomaPertenece) as conteo, s.nombreSintoma as sintoma
            FROM usuario u, diagnostico_cabecera dc, diagnostico_detalle dd, sintoma s
            WHERE dc.idUsuarioPertenece = u.idUsuario
            AND dd.idDiagnosticoPertenece = dc.idDiagnostico_Cabecera
            AND dd.idSintomaPertenece = s.idSintoma
            GROUP BY s.idSintoma");

        $conteos = array();
        $sintomas = array();
        $colores = array();

        foreach ($result as $registro) {
            array_push($conteos, $registro->conteo);
            array_push($sintomas, $registro->sintoma);
            $color = "rgba(" . rand(1, 255) . ", " . rand(1, 255) . ", " . rand(1, 255) . ", " . rand(1, 10)/10 . ")";
            array_push($colores, $color);
        }

        return response()->json([
            'data' => [
                'etiquetas' => $sintomas,
                'datos' => $conteos,
                'colores' => $colores
                // 'axisY' => $axisY
            ],
            'HttpResponse' => [
                'ok' => true,
                'status' => 200,
            ]
        ]);
    }

    public function getCantidadPorCadaDiagnostico()
    {
        $results = DB::select("SELECT d_c.resultadoDiagnostico AS resultado, COUNT(d_c.resultadoDiagnostico) AS conteo
                    FROM Usuario u,
                        Diagnostico_Cabecera d_c
                    WHERE u.idUsuario = d_c.idUsuarioPertenece
                    GROUP BY d_c.resultadoDiagnostico;");

        $etiquetas = array();
        $datos = array();

        foreach ($results as $registro) {
            array_push($etiquetas, $registro->resultado);
            array_push($datos, $registro->conteo);
        }

        return response()->json([
            'data' => [
                'etiquetas' => $etiquetas,
                'datos' => $datos,
                // 'axisY' => $axisY
            ],
            'HttpResponse' => [
                'ok' => true,
                'status' => 200,
            ]
        ]);
    }

    public function getHistorial_CantidadPorTipoDiagnostico()
    {

        $diagnosticos = DB::select("SELECT DISTINCT(Diagnostico_Cabecera.resultadoDiagnostico) as diagnostico FROM Diagnostico_Cabecera");
        $select_axisX = DB::select("SELECT DISTINCT(DATE_FORMAT(u.fechaRegistro, '%d-%m')) AS fechaRegistros
                    FROM Usuario u
                    WHERE DATE(u.fechaRegistro) >= (
                            SELECT DATE(u.fechaRegistro - INTERVAL 8 DAY)
                            FROM Usuario u
                            ORDER BY u.fechaRegistro DESC
                            LIMIT 1
                        )
                    ORDER BY u.fechaRegistro ASC");

        $axisX = array();
        $array_datos = array();

        foreach ($select_axisX as $s) {
            array_push($axisX, $s->fechaRegistros);
        }

        foreach ($diagnosticos as $d) {

            $results = DB::select("SELECT DATE_FORMAT(d_c.fechaDiagnostico, '%d-%m') as fechaRegistros, COUNT(u.idUsuario) as cantidadRegistros
                        FROM Usuario u,
                        Diagnostico_Cabecera d_c
                        WHERE DATE(u.fechaRegistro) >= (
                            SELECT DATE_FORMAT(u.fechaRegistro - INTERVAL 8 DAY, '%Y-%m-%d')
                            FROM Usuario u
                            ORDER BY u.fechaRegistro DESC
                            LIMIT 1
                        )
                        AND d_c.idUsuarioPertenece = u.idUsuario
                        AND d_c.resultadoDiagnostico = '" . $d->diagnostico . "'
                        GROUP BY DATE(u.fechaRegistro)
                        ORDER BY u.fechaRegistro ASC;");

            $ejeY = array();

            foreach ($select_axisX as $fechas_generales) {

                $existe = false;
                $objeto = new stdClass();

                foreach ($results as $key => $r) {
                    if ($fechas_generales->fechaRegistros == $r->fechaRegistros) {
                        $existe = true;
                        $objeto = $r;
                        break;
                    }
                }

                if ($existe) {
                    array_push($ejeY, $objeto->cantidadRegistros);
                } else {
                    array_push($ejeY, 0);
                }
            }

            $datos = new stdClass();

            $datos->diagnostico = $d->diagnostico;
            $datos->ejeY = $ejeY;

            array_push($array_datos, $datos);
        }

        return response()->json([
            'data' => [
                'data' => $array_datos,
                'axisX' => $axisX
            ],
            'HttpResponse' => [
                'ok' => true,
                'status' => 200,
            ]
        ]);
    }

    public function getHistorial_CantidadRegistrosDiarios()
    {
        $results = DB::select("SELECT DATE_FORMAT(fechaRegistro,'%d-%m') AS fechaRegistros, COUNT(u.idUsuario) AS cantidadRegistros
                        FROM Usuario u
                        WHERE DATE(u.fechaRegistro) >= (
                            SELECT DATE_FORMAT(u.fechaRegistro - INTERVAL 1 MONTH, '%Y-%m-%d')
                            FROM Usuario u 
                            ORDER BY u.fechaRegistro  DESC
                            LIMIT 1)
                        GROUP BY DATE(u.fechaRegistro)
                        ORDER BY u.fechaRegistro ASC");

        $axisX = array();
        $ejeY = array();

        foreach ($results as $registro) {
            array_push($axisX, $registro->fechaRegistros);
            array_push($ejeY, $registro->cantidadRegistros);
        }

        return response()->json([
            'data' => [
                'ejeY' => $ejeY,
                'axisX' => $axisX,
                // 'axisY' => $axisY
            ],
            'HttpResponse' => [
                'ok' => true,
                'status' => 200,
            ]
        ]);
    }
}

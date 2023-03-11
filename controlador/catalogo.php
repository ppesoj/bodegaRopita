<?php
    header('Content-Type: application/json');
    require "../database/conexion.php";
    require "./jwt_helper.php";

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    $recoger = "SELECT * FROM productos;";
    $nvConexion = nuevaConexion();
    $query_obtener = mysqli_query($nvConexion, $recoger);
    if (!$query_obtener) {
        $resultados["fallo"] = mysqli_error($nvConexion);
        $resultados["status"] = false;
        return print (json_encode($resultados));
    }
    if ( isset($query_obtener) ) {
        while ($fila = $query_obtener->fetch_assoc()) {
            $resultados["data"][]=$fila;
        }
        $resultados["status"] = true;
    } else {
        $resultados["status"] = false;
    }
    $nvConexion->close();
    return print (json_encode($resultados));

?>
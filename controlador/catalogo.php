<?php
    header('Content-Type: application/json');
    require "../database/conexion.php";
    require "./jwt_helper.php";

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    $accionCatalogo = $_POST["func"];


    if($accionCatalogo == "mostrarRopa") {

        $recoger = "SELECT foto, modelo, marcas.nombre AS marcaNombre, precioproductos.precioMenudeo, precioproductos.precioMayoreo, 1_2paquete, 1paquete, precioPromocion, dataQR  FROM productos
        LEFT JOIN marcas ON marcas.id = productos.id_marca
        LEFT JOIN precioproductos ON precioproductos.id_producto = productos.id;";

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
    }

    if($accionCatalogo == "buscarPrenda") {
        $codigo = $_POST["codigo"];
        $recoger = "SELECT foto, modelo, marcas.nombre AS marcaNombre, precioproductos.precioMenudeo, precioproductos.precioMayoreo, 1_2paquete, 1paquete, precioPromocion, dataQR  FROM productos
        LEFT JOIN marcas ON marcas.id = productos.id_marca
        LEFT JOIN precioproductos ON precioproductos.id_producto = productos.id 
        WHERE modelo = '".$codigo."';";
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
            $resu = false;
        }
        $nvConexion->close();
        return print (json_encode($resultados));
    }

?>
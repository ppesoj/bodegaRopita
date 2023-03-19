<?php
    header('Content-Type: application/json');
    require "../database/conexion.php";
    require "./jwt_helper.php";

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //check if its an ajax request, exit if not
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            //exit script outputting json data
            $output = json_encode( array( 'type' => 'error', 'text' => 'La solicitud debe provenir de Ajax'));
            die($output);
        }
        
        //recibimos los datos y no son vacios
        if (
                empty($_POST["func"]) ||
                empty($_POST["inputModelo"]) || 
                empty($_POST["inputPrecioRango1"]) 
                // ($_POST["portada"]) == "undefined"
            ) {
                $output = json_encode(array('type' => 'errorIncom', 'text' => 'Datos incompletos.'));
                die($output);
            }
    }

    if($_POST["func"] == "agregarProducto"){
        $resultados = ["status" => false];
        $idRegreso = 0;

        $inputModelo = $_POST["inputModelo"];
        $inputDescripcion = $_POST["inputDescripcion"];
        $inputPiezasPaquete = $_POST["inputPiezasPaquete"];
        $inputPrecioOferta_convert = $_POST["inputPrecioOferta-convert"];

        //consulta insertar la noticia
        $query_insertProduct = $conexion->query('INSERT INTO productos (id, modelo, codigo, descripcion, cantidad, foto, precio, precioOferta, piezasPaquete, created_at, updated_at, dataQR) VALUES (NULL, "'.$inputModelo.'", NULL, "'.$inputDescripcion.'", NULL, NULL, 0, '.$inputPrecioOferta_convert.', '.$inputPiezasPaquete.', NULL, NULL, NULL);');
        if (!$query_insertProduct) {
            printf("Error: %s\n", mysqli_error($conexion));
            echo "la db ha fallado :c";
            exit;
        }
        if ( isset($query_insertProduct) ) {
            $idRegreso = $conexion->insert_id;
            $resultados["idRegreso"] = $idRegreso;
            
            $resultados["status"] = true;
        } else {
            $resultados["status"] = false;
        }
        $conexion->close();
        
        return print (json_encode($resultados));
    }

?>
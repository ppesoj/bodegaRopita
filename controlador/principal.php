<?php
    header('Content-Type: application/json');
    require "../database/conexion.php";
    require "./jwt_helper.php";

    /* Autenticacion: session|db|token */
    define('__AUTH__', 'session');
    define('__SECRET_KEY__', 'asdawdsd8ws.6@');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //check if its an ajax request, exit if not
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            //exit script outputting json data
            $output = json_encode( array( 'type' => 'error', 'text' => 'Request must come from Ajax'));
            die($output);
        }

        if (empty($_POST["usuario"]) || empty($_POST["password"]) || empty($_POST["func"]) ) {
            $output = json_encode(array('type' => 'errorIncom', 'text' => 'Faltan datos >:V'));
            die($output);
        }

        $usuario = filter_var(trim($_POST["usuario"]), FILTER_SANITIZE_STRING);
        $password = filter_var(trim($_POST["password"]), FILTER_SANITIZE_EMAIL);
        $func = $_POST["func"];
        if($func == "login") {
            IniciarSesion($conexion, $usuario, $password);
        } else {
            $output = json_encode(array('type' => 'error', 'text' => 'Ocurrio un problema :,v'));
            die($output);
        }
    }

    function IniciarSesion ($conexion,$usuario,$pass) {
        $resultados = ["status" => false];
        $consultar = "SELECT status,id FROM `usuarios` WHERE `usuario` = '".$usuario."' AND `password` = '".$pass."'";
        $query = mysqli_query($conexion, $consultar);
        if (!$query) {
            echo "No pudo ejecutarse satisfactoriamente la consulta ($consultar) " .
                 "en la BD: " . mysql_error();
            exit;
        }
        if (mysqli_num_rows($query) == 0) {
            //"No se han encontrado filas, nada a imprimir, asi que voy a detenerme.";
            $resultados["data"]["login"] = 0;
            $resultados["status"] = false;
            // exit;
        }
        while ($fila = mysqli_fetch_assoc($query)) {
            $resultados["data"]["login"] = $fila["status"];
            $resultados["status"] = true;

            $token = array();
            $token["status"] = $fila["status"];
            $token["id"] = $fila["id"];
            $resultados["token"] = JWT::encode($token, 'secret_server_key');
        }
        
        return print (json_encode($resultados));
    }
    
?>
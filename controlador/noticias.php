<?php
    header('Content-Type: application/json');
    require "../database/conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //check if its an ajax request, exit if not
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            //exit script outputting json data
            $output = json_encode( array( 'type' => 'error', 'text' => 'La solicitud debe provenir de Ajax'));
            die($output);
        }
        
        //recibimos los datos y no son vacios
        if ( empty($_POST["func"])) {
            $output = json_encode(array('type' => 'errorIncom', 'text' => 'Datos incompletos pequeño fetito :,v'));
            die($output);
        }

        //llamada a la funcion
        $func = $_POST["func"];
        if($func == "mostrarNoticias") {
            mostrarNoticias();
        } else if($func == "imagenesNoticia") {
            $idu = $_POST["idU"];
            $idn = $_POST["idN"];
            imagenesNoticia($idn,$idu);
        } else {
            $output = json_encode(array('type' => 'error', 'text' => 'Ocurrio un problema con tus acciones >:v'));
            die($output);
        }
    }

    function mostrarNoticias() {
        $resultados = ["status" => false];

        $insertar = "SELECT id, txtMiniatura, nombrePortada, publicador FROM `noticias`;";

        $nvConexion = nuevaConexion();
        $query = mysqli_query($nvConexion, $insertar);
        if (!$query) {
            $resultados["noticias"]["pagina1"]["fallo"]= mysqli_error($nvConexion);
        }else {
            $resultados["status"] = true;
        }
        if ( isset($query) ) {

            while ($fila = $query->fetch_assoc()) {
                $resultados["data"][] = $fila;
            }

            if(!empty($resultados["data"])) {
                $resultados["noticias"]["pagina1"]["status"] = true;
            }else { $resultados["noticias"]["pagina1"]["status"] = false; }
        } else {
            $resultados["noticias"]["pagina1"]["status"] = false;
        }
        $nvConexion->close();
                    
        return print (json_encode($resultados));
    }

    function imagenesNoticia ($idn, $idu) {
        $resultados = ["status" => false];
        //imagenes de  la noticia
        $insertar = "SELECT nombre FROM `imagenes` WHERE idNoticia = $idn;";
        //$insertar = "SELECT img.nombre, noti.contenido, noti.titulo, noti.publicador FROM imagenes AS img JOIN noticias AS noti ON (img.idNoticia = noti.id) WHERE img.idNoticia = $idn;";

        $nvConexion = nuevaConexion();
        $query = mysqli_query($nvConexion, $insertar);
        if (!$query) {
            $resultados["imagenes"]["fallo"]= mysqli_error($nvConexion);
        }else {
            $resultados["status"] = true;
        }
        if ( isset($query) ) {

            while ($fila = $query->fetch_assoc()) {
                $resultados["data"][] = $fila;
            }

            $resultados["imagenes"]["status"] = true;
        } else {
            $resultados["imagenes"]["status"] = false;
        }
        $nvConexion->close();

        //autor de la noticia
        $insertar = "SELECT nombre,alias,imgPerfil FROM `usuarios` WHERE id = $idu;";

        $nvConexion = nuevaConexion();
        $query = mysqli_query($nvConexion, $insertar);
        if (!$query) {
            $resultados["publicador"]["fallo"]= mysqli_error($nvConexion);
        }else {
            $resultados["status"] = true;
        }
        if ( isset($query) ) {

            while ($fila = $query->fetch_assoc()) {
                $resultados["publicador"][] = $fila;
            }

            $resultados["publicador"]["status"] = true;
        } else {
            $resultados["publicador"]["status"] = false;
        }
        $nvConexion->close();

        //la noticia
        $insertar = "SELECT titulo, contenido FROM `noticias` WHERE id = $idn;";

        $nvConexion = nuevaConexion();
        $query = mysqli_query($nvConexion, $insertar);
        if (!$query) {
            $resultados["noticia"]["fallo"]= mysqli_error($nvConexion);
        }else {
            $resultados["status"] = true;
        }
        if ( isset($query) ) {

            while ($fila = $query->fetch_assoc()) {
                $resultados["noticia"][] = $fila;
            }

            $resultados["noticia"]["status"] = true;
        } else {
            $resultados["noticia"]["status"] = false;
        }
        $nvConexion->close();
                    
        return print (json_encode($resultados));
    }

?>
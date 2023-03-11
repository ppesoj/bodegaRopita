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

        //validacion de token
        $miToken = $_POST["token"];
        $resulToken = JWT::decode($_POST['token'], 'secret_server_key');
        if( isset($resulToken->error) ) {
            $output = ['type' => 'errorfatal', 'text' => 'Que andas haciendo? >:V'];
            return print (json_encode($output));
            exit;
        }
        //llamada a la funcion
        $func = $_POST["func"];
        if($func == "actualizarPerfil") {
            $nombre = $_POST["nombre"]; $alias = $_POST["alias"]; $id = $resulToken->id;
            actualizarPerfil($nombre, $alias, $id);
        } else if($func == "mostrarPerfil") {
            $id = $resulToken->id;
            mostrarPerfil($id);
        } else {
            $output = json_encode(array('type' => 'error', 'text' => 'Ocurrio un problema con tus acciones >:v'));
            die($output);
        }
    }

    function actualizarPerfil ($nombre, $alias, $id) {
        $ext_formatos = array("png","jpg","jpeg","webp");
        $p = $_POST["perfil"];
        if ( $_POST["perfil"] != "undefined" && !empty($p)  )  {//diferente de vacio :v
            //guardado de perfil
            $imgPorta = json_decode($_POST["perfil"]);
            $extensionPorta = explode('/', mime_content_type($imgPorta->result))[1];
            if(!in_array(strtolower($extensionPorta),$ext_formatos)) {
                $resultados["status"] = false;
                $resultados["images"] = "El tipo de imagen de portada no es correcto";
                return print (json_encode($resultados));
            } else {
                $imgPorta = str_replace('data:image/'.$extensionPorta.';base64,', '', $imgPorta->result);

                $imgPorta = str_replace(' ', '+', $imgPorta);
                //descodificar de base 64
                $dataPorta = base64_decode($imgPorta);
                $fileNamePorta = $id . ".png";
                $dir_to_save = "docs/perfiles/";
                $addPortada = $dir_to_save.$fileNamePorta;
                file_put_contents($addPortada,$dataPorta);

                if(!file_exists($addPortada)) {
                    $resultados["img"]["file"] = "No";
                }else {// si se agrego con exito la guardamos en la base de datos con su noticia asociada
                    $resultados["img"]["file"] = "Si";

                    $insertarPorta = "UPDATE usuarios SET imgPerfil = '$fileNamePorta' WHERE id = $id;";

                    $nvConexionPort = nuevaConexion();
                    $queryNoticiaPort = mysqli_query($nvConexionPort, $insertarPorta);
                    if (!$queryNoticiaPort) {
                        $resultados["img"]["fallo"] = mysqli_error($nvConexionPort);
                    }
                    if ( isset($queryNoticiaPort) ) {
                        $resultados["img"]["status"] = true;
                    } else {
                        $resultados["img"]["status"] = false;
                    }

                    $nvConexionPort->close();
                }
            }
        }
        $insertar = "UPDATE `usuarios` SET nombreCompleto = '$nombre', alias = '$alias' WHERE id = $id;";
        $nvConexion = nuevaConexion();
        $queryNoticia = mysqli_query($nvConexion, $insertar);
        if (!$queryNoticia) {
            $resultados["fallo"] = mysqli_error($nvConexion);
            $resultados["status"] = false;
            return print (json_encode($resultados));
        }
        if ( isset($queryNoticia) ) {
            $resultados["status"] = true;
        } else {
            $resultados["status"] = false;
        }
        $nvConexion->close();

        return print (json_encode($resultados));
    }//fin function

    function mostrarPerfil ($id) {
        $insertar = "SELECT nombreCompleto, alias, imgPerfil FROM usuarios WHERE id = $id;";
        $nvConexion = nuevaConexion();
        $queryNoticia = mysqli_query($nvConexion, $insertar);
        if (!$queryNoticia) {
            $resultados["perfil"]["fallo"] = mysqli_error($nvConexion);
            $resultados["status"] = false;
            return print (json_encode($resultados));
        }
        if ( isset($queryNoticia) ) {
            while ($fila = $queryNoticia->fetch_assoc()) {
                $resultados["data"][]=$fila;
            }
            $resultados["status"] = true;
        } else {
            $resultados["status"] = false;
        }
        $nvConexion->close();

        return print (json_encode($resultados));
    }
        
?>
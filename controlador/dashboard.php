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
        if (empty($_POST["titulo"]) || empty($_POST["noticia"]) || empty($_POST["func"]) || ($_POST["portada"]) == "undefined") {
            $output = json_encode(array('type' => 'errorIncom', 'text' => 'Datos incompletos pequeño fetito :,v'));
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
        if($func == "addNoticia") {
            $titulo = $_POST["titulo"]; $noticia = $_POST["noticia"]; $portada = $_POST["portada"]; $txtMiniatura = $_POST["txtMiniatura"];
            $id = $resulToken->id;
            InsertarNoticia($conexion, $titulo, $noticia, $portada, $txtMiniatura,$id);
        } else {
            $output = json_encode(array('type' => 'error', 'text' => 'Ocurrio un problema con tus acciones >:v'));
            die($output);
        }
    }

    function InsertarNoticia ($conexion,$titulo, $noticia, $portada, $txtMiniatura,$id) {
        $resultados = ["status" => false];
        $idNoticia = 0;

        //consulta insertar la noticia
        $query = $conexion->query("CALL InsertarNoticia('$titulo','$noticia','$txtMiniatura','$id')");
        if (!$query) {
            printf("Error: %s\n", mysqli_error($conexion));
            echo "la db ha fallado :c";
            exit;
        }
        if ( isset($query) ) {

            while ($fila = $query->fetch_assoc()) {
                $idNoticia = $fila["id"];
            }
            
            $resultados["status"] = true;
        } else {
            $resultados["status"] = false;
        }
        $conexion->close();

            $ext_formatos = array("png","jpg","jpeg","webp");

            //guardado de  portada
            $imgPorta = json_decode($_POST["portada"]);
            $extensionPorta = explode('/', mime_content_type($imgPorta->result))[1];
            if(!in_array(strtolower($extensionPorta),$ext_formatos)) {
                $resultados["status"] = false;
                $resultados["images"] = "El tipo de imagen de portada no es correcto";
                return print (json_encode($resultados));
            }else {
                $imgPorta = str_replace('data:image/'.$extensionPorta.';base64,', '', $imgPorta->result);

                $imgPorta = str_replace(' ', '+', $imgPorta);
                //descodificar de base 64
                $dataPorta = base64_decode($imgPorta);
                $fileNamePorta = $idNoticia . '-portada.' . $extensionPorta;
                $dir_to_save = "docs/noticias/";
                $addPortada = $dir_to_save.$fileNamePorta;
                file_put_contents($addPortada,$dataPorta);

                if(!file_exists($addPortada)) {
                    $resultados["images"]["portada"]["file"] = "No";
                }else {// si se agrego con exito la guardamos en la base de datos con su noticia asociada
                    $resultados["images"]["portada"]["file"] = "Si";
    
                    $insertarPorta = "UPDATE noticias SET nombrePortada = '$fileNamePorta' WHERE id = $idNoticia;";
    
                    $nvConexionPort = nuevaConexion();
                    $queryNoticiaPort = mysqli_query($nvConexionPort, $insertarPorta);
                    if (!$queryNoticiaPort) {
                        $resultados["images"]["portada"]["fallo"] = mysqli_error($nvConexionPort);
                    }
                    if ( isset($queryNoticiaPort) ) {
                        $resultados["images"]["portada"]["status"] = true;
                    } else {
                        $resultados["images"]["portada"]["status"] = false;
                    }

                    $nvConexionPort->close();
                }

            }

            
            //guardado de imagenes del carrucel
            $arrayImgs = json_decode($_POST['imgs']);
            foreach ($arrayImgs as $clave => $valor) {
                $img = $valor->result;
                $extension = explode('/', mime_content_type($img))[1];
                if(!in_array(strtolower($extension),$ext_formatos)) {
                    // print_r("$extension no paso");
                    $resultados["status"] = false;
                    $resultados["images"] = "Los tipos de imagenes no son correctos";
                    return print (json_encode($resultados));
                }
            }

            foreach ($arrayImgs as $clave => $valor) {
                $img = $valor->result;
                $extension = explode('/', mime_content_type($img))[1];
                $img = str_replace('data:image/'.$extension.';base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                //descodificar de base 64
                $data = base64_decode($img);
                //nombre de la imagen
                //$fileName = uniqid() . '.png';
                $fileName = $idNoticia."-".uniqid() . '.' . $extension;

                //direccion de guardado
                $dir_to_save = "docs/noticias/";

                if(!file_exists($dir_to_save)) {
                    mkdir($dir_to_save,755,true);
                }
                
                $add = $dir_to_save.$fileName;
                file_put_contents($add,$data);

                if(!file_exists($add)) {
                    $resultados["images"][$fileName]["file"] = "No";
                }else {// si se agrego con exito la guardamos en la base de datos con su noticia asociada
                    $resultados["images"][$fileName]["file"] = "Si";

                    $insertar = "INSERT INTO `imagenes`(`nombre`, `idNoticia`) VALUES ('".$fileName."','".$idNoticia."')";

                    $nvConexion = nuevaConexion();
                    $queryNoticia = mysqli_query($nvConexion, $insertar);
                    if (!$queryNoticia) {
                        $resultados["images"][$fileName]["fallo"] = mysqli_error($nvConexion);
                    }
                    if ( isset($queryNoticia) ) {
                        $resultados["images"][$fileName]["status"] = true;
                    } else {
                        $resultados["images"][$fileName]["status"] = false;
                    }
                    $nvConexion->close();

                }

            }
        
        return print (json_encode($resultados));
    }
        
?>
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

        $inputPrecioRango1_convert = $_POST["inputPrecioRango1-convert"];
        // $inputPrecioRango2_convert = $_POST["inputPrecioRango2-convert"];
        $inputPrecioRango3_convert = $_POST["inputPrecioRango3-convert"];
        $inputPrecioRango4_convert = $_POST["inputPrecioRango4-convert"];
        $inputPrecioRango5_convert = $_POST["inputPrecioRango5-convert"];

        //agrego la marca primero
        $idRegreso_marca = NULL;
        if (!empty($_POST['inputMarca'])) {
            $inputMarca = $_POST['inputMarca'];
            $query_insertMarca = $conexion->query("INSERT INTO marcas (id, nombre, logo, origen, created_at, updated_at ) VALUES (NULL, '$inputMarca', NULL, '', NULL, NULL);");
            $idRegreso_marca = $conexion->insert_id;
        }else {
            
        }

        //consulta insertar el producto
        $query_insertProduct = $conexion->query('INSERT INTO productos (id, modelo, codigo, descripcion, cantidad, foto, id_marca, precio, precioMayoreo, piezasPaquete, created_at, updated_at, dataQR) VALUES (NULL, "'.$inputModelo.'", NULL, "'.$inputDescripcion.'", NULL, NULL, '.$idRegreso_marca.', 0, '.$inputPrecioOferta_convert.', '.$inputPiezasPaquete.', NULL, NULL, NULL);');
        if (!$query_insertProduct) {
            printf("Error: %s\n", mysqli_error($conexion));
            echo "la db ha fallado :c";
            exit;
        }
        
        //si el producto fue insertado
        if ( isset($query_insertProduct) ) {
            $idRegreso = $conexion->insert_id; //el id nuevo que retorna este nuevo registro
            $resultados["idRegreso"] = $idRegreso;


            //inserto sus precios en la tabla de precios
            $query_insertPreciosRangos = $conexion->query("INSERT INTO precioproductos (id, id_producto, precioMenudeo, precioMayoreo, 1_2paquete, 1paquete, 2paquetes, precioPromocion, inicioPromocion, finPromocion, created_at, updated_at ) 
            VALUES (NULL, '$idRegreso', $inputPrecioRango1_convert, $inputPrecioOferta_convert, $inputPrecioRango3_convert, $inputPrecioRango4_convert, $inputPrecioRango5_convert, 0, NULL, NULL, NULL ,NULL );");


            //guardando en la db la imagen
            $data = $_POST["imgQRN"];
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
                        
            $nombreImg = 'bodegaRopa'.$_POST["imgQR_name"].'_'.getRandomString(7).'.jpg';
            $direccionGuardar = '../docs/codigosQR/ropa_productos/'.$nombreImg;
            file_put_contents ( $direccionGuardar, $data);

            //verificamos si el archivo existe en la carpeta, osea si se guardo ps
            if(!file_exists($direccionGuardar)) {
                $resultados["images"]["qr"]["file"] = "No guardado / error";
            }else {
                $resultados["images"]["qr"]["file"] = "si guardado / exitoso";
                $insertarImgDb = $conexion->query("UPDATE productos SET dataQR = '".$nombreImg."' WHERE id = ".$idRegreso.";");
    
                    
                    if (!$insertarImgDb) {
                        $resultados["images"]["qr"]["fallo"] = mysqli_error($nvConexionPort);
                    }
                    if ( isset($insertarImgDb) ) {
                        $resultados["images"]["qr"]["status"] = true;

                            //guardamos ahora la imagen de la ropa
                            $ext_formatos = array("png","jpg","jpeg","webp");
                            //guardado de  portada
                            $imgRopaPrenda = json_decode($_POST["portada"]);
                            $extensionPorta = explode('/', mime_content_type($imgRopaPrenda->result))[1];
                            if(!in_array(strtolower($extensionPorta),$ext_formatos)) {
                                $resultados["status"] = false;
                                $resultados["images"] = "El tipo de imagen de portada no es correcto";
                                return print (json_encode($resultados));
                            }else {
                                $imgRopaPrenda = str_replace('data:image/'.$extensionPorta.';base64,', '', $imgRopaPrenda->result);
                                $imgRopaPrenda = str_replace(' ', '+', $imgRopaPrenda);
                                $dataPrenda = base64_decode($imgRopaPrenda);
                                $fileNamePrenda = 'productoPrenda-'.$inputModelo.'-'.getRandomString(7).'.'.$extensionPorta;
                                $dir_to_save_prenda = "../docs/ropa/productos/";
                                $addPrenda = $dir_to_save_prenda.$fileNamePrenda;
                                file_put_contents($addPrenda,$dataPrenda);
                                //revizamos si se guardo
                                if(!file_exists($addPrenda)) {
                                    $resultados["images"]["prendaImg"]["file"] = "No";
                                }else {// si se agrego con exito la guardamos en la base de datos con su noticia asociada
                                    $resultados["images"]["prendaImg"]["file"] = "Si";
                    
                                    $insertarPrenda = "UPDATE productos SET foto = '".$fileNamePrenda."' WHERE id = ".$idRegreso.";";
                    
                                    $nvConexionPort = nuevaConexion();
                                    $queryNoticiaPrenda = mysqli_query($nvConexionPort, $insertarPrenda);
                                    if (!$queryNoticiaPrenda) {
                                        $resultados["images"]["portada"]["fallo"] = mysqli_error($nvConexionPort);
                                    }
                                    if ( isset($queryNoticiaPrenda) ) {
                                        $resultados["images"]["portada"]["status"] = true;
                                    } else {
                                        $resultados["images"]["portada"]["status"] = false;
                                    }
                
                                }
                            }
                        

                    } else {
                        $resultados["images"]["qr"]["status"] = false;
                    }
            }

            $resultados["status"] = true;
        } else {
            $resultados["status"] = false;
        }
        $conexion->close();
        
        return print (json_encode($resultados));
    }

    function getRandomString($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

?>
<?php
    //guardando en la db la imagen
    $data = $_POST["imgQRN"];
    echo($data);
    list($type, $data) = explode(';', $data);
    list(, $data)      = explode(',', $data);
    $data = base64_decode($data);
                
    $direccionGuardar = '../docs/codigosQR/ropa_productos_impresiones/qr.jpg';
    file_put_contents ( $direccionGuardar, $data);
?>
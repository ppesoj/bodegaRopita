<?php
    $conexion = "";
        $user = "root";
        $pass = "password";
        $server = "localhost";
        $db = "bodega";
        
        $conexion = new mysqli($server,$user,$pass,$db);
        if($conexion->connect_errno) {
            die ("La conexion ha sido rechazada por el servidor ".$conexion->connect_errno);
        }else {  }

        function nuevaConexion () {
            global $user;
            global $pass;
            global $server;
            global $db;
            $nvConexion = new mysqli($server,$user,$pass,$db);
            return $nvConexion;
        }

?>
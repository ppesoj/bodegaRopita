<?php
    $mysqli = new mysqli("localhost","root","password","bodega");

    $salida ="";
    $query = "SELECT * FROM productos ORDER BY modelo";

    if(isset($_POST['consulta'])){
        $q = $mysqli->real_escape_string($_POST['consulta']);
        $query = "SELECT foto, modelo, codigo, descripcion, precio, precioOferta FROM productos
        WHERE modelo LIKE '%".$q."%' OR codigo LIKE '%".$q."%' OR modelo LIKE '%".$q."%'";
    }

    $resultado = $mysqli->query($query);

    if($resultado->num_rows > 0){
        $salida.="<table class='tabla_datos'>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Número de modelo</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Precio de oferta</th>
                </tr>
            </thead>
            <tbody>";
        
        while($fila = $resultado->fetch_assoc()){
            $salida.="<tr>
                <td>".$fila['modelo']."</td>
                <td>".$fila['codigo']."</td>
                <td>".$fila['descripcion']."</td>
                <td>".$fila['precio']."</td>
                <td>".$fila['precioOferta']."</td>
        </tr>";
        }
        $salida.="</tbody></table>";
    }
    else{
        $salida.="No hay datos";
    }

    echo $salida;

    $mysqli->close();
?>
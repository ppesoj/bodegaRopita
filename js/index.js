$(document).ready(function(){
    mostrarRopa();
});

function mostrarRopa () {
    console.log("mostrando ropa");
    var miData = new FormData();
    miData.append("func","mostrarRopa");
    $.ajax({
        url: "controlador/catalogo.php",
        type: "POST",

        contentType: false,
        data: miData,
        processData: false,
        cache: false,
        success: function (respuesta) {
            if(respuesta["status"]) {
                console.log(respuesta)

                //insertar la ropita a la tabla               
                    var objNoticias = respuesta;
                    Object.keys(objNoticias.data).forEach((key, index) => {
                        //console.log(objNoticias.data[index]);
                        var miniatura = `
                            <tr>
                                <td>
                                    <img src="controlador/docs/ropa/${objNoticias.data[index].foto}" style="width: 8rem;" >
                                </td>
                                <td>${objNoticias.data[index].modelo}</td>
                                <td>${objNoticias.data[index].codigo}</td>
                                <td>${objNoticias.data[index].descripcion}</td>
                                <td>${objNoticias.data[index].precio}</td>
                                <td>${objNoticias.data[index].precioOferta}</td>
                            </tr>
                        `;
                        $("#cuerpoCatalogo").append(miniatura);
                    });


            }else {
                console.log("Hubo un error en el servidor");
            }
        },
        error: function (e) {
            console.log("ERROR : ", e);
        }
    });
}

$(buscar_datos());
function buscar_datos(consulta){
    $.ajax({
        url: 'controlador/buscar.php',
        type: 'POST',
        dataType: 'html',
        data: {consulta: consulta},
    })
    .done(function(respuesta){
        $("#datos").html(respuesta);
    })
    .fail(function(){
        console.log("error");
    })
}
$(document).on('keyup','#caja_busqueda', function(){
    var valor = $(this).val();
    if(valor != ""){
        buscar_datos(valor);
    } else{
        buscar_datos();
    }
});
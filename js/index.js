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
                                    <img src="controlador/docs/ropa/${objNoticias.data[index].foto}" style="    width: 8rem;" >
                                </td>
                                <td>${objNoticias.data[index].modelo}</td>
                                <td>${objNoticias.data[index].codigo}</td>
                                <td>${objNoticias.data[index].descripcion}</td>
                                <td>${objNoticias.data[index].precio}</td>
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
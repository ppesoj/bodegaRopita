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
                    const dataSet = [];
                    Object.keys(objNoticias.data).forEach((key, index) => {
                        dataSet.push(Object.values(objNoticias.data[index]));
                    });

                    console.log(dataSet);
                    $('#myTable').DataTable( {
                        data: dataSet,
                        columnDefs: [{
                            "targets": 0,
                            "render": function (data, type, row) {
                                var checkbox = '<img src="controlador/docs/ropa/'+data+'" style="width: 10em;">';
                                return checkbox;
                            }
                        }],
                    } );


            }else {
                console.log("Hubo un error en el servidor");
            }
        },
        error: function (e) {
            console.log("ERROR : ", e);
        }
    });
}

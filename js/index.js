var contador = 0;

$(document).ready(function(){
    mostrarRopa();
});

function mostrarRopa () {
    //color de filas
    const celda = document.getElementById("bodyTable");
    contador++;
    if((contador%2) == 0){  
        a.style.backgroundColor = "#FFFFFF";
    }
    else{
        //a[this].style.backgroundColor = "#FFFFFF";
    }

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
                        language: {
                            searchPlaceholder: 'Buscar...',
                            search: '',
                            lengthMenu: 'Mostrar _MENU_ registros por página',
                            zeroRecords: 'No se encontró nada similar :(',
                            info: 'Mostrando página _PAGE_ de _PAGES_',
                            infoEmpty: 'No se encontraron datos',
                            infoFiltered: '(Ordenar de _MAX_ total de productos)',
                            "paginate":{
                                "previous": "anterior",
                                "next": "siguiente",
                            },
                        },
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

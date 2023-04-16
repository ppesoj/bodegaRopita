var contador = 0;

$(document).ready(function(){
    mostrarRopa();
});

$(document).on("click", ".btnQr", function(e) {
    e.preventDefault();
    urlImg = $(this).data("nombreimagen");
    rutaImg = "./docs/codigosQR/ropa_productos/"+urlImg;
    $.confirm({
        boxWidth: '30%',
        title: 'codigo QR',
        content: `
            <div class="row">
                <div class="col-12">
                    <label>Elige una accion;</label>
                </div>
                <div class="col-12">
                    <img id="fotoQR" src="./docs/codigosQR/ropa_productos/${urlImg}"
                </div>
            </div>
        `,
        type: 'dark',
        typeAnimated: true,
        buttons: {
            imprimir: function(button) {
                mandarImprimir(urlImg)
            },
            close: function () {
            }
        }
    });
})

function clonarFoto(){
    // get the image element
    const originalImage = document.getElementById("fotoQR");
    // create a canvas element
    const canvas = document.createElement('canvas');
    canvas.width = originalImage.width*1.6;
    canvas.height = originalImage.height*1.6;
    // get the canvas context
    const context = canvas.getContext('2d');
    // draw the original image onto the canvas
    context.drawImage(originalImage, 0, 0);
    // create a new image element from the canvas AQUII
    const newImage = new Image();
    newImage.src = canvas.toDataURL('image/png');

    let url = canvas.toDataURL('image/png');
    var miData = new FormData();
    miData.append("imgQRN", url);

    $.ajax({
        url: "controlador/clonar.php",
        type: "POST",
        contentType: false,
        processData: false,
        cache: false,
        data: miData,
    })
}

function mandarImprimir() {
    clonarFoto();
    // './docs/codigosQR/ropa_productos/qr.jpg'
    console.log(rutaImg)
    
    printJS({
        printable: ['./docs/codigosQR/ropa_productos_impresiones/qr.jpg',
        './docs/codigosQR/ropa_productos_impresiones/qr.jpg',
        './docs/codigosQR/ropa_productos_impresiones/qr.jpg'],
        type: 'image',
        imageStyle: 'width:100%;margin-bottom:10px;'
    })
    console.log("imprimiendo la imagen ./docs/codigosQR/ropa_productos/"+urlImg)
}

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
                            responsive: true,
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
                        columnDefs: [
                            {
                                "targets": 0,
                                "render": function (data, type, row) {
                                    var checkbox = '<img class="tableFoto" src="docs/ropa/productos/'+data+'">';
                                    return checkbox;
                                }
                            },
                            {
                                "targets": 11,
                                "render": function (data, type, row) {
                                    var checkbox = '<button type="button" id="btnQR" data-nombreImagen='+data+'" class="btnQr btn btn-primary btn-sm"><i class="fa-solid fa-print"></i>Imprimir</button>';
                                    return checkbox;
                                }
                            }
                        ],
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

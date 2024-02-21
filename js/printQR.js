$(document).on("click",".bodyTable #btnImprimir", function (e) {
    var midata = $(this).data("midata")
    dato = midata[5];
    console.log(dato);
    alert(dato, midata);
    printJS({
        printable: ['vistas/qr.jpg', 'vistas/qr.jpg', 'vistas/qr.jpg'],
        type: 'image',
        imageStyle: 'width:100%;margin-bottom:10px;'
    })
})
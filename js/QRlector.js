
    var video = document.createElement("video");
    var canvasElement = document.getElementById("canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    var outputData = document.getElementById("outputData");
    var precioOferta = document.getElementById("precioOferta");
    var precioRango1 = document.getElementById("precioRango1");
    var imagenResultado = document.getElementById("imagenResultado");

    function drawLine(begin, end, color) {
      canvas.beginPath();
      canvas.moveTo(begin.x, begin.y);
      canvas.lineTo(end.x, end.y);
      canvas.lineWidth = 4;
      canvas.strokeStyle = color;
      canvas.stroke();
    }

    // Use facingMode: environment to attemt to get the front camera on phones
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
      video.play();
      requestAnimationFrame(tick);
    });

    function tick() {
      loadingMessage.innerText = "⌛ Loading video..."
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loadingMessage.hidden = true;
        canvasElement.hidden = false;
        outputContainer.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });
        if (code) {
          drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
          drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
          drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
          drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
          buscarPrenda(code.data);
        } else {
        //   outputMessage.hidden = false;
        //   outputData.parentElement.hidden = true;
        }
      }
      requestAnimationFrame(tick);
    }

    function buscarPrenda (codigo) {
        var miData = new FormData();
        miData.append("func","buscarPrenda");
        miData.append("codigo",codigo);
        $.ajax({
            url: "../controlador/catalogo.php",
            type: "POST",

            contentType: false,
            data: miData,
            processData: false,
            cache: false,
            success: function (respuesta) {
                if(respuesta["status"]) {
                    outputMessage.hidden = true;
                    outputData.parentElement.hidden = false;
                    outputData.innerText = codigo;
                    precioOferta.parentElement.hidden = false;
                    precioOferta.innerText = respuesta.data[0].precioOferta;
                    precioRango1.parentElement.hidden = false;
                    precioRango1.innerText = respuesta.data[0].precio;
                    imagenResultado.setAttribute("src", "../docs/ropa/productos/"+respuesta.data[0].foto);
                }else {
                    console.log("Hubo un error en el servidor");
                }
            },
            error: function (e) {
                console.log("ERROR : ", e);
            }
        });
    }
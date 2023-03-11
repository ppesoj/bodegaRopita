import jsQR from "jsqr";
class QRReader {
/*
    isCamReady: Una booleana para determinar si la cámara se ha iniciado por primera vez.
    isCamOpen: Otra booleana para controlar si la cámara está encendida o apagada.
    stream: El flujo de datos extraídos directamente de la cámara.
    rafID: El identificador de iteración de “requestAnimationFrame”. Usaremos esta API HTML5 para ejecutar el código 
    de escaneo de forma recurrente.
    camCanvas: Guardamos una referencia al canvas pasado como parámetro.
    qrDataContainer: También almacenamos la referencia al contenedor donde mostrar el resultado del código encontrado.
    camCanvasCtx: Obtenenos el contexto “2d” del canvas. Será necesario, para pintar cada fotograma de la cámara en el 
    área definida por el canvas.
    video: Creamos un elemento video. En una primera instancia, el “stream” obtenido de la cámara se plasmará en un vídeo, para posteriormente re-pintarlo en el canvas. Para evitar que el usuario vea esta etiqueta intermedia, la agregamos con unos estilos adicionales, y el atributo “playsinline
*/
    constructor(canvasVideoElement, qrDataContainerElement) {
        this.isCamReady = false;
        this.isCamOpen = false;
        this.stream = null;
        this.rafID = null;
        this.camCanvas = canvasVideoElement;
        this.qrDataContainer = qrDataContainerElement;
        this.camCanvasCtx = this.camCanvas.getContext("2d", {
        willReadFrequently: true,
        });
        this.video = document.createElement("video");
        this.video.classList.add("video-cam");
        this.video.setAttribute("playsinline", true);
        document.body.appendChild(this.video);
    }

    getIsCamOpen() {
        return this.isCamOpen;
    }
}
  
export default QRReader;
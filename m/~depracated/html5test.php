<!DOCTYPE HTML>
<html>
  <head>
    <script>
var surface;
var spin;
var angle = 0;

function drawCanvas() {
    // GET ELEMENT
    surface = document.getElementById("myCanvas");

    if (surface.getContext) {
        // LOAD IMG
        spin = new Image();
        spin.onload = loadingComplete;
        spin.src = "img/spinwheel.png";
    }
}

function loadingComplete(e) {
    // WHEN LOADED SET LOOP
    setInterval(loop, 5);
}

function loop() {
    // GRAN CONTEXT + LOOP IMG
    var surfaceContext = surface.getContext('2d');

    // SET CANVAS TO BACKGROUND COLOR
    surfaceContext.fillStyle = "rgb(224,228,204)";
    surfaceContext.fillRect(0, 0, surface.width, surface.height);

    // DERP! SAVE CONTEXT 
    surfaceContext.save();
    // TRANSLATE TO CENTER
    surfaceContext.translate(spin.width * 0.5, spin.height * 0.5);
    // ROTATE
    surfaceContext.rotate(DegToRad(angle));
    // TRANSLATE - TOP LEFT
    surfaceContext.translate(-spin.width * 0.5, -spin.height * 0.5);
    // DRAW THE SPINNER
    surfaceContext.drawImage(spin, 0, 0);
    // LOOP AGAIN
    surfaceContext.restore();

 	// INCREMENT L
    angle++;
}

function DegToRad(d) {
    // CONVERT DEGS TO RADS YO.
    return d * 0.0174532925199432957;
}
    </script>
  </head>
  
  
<body onload="drawCanvas();">
<a href="roulette.php">
    <div>
    
        <canvas id="myCanvas" width="300" height="300">
            <p>No dice kiddo, can't render HTML5.</p>
        </canvas>
        
    </div>
    
</a>
</body>
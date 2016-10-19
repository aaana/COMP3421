var canvas = document.getElementById('canvas'),
 context = canvas.getContext('2d');
context.lineWidth = 30;
context.font = '24px Helvetica';
context.fillText('Click anywhere to erase', 175, 40);
// MISSING LINES
context.fillRect(40,50,150,100);


context.canvas.onmousedown = function (e) {
 // MISSING LINES
 context.clearRect(40,50,150,100);
}; 
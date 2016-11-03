    var n_sec;  //秒
    var n_min;  //分
    var n_hour; //时
    var ele_timer;
    var isBounced;

    function timeStr(h,m,s){

        var str_hour = h;
        var str_min = m;
        var str_sec = s;
        if ( n_hour < 10 ) {
            str_hour = "0" + n_hour;
        }
        if ( n_min < 10 ) {
            str_min = "0" + n_min;
        }
        if ( n_sec < 10) {
            str_sec = "0" + n_sec;
        }
        var time = str_hour + ":" + str_min + ":" + str_sec;    
        return time;    
    }
    
    function timer() {
        
        return setInterval(function () {
            ele_timer = timeStr(n_hour,n_min,n_sec);
            if(n_hour==n_min&&n_min==n_sec&&n_sec==0){
                alert("TIME OUT");
                clearInterval(n_timer);
                document.location.reload();
            }
            n_sec--;
            if (n_sec < 0){
                n_sec = 59;
                n_min--;
            }
            if (n_min < 0) {
                n_min = 59;
                n_sec = 0;
                n_hour--;
            }

       }, 1000);
    }
    var brickRowCounts = [4,5,6,7,8,9];
    var brickColumnCounts = [8,8,9,9,10,11];

    var n_timer;
    var canvas = document.getElementById("myCanvas");
    var ctx = canvas.getContext("2d");
    var ballRadius;
    var x;
    var y;
    var dx;
    var dy;
    var paddleHeight;
    var paddleWidth;
    var paddleXs;
    var paddlePadding;
    var rightPressed;
    var leftPressed;
    var brickRowCount;
    var brickColumnCount;
    var brickWidth;
    var brickHeight;
    var brickPadding;
    var brickOffsetTop;
    var brickOffsetLeft;
    var score;
    var lives;
    var bricks = [];
    var brickColors;
    //0:初次开始 1:stop 2:resume -1: init
    var statusFlag = -1;
    var level;
    var speed;
    var globalAnimationID;
    var countdown;
    var orient = "portrait";
    // for(r=0; r<brickRowCount; r++) {
    //     bricks[r] = [];
    //     for(c=0; c<brickColomnCount; c++) {
    //         bricks[r][c] = { x: 0, y: 0, status: 1 };
    //     }
    // }

    function doOnOrientationChange(){
        orient = orient == "landscape" ? 'portrait' : 'landscape';
        resize();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBricks();
        drawBall();
        drawPaddle();
        drawScore();
        drawLives();
        drawTimer();
    }

    window.addEventListener("orientationchange", doOnOrientationChange,false);
    
    document.addEventListener("mousemove", mouseMoveHandler, false);

    document.addEventListener("touchmove", touchMoveHandler, false);
    // missing code here for keyevents

    document.addEventListener("keydown",keyDownHandler,false);
    document.addEventListener("keyup",keyUpHandler,false);

    function keyDownHandler(e) {
        // missing code here
        if(e.keyCode == 39){
            rightPressed = true;
            console.log("rightPressed");
        }else if (e.keyCode == 37) {
            leftPressed = true;
            console.log("leftPressed");
        }else if(e.keyCode == 65){
            aPressed = true;
            console.log("A pressed");
        }else if(e.keyCode == 68){
            dPressed = true;
            console.log("D pressed");
        }else if(e.keyCode == 32){     // It is a trick to prevent the about the modal window poping up 
            document.getElementById("about").blur();
            document.getElementById("special").blur();
            document.getElementById("levelSelect").blur();
            document.getElementById("speedSelect").blur();
            document.getElementById("paddleSelect").blur();

        }  
    }
    function keyUpHandler(e) {
        // missing code here
        if(e.keyCode == 39){
            rightPressed = false;
        }else if (e.keyCode == 37) {
            leftPressed = false;
        }else if (e.keyCode == 65) {
            aPressed = false;
        }else if (e.keyCode == 68) {
            dPressed = false;
        }else if(e.keyCode == 32){          
            start();
        }  
    }
    function mouseMoveHandler(e) {
        var relativeX = e.clientX - canvas.offsetLeft;
        if(relativeX > 0 && relativeX < canvas.width) {
            paddleXs[0] = relativeX - paddleWidth/2;
            // console.log(paddleX);
        }
    }
    function touchMoveHandler(e) {
        event.preventDefault();
        if (event.targetTouches.length == 1) {
            var touch = e.targetTouches[0];
            var relativeX = touch.pageX - canvas.offsetLeft;
            if(relativeX > 0 && relativeX < canvas.width) {
                paddleXs[0] = relativeX - paddleWidth/2;
            // console.log(paddleX);
            }
        }
    }

    function clearInput(self){
        self.value ="";
    }

    function start(){
        var startButton = document.getElementById("startButton");
        if(statusFlag==0){
            startButton.value = "Stop";
            n_timer = timer();
            statusFlag = 2;
            globalAnimationID = requestAnimationFrame(draw);

        }else if(statusFlag==1){//stop -> Resume
            startButton.value = "Stop";
            n_timer = timer();
            statusFlag = 2;
            globalAnimationID = requestAnimationFrame(draw);
        }else{ //resume -> stop
            startButton.value = "Resume";
            clearInterval(n_timer);
            statusFlag = 1;
            cancelAnimationFrame(globalAnimationID);
        }
    }

	function isWin(){
		for(r=0; r<brickRowCount; r++) {
            for(c=0; c<brickColumnCount; c++) {
            	var b = bricks[r][c];
                if(b.status == 1)
                	return false;
            }
        }
        return true;
	}

    function collisionDetection() {
        for(var r=0; r<brickRowCount; r++) {
            for(var c=0; c<brickColumnCount; c++) {
                var b = bricks[r][c];
                if(b.status == 1) {
                    // if(x > b.x && x < b.x+brickWidth && y > b.y && y < b.y+brickHeight) {
                    //     dy = -dy;
                    //     b.status = 0;
                    //     score++;
                    //     if(score == brickRowCount*brickColumnCount) {
                    //         alert("YOU WIN, CONGRATS!");
                    //         document.location.reload();
                    //     }                     
                    // } 
                    if(x > b.x - ballRadius && x < b.x+brickWidth+ballRadius && y > b.y-ballRadius && y < b.y+brickHeight + ballRadius) {
                        dy = -dy;
                        if(b.hard == 0){
                            b.status = 0;
                        }else{
                            b.hard --;
                            console.log(b.hard +" " + b.status);
                        }
                        score ++;
                        if(isWin()) {
                            alert("YOU WIN, CONGRATS!");
                            document.location.reload();
                        }                     
                    }                
                }             
            }         
        }
    }
    function drawBall() {
        ctx.beginPath();
        ctx.arc(x, y, ballRadius, 0, Math.PI*2);
        ctx.fillStyle = "#FF8C00";
        ctx.fill();
        ctx.closePath();
    }
    function drawPaddle() {
        for(var index in paddleXs){
            ctx.beginPath();
            ctx.rect(paddleXs[index], canvas.height-paddleHeight, paddleWidth, paddleHeight);
            ctx.fillStyle = "#0095DD";
            if(index == 1)
                ctx.fillStyle = "#FF8C00";
            ctx.fill();
            ctx.closePath(); 
        }
    }
    function drawBricks() {

        for(r=0; r<brickRowCount; r++) {
            for(c=0; c<brickColumnCount; c++) {
                if(bricks[r][c].status == 1) {
                    var brickX = (c*(brickWidth+brickPadding))+brickOffsetLeft;
                    var brickY = (r*(brickHeight+brickPadding))+brickOffsetTop;
                    if(brickY + brickHeight <= canvas.height){
                    	bricks[r][c].x = brickX;
                    	bricks[r][c].y = brickY;
                    	ctx.beginPath();
                    	ctx.rect(brickX, brickY, brickWidth, brickHeight);
                    	ctx.fillStyle = brickColors[bricks[r][c].hard];
                    	ctx.fill();
                    	ctx.closePath();
                    }else{
                    	continue;
                    }
                }
            }
        }
    }
    function drawScore() {
        ctx.font = "16px Arial";
        ctx.fillStyle = "#0095DD";
        ctx.fillText("Score: "+score, 8, 20);
    }
    function drawLives() {
        ctx.font = "16px Arial";
        ctx.fillStyle = "#0095DD";
        ctx.fillText("Lives: "+lives, canvas.width-65, 20);
    }
    function drawTimer() {
        ctx.font = "16px Helvetica";
        if(n_hour ==0 &&n_min==0 && n_sec<10){
            ctx.fillStyle = "red";
            countdown.play();
        }else{
            ctx.fillStyle = "#0095DD";

        }
        ctx.fillText(ele_timer, canvas.width/2-30, 20);
    }

    function resize(){
        x = x/canvas.width;
        y = y/canvas.height;
        for(paddleX in paddleXs){
            paddleXs[paddleX] = (paddleXs[paddleX] + paddleWidth/2)/ canvas.width; 
        }
        if(orient == "portrait"){
            document.getElementById("myCanvas").width = screen.availWidth - 20;
            var tempHeight = brickOffsetTop + brickRowCount*brickHeight +(brickRowCount-1)*brickPadding + ballRadius*2 +paddleHeight +80;
            document.getElementById("myCanvas").height = tempHeight+160 < screen.availHeight?tempHeight:screen.availHeight-160 ;
            brickWidth = (canvas.width-brickOffsetLeft)/brickColumnCount - brickPadding;
        }else{
            document.getElementById("myCanvas").width = screen.availHeight - 20;
            var tempHeight = brickOffsetTop + brickRowCount*brickHeight +(brickRowCount-1)*brickPadding + ballRadius*2 +paddleHeight +80;
            document.getElementById("myCanvas").height = tempHeight+160 < screen.availWidth?tempHeight:screen.availWidth-160 ;
            brickWidth = (canvas.width-brickOffsetLeft)/brickColumnCount - brickPadding;
        }
        y = y * canvas.height;
        x = x * canvas.width;
        for(paddleX in paddleXs){
            paddleXs[paddleX] =paddleXs[paddleX] * canvas.width - paddleWidth/2; 
        }
 
    }

    function replay(){
        statusFlag = -1;
        init();
    }

    function init(){
        console.log(orient);
        brickColors = ["#CEECF5","#00BFFF","#0095DD"];
        paddlePadding = 100;
        ballRadius = parseInt(document.getElementById("ballSelect").value)*5;
        level = document.getElementById("levelSelect").value;
        speed = document.getElementById("speedSelect").value;
        paddleNum = document.getElementById("paddleSelect").value;
        dx = 1.5*speed;
        dy = -1.5*speed;
        paddleHeight = 10;
        paddleWidth = 75;
        rightPressed = false;
        leftPressed = false;
        aPressed = false;
        dPressed = false;
        brickRowCount = brickRowCounts[level-1];
        brickColumnCount = brickColumnCounts[level-1];
        brickPadding = 10;
        brickOffsetTop = 30;
        brickOffsetLeft = 10;
        brickHeight = 20;
        if(orient == "landscape"){
            document.getElementById("myCanvas").width = screen.availHeight - 20;
            var tempHeight = brickOffsetTop + brickRowCount*brickHeight +(brickRowCount-1)*brickPadding + ballRadius*2 +paddleHeight +80;
            document.getElementById("myCanvas").height = tempHeight+160 < screen.availWidth?tempHeight:screen.availWidth-160 ;
            brickWidth = (canvas.width-brickOffsetLeft)/brickColumnCount - brickPadding;
        
        }else{
            document.getElementById("myCanvas").width = screen.availWidth - 20;
            var tempHeight = brickOffsetTop + brickRowCount*brickHeight +(brickRowCount-1)*brickPadding + ballRadius*2 +paddleHeight +80;
            document.getElementById("myCanvas").height = tempHeight+160 < screen.availHeight?tempHeight:screen.availHeight-160 ;
            brickWidth = (canvas.width-brickOffsetLeft)/brickColumnCount - brickPadding;
        }
            
        score = 0;
        lives = 30;
        // statusFlag = 0;
        // document.getElementById("myCanvas").width = 2*brickOffsetLeft + brickColumnCount*brickWidth +(brickColumnCount-1)*brickPadding;
       
        x = canvas.width/2;
        y = canvas.height-30;

        if(paddleNum == 1){
            paddleXs = [(canvas.width-paddleWidth)/2];
        }else{
            paddleXs = [(canvas.width-2*paddleWidth-paddlePadding)/2,(canvas.width-2*paddleWidth-paddlePadding)/2+paddleWidth+paddlePadding];
        }
        if(statusFlag == -1){
            for(r=0; r<brickRowCount; r++) {
            bricks[r] = [];
            for(c=0; c<brickColumnCount; c++) {
                var hardness = Math.floor(3*Math.random());
                console.log(hardness);
                bricks[r][c] = { x: 0, y: 0, status: 1, hard:hardness };
            }
        }
        }
        statusFlag = 0;

        countdown = document.getElementById("countdown");
        countdown.pause();
        n_sec = document.getElementById("inputSecond").value==""?0:document.getElementById("inputSecond").value;  //秒
        n_min = document.getElementById("inputMin").value==""?0:document.getElementById("inputMin").value;  //分
        n_hour = document.getElementById("inputHour").value==""?0:document.getElementById("inputHour").value; //时
        ele_timer = timeStr(n_hour,n_min,n_sec);

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBricks();
        drawBall();
        drawPaddle();
        drawScore();
        drawLives();
        drawTimer();
        var startButton = document.getElementById("startButton");
        startButton.value = "Start";

		Window.onresize=function(){
            resize();
		// canvas.width=document.documentElement.clientWidth; 
		// canvas.height=document.documentElement.clientHeight;
		};
        clearInterval(n_timer);
        // stopFlag = true;
        cancelAnimationFrame(globalAnimationID);
    }
    function draw() {
        isBounced = false;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBricks();
        drawBall();
        drawPaddle();
        drawScore();
        drawLives();
        drawTimer();
        collisionDetection();

        if(x + dx > canvas.width-ballRadius || x + dx < ballRadius) {
            dx = -dx;
        }
        if(y + dy < ballRadius) {
            dy = -dy;
        }else if(y + dy > canvas.height-ballRadius) {
            for(var index in paddleXs){
                if(x > paddleXs[index] && x < paddleXs[index] + paddleWidth) {
                    dy = -dy;
                    isBounced = true;
                    break;
                }
            }
            if(!isBounced){
                lives--;
                if(!lives) {
                    alert("GAME OVER");
                    document.location.reload();
                }else {
                    x = canvas.width/2;
                    y = canvas.height-30;
                    dx = Math.abs(dx);
                    dy = -Math.abs(dy);
                    // dx = 3;
                    // dy = -3;
                    // paddleX = (canvas.width-paddleWidth)/2;
                }
            }
        }
       
        // missing code here to update paddle position
        if(rightPressed && paddleXs[0] < canvas.width - paddleWidth){
            paddleXs[0] = paddleXs[0] + 10;
        }else if(leftPressed && paddleXs[0] > 0){
            paddleXs[0] = paddleXs[0] - 10;
        }

        if(dPressed && paddleXs[1] < canvas.width - paddleWidth){
            paddleXs[1] = paddleXs[1] + 10;
        }else if(aPressed && paddleXs[1] > 0){
            paddleXs[1] = paddleXs[1] - 10;
        }
        x += dx;
        y += dy;
        globalAnimationID = requestAnimationFrame(draw);
        console.log(ele_timer);
    }
    init();
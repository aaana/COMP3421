<!DOCTYPE HTML>
<html>
<head>
    <?php
    session_start(); 

    ?>
<link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="PongHauKiStyle.css">
<script src="jquery-3.1.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="Bootstrap/js/bootstrap.min.js"></script>
    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDLgVhcGQ2taML9OZq0VVehuOlkiXFrKRo"></script>
    <script style="text/javascript">
        function initMap(id,lat,lng,grade) {
            var myLatLng = {lat: lat, lng: lng};
            // Create a map object and specify the DOM element for display.
            var map = new google.maps.Map(document.getElementById(id), {
                center: myLatLng,
                scrollwheel: false,
                zoom: 15
            });

            var infowindow = new google.maps.InfoWindow({
                content: grade.toString()
            });
            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: 'Hello World!'
            });
            marker.addListener('click', function() {
                infowindow.open(map, marker);
            });
        }
var neighbours;
var position;
var turn;
var p11,p12,p21,p22;
var timer;
var sec;
var countdown;
var countdownAudio;
var turnFlag;
var startButton;
var mode;
var posStatus;
var status; //1 start, 0 stop, 2 resume
var finishFlag;

var n_sec;  //秒
var n_min;  //分
var n_hour; //时
var ele_timer;
var totalTime;

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


function movePiece(piece,sourcePos,targetPos){
    position[piece] = targetPos;
    posStatus[sourcePos] = 0;
    posStatus[targetPos] = 1;
    document.getElementById(targetPos).appendChild(document.getElementById(piece));
}

        function updatePos(){
            posStatus['pos1'] = posStatus['pos2'] = posStatus['pos3'] = posStatus['pos4'] = posStatus['pos5'] = 0;
            for(var i in position){
                document.getElementById(position[i]).appendChild(document.getElementById(i));
                posStatus[i] = 1;
            }
        }

function ai(){
    if((position["p11"] == "pos1" && position["p12"] == "pos4") || (position["p11"] == "pos4" && position["p12"] == "pos1")){
        if(position["p21"]=="pos3"){
            movePiece("p22","pos2","pos5");
        }else if(position["p21"] == "pos2"){
            if(position["p22"] == "pos3"){
                movePiece("p21","pos2","pos5");
            }else{
                movePiece("p21","pos2","pos3");
            }
        }else{
            movePiece("p22","pos2","pos3");
        }
    }else if((position["p11"] == "pos2" && position["p12"] == "pos5") || (position["p11"] == "pos5" && position["p12"] == "pos2")){
        if(position["p21"]=="pos3"){
            movePiece("p22","pos1","pos4");
        }else if(position["p21"] == "pos1"){
            if(position["p22"] == "pos3"){
                movePiece("p21","pos1","pos4");
            }else{
                movePiece("p21","pos1","pos3");
            }
        }else{
            movePiece("p22","pos1","pos3");
        }
    }else{ //generate random
        var pieces;
        if(Math.random() < 0.5){
            pieces = ["p21","p22"];
        }else{
            pieces = ["p22","p21"];
        }
        for(var i in pieces){
            var possiblePos = neighbours[position[pieces[i]]];
            for(var j in possiblePos){
                if(posStatus[possiblePos[j]]==0){
                    movePiece(pieces[i],position[pieces[i]],possiblePos[j]);
                    return;
                }
            }
        }

    }

}

function resetTimer(){
    countdownAudio.pause();
    sec = 20;
    countdown.style.color = "black";
}

function startTimer(){
    
    timer = setInterval(function(){
        sec --;
        n_sec ++;
        countdown.innerHTML = sec;
        if(n_sec >= 60){
            n_sec = 0;
            n_min = 1;
        }
        if(n_min>=60){
            n_hour = 1;
            n_min = 0;
        }
        ele_timer = timeStr(n_hour,n_min,n_sec)
        totalTime.innerHTML = ele_timer;
        if(sec <= 5){
            countdown.style.color = "red";
            countdownAudio.play();
        }
        if(sec <= 0){
            stop();
            if(turn == 1){
                finishFlag = 2;
                if(mode == 2){
                    alert("Your time out, Player 2 wins!");
                }else{
                    alert("Your time out, Computer wins!");
                }
            }else{
                finishFlag = 1;
                if(mode == 2){
                    alert("Your time out, Player 1 wins!");
                }else{
                    alert("Your time out, Computer wins!");
                }
            }
            
        }
    },1000);
}

function stopTimer(){
    clearInterval(timer);
}

// function strTimer(sec){
//  var str_sec = sec;
//  if(sec<10){
//      str_sec = "0" + sec;
//  }
//  return str_sec;
// }

        function updateStartButton(status) {
            if(status == 1){
                startButton.value = "Start";
            }else if(status == 0){
                startButton.value = 'Stop';
            }else{
                startButton.value = 'Resume';
            }

        }

function start(){
    <?if(isset($_SESSION['mode'])){?>
    mode =<?echo $_SESSION['mode'];?>;
    <?}else{?>
        mode = document.getElementById("modeSelect").value;
    <?}?>

    if(status == 1){// first start
        setTurn();
        resetTimer();
        startTimer();
        status = 0;
        if(mode == 2){
            send(function () {

            },6,null);
//            setInterval(getGameStatus,500);
            <?if(isset($_SESSION['playerNum']) &&$_SESSION['playerNum'] == 1){?>
            p11.setAttribute("draggable",true);
            p12.setAttribute("draggable",true);
            p21.setAttribute("draggable",false);
            p22.setAttribute("draggable",false);
            <?}else{?>
            p11.setAttribute("draggable",false);
            p12.setAttribute("draggable",false);
            p21.setAttribute("draggable",false);
            p22.setAttribute("draggable",false);
            <?}?>
            send(function () {
            },4,{'turn':1,'status':0,'position':position});
        }

        startButton.value = "Stop";
    }else if(status==0){ //to stop
        stop();
        status = 2;
        startButton.value = "Resume";
        if(mode == 2){
            send(function () {

            },4,{'turn':1,'status':0,'position':position});
        }

    }else{ // stop - >resume
        startTimer();
        setTurn();
        status = 0;
        startButton.value = "Stop";
        if(mode ==2){
            send(function () {

            },4,{'turn':1,'status':0,'position':position});
        }

    }


}

function stop(){
    p11.setAttribute("draggable",false);
    p12.setAttribute("draggable",false);
    p21.setAttribute("draggable",false);
    p22.setAttribute("draggable",false);
    stopTimer();
    countdownAudio.pause();
}

function init(){

    neighbours = new Array(5);
    neighbours["pos1"] = ["pos3","pos4"];
    neighbours["pos2"] = ["pos3","pos5"];
    neighbours["pos3"] = ["pos1","pos2","pos4","pos5"];
    neighbours["pos4"] = ["pos1","pos3","pos5"];
    neighbours["pos5"] = ["pos2","pos3","pos4"];
    position = {p11:"pos1",p12:"pos2",p21:"pos4","p22":"pos5"};
    posStatus = {"pos1":1,"pos2":1,"pos3":0,"pos4":1,"pos5":1};
    turn = 1;
    status = 1;
    finishFlag = -1;
    mode = document.getElementById("modeSelect").value;
    p11 = document.getElementById("p11");
    p12 = document.getElementById("p12");
    p21 = document.getElementById("p21");
    p22 = document.getElementById("p22");
    n_sec = 0;
    n_min = 0;
    n_hour = 0;
    ele_timer = timeStr(n_hour,n_min,n_sec);
    totalTime = document.getElementById("totalTime");
    totalTime.innerHTML = ele_timer;
    countdown = document.getElementById("countdown");
    countdownAudio = document.getElementById("countdownAudio");
    countdown.style.color = "black";
    countdown.innerHTML = "20";
    turnFlag = document.getElementById("turn");
    turnFlag.style.color = "#61afea"
    <?if(isset($_SESSION['username'])){?>
    turnFlag.innerHTML = "<?echo $_SESSION['username'];?>";
    <?}else{?>
    turnFlag.innerHTML = "Player";
<?}?>

    startButton = document.getElementById("startButton");
    startButton.value = "Start";
    var pos1 = document.getElementById("pos1");
    var pos2 = document.getElementById("pos2");
    var pos4 = document.getElementById("pos4");
    var pos5 = document.getElementById("pos5");
    pos1.appendChild(p11);
    pos2.appendChild(p12);
    pos4.appendChild(p21);
    pos5.appendChild(p22);
    stop();

    // alert(p11);
}
function setTurn(){
    <?if(isset($_SESSION['playerNum'])){?>

    if(mode == 2){
        var player = <?echo $_SESSION['playerNum'];?>;
        if(player == turn){
            if(turn == 1){
                p11.setAttribute("draggable",true);
                p12.setAttribute("draggable",true);
                p21.setAttribute("draggable",false);
                p22.setAttribute("draggable",false);
                turnFlag.style.color = "#61afea";
            }else{
                p11.setAttribute("draggable",false);
                p12.setAttribute("draggable",false);
                p21.setAttribute("draggable",true);
                p22.setAttribute("draggable",true);
                turnFlag.style.color = "#e4760f";
            }
            turnFlag.innerHTML = '<?echo $_SESSION['username'];?>';

        }else{
                p11.setAttribute("draggable",false);
                p12.setAttribute("draggable",false);
                p21.setAttribute("draggable",false);
                p22.setAttribute("draggable",false);
                if(turn == 1){
                    turnFlag.style.color = "#61afea";
                }else{
                    turnFlag.style.color = "#e4760f";
                }
                turnFlag.innerHTML = '<?echo $_SESSION['competitor']['name'];?>';
        }

    }
    <?}?>
    if(mode == 1) {
        if(turn == 1){
            p11.setAttribute("draggable",true);
            p12.setAttribute("draggable",true);
            p21.setAttribute("draggable",false);
            p22.setAttribute("draggable",false);
            turnFlag.style.color = "#61afea";
            var turnFlagText = "Player";
            <?if(isset($_SESSION['username'])){?>
                var turnFlagText = <?echo $_SESSION['username'];?>;
            <?}?>
            turnFlag.innerHTML = turnFlagText;
        }else{
            p11.setAttribute("draggable",false);
            p12.setAttribute("draggable",false);
            p21.setAttribute("draggable",false);
            p22.setAttribute("draggable",false);
            turnFlag.style.color = "#e4760f";
            turnFlag.innerHTML = "Computer";
        }

    }  
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    var sourcePos = position[data];
    var targetPos = ev.target.id;
    // alert(sourcePos+"->"+targetPos);
    if(finishFlag == -1 && neighbours[sourcePos].indexOf(targetPos)!=-1 && posStatus[targetPos]==0){
        position[data] = targetPos;
        ev.target.appendChild(document.getElementById(data));
        posStatus[sourcePos] = 0;
        posStatus[targetPos] = 1;
        finishFlag = isFinished();
        if(finishFlag == 1){
            if(mode == 1){
                alert("Game Over. You win!")
            }else{
                alert("Game Over. Player1 wins!");
            }
            stop();
        }else if(finishFlag == 2){
            alert("Game Over. Player2 wins!")
            stop();
        }else{
            stopTimer();
            resetTimer();
            startTimer();
            if(turn == 1){
                turn = 2;
            }else{
                turn = 1;
            }
            setTurn();
            if(mode == 2){
                  send(function () {

            },4,{'turn':turn,'status':status,'position':position});
            }
          
            if(mode == 1 && turn == 2){
                setTimeout(function(){
                    ai();
                    turn = 1;
                    setTurn();
                },1000);
                setTimeout(function(){
                    finishFlag = isFinished();
                    if(finishFlag == 1){
                        alert("Game Over. Player wins!")
                        stop();
                    }else if(finishFlag == 2){
                        alert("Game Over. Computer wins!")
                        stop();
                    }
                },1500);
                
            }
        }

    }

}



function isFinished(){
    if((position["p11"] == "pos1" && position["p12"] == "pos4") || (position["p11"] == "pos4" && position["p12"] == "pos1")){
        if((position["p21"] == "pos3" && position["p22"] == "pos5") || (position["p21"] == "pos5" && position["p22"] == "pos3")) {
            return 2;
        }
    }
    if((position["p11"] == "pos2" && position["p12"] == "pos5") || (position["p11"] == "pos5" && position["p12"] == "pos2")){
        if((position["p21"] == "pos3" && position["p22"] == "pos4") || (position["p21"] == "pos4" && position["p22"] == "pos3")) {
            return 2;
        }
    }
    if((position["p21"] == "pos1" && position["p22"] == "pos4") || (position["p21"] == "pos4" && position["p22"] == "pos1")){
        if((position["p11"] == "pos3" && position["p12"] == "pos5") || (position["p11"] == "pos5" && position["p12"] == "pos3")){
            return 1;
        }
    }
    if((position["p21"] == "pos2" && position["p22"] == "pos5") || (position["p21"] == "pos5" && position["p22"] == "pos2")){
        if((position["p11"] == "pos3" && position["p12"] == "pos4") || (position["p11"] == "pos4" && position["p12"] == "pos3")){
            return 1;
        }
    }
    return -1;
}
        



</script>
</head>
<body onload="init()">

<div  class="col-sm-3" style="margin-top: 10px;">
    <?php
    if (!isset($_SESSION['username'])){
    ?>
    <span id="greeting">Hello , Please login first!</span></br>
    <div id="authPanel">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#registerPanel" role="tab" data-toggle="tab">Register</a></li>
            <li role="presentation"><a href="#loginPanel" role="tab" data-toggle="tab">Login</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="margin-top: 10px;">
            <div role="tabpanel" class="tab-pane active" id="registerPanel">
                <form id="registerForm" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="registerName" class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-8">
                            <input type="text" name="username" class="form-control" id="registerName" placeholder="Username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registerPwd" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control" id="registerPwd" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location" class="col-sm-3 control-label">Location</label>
                        <div class="col-sm-8">
                            <input type="text" name="location" class="form-control" id="location" placeholder="Geo-location">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5">
                            <button id="locate" type="button" class="btn btn-default">Locate</button>
                        </div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-default">Register</button>
                        </div>
                    </div>
                </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="loginPanel">
                <form id="loginForm" class="form-horizontal" role="form" action="login.php" method="post">
                    <div class="form-group">
                        <label for="loginName" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="username" class="form-control" id="loginName" placeholder="Username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="loginPwd" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control" id="loginPwd" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-default">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
<?}else{?>
        <span id="greeting">Hello , <?echo $_SESSION['username'];?></span>
        <button id="logout">Logout</button>
    <?}?>
    <div id="myMap" style="height:300px;"></div>
</div>

<div class="col-sm-6">
    <div class="gamehead center">
        Pong Hau Ki
    </div>
    <div class="infoPannel">
    <span>
        <span class="mode">Mode</span>
        <select onchange="modeChange(this)" id="modeSelect">
            <option value="1">Computer-Player</option>
            <option value="2">Player-Player</option>
        </select>
    </span>

    <span class="timer">
        <span>Timer:</span>
        <span id="countdown"></span>
    </span>
    <span class="totalTime">
        <span>Total:</span>
        <span id="totalTime"></span>
    </span>
    <span class="turnFlag">
        <span>Turn:</span>
        <span id="turn"></span>
    </span>
        <button type="button" id="about" data-toggle="modal" data-target="#aboutModal">
            <span class="glyphicon glyphicon-info-sign" ></span>
        </button>


    </div>
    <div class="center chessBoard">
        <table width="100%">
            <tr>
                <td>
                    <div id="pos1" class="div1" ondrop="drop(event)" ondragover="allowDrop(event)">
                        <img id="p11" src="resources/piece1.png" draggable="false" ondragstart="drag(event)" width="69" height="69">
                    </div>
                </td>
                <td></td>
                <td>
                    <div id="pos2" class="div1" ondrop="drop(event)" ondragover="allowDrop(event)">
                        <img id="p12" src="resources/piece1.png" draggable="false" ondragstart="drag(event)" width="69" height="69">
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><div id="pos3" class="div1" ondrop="drop(event)" ondragover="allowDrop(event)"></div></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div id="pos4" class="div1" ondrop="drop(event)" ondragover="allowDrop(event)">
                        <img id="p21" src="resources/piece2.png" draggable="false" ondragstart="drag(event)" width="69" height="69">
                    </div>
                </td>
                <td></td>
                <td>
                    <div id="pos5" class="div1" ondrop="drop(event)" ondragover="allowDrop(event)">
                        <img id="p22" src="resources/piece2.png" draggable="false" ondragstart="drag(event)" width="69" height="69">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="controlPannel">
        <input type="button" style="margin-left:50px" id="startButton" class="controlButton" onclick="start()" value="Start" />
        <input type="button" style="margin-left:200px" class="controlButton" onclick="init()" value="Replay" />
    </div>
</div>

<div id="competitor" class="col-sm-3">
    <?php
    if(isset($_SESSION['competitor'])){
    ?>
        <span style="margin-top: 10px" id="competitorName">Your competitor:<?echo $_SESSION['competitor']['name'];?></span>
        <div id="competitorMap" style="height:300px;"></div>

    <?}else{?>
    <span id="competitorName"></span>
        <div id="competitorMap" style="height:300px;"></div>
    <?}?>
</div>

<audio id="countdownAudio" src="resources/countdown.mp3" style="display:none" controls loop ></audio>
<!-- Readme Modal Dialog!!! -->
<!-- Modal -->
  <div class="modal fade" id="aboutModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Readme</h4>
      </div>
      <div class="modal-body">
            <p>
            Pong Hau K'i is a Chinese traditional board game for two players. The board consists of 5 vertices and 7 edges. Each player has two pieces. Players take turns to move. At each turn, the player moves one of his two pieces into the adjacent vacant vertex. If a player can't move, he loses.
            </p>
            <hr>
            The counting down audio will play when there is only 5 seconds left.
            <hr>
            <p>
            There are 2 modes(2-Player & Player VS Computer).<br>
            The <b style="color:#61afea;">blue(goes first)</b> piece : Player1<br>
            The <b style="color:#e4760f;">orange</b>  piece : Player2/Computer
            </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>

<!-- 
<div style="width:350px;height:70px;" ondrop="drop(event)" ondragover="allowDrop(event)">
    <img id="drag1" src="lab4/chicken.gif" draggable="true" ondragstart="drag(event)" width="336" height="69">
</div> -->

</body>
<script>

    <?if(isset($_SESSION["mode"])){?>
    $('#modeSelect').val(<?echo $_SESSION["mode"];?>);
    <?}?>

    <?php if(isset($_SESSION['username'])){?>
    initMap('myMap',<?echo $_SESSION['lat'];?>,<?echo $_SESSION['lng'];?>,<?echo json_encode($_SESSION['grade']);?>);
    <?}?>
    <?php if(isset($_SESSION['competitor'])){?>
//    $('#competitorName').text('Your competitor:'+'<?//echo $_SESSION['competitor']['name']?>//');
    initMap('competitorMap',<?echo $_SESSION['competitor']['lat'];?>,<?echo $_SESSION['competitor']['lng'];?>,<?echo json_encode($_SESSION['competitor']['grade']);?>);
    $('#competitorMap').attr('display','block');
    <?}?>
    var geocoder = new google.maps.Geocoder;
    function getLocation()
    {
        if (navigator.geolocation)
        {
            navigator.geolocation.getCurrentPosition(function (pos) {
                var latlng = new google.maps.LatLng(pos.coords.latitude,pos.coords.longitude);
                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $('#location').val( results[1].formatted_address);
                        }
                    }
                });
            });
        }
        setTimeout(function(){
              if($('#location').val() == ''){
                alert("can not obtain the location, please set the privacy or enter manually");
                $('#location').focus();
            }
        },3000);
    
    }
    
    $('#locate').click(function () {
        getLocation();
    });
        
//    function connect(serverUrl) {
//        if (window.MozWebSocket) {
//            socket = new MozWebSocket(serverUrl);
//        } else if (window.WebSocket) {
//            socket = new WebSocket(serverUrl);
//        }
//        socket.binaryType = 'blob';
//        socket.onopen = function (msg) {
//            return true;
//        };
//        socket.onmessage = function (msg) {
//            var response;
//            response = JSON.parse(msg.data);
//            return true;
//        };
//        socket.onclose = function (msg) {
//
//        }
//    }

//    //address -> geocoding
//    function getGeoCoding(address) {
//        geocoder.geocode( { 'address': address}, function(results, status) {
//            if (status == google.maps.GeocoderStatus.OK) {
//                return {'lng':results[0].geometry.location[1],'lat':results[0].geometry.location[0]};
//            }
//        });
//    }
//
    //reverse 纬经度 -> 地址
//    function getAddress(lat,lng) {
//        var latlng = new google.maps.LatLng(lat,lng);
//        geocoder.geocode({'location': latlng}, function(results, status) {
//            if (status === google.maps.GeocoderStatus.OK) {
//                if (results[1]) {
//                    return results[1].formatted_address
//                }
//            }
//        });
//    }

        $('#registerForm').submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting via the browser
            var form = $(this);
            if ($('#registerName').val() == '') {
                alert("Please enter a name!");
                $('#registerName').focus();
            }else if($('#registerForm input[name="password"]').val() == ''){
                alert("Please enter a password!");
                $('#registerPwd').focus();
            }else if($('#location').val() === ""){
                alert("Please enter your location!");
                $("#location").focus();
            }else{
                geocoder.geocode( { 'address': $('#location').val()}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var location = [results[0].geometry.location.lat(),results[0].geometry.location.lng()];
                        $.ajax({
                            type: "post",
                            url: "register.php",
                            data: {
                                'username':$('#registerName').val(),
                                'password':$('#registerPwd').val(),
                                'location':location,
                            },
                            dataType:"json",
                            success:function (data) {
                                if(data.success == 1){
                                    alert("Register succeeds!Ready to login");
                                    $('#loginName').val($('#registerName').val());
                                    $('#loginPwd').val($('#registerPwd').val());
                                    $('ul > li:first-child').removeClass("active");
                                    $('ul > li:nth-child(2)').addClass("active");
                                    $('#registerPanel').removeClass("active");
                                    $('#loginPanel').addClass("active");
                                }else if(data.success == -1){
                                    alert("User exists");
                                    $('#registerName').val('');
                                    $('#registerName').focus();
                                }
                            },
                            error: function (e) {
                                alert(e.responseText);
                            }
                        })
                    }
                });

            }

        });


        function send(success,type,content =null) {
            $.ajax({
                type:"post",
                url:'handle.php',
                data:{"type":type,"content":content},
                dataType:'json',
                success:success,
                error:function (e) {
                    alert(e.responseText);
                }
            })

        }

    $('#logout').click(function () {
        alert("Logout clicked!");
        send(function (data) {
            if (data.success == 1) {
                console.log("success");
                alert(data.user + " exits");
                window.location.href = 'PongHauKi.php';
            }
        }, -1, $('#loginName').val());
    });

    $('#loginForm').submit(function (event) {
        event.preventDefault();
        if($('#loginName').val() === ""){
            alert("Please enter a name!");
            $('#loginName').focus();
        }else if($('#loginPwd').val() === ""){
            alert("Please enter your password!");
            $('#loginPwd').focus();
        }else {
            send(function (data) {
                if (data.success == 1) {
                    alert("Login succeeds!");
                    $('#authPanel').slideUp();
                    initMap('myMap',data.lat, data.lng, data.grade);
                    $('#greeting').text("Hello, " + $('#loginName').val());
                    $('#greeting').after('<button id="logout">Logout</button>');
                    location.reload(); 

//                        $.ajax({
//                            type: 'post',
//                            data: $('#loginName').val(),
//                            dataType: 'json',
//                            url: "logout.php",
//                            success: function (data) {
//                                if (data.success == 1) {
//                                    console.log("success");
//                                    alert(data.user + " exits");
//                                    window.location.href = 'PongHauKi.php';
//                                }
//                            },
//                            error: function (e) {
//                                alert("error" + e.responseText);
//                            }
//                        })
//                    });
//                        connect('ws://localhost:8000');
//                        payload = new Object();
//                        payload.action = 'login';
//                        socket.send(JSON.stringify(payload));

                } else if (data.success == 2) {
                    alert("Register first!");
                    $('ul > li:first-child').addClass("active");
                    $('ul > li:nth-child(2)').removeClass("active");
                    $('#registerPanel').addClass("active");
                    $('#loginPanel').removeClass("active");
                    $('#registerName').focus();
                } else if (data.success == 3) {
                    alert("Wrong password!");
                    $('#loginPwd').val('');
                    $('#loginPwd').focus();
                }
            }, 0, {'username': $('#loginName').val(), 'password': $('#loginPwd').val()});
        }
//            $.ajax({
//                type:"post",
//                url:"login.php",
//                data:$('#loginForm').serialize(),
//                dataType:"json",
//                success: function (data) {
//                    if(data.success == 1){
//                        alert("Login succeeds!");
//                        $('#authPanel').slideUp();
//                        initMap(data.lat,data.lng,data.grade.toString());
//                        $('#greeting').text("Hello, "+ $('#loginName').val());
//                        $('#greeting').after('<button id="logout">Logout</button>');
//                        $('#logout').click(function () {
//                            alert("Logout clicked!");
//                            $.ajax({
//                                type:'post',
//                                data:$('#loginName').val(),
//                                dataType:'json',
//                                url:"logout.php",
//                                success:function (data) {
//                                    if(data.success == 1){
//                                        console.log("success");
//                                        alert(data.user + " exits");
//                                        window.location.href = 'PongHauKi.php';
//                                    }
//                                },
//                                error:function (e) {
//                                    alert("error"+e.responseText);
//                                }
//                            })
//                        });
////                        connect('ws://localhost:8000');
////                        payload = new Object();
////                        payload.action = 'login';
////                        socket.send(JSON.stringify(payload));
//
//                    }else if(data.success == 2){
//                        alert("Register first!");
//                        $('ul > li:first-child').addClass("active");
//                        $('ul > li:nth-child(2)').removeClass("active");
//                        $('#registerPanel').addClass("active");
//                        $('#loginPanel').removeClass("active");
//                        $('#registerName').focus();
//                    }else if(data.success == 3){
//                        alert("Wrong password!");
//                        $('#loginPwd').val('');
//                        $('#loginPwd').focus();
//                    }
//                }
//            })

    });

    var intervalId;
    var findCompetitor = function () {
        send(function (result) {
            // alert(result.success);
            if(result.success == 2){
//                alert("There is no competitor right now.You can play with computer and try again later.");
                $('#competitorName').text("Waiting for competitor");
            }else if(result.success == 1){
                alert("Your competitor is " + result.competitor.name);

                clearInterval(intervalId);
                $('#competitorName').text("Your competitor: "+result.competitor.name);
                $("#competitorMap").css('display','block');
                initMap('competitorMap',result.competitor.lat,result.competitor.lng,result.competitor.grade);
                $("#competitorMap").slideDown();
                $('#modeSelect').attr('disabled','disabled');
                if(result.playerNum==1){
                turnFlag.style.color = "#61afea";
                turnFlag.innerHTML = result.username;
                location.reload();
            }else{
                turnFlag.style.color = "#e4760f";
                turnFlag.innerHTML = result.competitor.name; 
            }
            }
        },3,null);
    };

    var gameId = setInterval(send(function(data){
        if(data.success == 1){
            clearInterval(gameId);
             statusIntervalId = setInterval(function () {
                <?if(file_exists('game.json')){?>
                getGameStatus();
                <?}else if(isset($_SESSION['competitor'])){?>
                            alert('Game over!The competitor exits');
                        clearInterval(statusIntervalId);
                    <?}?>
            },500);
        }
    },7,null));



    var getGameStatus = function () {
        send(function (data) {
            turn = data.turn;
            status = data.status;
            position = data.position;
            // if(status == -1){
            //     alert('The competitor exits!');
            //     clearInterval(statusIntervalId);
            // }
            setTurn();
            updatePos();
            updateStartButton(status);
        },5,null)
    };

    function modeChange(self){
            <?if(isset($_SESSION['username'])){?>
                    send(function (data) {
                        // alert(data.mode);
                        if (data.mode == 2) {
                            intervalId = setInterval(findCompetitor, 1000);
        //                send(function (result) {
        //                    alert(result.success);
        //                    if(result.success == 2){
        //                        alert("There is no competitor right now.You can play with computer and try again later.");
        //                        setInterval()
        //                    }else if(result.success == 1){
        //                        alert("Your competitor is " + result.competitor);
        //                    }
        //                },3,null);
                        } else {
                            if (intervalId)
                                clearInterval(intervalId);
                            location.reload();
                        }

                    }, 2, {'mode': self.value});
            <?}else{?>
            if(self.value == 2)
                alert("Please login!");
            <?}?>
    }
</script>

</html>

<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/23/16
 * Time: 12:15 PM
 */
require_once 'client/client.php';
session_start();
$type = $_POST['type'];
$content = $_POST['content'];
//login
if($type == 0){
    $data = ["success" =>1,"grade" =>"1",'username'=>$content['username']];
    $_SESSION["username"] = $content['username'];

    //todo check username and password redirect to register or alert or succeeds

    //todo
//    $socket = new client();
//    $socket.createSocket("127.0.0.1",'8000');
//    $socket.send(...);
//    $socket.read(...);
}
//logout
else if($type == -1){
    $data = ["success" =>1];
    session_unset();
    session_destroy();
    //todo inform the competitor
}
//modeChoose
// computer - player
// player - player
else if($type == 2){

}
//ready
else if($type == 3){

}
//movePieces
else if($type == 4){

}
//getPiecesPosition
else if($type == 5){

}

echo json_encode($data);

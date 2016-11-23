<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/21/16
 * Time: 9:13 PM
 */

include_once 'game.php';
$players[] = array();
$game[] = array();
$readyPlayers[] = array();
$playingPlayers[] =array();

$host = "127.0.0.1";
$port = "8000";
set_time_limit(0);
print("Starting Socket Server...\n");
$sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
socket_bind($sock,$host,$port);
socket_listen($sock,4);

print(" Socket Server on work...\n");

function handle(){

}

do {
    if($childSocket = socket_accept($sock)<0){
        $sock.log('Socket error: ' . socket_strerror(socket_last_error($childSocket)));
    }else{
        $players[] = new player($childSocket);
    }
    foreach ($players as $player){
//        $incommingData = socket_read($player, 2048);
//        if (trim($incommingData) == "are you hungry?") {
//            $response = "Server response > I could eat!\n";
//            socket_write($player, $response, strlen($response));
//        } elseif (trim($incommingData) == "exit") {
//            $response = "Goodbye!\n";
//            socket_write($player, $response, strlen($response));
//            socket_close($player);
//            break;
//        } else {
//            $response = strtoupper(trim($incommingData)) . "\n";
//            $writeResp = socket_write($player, $response, strlen($response));
//            if ($writeResp === FALSE) {
//                socket_close($player);
//                break;
//            }
//
//        }
    }
}while(true);
socket_close($sock);?>
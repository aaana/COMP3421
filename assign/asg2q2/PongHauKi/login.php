<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/21/16
 * Time: 7:49 PM
 */
require_once 'client.php';

$data = ["success" =>2,"grade" =>"1"];
session_start();
$_SESSION["username"] = $_POST['username'];

$socket = new client();
$socket.createSocket("127.0.0.1",'8000');

echo json_encode($data);
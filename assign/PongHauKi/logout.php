<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/24/16
 * Time: 2:00 AM
 */
session_start();
$user = $_SESSION['username'];
session_unset();
session_destroy();
$data = ['success'=>1,'user'=>$user];
echo json_encode($data);
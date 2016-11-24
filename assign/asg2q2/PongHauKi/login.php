<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/21/16
 * Time: 7:49 PM
 */

session_start();

$data = array();
$user = $_POST['username'];
$password = $_POST['password'];
$conn = new SQLite3('user.db');
if(!$conn) {
//    echo $conn->lastErrorMsg();
    $data['success'] = -1;
}else{
    $sql = <<<EOD
SELECT * FROM userinfo where name = '$user';
EOD;

    $ret = $conn->query($sql) or die("Unable to select");
    if ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        if ($row['password'] != $password) {
            $data['success'] = 3;
        }else{
            $_SESSION["username"] = $_POST['username'];
            $data = ['success'=>1,'name'=>$user,'lat'=>$row['lat'],'lng'=>$row['lng'],'grade'=>$row['grade']];
            $client =new client();
            $client->createSocket('localhost',8001);
            $out = fopen("online.txt","a");
            if (!$out) {
                print("Could not append to file");
                exit;
            }else{
                fputs($out,$user+"\n");
            }
        }
    } else {
        $data['success'] = 2;
    }
}
echo json_encode($data);
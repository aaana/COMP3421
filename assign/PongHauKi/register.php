<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/21/16
 * Time: 6:36 PM
 */

//todo store in the database
$success = 1;
$conn = new SQLite3('user.db');
if(!$conn) {
//    echo $conn->lastErrorMsg();
    $success = -2;
}
// else {
//    echo "Opened database successfully\n";
//}
$user = $_POST['username'];
$password = $_POST['password'];
$location = $_POST['location'];

if($conn){
    $sql = <<<EOD
SELECT * FROM userinfo where name = '$user';
EOD;

    $ret = $conn->query($sql)or die("Unable to select");
    if($row = $ret->fetchArray(SQLITE3_ASSOC)){
        $success = -1;
    }else{
        $sql = <<<EOD
    insert into userinfo values('$user','$password','$location[0]','$location[1]',0)
EOD;

        $conn->exec($sql) or die("Unable to insert $user");
    }
}

$data = ['success'=>$success];

echo json_encode($data);
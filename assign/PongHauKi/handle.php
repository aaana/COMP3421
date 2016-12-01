<?php
/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/23/16
 * Time: 12:15 PM
 */
session_start();
$type = $_POST['type'];
$content = $_POST['content'];
$players = array();
$data = array();
//login
if($type == 0){

    if(file_exists('player1.json')&&file_exists('player2.json')){
        $data['success'] = -2;
    }else{
        //    $data = ["success" =>1,"grade" =>"1",'username'=>$content['username']];
//    $_SESSION["username"] = $content['username'];

    $user = $content['username'];
    $password = $content['password'];
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
                $_SESSION["username"] = $content['username'];
                $_SESSION['lat'] = $row['lat'];
                $_SESSION['lng'] = $row['lng'];
                $_SESSION['grade'] = $row['grade'];
                // var_dump($_SESSION['lat']);
                $data = ['success'=>1,'name'=>$user,'lat'=>$row['lat'],'lng'=>$row['lng'],'grade'=>$row['grade']];
                $out = fopen("online.txt","a");
//                $players[] = new player($user);
//                var_dump($players);
                if (!$out) {
                    print("Could not append to file");
                    exit;
                }else{
                    fwrite($out,$user.' ');
                }
            }
        } else {
            $data['success'] = 2;
        }
    }
    }

//    echo json_encode($data);
    //todo check username and password redirect to register or alert or succeeds

    
    //todo
//    $socket = new client();
//    $socket.createSocket("127.0.0.1",'8000');
//    $socket.send(...);
//    $socket.read(...);
}
//logout
else if($type == -1){

    $user = $_SESSION['username'];
//    $fp = fopen("online.txt","r+");
//                $players[] = new player($user);
//                var_dump($players);
//    if (!$fp) {
//        print("Could not open the file");
//        exit;
//    }else{
    $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 1
            )
        )
    );
    $file = 'online.txt';
    deleteUser($file,$user);
    if(isset($_SESSION['playerNum'])){
            $filename = 'player'.$_SESSION['playerNum'].'.json';
            unlink($filename);

    }
    session_unset();
    session_destroy();

    if(file_exists('game.json')){
        unlink('game.json');
        // $string = file_get_contents('game.json');
        // $json_a = json_decode($string, true);
        // $turn = $json_a['turn'];
        // $status = $json_a['status'];
        // $position = $json_a['position'];
        // $out = fopen("game.json","w");
        // if (!$out) {
        //     print("Could not write the file");
        //     exit;
        // }else{
        //     $gameStatus = ['turn'=>$turn,'status'=>-1,'position'=>$position];
        //     fwrite($out,json_encode($gameStatus));
        // }
        // fclose($out);
    } 

    $data = ['success'=>1,'user'=>$user];
//    echo json_encode($data);
}
//modeChoose
// computer - player
// player - player
else if($type == 2){
    if($_SESSION['username']){
        $user = $_SESSION['username'];
        if($content['mode'] == 2){
            $filename = file_exists('player1.json')?'player2.json':'player1.json';
            $_SESSION['playerNum'] = file_exists('player1.json')?2:1;
            $_SESSION['mode'] =2;
            $out = fopen($filename,"w");
//                $players[] = new player($user);
//                var_dump($players);
            if (!$out) {
                print("Could not append to file");
                exit;
            }else{
                $userInfo = ['user'=>$user];
                fwrite($out,json_encode($userInfo));
            }
        }else{
            $_SESSION['mode'] = 1;
            unset($_SESSION['competitor']);
            unset($_SESSION['playerNum']);
            unlink('player'.$_SESSION['playerNum'].'.json');
        }
    }

$data = ['success'=>1,'mode'=>$content['mode']];
}
//request competitor
else if($type == 3){
    if(file_exists('player1.json')&&file_exists('player2.json')){
//        echo json_encode($users);
                $data = ['success' => 1,'playerNum'=>$_SESSION['playerNum'],'username'=>$_SESSION['username']];
               
                $competitorFile = $_SESSION['playerNum'] == 1?"player2.json":'player1.json';
                $string = file_get_contents($competitorFile);
                $json_a = json_decode($string, true);
                $user = $json_a['user'];
                $conn = new SQLite3('user.db');
                if (!$conn) {
//    echo $conn->lastErrorMsg();
                    $data['success'] = -1;
                } else {
                    $sql = <<<EOD
SELECT * FROM userinfo where name = '$user';
EOD;

                    $ret = $conn->query($sql) or die("Unable to select");
                    if ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
                        $_SESSION['competitor'] =$data['competitor'] = ['name'=>$user,'lat'=>$row['lat'],'lng'=>$row['lng'],'grade'=>$row['grade']];
                    }
//                deleteUser('ready.txt',$user);
//                deleteUser('ready.txt',$_SESSION['username']);
                }

    }else{
        //No competitor
        $data = ['success'=>2];
    }

}
//update game
else if($type == 4) {
    $out = fopen("game.json","w");
    if (!$out) {
        print("Could not write the file");
        exit;
    }else{
        $position = $content['position'];
        $gameStatus = ['turn'=>$content['turn'],'status'=>$content['status'],'position'=>$position];
//                    $posStatus = ["pos1"=>1,"pos2"=>1,"pos3"=>0,"pos4"=>1,"pos5"=>1];
        fwrite($out,json_encode($gameStatus));
    }
    fclose($out);

}else if($type == 6){
    if(!file_exists('game.json')){
            $out = fopen("game.json","w");
            if (!$out) {
                print("Could not write the file");
                exit;
            }else{
                $position = ['p11'=>"pos1",'p12'=>"pos2",'p21'=>"pos4","p22"=>"pos5"];
                $gameStatus = ['turn'=>1,'status'=>1,'position'=>$position];
        //                    $posStatus = ["pos1"=>1,"pos2"=>1,"pos3"=>0,"pos4"=>1,"pos5"=>1];
                fwrite($out,json_encode($gameStatus));
            }
            fclose($out);
    }

    $data['success'] = 1;
}
//getgame
else if($type == 5){
    
    if(file_exists('game.json')){
        $string = file_get_contents('game.json');
        $json_a = json_decode($string, true);
        $turn = $json_a['turn'];
        $status = $json_a['status'];
        $position = $json_a['position'];
        $data = ['success'=>1,'turn'=>$turn,'status'=>$status,'position'=>$position];
    }else{
        if($content['status']!='1'&&$content['mode']==2){
            $data['success'] = 0;
            $data['ss'] = $content['status'];
        }else{
            $data['success'] = -1;
        }
    }
   
}
else if($type == 7){
    if(file_exists('game.json')){
        $data['success'] = 1;
    }else{
        $data['success'] = -1;
    }
}

function deleteUser($file,$user){
    if(file_exists($file)){
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 1
                )
            )
        );
        $str =  file_get_contents($file, 0, $ctx);
//        var_dump($str);
        $fileUsers = explode(" ",$str);
//        echo json_encode($onlines);
//        var_dump($onlines);
        foreach ($fileUsers as $i=>$fileUser){
            if($fileUser == $user){
                unset($fileUsers[$i]);
                $onlines = array_values($fileUsers);
//                echo json_encode($onlines);
                break;
            }
        }
        file_put_contents($file, implode(" ",$fileUsers));
    }
}

function countUser($file){
    $count = 0;
    if(file_exists($file)) {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 1
                )
            )
        );
        $str = file_get_contents($file, 0, $ctx);
//        var_dump($str);
        $fileUsers = explode(" ", $str);
        foreach ($fileUsers as $fileUser){
            if($fileUser!=""){
                $count++;
            }
        }
    }
    return ["count"=>$count,"users"=>$fileUsers];
}

echo json_encode($data);

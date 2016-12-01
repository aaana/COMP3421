<?php

/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/22/16
 * Time: 7:12 PM
 */
class client
{
//    private $fp;

    private $socket;
    function __construct()
    {
//        $this->fp = null;
    }

    public function createSocket($host,$port){

        $this->fp = fsockopen($host,$port,$errno,$errstr);
        if(!$this->fp){
            $result = -1;
        }
        return $result;
//        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//        if ($this->socket < 0) {
//            $result = -1;
//        }else{
//            $result = $this->connect($this->socket,$host,$port);
//        }
//        return $result;
    }

//    private function connect($socket,$ip,$port){
//        $result = socket_connect($socket, $ip, $port);
//        return $result;
//    }
//
//    public function send($data){
//        socket_write($this->socket, $data, strlen($data));
//    }



    public function send($data){
        if(!$this->fp){
            fwrite($this->fp,$data);
        }
    }

    public function read($data){
        if(!$this->fp){
            $data = fgets($this->fp,1024);
        }
    }
}
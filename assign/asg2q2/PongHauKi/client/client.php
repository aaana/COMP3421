<?php

/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/22/16
 * Time: 7:12 PM
 */
class client
{
    private $fp;

    function __construct()
    {
        $this->fp = null;
    }

    public function createSocket($host,$port){

        $this->fp = fsockopen($host,$port,$errno,$errstr);
        if(!$this->fp){
            $result = -1;
        }
        return $result;
    }

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
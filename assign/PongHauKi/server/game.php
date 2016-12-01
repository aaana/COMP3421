<?php

/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/21/16
 * Time: 9:16 PM
 */
class game
{
    private $position;
    private $turn;
//    private  $player1;
//    private $player2;
//
//    public function __construct($player1,$player2)
//    {
//        $this->player1 = $player1;
//        $this->player2 = $player2;
//    }

    public function getPosition(){
        return $this->position;
    }
    
    public function getTurn(){
        return $this->turn;
    }
    
    public function setPosition($pos){
        $this->position = $pos;
    }
    public function setTurn($turn){
        $this->turn = $turn;
    }
}
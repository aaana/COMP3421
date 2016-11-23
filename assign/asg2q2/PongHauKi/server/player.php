<?php

/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/22/16
 * Time: 6:47 PM
 */
class player
{
    private $username;
    private $competitor;
    private $game;
    private $state;
    private $socket;
    
    function __construct($socket)
    {
        $this->socket = $socket;
    }
    
    function setUsername($name){
        $this->username = $name;
    }
    
    function setCompetitor($competitor){   
        $this->competitor = $competitor;
    }
    function setGame($game){
        $this->game = $game;
    }
    function setState($state){
        $this->state = $state;
    }
    
    function getUsername(){
        return $this->username;
    }
    
    function getCompetitor(){
        return $this->competitor;
    }
    
    function getGame(){
        return $this->game;
    }
    
    function getState(){
        return $this->state;
    }
    
    

}
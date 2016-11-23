<?php

/**
 * Created by PhpStorm.
 * User: huanganna
 * Date: 11/22/16
 * Time: 7:43 PM
 */
class message
{
    private $type;
    private $position;
    private $competitor;

    public function getType(){
        return $this->type;
    }

    public function getPosition(){
        return $this->position;
    }

    public function getCompetitor(){
        return $this->competitor;
    }
}
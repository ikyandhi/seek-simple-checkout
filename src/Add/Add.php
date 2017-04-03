<?php

namespace Seek\Add;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 1, 2017 5:48:29 PM
 * File         : Add.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Add
{

    public $name;
    public $price = 0;

    public function __construct($code, $price)
    {
        $this->name  = $code;
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getName()
    {
        return $this->name;
    }

}

/* End of file Add.php */
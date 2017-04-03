<?php

namespace Seek\Customer;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 1, 2017 7:49:44 PM
 * File         : Customer.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Customer
{
    
    protected $id;
    
    protected $name;
    
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
}

/* End of file Customer.php */
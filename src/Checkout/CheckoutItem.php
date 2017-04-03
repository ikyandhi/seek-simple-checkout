<?php

namespace Seek\Checkout;

use Seek\Add\Add;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 1, 2017 11:48:52 PM
 * File         : CheckoutItem.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class CheckoutItem
{

    /**
     *
     * @var Add 
     */
    protected $add;

    /**
     *
     * @var Int
     */
    public $quantity;

    /**
     *
     * @var Double
     */
    public $discountAmount;
    
    /**
     *
     * @var Double 
     */
    public $price;

    public function __construct(Add $add, $quantity = 1)
    {
        $this->add            = $add;
        $this->quantity       = $quantity ? : 1;
        $this->discountAmount = 0;
    }

    public function getAdd()
    {
        return $this->add;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setAdd(Add $add)
    {
        $this->add = $add;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function getSubTotal()
    {
        return $this->add->price * $this->quantity;
    }
    
    public function getGrandTotal()
    {
        return $this->getSubTotal() - $this->discountAmount;
    }

}

/* End of file CheckoutItem.php */
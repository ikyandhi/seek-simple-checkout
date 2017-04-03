<?php

namespace Seek\Rule;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 1, 2017 6:19:11 PM
 * File         : Rule.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Rule
{

    /**
     * This is fixed amount discount for per item qty
     */
    const FIXED_OF_PRICE_AMOUNT_DISCOUNT = 2;

    /**
     * This is if BUY X GET Y
     */
    const BUY_X_GET_Y = 1;

    /**
     *
     * @var String
     */
    protected $code;

    /**
     *
     * @var Array 
     */
    protected $instruction;

    public function __construct($code, $instruction)
    {
        $this->code = $code;

        $this->instruction = $instruction;
    }

    /**
     * Return code name set
     * 
     * @return String
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 
     * @return Array
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

}

/* End of file Rule.php */
<?php

namespace Seek\Customer;

use Illuminate\Support\Collection;
use Seek\Rule\Rule;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 1, 2017 7:50:58 PM
 * File         : CustomerGroup.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class CustomerGroup
{

    protected $id;

    /**
     *
     * @var Collection
     */
    protected $customers;

    /**
     *
     * @var Collection
     */
    protected $checkoutRules;

    public function __construct($id, Collection $customers)
    {
        $this->id        = $id;
        
        $this->customers = $customers;
        
        $this->checkoutRules = collect();
    }

    /**
     * Return collection of customers
     * 
     * @return Collection
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * Return ID
     * 
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getCheckoutRules()
    {
        return $this->checkoutRules;
    }

    /**
     * Set Checkout Rule
     * 
     * @param Rule $rule
     */
    public function setCheckoutRule(Rule $rule)
    {
        $this->checkoutRules->push($rule);
    }

}

/* End of file CustomerGroup.php */
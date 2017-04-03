<?php

namespace Seek\Checkout;

use Illuminate\Support\Collection;
use Seek\Customer\Customer;
use Seek\Customer\CustomerGroup;
use Seek\Rule\Rule;

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 1, 2017 5:45:32 PM
 * File         : Checkout.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Checkout
{

    /**
     *
     * @var Collection
     */
    public $checkoutRules;

    /**
     *
     * @var Collection
     */
    public $items;

    /**
     *
     * @var Customer
     */
    public $customer;

    /**
     *
     * @var CustomerGroup
     */
    public $customerGroup;

    /**
     *
     * @var Double
     */
    public $discountAmount;

    /**
     *
     * @var Double
     */
    public $total;

    public function __construct($items = false)
    {
        $this->items = ($items) ? : collect();
    }

    /**
     * Add checkout item
     * 
     * @param \Seek\Checkout\CheckoutItem $checkoutItem
     */
    public function addCheckoutItem(CheckoutItem $checkoutItem)
    {
        $this->items->push($checkoutItem);
    }

    /**
     * Set customer and group
     * 
     * @param Customer $customer
     * @param CustomerGroup $group
     */
    public function setCustomer(Customer $customer, CustomerGroup $group = null)
    {
        $this->customer = $customer;

        $this->customerGroup = ($group) ? : new CustomerGroup(0, collect([$customer])); //if none group pass, this shall belong to general
    }

    /**
     * Set code rule
     * 
     * @param type $code
     * @return boolean | null
     */
    public function applyRule($code)
    {
        //check if customer is set
        if ($this->customerGroup) {

            $_customerId = $this->customer->getId();
            $_customer   = $this->customerGroup->getCustomers()->first(function($item) use ($_customerId) {
                return $item->getId() === $_customerId;
            });

            if (!$_customer) {
                //customer doesn't exists or not belongs to the group
                return false;
            }

            $_checkoutRule = $this->customerGroup->getCheckoutRules()->first(function($item) use ($code) {
                return ($item->getCode() === $code);
            });

            if (!$_checkoutRule) {
                //rules doesn't exists or not belongs to the group
                return false;
            }

            if ($_customer && $_checkoutRule) {
                //customer and checkout rule match, proceed to process the rule
                $this->processRule($_checkoutRule);
            }
        }
        else {
            //no customer group set, hence none rule is applied
            return null;
        }
    }

    /**
     * 
     * @param type $checkoutRule
     * @return void
     */
    protected function processRule($checkoutRule)
    {
        if (empty($this->items)) { //none item is added
            return;
        }

        $instruction = $checkoutRule->getInstruction();

        foreach ($instruction['rules'] as $rule) {

            foreach ($this->items as $index => $item) {

                if (!isset($item->getAdd()->{$rule['property']['key']}) || ($item->getAdd()->{$rule['property']['key']} !== $rule['property']['value'])) {
                    continue;
                }

                if ($this->isRuleApply($rule, $instruction, $item)) {
                    $this->applyDiscountByActionType($instruction, $index, $item);
                }
            }
        }

        return;
    }

    /**
     * Check if rule is apply
     * 
     * @param array $rule
     * @param array $instruction
     * @param \Seek\Add\Add $item
     * @return boolean
     */
    protected function isRuleApply($rule, $instruction, $item)
    {
        $isRuleApplied = false;

        switch ($instruction['action']['type']) {
            case Rule::BUY_X_GET_Y:
                $isRuleApplied = ($instruction['action']['step'] && ($item->getQuantity() / $instruction['action']['step']) > 1) ? true : false;
                break;

            case Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT:
                if (!isset($rule['conditions'])) {
                    $isRuleApplied = ( $item->getQuantity() > 0) ? : false;
                }
                else {
                    $isRuleApplied = $this->validateRuleConditions($rule, $item);
                }
                break;

            default:
                $isRuleApplied = false;
                break;
        }

        return $isRuleApplied;
    }

    /**
     * Validate rule conditions agains property
     * 
     * @param array $rule
     * @param \Seek\Add\Add $item
     * @return boolean
     */
    protected function validateRuleConditions($rule, $item)
    {
        $isRuleApplied = false;

        foreach ($rule['conditions'] as $row) {
            switch ($row['type']) {
                case 'gteq':
                    $isRuleApplied = isset($item->{$row['field']}) && ($item->{$row['field']} >= $row['value']) ? true : false;
                    break;

                case 'gt':
                    $isRuleApplied = isset($item->{$row['field']}) && ($item->{$row['field']} > $row['value']) ? true : false;
                    break;

                case 'lteq':
                    $isRuleApplied = isset($item->{$row['field']}) && ($item->{$row['field']} <= $row['value']) ? true : false;
                    break;

                case 'lt':
                    $isRuleApplied = isset($item->{$row['field']}) && ($item->{$row['field']} < $row['value']) ? true : false;
                    break;

                default:
                    $isRuleApplied = false;
                    break;
            }
        }

        return $isRuleApplied;
    }

    /**
     * Apply discount by action type
     * 
     * @param array $instruction
     * @param integer $index
     * @param \Seek\Add\Add $item
     */
    protected function applyDiscountByActionType($instruction, $index, $item)
    {
        switch ($instruction['action']['type']) {
            case Rule::BUY_X_GET_Y:
                $this->applyDiscountToCheckoutItem($index, $item, $instruction['action']['amount'] * floor(($item->getQuantity() / $instruction['action']['step'])));
                break;

            case Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT:
                $this->applyDiscountToCheckoutItem($index, $item, $instruction['action']['amount'] * $item->getQuantity());
                break;

            case Rule::PERCENT_OF_PRICE_AMOUNT_DISCOUNT:
            case Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT_WHOLE_CHECKOUT:
                break;

            default:
                break;
        }
    }

    /**
     * Set discount amount to checkout item
     * 
     * @param integer $itemIndex
     * @param \Seek\Add\Add $item
     * @param float $discountAmount
     */
    protected function applyDiscountToCheckoutItem($itemIndex, $item,
                                                   $discountAmount = 0)
    {
        $item->discountAmount = $discountAmount;
        $this->items->put($itemIndex, $item);
    }

    /**
     * Get checkout grand total
     * 
     * @return float
     */
    public function getTotal()
    {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->getGrandTotal();
        }

        return $total;
    }

}

/* End of file Checkout.php */
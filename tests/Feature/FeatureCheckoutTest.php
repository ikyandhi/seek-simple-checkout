<?php

namespace Tests\Feature;

use Seek\Add\Add;
use Seek\Checkout\Checkout;
use Seek\Customer\Customer;
use Seek\Customer\CustomerGroup;
use Seek\Rule\Rule;
use Tests\TestCase;

class FeatureCheckoutTest extends TestCase
{

    /**
     *
     * @var Checkout
     */
    protected $checkout;

    /**
     *
     * @var CustomerGroup
     */
    protected $groupOne;

    /**
     *
     * @var CustomerGroup
     */
    protected $groupTwo;

    /**
     *
     * @var CustomerGroup
     */
    protected $groupThree;

    /**
     *
     * @var CustomerGroup
     */
    protected $groupFour;

    /**
     *
     * @var Add
     */
    protected $addClassic;

    /**
     *
     * @var Add
     */
    protected $addStandout;

    /**
     *
     * @var Add
     */
    protected $addPremium;

    public function setUp()
    {
        $this->createProducts();

        $this->createCustomerWithGroup();

        $this->createAndAssignRuleToCustomerGroup();

        $this->createCheckoutWithItem();
    }

    public function test_group_consist_of_customers()
    {
        $this->assertCount(1, $this->groupOne->getCustomers());
    }

    public function test_group_has_correct_customer()
    {
        $customer = $this->groupFour->getCustomers()->get(0);

        $this->assertEquals('FORD', $customer->getName());
    }

    public function test_group_has_checkout_rule()
    {
        $this->assertCount(1, $this->groupThree->getCheckoutRules());
    }

    public function test_group_checkout_rule_has_correct_code()
    {
        $checkoutRule = $this->groupOne->getCheckoutRules()->get(0);

        $this->assertEquals('buy2classicget1', $checkoutRule->getCode());
    }

    public function test_group_checkout_rule_has_correct_n_total()
    {
        $this->assertCount(3, $this->groupFour->getCheckoutRules());
    }

    public function test_checkout_consist_of_items()
    {
        $this->assertCount(3, $this->checkout->items);
    }

    public function test_checkout_has_correct_total_without_rule()
    {
        $this->assertEquals(987.97, $this->checkout->getTotal());
    }

    public function test_checkout_has_correct_total_of_default_group_and_without_rule()
    {
        $this->checkout = new Checkout();

        $this->checkout->setCustomer(new Customer(6, 'AIG'));

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addClassic, 1));
        
        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addStandout, 1));

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addPremium, 1));

        $this->assertEquals(987.97, $this->checkout->getTotal()); //assert paid amount
    }

    public function test_checkout_has_correct_total_with_rule_apply_buy_x_get_y()
    {
        $this->checkout = new Checkout();

        $this->checkout->setCustomer(new Customer(1, 'UNILEVER'), $this->groupOne);

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addClassic, 3));

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addPremium, 1));

        $this->checkout->applyRule('buy2classicget1');

        $this->assertEquals(934.97, $this->checkout->getTotal()); //assert paid amount
    }

    public function test_checkout_has_correct_total_with_rule_apply_buy_x_get_y_2()
    {
        $this->checkout = new Checkout();

        $this->checkout->setCustomer(new Customer(4, 'FORD'), $this->groupFour);

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addClassic, 5));

        $this->checkout->applyRule('buy4classicget1');

        $this->assertEquals(($this->addClassic->getPrice() * 4), $this->checkout->getTotal()); //assert paid amount
    }

    public function test_checkout_has_incorrect_total_with_rule_apply_buy_x_get_y_and_non_customer_group()
    {
        $this->checkout = new Checkout();

        $this->checkout->setCustomer(new Customer(4, 'FORD'), $this->groupOne);

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addClassic, 3));

        $this->checkout->applyRule('buy2classicget1');

        $this->assertNotEquals(($this->addClassic->getPrice() * 2), $this->checkout->getTotal()); //assert paid amount
    }

    public function test_checkout_has_correct_total_with_rule_apply_fixed_price_discount_amount()
    {
        $this->checkout = new Checkout();

        $this->checkout->setCustomer(new Customer(2, 'APPLE'), $this->groupTwo);

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addStandout, 3));

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addPremium, 1));

        $this->checkout->applyRule('buystandoutoff23each');

        $this->assertEquals(1294.96, $this->checkout->getTotal()); //assert paid amount
    }

    public function test_checkout_has_correct_total_with_rule_apply_fixed_price_discount_amount_of_quantity_greater_than_or_equal()
    {
        $this->checkout = new Checkout();

        $this->checkout->setCustomer(new Customer(3, 'NIKE'), $this->groupThree);

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addPremium, 4));

        $this->checkout->applyRule('buy4ormorepremiumget15offeach');

        $this->assertEquals(1519.96, $this->checkout->getTotal()); //assert paid amount
    }

    public function createCheckoutWithItem()
    {
        $this->checkout = new Checkout();

        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addClassic));
        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addStandout));
        $this->checkout->addCheckoutItem(new \Seek\Checkout\CheckoutItem($this->addPremium));
    }

    public function createProducts()
    {
        $this->addClassic  = new Add('classic', 269.99);
        $this->addStandout = new Add('standout', 322.99);
        $this->addPremium  = new Add('premium', 394.99);
    }

    public function createCustomerWithGroup()
    {
        $this->groupOne = new CustomerGroup(1, collect([new Customer(1, 'UNILEVER')]));

        $this->groupTwo = new CustomerGroup(2, collect([new Customer(2, 'APPLE')]));

        $this->groupThree = new CustomerGroup(3, collect([new Customer(3, 'NIKE')]));

        $this->groupFour = new CustomerGroup(4, collect([new Customer(4, 'FORD')]));
    }

    public function createAndAssignRuleToCustomerGroup()
    {
        
        $addClassic = new Add('classic', 269.99);

        $this->groupOne->setCheckoutRule(new Rule('buy2classicget1', [
            'rules'  => [
                [
                    'property' => [
                        'key'   => 'name',
                        'value' => 'classic'
                    ]
                ]
            ],
            'action' => [
                'type'   => Rule::BUY_X_GET_Y,
                'step'   => 2,
                'amount' => $addClassic->price
            ]
        ]));

        $this->groupTwo->setCheckoutRule(new Rule('buystandoutoff23each', [
            'rules'  => [
                [
                    'property' => [
                        'key'   => 'name',
                        'value' => 'standout'
                    ]
                ]
            ],
            'action' => [
                'type'   => Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT,
                'amount' => 23
            ]
        ]));

        $this->groupThree->setCheckoutRule(new Rule('buy4ormorepremiumget15offeach', [
            'rules'  => [
                [
                    'property'   => [
                        'key'   => 'name',
                        'value' => 'premium'
                    ],
                    'conditions' => [
                        [
                            'field' => 'quantity',
                            'type'  => 'gteq',
                            'value' => 4
                        ]
                    ]
                ]
            ],
            'action' => [
                'type'   => Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT,
                'amount' => 15
            ]
        ]));

        $this->groupFour->setCheckoutRule(new Rule('buy4classicget1', [
            'rules'  => [
                [
                    'property' => [
                        'key'   => 'name',
                        'value' => 'classic'
                    ]
                ]
            ],
            'action' => [
                'type'   => Rule::BUY_X_GET_Y,
                'step'   => 4,
                'amount' => $addClassic->price
            ]
        ]));

        $this->groupFour->setCheckoutRule(new Rule('buystandoutoff13each', [
            'rules'  => [
                [
                    'property' => [
                        'key'   => 'name',
                        'value' => 'standout'
                    ]
                ]
            ],
            'action' => [
                'type'   => Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT,
                'amount' => 13
            ]
        ]));

        $this->groupFour->setCheckoutRule(new Rule('buy3ormorepremiumget5offeach', [
            'rules'  => [
                [
                    'property'   => [
                        'key'   => 'name',
                        'value' => 'premium'
                    ],
                    'conditions' => [
                        [
                            'field' => 'quantity',
                            'type'  => 'gteq',
                            'value' => 3
                        ]
                    ]
                ]
            ],
            'action' => [
                'type'   => Rule::FIXED_OF_PRICE_AMOUNT_DISCOUNT,
                'amount' => 5
            ]
        ]));
    }

}

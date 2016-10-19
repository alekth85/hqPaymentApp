<?php

require_once('../vendor/autoload.php');
require_once('../includes/config.php');

// Paypal sdk has it's own test classes, I wrote only for my own classes as an example.
// Look at ClassDetermine.php and ClassDatabase.php
class PayPalPaymentTest extends \PHPUnit_Framework_TestCase
{
    function testPay()
    {
        $this->assertTrue(true);
    }
}
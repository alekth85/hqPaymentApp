<?php

require_once('../vendor/autoload.php');
require_once('../includes/config.php');

class DatermineTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @dataProvider providerPayTrueMethod
    */
    public function testpayMethodTRUE($currency, $card)
    {
        $determine = new Determine;
        $ret = $determine->payMethod($currency, $card);
        $this->assertTrue($ret[0]);
    }
    
    /**
    * @dataProvider providerPayFalseMethod
    */
    public function testpayMethodFALSE($currency, $card)
    {
        $determine = new Determine;
        $ret = $determine->payMethod($currency, $card);
        $this->assertFalse($ret[0]);
    }
    
    public function providerPayTrueMethod()
    {
        $a = [];
        $a[] = array('usd', 'visa');
        $a[] = array('eur', 'visa');
        $a[] = array('usd', 'amex');
        return ($a);
    }
    
    public function providerPayFalseMethod()
    {
        $a = [];
        $a[] = array('thb', 'amex');
        $a[] = array('aud', 'amex');
        $a[] = array('eur', 'amex');
        $a[] = array('hkd', 'amex');
        $a[] = array('shd', 'amex');
        return ($a);
    }
    
}
?>
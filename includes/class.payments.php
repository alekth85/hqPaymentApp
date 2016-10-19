<?php

abstract class pApi
{
    public $payment_url;
	public $success_msg = "Payment verified. Click <a href='list.php'>Here</a> to see list of payments";
	public $failure_msg = "Payment error. Some information missing or incorrect according to our gateway";
	public $gen_failure_msg = "General failure message. Please try again or contact administrator.";
	private $db;
	private $gateway = null; // $this->gateway will serve as a gateway indicator for sql insert.
	private $transId;
    
    abstract public function insertToSQL();
	abstract public function getTransID();
	abstract public function setTransID($id);
    
}
		

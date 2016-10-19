<?php

class BrainTreePayment extends pApi
{
    private $firstName = null;
    private $lastName = null;
    private $expMonth = null;
    private $expYear = null;
    private $gateway = "braintree";
    private $db;
    private $transId;
    
    function __construct()
    {
        $this->db = new Database;
    }
    
    function setName()
    {
        $e = explode(" ", $_POST['ccfullname']);
        if (isset($e[0]))
        {
            $this->firstName = $e[0];
        }
        
        if (isset($e[1]))
        {
            $this->lastName = $e[1];
        }
    }
    
    function getName()
    {
        // Name must always exist
        if ($this->firstName != null && $this->lastName != null )
        {
            return array($this->firstName, $this->lastName);
        }        
    }
    
    function pay()
    {
        $name = $this->getName();
        $fName = $name[0];
        $lName = $name[1];
        
        $result = Braintree_Transaction::sale([
            'amount' => $_POST['price'],
            'orderId' => uniqid(),
            'creditCard' => [
                'number' => $_POST['ccnumber'],
                'expirationDate' => $_POST['ccexpire'],
                "cvv" => $_POST['cccvv2']
            ],
            'customer' => [
                'firstName' => $fName,
                'lastName' => $lName
            ]
        ]);
        
        if ($result->success == false)
        {
            echo $result->message;
            return array(false, $result->message);
        }
        
        if ($result->transaction->processorResponseText == "Approved")
        {
            echo $this->success_msg;
            $this->setTransID($result->transaction->orderId);
            $this->insertToSQL();
            return array(true, '');
        }
        
        echo $this->gen_failure_msg;
        return array(false, 'general failure');
        
        #var_dump($result->transaction->processorResponseText);
        #var_dump($result);
        #var_dump($_POST);
        
    }
    
    public function setTransID($id)
    {
        $this->transId = $id;
    }
    
    public function getTransID()
    {
        return $this->transId;
    }
    
    public function insertToSQL()
    {
        // I'm not doing anything to check fields. In case of sqllite we would use sqllite_escape_string
        // and in case of mysql / postgresql - we would use prepared statements, to prevent sql injections.
        // But for this example I'll just assume it's all proper and insert whatever you write in DB.
        // I don't generally use sqllite at all, it's just for ease of installation as this doesn't require
        // any settings.
        
        $ccfullname = $_POST['ccfullname'];
        $cctype     = $_POST['cctype'];
        $ccnumber   = $_POST['ccnumber'];
        $ccexpire   = $_POST['ccexpire'];
        $cccvv      = $_POST['cccvv'];
        $price      = $_POST['price'];
        $currency   = $_POST['currency'];
        $transId    = $this->getTransID;
        $gateway    = $this->gateway;
        $sql = "INSERT INTO hq_payments (full_name, cctype, ccnumber, ccexpire, cccvv, price, currency, verified, payid, gateway)
                VALUES ('$ccfullname', '$cctype', '$ccnumber', '$ccexpire', '$cccvv', '$price', '$currency', 1, '$this->transId', '$this->gateway' )";
        $this->db->exec($sql);
    }
    
}
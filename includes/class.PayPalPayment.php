<?php

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\Details;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;


class PayPalPayment extends pApi
{
    private $apiContext;
    public $payer;
    public $item;
    public $item2;
    public $itemList;
    public $details;
    public $amount;
    public $transaction;
    public $redirUrls;
    public $payment;
    public $fi;
    public $card;
    private $db;
    private $transId;
    public static $returl_success;
    public static $returl_failure;
    
    private $gateway = "paypal";
    
    public function __construct()
    {
        // Set api context.
        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                PAYPAL_API_KEY,     // ClientID
                PAYPAL_API_SEC      // ClientSecret
            )
        );
        
        // Initialize paypal classes    
        $this->payer    = new Payer();
        $this->item     = new Item();
        $this->item2    = new Item();
        $this->itemList = new ItemList();
        $this->details  = new Details();
        $this->amount   = new Amount();
        $this->transaction  = new Transaction();
        $this->redirUrls    = new RedirectUrls();
        $this->payment  = new Payment();
        $this->card     = new CreditCard();
        $this->fi       = new FundingInstrument();
        $this->db       = new Database();
    }
    
    public function setCreditCard()
    {
        
        $ex = explode("/", $_POST['ccexpire']);
        $ccExpireMonth = $ex[0];
        $ccExpireYear = $ex[1];
        unset($ex);
                
        $ex = explode(" ", $_POST['ccfullname']);
        $ccFirstName = $ex[0];
        $ccLastName = $ex[1];
        
        $ccdata = array('type' => $_POST['cctype'],
                        'number' => $_POST['ccnumber'],
                        'expmonth' => $ccExpireMonth,
                        'expyear' => $ccExpireYear,
                        'cvv2' => '865',
                        'first_name' => $ccFirstName,
                        'last_name' => $ccLastName);
        
        $this->card->setType($ccdata['type'])
            ->setNumber($ccdata["number"])
            ->setExpireMonth($ccdata['expmonth'])
            ->setExpireYear($ccdata['expyear'])
            ->setCvv2($ccdata['cvv2'])
            ->setFirstName($ccdata['first_name'])
            ->setLastName($ccdata['last_name']);
                
            #$this->fi->setCreditCard($this->card);
        
    }
    
    public function setPayer($method = "paypal")
    {
        if ($method == "paypal")
        {
            $this->payer->setPaymentMethod($method);
        } else {
            $this->fi->setCreditCard($this->card);
            $this->payer->setPaymentMethod("credit_card")->setFundingInstruments(array($this->fi));
        }
    }
    
    public function setItem()
    {
        //$elem = $_POST;
        $elem = array('item' => 'HQ Test Item',
                    'currency' => $_POST['currency'],
                    'quantity' => 1,
                    'sku' => '1221',
                    'price' => $_POST['price']);
        
        $this->item->setName($elem['item'])
            ->setDescription($elem['item'])
            ->setCurrency($elem['currency'])
            ->setQuantity($elem['quantity'])
            ->setTax(CONFIG_TAX)
            ->setPrice($elem['price']);
    }
    
    public function setItemList()
    {
        // Only support for one item.
        $this->itemList->setItems(array($this->item));
    }

    public function setDetails()
    {
        #$elem = $_POST;
        $elem = array('shipping' => CONFIG_SHIPPING,
                    'tax' => CONFIG_TAX,
                    'subtotal' => $_POST['price']);
        
        $this->details->setShipping($elem['shipping'])
            ->setTax($elem['tax'])
            ->setSubtotal($elem['subtotal']);
    }

    public function setAmount()
    {
        #$elem = $_POST;
        $elem = array('total' => $_POST['price'], 'currency' => 'USD');
        $this->amount->setCurrency($elem['currency'])
            ->setTotal($elem['total'])   
            ->setDetails($this->details);
    }
    
    public function setTransaction()
    {
        $this->transaction->setAmount($this->amount) 
                ->setItemList($this->itemList)
                ->setDescription("payment descr")
                ->setInvoiceNumber(uniqid());
    }
    
    public function setRedirUrls()
    {
        $baseUrl = getBaseUrl();
        $this->returl_success = PAYPAL_SUCCESS_URL;
        $this->return_failure = PAYPAL_FAILURE_URL;
        $this->redirUrls->setReturnUrl("$baseUrl" . RETURN_SUCCESS)->setCancelUrl("$baseUrl" . RETURN_FAILURE);
    }

    public function setPayment()
    {
        $this->payment->setIntent("sale")
            ->setPayer($this->payer) 
            ->setRedirectUrls($this->redirectUrls)
            ->setTransactions(array($this->transaction));
    }

    public function getPayment()
    {
        try {
            $pay = $this->payment;
            $pay->create($this->apiContext);
            $message = null;
            
            if (gettype($pay) != "object" && isset($pay[1])) {
                $message = $this->failure_msg;
            } else {
                $message = $this->success_msg . ' - ';
                $this->setTransID($pay->getId() );
                $this->insertToSQL();
            }

            return array(true, $message);
        } catch (Exception $ex) {
            $this->errors = true;
            return array(false, "error code - " . $ex->getCode() . ' Click back and re-check if data is correct.');
        }
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
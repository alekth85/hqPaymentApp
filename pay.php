<?php

// This will load everything. It's a small example app, so I didn't bother with different setup.
// It could be done dozen different ways, with or without classes. I just chose this one because how it's done
// is usually influenced by the context of the greater application around it.

require_once(__DIR__ . '/includes/config.php');
$determine = new Determine();
$method = null;

// Main code
// Could be made to be more dynamic, like pluggable modules type of thing
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $fieldSet = array('price','currency','fullname','ccfullname','ccnumber','ccexpire','cccvv2', 'cctype');
    
    foreach ($fieldSet as $key => $val)
    {
        if (!array_key_exists($val, $_POST))
        {
            echo "Forgot something, like field $val for example ?";
            die();
        }
    }
    
    $method = $determine->payMethod($_POST['currency'], $_POST['cctype']);
    if ($method != null && $method[0] == true)
    {
        switch ($method[1])
        {
            case 'paypal':
                
                $errors = null;
                
                // Paypal SDK specific stuff.
                // Honestly in retrospect, I think i shouldn't have used the SDK and just go directly with the API
                // This SDK is huge. Compare this to braintree payment.
                
                $payPal = new PayPalPayment;
                $payPal->setCreditCard();
                $payPal->setPayer("credit_card");
                $payPal->setItem();
                $payPal->setItemList();
                $payPal->setDetails();
                $payPal->setAmount();
                $payPal->setTransaction();
                $payPal->setPayment();
                $py = $payPal->getPayment();
                
                if (isset($py[0]))
                {
                    echo $py[1];
                }
                break;
            
            case 'braintree':
                $bTree = new BrainTreePayment;
                $bTree->pay();
                break;
        }
    }
    
    // Errors ?
    if ($method != null && $method[0] == false)
    {
        echo $method[1];
    }

}

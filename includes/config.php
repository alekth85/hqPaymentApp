<?php

/*
 * This is loading all .. irrelevant of which payment is in use
 * This code loading should be actually put in a different file, and leave this just for config
 * But for this example app
*/

// APP Settings
DEFINE('APP_DIR', '/sites/lb.geek.rs.ba/htdocs/paypal/'); // Put trailing slash
DEFINE('APP_URL', 'http://lb.geek.rs.ba/paypal/');

DEFINE('LISTURL', APP_URL . 'list.php');

require_once __DIR__  . '/../vendor/autoload.php';

// Paypal settings, init
DEFINE('PAYPAL_API_KEY', 'AZwXvTtt5oN9guGnoAAGWs1OgS8Zcec9bFDUXMXfvYAZ0Yjt5byTZ1rIjXbe6Wf8NBVpd_ZM8gEWGO7F');
DEFINE('PAYPAL_API_SEC', 'ENCPeUpaytcjoxlyt8bBLlr-ICBOiz0Cj0nbf7B-Yf_4kf4S8Ru3DB2s1disR8LP-kl5mGukYzADOQRm');

// These would normally go to DB... but for this test, here's in config
DEFINE('CONFIG_TAX', 0.00);
DEFINE('CONFIG_SHIPPING', 0.00);

// SQLLITE3 DB FILE (MUST BE WRITEABLE BY WHATEVER IS RUNNING PHP)
DEFINE('DBPATH', 'db/hq.db');

// PHP7 allows arrays in DEFINE, which makes sense to use.. but I'll go with a var instead
// Keep it all lowercase.
//$RULES = array('paypal' => array('currencies' => array('usd','eur','aud')), 'cards' => array('all') );
//$RULES[] = array('braintree' => array('currencies' => array( '!usd', '!eur', '!aud') )
$cards      = array('visa','mastercard','amex');
$currencies = array('usd', 'eur', 'aud', 'thb', 'hkd', 'sgd');

// Come to think of it.. the second array could just be string.. oh well.
$rules = array('currencies' => array('usd'      => array('paypal'),
                                     'eur'      => array('paypal'),
                                     'aud'      => array('paypal'),
                                     'hkd'      => array('braintree'),
                                     'thb'      => array('braintree'),
                                     'sgd'      => array('braintree'),
                                     'default'  => array('braintree')),
               'cards'      => array('amex'     => array('paypal'),
                                     'visa'     => array('paypal'),
                                     'default'  => array('paypal'))
);

// Card->Currency
$rules['special'] = array('amex:usd');

// Serialize arrays
DEFINE('CURRENCIES', serialize($currencies));
DEFINE('CARDS', serialize($cards));
DEFINE('RULES', serialize($rules));

// Braintree settings, init
Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('rs6z732vrjjxjnj7');
Braintree_Configuration::publicKey('g59v8yd5fxq4cbp2');
Braintree_Configuration::privateKey('1293c6f38713dc9567183089f6134fe8');

require_once APP_DIR . 'includes/class.payments.php';

// Payment classes.
require_once APP_DIR . 'includes/class.PayPalPayment.php';
require_once APP_DIR . 'includes/class.BrainTreePayment.php';

// Database class
require_once APP_DIR . 'includes/class.database.php';

// Determination class
require_once APP_DIR . 'includes/class.Determine.php';

// We'll init DB here even, so it's automatically available everywhere.
// Some people frown upon static classes, but actually I like it more than this for example, but for this example
// i think doing it like this is ok.
$db = new Database();


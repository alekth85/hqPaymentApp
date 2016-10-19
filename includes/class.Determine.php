<?php

// There are some messages in this .. class, hardcoded.. in real project the messages would go to a different
// Class or a config file.

class Determine
{
    public function payMethod($currency = null, $card = null)
    {
        if ($currency != null && $card != null)
        {
            $currency   = strtolower($currency);
            $card       = strtolower($card);
            $method     = null;
            $currencies = unserialize(CURRENCIES);
            $cards      = unserialize(CARDS);
            $rules      = unserialize(RULES);
            
            // Check if supported currency first.
            if (!in_array($currency, $currencies))
            {
                #var_dump($currencies);
                return array(false, 'Wrong currency');
            }
            
            // Check if supported card
            if (!in_array($card, $cards))
            {
                return array(false, 'Wrong card');
            }
            
            // Check for special rules, such as amex rule.
            if (array_key_exists($currency, $rules['currencies']))
            {
                // Check the special rules first.
                foreach ($rules['special'] as $skey => $sval)
                {
                    // amex:usd ..
                    $e = explode(":", $sval);
                    #echo "Checking $card against " . $e[0] . "<br>";
                    #echo "Checking $currency against " . $e[1] ."<br>";
                    if ($e[0] == $card && $e[1] != $currency)
                    {
                        #var_dump($_POST);
                        return array(false, ucfirst($e[0]) . ' cards can be only used with USD Currency.');
                    }
                }
            }
            
            // Go through general currency rules.
            foreach ($rules['currencies'] as $key => $val)
            {
                if ($currency == $key && $key != 'default')
                {
                    // [0] Because we made a second array in config file to hold another array, instead of stirng..
                    $method = $val[0];
                }
            }
            
            // Nothing has been met. Fallback
            if ($method == null)
            {
                $method = $rules['currencies']['default'][0];
            }
            
            return array(true, $method);
        
        }
        
        return array(false, 'No data provided.');
    }
}
<?php

namespace ccxt;

use Exception; // a common import

class bequant extends hitbtc {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bequant',
            'name' => 'Bequant',
            'countries' => array( 'MT' ), // Malta
            'pro' => true,
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/55248342-a75dfe00-525a-11e9-8aa2-05e9dca943c6.jpg',
                'api' => array(
                    'public' => 'https://api.bequant.io',
                    'private' => 'https://api.bequant.io',
                ),
                'www' => 'https://bequant.io',
                'doc' => array(
                    'https://api.bequant.io/',
                ),
                'fees' => array(
                    'https://bequant.io/fees-and-limits',
                ),
                'referral' => 'https://bequant.io',
            ),
        ));
    }
}

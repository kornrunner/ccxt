<?php

namespace ccxt;

use Exception as Exception; // a common import

class bequant extends hitbtc2 {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bequant',
            'name' => 'Bequant',
            'countries' => array ( 'MT' ), // Malta
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/55248342-a75dfe00-525a-11e9-8aa2-05e9dca943c6.jpg',
                'api' => 'https://api.bequant.io',
                'www' => 'https://bequant.io',
                'doc' => array (
                    'https://api.bequant.io/',
                ),
                'fees' => array (
                    'https://bequant.io/fees-and-limits',
                ),
            ),
        ));
    }
}

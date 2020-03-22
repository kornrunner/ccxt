<?php

namespace ccxt;

use Exception; // a common import

class chilebit extends foxbit {

    public function describe() {
        return array_replace_recursive(parent::describe (), array(
            'id' => 'chilebit',
            'name' => 'ChileBit',
            'countries' => array( 'CL' ),
            'has' => array(
                'CORS' => false,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27991414-1298f0d8-647f-11e7-9c40-d56409266336.jpg',
                'api' => array(
                    'public' => 'https://api.blinktrade.com/api',
                    'private' => 'https://api.blinktrade.com/tapi',
                ),
                'www' => 'https://chilebit.net',
                'doc' => 'https://blinktrade.com/docs',
            ),
            'options' => array(
                'brokerId' => '9', // https://blinktrade.com/docs/#brokers
            ),
        ));
    }
}

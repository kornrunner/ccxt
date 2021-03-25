<?php

namespace ccxt;

use Exception; // a common import

class bitmax extends ascendex {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitmax',
            'name' => 'BitMax',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/66820319-19710880-ef49-11e9-8fbe-16be62a11992.jpg',
                'api' => 'https://bitmax.io',
                'test' => 'https://bitmax-test.io',
                'www' => 'https://bitmax.io',
                'doc' => array(
                    'https://bitmax-exchange.github.io/bitmax-pro-api/#bitmax-pro-api-documentation',
                ),
                'fees' => 'https://bitmax.io/#/feeRate/tradeRate',
                'referral' => 'https://bitmax.io/#/register?inviteCode=EL6BXBQM',
            ),
        ));
    }
}

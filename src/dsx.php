<?php

namespace ccxt;

use Exception; // a common import

class dsx extends hitbtc {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'dsx',
            'name' => 'DSX',
            'countries' => array( 'UK' ),
            'rateLimit' => 100,
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/76909626-cb2bb100-68bc-11ea-99e0-28ba54f04792.jpg',
                'api' => array(
                    'public' => 'https://api.dsxglobal.com',
                    'private' => 'https://api.dsxglobal.com',
                ),
                'www' => 'http://dsxglobal.com',
                'doc' => array(
                    'https://api.dsxglobal.com',
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'maker' => 0.15 / 100,
                    'taker' => 0.25 / 100,
                ),
            ),
        ));
    }
}

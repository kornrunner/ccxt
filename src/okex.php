<?php

namespace ccxt;

use Exception; // a common import

class okex extends okcoinusd {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'okex',
            'name' => 'OKEX',
            'countries' => array ( 'CN', 'US' ),
            'has' => array (
                'CORS' => false,
                'futures' => true,
                'fetchTickers' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/32552768-0d6dd3c6-c4a6-11e7-90f8-c043b64756a7.jpg',
                'api' => array (
                    'web' => 'https://www.okex.com/v2',
                    'public' => 'https://www.okex.com/api',
                    'private' => 'https://www.okex.com/api',
                ),
                'www' => 'https://www.okex.com',
                'doc' => array (
                    'https://github.com/okcoin-okex/API-docs-OKEx.com',
                    'https://www.okex.com/docs/en/',
                ),
                'fees' => 'https://www.okex.com/pages/products/fees.html',
                'referral' => 'https://www.okex.com',
            ),
            'fees' => array (
                'trading' => array (
                    'taker' => 0.0015,
                    'maker' => 0.0010,
                ),
                'spot' => array (
                    'taker' => 0.0015,
                    'maker' => 0.0010,
                ),
                'future' => array (
                    'taker' => 0.0005,
                    'maker' => 0.0002,
                ),
                'swap' => array (
                    'taker' => 0.00075,
                    'maker' => 0.0002,
                ),
            ),
            'commonCurrencies' => array (
                // OKEX refers to ERC20 version of Aeternity (AEToken)
                'AE' => 'AET', // https://github.com/ccxt/ccxt/issues/4981
                'HOT' => 'Hydro Protocol',
                'HSR' => 'HC',
                'MAG' => 'Maggie',
                'YOYO' => 'YOYOW',
                'WIN' => 'WinToken', // https://github.com/ccxt/ccxt/issues/5701
            ),
        ));
    }
}

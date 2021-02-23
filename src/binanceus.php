<?php

namespace ccxt;

use Exception; // a common import

class binanceus extends binance {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'binanceus',
            'name' => 'Binance US',
            'countries' => array( 'US' ), // US
            'certified' => false,
            'pro' => true,
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/65177307-217b7c80-da5f-11e9-876e-0b748ba0a358.jpg',
                'api' => array(
                    'web' => 'https://www.binance.us',
                    'wapi' => 'https://api.binance.us/wapi/v3',
                    'public' => 'https://api.binance.us/api/v1',
                    'private' => 'https://api.binance.us/api/v3',
                    'v3' => 'https://api.binance.us/api/v3',
                    'v1' => 'https://api.binance.us/api/v1',
                ),
                'www' => 'https://www.binance.us',
                'referral' => 'https://www.binance.us/?ref=35005074',
                'doc' => 'https://github.com/binance-us/binance-official-api-docs',
                'fees' => 'https://www.binance.us/en/fee/schedule',
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.001, // 0.1% trading fee, zero fees for all trading pairs before November 1
                    'maker' => 0.001, // 0.1% trading fee, zero fees for all trading pairs before November 1
                ),
            ),
            'options' => array(
                'quoteOrderQty' => false,
            ),
        ));
    }

    public function fetch_currencies($params = array ()) {
        return null;
    }
}

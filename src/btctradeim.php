<?php

namespace ccxt;

class btctradeim extends coinegg {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'btctradeim',
            'name' => 'BtcTrade.im',
            'countries' => 'HK',
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/36770531-c2142444-1c5b-11e8-91e2-a4d90dc85fe8.jpg',
                'api' => array (
                    'web' => 'https://www.btctrade.im/coin',
                    'rest' => 'https://api.btctrade.im/api/v1',
                ),
                'www' => 'https://www.btctrade.im',
                'doc' => 'https://www.btctrade.im/help.api.html',
                'fees' => 'https://www.btctrade.im/spend.price.html',
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array (
                    'withdraw' => array (
                        'BTC' => 0.001,
                    ),
                ),
            ),
        ));
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if ($api === 'web') {
            return $response;
        }
        $data = $this->safe_value($response, 'data');
        if ($data) {
            $code = $this->safe_string($response, 'code');
            if ($code !== '0') {
                $message = $this->safe_string($response, 'msg', 'Error');
                throw new ExchangeError ($message);
            }
            return $data;
        }
        return $response;
    }
}

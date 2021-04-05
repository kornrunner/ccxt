<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class mixcoins extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'mixcoins',
            'name' => 'MixCoins',
            'countries' => array( 'GB', 'HK' ),
            'rateLimit' => 1500,
            'version' => 'v1',
            'userAgent' => $this->userAgents['chrome'],
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87460810-1dd06c00-c616-11ea-9276-956f400d6ffa.jpg',
                'api' => 'https://mixcoins.com/api',
                'www' => 'https://mixcoins.com',
                'doc' => 'https://mixcoins.com/help/api/',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ticker/',
                        'trades/',
                        'depth/',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'cancel',
                        'info',
                        'orders',
                        'order',
                        'transactions',
                        'trade',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/USDT' => array( 'id' => 'btc_usdt', 'symbol' => 'BTC/USDT', 'base' => 'BTC', 'quote' => 'USDT', 'baseId' => 'btc', 'quoteId' => 'usdt', 'maker' => 0.0015, 'taker' => 0.0025 ),
                'ETH/BTC' => array( 'id' => 'eth_btc', 'symbol' => 'ETH/BTC', 'base' => 'ETH', 'quote' => 'BTC', 'baseId' => 'eth', 'quoteId' => 'btc', 'maker' => 0.001, 'taker' => 0.0015 ),
                'BCH/BTC' => array( 'id' => 'bch_btc', 'symbol' => 'BCH/BTC', 'base' => 'BCH', 'quote' => 'BTC', 'baseId' => 'bch', 'quoteId' => 'btc', 'maker' => 0.001, 'taker' => 0.0015 ),
                'LSK/BTC' => array( 'id' => 'lsk_btc', 'symbol' => 'LSK/BTC', 'base' => 'LSK', 'quote' => 'BTC', 'baseId' => 'lsk', 'quoteId' => 'btc', 'maker' => 0.0015, 'taker' => 0.0025 ),
                'BCH/USDT' => array( 'id' => 'bch_usdt', 'symbol' => 'BCH/USDT', 'base' => 'BCH', 'quote' => 'USDT', 'baseId' => 'bch', 'quoteId' => 'usdt', 'maker' => 0.001, 'taker' => 0.0015 ),
                'ETH/USDT' => array( 'id' => 'eth_usdt', 'symbol' => 'ETH/USDT', 'base' => 'ETH', 'quote' => 'USDT', 'baseId' => 'eth', 'quoteId' => 'usdt', 'maker' => 0.001, 'taker' => 0.0015 ),
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostInfo ($params);
        $balances = $this->safe_value($response['result'], 'wallet');
        $result = array( 'info' => $response );
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $balance = $this->safe_value($balances, $currencyId, array());
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'avail');
            $account['used'] = $this->safe_number($balance, 'lock');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
        );
        $response = $this->publicGetDepth (array_merge($request, $params));
        return $this->parse_order_book($response['result']);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $ticker = $this->safe_value($response, 'result');
        $timestamp = $this->milliseconds();
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $id = $this->safe_string($trade, 'id');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => null,
            'order' => null,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
            'op' => $side,
            'amount' => $amount,
        );
        if ($type === 'market') {
            $request['order_type'] = 1;
            $request['price'] = $price;
        } else {
            $request['order_type'] = 0;
        }
        $response = $this->privatePostTrade (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => (string) $response['result']['id'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        return $this->privatePostCancel (array_merge($request, $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array(
                'nonce' => $nonce,
            ), $params));
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $this->hmac($this->encode($body), $this->secret, 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('status', $response)) {
            //
            // todo add a unified standard handleErrors with $this->exceptions in describe()
            //
            //     array("status":503,"message":"Maintenancing, try again later","result":null)
            //
            if ($response['status'] === 200) {
                return $response;
            }
        }
        throw new ExchangeError($this->id . ' ' . $this->json($response));
    }
}

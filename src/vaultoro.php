<?php

namespace ccxt;

use Exception; // a common import

class vaultoro extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'vaultoro',
            'name' => 'Vaultoro',
            'countries' => array ( 'CH' ),
            'rateLimit' => 1000,
            'version' => '1',
            'has' => array (
                'CORS' => true,
                'fetchMarkets' => true,
                'fetchOrderBook' => true,
                'fetchBalance' => true,
                'createOrder' => true,
                'cancelOrder' => true,
                'fetchTrades' => true,
                'fetchTicker' => false,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766880-f205e870-5ee9-11e7-8fe2-0d5b15880752.jpg',
                'api' => 'https://api.vaultoro.com',
                'www' => 'https://www.vaultoro.com',
                'doc' => 'https://api.vaultoro.com',
            ),
            'commonCurrencies' => array (
                'GLD' => 'Gold',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'bidandask',
                        'buyorders',
                        'latest',
                        'latesttrades',
                        'markets',
                        'orderbook',
                        'sellorders',
                        'transactions/day',
                        'transactions/hour',
                        'transactions/month',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'balance',
                        'mytrades',
                        'orders',
                    ),
                    'post' => array (
                        'buy/{symbol}/{type}',
                        'cancel/{id}',
                        'sell/{symbol}/{type}',
                        'withdraw',
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $result = array();
        $response = $this->publicGetMarkets ($params);
        $market = $this->safe_value($response, 'data');
        $baseId = $this->safe_string($market, 'MarketCurrency');
        $quoteId = $this->safe_string($market, 'BaseCurrency');
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        $symbol = $base . '/' . $quote;
        $id = $this->safe_string($market, 'MarketName');
        $result[] = array (
            'id' => $id,
            'symbol' => $symbol,
            'base' => $base,
            'quote' => $quote,
            'baseId' => $baseId,
            'quoteId' => $quoteId,
            'info' => $market,
        );
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetBalance ($params);
        $balances = $this->safe_value($response, 'data');
        $result = array( 'info' => $balances );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency_code');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'cash');
            $account['used'] = $this->safe_float($balance, 'reserved');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetOrderbook ($params);
        $orderbook = array (
            'bids' => $response['data'][0]['b'],
            'asks' => $response['data'][1]['s'],
        );
        return $this->parse_order_book($orderbook, null, 'bids', 'asks', 'Gold_Price', 'Gold_Amount');
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->parse8601 ($this->safe_string($trade, 'Time'));
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $price = $this->safe_float($trade, 'Gold_Price');
        $amount = $this->safe_float($trade, 'Gold_Amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        return array (
            'id' => null,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'order' => null,
            'type' => null,
            'side' => null,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTransactionsDay ($params);
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $method = 'privatePost' . $this->capitalize ($side) . 'SymbolType';
        $request = array (
            'symbol' => strtolower($market['quoteId']),
            'type' => $type,
            'gld' => $amount,
            'price' => $price || 1,
        );
        $response = $this->$method (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $response['data']['Order_ID'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'id' => $id,
        );
        return $this->privatePostCancelId (array_merge ($request, $params));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/';
        if ($api === 'public') {
            $url .= $path;
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $url .= $this->version . '/' . $this->implode_params($path, $params);
            $query = array_merge (array (
                'nonce' => $nonce,
                'apikey' => $this->apiKey,
            ), $this->omit ($params, $this->extract_params($path)));
            $url .= '?' . $this->urlencode ($query);
            $headers = array (
                'Content-Type' => 'application/json',
                'X-Signature' => $this->hmac ($this->encode ($url), $this->encode ($this->secret)),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

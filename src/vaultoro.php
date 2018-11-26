<?php

namespace ccxt;

use Exception as Exception; // a common import

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
        $result = array ();
        $markets = $this->publicGetMarkets ();
        $market = $markets['data'];
        $baseId = $market['MarketCurrency'];
        $quoteId = $market['BaseCurrency'];
        $base = $this->common_currency_code($baseId);
        $quote = $this->common_currency_code($quoteId);
        $symbol = $base . '/' . $quote;
        $id = $market['MarketName'];
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
        $response = $this->privateGetBalance ();
        $balances = $response['data'];
        $result = array ( 'info' => $balances );
        for ($b = 0; $b < count ($balances); $b++) {
            $balance = $balances[$b];
            $currencyId = strtoupper ($balance['currency_code']);
            $code = $currencyId;
            if (is_array ($this->currencies_by_id[$currencyId]) && array_key_exists ($currencyId, $this->currencies_by_id[$currencyId]))
                $code = $this->currencies_by_id[$currencyId]['code'];
            $free = $balance['cash'];
            $used = $balance['reserved'];
            $total = $this->sum ($free, $used);
            $account = array (
                'free' => $free,
                'used' => $used,
                'total' => $total,
            );
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

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $quote = $this->publicGetBidandask ($params);
        $bidsLength = is_array ($quote['bids']) ? count ($quote['bids']) : 0;
        $bid = $quote['bids'][$bidsLength - 1];
        $ask = $quote['asks'][0];
        $response = $this->publicGetMarkets ($params);
        $ticker = $response['data'];
        $timestamp = $this->milliseconds ();
        $last = $this->safe_float($ticker, 'LastPrice');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, '24hHigh'),
            'low' => $this->safe_float($ticker, '24hLow'),
            'bid' => $bid[0],
            'bidVolume' => null,
            'ask' => $ask[0],
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $this->safe_float($ticker, '24hVolume'),
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market) {
        $timestamp = $this->parse8601 ($trade['Time']);
        return array (
            'id' => null,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $market['symbol'],
            'order' => null,
            'type' => null,
            'side' => null,
            'price' => $trade['Gold_Price'],
            'amount' => $trade['Gold_Amount'],
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
        $response = $this->$method (array_merge (array (
            'symbol' => strtolower ($market['quoteId']),
            'type' => $type,
            'gld' => $amount,
            'price' => $price || 1,
        ), $params));
        return array (
            'info' => $response,
            'id' => $response['data']['Order_ID'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        return $this->privatePostCancelId (array_merge (array (
            'id' => $id,
        ), $params));
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
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

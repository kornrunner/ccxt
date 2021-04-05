<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\BadSymbol;
use \ccxt\NotSupported;

class bitstamp1 extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitstamp1',
            'name' => 'Bitstamp',
            'countries' => array( 'GB' ),
            'rateLimit' => 1000,
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMyTrades' => true,
                'fetchOrder' => false,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27786377-8c8ab57e-5fe9-11e7-8ea4-2b05b6bcceec.jpg',
                'api' => 'https://www.bitstamp.net/api',
                'www' => 'https://www.bitstamp.net',
                'doc' => 'https://www.bitstamp.net/api',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ticker',
                        'ticker_hour',
                        'order_book',
                        'transactions',
                        'eur_usd',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'balance',
                        'user_transactions',
                        'open_orders',
                        'order_status',
                        'cancel_order',
                        'cancel_all_orders',
                        'buy',
                        'sell',
                        'bitcoin_deposit_address',
                        'unconfirmed_btc',
                        'ripple_withdrawal',
                        'ripple_address',
                        'withdrawal_requests',
                        'bitcoin_withdrawal',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/USD' => array( 'id' => 'btcusd', 'symbol' => 'BTC/USD', 'base' => 'BTC', 'quote' => 'USD', 'baseId' => 'btc', 'quoteId' => 'usd', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTC/EUR' => array( 'id' => 'btceur', 'symbol' => 'BTC/EUR', 'base' => 'BTC', 'quote' => 'EUR', 'baseId' => 'btc', 'quoteId' => 'eur', 'maker' => 0.005, 'taker' => 0.005 ),
                'EUR/USD' => array( 'id' => 'eurusd', 'symbol' => 'EUR/USD', 'base' => 'EUR', 'quote' => 'USD', 'baseId' => 'eur', 'quoteId' => 'usd', 'maker' => 0.005, 'taker' => 0.005 ),
                'XRP/USD' => array( 'id' => 'xrpusd', 'symbol' => 'XRP/USD', 'base' => 'XRP', 'quote' => 'USD', 'baseId' => 'xrp', 'quoteId' => 'usd', 'maker' => 0.005, 'taker' => 0.005 ),
                'XRP/EUR' => array( 'id' => 'xrpeur', 'symbol' => 'XRP/EUR', 'base' => 'XRP', 'quote' => 'EUR', 'baseId' => 'xrp', 'quoteId' => 'eur', 'maker' => 0.005, 'taker' => 0.005 ),
                'XRP/BTC' => array( 'id' => 'xrpbtc', 'symbol' => 'XRP/BTC', 'base' => 'XRP', 'quote' => 'BTC', 'baseId' => 'xrp', 'quoteId' => 'btc', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/USD' => array( 'id' => 'ltcusd', 'symbol' => 'LTC/USD', 'base' => 'LTC', 'quote' => 'USD', 'baseId' => 'ltc', 'quoteId' => 'usd', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/EUR' => array( 'id' => 'ltceur', 'symbol' => 'LTC/EUR', 'base' => 'LTC', 'quote' => 'EUR', 'baseId' => 'ltc', 'quoteId' => 'eur', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/BTC' => array( 'id' => 'ltcbtc', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'baseId' => 'ltc', 'quoteId' => 'btc', 'maker' => 0.005, 'taker' => 0.005 ),
                'ETH/USD' => array( 'id' => 'ethusd', 'symbol' => 'ETH/USD', 'base' => 'ETH', 'quote' => 'USD', 'baseId' => 'eth', 'quoteId' => 'usd', 'maker' => 0.005, 'taker' => 0.005 ),
                'ETH/EUR' => array( 'id' => 'etheur', 'symbol' => 'ETH/EUR', 'base' => 'ETH', 'quote' => 'EUR', 'baseId' => 'eth', 'quoteId' => 'eur', 'maker' => 0.005, 'taker' => 0.005 ),
                'ETH/BTC' => array( 'id' => 'ethbtc', 'symbol' => 'ETH/BTC', 'base' => 'ETH', 'quote' => 'BTC', 'baseId' => 'eth', 'quoteId' => 'btc', 'maker' => 0.005, 'taker' => 0.005 ),
            ),
        ));
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        if ($symbol !== 'BTC/USD') {
            throw new ExchangeError($this->id . ' ' . $this->version . " fetchOrderBook doesn't support " . $symbol . ', use it for BTC/USD only');
        }
        $this->load_markets();
        $orderbook = $this->publicGetOrderBook ($params);
        $timestamp = $this->safe_timestamp($orderbook, 'timestamp');
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        if ($symbol !== 'BTC/USD') {
            throw new ExchangeError($this->id . ' ' . $this->version . " fetchTicker doesn't support " . $symbol . ', use it for BTC/USD only');
        }
        $this->load_markets();
        $ticker = $this->publicGetTicker ($params);
        $timestamp = $this->safe_timestamp($ticker, 'timestamp');
        $vwap = $this->safe_number($ticker, 'vwap');
        $baseVolume = $this->safe_number($ticker, 'volume');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
        }
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => $this->safe_number($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp_2($trade, 'date', 'datetime');
        $side = ($trade['type'] === 0) ? 'buy' : 'sell';
        $orderId = $this->safe_string($trade, 'order_id');
        if (is_array($trade) && array_key_exists('currency_pair', $trade)) {
            if (is_array($this->markets_by_id) && array_key_exists($trade['currency_pair'], $this->markets_by_id)) {
                $market = $this->markets_by_id[$trade['currency_pair']];
            }
        }
        $id = $this->safe_string($trade, 'tid');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        if ($symbol !== 'BTC/USD') {
            throw new BadSymbol($this->id . ' ' . $this->version . " fetchTrades doesn't support " . $symbol . ', use it for BTC/USD only');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'time' => 'minute',
        );
        $response = $this->publicGetTransactions (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $balance = $this->privatePostBalance ($params);
        $result = array( 'info' => $balance );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $currency = $this->currency($code);
            $currencyId = $currency['id'];
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, $currencyId . '_available');
            $account['used'] = $this->safe_number($balance, $currencyId . '_reserved');
            $account['total'] = $this->safe_number($balance, $currencyId . '_balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' ' . $this->version . ' accepts limit orders only');
        }
        if ($symbol !== 'BTC/USD') {
            throw new ExchangeError($this->id . ' v1 supports BTC/USD orders only');
        }
        $this->load_markets();
        $method = 'privatePost' . $this->capitalize($side);
        $request = array(
            'amount' => $amount,
            'price' => $price,
        );
        $response = $this->$method (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        return $this->privatePostCancelOrder (array( 'id' => $id ));
    }

    public function parse_order_status($status) {
        $statuses = array(
            'In Queue' => 'open',
            'Open' => 'open',
            'Finished' => 'closed',
            'Canceled' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function fetch_order_status($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privatePostOrderStatus (array_merge($request, $params));
        return $this->parse_order_status($response);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $pair = $market ? $market['id'] : 'all';
        $request = array(
            'id' => $pair,
        );
        $response = $this->privatePostOpenOrdersId (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        throw new NotSupported($this->id . ' fetchOrder is not implemented yet');
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $auth = $nonce . $this->uid . $this->apiKey;
            $signature = $this->encode($this->hmac($this->encode($auth), $this->encode($this->secret)));
            $query = array_merge(array(
                'key' => $this->apiKey,
                'signature' => strtoupper($signature),
                'nonce' => $nonce,
            ), $query);
            $body = $this->urlencode($query);
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('status', $response)) {
            if ($response['status'] === 'error') {
                throw new ExchangeError($this->id . ' ' . $this->json($response));
            }
        }
        return $response;
    }
}

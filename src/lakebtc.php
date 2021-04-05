<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class lakebtc extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'lakebtc',
            'name' => 'LakeBTC',
            'countries' => array( 'US' ),
            'version' => 'api_v2',
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/28074120-72b7c38a-6660-11e7-92d9-d9027502281d.jpg',
                'api' => 'https://api.lakebtc.com',
                'www' => 'https://www.lakebtc.com',
                'doc' => array(
                    'https://www.lakebtc.com/s/api_v2',
                    'https://www.lakebtc.com/s/api',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'bcorderbook',
                        'bctrades',
                        'ticker',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'buyOrder',
                        'cancelOrders',
                        'getAccountInfo',
                        'getExternalAccounts',
                        'getOrders',
                        'getTrades',
                        'openOrders',
                        'sellOrder',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.15 / 100,
                    'taker' => 0.2 / 100,
                ),
            ),
            'exceptions' => array(
                'broad' => array(
                    'Signature' => '\\ccxt\\AuthenticationError',
                    'invalid symbol' => '\\ccxt\\BadSymbol',
                    'Volume doit' => '\\ccxt\\InvalidOrder',
                    'insufficient_balance' => '\\ccxt\\InsufficientFunds',
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetTicker ($params);
        $result = array();
        $keys = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $market = $response[$id];
            $baseId = mb_substr($id, 0, 3 - 0);
            $quoteId = mb_substr($id, 3, 6 - 3);
            $base = strtoupper($baseId);
            $quote = strtoupper($quoteId);
            $symbol = $base . '/' . $quote;
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => null,
                'precision' => $this->precision,
                'limits' => $this->limits,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetAccountInfo ($params);
        $balances = $this->safe_value($response, 'balance', array());
        $result = array( 'info' => $response );
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_number($balances, $currencyId);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        $response = $this->publicGetBcorderbook (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
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
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $marketId = $ids[$i];
            $ticker = $response[$marketId];
            $market = $this->safe_market($marketId);
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $tickers = $this->publicGetTicker ($params);
        return $this->parse_ticker($tickers[$market['id']], $market);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
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

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetBctrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $method = 'privatePost' . $this->capitalize($side) . 'Order';
        $market = $this->market($symbol);
        $order = array(
            'params' => [ $price, $amount, $market['id'] ],
        );
        $response = $this->$method (array_merge($order, $params));
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'id'),
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'params' => array( $id ),
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function nonce() {
        return $this->microseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version;
        if ($api === 'public') {
            $url .= '/' . $path;
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $nonceAsString = (string) $nonce;
            $requestId = $this->seconds();
            $queryParams = '';
            if (is_array($params) && array_key_exists('params', $params)) {
                $paramsList = $params['params'];
                $stringParams = array();
                for ($i = 0; $i < count($paramsList); $i++) {
                    $param = $paramsList[$i];
                    if (gettype($paramsList) !== 'string') {
                        $param = (string) $param;
                    }
                    $stringParams[] = $param;
                }
                $queryParams = implode(',', $stringParams);
                $body = array(
                    'method' => $path,
                    'params' => $params['params'],
                    'id' => $requestId,
                );
            } else {
                $body = array(
                    'method' => $path,
                    'params' => '',
                    'id' => $requestId,
                );
            }
            $body = $this->json($body);
            $query = array(
                'tonce=' . $nonceAsString,
                'accesskey=' . $this->apiKey,
                'requestmethod=' . strtolower($method),
                'id=' . (string) $requestId,
                'method=' . $path,
                'params=' . $queryParams,
            );
            $query = implode('&', $query);
            $signature = $this->hmac($this->encode($query), $this->encode($this->secret), 'sha1');
            $auth = $this->apiKey . ':' . $signature;
            $signature64 = $this->decode(base64_encode($auth));
            $headers = array(
                'Json-Rpc-Tonce' => $nonceAsString,
                'Authorization' => 'Basic ' . $signature64,
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to the default $error handler
        }
        //
        //     array("$error":"Failed to submit order => invalid symbol")
        //     array("$error":"Failed to submit order => La validation a échoué : Volume doit être supérieur ou égal à 1.0")
        //     array("$error":"Failed to submit order => insufficient_balance")
        //
        $feedback = $this->id . ' ' . $body;
        $error = $this->safe_string($response, 'error');
        if ($error !== null) {
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $error, $feedback);
            throw new ExchangeError($feedback); // unknown message
        }
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ArgumentsRequired;

class coinfalcon extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinfalcon',
            'name' => 'CoinFalcon',
            'countries' => array( 'GB' ),
            'rateLimit' => 1000,
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/41822275-ed982188-77f5-11e8-92bb-496bcd14ca52.jpg',
                'api' => 'https://coinfalcon.com',
                'www' => 'https://coinfalcon.com',
                'doc' => 'https://docs.coinfalcon.com',
                'fees' => 'https://coinfalcon.com/fees',
                'referral' => 'https://coinfalcon.com/?ref=CFJSVGTUPASB',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'markets',
                        'markets/{market}/orders',
                        'markets/{market}/trades',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'user/accounts',
                        'user/orders',
                        'user/orders/{id}',
                        'user/trades',
                    ),
                    'post' => array(
                        'user/orders',
                    ),
                    'delete' => array(
                        'user/orders/{id}',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'maker' => 0.0,
                    'taker' => 0.002, // tiered fee starts at 0.2%
                ),
            ),
            'precision' => array(
                'amount' => 8,
                'price' => 8,
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetMarkets ($params);
        $markets = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            list($baseId, $quoteId) = explode('-', $market['name']);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'size_precision'),
                'price' => $this->safe_integer($market, 'price_precision'),
            );
            $result[] = array(
                'id' => $market['name'],
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $marketId = $this->safe_string($ticker, 'name');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $timestamp = $this->milliseconds();
        $last = floatval($ticker['last_price']);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_float($ticker, 'change_in_24h'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $this->safe_float($ticker, 'volume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $tickers = $this->fetch_tickers($params);
        return $tickers[$symbol];
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetMarkets ($params);
        $tickers = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $this->parse_ticker($tickers[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
            'level' => '3',
        );
        $response = $this->publicGetMarketsMarketOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order_book($data, null, 'bids', 'asks', 'price', 'size');
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'size');
        $symbol = $market['symbol'];
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = floatval($this->cost_to_precision($symbol, $price * $amount));
            }
        }
        $tradeId = $this->safe_string($trade, 'id');
        $side = $this->safe_string($trade, 'side');
        $orderId = $this->safe_string($trade, 'order_id');
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyCode = $this->safe_string($trade, 'fee_currency_code');
            $fee = array(
                'cost' => $feeCost,
                'currency' => $this->safe_currency_code($feeCurrencyCode),
            );
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $tradeId,
            'order' => $orderId,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($since !== null) {
            $request['start_time'] = $this->iso8601($since);
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetUserTrades (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($since !== null) {
            $request['since'] = $this->iso8601($since);
        }
        $response = $this->publicGetMarketsMarketTrades (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetUserAccounts ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'data');
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency_code');
            $code = $this->safe_currency_code($currencyId);
            $account = array(
                'free' => $this->safe_float($balance, 'available_balance'),
                'used' => $this->safe_float($balance, 'hold_balance'),
                'total' => $this->safe_float($balance, 'balance'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'fulfilled' => 'closed',
            'canceled' => 'canceled',
            'pending' => 'open',
            'open' => 'open',
            'partially_filled' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "id":"8bdd79f4-8414-40a2-90c3-e9f4d6d1eef4"
        //         "$market":"IOT-BTC"
        //         "$price":"0.0000003"
        //         "size":"4.0"
        //         "size_filled":"3.0"
        //         "fee":"0.0075"
        //         "fee_currency_code":"iot"
        //         "funds":"0.0"
        //         "$status":"canceled"
        //         "order_type":"buy"
        //         "post_only":false
        //         "operation_type":"market_order"
        //         "created_at":"2018-01-12T21:14:06.747828Z"
        //     }
        //
        $marketId = $this->safe_string($order, 'market');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'size');
        $filled = $this->safe_float($order, 'size_filled');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $type = $this->safe_string($order, 'operation_type');
        if ($type !== null) {
            $type = explode('_', $type);
            $type = $type[0];
        }
        $side = $this->safe_string($order, 'order_type');
        $postOnly = $this->safe_value($order, 'post_only');
        return $this->safe_order(array(
            'id' => $this->safe_string($order, 'id'),
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => $postOnly,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'trades' => null,
            'fee' => null,
            'info' => $order,
            'lastTradeTimestamp' => null,
            'average' => null,
        ));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        // price/size must be string
        $request = array(
            'market' => $market['id'],
            'size' => $this->amount_to_precision($symbol, $amount),
            'order_type' => $side,
        );
        if ($type === 'limit') {
            $price = $this->price_to_precision($symbol, $price);
            $request['price'] = (string) $price;
        }
        $request['operation_type'] = $type . '_order';
        $response = $this->privatePostUserOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privateDeleteUserOrdersId (array_merge($request, $params));
        $market = $this->market($symbol);
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data, $market);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetUserOrdersId (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['market'] = $market['id'];
        }
        if ($since !== null) {
            $request['since_time'] = $this->iso8601($since);
        }
        // TODO => test status=all if it works for closed $orders too
        $response = $this->privateGetUserOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        $orders = $this->filter_by_array($data, 'status', array( 'pending', 'open', 'partially_filled' ), false);
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/api/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $request .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            if ($method === 'GET') {
                if ($query) {
                    $request .= '?' . $this->urlencode($query);
                }
            } else {
                $body = $this->json($query);
            }
            $seconds = (string) $this->seconds();
            $payload = implode('|', array($seconds, $method, $request));
            if ($body) {
                $payload .= '|' . $body;
            }
            $signature = $this->hmac($this->encode($payload), $this->encode($this->secret));
            $headers = array(
                'CF-API-KEY' => $this->apiKey,
                'CF-API-TIMESTAMP' => $seconds,
                'CF-API-SIGNATURE' => $signature,
                'Content-Type' => 'application/json',
            );
        }
        $url = $this->urls['api'] . $request;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($code < 400) {
            return;
        }
        $ErrorClass = $this->safe_value(array(
            '401' => '\\ccxt\\AuthenticationError',
            '429' => '\\ccxt\\RateLimitExceeded',
        ), $code, '\\ccxt\\ExchangeError');
        throw new $ErrorClass($body);
    }
}

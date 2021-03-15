<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\OrderNotFound;

class oceanex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'oceanex',
            'name' => 'OceanEx',
            'countries' => array( 'US' ),
            'version' => 'v1',
            'rateLimit' => 3000,
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/58385970-794e2d80-8001-11e9-889c-0567cd79b78e.jpg',
                'api' => 'https://api.oceanex.pro',
                'www' => 'https://www.oceanex.pro.com',
                'doc' => 'https://api.oceanex.pro/doc/v1',
                'referral' => 'https://oceanex.pro/signup?referral=VE24QX',
            ),
            'has' => array(
                'fetchMarkets' => true,
                'fetchCurrencies' => false,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchOrderBook' => true,
                'fetchOrderBooks' => true,
                'fetchTrades' => true,
                'fetchTradingLimits' => false,
                'fetchTradingFees' => false,
                'fetchAllTradingFees' => true,
                'fetchFundingFees' => false,
                'fetchTime' => true,
                'fetchOrder' => true,
                'fetchOrders' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchBalance' => true,
                'createMarketOrder' => true,
                'createOrder' => true,
                'cancelOrder' => true,
                'cancelOrders' => true,
                'cancelAllOrders' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '4h' => '4h',
                '12h' => '12h',
                '1d' => '1d',
                '1w' => '1w',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'markets',
                        'tickers/{pair}',
                        'tickers_multi',
                        'order_book',
                        'order_book/multi',
                        'fees/trading',
                        'trades',
                        'timestamp',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'key',
                        'members/me',
                        'orders',
                        'orders/filter',
                    ),
                    'post' => array(
                        'orders',
                        'orders/multi',
                        'order/delete',
                        'order/delete/multi',
                        'orders/clear',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.1 / 100,
                    'taker' => 0.1 / 100,
                ),
            ),
            'commonCurrencies' => array(
                'PLA' => 'Plair',
            ),
            'exceptions' => array(
                'codes' => array(
                    '-1' => '\\ccxt\\BadRequest',
                    '-2' => '\\ccxt\\BadRequest',
                    '1001' => '\\ccxt\\BadRequest',
                    '1004' => '\\ccxt\\ArgumentsRequired',
                    '1006' => '\\ccxt\\AuthenticationError',
                    '1008' => '\\ccxt\\AuthenticationError',
                    '1010' => '\\ccxt\\AuthenticationError',
                    '1011' => '\\ccxt\\PermissionDenied',
                    '2001' => '\\ccxt\\AuthenticationError',
                    '2002' => '\\ccxt\\InvalidOrder',
                    '2004' => '\\ccxt\\OrderNotFound',
                    '9003' => '\\ccxt\\PermissionDenied',
                ),
                'exact' => array(
                    'market does not have a valid value' => '\\ccxt\\BadRequest',
                    'side does not have a valid value' => '\\ccxt\\BadRequest',
                    'Account::AccountError => Cannot lock funds' => '\\ccxt\\InsufficientFunds',
                    'The account does not exist' => '\\ccxt\\AuthenticationError',
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $request = array( 'show_details' => true );
        $response = $this->publicGetMarkets (array_merge($request, $params));
        $result = array();
        $markets = $this->safe_value($response, 'data');
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_value($market, 'id');
            $name = $this->safe_value($market, 'name');
            list($baseId, $quoteId) = explode('/', $name);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $baseId = strtolower($baseId);
            $quoteId = strtolower($quoteId);
            $symbol = $base . '/' . $quote;
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'info' => $market,
                'precision' => array(
                    'amount' => $this->safe_value($market, 'amount_precision'),
                    'price' => $this->safe_value($market, 'price_precision'),
                    'base' => $this->safe_value($market, 'ask_precision'),
                    'quote' => $this->safe_value($market, 'bid_precision'),
                ),
                'limits' => array(
                    'amount' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $this->safe_value($market, 'minimum_trading_amount'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        $response = $this->publicGetTickersPair (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "message":"Operation successful",
        //         "$data" => {
        //             "at":1559431729,
        //             "ticker" => {
        //                 "buy":"0.0065",
        //                 "sell":"0.00677",
        //                 "low":"0.00677",
        //                 "high":"0.00677",
        //                 "last":"0.00677",
        //                 "vol":"2000.0"
        //             }
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ticker($data, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        if ($symbols === null) {
            $symbols = $this->symbols;
        }
        $marketIds = $this->market_ids($symbols);
        $request = array( 'markets' => $marketIds );
        $response = $this->publicGetTickersMulti (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "message":"Operation successful",
        //         "$data" => {
        //             "at":1559431729,
        //             "$ticker" => {
        //                 "buy":"0.0065",
        //                 "sell":"0.00677",
        //                 "low":"0.00677",
        //                 "high":"0.00677",
        //                 "last":"0.00677",
        //                 "vol":"2000.0"
        //             }
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $ticker = $data[$i];
            $marketId = $this->safe_string($ticker, 'market');
            $market = $this->safe_market($marketId);
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function parse_ticker($data, $market = null) {
        //
        //         {
        //             "at":1559431729,
        //             "$ticker" => {
        //                 "buy":"0.0065",
        //                 "sell":"0.00677",
        //                 "low":"0.00677",
        //                 "high":"0.00677",
        //                 "last":"0.00677",
        //                 "vol":"2000.0"
        //             }
        //         }
        //
        $ticker = $this->safe_value($data, 'ticker', array());
        $timestamp = $this->safe_timestamp($data, 'at');
        return array(
            'symbol' => $market['symbol'],
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $this->safe_float($ticker, 'last'),
            'last' => $this->safe_float($ticker, 'last'),
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetOrderBook (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "message":"Operation successful",
        //         "data" => {
        //             "$timestamp":1559433057,
        //             "asks" => [
        //                 ["100.0","20.0"],
        //                 ["4.74","2000.0"],
        //                 ["1.74","4000.0"],
        //             ],
        //             "bids":[
        //                 ["0.0065","5482873.4"],
        //                 ["0.00649","4781956.2"],
        //                 ["0.00648","2876006.8"],
        //             ],
        //         }
        //     }
        //
        $orderbook = $this->safe_value($response, 'data', array());
        $timestamp = $this->safe_timestamp($orderbook, 'timestamp');
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function fetch_order_books($symbols = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbols === null) {
            $symbols = $this->symbols;
        }
        $marketIds = $this->market_ids($symbols);
        $request = array(
            'markets' => $marketIds,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetOrderBookMulti (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "message":"Operation successful",
        //         "$data" => [
        //             array(
        //                 "$timestamp":1559433057,
        //                 "market" => "bagvet",
        //                 "asks" => [
        //                     ["100.0","20.0"],
        //                     ["4.74","2000.0"],
        //                     ["1.74","4000.0"],
        //                 ],
        //                 "bids":[
        //                     ["0.0065","5482873.4"],
        //                     ["0.00649","4781956.2"],
        //                     ["0.00648","2876006.8"],
        //                 ],
        //             ),
        //             ...,
        //         ],
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $orderbook = $data[$i];
            $marketId = $this->safe_string($orderbook, 'market');
            $symbol = $this->safe_symbol($marketId);
            $timestamp = $this->safe_timestamp($orderbook, 'timestamp');
            $result[$symbol] = $this->parse_order_book($orderbook, $timestamp);
        }
        return $result;
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        $data = $this->safe_value($response, 'data');
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        $side = $this->safe_value($trade, 'side');
        if ($side === 'bid') {
            $side = 'buy';
        } else if ($side === 'ask') {
            $side = 'sell';
        }
        $marketId = $this->safe_value($trade, 'market');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->safe_timestamp($trade, 'created_on');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $this->safe_string($trade, 'id'),
            'order' => null,
            'type' => 'limit',
            'takerOrMaker' => null,
            'side' => $side,
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'volume'),
            'cost' => null,
            'fee' => null,
        );
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetTimestamp ($params);
        //
        //     array("code":0,"message":"Operation successful","data":1559433420)
        //
        return $this->safe_timestamp($response, 'data');
    }

    public function fetch_all_trading_fees($params = array ()) {
        $response = $this->publicGetFeesTrading ($params);
        $data = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $group = $data[$i];
            $maker = $this->safe_value($group, 'ask_fee', array());
            $taker = $this->safe_value($group, 'bid_fee', array());
            $marketId = $this->safe_string($group, 'market');
            $symbol = $this->safe_symbol($marketId);
            $result[$symbol] = array(
                'info' => $group,
                'symbol' => $symbol,
                'maker' => $this->safe_float($maker, 'value'),
                'taker' => $this->safe_float($taker, 'value'),
            );
        }
        return $result;
    }

    public function fetch_key($params = array ()) {
        $response = $this->privateGetKey ($params);
        return $this->safe_value($response, 'data');
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetMembersMe ($params);
        $data = $this->safe_value($response, 'data');
        $balances = $this->safe_value($data, 'accounts');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_value($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'balance');
            $account['used'] = $this->safe_float($balance, 'locked');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'side' => $side,
            'ord_type' => $type,
            'volume' => $this->amount_to_precision($symbol, $amount),
        );
        if ($type === 'limit') {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'data');
        return $this->parse_order($data, $market);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $ids = $id;
        if (gettype($id) === 'array' && count(array_filter(array_keys($id), 'is_string')) != 0) {
            $ids = array( $id );
        }
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $request = array( 'ids' => $ids );
        $response = $this->privateGetOrders (array_merge($request, $params));
        $data = $this->safe_value($response, 'data');
        $dataLength = is_array($data) ? count($data) : 0;
        if ($data === null) {
            throw new OrderNotFound($this->id . ' could not found matching order');
        }
        if (gettype($id) === 'array' && count(array_filter(array_keys($id), 'is_string')) == 0) {
            return $this->parse_orders($data, $market);
        }
        if ($dataLength === 0) {
            throw new OrderNotFound($this->id . ' could not found matching order');
        }
        return $this->parse_order($data[0], $market);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'states' => array( 'wait' ),
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'states' => array( 'done', 'cancel' ),
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders() requires a `$symbol` argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $states = $this->safe_value($params, 'states', array( 'wait', 'done', 'cancel' ));
        $query = $this->omit($params, 'states');
        $request = array(
            'market' => $market['id'],
            'states' => $states,
            'need_price' => 'True',
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetOrdersFilter (array_merge($request, $query));
        $data = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $orders = $this->safe_value($data[$i], 'orders', array());
            $status = $this->parse_order_status($this->safe_value($data[$i], 'state'));
            $parsedOrders = $this->parse_orders($orders, $market, $since, $limit, array( 'status' => $status ));
            $result = $this->array_concat($result, $parsedOrders);
        }
        return $result;
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "created_at" => "2019-01-18T00:38:18Z",
        //         "trades_count" => 0,
        //         "remaining_volume" => "0.2",
        //         "price" => "1001.0",
        //         "created_on" => "1547771898",
        //         "side" => "buy",
        //         "volume" => "0.2",
        //         "state" => "wait",
        //         "ord_type" => "limit",
        //         "avg_price" => "0.0",
        //         "executed_volume" => "0.0",
        //         "id" => 473797,
        //         "$market" => "veteth"
        //     }
        //
        $status = $this->parse_order_status($this->safe_value($order, 'state'));
        $marketId = $this->safe_string_2($order, 'market', 'market_id');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->safe_timestamp($order, 'created_on');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        }
        return $this->safe_order(array(
            'info' => $order,
            'id' => $this->safe_string($order, 'id'),
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $this->safe_value($order, 'ord_type'),
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $this->safe_value($order, 'side'),
            'price' => $this->safe_float($order, 'price'),
            'stopPrice' => null,
            'average' => $this->safe_float($order, 'avg_price'),
            'amount' => $this->safe_float($order, 'volume'),
            'remaining' => $this->safe_float($order, 'remaining_volume'),
            'filled' => $this->safe_float($order, 'executed_volume'),
            'status' => $status,
            'cost' => null,
            'trades' => null,
            'fee' => null,
        ));
    }

    public function parse_order_status($status) {
        $statuses = array(
            'wait' => 'open',
            'done' => 'closed',
            'cancel' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function create_orders($symbol, $orders, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'orders' => $orders,
        );
        // $orders => [array("side":"buy", "volume":.2, "price":1001), array("side":"sell", "volume":0.2, "price":1002)]
        $response = $this->privatePostOrdersMulti (array_merge($request, $params));
        $data = $response['data'];
        return $this->parse_orders($data);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrderDelete (array_merge(array( 'id' => $id ), $params));
        $data = $this->safe_value($response, 'data');
        return $this->parse_order($data);
    }

    public function cancel_orders($ids, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrderDeleteMulti (array_merge(array( 'ids' => $ids ), $params));
        $data = $this->safe_value($response, 'data');
        return $this->parse_orders($data);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrdersClear ($params);
        $data = $this->safe_value($response, 'data');
        return $this->parse_orders($data);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($path === 'tickers_multi' || $path === 'order_book/multi') {
                $request = '?';
                $markets = $this->safe_value($params, 'markets');
                for ($i = 0; $i < count($markets); $i++) {
                    $request .= 'marketsarray()=' . $markets[$i] . '&';
                }
                $limit = $this->safe_value($params, 'limit');
                if ($limit !== null) {
                    $request .= 'limit=' . $limit;
                }
                $url .= $request;
            } else if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($api === 'private') {
            $this->check_required_credentials();
            $request = array(
                'uid' => $this->apiKey,
                'data' => $query,
            );
            // to set the private key:
            // $fs = require ('fs')
            // exchange.secret = $fs->readFileSync ('oceanex.pem', 'utf8')
            $jwt_token = $this->jwt($request, $this->encode($this->secret), 'RS256');
            $url .= '?user_jwt=' . $jwt_token;
        }
        $headers = array( 'Content-Type' => 'application/json' );
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        //
        //     array("$code":1011,"$message":"This IP '5.228.233.138' is not allowed","data":array())
        //
        if ($response === null) {
            return;
        }
        $errorCode = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message');
        if (($errorCode !== null) && ($errorCode !== '0')) {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['codes'], $errorCode, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            throw new ExchangeError($response);
        }
    }
}

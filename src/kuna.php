<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ArgumentsRequired;
use \ccxt\OrderNotFound;

class kuna extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'kuna',
            'name' => 'Kuna',
            'countries' => array( 'UA' ),
            'rateLimit' => 1000,
            'version' => 'v2',
            'has' => array(
                'CORS' => false,
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => 'emulated',
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'withdraw' => false,
            ),
            'timeframes' => null,
            'urls' => array(
                'extension' => '.json',
                'referral' => 'https://kuna.io?r=kunaid-gvfihe8az7o4',
                'logo' => 'https://user-images.githubusercontent.com/51840849/87153927-f0578b80-c2c0-11ea-84b6-74612568e9e1.jpg',
                'api' => 'https://kuna.io',
                'www' => 'https://kuna.io',
                'doc' => 'https://kuna.io/documents/api',
                'fees' => 'https://kuna.io/documents/api',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'depth', // Get depth or specified market Both asks and bids are sorted from highest price to lowest.
                        'k_with_pending_trades', // Get K data with pending trades, which are the trades not included in K data yet, because there's delay between trade generated and processed by K data generator
                        'k', // Get OHLC(k line) of specific market
                        'markets', // Get all available markets
                        'order_book', // Get the order book of specified market
                        'order_book/{market}',
                        'tickers', // Get ticker of all markets
                        'tickers/{market}', // Get ticker of specific market
                        'timestamp', // Get server current time, in seconds since Unix epoch
                        'trades', // Get recent trades on market, each trade is included only once Trades are sorted in reverse creation order.
                        'trades/{market}',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'members/me', // Get your profile and accounts info
                        'deposits', // Get your deposits history
                        'deposit', // Get details of specific deposit
                        'deposit_address', // Where to deposit The address field could be empty when a new address is generating (e.g. for bitcoin), you should try again later in that case.
                        'orders', // Get your orders, results is paginated
                        'order', // Get information of specified order
                        'trades/my', // Get your executed trades Trades are sorted in reverse creation order.
                        'withdraws', // Get your cryptocurrency withdraws
                        'withdraw', // Get your cryptocurrency withdraw
                    ),
                    'post' => array(
                        'orders', // Create a Sell/Buy order
                        'orders/multi', // Create multiple sell/buy orders
                        'orders/clear', // Cancel all my orders
                        'order/delete', // Cancel an order
                        'withdraw', // Create a withdraw
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.25 / 100,
                    'maker' => 0.25 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        'UAH' => '1%',
                        'BTC' => 0.001,
                        'BCH' => 0.001,
                        'ETH' => 0.01,
                        'WAVES' => 0.01,
                        'GOL' => 0.0,
                        'GBG' => 0.0,
                        // 'RMC' => 0.001 BTC
                        // 'ARN' => 0.01 ETH
                        // 'R' => 0.01 ETH
                        // 'EVR' => 0.01 ETH
                    ),
                    'deposit' => array(
                        // 'UAH' => (amount) => amount * 0.001 + 5
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'PLA' => 'Plair',
            ),
            'exceptions' => array(
                '2002' => '\\ccxt\\InsufficientFunds',
                '2003' => '\\ccxt\\OrderNotFound',
            ),
        ));
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetTimestamp ($params);
        //
        //     1594911427
        //
        return $response * 1000;
    }

    public function fetch_markets($params = array ()) {
        $quotes = array( 'btc', 'eth', 'eurs', 'rub', 'uah', 'usd', 'usdt', 'gol' );
        $pricePrecisions = array(
            'UAH' => 0,
        );
        $markets = array();
        $response = $this->publicGetTickers ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            for ($j = 0; $j < count($quotes); $j++) {
                $quoteId = $quotes[$j];
                $index = mb_strpos($id, $quoteId);
                $slice = mb_substr($id, $index);
                if (($index > 0) && ($slice === $quoteId)) {
                    $baseId = str_replace($quoteId, '', $id);
                    $base = $this->safe_currency_code($baseId);
                    $quote = $this->safe_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                    $precision = array(
                        'amount' => 6,
                        'price' => $this->safe_integer($pricePrecisions, $quote, 6),
                    );
                    $markets[] = array(
                        'id' => $id,
                        'symbol' => $symbol,
                        'base' => $base,
                        'quote' => $quote,
                        'baseId' => $baseId,
                        'quoteId' => $quoteId,
                        'precision' => $precision,
                        'limits' => array(
                            'amount' => array(
                                'min' => pow(10, -$precision['amount']),
                                'max' => pow(10, $precision['amount']),
                            ),
                            'price' => array(
                                'min' => pow(10, -$precision['price']),
                                'max' => pow(10, $precision['price']),
                            ),
                            'cost' => array(
                                'min' => null,
                                'max' => null,
                            ),
                        ),
                        'active' => null,
                        'info' => null,
                    );
                    break;
                }
            }
        }
        return $markets;
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 300
        }
        $orderbook = $this->publicGetDepth (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($orderbook, 'timestamp');
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'at');
        $ticker = $ticker['ticker'];
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
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
            'open' => $this->safe_number($ticker, 'open'),
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

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = null;
            $symbol = $id;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                $base = mb_substr($id, 0, 3 - 0);
                $quote = mb_substr($id, 3, 6 - 3);
                $base = strtoupper($base);
                $quote = strtoupper($quote);
                $base = $this->safe_currency_code($base);
                $quote = $this->safe_currency_code($quote);
                $symbol = $base . '/' . $quote;
            }
            $result[$symbol] = $this->parse_ticker($response[$id], $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetTickersMarket (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function fetch_l3_order_book($symbol, $limit = null, $params = array ()) {
        return $this->fetch_order_book($symbol, $limit, $params);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_string_2($trade, 'side', 'trend');
        if ($side !== null) {
            $sideMap = array(
                'ask' => 'sell',
                'bid' => 'buy',
            );
            $side = $this->safe_string($sideMap, $side, $side);
        }
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'volume');
        $cost = $this->safe_number($trade, 'funds');
        $orderId = $this->safe_string($trade, 'order_id');
        $id = $this->safe_string($trade, 'id');
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'order' => $orderId,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $trades = $this->fetch_trades($symbol, $since, $limit, $params);
        $ohlcvc = $this->build_ohlcvc($trades, $timeframe, $since, $limit);
        $result = array();
        for ($i = 0; $i < count($ohlcvc); $i++) {
            $ohlcv = $ohlcvc[$i];
            $result[] = [
                $ohlcv[0],
                $ohlcv[1],
                $ohlcv[2],
                $ohlcv[3],
                $ohlcv[4],
                $ohlcv[5],
            ];
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetMembersMe ($params);
        $balances = $this->safe_value($response, 'accounts');
        $result = array( 'info' => $balances );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'balance');
            $account['used'] = $this->safe_number($balance, 'locked');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
            'side' => $side,
            'volume' => (string) $amount,
            'ord_type' => $type,
        );
        if ($type === 'limit') {
            $request['price'] = (string) $price;
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        $marketId = $this->safe_value($response, 'market');
        $market = $this->safe_value($this->markets_by_id, $marketId);
        return $this->parse_order($response, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privatePostOrderDelete (array_merge($request, $params));
        $order = $this->parse_order($response);
        $status = $order['status'];
        if ($status === 'closed' || $status === 'canceled') {
            throw new OrderNotFound($this->id . ' ' . $this->json($order));
        }
        return $order;
    }

    public function parse_order_status($status) {
        $statuses = array(
            'done' => 'closed',
            'wait' => 'open',
            'cancel' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        $marketId = $this->safe_string($order, 'market');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side');
        $id = $this->safe_string($order, 'id');
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $this->safe_number($order, 'price'),
            'stopPrice' => null,
            'amount' => $this->safe_number($order, 'volume'),
            'filled' => $this->safe_number($order, 'executed_volume'),
            'remaining' => $this->safe_number($order, 'remaining_volume'),
            'trades' => null,
            'fee' => null,
            'info' => $order,
            'cost' => null,
            'average' => null,
        ));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => intval($id),
        );
        $response = $this->privateGetOrder (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->privateGetOrders (array_merge($request, $params));
        // todo emulation of fetchClosedOrders, fetchOrders, fetchOrder
        // with order cache . fetchOpenOrders
        // as in BTC-e, Liqui, Yobit, DSX, Tidex, WEX
        return $this->parse_orders($response, $market, $since, $limit);
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
        $response = $this->privateGetTradesMy (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function encode_params($params) {
        if (is_array($params) && array_key_exists('orders', $params)) {
            $orders = $params['orders'];
            $query = $this->urlencode($this->keysort($this->omit($params, 'orders')));
            for ($i = 0; $i < count($orders); $i++) {
                $order = $orders[$i];
                $keys = is_array($order) ? array_keys($order) : array();
                for ($k = 0; $k < count($keys); $k++) {
                    $key = $keys[$k];
                    $value = $order[$key];
                    $query .= '&$orders%5B%5D%5B' . $key . '%5D=' . (string) $value;
                }
            }
            return $query;
        }
        return $this->urlencode($this->keysort($params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/api/' . $this->version . '/' . $this->implode_params($path, $params);
        if (is_array($this->urls) && array_key_exists('extension', $this->urls)) {
            $request .= $this->urls['extension'];
        }
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->urls['api'] . $request;
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $query = $this->encode_params(array_merge(array(
                'access_key' => $this->apiKey,
                'tonce' => $nonce,
            ), $params));
            $auth = $method . '|' . $request . '|' . $query;
            $signed = $this->hmac($this->encode($auth), $this->encode($this->secret));
            $suffix = $query . '&signature=' . $signed;
            if ($method === 'GET') {
                $url .= '?' . $suffix;
            } else {
                $body = $suffix;
                $headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        if ($code === 400) {
            $error = $this->safe_value($response, 'error');
            $errorCode = $this->safe_string($error, 'code');
            $feedback = $this->id . ' ' . $this->json($response);
            $this->throw_exactly_matched_exception($this->exceptions, $errorCode, $feedback);
            // fallback to default $error handler
        }
    }
}

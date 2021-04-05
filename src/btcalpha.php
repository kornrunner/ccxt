<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\InvalidOrder;
use \ccxt\DDoSProtection;

class btcalpha extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'btcalpha',
            'name' => 'BTC-Alpha',
            'countries' => array( 'US' ),
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => false,
                'fetchTrades' => true,
            ),
            'timeframes' => array(
                '1m' => '1',
                '5m' => '5',
                '15m' => '15',
                '30m' => '30',
                '1h' => '60',
                '4h' => '240',
                '1d' => 'D',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/42625213-dabaa5da-85cf-11e8-8f99-aa8f8f7699f0.jpg',
                'api' => 'https://btc-alpha.com/api',
                'www' => 'https://btc-alpha.com',
                'doc' => 'https://btc-alpha.github.io/api-docs',
                'fees' => 'https://btc-alpha.com/fees/',
                'referral' => 'https://btc-alpha.com/?r=123788',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencies/',
                        'pairs/',
                        'orderbook/{pair_name}/',
                        'exchanges/',
                        'charts/{pair}/{type}/chart/',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'wallets/',
                        'orders/own/',
                        'order/{id}/',
                        'exchanges/own/',
                        'deposits/',
                        'withdraws/',
                    ),
                    'post' => array(
                        'order/',
                        'order-cancel/',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => 0.00135,
                        'LTC' => 0.0035,
                        'XMR' => 0.018,
                        'ZEC' => 0.002,
                        'ETH' => 0.01,
                        'ETC' => 0.01,
                        'SIB' => 1.5,
                        'CCRB' => 4,
                        'PZM' => 0.05,
                        'ITI' => 0.05,
                        'DCY' => 5,
                        'R' => 5,
                        'ATB' => 0.05,
                        'BRIA' => 0.05,
                        'KZC' => 0.05,
                        'HWC' => 1,
                        'SPA' => 1,
                        'SMS' => 0.001,
                        'REC' => 0.01,
                        'SUP' => 1,
                        'BQ' => 100,
                        'GDS' => 0.1,
                        'EVN' => 300,
                        'TRKC' => 0.01,
                        'UNI' => 1,
                        'STN' => 1,
                        'BCH' => null,
                        'QBIC' => 0.5,
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'CBC' => 'Cashbery',
            ),
            'exceptions' => array(
                'exact' => array(),
                'broad' => array(
                    'Out of balance' => '\\ccxt\\InsufficientFunds', // array("date":1570599531.4814300537,"error":"Out of balance -9.99243661 BTC")
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetPairs ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'name');
            $baseId = $this->safe_string($market, 'currency1');
            $quoteId = $this->safe_string($market, 'currency2');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => 8,
                'price' => $this->safe_integer($market, 'price_precision'),
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'active' => true,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_number($market, 'minimum_order_size'),
                        'max' => $this->safe_number($market, 'maximum_order_size'),
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
                'info' => $market,
                'baseId' => null,
                'quoteId' => null,
            );
        }
        return $result;
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair_name' => $this->market_id($symbol),
        );
        if ($limit) {
            $request['limit_sell'] = $limit;
            $request['limit_buy'] = $limit;
        }
        $response = $this->publicGetOrderbookPairName (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'buy', 'sell', 'price', 'amount');
    }

    public function parse_bids_asks($bidasks, $priceKey = 0, $amountKey = 1) {
        $result = array();
        for ($i = 0; $i < count($bidasks); $i++) {
            $bidask = $bidasks[$i];
            if ($bidask) {
                $result[] = $this->parse_bid_ask($bidask, $priceKey, $amountKey);
            }
        }
        return $result;
    }

    public function parse_trade($trade, $market = null) {
        $symbol = null;
        if ($market === null) {
            $market = $this->safe_value($this->marketsById, $trade['pair']);
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_timestamp($trade, 'timestamp');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = floatval($this->cost_to_precision($symbol, $price * $amount));
            }
        }
        $id = $this->safe_string_2($trade, 'id', 'tid');
        $side = $this->safe_string_2($trade, 'my_side', 'side');
        $orderId = $this->safe_string($trade, 'o_id');
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => 'limit',
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $trades = $this->publicGetExchanges (array_merge($request, $params));
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "time":1591296000,
        //         "open":0.024746,
        //         "close":0.024728,
        //         "low":0.024728,
        //         "high":0.024753,
        //         "volume":16.624
        //     }
        //
        return array(
            $this->safe_timestamp($ohlcv, 'time'),
            $this->safe_number($ohlcv, 'open'),
            $this->safe_number($ohlcv, 'high'),
            $this->safe_number($ohlcv, 'low'),
            $this->safe_number($ohlcv, 'close'),
            $this->safe_number($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '5m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
            'type' => $this->timeframes[$timeframe],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        if ($since !== null) {
            $request['since'] = intval($since / 1000);
        }
        $response = $this->publicGetChartsPairTypeChart (array_merge($request, $params));
        //
        //     array(
        //         array("time":1591296000,"open":0.024746,"close":0.024728,"low":0.024728,"high":0.024753,"volume":16.624),
        //         array("time":1591295700,"open":0.024718,"close":0.02475,"low":0.024711,"high":0.02475,"volume":31.645),
        //         array("time":1591295400,"open":0.024721,"close":0.024717,"low":0.024711,"high":0.02473,"volume":65.071)
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetWallets ($params);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['used'] = $this->safe_number($balance, 'reserve');
            $account['total'] = $this->safe_number($balance, 'balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order_status($status) {
        $statuses = array(
            '1' => 'open',
            '2' => 'canceled',
            '3' => 'closed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        $symbol = null;
        if ($market === null) {
            $market = $this->safe_value($this->marketsById, $order['pair']);
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_timestamp($order, 'date');
        $price = $this->safe_number($order, 'price');
        $amount = $this->safe_number($order, 'amount');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $id = $this->safe_string_2($order, 'oid', 'id');
        $trades = $this->safe_value($order, 'trades', array());
        $trades = $this->parse_trades($trades, $market);
        $side = $this->safe_string_2($order, 'my_side', 'type');
        $filled = null;
        $numTrades = is_array($trades) ? count($trades) : 0;
        if ($numTrades > 0) {
            $filled = 0.0;
            for ($i = 0; $i < $numTrades; $i++) {
                $filled = $this->sum($filled, $trades[$i]['amount']);
            }
        }
        $remaining = null;
        if (($amount !== null) && ($amount > 0) && ($filled !== null)) {
            $remaining = max (0, $amount - $filled);
        }
        return array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => $trades,
            'fee' => null,
            'info' => $order,
            'lastTradeTimestamp' => null,
            'average' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
            'type' => $side,
            'amount' => $amount,
            'price' => $this->price_to_precision($symbol, $price),
        );
        $response = $this->privatePostOrder (array_merge($request, $params));
        if (!$response['success']) {
            throw new InvalidOrder($this->id . ' ' . $this->json($response));
        }
        $order = $this->parse_order($response, $market);
        $amount = ($order['amount'] > 0) ? $order['amount'] : $amount;
        return array_merge($order, array(
            'amount' => $amount,
        ));
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'order' => $id,
        );
        $response = $this->privatePostOrderCancel (array_merge($request, $params));
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $order = $this->privateGetOrderId (array_merge($request, $params));
        return $this->parse_order($order);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $orders = $this->privateGetOrdersOwn (array_merge($request, $params));
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => '1',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => '3',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $trades = $this->privateGetExchangesOwn (array_merge($request, $params));
        return $this->parse_trades($trades, null, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = $this->urlencode($this->keysort($this->omit($params, $this->extract_params($path))));
        $url = $this->urls['api'] . '/';
        if ($path !== 'charts/{pair}/{type}/chart/') {
            $url .= 'v1/';
        }
        $url .= $this->implode_params($path, $params);
        $headers = array( 'Accept' => 'application/json' );
        if ($api === 'public') {
            if (strlen($query)) {
                $url .= '?' . $query;
            }
        } else {
            $this->check_required_credentials();
            $payload = $this->apiKey;
            if ($method === 'POST') {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                $body = $query;
                $payload .= $body;
            } else if (strlen($query)) {
                $url .= '?' . $query;
            }
            $headers['X-KEY'] = $this->apiKey;
            $headers['X-SIGN'] = $this->hmac($this->encode($payload), $this->encode($this->secret));
            $headers['X-NONCE'] = (string) $this->nonce();
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        //
        //     array("date":1570599531.4814300537,"$error":"Out of balance -9.99243661 BTC")
        //
        $error = $this->safe_string($response, 'error');
        $feedback = $this->id . ' ' . $body;
        if ($error !== null) {
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $error, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $error, $feedback);
        }
        if ($code === 401 || $code === 403) {
            throw new AuthenticationError($feedback);
        } else if ($code === 429) {
            throw new DDoSProtection($feedback);
        }
        if ($code < 400) {
            return;
        }
        throw new ExchangeError($feedback);
    }
}

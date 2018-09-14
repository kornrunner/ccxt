<?php

namespace ccxt;

use Exception as Exception; // a common import

class btcalpha extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'btcalpha',
            'name' => 'BTC-Alpha',
            'countries' => array ( 'US' ),
            'version' => 'v1',
            'has' => array (
                'fetchTicker' => false,
                'fetchOHLCV' => true,
                'fetchOrders' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchMyTrades' => true,
            ),
            'timeframes' => array (
                '1m' => '1',
                '5m' => '5',
                '15m' => '15',
                '30m' => '30',
                '1h' => '60',
                '4h' => '240',
                '1d' => 'D',
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/42625213-dabaa5da-85cf-11e8-8f99-aa8f8f7699f0.jpg',
                'api' => 'https://btc-alpha.com/api',
                'www' => 'https://btc-alpha.com',
                'doc' => 'https://btc-alpha.github.io/api-docs',
                'fees' => 'https://btc-alpha.com/fees/',
                'referral' => 'https://btc-alpha.com/?r=123788',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'currencies/',
                        'pairs/',
                        'orderbook/{pair_name}/',
                        'exchanges/',
                        'charts/{pair}/{type}/chart/',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'wallets/',
                        'orders/own/',
                        'order/{id}/',
                        'exchanges/own/',
                        'deposits/',
                        'withdraws/',
                    ),
                    'post' => array (
                        'order/',
                        'order-cancel/',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array (
                    'withdraw' => array (
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
        ));
    }

    public function fetch_markets () {
        $markets = $this->publicGetPairs ();
        $result = array ();
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $id = $market['name'];
            $base = $this->common_currency_code($market['currency1']);
            $quote = $this->common_currency_code($market['currency2']);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => 8,
                'price' => intval ($market['price_precision']),
            );
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'active' => true,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => floatval ($market['minimum_order_size']),
                        'max' => floatval ($market['maximum_order_size']),
                    ),
                    'price' => array (
                        'min' => pow (10, -$precision['price']),
                        'max' => pow (10, $precision['price']),
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'pair_name' => $this->market_id($symbol),
        );
        if ($limit) {
            $request['limit_sell'] = $limit;
            $request['limit_buy'] = $limit;
        }
        $reponse = $this->publicGetOrderbookPairName (array_merge ($request, $params));
        return $this->parse_order_book($reponse, null, 'buy', 'sell', 'price', 'amount');
    }

    public function parse_trade ($trade, $market = null) {
        $symbol = null;
        if (!$market)
            $market = $this->safe_value($this->marketsById, $trade['pair']);
        if ($market)
            $symbol = $market['symbol'];
        $timestamp = intval ($trade['timestamp'] * 1000);
        $price = floatval ($trade['price']);
        $amount = floatval ($trade['amount']);
        $cost = $this->cost_to_precision($symbol, $price * $amount);
        $id = $this->safe_string($trade, 'id');
        if (!$id)
            $id = $this->safe_string($trade, 'tid');
        return array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $this->safe_string($trade, 'o_id'),
            'type' => 'limit',
            'side' => $trade['type'],
            'price' => $price,
            'amount' => $amount,
            'cost' => floatval ($cost),
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array ();
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit)
            $request['limit'] = $limit;
        $trades = $this->publicGetExchanges (array_merge ($request, $params));
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '5m', $since = null, $limit = null) {
        return [
            $ohlcv['time'] * 1000,
            $ohlcv['open'],
            $ohlcv['high'],
            $ohlcv['low'],
            $ohlcv['close'],
            $ohlcv['volume'],
        ];
    }

    public function fetch_ohlcv ($symbol, $timeframe = '5m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'pair' => $market['id'],
            'type' => $this->timeframes[$timeframe],
        );
        if ($limit)
            $request['limit'] = $limit;
        if ($since)
            $request['since'] = intval ($since / 1000);
        $response = $this->publicGetChartsPairTypeChart (array_merge ($request, $params));
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $balances = $this->privateGetWallets ($params);
        $result = array ( 'info' => $balances );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currency = $this->common_currency_code($balance['currency']);
            $used = $this->safe_float($balance, 'reserve');
            $total = $this->safe_float($balance, 'balance');
            $free = null;
            if ($used !== null) {
                if ($total !== null) {
                    $free = $total - $used;
                }
            }
            $result[$currency] = array (
                'free' => $free,
                'used' => $used,
                'total' => $total,
            );
        }
        return $this->parse_balance($result);
    }

    public function parse_order ($order, $market = null) {
        $symbol = null;
        if (!$market)
            $market = $this->safe_value($this->marketsById, $order['pair']);
        if ($market)
            $symbol = $market['symbol'];
        $timestamp = intval ($order['date'] * 1000);
        $price = floatval ($order['price']);
        $amount = $this->safe_float($order, 'amount');
        $status = $this->safe_string($order, 'status');
        $statuses = array (
            '1' => 'open',
            '2' => 'canceled',
            '3' => 'closed',
        );
        $id = $this->safe_string($order, 'oid');
        if (!$id)
            $id = $this->safe_string($order, 'id');
        $trades = $this->safe_value($order, 'trades');
        if ($trades)
            $trades = $this->parse_trades($trades, $market);
        return array (
            'id' => $id,
            'datetime' => $this->iso8601 ($timestamp),
            'timestamp' => $timestamp,
            'status' => $this->safe_string($statuses, $status),
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $order['type'],
            'price' => $price,
            'cost' => null,
            'amount' => $amount,
            'filled' => null,
            'remaining' => null,
            'trades' => $trades,
            'fee' => null,
            'info' => $order,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->privatePostOrder (array_merge (array (
            'pair' => $market['id'],
            'type' => $side,
            'amount' => $amount,
            'price' => $this->price_to_precision($symbol, $price),
        ), $params));
        if (!$response['success'])
            throw new InvalidOrder ($this->id . ' ' . $this->json ($response));
        return $this->parse_order($response, $market);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $response = $this->privatePostOrderCancel (array_merge (array (
            'order' => $id,
        ), $params));
        return $response;
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $order = $this->privateGetOrderId (array_merge (array (
            'id' => $id,
        ), $params));
        return $this->parse_order($order);
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array ();
        $market = null;
        if ($symbol) {
            $market = $this->market ($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit)
            $request['limit'] = $limit;
        $orders = $this->privateGetOrdersOwn (array_merge ($request, $params));
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, array_merge (array (
            'status' => '1',
        ), $params));
        return $orders;
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, array_merge (array (
            'status' => '3',
        ), $params));
        return $orders;
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array ();
        if ($symbol) {
            $market = $this->market ($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit)
            $request['limit'] = $limit;
        $trades = $this->privateGetExchangesOwn (array_merge ($request, $params));
        return $this->parse_trades($trades, null, $since, $limit);
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = $this->urlencode ($this->keysort ($this->omit ($params, $this->extract_params($path))));
        $url = $this->urls['api'] . '/';
        if ($path !== 'charts/{pair}/{type}/chart/') {
            $url .= 'v1/';
        }
        $url .= $this->implode_params($path, $params);
        $headers = array ( 'Accept' => 'application/json' );
        if ($api === 'public') {
            if (strlen ($query))
                $url .= '?' . $query;
        } else {
            $this->check_required_credentials();
            $payload = $this->apiKey;
            if ($method === 'POST') {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                $body = $query;
                $payload .= $body;
            } else if (strlen ($query)) {
                $url .= '?' . $query;
            }
            $headers['X-KEY'] = $this->apiKey;
            $headers['X-SIGN'] = $this->hmac ($this->encode ($payload), $this->encode ($this->secret));
            $headers['X-NONCE'] = (string) $this->nonce ();
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body) {
        if ($code < 400)
            return;
        if (gettype ($body) !== 'string')
            return; // fallback to default error handler
        if (strlen ($body) < 2)
            return; // fallback to default error handler
        if (($body[0] === '{') || ($body[0] === '[')) {
            $response = json_decode ($body, $as_associative_array = true);
            $message = $this->id . ' ' . $this->safe_value($response, 'detail', $body);
            if ($code === 401 || $code === 403) {
                throw new AuthenticationError ($message);
            } else if ($code === 429) {
                throw new DDoSProtection ($message);
            }
            throw new ExchangeError ($message);
        }
    }
}

<?php

namespace ccxt;

use Exception; // a common import

class negociecoins extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'negociecoins',
            'name' => 'NegocieCoins',
            'countries' => array ( 'BR' ),
            'rateLimit' => 1000,
            'version' => 'v3',
            'has' => array (
                'createMarketOrder' => false,
                'fetchOrder' => true,
                'fetchOrders' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/38008571-25a6246e-3258-11e8-969b-aeb691049245.jpg',
                'api' => array (
                    'public' => 'https://broker.negociecoins.com.br/api/v3',
                    'private' => 'https://broker.negociecoins.com.br/tradeapi/v1',
                ),
                'www' => 'https://www.negociecoins.com.br',
                'doc' => array (
                    'https://www.negociecoins.com.br/documentacao-tradeapi',
                    'https://www.negociecoins.com.br/documentacao-api',
                ),
                'fees' => 'https://www.negociecoins.com.br/comissoes',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        '{PAR}/ticker',
                        '{PAR}/orderbook',
                        '{PAR}/trades',
                        '{PAR}/trades/{timestamp_inicial}',
                        '{PAR}/trades/{timestamp_inicial}/{timestamp_final}',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'user/balance',
                        'user/order/{orderId}',
                    ),
                    'post' => array (
                        'user/order',
                        'user/orders',
                    ),
                    'delete' => array (
                        'user/order/{orderId}',
                    ),
                ),
            ),
            'markets' => array (
                'B2X/BRL' => array( 'id' => 'b2xbrl', 'symbol' => 'B2X/BRL', 'base' => 'B2X', 'quote' => 'BRL' ),
                'BCH/BRL' => array( 'id' => 'bchbrl', 'symbol' => 'BCH/BRL', 'base' => 'BCH', 'quote' => 'BRL' ),
                'BTC/BRL' => array( 'id' => 'btcbrl', 'symbol' => 'BTC/BRL', 'base' => 'BTC', 'quote' => 'BRL' ),
                'BTG/BRL' => array( 'id' => 'btgbrl', 'symbol' => 'BTG/BRL', 'base' => 'BTG', 'quote' => 'BRL' ),
                'DASH/BRL' => array( 'id' => 'dashbrl', 'symbol' => 'DASH/BRL', 'base' => 'DASH', 'quote' => 'BRL' ),
                'LTC/BRL' => array( 'id' => 'ltcbrl', 'symbol' => 'LTC/BRL', 'base' => 'LTC', 'quote' => 'BRL' ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.005,
                    'taker' => 0.005,
                ),
                'funding' => array (
                    'withdraw' => array (
                        'BTC' => 0.001,
                        'BCH' => 0.00003,
                        'BTG' => 0.00009,
                        'LTC' => 0.005,
                    ),
                ),
            ),
            'limits' => array (
                'amount' => array (
                    'min' => 0.001,
                    'max' => null,
                ),
            ),
            'precision' => array (
                'amount' => 8,
                'price' => 8,
            ),
        ));
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'date');
        $symbol = ($market !== null) ? $market['symbol'] : null;
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'PAR' => $market['id'],
        );
        $ticker = $this->publicGetPARTicker (array_merge ($request, $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'PAR' => $this->market_id($symbol),
        );
        $response = $this->publicGetPAROrderbook (array_merge ($request, $params));
        return $this->parse_order_book($response, null, 'bid', 'ask', 'price', 'quantity');
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
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
        $id = $this->safe_string($trade, 'tid');
        $type = 'limit';
        $side = $this->safe_string_lower($trade, 'type');
        return array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => null,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        if ($since === null) {
            $since = 0;
        }
        $request = array (
            'PAR' => $market['id'],
            'timestamp_inicial' => intval ($since / 1000),
        );
        $response = $this->publicGetPARTradesTimestampInicial (array_merge ($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetUserBalance ($params);
        //
        //     {
        //         "coins" => array (
        //             array("name":"BRL","available":0.0,"$openOrders":0.0,"$withdraw":0.0,"total":0.0),
        //             array("name":"BTC","available":0.0,"$openOrders":0.0,"$withdraw":0.0,"total":0.0),
        //         ),
        //     }
        //
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'coins');
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'name');
            $code = $this->safe_currency_code($currencyId);
            $openOrders = $this->safe_float($balance, 'openOrders');
            $withdraw = $this->safe_float($balance, 'withdraw');
            $account = array (
                'free' => $this->safe_float($balance, 'total'),
                'used' => $this->sum ($openOrders, $withdraw),
                'total' => $this->safe_float($balance, 'available'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order_status ($status) {
        $statuses = array (
            'filled' => 'closed',
            'cancelled' => 'canceled',
            'partially filled' => 'open',
            'pending' => 'open',
            'rejected' => 'rejected',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order ($order, $market = null) {
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($order, 'pair');
            $market = $this->safe_value($this->marketsById, $marketId);
            if ($market) {
                $symbol = $market['symbol'];
            }
        }
        $timestamp = $this->parse8601 ($this->safe_string($order, 'created'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'quantity');
        $cost = $this->safe_float($order, 'total');
        $remaining = $this->safe_float($order, 'pending_quantity');
        $filled = $this->safe_float($order, 'executed_quantity');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $trades = null;
        // if ($order['operations'])
        //     $trades = $this->parse_trades($order['operations']);
        return array (
            'id' => (string) $order['id'],
            'datetime' => $this->iso8601 ($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $order['type'],
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => $trades,
            'fee' => array (
                'currency' => $market['quote'],
                'cost' => $this->safe_float($order, 'fee'),
            ),
            'info' => $order,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'pair' => $market['id'],
            'price' => $this->price_to_precision($symbol, $price),
            'volume' => $this->amount_to_precision($symbol, $amount),
            'type' => $side,
        );
        $response = $this->privatePostUserOrder (array_merge ($request, $params));
        $order = $this->parse_order($response[0], $market);
        $id = $order['id'];
        $this->orders[$id] = $order;
        return $order;
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->markets[$symbol];
        $request = array (
            'orderId' => $id,
        );
        $response = $this->privateDeleteUserOrderOrderId (array_merge ($request, $params));
        return $this->parse_order($response[0], $market);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'orderId' => $id,
        );
        $order = $this->privateGetUserOrderOrderId (array_merge ($request, $params));
        return $this->parse_order($order[0]);
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders () requires a $symbol argument');
        }
        $market = $this->market ($symbol);
        $request = array (
            'pair' => $market['id'],
            // type => buy, sell
            // status => cancelled, filled, partially filled, pending, rejected
            // startId
            // endId
            // startDate yyyy-MM-dd
            // endDate => yyyy-MM-dd
        );
        if ($since !== null) {
            $request['startDate'] = $this->ymd ($since);
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $orders = $this->privatePostUserOrders (array_merge ($request, $params));
        return $this->parse_orders($orders, $market);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array (
            'status' => 'pending',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge ($request, $params));
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array (
            'status' => 'filled',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge ($request, $params));
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        $queryString = $this->urlencode ($query);
        if ($api === 'public') {
            if (strlen ($queryString)) {
                $url .= '?' . $queryString;
            }
        } else {
            $this->check_required_credentials();
            $timestamp = (string) $this->seconds ();
            $nonce = (string) $this->nonce ();
            $content = '';
            if (strlen ($queryString)) {
                $body = $this->json ($query);
                $content = $this->hash ($this->encode ($body), 'md5', 'base64');
            } else {
                $body = '';
            }
            $uri = strtolower($this->encode_uri_component($url));
            $payload = implode('', array($this->apiKey, $method, $uri, $timestamp, $nonce, $content));
            $secret = base64_decode ($this->secret);
            $signature = $this->hmac ($this->encode ($payload), $secret, 'sha256', 'base64');
            $signature = $this->decode ($signature);
            $auth = implode(':', array($this->apiKey, $signature, $nonce, $timestamp));
            $headers = array (
                'Authorization' => 'amx ' . $auth,
            );
            if ($method === 'POST') {
                $headers['Content-Type'] = 'application/json; charset=UTF-8';
                $headers['Content-Length'] = is_array ($body) ? count ($body) : 0;
            } else if (strlen ($queryString)) {
                $url .= '?' . $queryString;
                $body = null;
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

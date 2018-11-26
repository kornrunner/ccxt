<?php

namespace ccxt;

use Exception as Exception; // a common import

class deribit extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'deribit',
            'name' => 'Deribit',
            'countries' => array ( 'NL' ), // Netherlands
            'version' => 'v1',
            'userAgent' => null,
            'rateLimit' => 2000,
            'has' => array (
                'CORS' => true,
                'editOrder' => true,
                'fetchOrder' => true,
                'fetchOrders' => false,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchMyTrades' => true,
                'fetchTickers' => false,
            ),
            'timeframes' => array (),
            'urls' => array (
                // 'test' => 'https://test.deribit.com',
                'logo' => 'https://user-images.githubusercontent.com/1294454/41933112-9e2dd65a-798b-11e8-8440-5bab2959fcb8.jpg',
                'api' => 'https://www.deribit.com',
                'www' => 'https://www.deribit.com',
                'doc' => array (
                    'https://www.deribit.com/pages/docs/api',
                    'https://github.com/deribit',
                ),
                'fees' => 'https://www.deribit.com/pages/information/fees',
                'referral' => 'https://www.deribit.com/reg-1189.4038',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'test',
                        'getinstruments',
                        'index',
                        'getcurrencies',
                        'getorderbook',
                        'getlasttrades',
                        'getsummary',
                        'stats',
                        'getannouncments',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'account',
                        'getopenorders',
                        'positions',
                        'orderhistory',
                        'orderstate',
                        'tradehistory',
                        'newannouncements',
                    ),
                    'post' => array (
                        'buy',
                        'sell',
                        'edit',
                        'cancel',
                        'cancelall',
                    ),
                ),
            ),
            'exceptions' => array (
                'Invalid API Key.' => '\\ccxt\\AuthenticationError',
                'Access Denied' => '\\ccxt\\PermissionDenied',
            ),
            'options' => array (
                'fetchTickerQuotes' => true,
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $marketsResponse = $this->publicGetGetinstruments ();
        $markets = $marketsResponse['result'];
        $result = array ();
        for ($p = 0; $p < count ($markets); $p++) {
            $market = $markets[$p];
            $id = $market['instrumentName'];
            $base = $market['baseCurrency'];
            $quote = $market['currency'];
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $result[] = array (
                'id' => $id,
                'symbol' => $id,
                'base' => $base,
                'quote' => $quote,
                'active' => $market['isActive'],
                'precision' => array (
                    'amount' => $market['minTradeSize'],
                    'price' => $market['tickSize'],
                ),
                'limits' => array (
                    'amount' => array (
                        'min' => $market['minTradeSize'],
                    ),
                    'price' => array (
                        'min' => $market['tickSize'],
                    ),
                ),
                'type' => $market['kind'],
                'spot' => false,
                'future' => $market['kind'] === 'future',
                'option' => $market['kind'] === 'option',
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $account = $this->privateGetAccount ();
        $result = array (
            'BTC' => array (
                'free' => $account['result']['availableFunds'],
                'used' => $account['result']['maintenanceMargin'],
                'total' => $account['result']['equity'],
            ),
        );
        return $this->parse_balance($result);
    }

    public function fetch_deposit_address ($currency, $params = array ()) {
        $account = $this->privateGetAccount ();
        return array (
            'currency' => 'BTC',
            'address' => $account['depositAddress'],
            'tag' => null,
            'info' => $account,
        );
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->safe_integer($ticker, 'created');
        $iso8601 = ($timestamp === null) ? null : $this->iso8601 ($timestamp);
        $symbol = $this->find_symbol($this->safe_string($ticker, 'instrumentName'), $market);
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $iso8601,
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bidPrice'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'askPrice'),
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
            'quoteVolume' => $this->safe_float($ticker, 'volume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetGetsummary (array_merge (array (
            'instrument' => $market['id'],
        ), $params));
        return $this->parse_ticker($response['result'], $market);
    }

    public function parse_trade ($trade, $market = null) {
        $id = $this->safe_string($trade, 'tradeId');
        $symbol = null;
        if ($market !== null)
            $symbol = $market['symbol'];
        $timestamp = $this->safe_integer($trade, 'timeStamp');
        return array (
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'order' => null,
            'type' => null,
            'side' => $trade['direction'],
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'quantity'),
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrument' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        } else {
            $request['limit'] = 10000;
        }
        $response = $this->publicGetGetlasttrades (array_merge ($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetGetorderbook (array ( 'instrument' => $market['id'] ));
        $timestamp = intval ($response['usOut'] / 1000);
        $orderbook = $this->parse_order_book($response['result'], $timestamp, 'bids', 'asks', 'price', 'quantity');
        return array_merge ($orderbook, array (
            'nonce' => $this->safe_integer($response, 'tstamp'),
        ));
    }

    public function parse_order_status ($status) {
        $statuses = array (
            'open' => 'open',
            'cancelled' => 'canceled',
            'filled' => 'closed',
        );
        if (is_array ($statuses) && array_key_exists ($status, $statuses)) {
            return $statuses[$status];
        }
        return $status;
    }

    public function parse_order ($order, $market = null) {
        //
        //     {
        //         "orderId" => 5258039,          // ID of the $order
        //         "$type" => "limit",             // not documented, but present in the actual response
        //         "instrument" => "BTC-26MAY17", // instrument name ($market $id)
        //         "direction" => "sell",         // $order direction, "buy" or "sell"
        //         "$price" => 1860,               // float, USD for futures, BTC for options
        //         "label" => "",                 // label set by the owner, up to 32 chars
        //         "quantity" => 10,              // quantity, in contracts ($10 per contract for futures, ฿1 — for options)
        //         "filledQuantity" => 3,         // $filled quantity, in contracts ($10 per contract for futures, ฿1 — for options)
        //         "avgPrice" => 1860,            // $average fill $price of the $order
        //         "commission" => -0.000001613,  // in BTC units
        //         "created" => 1494491899308,    // creation $timestamp
        //         "state" => "open",             // open, cancelled, etc
        //         "postOnly" => false            // true for post-only orders only
        // open orders --------------------------------------------------------
        //         "$lastUpdate" => 1494491988754, // $timestamp of the last $order state change (before this cancelorder of course)
        // closed orders ------------------------------------------------------
        //         "tstamp" => 1494492913288,     // $timestamp of the last $order state change, documented, but may be missing in the actual response
        //         "modified" => 1494492913289,   // $timestamp of the last db write operation, e.g. trade that doesn't change $order $status, documented, but may missing in the actual response
        //         "adv" => false                 // advanced $type (false, or "usd" or "implv")
        //         "trades" => array (),                // not documented, injected from the outside of the parseOrder method into the $order
        //     }
        //
        $timestamp = $this->safe_integer($order, 'created');
        $lastUpdate = $this->safe_integer($order, 'lastUpdate');
        $lastTradeTimestamp = $this->safe_integer_2($order, 'tstamp', 'modified');
        $id = $this->safe_string($order, 'orderId');
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'avgPrice');
        $amount = $this->safe_float($order, 'quantity');
        $filled = $this->safe_float($order, 'filledQuantity');
        if ($lastTradeTimestamp === null) {
            if ($filled !== null) {
                if ($filled > 0) {
                    $lastTradeTimestamp = $lastUpdate;
                }
            }
        }
        $remaining = null;
        $cost = null;
        if ($filled !== null) {
            if ($amount !== null) {
                $remaining = $amount - $filled;
            }
            if ($price !== null) {
                $cost = $price * $filled;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $side = $this->safe_string($order, 'direction');
        if ($side !== null) {
            $side = strtolower ($side);
        }
        $feeCost = $this->safe_float($order, 'commission');
        if ($feeCost !== null) {
            $feeCost = abs ($feeCost);
        }
        $fee = array (
            'cost' => $feeCost,
            'currency' => 'BTC',
        );
        $type = $this->safe_string($order, 'type');
        return array (
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $order['instrument'],
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => null, // todo => parse trades
        );
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privateGetOrderstate (array ( 'orderId' => $id ));
        return $this->parse_order($response['result']);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'instrument' => $this->market_id($symbol),
            'quantity' => $amount,
            'type' => $type,
        );
        if ($price !== null)
            $request['price'] = $price;
        $method = 'privatePost' . $this->capitalize ($side);
        $response = $this->$method (array_merge ($request, $params));
        $order = $this->safe_value($response['result'], 'order');
        if ($order === null) {
            return $response;
        }
        return $this->parse_order($order);
    }

    public function edit_order ($id, $symbol, $type, $side, $amount = null, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'orderId' => $id,
        );
        if ($amount !== null)
            $request['quantity'] = $amount;
        if ($price !== null)
            $request['price'] = $price;
        $response = $this->privatePostEdit (array_merge ($request, $params));
        return $this->parse_order($response['result']);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostCancel (array_merge (array ( 'orderId' => $id ), $params));
        return $this->parse_order($response['result']);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrument' => $market['id'],
        );
        $response = $this->privateGetGetopenorders (array_merge ($request, $params));
        return $this->parse_orders($response['result'], $market, $since, $limit);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrument' => $market['id'],
        );
        $response = $this->privateGetOrderhistory (array_merge ($request, $params));
        return $this->parse_orders($response['result'], $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrument' => $market['id'],
        );
        if ($limit !== null) {
            $request['count'] = $limit; // default = 20
        }
        $response = $this->privateGetTradehistory (array_merge ($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = '/' . 'api/' . $this->version . '/' . $api . '/' . $path;
        $url = $this->urls['api'] . $query;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode ($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce ();
            $auth = '_=' . $nonce . '&_ackey=' . $this->apiKey . '&_acsec=' . $this->secret . '&_action=' . $query;
            if ($method === 'POST') {
                $params = $this->keysort ($params);
                $auth .= '&' . $this->urlencode ($params);
            }
            $hash = $this->hash ($this->encode ($auth), 'sha256', 'base64');
            $signature = $this->apiKey . '.' . $nonce . '.' . $this->decode ($hash);
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-deribit-sig' => $signature,
            );
            $body = $this->urlencode ($params);
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

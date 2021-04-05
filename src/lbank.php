<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ArgumentsRequired;

class lbank extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'lbank',
            'name' => 'LBank',
            'countries' => array( 'CN' ),
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => false, // status 0 API doesn't work
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => 'minute1',
                '5m' => 'minute5',
                '15m' => 'minute15',
                '30m' => 'minute30',
                '1h' => 'hour1',
                '2h' => 'hour2',
                '4h' => 'hour4',
                '6h' => 'hour6',
                '8h' => 'hour8',
                '12h' => 'hour12',
                '1d' => 'day1',
                '1w' => 'week1',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/38063602-9605e28a-3302-11e8-81be-64b1e53c4cfb.jpg',
                'api' => 'https://api.lbank.info',
                'www' => 'https://www.lbank.info',
                'doc' => 'https://github.com/LBank-exchange/lbank-official-api-docs',
                'fees' => 'https://lbankinfo.zendesk.com/hc/en-gb/articles/360012072873-Trading-Fees',
                'referral' => 'https://www.lbex.io/invite?icode=7QCY',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencyPairs',
                        'ticker',
                        'depth',
                        'trades',
                        'kline',
                        'accuracy',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'user_info',
                        'create_order',
                        'cancel_order',
                        'orders_info',
                        'orders_info_history',
                        'withdraw',
                        'withdrawCancel',
                        'withdraws',
                        'withdrawConfigs',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.1 / 100,
                    'taker' => 0.1 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => null,
                        'ZEC' => 0.01,
                        'ETH' => 0.01,
                        'ETC' => 0.01,
                        // 'QTUM' => amount => max (0.01, amount * (0.1 / 100)),
                        'VEN' => 10.0,
                        'BCH' => 0.0002,
                        'SC' => 50.0,
                        'BTM' => 20.0,
                        'NAS' => 1.0,
                        'EOS' => 1.0,
                        'XWC' => 5.0,
                        'BTS' => 1.0,
                        'INK' => 10.0,
                        'BOT' => 3.0,
                        'YOYOW' => 15.0,
                        'TGC' => 10.0,
                        'NEO' => 0.0,
                        'CMT' => 20.0,
                        'SEER' => 2000.0,
                        'FIL' => null,
                        'BTG' => null,
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'VET_ERC20' => 'VEN',
                'PNT' => 'Penta',
            ),
            'options' => array(
                'cacheSecretAsPem' => true,
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetAccuracy ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $market['symbol'];
            $parts = explode('_', $id);
            $baseId = null;
            $quoteId = null;
            $numParts = is_array($parts) ? count($parts) : 0;
            // lbank will return symbols like "vet_erc20_usdt"
            if ($numParts > 2) {
                $baseId = $parts[0] . '_' . $parts[1];
                $quoteId = $parts[2];
            } else {
                $baseId = $parts[0];
                $quoteId = $parts[1];
            }
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'quantityAccuracy'),
                'price' => $this->safe_integer($market, 'priceAccuracy'),
            );
            $result[] = array(
                'id' => $id,
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
                        'max' => pow(10, $precision['price']),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $id,
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($ticker, 'symbol');
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->marketsById[$marketId];
                $symbol = $market['symbol'];
            } else {
                $parts = explode('_', $marketId);
                $baseId = null;
                $quoteId = null;
                $numParts = is_array($parts) ? count($parts) : 0;
                // lbank will return symbols like "vet_erc20_usdt"
                if ($numParts > 2) {
                    $baseId = $parts[0] . '_' . $parts[1];
                    $quoteId = $parts[2];
                } else {
                    $baseId = $parts[0];
                    $quoteId = $parts[1];
                }
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        $timestamp = $this->safe_integer($ticker, 'timestamp');
        $info = $ticker;
        $ticker = $info['ticker'];
        $last = $this->safe_number($ticker, 'latest');
        $percentage = $this->safe_number($ticker, 'change');
        $open = null;
        if ($percentage !== null) {
            $relativeChange = $this->sum(1, $percentage / 100);
            if ($relativeChange > 0) {
                $open = $last / $this->sum(1, $relativeChange);
            }
        }
        $change = null;
        $average = null;
        if ($last !== null && $open !== null) {
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_number($ticker, 'vol'),
            'quoteVolume' => $this->safe_number($ticker, 'turnover'),
            'info' => $info,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => 'all',
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = 60, $params = array ()) {
        $this->load_markets();
        $size = 60;
        if ($limit !== null) {
            $size = min ($limit, $size);
        }
        $request = array(
            'symbol' => $this->market_id($symbol),
            'size' => $size,
        );
        $response = $this->publicGetDepth (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function parse_trade($trade, $market = null) {
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($trade, 'date_ms');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = floatval($this->cost_to_precision($symbol, $price * $amount));
            }
        }
        $id = $this->safe_string($trade, 'tid');
        $type = null;
        $side = $this->safe_string($trade, 'type');
        $side = str_replace('_market', '', $side);
        return array(
            'id' => $id,
            'info' => $this->safe_value($trade, 'info', $trade),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => null,
            'type' => $type,
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
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'size' => 100,
        );
        if ($since !== null) {
            $request['time'] = intval($since);
        }
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1590969600,
        //         0.02451657,
        //         0.02452675,
        //         0.02443701,
        //         0.02447814,
        //         238.38210000
        //     )
        //
        return array(
            $this->safe_timestamp($ohlcv, 0),
            $this->safe_number($ohlcv, 1),
            $this->safe_number($ohlcv, 2),
            $this->safe_number($ohlcv, 3),
            $this->safe_number($ohlcv, 4),
            $this->safe_number($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '5m', $since = null, $limit = 1000, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($since === null) {
            throw new ArgumentsRequired($this->id . ' fetchOHLCV() requires a `$since` argument');
        }
        if ($limit === null) {
            throw new ArgumentsRequired($this->id . ' fetchOHLCV() requires a `$limit` argument');
        }
        $request = array(
            'symbol' => $market['id'],
            'type' => $this->timeframes[$timeframe],
            'size' => $limit,
            'time' => intval($since / 1000),
        );
        $response = $this->publicGetKline (array_merge($request, $params));
        //
        //     [
        //         [1590969600,0.02451657,0.02452675,0.02443701,0.02447814,238.38210000],
        //         [1590969660,0.02447814,0.02449883,0.02443209,0.02445973,212.40270000],
        //         [1590969720,0.02445973,0.02452067,0.02445909,0.02446151,266.16920000],
        //     ]
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostUserInfo ($params);
        //
        //     {
        //         "$result":"true",
        //         "$info":{
        //             "$freeze":array(
        //                 "iog":"0.00000000",
        //                 "ssc":"0.00000000",
        //                 "eon":"0.00000000",
        //             ),
        //             "$asset":array(
        //                 "iog":"0.00000000",
        //                 "ssc":"0.00000000",
        //                 "eon":"0.00000000",
        //             ),
        //             "$free":array(
        //                 "iog":"0.00000000",
        //                 "ssc":"0.00000000",
        //                 "eon":"0.00000000",
        //             ),
        //         }
        //     }
        //
        $result = array( 'info' => $response );
        $info = $this->safe_value($response, 'info', array());
        $free = $this->safe_value($info, 'free', array());
        $freeze = $this->safe_value($info, 'freeze', array());
        $asset = $this->safe_value($info, 'asset', array());
        $currencyIds = is_array($free) ? array_keys($free) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($free, $currencyId);
            $account['used'] = $this->safe_number($freeze, $currencyId);
            $account['total'] = $this->safe_number($asset, $currencyId);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order_status($status) {
        $statuses = array(
            '-1' => 'cancelled', // cancelled
            '0' => 'open', // not traded
            '1' => 'open', // partial deal
            '2' => 'closed', // complete deal
            '4' => 'closed', // disposal processing
        );
        return $this->safe_string($statuses, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "$symbol"："eth_btc",
        //         "$amount"：10.000000,
        //         "create_time"：1484289832081,
        //         "$price"：5000.000000,
        //         "avg_price"：5277.301200,
        //         "$type"："sell",
        //         "order_id"："ab704110-af0d-48fd-a083-c218f19a4a55",
        //         "deal_amount"：10.000000,
        //         "$status"：2
        //     }
        //
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $timestamp = $this->safe_integer($order, 'create_time');
        // Limit Order Request Returns => Order Price
        // Market Order Returns => cny $amount of $market $order
        $price = $this->safe_number($order, 'price');
        $amount = $this->safe_number($order, 'amount', 0.0);
        $filled = $this->safe_number($order, 'deal_amount', 0.0);
        $average = $this->safe_number($order, 'avg_price');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $id = $this->safe_string($order, 'order_id');
        $type = $this->safe_string($order, 'order_type');
        $side = $this->safe_string($order, 'type');
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'trades' => null,
            'fee' => null,
            'info' => $this->safe_value($order, 'info', $order),
            'average' => $average,
        ));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $order = array(
            'symbol' => $market['id'],
            'type' => $side,
            'amount' => $amount,
        );
        if ($type === 'market') {
            $order['type'] .= '_market';
        } else {
            $order['price'] = $price;
        }
        $response = $this->privatePostCreateOrder (array_merge($order, $params));
        $order = $this->omit($order, 'type');
        $order['order_id'] = $response['order_id'];
        $order['type'] = $side;
        $order['order_type'] = $type;
        $order['create_time'] = $this->milliseconds();
        $order['info'] = $response;
        return $this->parse_order($order, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'order_id' => $id,
        );
        $response = $this->privatePostCancelOrder (array_merge($request, $params));
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        // Id can be a list of ids delimited by a comma
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'order_id' => $id,
        );
        $response = $this->privatePostOrdersInfo (array_merge($request, $params));
        $data = $this->safe_value($response, 'orders', array());
        $orders = $this->parse_orders($data, $market);
        $numOrders = is_array($orders) ? count($orders) : 0;
        if ($numOrders === 1) {
            return $orders[0];
        } else {
            return $orders;
        }
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($limit === null) {
            $limit = 100;
        }
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'current_page' => 1,
            'page_length' => $limit,
        );
        $response = $this->privatePostOrdersInfoHistory (array_merge($request, $params));
        $data = $this->safe_value($response, 'orders', array());
        return $this->parse_orders($data, null, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        $closed = $this->filter_by($orders, 'status', 'closed');
        $canceled = $this->filter_by($orders, 'status', 'cancelled'); // cancelled $orders may be partially filled
        $allOrders = $this->array_concat($closed, $canceled);
        return $this->filter_by_symbol_since_limit($allOrders, $symbol, $since, $limit);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        // mark and fee are optional $params, mark is a note and must be less than 255 characters
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'assetCode' => $currency['id'],
            'amount' => $amount,
            'account' => $address,
        );
        if ($tag !== null) {
            $request['memo'] = $tag;
        }
        $response = $this->privatePostWithdraw (array_merge($request, $params));
        return array(
            'id' => $this->safe_string($response, 'id'),
            'info' => $response,
        );
    }

    public function convert_secret_to_pem($secret) {
        $lineLength = 64;
        $secretLength = strlen($secret) - 0;
        $numLines = intval($secretLength / $lineLength);
        $numLines = $this->sum($numLines, 1);
        $pem = "-----BEGIN PRIVATE KEY-----\n"; // eslint-disable-line
        for ($i = 0; $i < $numLines; $i++) {
            $start = $i * $lineLength;
            $end = $this->sum($start, $lineLength);
            $pem .= mb_substr($this->secret, $start, $end - $start) . "\n"; // eslint-disable-line
        }
        return $pem . '-----END PRIVATE KEY-----';
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->urls['api'] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        // Every endpoint ends with ".do"
        $url .= '.do';
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $query = $this->keysort(array_merge(array(
                'api_key' => $this->apiKey,
            ), $params));
            $queryString = $this->rawencode($query);
            $message = strtoupper($this->hash($this->encode($queryString)));
            $cacheSecretAsPem = $this->safe_value($this->options, 'cacheSecretAsPem', true);
            $pem = null;
            if ($cacheSecretAsPem) {
                $pem = $this->safe_value($this->options, 'pem');
                if ($pem === null) {
                    $pem = $this->convert_secret_to_pem($this->secret);
                    $this->options['pem'] = $pem;
                }
            } else {
                $pem = $this->convert_secret_to_pem($this->secret);
            }
            $sign = $this->binary_to_base64($this->rsa($message, $this->encode($pem), 'RS256'));
            $query['sign'] = $sign;
            $body = $this->urlencode($query);
            $headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        $success = $this->safe_string($response, 'result');
        if ($success === 'false') {
            $errorCode = $this->safe_string($response, 'error_code');
            $message = $this->safe_string(array(
                '10000' => 'Internal error',
                '10001' => 'The required parameters can not be empty',
                '10002' => 'verification failed',
                '10003' => 'Illegal parameters',
                '10004' => 'User requests are too frequent',
                '10005' => 'Key does not exist',
                '10006' => 'user does not exist',
                '10007' => 'Invalid signature',
                '10008' => 'This currency pair is not supported',
                '10009' => 'Limit orders can not be missing orders and the number of orders',
                '10010' => 'Order price or order quantity must be greater than 0',
                '10011' => 'Market orders can not be missing the amount of the order',
                '10012' => 'market sell orders can not be missing orders',
                '10013' => 'is less than the minimum trading position 0.001',
                '10014' => 'Account number is not enough',
                '10015' => 'The order type is wrong',
                '10016' => 'Account balance is not enough',
                '10017' => 'Abnormal server',
                '10018' => 'order inquiry can not be more than 50 less than one',
                '10019' => 'withdrawal orders can not be more than 3 less than one',
                '10020' => 'less than the minimum amount of the transaction limit of 0.001',
                '10022' => 'Insufficient key authority',
            ), $errorCode, $this->json($response));
            $ErrorClass = $this->safe_value(array(
                '10002' => '\\ccxt\\AuthenticationError',
                '10004' => '\\ccxt\\DDoSProtection',
                '10005' => '\\ccxt\\AuthenticationError',
                '10006' => '\\ccxt\\AuthenticationError',
                '10007' => '\\ccxt\\AuthenticationError',
                '10009' => '\\ccxt\\InvalidOrder',
                '10010' => '\\ccxt\\InvalidOrder',
                '10011' => '\\ccxt\\InvalidOrder',
                '10012' => '\\ccxt\\InvalidOrder',
                '10013' => '\\ccxt\\InvalidOrder',
                '10014' => '\\ccxt\\InvalidOrder',
                '10015' => '\\ccxt\\InvalidOrder',
                '10016' => '\\ccxt\\InvalidOrder',
                '10022' => '\\ccxt\\AuthenticationError',
            ), $errorCode, '\\ccxt\\ExchangeError');
            throw new $ErrorClass($message);
        }
        return $response;
    }
}

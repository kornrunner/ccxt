<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class coinone extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinone',
            'name' => 'CoinOne',
            'countries' => array( 'KR' ), // Korea
            // 'enableRateLimit' => false,
            'rateLimit' => 667,
            'version' => 'v2',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => false,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                // https://github.com/ccxt/ccxt/pull/7067
                // the endpoint that should return closed orders actually returns trades
                'fetchClosedOrders' => false,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/38003300-adc12fba-323f-11e8-8525-725f53c4a659.jpg',
                'api' => 'https://api.coinone.co.kr',
                'www' => 'https://coinone.co.kr',
                'doc' => 'https://doc.coinone.co.kr',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'orderbook/',
                        'trades/',
                        'ticker/',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'account/btc_deposit_address/',
                        'account/balance/',
                        'account/daily_balance/',
                        'account/user_info/',
                        'account/virtual_account/',
                        'order/cancel_all/',
                        'order/cancel/',
                        'order/limit_buy/',
                        'order/limit_sell/',
                        'order/complete_orders/',
                        'order/limit_orders/',
                        'order/order_info/',
                        'transaction/auth_number/',
                        'transaction/history/',
                        'transaction/krw/history/',
                        'transaction/btc/',
                        'transaction/coin/',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.002,
                    'maker' => 0.002,
                ),
            ),
            'precision' => array(
                'price' => 4,
                'amount' => 4,
                'cost' => 8,
            ),
            'exceptions' => array(
                '405' => '\\ccxt\\OnMaintenance', // array("errorCode":"405","status":"maintenance","result":"error")
                '104' => '\\ccxt\\OrderNotFound', // array("errorCode":"104","errorMsg":"Order id is not exist","result":"error")
                '108' => '\\ccxt\\BadSymbol', // array("errorCode":"108","errorMsg":"Unknown CryptoCurrency","result":"error")
                '107' => '\\ccxt\\BadRequest', // array("errorCode":"107","errorMsg":"Parameter error","result":"error")
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $request = array(
            'currency' => 'all',
        );
        $response = $this->publicGetTicker ($request);
        $result = array();
        $quoteId = 'krw';
        $quote = $this->safe_currency_code($quoteId);
        $baseIds = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($baseIds); $i++) {
            $baseId = $baseIds[$i];
            $ticker = $this->safe_value($response, $baseId, array());
            $currency = $this->safe_value($ticker, 'currency');
            if ($currency === null) {
                continue;
            }
            $base = $this->safe_currency_code($baseId);
            $result[] = array(
                'id' => $baseId,
                'symbol' => $base . '/' . $quote,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostAccountBalance ($params);
        $result = array( 'info' => $response );
        $balances = $this->omit($response, array(
            'errorCode',
            'result',
            'normalWallets',
        ));
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $balance = $balances[$currencyId];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'avail');
            $account['total'] = $this->safe_number($balance, 'balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'format' => 'json',
        );
        $response = $this->publicGetOrderbook (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($response, 'timestamp');
        return $this->parse_order_book($response, $timestamp, 'bid', 'ask', 'price', 'qty');
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'currency' => 'all',
            'format' => 'json',
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $result = array();
        $ids = is_array($response) ? array_keys($response) : array();
        $timestamp = $this->safe_timestamp($response, 'timestamp');
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $symbol = $id;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
                $ticker = $response[$id];
                $result[$symbol] = $this->parse_ticker($ticker, $market);
                $result[$symbol]['timestamp'] = $timestamp;
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'format' => 'json',
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'timestamp');
        $first = $this->safe_number($ticker, 'first');
        $last = $this->safe_number($ticker, 'last');
        $average = null;
        if ($first !== null && $last !== null) {
            $average = $this->sum($first, $last) / 2;
        }
        $previousClose = $this->safe_number($ticker, 'yesterday_last');
        $change = null;
        $percentage = null;
        if ($last !== null && $previousClose !== null) {
            $change = $last - $previousClose;
            if ($previousClose !== 0) {
                $percentage = $change / $previousClose * 100;
            }
        }
        $symbol = ($market !== null) ? $market['symbol'] : null;
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
            'open' => $first,
            'close' => $last,
            'last' => $last,
            'previousClose' => $previousClose,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_number($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "$timestamp" => "1416893212",
        //         "$price" => "420000.0",
        //         "qty" => "0.1",
        //         "$is_ask" => "1"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "$timestamp" => "1416561032",
        //         "$price" => "419000.0",
        //         "type" => "bid",
        //         "qty" => "0.001",
        //         "$feeRate" => "-0.0015",
        //         "$fee" => "-0.0000015",
        //         "$orderId" => "E84A1AC2-8088-4FA0-B093-A3BCDB9B3C85"
        //     }
        //
        $timestamp = $this->safe_timestamp($trade, 'timestamp');
        $symbol = ($market !== null) ? $market['symbol'] : null;
        $is_ask = $this->safe_string($trade, 'is_ask');
        $side = $this->safe_string($trade, 'type');
        if ($is_ask !== null) {
            if ($is_ask === '1') {
                $side = 'sell';
            } else if ($is_ask === '0') {
                $side = 'buy';
            }
        } else {
            if ($side === 'ask') {
                $side = 'sell';
            } else if ($side === 'bid') {
                $side = 'buy';
            }
        }
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'qty');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $orderId = $this->safe_string($trade, 'orderId');
        $feeCost = $this->safe_number($trade, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCost = abs($feeCost);
            $feeRate = $this->safe_number($trade, 'feeRate');
            $feeRate = abs($feeRate);
            $feeCurrencyCode = null;
            if ($market !== null) {
                $feeCurrencyCode = ($side === 'sell') ? $market['quote'] : $market['base'];
            }
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
                'rate' => $feeRate,
            );
        }
        return array(
            'id' => $this->safe_string($trade, 'id'),
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'order' => $orderId,
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'format' => 'json',
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0",
        //         "timestamp" => "1416895635",
        //         "currency" => "btc",
        //         "$completeOrders" => array(
        //             {
        //                 "timestamp" => "1416893212",
        //                 "price" => "420000.0",
        //                 "qty" => "0.1",
        //                 "is_ask" => "1"
        //             }
        //         )
        //     }
        //
        $completeOrders = $this->safe_value($response, 'completeOrders', array());
        return $this->parse_trades($completeOrders, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $request = array(
            'price' => $price,
            'currency' => $this->market_id($symbol),
            'qty' => $amount,
        );
        $method = 'privatePostOrder' . $this->capitalize($type) . $this->capitalize($side);
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0",
        //         "orderId" => "8a82c561-40b4-4cb3-9bc0-9ac9ffc1d63b"
        //     }
        //
        return $this->parse_order($response);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'order_id' => $id,
            'currency' => $market['id'],
        );
        $response = $this->privatePostOrderOrderInfo (array_merge($request, $params));
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0",
        //         "status" => "live",
        //         "$info" => {
        //             "orderId" => "32FF744B-D501-423A-8BA1-05BB6BE7814A",
        //             "currency" => "BTC",
        //             "type" => "bid",
        //             "price" => "2922000.0",
        //             "qty" => "115.4950",
        //             "remainQty" => "45.4950",
        //             "feeRate" => "0.0003",
        //             "fee" => "0",
        //             "timestamp" => "1499340941"
        //         }
        //     }
        //
        $info = $this->safe_value($response, 'info', array());
        $info['status'] = $this->safe_string($info, 'status');
        return $this->parse_order($info, $market);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'live' => 'open',
            'partially_filled' => 'open',
            'filled' => 'closed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0",
        //         "orderId" => "8a82c561-40b4-4cb3-9bc0-9ac9ffc1d63b"
        //     }
        //
        // fetchOrder
        //
        //     {
        //         "$status" => "live", // injected in fetchOrder
        //         "orderId" => "32FF744B-D501-423A-8BA1-05BB6BE7814A",
        //         "currency" => "BTC",
        //         "type" => "bid",
        //         "$price" => "2922000.0",
        //         "qty" => "115.4950",
        //         "remainQty" => "45.4950",
        //         "feeRate" => "0.0003",
        //         "$fee" => "0",
        //         "$timestamp" => "1499340941"
        //     }
        //
        // fetchOpenOrders
        //
        //     {
        //         "index" => "0",
        //         "orderId" => "68665943-1eb5-4e4b-9d76-845fc54f5489",
        //         "$timestamp" => "1449037367",
        //         "$price" => "444000.0",
        //         "qty" => "0.3456",
        //         "type" => "ask",
        //         "feeRate" => "-0.0015"
        //     }
        //
        $id = $this->safe_string($order, 'orderId');
        $price = $this->safe_number($order, 'price');
        $timestamp = $this->safe_timestamp($order, 'timestamp');
        $side = $this->safe_string($order, 'type');
        if ($side === 'ask') {
            $side = 'sell';
        } else if ($side === 'bid') {
            $side = 'buy';
        }
        $remaining = $this->safe_number($order, 'remainQty');
        $amount = $this->safe_number($order, 'qty');
        $status = $this->safe_string($order, 'status');
        // https://github.com/ccxt/ccxt/pull/7067
        if ($status === 'live') {
            if (($remaining !== null) && ($amount !== null)) {
                if ($remaining < $amount) {
                    $status = 'canceled';
                }
            }
        }
        $status = $this->parse_order_status($status);
        $symbol = null;
        $base = null;
        $quote = null;
        $marketId = $this->safe_string_lower($order, 'currency');
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                $base = $this->safe_currency_code($marketId);
                $quote = 'KRW';
                $symbol = $base . '/' . $quote;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
            $base = $market['base'];
            $quote = $market['quote'];
        }
        $fee = null;
        $feeCost = $this->safe_number($order, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyCode = ($side === 'sell') ? $quote : $base;
            $fee = array(
                'cost' => $feeCost,
                'rate' => $this->safe_number($order, 'feeRate'),
                'currency' => $feeCurrencyCode,
            );
        }
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'average' => null,
            'amount' => $amount,
            'filled' => null,
            'remaining' => $amount,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        ));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        // The returned amount might not be same as the ordered amount. If an order is partially filled, the returned amount means the remaining amount.
        // For the same reason, the returned amount and remaining are always same, and the returned filled and cost are always zero.
        if ($symbol === null) {
            throw new ExchangeError($this->id . ' allows fetching closed orders with a specific symbol');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
        );
        $response = $this->privatePostOrderLimitOrders (array_merge($request, $params));
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0",
        //         "$limitOrders" => array(
        //             {
        //                 "index" => "0",
        //                 "orderId" => "68665943-1eb5-4e4b-9d76-845fc54f5489",
        //                 "timestamp" => "1449037367",
        //                 "price" => "444000.0",
        //                 "qty" => "0.3456",
        //                 "type" => "ask",
        //                 "feeRate" => "-0.0015"
        //             }
        //         )
        //     }
        //
        $limitOrders = $this->safe_value($response, 'limitOrders', array());
        return $this->parse_orders($limitOrders, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
        );
        $response = $this->privatePostOrderCompleteOrders (array_merge($request, $params));
        //
        // despite the name of the endpoint it returns trades which may have a duplicate orderId
        // https://github.com/ccxt/ccxt/pull/7067
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0",
        //         "$completeOrders" => array(
        //             {
        //                 "timestamp" => "1416561032",
        //                 "price" => "419000.0",
        //                 "type" => "bid",
        //                 "qty" => "0.001",
        //                 "feeRate" => "-0.0015",
        //                 "fee" => "-0.0000015",
        //                 "orderId" => "E84A1AC2-8088-4FA0-B093-A3BCDB9B3C85"
        //             }
        //         )
        //     }
        //
        $completeOrders = $this->safe_value($response, 'completeOrders', array());
        return $this->parse_trades($completeOrders, $market, $since, $limit);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            // eslint-disable-next-line quotes
            throw new ArgumentsRequired($this->id . " cancelOrder() requires a $symbol argument. To cancel the order, pass a $symbol argument and array('price' => 12345, 'qty' => 1.2345, 'is_ask' => 0) in the $params argument of cancelOrder.");
        }
        $price = $this->safe_number($params, 'price');
        $qty = $this->safe_number($params, 'qty');
        $isAsk = $this->safe_integer($params, 'is_ask');
        if (($price === null) || ($qty === null) || ($isAsk === null)) {
            // eslint-disable-next-line quotes
            throw new ArgumentsRequired($this->id . " cancelOrder() requires array('price' => 12345, 'qty' => 1.2345, 'is_ask' => 0) in the $params argument.");
        }
        $this->load_markets();
        $request = array(
            'order_id' => $id,
            'price' => $price,
            'qty' => $qty,
            'is_ask' => $isAsk,
            'currency' => $this->market_id($symbol),
        );
        $response = $this->privatePostOrderCancel (array_merge($request, $params));
        //
        //     {
        //         "result" => "success",
        //         "errorCode" => "0"
        //     }
        //
        return $response;
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->urls['api'] . '/';
        if ($api === 'public') {
            $url .= $request;
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $url .= $this->version . '/' . $request;
            $nonce = (string) $this->nonce();
            $json = $this->json(array_merge(array(
                'access_token' => $this->apiKey,
                'nonce' => $nonce,
            ), $params));
            $payload = base64_encode($json);
            $body = $this->decode($payload);
            $secret = strtoupper($this->secret);
            $signature = $this->hmac($payload, $this->encode($secret), 'sha512');
            $headers = array(
                'Content-Type' => 'application/json',
                'X-COINONE-PAYLOAD' => $payload,
                'X-COINONE-SIGNATURE' => $signature,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        if (is_array($response) && array_key_exists('result', $response)) {
            $result = $response['result'];
            if ($result !== 'success') {
                //
                //    array(  "$errorCode" => "405",  "status" => "maintenance",  "$result" => "error")
                //
                $errorCode = $this->safe_string($response, 'errorCode');
                $feedback = $this->id . ' ' . $body;
                $this->throw_exactly_matched_exception($this->exceptions, $errorCode, $feedback);
                throw new ExchangeError($feedback);
            }
        } else {
            throw new ExchangeError($this->id . ' ' . $body);
        }
    }
}

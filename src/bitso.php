<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\OrderNotFound;

class bitso extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitso',
            'name' => 'Bitso',
            'countries' => array( 'MX' ), // Mexico
            'rateLimit' => 2000, // 30 requests per minute
            'version' => 'v3',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchDepositAddress' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrderTrades' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
                'withdraw' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87295554-11f98280-c50e-11ea-80d6-15b3bafa8cbf.jpg',
                'api' => 'https://api.bitso.com',
                'www' => 'https://bitso.com',
                'doc' => 'https://bitso.com/api_info',
                'fees' => 'https://bitso.com/fees',
                'referral' => 'https://bitso.com/?ref=itej',
            ),
            'precisionMode' => TICK_SIZE,
            'options' => array(
                'precision' => array(
                    'XRP' => 0.000001,
                    'MXN' => 0.01,
                    'TUSD' => 0.01,
                ),
                'defaultPrecision' => 0.00000001,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'available_books',
                        'ticker',
                        'order_book',
                        'trades',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'account_status',
                        'balance',
                        'fees',
                        'fundings',
                        'fundings/{fid}',
                        'funding_destination',
                        'kyc_documents',
                        'ledger',
                        'ledger/trades',
                        'ledger/fees',
                        'ledger/fundings',
                        'ledger/withdrawals',
                        'mx_bank_codes',
                        'open_orders',
                        'order_trades/{oid}',
                        'orders/{oid}',
                        'user_trades',
                        'user_trades/{tid}',
                        'withdrawals/',
                        'withdrawals/{wid}',
                    ),
                    'post' => array(
                        'bitcoin_withdrawal',
                        'debit_card_withdrawal',
                        'ether_withdrawal',
                        'ripple_withdrawal',
                        'bcash_withdrawal',
                        'litecoin_withdrawal',
                        'orders',
                        'phone_number',
                        'phone_verification',
                        'phone_withdrawal',
                        'spei_withdrawal',
                        'ripple_withdrawal',
                        'bcash_withdrawal',
                        'litecoin_withdrawal',
                    ),
                    'delete' => array(
                        'orders/{oid}',
                        'orders/all',
                    ),
                ),
            ),
            'exceptions' => array(
                '0201' => '\\ccxt\\AuthenticationError', // Invalid Nonce or Invalid Credentials
                '104' => '\\ccxt\\InvalidNonce', // Cannot perform request - nonce must be higher than 1520307203724237
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetAvailableBooks ($params);
        //
        //     {
        //         "success":true,
        //         "payload":array(
        //             {
        //                 "book":"btc_mxn",
        //                 "minimum_price":"500",
        //                 "maximum_price":"10000000",
        //                 "minimum_amount":"0.00005",
        //                 "maximum_amount":"500",
        //                 "minimum_value":"5",
        //                 "maximum_value":"10000000",
        //                 "tick_size":"0.01",
        //                 "$fees":array(
        //                     "flat_rate":array("$maker":"0.500","$taker":"0.650"),
        //                     "structure":array(
        //                         array("$volume":"1500000","$maker":"0.00500","$taker":"0.00650"),
        //                         array("$volume":"2000000","$maker":"0.00490","$taker":"0.00637"),
        //                         array("$volume":"5000000","$maker":"0.00480","$taker":"0.00624"),
        //                         array("$volume":"7000000","$maker":"0.00440","$taker":"0.00572"),
        //                         array("$volume":"10000000","$maker":"0.00420","$taker":"0.00546"),
        //                         array("$volume":"15000000","$maker":"0.00400","$taker":"0.00520"),
        //                         array("$volume":"35000000","$maker":"0.00370","$taker":"0.00481"),
        //                         array("$volume":"50000000","$maker":"0.00300","$taker":"0.00390"),
        //                         array("$volume":"150000000","$maker":"0.00200","$taker":"0.00260"),
        //                         array("$volume":"250000000","$maker":"0.00100","$taker":"0.00130"),
        //                         array("$volume":"9999999999","$maker":"0.00000","$taker":"0.00130"),
        //                     )
        //                 }
        //             ),
        //         )
        //     }
        $markets = $this->safe_value($response, 'payload');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'book');
            list($baseId, $quoteId) = explode('_', $id);
            $base = strtoupper($baseId);
            $quote = strtoupper($quoteId);
            $base = $this->safe_currency_code($base);
            $quote = $this->safe_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_number($market, 'minimum_amount'),
                    'max' => $this->safe_number($market, 'maximum_amount'),
                ),
                'price' => array(
                    'min' => $this->safe_number($market, 'minimum_price'),
                    'max' => $this->safe_number($market, 'maximum_price'),
                ),
                'cost' => array(
                    'min' => $this->safe_number($market, 'minimum_value'),
                    'max' => $this->safe_number($market, 'maximum_value'),
                ),
            );
            $defaultPricePrecision = $this->safe_number($this->options['precision'], $quote, $this->options['defaultPrecision']);
            $pricePrecision = $this->safe_number($market, 'tick_size', $defaultPricePrecision);
            $precision = array(
                'amount' => $this->safe_number($this->options['precision'], $base, $this->options['defaultPrecision']),
                'price' => $pricePrecision,
            );
            $fees = $this->safe_value($market, 'fees', array());
            $flatRate = $this->safe_value($fees, 'flat_rate', array());
            $maker = $this->safe_number($flatRate, 'maker');
            $taker = $this->safe_number($flatRate, 'taker');
            $feeTiers = $this->safe_value($fees, 'structure', array());
            $fee = array(
                'taker' => floatval($this->decimal_to_precision($taker / 100, ROUND, 0.00000001, TICK_SIZE)),
                'maker' => floatval($this->decimal_to_precision($maker / 100, ROUND, 0.00000001, TICK_SIZE)),
                'percentage' => true,
                'tierBased' => true,
            );
            $takerFees = array();
            $makerFees = array();
            for ($j = 0; $j < count($feeTiers); $j++) {
                $tier = $feeTiers[$j];
                $volume = $this->safe_number($tier, 'volume');
                $takerFee = $this->safe_number($tier, 'taker');
                $makerFee = $this->safe_number($tier, 'maker');
                $takerFeeToPrecision = floatval($this->decimal_to_precision($takerFee / 100, ROUND, 0.00000001, TICK_SIZE));
                $makerFeeToPrecision = floatval($this->decimal_to_precision($makerFee / 100, ROUND, 0.00000001, TICK_SIZE));
                $takerFees[] = array( $volume, $takerFeeToPrecision );
                $makerFees[] = array( $volume, $makerFeeToPrecision );
                if ($j === 0) {
                    $fee['taker'] = floatval($this->decimal_to_precision($taker / 100, ROUND, 0.00000001, TICK_SIZE));
                    $fee['maker'] = floatval($this->decimal_to_precision($maker / 100, ROUND, 0.00000001, TICK_SIZE));
                }
            }
            $tiers = array(
                'taker' => $takerFees,
                'maker' => $makerFees,
            );
            $fee['tiers'] = $tiers;
            $result[] = array_merge(array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'limits' => $limits,
                'precision' => $precision,
                'active' => null,
            ), $fee);
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetBalance ($params);
        $balances = $this->safe_value($response['payload'], 'balances');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = array(
                'free' => $this->safe_number($balance, 'available'),
                'used' => $this->safe_number($balance, 'locked'),
                'total' => $this->safe_number($balance, 'total'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'book' => $this->market_id($symbol),
        );
        $response = $this->publicGetOrderBook (array_merge($request, $params));
        $orderbook = $this->safe_value($response, 'payload');
        $timestamp = $this->parse8601($this->safe_string($orderbook, 'updated_at'));
        return $this->parse_order_book($orderbook, $timestamp, 'bids', 'asks', 'price', 'amount');
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $request = array(
            'book' => $this->market_id($symbol),
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $ticker = $this->safe_value($response, 'payload');
        $timestamp = $this->parse8601($this->safe_string($ticker, 'created_at'));
        $vwap = $this->safe_number($ticker, 'vwap');
        $baseVolume = $this->safe_number($ticker, 'volume');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
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
            'vwap' => $vwap,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        $marketId = $this->safe_string($trade, 'book');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $side = $this->safe_string_2($trade, 'side', 'maker_side');
        $amount = $this->safe_number_2($trade, 'amount', 'major');
        if ($amount !== null) {
            $amount = abs($amount);
        }
        $fee = null;
        $feeCost = $this->safe_number($trade, 'fees_amount');
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'fees_currency');
            $feeCurrency = $this->safe_currency_code($feeCurrencyId);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        $cost = $this->safe_number($trade, 'minor');
        if ($cost !== null) {
            $cost = abs($cost);
        }
        $price = $this->safe_number($trade, 'price');
        $orderId = $this->safe_string($trade, 'oid');
        $id = $this->safe_string($trade, 'tid');
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
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

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'book' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response['payload'], $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = 25, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        // the don't support fetching trades starting from a date yet
        // use the `marker` extra param for that
        // this is not a typo, the variable name is 'marker' (don't confuse with 'market')
        $markerInParams = (is_array($params) && array_key_exists('marker', $params));
        // warn the user with an exception if the user wants to filter
        // starting from $since timestamp, but does not set the trade id with an extra 'marker' param
        if (($since !== null) && !$markerInParams) {
            throw new ExchangeError($this->id . ' fetchMyTrades does not support fetching trades starting from a timestamp with the `$since` argument, use the `marker` extra param to filter starting from an integer trade id');
        }
        // convert it to an integer unconditionally
        if ($markerInParams) {
            $params = array_merge($params, array(
                'marker' => intval($params['marker']),
            ));
        }
        $request = array(
            'book' => $market['id'],
            'limit' => $limit, // default = 25, max = 100
            // 'sort' => 'desc', // default = desc
            // 'marker' => id, // integer id to start from
        );
        $response = $this->privateGetUserTrades (array_merge($request, $params));
        return $this->parse_trades($response['payload'], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'book' => $this->market_id($symbol),
            'side' => $side,
            'type' => $type,
            'major' => $this->amount_to_precision($symbol, $amount),
        );
        if ($type === 'limit') {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        $id = $this->safe_string($response['payload'], 'oid');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'oid' => $id,
        );
        return $this->privateDeleteOrdersOid (array_merge($request, $params));
    }

    public function parse_order_status($status) {
        $statuses = array(
            'partial-fill' => 'open', // this is a common substitution in ccxt
            'completed' => 'closed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        $id = $this->safe_string($order, 'oid');
        $side = $this->safe_string($order, 'side');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'book');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $orderType = $this->safe_string($order, 'type');
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $price = $this->safe_number($order, 'price');
        $amount = $this->safe_number($order, 'original_amount');
        $remaining = $this->safe_number($order, 'unfilled_amount');
        $clientOrderId = $this->safe_string($order, 'client_id');
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $orderType,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'cost' => null,
            'remaining' => $remaining,
            'filled' => null,
            'status' => $status,
            'fee' => null,
            'average' => null,
            'trades' => null,
        ));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = 25, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        // the don't support fetching trades starting from a date yet
        // use the `marker` extra param for that
        // this is not a typo, the variable name is 'marker' (don't confuse with 'market')
        $markerInParams = (is_array($params) && array_key_exists('marker', $params));
        // warn the user with an exception if the user wants to filter
        // starting from $since timestamp, but does not set the trade id with an extra 'marker' param
        if (($since !== null) && !$markerInParams) {
            throw new ExchangeError($this->id . ' fetchOpenOrders does not support fetching $orders starting from a timestamp with the `$since` argument, use the `marker` extra param to filter starting from an integer trade id');
        }
        // convert it to an integer unconditionally
        if ($markerInParams) {
            $params = array_merge($params, array(
                'marker' => intval($params['marker']),
            ));
        }
        $request = array(
            'book' => $market['id'],
            'limit' => $limit, // default = 25, max = 100
            // 'sort' => 'desc', // default = desc
            // 'marker' => id, // integer id to start from
        );
        $response = $this->privateGetOpenOrders (array_merge($request, $params));
        $orders = $this->parse_orders($response['payload'], $market, $since, $limit);
        return $orders;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privateGetOrdersOid (array(
            'oid' => $id,
        ));
        $payload = $this->safe_value($response, 'payload');
        if (gettype($payload) === 'array' && count(array_filter(array_keys($payload), 'is_string')) == 0) {
            $numOrders = is_array($response['payload']) ? count($response['payload']) : 0;
            if ($numOrders === 1) {
                return $this->parse_order($payload[0]);
            }
        }
        throw new OrderNotFound($this->id . ' => The order ' . $id . ' not found.');
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'oid' => $id,
        );
        $response = $this->privateGetOrderTradesOid (array_merge($request, $params));
        return $this->parse_trades($response['payload'], $market);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'fund_currency' => $currency['id'],
        );
        $response = $this->privateGetFundingDestination (array_merge($request, $params));
        $address = $this->safe_string($response['payload'], 'account_identifier');
        $tag = null;
        if (mb_strpos($address, '?dt=') !== false) {
            $parts = explode('?dt=', $address);
            $address = $this->safe_string($parts, 0);
            $tag = $this->safe_string($parts, 1);
        }
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $methods = array(
            'BTC' => 'Bitcoin',
            'ETH' => 'Ether',
            'XRP' => 'Ripple',
            'BCH' => 'Bcash',
            'LTC' => 'Litecoin',
        );
        $method = (is_array($methods) && array_key_exists($code, $methods)) ? $methods[$code] : null;
        if ($method === null) {
            throw new ExchangeError($this->id . ' not valid withdraw coin => ' . $code);
        }
        $request = array(
            'amount' => $amount,
            'address' => $address,
            'destination_tag' => $tag,
        );
        $classMethod = 'privatePost' . $method . 'Withdrawal';
        $response = $this->$classMethod (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $this->safe_string($response['payload'], 'wid'),
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $endpoint = '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($method === 'GET') {
            if ($query) {
                $endpoint .= '?' . $this->urlencode($query);
            }
        }
        $url = $this->urls['api'] . $endpoint;
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $request = implode('', array($nonce, $method, $endpoint));
            if ($method !== 'GET') {
                if ($query) {
                    $body = $this->json($query);
                    $request .= $body;
                }
            }
            $signature = $this->hmac($this->encode($request), $this->encode($this->secret));
            $auth = $this->apiKey . ':' . $nonce . ':' . $signature;
            $headers = array(
                'Authorization' => 'Bitso ' . $auth,
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        if (is_array($response) && array_key_exists('success', $response)) {
            //
            //     array("$success":false,"$error":array("$code":104,"message":"Cannot perform request - nonce must be higher than 1520307203724237"))
            //
            $success = $this->safe_value($response, 'success', false);
            if (gettype($success) === 'string') {
                if (($success === 'true') || ($success === '1')) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
            if (!$success) {
                $feedback = $this->id . ' ' . $this->json($response);
                $error = $this->safe_value($response, 'error');
                if ($error === null) {
                    throw new ExchangeError($feedback);
                }
                $code = $this->safe_string($error, 'code');
                $this->throw_exactly_matched_exception($this->exceptions, $code, $feedback);
                throw new ExchangeError($feedback);
            }
        }
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('success', $response)) {
            if ($response['success']) {
                return $response;
            }
        }
        throw new ExchangeError($this->id . ' ' . $this->json($response));
    }
}

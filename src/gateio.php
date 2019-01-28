<?php

namespace ccxt;

use Exception as Exception; // a common import

class gateio extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'gateio',
            'name' => 'Gate.io',
            'countries' => array ( 'CN' ),
            'version' => '2',
            'rateLimit' => 1000,
            'has' => array (
                'CORS' => false,
                'createMarketOrder' => false,
                'fetchTickers' => true,
                'withdraw' => true,
                'createDepositAddress' => true,
                'fetchDepositAddress' => true,
                'fetchClosedOrders' => true,
                'fetchOpenOrders' => true,
                'fetchOrderTrades' => true,
                'fetchOrders' => true,
                'fetchOrder' => true,
                'fetchMyTrades' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/31784029-0313c702-b509-11e7-9ccc-bc0da6a0e435.jpg',
                'api' => array (
                    'public' => 'https://data.gate.io/api',
                    'private' => 'https://data.gate.io/api',
                ),
                'www' => 'https://gate.io/',
                'doc' => 'https://gate.io/api2',
                'fees' => array (
                    'https://gate.io/fee',
                    'https://support.gate.io/hc/en-us/articles/115003577673',
                ),
                'referral' => 'https://www.gate.io/signup/2436035',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'pairs',
                        'marketinfo',
                        'marketlist',
                        'tickers',
                        'ticker/{id}',
                        'orderBook/{id}',
                        'trade/{id}',
                        'tradeHistory/{id}',
                        'tradeHistory/{id}/{tid}',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'balances',
                        'depositAddress',
                        'newAddress',
                        'depositsWithdrawals',
                        'buy',
                        'sell',
                        'cancelOrder',
                        'cancelAllOrders',
                        'getOrder',
                        'openOrders',
                        'tradeHistory',
                        'withdraw',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => true,
                    'percentage' => true,
                    'maker' => 0.002,
                    'taker' => 0.002,
                ),
            ),
            'exceptions' => array (
                '4' => '\\ccxt\\DDoSProtection',
                '7' => '\\ccxt\\NotSupported',
                '8' => '\\ccxt\\NotSupported',
                '9' => '\\ccxt\\NotSupported',
                '15' => '\\ccxt\\DDoSProtection',
                '16' => '\\ccxt\\OrderNotFound',
                '17' => '\\ccxt\\OrderNotFound',
                '21' => '\\ccxt\\InsufficientFunds',
            ),
            // https://gate.io/api2#errCode
            'errorCodeNames' => array (
                '1' => 'Invalid request',
                '2' => 'Invalid version',
                '3' => 'Invalid request',
                '4' => 'Too many attempts',
                '5' => 'Invalid sign',
                '6' => 'Invalid sign',
                '7' => 'Currency is not supported',
                '8' => 'Currency is not supported',
                '9' => 'Currency is not supported',
                '10' => 'Verified failed',
                '11' => 'Obtaining address failed',
                '12' => 'Empty params',
                '13' => 'Internal error, please report to administrator',
                '14' => 'Invalid user',
                '15' => 'Cancel order too fast, please wait 1 min and try again',
                '16' => 'Invalid order id or order is already closed',
                '17' => 'Invalid orderid',
                '18' => 'Invalid amount',
                '19' => 'Not permitted or trade is disabled',
                '20' => 'Your order size is too small',
                '21' => 'You don\'t have enough fund',
            ),
            'options' => array (
                'limits' => array (
                    'cost' => array (
                        'min' => array (
                            'BTC' => 0.0001,
                            'ETH' => 0.001,
                            'USDT' => 1,
                        ),
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetMarketinfo ();
        $markets = $this->safe_value($response, 'pairs');
        if (!$markets)
            throw new ExchangeError ($this->id . ' fetchMarkets got an unrecognized response');
        $result = array ();
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $keys = is_array ($market) ? array_keys ($market) : array ();
            $id = $keys[0];
            $details = $market[$id];
            list ($baseId, $quoteId) = explode ('_', $id);
            $base = strtoupper ($baseId);
            $quote = strtoupper ($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => 8,
                'price' => $details['decimal_places'],
            );
            $amountLimits = array (
                'min' => $details['min_amount'],
                'max' => null,
            );
            $priceLimits = array (
                'min' => pow (10, -$details['decimal_places']),
                'max' => null,
            );
            $defaultCost = $amountLimits['min'] * $priceLimits['min'];
            $minCost = $this->safe_float($this->options['limits']['cost']['min'], $quote, $defaultCost);
            $costLimits = array (
                'min' => $minCost,
                'max' => null,
            );
            $limits = array (
                'amount' => $amountLimits,
                'price' => $priceLimits,
                'cost' => $costLimits,
            );
            $active = true;
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => $active,
                'maker' => $details['fee'] / 100,
                'taker' => $details['fee'] / 100,
                'precision' => $precision,
                'limits' => $limits,
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $balance = $this->privatePostBalances ();
        $result = array ( 'info' => $balance );
        $currencies = is_array ($this->currencies) ? array_keys ($this->currencies) : array ();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $code = $this->common_currency_code($currency);
            $account = $this->account ();
            if (is_array ($balance) && array_key_exists ('available', $balance)) {
                if (is_array ($balance['available']) && array_key_exists ($currency, $balance['available'])) {
                    $account['free'] = floatval ($balance['available'][$currency]);
                }
            }
            if (is_array ($balance) && array_key_exists ('locked', $balance)) {
                if (is_array ($balance['locked']) && array_key_exists ($currency, $balance['locked'])) {
                    $account['used'] = floatval ($balance['locked'][$currency]);
                }
            }
            $account['total'] = $this->sum ($account['free'], $account['used']);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $orderbook = $this->publicGetOrderBookId (array_merge (array (
            'id' => $this->market_id($symbol),
        ), $params));
        return $this->parse_order_book($orderbook);
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->milliseconds ();
        $symbol = null;
        if ($market)
            $symbol = $market['symbol'];
        $last = $this->safe_float($ticker, 'last');
        $percentage = $this->safe_float($ticker, 'percentChange');
        $open = null;
        $change = null;
        $average = null;
        if (($last !== null) && ($percentage !== null)) {
            $relativeChange = $percentage / 100;
            $open = $last / $this->sum (1, $relativeChange);
            $change = $last - $open;
            $average = $this->sum ($last, $open) / 2;
        }
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high24hr'),
            'low' => $this->safe_float($ticker, 'low24hr'),
            'bid' => $this->safe_float($ticker, 'highestBid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'lowestAsk'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'quoteVolume' => $this->safe_float($ticker, 'baseVolume'),
            'info' => $ticker,
        );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response) {
        if (strlen ($body) <= 0) {
            return;
        }
        if ($body[0] !== '{') {
            return;
        }
        $resultString = $this->safe_string($response, 'result', '');
        if ($resultString !== 'false') {
            return;
        }
        $errorCode = $this->safe_string($response, 'code');
        if ($errorCode !== null) {
            $exceptions = $this->exceptions;
            $errorCodeNames = $this->errorCodeNames;
            if (is_array ($exceptions) && array_key_exists ($errorCode, $exceptions)) {
                $message = '';
                if (is_array ($errorCodeNames) && array_key_exists ($errorCode, $errorCodeNames)) {
                    $message = $errorCodeNames[$errorCode];
                } else {
                    $message = $this->safe_string($response, 'message', '(unknown)');
                }
                throw new $exceptions[$errorCode] ($message);
            }
        }
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $tickers = $this->publicGetTickers ($params);
        $result = array ();
        $ids = is_array ($tickers) ? array_keys ($tickers) : array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            list ($baseId, $quoteId) = explode ('_', $id);
            $base = strtoupper ($baseId);
            $quote = strtoupper ($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $ticker = $tickers[$id];
            $market = null;
            if (is_array ($this->markets) && array_key_exists ($symbol, $this->markets))
                $market = $this->markets[$symbol];
            if (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id))
                $market = $this->markets_by_id[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $result;
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $ticker = $this->publicGetTickerId (array_merge (array (
            'id' => $market['id'],
        ), $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade ($trade, $market) {
        // public fetchTrades
        $timestamp = $this->safe_integer($trade, 'timestamp');
        // private fetchMyTrades
        $timestamp = $this->safe_integer($trade, 'time_unix', $timestamp);
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        $id = $this->safe_string($trade, 'tradeID');
        $id = $this->safe_string($trade, 'id', $id);
        // take either of orderid or $orderId
        $orderId = $this->safe_string($trade, 'orderid');
        $orderId = $this->safe_string($trade, 'orderNumber', $orderId);
        $price = $this->safe_float($trade, 'rate');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        return array (
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $market['symbol'],
            'order' => $orderId,
            'type' => null,
            'side' => $trade['type'],
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTradeHistoryId (array_merge (array (
            'id' => $market['id'],
        ), $params));
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->privatePostOpenOrders ($params);
        return $this->parse_orders($response['orders'], null, $since, $limit);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetOrder (array_merge (array (
            'orderNumber' => $id,
            'currencyPair' => $this->market_id($symbol),
        ), $params));
        return $this->parse_order($response['order']);
    }

    public function parse_order_status ($status) {
        $statuses = array (
            'cancelled' => 'canceled',
            // 'closed' => 'closed', // these two $statuses aren't actually needed
            // 'open' => 'open', // as they are mapped one-to-one
        );
        if (is_array ($statuses) && array_key_exists ($status, $statuses))
            return $statuses[$status];
        return $status;
    }

    public function parse_order ($order, $market = null) {
        //
        //    array ('amount' => '0.00000000',
        //     'currencyPair' => 'xlm_usdt',
        //     'fee' => '0.0113766632239302 USDT',
        //     'feeCurrency' => 'USDT',
        //     'feePercentage' => 0.18,
        //     'feeValue' => '0.0113766632239302',
        //     'filledAmount' => '30.14004987',
        //     'filledRate' => 0.2097,
        //     'initialAmount' => '30.14004987',
        //     'initialRate' => '0.2097',
        //     'left' => 0,
        //     'orderNumber' => '998307286',
        //     'rate' => '0.2097',
        //     'status' => 'closed',
        //     'timestamp' => 1531158583,
        //     'type' => 'sell'),
        //
        $id = $this->safe_string($order, 'orderNumber');
        $symbol = null;
        $marketId = $this->safe_string($order, 'currencyPair');
        if (is_array ($this->markets_by_id) && array_key_exists ($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
        }
        if ($market !== null)
            $symbol = $market['symbol'];
        $timestamp = $this->safe_integer($order, 'timestamp');
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $side = $this->safe_string($order, 'type');
        $price = $this->safe_float($order, 'filledRate');
        $amount = $this->safe_float($order, 'initialAmount');
        $filled = $this->safe_float($order, 'filledAmount');
        $remaining = $this->safe_float($order, 'leftAmount');
        if ($remaining === null) {
            // In the $order $status response, this field has a different name.
            $remaining = $this->safe_float($order, 'left');
        }
        $feeCost = $this->safe_float($order, 'feeValue');
        $feeCurrency = $this->safe_string($order, 'feeCurrency');
        $feeRate = $this->safe_float($order, 'feePercentage');
        if ($feeRate !== null) {
            $feeRate = $feeRate / 100;
        }
        if ($feeCurrency !== null) {
            if (is_array ($this->currencies_by_id) && array_key_exists ($feeCurrency, $this->currencies_by_id)) {
                $feeCurrency = $this->currencies_by_id[$feeCurrency]['code'];
            }
        }
        return array (
            'id' => $id,
            'datetime' => $this->iso8601 ($timestamp),
            'timestamp' => $timestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => array (
                'cost' => $feeCost,
                'currency' => $feeCurrency,
                'rate' => $feeRate,
            ),
            'info' => $order,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market')
            throw new ExchangeError ($this->id . ' allows limit orders only');
        $this->load_markets();
        $method = 'privatePost' . $this->capitalize ($side);
        $market = $this->market ($symbol);
        $order = array (
            'currencyPair' => $market['id'],
            'rate' => $price,
            'amount' => $amount,
        );
        $response = $this->$method (array_merge ($order, $params));
        return $this->parse_order(array_merge (array (
            'status' => 'open',
            'type' => $side,
            'initialAmount' => $amount,
        ), $response), $market);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        if ($symbol === null)
            throw new ArgumentsRequired ($this->id . ' cancelOrder requires $symbol argument');
        $this->load_markets();
        return $this->privatePostCancelOrder (array (
            'orderNumber' => $id,
            'currencyPair' => $this->market_id($symbol),
        ));
    }

    public function query_deposit_address ($method, $code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $method = 'privatePost' . $method . 'Address';
        $response = $this->$method (array_merge (array (
            'currency' => $currency['id'],
        ), $params));
        $address = $this->safe_string($response, 'addr');
        $tag = null;
        if (($address !== null) && (mb_strpos ($address, 'address') !== false))
            throw new InvalidAddress ($this->id . ' queryDepositAddress ' . $address);
        if ($code === 'XRP') {
            $parts = explode (' ', $address);
            $address = $parts[0];
            $tag = $parts[1];
        }
        return array (
            'currency' => $currency,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function create_deposit_address ($code, $params = array ()) {
        return $this->query_deposit_address ('New', $code, $params);
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        return $this->query_deposit_address ('Deposit', $code, $params);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
        }
        $response = $this->privatePostOpenOrders ();
        return $this->parse_orders($response['orders'], $market, $since, $limit);
    }

    public function fetch_order_trades ($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired ($this->id . ' fetchMyTrades requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->privatePostTradeHistory (array_merge (array (
            'currencyPair' => $market['id'],
            'orderNumber' => $id,
        ), $params));
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null)
            throw new ExchangeError ($this->id . ' fetchMyTrades requires $symbol param');
        $this->load_markets();
        $market = $this->market ($symbol);
        $id = $market['id'];
        $response = $this->privatePostTradeHistory (array_merge (array ( 'currencyPair' => $id ), $params));
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address, // Address must exist in you AddressBook in security settings
        );
        if ($tag !== null) {
            $request['address'] .= ' ' . $tag;
        }
        $response = $this->privatePostWithdraw (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => null,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $prefix = ($api === 'private') ? ($api . '/') : '';
        $url = $this->urls['api'][$api] . $this->version . '/1/' . $prefix . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query)
                $url .= '?' . $this->urlencode ($query);
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $request = array ( 'nonce' => $nonce );
            $body = $this->urlencode (array_merge ($request, $query));
            $signature = $this->hmac ($this->encode ($body), $this->encode ($this->secret), 'sha512');
            $headers = array (
                'Key' => $this->apiKey,
                'Sign' => $signature,
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if (is_array ($response) && array_key_exists ('result', $response)) {
            $result = $response['result'];
            $message = $this->id . ' ' . $this->json ($response);
            if ($result === null)
                throw new ExchangeError ($message);
            if (gettype ($result) === 'string') {
                if ($result !== 'true')
                    throw new ExchangeError ($message);
            } else if (!$result) {
                throw new ExchangeError ($message);
            }
        }
        return $response;
    }
}

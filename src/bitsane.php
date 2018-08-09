<?php

namespace ccxt;

use Exception as Exception; // a common import

class bitsane extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitsane',
            'name' => 'Bitsane',
            'countries' => array ( 'IE' ), // Ireland
            'has' => array (
                'fetchCurrencies' => true,
                'fetchTickers' => true,
                'fetchOpenOrders' => true,
                'fetchDepositAddress' => true,
                'withdraw' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/41387105-d86bf4c6-6f8d-11e8-95ea-2fa943872955.jpg',
                'api' => 'https://bitsane.com/api',
                'www' => 'https://bitsane.com',
                'doc' => 'https://bitsane.com/info-api',
                'fees' => 'https://bitsane.com/fees',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'assets/currencies',
                        'assets/pairs',
                        'ticker',
                        'orderbook',
                        'trades',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'balances',
                        'order/cancel',
                        'order/new',
                        'order/status',
                        'orders',
                        'orders/history',
                        'deposit/address',
                        'withdraw',
                        'withdrawal/status',
                        'transactions/history',
                        'vouchers',
                        'vouchers/create',
                        'vouchers/redeem',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.15 / 100,
                    'taker' => 0.25 / 100,
                ),
            ),
            'exceptions' => array (
                '3' => '\\ccxt\\AuthenticationError',
                '4' => '\\ccxt\\AuthenticationError',
                '5' => '\\ccxt\\AuthenticationError',
                '6' => '\\ccxt\\InvalidNonce',
                '7' => '\\ccxt\\AuthenticationError',
                '8' => '\\ccxt\\InvalidNonce',
                '9' => '\\ccxt\\AuthenticationError',
                '10' => '\\ccxt\\AuthenticationError',
                '11' => '\\ccxt\\AuthenticationError',
            ),
            'options' => array (
                'defaultCurrencyPrecision' => 2,
            ),
        ));
    }

    public function fetch_currencies ($params = array ()) {
        $currencies = $this->publicGetAssetsCurrencies ($params);
        $ids = is_array ($currencies) ? array_keys ($currencies) : array ();
        $result = array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $currency = $currencies[$id];
            $precision = $this->safe_integer($currency, 'precision', $this->options['defaultCurrencyPrecision']);
            $code = $this->common_currency_code($id);
            $canWithdraw = $this->safe_value($currency, 'withdrawal', true);
            $canDeposit = $this->safe_value($currency, 'deposit', true);
            $active = true;
            if (!$canWithdraw || !$canDeposit)
                $active = false;
            $result[$code] = array (
                'id' => $id,
                'code' => $code,
                'name' => $this->safe_string($currency, 'full_name', $code),
                'active' => $active,
                'precision' => $precision,
                'funding' => array (
                    'withdraw' => array (
                        'active' => $canWithdraw,
                        'fee' => $this->safe_value($currency, 'withdrawal_fee'),
                    ),
                    'deposit' => array (
                        'active' => $canDeposit,
                        'fee' => $this->safe_value($currency, 'deposit_fee'),
                    ),
                ),
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($currency, 'minAmountTrade'),
                        'max' => pow (10, $precision),
                    ),
                    'price' => array (
                        'min' => pow (10, -$precision),
                        'max' => pow (10, $precision),
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $currency,
            );
        }
        return $result;
    }

    public function fetch_markets () {
        $markets = $this->publicGetAssetsPairs ();
        $result = array ();
        $marketIds = is_array ($markets) ? array_keys ($markets) : array ();
        for ($i = 0; $i < count ($marketIds); $i++) {
            $id = $marketIds[$i];
            $market = $markets[$id];
            $base = $this->common_currency_code($market['base']);
            $quote = $this->common_currency_code($market['quote']);
            $symbol = $base . '/' . $quote;
            $limits = $this->safe_value($market, 'limits');
            $minLimit = null;
            $maxLimit = null;
            if ($limits !== null) {
                $minLimit = $this->safe_float($limits, 'minimum');
                $maxLimit = $this->safe_float($limits, 'maximum');
            }
            $precision = array (
                'amount' => intval ($market['precision']),
                'price' => 8,
            );
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $market['base'],
                'quoteId' => $market['quote'],
                'active' => true,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => $minLimit,
                        'max' => $maxLimit,
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
                'info' => $id,
            );
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $symbol = $market['symbol'];
        $timestamp = $this->milliseconds ();
        $last = $this->safe_float($ticker, 'last');
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
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_float($ticker, 'percentChange'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'baseVolume'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $tickers = $this->fetch_tickers([$symbol], $params);
        return $tickers[$symbol];
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array ();
        if ($symbols) {
            $ids = $this->market_ids($symbols);
            $request['pairs'] = implode (',', $ids);
        }
        $tickers = $this->publicGetTicker (array_merge ($request, $params));
        $marketIds = is_array ($tickers) ? array_keys ($tickers) : array ();
        $result = array ();
        for ($i = 0; $i < count ($marketIds); $i++) {
            $id = $marketIds[$i];
            $market = $this->safe_value($this->marketsById, $id);
            if ($market === null) {
                continue;
            }
            $symbol = $market['symbol'];
            $ticker = $tickers[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetOrderbook (array_merge (array (
            'pair' => $this->market_id($symbol),
        ), $params));
        return $this->parse_order_book($response['result'], null, 'bids', 'asks', 'price', 'amount');
    }

    public function parse_trade ($trade, $market = null) {
        $symbol = $market['symbol'];
        $timestamp = intval ($trade['timestamp']) * 1000;
        $price = floatval ($trade['price']);
        $amount = floatval ($trade['amount']);
        $cost = $this->cost_to_precision($symbol, $price * $amount);
        return array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => $this->safe_string($trade, 'tid'),
            'order' => null,
            'type' => null,
            'side' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => floatval ($cost),
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'pair' => $market['id'],
        );
        if ($since !== null)
            $request['since'] = intval ($since / 1000);
        if ($limit !== null)
            $request['limit'] = $limit;
        $response = $this->publicGetTrades (array_merge ($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalances ($params);
        $result = array ( 'info' => $response );
        $balances = $response['result'];
        $ids = is_array ($balances) ? array_keys ($balances) : array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $balance = $balances[$id];
            $code = $id;
            if (is_array ($this->currencies_by_id) && array_key_exists ($id, $this->currencies_by_id)) {
                $code = $this->currencies_by_id[$id]['code'];
            } else {
                $code = $this->common_currency_code($code);
            }
            $account = array (
                'free' => floatval ($balance['amount']),
                'used' => floatval ($balance['locked']),
                'total' => floatval ($balance['total']),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order ($order, $market = null) {
        $symbol = null;
        if (!$market)
            $market = $this->safe_value($this->marketsById, $order['pair']);
        if ($market)
            $symbol = $market['symbol'];
        $timestamp = $this->safe_integer($order, 'timestamp') * 1000;
        $price = floatval ($order['price']);
        $amount = $this->safe_float($order, 'original_amount');
        $filled = $this->safe_float($order, 'executed_amount');
        $remaining = $this->safe_float($order, 'remaining_amount');
        $status = 'closed';
        if ($order['is_cancelled']) {
            $status = 'canceled';
        } else if ($order['is_live']) {
            $status = 'open';
        }
        return array (
            'id' => $this->safe_string($order, 'id'),
            'datetime' => $this->iso8601 ($timestamp),
            'timestamp' => $timestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $this->safe_string($order, 'type'),
            'side' => $this->safe_string($order, 'side'),
            'price' => $price,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => null,
            'info' => $this->safe_value($order, 'info', $order),
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $order = array (
            'pair' => $market['id'],
            'amount' => $amount,
            'type' => $type,
            'side' => $side,
        );
        if ($type !== 'market')
            $order['price'] = $price;
        $response = $this->privatePostOrderNew (array_merge ($order, $params));
        $order['id'] = $response['result']['order_id'];
        $order['timestamp'] = $this->seconds ();
        $order['original_amount'] = $order['amount'];
        $order['info'] = $response;
        $order = $this->parse_order($order, $market);
        $id = $order['id'];
        $this->orders[$id] = $order;
        return $order;
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $response = $this->privatePostOrderCancel (array_merge (array (
            'order_id' => $id,
        ), $params));
        return $this->parse_order($response['result']);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrderStatus (array_merge (array (
            'order_id' => $id,
        ), $params));
        return $this->parse_order($response['result']);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrders ();
        return $this->parse_orders($response['result'], null, $since, $limit);
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $response = $this->privatePostDepositAddress (array_merge (array (
            'currency' => $currency['id'],
        ), $params));
        $address = $this->safe_string($response['result'], 'address');
        return array (
            'currency' => $code,
            'address' => $address,
            'info' => $response,
        );
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address,
        );
        if ($tag)
            $request['additional'] = $tag;
        $response = $this->privatePostWithdraw (array_merge ($request, $params));
        return array (
            'id' => $response['result']['withdrawal_id'],
            'info' => $response,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $api . '/' . $path;
        if ($api === 'public') {
            if ($params)
                $url .= '?' . $this->urlencode ($params);
        } else {
            $this->check_required_credentials();
            $body = array_merge (array (
                'nonce' => $this->nonce (),
            ), $params);
            $payload = $this->json ($body);
            $payload64 = base64_encode ($this->encode ($payload));
            $body = $this->decode ($payload64);
            $headers = array (
                'X-BS-APIKEY' => $this->apiKey,
                'X-BS-PAYLOAD' => $body,
                'X-BS-SIGNATURE' => $this->hmac ($payload64, $this->encode ($this->secret), 'sha384'),
            );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body) {
        if (gettype ($body) !== 'string')
            return; // fallback to default error handler
        if (strlen ($body) < 2)
            return; // fallback to default error handler
        if (($body[0] === '{') || ($body[0] === '[')) {
            $response = json_decode ($body, $as_associative_array = true);
            $statusCode = $this->safe_string($response, 'statusCode');
            if ($statusCode !== null) {
                if ($statusCode !== '0') {
                    $feedback = $this->id . ' ' . $this->json ($response);
                    $exceptions = $this->exceptions;
                    if (is_array ($exceptions) && array_key_exists ($statusCode, $exceptions)) {
                        throw new $exceptions[$statusCode] ($feedback);
                    } else {
                        throw new ExchangeError ($this->id . ' ' . $this->json ($response));
                    }
                }
            }
            return $response;
        }
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\ArgumentsRequired;

class ice3x extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'ice3x',
            'name' => 'ICE3X',
            'countries' => array( 'ZA' ), // South Africa
            'rateLimit' => 1000,
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87460809-1dd06c00-c616-11ea-98ad-7d5e1cb7fcdd.jpg',
                'api' => 'https://ice3x.com/api',
                'www' => 'https://ice3x.com', // 'https://ice3x.co.za',
                'doc' => 'https://ice3x.co.za/ice-cubed-bitcoin-exchange-api-documentation-1-june-2017',
                'fees' => array(
                    'https://help.ice3.com/support/solutions/articles/11000033293-trading-fees',
                    'https://help.ice3.com/support/solutions/articles/11000033288-fees-explained',
                    'https://help.ice3.com/support/solutions/articles/11000008131-what-are-your-fiat-deposit-and-withdrawal-fees-',
                    'https://help.ice3.com/support/solutions/articles/11000033289-deposit-fees',
                ),
                'referral' => 'https://ice3x.com?ref=14341802',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currency/list',
                        'currency/info',
                        'pair/list',
                        'pair/info',
                        'stats/marketdepthfull',
                        'stats/marketdepthbtcav',
                        'stats/marketdepth',
                        'orderbook/info',
                        'trade/list',
                        'trade/info',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'balance/list',
                        'balance/info',
                        'order/new',
                        'order/cancel',
                        'order/list',
                        'order/info',
                        'trade/list',
                        'trade/info',
                        'transaction/list',
                        'transaction/info',
                        'invoice/list',
                        'invoice/info',
                        'invoice/pdf',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.005,
                    'taker' => 0.005,
                ),
            ),
            'precision' => array(
                'amount' => 8,
                'price' => 8,
            ),
        ));
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencyList ($params);
        $currencies = $response['response']['entities'];
        $precision = $this->precision['amount'];
        $result = array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'currency_id');
            $currencyId = $this->safe_string($currency, 'iso');
            $code = $this->safe_currency_code($currencyId);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $currency['name'],
                'active' => true,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => null,
                        'max' => pow(10, $precision),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $currency,
                'fee' => null,
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        if ($this->currencies_by_id === null) {
            $this->currencies = $this->fetch_currencies();
            $this->currencies_by_id = $this->index_by($this->currencies, 'id');
        }
        $response = $this->publicGetPairList ($params);
        $markets = $this->safe_value($response['response'], 'entities');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'pair_id');
            $baseId = $this->safe_string($market, 'currency_id_from');
            $quoteId = $this->safe_string($market, 'currency_id_to');
            $baseCurrency = $this->currencies_by_id[$baseId];
            $quoteCurrency = $this->currencies_by_id[$quoteId];
            $base = $baseCurrency['code'];
            $quote = $quoteCurrency['code'];
            $symbol = $base . '/' . $quote;
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => null,
                'info' => $market,
                'precision' => $this->precision,
                'limits' => $this->limits,
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = $market['symbol'];
        $last = $this->safe_float($ticker, 'last_price');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'max'),
            'low' => $this->safe_float($ticker, 'min'),
            'bid' => $this->safe_float($ticker, 'max_bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'min_ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_float($ticker, 'avg'),
            'baseVolume' => null,
            'quoteVolume' => $this->safe_float($ticker, 'vol'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair_id' => $market['id'],
        );
        $response = $this->publicGetStatsMarketdepthfull (array_merge($request, $params));
        $ticker = $this->safe_value($response['response'], 'entity');
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetStatsMarketdepthfull ($params);
        $tickers = $this->safe_value($response['response'], 'entities');
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $tickers[$i];
            $marketId = $this->safe_string($ticker, 'pair_id');
            $market = $this->safe_value($this->marketsById, $marketId);
            if ($market !== null) {
                $symbol = $market['symbol'];
                $result[$symbol] = $this->parse_ticker($ticker, $market);
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair_id' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $type = $this->safe_string($params, 'type');
            if (($type !== 'ask') && ($type !== 'bid')) {
                // eslint-disable-next-line quotes
                throw new ArgumentsRequired($this->id . " fetchOrderBook() requires an exchange-specific extra 'type' param ('bid' or 'ask') when used with a $limit");
            } else {
                $request['items_per_page'] = $limit;
            }
        }
        $response = $this->publicGetOrderbookInfo (array_merge($request, $params));
        $orderbook = $this->safe_value($response['response'], 'entities');
        return $this->parse_order_book($orderbook, null, 'bids', 'asks', 'price', 'amount');
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'created');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'volume');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $market['quote'],
            );
        }
        $type = 'limit';
        $side = $this->safe_string($trade, 'type');
        $id = $this->safe_string($trade, 'trade_id');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'id' => $id,
            'info' => $trade,
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
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair_id' => $market['id'],
        );
        $response = $this->publicGetTradeList (array_merge($request, $params));
        $trades = $this->safe_value($response['response'], 'entities');
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalanceList ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response['response'], 'entities', array());
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            // currency ids are numeric strings
            $currencyId = $this->safe_string($balance, 'currency_id');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_float($balance, 'balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order($order, $market = null) {
        $pairId = $this->safe_integer($order, 'pair_id');
        $symbol = null;
        if ($pairId && !$market && (is_array($this->marketsById) && array_key_exists($pairId, $this->marketsById))) {
            $market = $this->marketsById[$pairId];
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_timestamp($order, 'created');
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'volume');
        $status = $this->safe_integer($order, 'active');
        $remaining = $this->safe_float($order, 'remaining');
        $filled = null;
        if ($status === 1) {
            $status = 'open';
        } else {
            $status = 'closed';
            $remaining = 0;
            $filled = $amount;
        }
        $fee = null;
        $feeCost = $this->safe_float($order, 'fee');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
            );
            if ($market !== null) {
                $fee['currency'] = $market['quote'];
            }
        }
        return array(
            'id' => $this->safe_string($order, 'order_id'),
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $this->safe_string($order, 'type'),
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => $fee,
            'info' => $order,
            'average' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair_id' => $market['id'],
            'type' => $side,
            'amount' => $amount,
            'price' => $price,
        );
        $response = $this->privatePostOrderNew (array_merge($request, $params));
        $order = $this->parse_order(array(
            'order_id' => $response['response']['entity']['order_id'],
            'created' => $this->seconds(),
            'active' => 1,
            'type' => $side,
            'price' => $price,
            'volume' => $amount,
            'remaining' => $amount,
            'info' => $response,
        ), $market);
        return $order;
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'order_id' => $id,
        );
        return $this->privatePostOrderCancel (array_merge($request, $params));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order _id' => $id,
        );
        $response = $this->privatePostOrderInfo (array_merge($request, $params));
        $data = $this->safe_value($response, 'response', array());
        $order = $this->safe_value($data, 'entity');
        return $this->parse_order($order);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrderList ($params);
        $data = $this->safe_value($response, 'response', array());
        $orders = $this->safe_value($data, 'entities', array());
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair_id' => $market['id'],
        );
        if ($limit !== null) {
            $request['items_per_page'] = $limit;
        }
        if ($since !== null) {
            $request['date_from'] = intval($since / 1000);
        }
        $response = $this->privatePostTradeList (array_merge($request, $params));
        $data = $this->safe_value($response, 'response', array());
        $trades = $this->safe_value($data, 'entities', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency_id' => $currency['id'],
        );
        $response = $this->privatePostBalanceInfo (array_merge($request, $params));
        $data = $this->safe_value($response, 'response', array());
        $balance = $this->safe_value($data, 'entity', array());
        $address = $this->safe_string($balance, 'address');
        $status = $address ? 'ok' : 'none';
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => null,
            'status' => $status,
            'info' => $response,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $this->check_required_credentials();
            $body = $this->urlencode(array_merge(array(
                'nonce' => $this->nonce(),
            ), $params));
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        $errors = $this->safe_value($response, 'errors');
        $data = $this->safe_value($response, 'response');
        if ($errors || !$data) {
            $authErrorKeys = array( 'Key', 'user_id', 'Sign' );
            for ($i = 0; $i < count($authErrorKeys); $i++) {
                $errorKey = $authErrorKeys[$i];
                $errorMessage = $this->safe_string($errors, $errorKey);
                if (!$errorMessage) {
                    continue;
                }
                if ($errorKey === 'user_id' && mb_strpos($errorMessage, 'authorization') < 0) {
                    continue;
                }
                throw new AuthenticationError($errorMessage);
            }
            throw new ExchangeError($this->json($errors));
        }
        return $response;
    }
}

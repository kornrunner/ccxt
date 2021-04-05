<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\ExchangeNotAvailable;

class exx extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'exx',
            'name' => 'EXX',
            'countries' => array( 'CN' ),
            'rateLimit' => 1000 / 10,
            'userAgent' => $this->userAgents['chrome'],
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/37770292-fbf613d0-2de4-11e8-9f79-f2dc451b8ccb.jpg',
                'api' => array(
                    'public' => 'https://api.exx.com/data/v1',
                    'private' => 'https://trade.exx.com/api',
                ),
                'www' => 'https://www.exx.com/',
                'doc' => 'https://www.exx.com/help/restApi',
                'fees' => 'https://www.exx.com/help/rate',
                'referral' => 'https://www.exx.com/r/fde4260159e53ab8a58cc9186d35501f?recommQd=1',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'markets',
                        'tickers',
                        'ticker',
                        'depth',
                        'trades',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'order',
                        'cancel',
                        'getOrder',
                        'getOpenOrders',
                        'getBalance',
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
                        'BCC' => 0.0003,
                        'BCD' => 0.0,
                        'BOT' => 10.0,
                        'BTC' => 0.001,
                        'BTG' => 0.0,
                        'BTM' => 25.0,
                        'BTS' => 3.0,
                        'EOS' => 1.0,
                        'ETC' => 0.01,
                        'ETH' => 0.01,
                        'ETP' => 0.012,
                        'HPY' => 0.0,
                        'HSR' => 0.001,
                        'INK' => 20.0,
                        'LTC' => 0.005,
                        'MCO' => 0.6,
                        'MONA' => 0.01,
                        'QASH' => 5.0,
                        'QCASH' => 5.0,
                        'QTUM' => 0.01,
                        'USDT' => 5.0,
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'DOS' => 'DEMOS',
                'TV' => 'TIV', // Ti-Value
            ),
            'exceptions' => array(
                '103' => '\\ccxt\\AuthenticationError',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetMarkets ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $response[$id];
            list($baseId, $quoteId) = explode('_', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $active = $market['isOpen'] === true;
            $precision = array(
                'amount' => intval($market['amountScale']),
                'price' => intval($market['priceScale']),
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => pow(10, $precision['amount']),
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
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $symbol = $market['symbol'];
        $timestamp = $this->safe_integer($ticker, 'date');
        $ticker = $ticker['ticker'];
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_number($ticker, 'riseRate'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        $result = array();
        $timestamp = $this->milliseconds();
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            if (!(is_array($this->marketsById) && array_key_exists($id, $this->marketsById))) {
                continue;
            }
            $market = $this->marketsById[$id];
            $symbol = $market['symbol'];
            $ticker = array(
                'date' => $timestamp,
                'ticker' => $response[$id],
            );
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'currency' => $this->market_id($symbol),
        );
        $response = $this->publicGetDepth (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($response, 'timestamp');
        return $this->parse_order_book($response, $timestamp);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
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
        $type = 'limit';
        $side = $this->safe_string($trade, 'type');
        $id = $this->safe_string($trade, 'tid');
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
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetGetBalance ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'funds');
        $currencies = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currencyId = $currencies[$i];
            $balance = $balances[$currencyId];
            $code = $this->safe_currency_code($currencyId);
            $account = array(
                'free' => $this->safe_number($balance, 'balance'),
                'used' => $this->safe_number($balance, 'freeze'),
                'total' => $this->safe_number($balance, 'total'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "fees" => 0,
        //         "total_amount" => 1,
        //         "trade_amount" => 0,
        //         "$price" => 30,
        //         "currency" => “eth_hsr",
        //         "id" => "13878",
        //         "trade_money" => 0,
        //         "type" => "buy",
        //         "trade_date" => 1509728897772,
        //         "$status" => 0
        //     }
        //
        $symbol = $market['symbol'];
        $timestamp = intval($order['trade_date']);
        $price = $this->safe_number($order, 'price');
        $cost = $this->safe_number($order, 'trade_money');
        $amount = $this->safe_number($order, 'total_amount');
        $filled = $this->safe_number($order, 'trade_amount', 0.0);
        $status = $this->safe_integer($order, 'status');
        if ($status === 1) {
            $status = 'canceled';
        } else if ($status === 2) {
            $status = 'closed';
        } else {
            $status = 'open';
        }
        $fee = null;
        if (is_array($order) && array_key_exists('fees', $order)) {
            $fee = array(
                'cost' => $this->safe_number($order, 'fees'),
                'currency' => $market['quote'],
            );
        }
        return $this->safe_order(array(
            'id' => $this->safe_string($order, 'id'),
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $order['type'],
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'trades' => null,
            'fee' => $fee,
            'info' => $order,
            'average' => null,
        ));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'type' => $side,
            'price' => $price,
            'amount' => $amount,
        );
        $response = $this->privateGetOrder (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        $order = $this->parse_order(array(
            'id' => $id,
            'trade_date' => $this->milliseconds(),
            'total_amount' => $amount,
            'price' => $price,
            'type' => $side,
            'info' => $response,
        ), $market);
        return $order;
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $id,
            'currency' => $market['id'],
        );
        $response = $this->privateGetCancel (array_merge($request, $params));
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $id,
            'currency' => $market['id'],
        );
        $response = $this->privateGetGetOrder (array_merge($request, $params));
        return $this->parse_order($response, $market);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
        );
        $response = $this->privateGetGetOpenOrders (array_merge($request, $params));
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) != 0) {
            return array();
        }
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $this->check_required_credentials();
            $query = $this->urlencode($this->keysort(array_merge(array(
                'accesskey' => $this->apiKey,
                'nonce' => $this->nonce(),
            ), $params)));
            $signed = $this->hmac($this->encode($query), $this->encode($this->secret), 'sha512');
            $url .= '?' . $query . '&signature=' . $signed;
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //
        //  array("$result":false,"$message":"服务端忙碌")
        //  ... and other formats
        //
        $code = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message');
        $feedback = $this->id . ' ' . $body;
        if ($code === '100') {
            return;
        }
        if ($code !== null) {
            $this->throw_exactly_matched_exception($this->exceptions, $code, $feedback);
            if ($code === '308') {
                // this is returned by the exchange when there are no open orders
                // array("$code":308,"$message":"Not Found Transaction Record")
                return;
            } else {
                throw new ExchangeError($feedback);
            }
        }
        $result = $this->safe_value($response, 'result');
        if ($result !== null) {
            if (!$result) {
                if ($message === '服务端忙碌') {
                    throw new ExchangeNotAvailable($feedback);
                } else {
                    throw new ExchangeError($feedback);
                }
            }
        }
    }
}

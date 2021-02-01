<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class coinegg extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinegg',
            'name' => 'CoinEgg',
            'countries' => array( 'CN', 'UK' ),
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => false,
                'fetchOpenOrders' => 'emulated',
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => false,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/36770310-adfa764e-1c5a-11e8-8e09-449daac3d2fb.jpg',
                'api' => array(
                    'web' => 'https://trade.coinegg.com/web',
                    'rest' => 'https://api.coinegg.com/api/v1',
                ),
                'www' => 'https://www.coinegg.com',
                'doc' => 'https://www.coinegg.com/explain.api.html',
                'fees' => 'https://www.coinegg.com/fee.html',
                'referral' => 'https://www.coinegg.com/user/register?invite=523218',
            ),
            'api' => array(
                'web' => array(
                    'get' => array(
                        'symbol/ticker?right_coin={quote}',
                        '{quote}/trends',
                        '{quote}/{base}/order',
                        '{quote}/{base}/trades',
                        '{quote}/{base}/depth.js',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'ticker/region/{quote}',
                        'depth/region/{quote}',
                        'orders/region/{quote}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'balance',
                        'trade_add/region/{quote}',
                        'trade_cancel/region/{quote}',
                        'trade_view/region/{quote}',
                        'trade_list/region/{quote}',
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
                        'BTC' => 0.008,
                        'BCH' => 0.002,
                        'LTC' => 0.001,
                        'ETH' => 0.01,
                        'ETC' => 0.01,
                        'NEO' => 0,
                        'QTUM' => '1%',
                        'XRP' => '1%',
                        'DOGE' => '1%',
                        'LSK' => '1%',
                        'XAS' => '1%',
                        'BTS' => '1%',
                        'GAME' => '1%',
                        'GOOC' => '1%',
                        'NXT' => '1%',
                        'IFC' => '1%',
                        'DNC' => '1%',
                        'BLK' => '1%',
                        'VRC' => '1%',
                        'XPM' => '1%',
                        'VTC' => '1%',
                        'TFC' => '1%',
                        'PLC' => '1%',
                        'EAC' => '1%',
                        'PPC' => '1%',
                        'FZ' => '1%',
                        'ZET' => '1%',
                        'RSS' => '1%',
                        'PGC' => '1%',
                        'SKT' => '1%',
                        'JBC' => '1%',
                        'RIO' => '1%',
                        'LKC' => '1%',
                        'ZCC' => '1%',
                        'MCC' => '1%',
                        'QEC' => '1%',
                        'MET' => '1%',
                        'YTC' => '1%',
                        'HLB' => '1%',
                        'MRYC' => '1%',
                        'MTC' => '1%',
                        'KTC' => 0,
                    ),
                ),
            ),
            'exceptions' => array(
                '103' => '\\ccxt\\AuthenticationError',
                '104' => '\\ccxt\\AuthenticationError',
                '105' => '\\ccxt\\AuthenticationError',
                '106' => '\\ccxt\\InvalidNonce',
                '200' => '\\ccxt\\InsufficientFunds',
                '201' => '\\ccxt\\InvalidOrder',
                '202' => '\\ccxt\\InvalidOrder',
                '203' => '\\ccxt\\OrderNotFound',
                '402' => '\\ccxt\\DDoSProtection',
            ),
            'errorMessages' => array(
                '100' => 'Required parameters can not be empty',
                '101' => 'Illegal parameter',
                '102' => 'coin does not exist',
                '103' => 'Key does not exist',
                '104' => 'Signature does not match',
                '105' => 'Insufficient permissions',
                '106' => 'Request expired(nonce error)',
                '200' => 'Lack of balance',
                '201' => 'Too small for the number of trading',
                '202' => 'Price must be in 0 - 1000000',
                '203' => 'Order does not exist',
                '204' => 'Pending order amount must be above 0.001 BTC',
                '205' => 'Restrict pending order prices',
                '206' => 'Decimal place error',
                '401' => 'System error',
                '402' => 'Requests are too frequent',
                '403' => 'Non-open API',
                '404' => 'IP restriction does not request the resource',
                '405' => 'Currency transactions are temporarily closed',
            ),
            'options' => array(
                'quoteIds' => array( 'btc', 'eth', 'usc', 'usdt' ),
            ),
            'commonCurrencies' => array(
                'JBC' => 'JubaoCoin',
                'SBTC' => 'Super Bitcoin',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $quoteIds = $this->options['quoteIds'];
        $result = array();
        for ($b = 0; $b < count($quoteIds); $b++) {
            $quoteId = $quoteIds[$b];
            $response = $this->webGetSymbolTickerRightCoinQuote (array(
                'quote' => $quoteId,
            ));
            $tickers = $this->safe_value($response, 'data', array());
            for ($i = 0; $i < count($tickers); $i++) {
                $ticker = $tickers[$i];
                $id = $ticker['symbol'];
                $baseId = explode('_', $id)[0];
                $base = strtoupper($baseId);
                $quote = strtoupper($quoteId);
                $base = $this->safe_currency_code($base);
                $quote = $this->safe_currency_code($quote);
                $symbol = $base . '/' . $quote;
                $precision = array(
                    'amount' => 8,
                    'price' => 8,
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
                    'info' => $ticker,
                );
            }
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $symbol = $market['symbol'];
        $timestamp = $this->milliseconds();
        $last = $this->safe_float($ticker, 'last');
        $percentage = $this->safe_float($ticker, 'change');
        $open = null;
        $change = null;
        $average = null;
        if ($percentage !== null) {
            $relativeChange = $percentage / 100;
            $open = $last / $this->sum(1, $relativeChange);
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVol'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
        );
        $response = $this->publicGetTickerRegionQuote (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
        );
        $response = $this->publicGetDepthRegionQuote (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $symbol = $market['symbol'];
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $this->cost_to_precision($symbol, $price * $amount);
            }
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
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
        );
        $response = $this->publicGetOrdersRegionQuote (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalance ($params);
        $result = array( 'info' => $response );
        $data = $this->safe_value($response, 'data', array());
        $balances = $this->omit($data, 'uid');
        $keys = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            list($currencyId, $accountType) = explode('_', $key);
            $code = $this->safe_currency_code($currencyId);
            if (!(is_array($result) && array_key_exists($code, $result))) {
                $result[$code] = $this->account();
            }
            $type = ($accountType === 'lock') ? 'used' : 'free';
            $result[$code][$type] = $this->safe_float($balances, $key);
        }
        return $this->parse_balance($result);
    }

    public function parse_order($order, $market = null) {
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->parse8601($this->safe_string($order, 'datetime'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount_original');
        $remaining = $this->safe_float($order, 'amount_outstanding');
        $filled = null;
        if ($amount !== null) {
            if ($remaining !== null) {
                $filled = $amount - $remaining;
            }
        }
        $status = $this->safe_string($order, 'status');
        if ($status === 'cancelled') {
            $status = 'canceled';
        } else {
            $status = $remaining ? 'open' : 'closed';
        }
        $info = $this->safe_value($order, 'info', $order);
        $type = 'limit';
        $side = $this->safe_string($order, 'type');
        $id = $this->safe_string($order, 'id');
        return array(
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
            'remaining' => $remaining,
            'trades' => null,
            'fee' => null,
            'info' => $info,
            'average' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
            'type' => $side,
            'amount' => $amount,
            'price' => $price,
        );
        $response = $this->privatePostTradeAddRegionQuote (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        $order = $this->parse_order(array(
            'id' => $id,
            'datetime' => $this->ymdhms($this->milliseconds()),
            'amount_original' => $amount,
            'amount_outstanding' => $amount,
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
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
        );
        return $this->privatePostTradeCancelRegionQuote (array_merge($request, $params));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $id,
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
        );
        $response = $this->privatePostTradeViewRegionQuote (array_merge($request, $params));
        $data = $this->safe_value($response, 'data');
        return $this->parse_order($data, $market);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'coin' => $market['baseId'],
            'quote' => $market['quoteId'],
        );
        if ($since !== null) {
            $request['since'] = $since / 1000;
        }
        $response = $this->privatePostTradeListRegionQuote (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'type' => 'open',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $apiType = 'rest';
        if ($api === 'web') {
            $apiType = $api;
        }
        $url = $this->urls['api'][$apiType] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public' || $api === 'web') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $query = $this->urlencode(array_merge(array(
                'key' => $this->apiKey,
                'nonce' => $this->nonce(),
            ), $query));
            $secret = $this->hash($this->encode($this->secret));
            $signature = $this->hmac($this->encode($query), $this->encode($secret));
            $query .= '&' . 'signature=' . $signature;
            if ($method === 'GET') {
                $url .= '?' . $query;
            } else {
                $headers = array(
                    'Content-type' => 'application/x-www-form-urlencoded',
                );
                $body = $query;
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        // private endpoints return the following structure:
        // array("$result":true,"data":array(...)) - success
        // array("$result":false,"$code":"103") - failure
        // array("$code":0,"msg":"Suceess","data":array("uid":"2716039","btc_balance":"0.00000000","btc_lock":"0.00000000","xrp_balance":"0.00000000","xrp_lock":"0.00000000"))
        $result = $this->safe_value($response, 'result');
        if ($result === null) {
            // public endpoint ← this comment left here by the contributor, in fact a missing $result does not necessarily mean a public endpoint...
            // we should just check the $code and don't rely on the $result at all here...
            return;
        }
        if ($result === true) {
            // success
            return;
        }
        $errorCode = $this->safe_string($response, 'code');
        $errorMessages = $this->errorMessages;
        $message = $this->safe_string($errorMessages, $errorCode, 'Unknown Error');
        $feedback = $this->id . ' ' . $message;
        $this->throw_exactly_matched_exception($this->exceptions, $errorCode, $feedback);
        throw new ExchangeError($this->id . ' ' . $message);
    }
}

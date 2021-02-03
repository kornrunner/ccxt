<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class btcturk extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'btcturk',
            'name' => 'BTCTurk',
            'countries' => array( 'TR' ), // Turkey
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'timeframes' => array(
                '1d' => '1d',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87153926-efbef500-c2c0-11ea-9842-05b63612c4b9.jpg',
                'api' => 'https://www.btcturk.com/api',
                'www' => 'https://www.btcturk.com',
                'doc' => 'https://github.com/BTCTrader/broker-api-docs',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ohlcdata', // ?last=COUNT
                        'orderbook',
                        'ticker',
                        'trades',   // ?last=COUNT (max 50)
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'balance',
                        'openOrders',
                        'userTransactions', // ?offset=0&limit=25&sort=asc
                    ),
                    'post' => array(
                        'exchange',
                        'cancelOrder',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.002 * 1.18,
                    'taker' => 0.003 * 1.18,
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetTicker ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'pair');
            $baseId = mb_substr($id, 0, 3 - 0);
            $quoteId = mb_substr($id, 3, 6 - 3);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $baseId = strtolower($baseId);
            $quoteId = strtolower($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => 8,
                'price' => 8,
            );
            $active = true;
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'info' => $market,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetBalance ($params);
        $result = array( 'info' => $response );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $currency = $this->currencies[$code];
            $free = $currency['id'] . '_available';
            $total = $currency['id'] . '_balance';
            $used = $currency['id'] . '_reserved';
            if (is_array($response) && array_key_exists($free, $response)) {
                $account = $this->account();
                $account['free'] = $this->safe_float($response, $free);
                $account['total'] = $this->safe_float($response, $total);
                $account['used'] = $this->safe_float($response, $used);
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pairSymbol' => $market['id'],
        );
        $response = $this->publicGetOrderbook (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($response, 'timestamp');
        return $this->parse_order_book($response, $timestamp);
    }

    public function parse_ticker($ticker, $market = null) {
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_timestamp($ticker, 'timestamp');
        $last = $this->safe_float($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_float($ticker, 'average'),
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $tickers = $this->publicGetTicker ($params);
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $tickers[$i];
            $marketId = $this->safe_string($ticker, 'pair');
            $symbol = $marketId;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$symbol];
                $symbol = $market['symbol'];
            }
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $tickers = $this->fetch_tickers($params);
        return $this->safe_value_2($tickers, $market['id'], $symbol);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $id = $this->safe_string($trade, 'tid');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $amount * $price;
            }
        }
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
            'type' => null,
            'side' => null,
            'order' => null,
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
        // $maxCount = 50;
        $request = array(
            'pairSymbol' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'Time')),
            $this->safe_float($ohlcv, 'Open'),
            $this->safe_float($ohlcv, 'High'),
            $this->safe_float($ohlcv, 'Low'),
            $this->safe_float($ohlcv, 'Close'),
            $this->safe_float($ohlcv, 'Volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1d', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array();
        if ($limit !== null) {
            $request['last'] = $limit;
        }
        $response = $this->publicGetOhlcdata (array_merge($request, $params));
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'PairSymbol' => $this->market_id($symbol),
            'OrderType' => ($side === 'buy') ? 0 : 1,
            'OrderMethod' => ($type === 'market') ? 1 : 0,
        );
        if ($type === 'market') {
            if (!(is_array($params) && array_key_exists('Total', $params))) {
                throw new ExchangeError($this->id . ' createOrder() requires the "Total" extra parameter for market orders ($amount and $price are both ignored)');
            }
        } else {
            $request['Price'] = $price;
            $request['Amount'] = $amount;
        }
        $response = $this->privatePostExchange (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => $id,
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        if ($this->id === 'btctrader') {
            throw new ExchangeError($this->id . ' is an abstract base API for BTCExchange, BTCTurk');
        }
        $url = $this->urls['api'] . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $body = $this->urlencode($params);
            $secret = base64_decode($this->secret);
            $auth = $this->apiKey . $nonce;
            $headers = array(
                'X-PCK' => $this->apiKey,
                'X-Stamp' => $nonce,
                'X-Signature' => $this->hmac($this->encode($auth), $secret, 'sha256', 'base64'),
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

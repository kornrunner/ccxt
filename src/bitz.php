<?php

namespace ccxt;

use Exception as Exception; // a common import

class bitz extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitz',
            'name' => 'Bit-Z',
            'countries' => 'HK',
            'rateLimit' => 2000,
            'version' => 'v1',
            'userAgent' => $this->userAgents['chrome'],
            'has' => array (
                'fetchTickers' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
            ),
            'timeframes' => array (
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '1d' => '1d',
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/35862606-4f554f14-0b5d-11e8-957d-35058c504b6f.jpg',
                'api' => 'https://api.bit-z.com/api_v1',
                'www' => 'https://www.bit-z.com',
                'doc' => 'https://www.bit-z.com/api.html',
                'fees' => 'https://www.bit-z.com/about/fee',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'ticker',
                        'tickerall',
                        'depth',
                        'orders',
                        'kline',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'balances',
                        'tradeAdd',
                        'tradeCancel',
                        'openOrders',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.001,
                    'taker' => 0.001,
                ),
                'funding' => array (
                    'withdraw' => array (
                        'BTC' => '0.5%',
                        'DKKT' => '0.5%',
                        'ETH' => 0.01,
                        'USDT' => '0.5%',
                        'LTC' => '0.5%',
                        'FCT' => '0.5%',
                        'LSK' => '0.5%',
                        'HXI' => '0.8%',
                        'ZEC' => '0.5%',
                        'DOGE' => '0.5%',
                        'MZC' => '0.5%',
                        'ETC' => '0.5%',
                        'GXS' => '0.5%',
                        'XPM' => '0.5%',
                        'PPC' => '0.5%',
                        'BLK' => '0.5%',
                        'XAS' => '0.5%',
                        'HSR' => '0.5%',
                        'NULS' => 5.0,
                        'VOISE' => 350.0,
                        'PAY' => 1.5,
                        'EOS' => 0.6,
                        'YBCT' => 35.0,
                        'OMG' => 0.3,
                        'OTN' => 0.4,
                        'BTX' => '0.5%',
                        'QTUM' => '0.5%',
                        'DASH' => '0.5%',
                        'GAME' => '0.5%',
                        'BCH' => '0.5%',
                        'GNT' => 9.0,
                        'SSS' => 1500.0,
                        'ARK' => '0.5%',
                        'PART' => '0.5%',
                        'LEO' => '0.5%',
                        'DGB' => '0.5%',
                        'ZSC' => 130.0,
                        'VIU' => 350.0,
                        'BTG' => '0.5%',
                        'ARN' => 10.0,
                        'VTC' => '0.5%',
                        'BCD' => '0.5%',
                        'TRX' => 200.0,
                        'HWC' => '0.5%',
                        'UNIT' => '0.5%',
                        'OXY' => '0.5%',
                        'MCO' => 0.3500,
                        'SBTC' => '0.5%',
                        'BCX' => '0.5%',
                        'ETF' => '0.5%',
                        'PYLNT' => 0.4000,
                        'XRB' => '0.5%',
                        'ETP' => '0.5%',
                    ),
                ),
            ),
            'precision' => array (
                'amount' => 8,
                'price' => 8,
            ),
            'options' => array (
                'lastNonceTimestamp' => 0,
            ),
            'commonCurrencies' => array (
                'PXC' => 'Pixiecoin',
            ),
        ));
    }

    public function fetch_markets () {
        $response = $this->publicGetTickerall ();
        $markets = $response['data'];
        $ids = is_array ($markets) ? array_keys ($markets) : array ();
        $result = array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $market = $markets[$id];
            list ($baseId, $quoteId) = explode ('_', $id);
            $base = strtoupper ($baseId);
            $quote = strtoupper ($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalances ($params);
        $data = $response['data'];
        $balances = $this->omit ($data, 'uid');
        $result = array ( 'info' => $response );
        $keys = is_array ($balances) ? array_keys ($balances) : array ();
        for ($i = 0; $i < count ($keys); $i++) {
            $id = $keys[$i];
            $idHasUnderscore = (mb_strpos ($id, '_') !== false);
            if (!$idHasUnderscore) {
                $code = strtoupper ($id);
                if (is_array ($this->currencies_by_id) && array_key_exists ($id, $this->currencies_by_id)) {
                    $code = $this->currencies_by_id[$id]['code'];
                }
                $account = $this->account ();
                $usedField = $id . '_lock';
                $account['used'] = $this->safe_float($balances, $usedField);
                $account['total'] = $this->safe_float($balances, $id);
                $account['free'] = $account['total'] - $account['used'];
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $ticker['date'] * 1000;
        $symbol = $market['symbol'];
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTicker (array_merge (array (
            'coin' => $market['id'],
        ), $params));
        return $this->parse_ticker($response['data'], $market);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickerall ($params);
        $tickers = $response['data'];
        $result = array ();
        $ids = is_array ($tickers) ? array_keys ($tickers) : array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $market = $this->markets_by_id[$id];
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($tickers[$id], $market);
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetDepth (array_merge (array (
            'coin' => $this->market_id($symbol),
        ), $params));
        $orderbook = $response['data'];
        $timestamp = $orderbook['date'] * 1000;
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function parse_trade ($trade, $market = null) {
        $hkt = $this->sum ($this->milliseconds (), 28800000);
        $utcDate = $this->iso8601 ($hkt);
        $utcDate = explode ('T', $utcDate);
        $utcDate = $utcDate[0] . ' ' . $trade['t'] . '+08';
        $timestamp = $this->parse8601 ($utcDate);
        $price = $this->safe_float($trade, 'p');
        $amount = $this->safe_float($trade, 'n');
        $symbol = $market['symbol'];
        $cost = $this->price_to_precision($symbol, $amount * $price);
        return array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => null,
            'order' => null,
            'type' => 'limit',
            'side' => $trade['s'],
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetOrders (array_merge (array (
            'coin' => $market['id'],
        ), $params));
        $trades = $response['data']['d'];
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetKline (array_merge (array (
            'coin' => $market['id'],
            'type' => $this->timeframes[$timeframe],
        ), $params));
        $ohlcv = json_decode ($response['data']['datas']['data'], $as_associative_array = true);
        return $this->parse_ohlcvs($ohlcv, $market, $timeframe, $since, $limit);
    }

    public function parse_order ($order, $market = null) {
        $symbol = null;
        if ($market !== null)
            $symbol = $market['symbol'];
        $side = $this->safe_string($order, 'side');
        if ($side === null) {
            $side = $this->safe_string($order, 'type');
            if ($side !== null)
                $side = ($side === 'in') ? 'buy' : 'sell';
            if ($side === null)
                $side = $this->safe_string($order, 'flag');
        }
        $amount = $this->safe_float($order, 'number');
        $remaining = $this->safe_float($order, 'numberover');
        $filled = null;
        if ($amount !== null)
            if ($remaining !== null)
                $filled = $amount - $remaining;
        $timestamp = null;
        $iso8601 = null;
        if (is_array ($order) && array_key_exists ('datetime', $order)) {
            $timestamp = $this->parse8601 ($order['datetime']);
            $iso8601 = $this->iso8601 ($timestamp);
        }
        return array (
            'id' => $order['id'],
            'datetime' => $iso8601,
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => 'open',
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $order['price'],
            'cost' => null,
            'amount' => $order['number'],
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => null,
            'info' => $order,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $orderType = ($side === 'buy') ? 'in' : 'out';
        if (!$this->password)
            throw new ExchangeError ($this->id . ' createOrder() requires you to set exchange.password = "YOUR_TRADING_PASSWORD" (a trade password is NOT THE SAME as your login password)');
        $request = array (
            'coin' => $market['id'],
            'type' => $orderType,
            'price' => $this->price_to_precision($symbol, $price),
            'number' => $this->amount_to_string($symbol, $amount),
            'tradepwd' => $this->password,
        );
        $response = $this->privatePostTradeAdd (array_merge ($request, $params));
        $id = $response['data']['id'];
        $order = $this->parse_order(array (
            'id' => $id,
            'price' => $price,
            'number' => $amount,
            'side' => $side,
        ), $market);
        $this->orders[$id] = $order;
        return $order;
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostTradeCancel (array_merge (array (
            'id' => $id,
        ), $params));
        return $response;
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->privatePostOpenOrders (array_merge (array (
            'coin' => $market['id'],
        ), $params));
        return $this->parse_orders($response['data'], $market, $since, $limit);
    }

    public function nonce () {
        $currentTimestamp = $this->seconds ();
        if ($currentTimestamp > $this->options['lastNonceTimestamp']) {
            $this->options['lastNonceTimestamp'] = $currentTimestamp;
            $this->options['lastNonce'] = 100000;
        }
        $this->options['lastNonce'] = $this->sum ($this->options['lastNonce'], 1);
        return $this->options['lastNonce'];
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $path;
        $query = null;
        if ($api === 'public') {
            $query = $this->urlencode ($params);
            if (strlen ($query))
                $url .= '?' . $query;
        } else {
            $this->check_required_credentials();
            $body = $this->urlencode ($this->keysort (array_merge (array (
                'api_key' => $this->apiKey,
                'timestamp' => $this->seconds (),
                'nonce' => $this->nonce (),
            ), $params)));
            $body .= '&sign=' . $this->hash ($this->encode ($body . $this->secret));
            $headers = array ( 'Content-type' => 'application/x-www-form-urlencoded' );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        $code = $this->safe_string($response, 'code');
        if ($code !== '0') {
            $ErrorClass = $this->safe_value(array (
                '103' => '\\ccxt\\AuthenticationError',
                '104' => '\\ccxt\\AuthenticationError',
                '200' => '\\ccxt\\AuthenticationError',
                '202' => '\\ccxt\\AuthenticationError',
                '401' => '\\ccxt\\AuthenticationError',
                '406' => '\\ccxt\\AuthenticationError',
                '203' => '\\ccxt\\InvalidNonce',
                '201' => '\\ccxt\\OrderNotFound',
                '408' => '\\ccxt\\InsufficientFunds',
                '106' => '\\ccxt\\DDoSProtection',
            ), $code, '\\ccxt\\ExchangeError');
            $message = $this->safe_string($response, 'msg', 'Error');
            throw new $ErrorClass ($message);
        }
        return $response;
    }
}

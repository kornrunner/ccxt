<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\NotSupported;

class xbtce extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'xbtce',
            'name' => 'xBTCe',
            'countries' => array( 'RU' ),
            'rateLimit' => 2000, // responses are cached every 2 seconds
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => false,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'referral' => 'https://xbtce.com/?agent=XX97BTCXXXG687021000B',
                'logo' => 'https://user-images.githubusercontent.com/1294454/28059414-e235970c-662c-11e7-8c3a-08e31f78684b.jpg',
                'api' => 'https://cryptottlivewebapi.xbtce.net:8443/api',
                'www' => 'https://www.xbtce.com',
                'doc' => array(
                    'https://www.xbtce.com/tradeapi',
                    'https://support.xbtce.info/Knowledgebase/Article/View/52/25/xbtce-exchange-api',
                ),
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currency',
                        'currency/{filter}',
                        'level2',
                        'level2/{filter}',
                        'quotehistory/{symbol}/{periodicity}/bars/ask',
                        'quotehistory/{symbol}/{periodicity}/bars/bid',
                        'quotehistory/{symbol}/level2',
                        'quotehistory/{symbol}/ticks',
                        'symbol',
                        'symbol/{filter}',
                        'tick',
                        'tick/{filter}',
                        'ticker',
                        'ticker/{filter}',
                        'tradesession',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'tradeserverinfo',
                        'tradesession',
                        'currency',
                        'currency/{filter}',
                        'level2',
                        'level2/{filter}',
                        'symbol',
                        'symbol/{filter}',
                        'tick',
                        'tick/{filter}',
                        'account',
                        'asset',
                        'asset/{id}',
                        'position',
                        'position/{id}',
                        'trade',
                        'trade/{id}',
                        'quotehistory/{symbol}/{periodicity}/bars/ask',
                        'quotehistory/{symbol}/{periodicity}/bars/ask/info',
                        'quotehistory/{symbol}/{periodicity}/bars/bid',
                        'quotehistory/{symbol}/{periodicity}/bars/bid/info',
                        'quotehistory/{symbol}/level2',
                        'quotehistory/{symbol}/level2/info',
                        'quotehistory/{symbol}/periodicities',
                        'quotehistory/{symbol}/ticks',
                        'quotehistory/{symbol}/ticks/info',
                        'quotehistory/cache/{symbol}/{periodicity}/bars/ask',
                        'quotehistory/cache/{symbol}/{periodicity}/bars/bid',
                        'quotehistory/cache/{symbol}/level2',
                        'quotehistory/cache/{symbol}/ticks',
                        'quotehistory/symbols',
                        'quotehistory/version',
                    ),
                    'post' => array(
                        'trade',
                        'tradehistory',
                    ),
                    'put' => array(
                        'trade',
                    ),
                    'delete' => array(
                        'trade',
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'DSH' => 'DASH',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->privateGetSymbol ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'Symbol');
            $baseId = $this->safe_string($market, 'MarginCurrency');
            $quoteId = $this->safe_string($market, 'ProfitCurrency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $symbol = $market['IsTradeAllowed'] ? $symbol : $id;
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => null,
                'precision' => $this->precision,
                'limits' => $this->limits,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balances = $this->privateGetAsset ($params);
        $result = array( 'info' => $balances );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'Currency');
            $code = $this->safe_currency_code($currencyId);
            $account = array(
                'free' => $this->safe_number($balance, 'FreeAmount'),
                'used' => $this->safe_number($balance, 'LockedAmount'),
                'total' => $this->safe_number($balance, 'Amount'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'filter' => $market['id'],
        );
        $response = $this->privateGetLevel2Filter (array_merge($request, $params));
        $orderbook = $response[0];
        $timestamp = $this->safe_integer($orderbook, 'Timestamp');
        return $this->parse_order_book($orderbook, $timestamp, 'Bids', 'Asks', 'Price', 'Volume');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = 0;
        $last = null;
        if (is_array($ticker) && array_key_exists('LastBuyTimestamp', $ticker)) {
            if ($timestamp < $ticker['LastBuyTimestamp']) {
                $timestamp = $ticker['LastBuyTimestamp'];
                $last = $ticker['LastBuyPrice'];
            }
        }
        if (is_array($ticker) && array_key_exists('LastSellTimestamp', $ticker)) {
            if ($timestamp < $ticker['LastSellTimestamp']) {
                $timestamp = $ticker['LastSellTimestamp'];
                $last = $ticker['LastSellPrice'];
            }
        }
        if (!$timestamp) {
            $timestamp = $this->milliseconds();
        }
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $ticker['DailyBestBuyPrice'],
            'low' => $ticker['DailyBestSellPrice'],
            'bid' => $ticker['BestBid'],
            'bidVolume' => null,
            'ask' => $ticker['BestAsk'],
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $ticker['DailyTradedTotalVolume'],
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        $tickers = $this->index_by($response, 'Symbol');
        $ids = is_array($tickers) ? array_keys($tickers) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = null;
            $symbol = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                $baseId = mb_substr($id, 0, 3 - 0);
                $quoteId = mb_substr($id, 3, 6 - 3);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
            $ticker = $tickers[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'filter' => $market['id'],
        );
        $response = $this->publicGetTickerFilter (array_merge($request, $params));
        $length = is_array($response) ? count($response) : 0;
        if ($length < 1) {
            throw new ExchangeError($this->id . ' fetchTicker returned empty $response, xBTCe public API error');
        }
        $tickers = $this->index_by($response, 'Symbol');
        $ticker = $tickers[$market['id']];
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // no method for trades?
        return $this->privateGetTrade ($params);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        return array(
            $this->safe_integer($ohlcv, 'Timestamp'),
            $this->safe_number($ohlcv, 'Open'),
            $this->safe_number($ohlcv, 'High'),
            $this->safe_number($ohlcv, 'Low'),
            $this->safe_number($ohlcv, 'Close'),
            $this->safe_number($ohlcv, 'Volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        //     $minutes = intval($timeframe / 60); // 1 minute by default
        //     $periodicity = (string) $minutes;
        //     $this->load_markets();
        //     $market = $this->market($symbol);
        //     if ($since === null)
        //         $since = $this->seconds() - 86400 * 7; // last day by defulat
        //     if ($limit === null)
        //         $limit = 1000; // default
        //     $response = $this->privateGetQuotehistorySymbolPeriodicityBarsBid (array_merge(array(
        //         'symbol' => $market['id'],
        //         'periodicity' => $periodicity,
        //         'timestamp' => $since,
        //         'count' => $limit,
        //     ), $params));
        //     return $this->parse_ohlcvs($response['Bars'], $market, $timeframe, $since, $limit);
        throw new NotSupported($this->id . ' fetchOHLCV is disabled by the exchange');
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $request = array(
            'pair' => $this->market_id($symbol),
            'type' => $side,
            'amount' => $amount,
            'rate' => $price,
        );
        $response = $this->privatePostTrade (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => (string) $response['Id'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'Type' => 'Cancel',
            'Id' => $id,
        );
        return $this->privateDeleteTrade (array_merge($request, $params));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        if (!$this->apiKey) {
            throw new AuthenticationError($this->id . ' requires apiKey for all requests, their public API is always busy');
        }
        if (!$this->uid) {
            throw new AuthenticationError($this->id . ' requires uid property for authentication and trading, their public API is always busy');
        }
        $url = $this->urls['api'] . '/' . $this->version;
        if ($api === 'public') {
            $url .= '/' . $api;
        }
        $url .= '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $headers = array( 'Accept-Encoding' => 'gzip, deflate' );
            $nonce = (string) $this->nonce();
            if ($method === 'POST') {
                if ($query) {
                    $headers['Content-Type'] = 'application/json';
                    $body = $this->json($query);
                } else {
                    $url .= '?' . $this->urlencode($query);
                }
            }
            $auth = $nonce . $this->uid . $this->apiKey . $method . $url;
            if ($body) {
                $auth .= $body;
            }
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha256', 'base64');
            $credentials = $this->uid . ':' . $this->apiKey . ':' . $nonce . ':' . $signature;
            $headers['Authorization'] = 'HMAC ' . $credentials;
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

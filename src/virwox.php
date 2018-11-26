<?php

namespace ccxt;

use Exception as Exception; // a common import

class virwox extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'virwox',
            'name' => 'VirWoX',
            'countries' => array ( 'AT', 'EU' ),
            'rateLimit' => 1000,
            'has' => array (
                'CORS' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766894-6da9d360-5eea-11e7-90aa-41f2711b7405.jpg',
                'api' => array (
                    'public' => 'http://api.virwox.com/api/json.php',
                    'private' => 'https://www.virwox.com/api/trading.php',
                ),
                'www' => 'https://www.virwox.com',
                'doc' => 'https://www.virwox.com/developers.php',
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => false,
                'login' => true,
                'password' => true,
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'getInstruments',
                        'getBestPrices',
                        'getMarketDepth',
                        'estimateMarketOrder',
                        'getTradedPriceVolume',
                        'getRawTradeData',
                        'getStatistics',
                        'getTerminalList',
                        'getGridList',
                        'getGridStatistics',
                    ),
                    'post' => array (
                        'getInstruments',
                        'getBestPrices',
                        'getMarketDepth',
                        'estimateMarketOrder',
                        'getTradedPriceVolume',
                        'getRawTradeData',
                        'getStatistics',
                        'getTerminalList',
                        'getGridList',
                        'getGridStatistics',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'cancelOrder',
                        'getBalances',
                        'getCommissionDiscount',
                        'getOrders',
                        'getTransactions',
                        'placeOrder',
                    ),
                    'post' => array (
                        'cancelOrder',
                        'getBalances',
                        'getCommissionDiscount',
                        'getOrders',
                        'getTransactions',
                        'placeOrder',
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $markets = $this->publicGetGetInstruments ();
        $keys = is_array ($markets['result']) ? array_keys ($markets['result']) : array ();
        $result = array ();
        for ($p = 0; $p < count ($keys); $p++) {
            $market = $markets['result'][$keys[$p]];
            $id = $market['instrumentID'];
            $symbol = $market['symbol'];
            $base = $market['longCurrency'];
            $quote = $market['shortCurrency'];
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetBalances ();
        $balances = $response['result']['accountList'];
        $result = array ( 'info' => $balances );
        for ($b = 0; $b < count ($balances); $b++) {
            $balance = $balances[$b];
            $currency = $balance['currency'];
            $total = $balance['balance'];
            $account = array (
                'free' => $total,
                'used' => 0.0,
                'total' => $total,
            );
            $result[$currency] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_market_price ($symbol, $params = array ()) {
        $this->load_markets();
        $response = $this->publicPostGetBestPrices (array_merge (array (
            'symbols' => array ( $symbol ),
        ), $params));
        $result = $response['result'];
        return array (
            'bid' => $this->safe_float($result[0], 'bestBuyPrice'),
            'ask' => $this->safe_float($result[0], 'bestSellPrice'),
        );
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbols' => array ( $symbol ),
        );
        if ($limit !== null) {
            $request['buyDepth'] = $limit; // 100
            $request['sellDepth'] = $limit; // 100
        }
        $response = $this->publicPostGetMarketDepth (array_merge ($request, $params));
        $orderbook = $response['result'][0];
        return $this->parse_order_book($orderbook, null, 'buy', 'sell', 'price', 'volume');
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $end = $this->milliseconds ();
        $start = $end - 86400000;
        $response = $this->publicGetGetTradedPriceVolume (array_merge (array (
            'instrument' => $symbol,
            'endDate' => $this->ymdhms ($end),
            'startDate' => $this->ymdhms ($start),
            'HLOC' => 1,
        ), $params));
        $tickers = $response['result']['priceVolumeList'];
        $keys = is_array ($tickers) ? array_keys ($tickers) : array ();
        $length = is_array ($keys) ? count ($keys) : 0;
        $lastKey = $keys[$length - 1];
        $ticker = $tickers[$lastKey];
        $timestamp = $this->milliseconds ();
        $close = $this->safe_float($ticker, 'close');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'open'),
            'close' => $close,
            'last' => $close,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'longVolume'),
            'quoteVolume' => $this->safe_float($ticker, 'shortVolume'),
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $symbol = null) {
        $sec = $this->safe_integer($trade, 'time');
        $timestamp = $sec * 1000;
        return array (
            'id' => $trade['tid'],
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'order' => null,
            'symbol' => $symbol,
            'type' => null,
            'side' => null,
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'vol'),
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetGetRawTradeData (array_merge (array (
            'instrument' => $symbol,
            'timespan' => 3600,
        ), $params));
        $result = $response['result'];
        $trades = $result['data'];
        return $this->parse_trades($trades, $market);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $order = array (
            'instrument' => $market['symbol'],
            'orderType' => strtoupper ($side),
            'amount' => $amount,
        );
        if ($type === 'limit')
            $order['price'] = $price;
        $response = $this->privatePostPlaceOrder (array_merge ($order, $params));
        return array (
            'info' => $response,
            'id' => (string) $response['result']['orderID'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        return $this->privatePostCancelOrder (array_merge (array (
            'orderID' => $id,
        ), $params));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        $auth = array ();
        if ($api === 'private') {
            $this->check_required_credentials();
            $auth['key'] = $this->apiKey;
            $auth['user'] = $this->login;
            $auth['pass'] = $this->password;
        }
        $nonce = $this->nonce ();
        if ($method === 'GET') {
            $url .= '?' . $this->urlencode (array_merge (array (
                'method' => $path,
                'id' => $nonce,
            ), $auth, $params));
        } else {
            $headers = array ( 'Content-Type' => 'application/json' );
            $body = $this->json (array (
                'method' => $path,
                'params' => array_merge ($auth, $params),
                'id' => $nonce,
            ));
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response = null) {
        if ($code === 200) {
            if (($body[0] === '{') || ($body[0] === '[')) {
                $response = json_decode ($body, $as_associative_array = true);
                if (is_array ($response) && array_key_exists ('result', $response)) {
                    $result = $response['result'];
                    if (is_array ($result) && array_key_exists ('errorCode', $result)) {
                        $errorCode = $result['errorCode'];
                        if ($errorCode !== 'OK') {
                            throw new ExchangeError ($this->id . ' error returned => ' . $body);
                        }
                    }
                } else {
                    throw new ExchangeError ($this->id . ' malformed $response => no $result in $response => ' . $body);
                }
            } else {
                // if not a JSON $response
                throw new ExchangeError ($this->id . ' returned a non-JSON reply => ' . $body);
            }
        }
    }
}

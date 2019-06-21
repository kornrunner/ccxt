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
                    'public' => 'https://api.virwox.com/api/json.php',
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
        $response = $this->publicGetGetInstruments ($params);
        $markets = $this->safe_value($response, 'result');
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count ($keys); $i++) {
            $key = $keys[$i];
            $market = $this->safe_value($markets, $key, array());
            $id = $this->safe_string($market, 'instrumentID');
            $baseId = $this->safe_string($market, 'longCurrency');
            $quoteId = $this->safe_string($market, 'shortCurrency');
            $base = $this->common_currency_code($baseId);
            $quote = $this->common_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetBalances ($params);
        $balances = $this->safe_value($response['result'], 'accountList');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->common_currency_code($currencyId);
            $total = $this->safe_float($balance, 'balance');
            $account = array (
                'free' => $total,
                'used' => 0.0,
                'total' => $total,
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_market_price ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbols' => array ( $symbol ),
        );
        $response = $this->publicPostGetBestPrices (array_merge ($request, $params));
        $result = $this->safe_value($response, 'result');
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
        $request = array (
            'instrument' => $symbol,
            'endDate' => $this->ymdhms ($end),
            'startDate' => $this->ymdhms ($start),
            'HLOC' => 1,
        );
        $response = $this->publicGetGetTradedPriceVolume (array_merge ($request, $params));
        $tickers = $this->safe_value($response['result'], 'priceVolumeList');
        $keys = is_array($tickers) ? array_keys($tickers) : array();
        $length = is_array ($keys) ? count ($keys) : 0;
        $lastKey = $keys[$length - 1];
        $ticker = $this->safe_value($tickers, $lastKey);
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

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->safe_integer($trade, 'time');
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        $id = $this->safe_string($trade, 'tid');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'vol');
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
        return array (
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'order' => null,
            'symbol' => $symbol,
            'type' => null,
            'side' => null,
            'takerOrMaker' => null,
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
        $request = array (
            'instrument' => $symbol,
            'timespan' => 3600,
        );
        $response = $this->publicGetGetRawTradeData (array_merge ($request, $params));
        $result = $this->safe_value($response, 'result', array());
        $trades = $this->safe_value($result, 'data', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'instrument' => $market['symbol'],
            'orderType' => strtoupper($side),
            'amount' => $amount,
        );
        if ($type === 'limit') {
            $request['price'] = $price;
        }
        $response = $this->privatePostPlaceOrder (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $this->safe_string($response['result'], 'orderID'),
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $request = array (
            'orderID' => $id,
        );
        return $this->privatePostCancelOrder (array_merge ($request, $params));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        $auth = array();
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
            $headers = array( 'Content-Type' => 'application/json' );
            $body = $this->json (array (
                'method' => $path,
                'params' => array_merge ($auth, $params),
                'id' => $nonce,
            ));
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response) {
        if ($code === 200) {
            if (($body[0] === '{') || ($body[0] === '[')) {
                if (is_array($response) && array_key_exists('result', $response)) {
                    $result = $response['result'];
                    if (is_array($result) && array_key_exists('errorCode', $result)) {
                        $errorCode = $result['errorCode'];
                        if ($errorCode !== 'OK') {
                            throw new ExchangeError($this->id . ' error returned => ' . $body);
                        }
                    }
                } else {
                    throw new ExchangeError($this->id . ' malformed $response => no $result in $response => ' . $body);
                }
            } else {
                // if not a JSON $response
                throw new ExchangeError($this->id . ' returned a non-JSON reply => ' . $body);
            }
        }
    }
}

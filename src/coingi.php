<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class coingi extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coingi',
            'name' => 'Coingi',
            'rateLimit' => 1000,
            'countries' => array( 'PA', 'BG', 'CN', 'US' ), // Panama, Bulgaria, China, US
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'referral' => 'https://www.coingi.com/?r=XTPPMC',
                'logo' => 'https://user-images.githubusercontent.com/1294454/28619707-5c9232a8-7212-11e7-86d6-98fe5d15cc6e.jpg',
                'api' => array(
                    'www' => 'https://coingi.com',
                    'current' => 'https://api.coingi.com',
                    'user' => 'https://api.coingi.com',
                ),
                'www' => 'https://coingi.com',
                'doc' => 'https://coingi.docs.apiary.io',
            ),
            'api' => array(
                'www' => array(
                    'get' => array(
                        '',
                    ),
                ),
                'current' => array(
                    'get' => array(
                        'order-book/{pair}/{askCount}/{bidCount}/{depth}',
                        'transactions/{pair}/{maxCount}',
                        '24hour-rolling-aggregation',
                    ),
                ),
                'user' => array(
                    'post' => array(
                        'balance',
                        'add-order',
                        'cancel-order',
                        'orders',
                        'transactions',
                        'create-crypto-withdrawal',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.2 / 100,
                    'maker' => 0.2 / 100,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(
                        'BTC' => 0.001,
                        'LTC' => 0.01,
                        'DOGE' => 2,
                        'PPC' => 0.02,
                        'VTC' => 0.2,
                        'NMC' => 2,
                        'DASH' => 0.002,
                        'USD' => 10,
                        'EUR' => 10,
                    ),
                    'deposit' => array(
                        'BTC' => 0,
                        'LTC' => 0,
                        'DOGE' => 0,
                        'PPC' => 0,
                        'VTC' => 0,
                        'NMC' => 0,
                        'DASH' => 0,
                        'USD' => 5,
                        'EUR' => 1,
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->wwwGet ($params);
        $parts = explode('do=currencyPairSelector-selectCurrencyPair" class="active">', $response);
        $currencyParts = explode('<div class="currency-pair-label">', $parts[1]);
        $result = array();
        for ($i = 1; $i < count($currencyParts); $i++) {
            $currencyPart = $currencyParts[$i];
            $idParts = explode('</div>', $currencyPart);
            $id = $idParts[0];
            $id = str_replace('/', '-', $id);
            $id = strtolower($id);
            list($baseId, $quoteId) = explode('-', $id);
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
                'info' => $id,
                'active' => true,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => pow(10, $precision['amount']),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => 0,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $lowercaseCurrencies = array();
        $currencies = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            $lowercaseCurrencies[] = strtolower($currency);
        }
        $request = array(
            'currencies' => implode(',', $lowercaseCurrencies),
        );
        $response = $this->userPostBalance (array_merge($request, $params));
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance['currency'], 'name');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'available');
            $blocked = $this->safe_number($balance, 'blocked');
            $inOrders = $this->safe_number($balance, 'inOrders');
            $withdrawing = $this->safe_number($balance, 'withdrawing');
            $account['used'] = $this->sum($blocked, $inOrders, $withdrawing);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = 512, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
            'depth' => 32, // maximum number of depth range steps 1-32
            'askCount' => $limit, // maximum returned number of asks 1-512
            'bidCount' => $limit, // maximum returned number of bids 1-512
        );
        $orderbook = $this->currentGetOrderBookPairAskCountBidCountDepth (array_merge($request, $params));
        return $this->parse_order_book($orderbook, null, 'bids', 'asks', 'price', 'baseAmount');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'highestBid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'lowestAsk'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => null,
            'last' => null,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'baseVolume'),
            'quoteVolume' => $this->safe_number($ticker, 'counterVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->currentGet24hourRollingAggregation ($params);
        $result = array();
        for ($t = 0; $t < count($response); $t++) {
            $ticker = $response[$t];
            $base = strtoupper($ticker['currencyPair']['base']);
            $quote = strtoupper($ticker['currencyPair']['counter']);
            $symbol = $base . '/' . $quote;
            $market = null;
            if (is_array($this->markets) && array_key_exists($symbol, $this->markets)) {
                $market = $this->markets[$symbol];
            }
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $tickers = $this->fetch_tickers(null, $params);
        if (is_array($tickers) && array_key_exists($symbol, $tickers)) {
            return $tickers[$symbol];
        }
        throw new ExchangeError($this->id . ' return did not contain ' . $symbol);
    }

    public function parse_trade($trade, $market = null) {
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $timestamp = $this->safe_integer($trade, 'timestamp');
        $id = $this->safe_string($trade, 'id');
        $marketId = $this->safe_string($trade, 'currencyPair');
        $symbol = $this->safe_symbol($marketId, $market);
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => null, // type
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
        $request = array(
            'pair' => $market['id'],
            'maxCount' => 128,
        );
        $response = $this->currentGetTransactionsPairMaxCount (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'currencyPair' => $this->market_id($symbol),
            'volume' => $amount,
            'price' => $price,
            'orderType' => ($side === 'buy') ? 0 : 1,
        );
        $response = $this->userPostAddOrder (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['result'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => $id,
        );
        return $this->userPostCancelOrder (array_merge($request, $params));
    }

    public function sign($path, $api = 'current', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        if ($api !== 'www') {
            $url .= '/' . $api . '/' . $this->implode_params($path, $params);
        }
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'current') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($api === 'user') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $request = array_merge(array(
                'token' => $this->apiKey,
                'nonce' => $nonce,
            ), $query);
            $auth = (string) $nonce . '$' . $this->apiKey;
            $request['signature'] = $this->hmac($this->encode($auth), $this->encode($this->secret));
            $body = $this->json($request);
            $headers = array(
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'current', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (gettype($response) !== 'string') {
            if (is_array($response) && array_key_exists('errors', $response)) {
                throw new ExchangeError($this->id . ' ' . $this->json($response));
            }
        }
        return $response;
    }
}

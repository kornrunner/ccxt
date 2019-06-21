<?php

namespace ccxt;

use Exception as Exception; // a common import

class bitmarket extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitmarket',
            'name' => 'BitMarket',
            'countries' => array ( 'PL', 'EU' ),
            'rateLimit' => 1500,
            'has' => array (
                'CORS' => false,
                'fetchOHLCV' => true,
                'withdraw' => true,
            ),
            'timeframes' => array (
                '90m' => '90m',
                '6h' => '6h',
                '1d' => '1d',
                '1w' => '7d',
                '1M' => '1m',
                '3M' => '3m',
                '6M' => '6m',
                '1y' => '1y',
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27767256-a8555200-5ef9-11e7-96fd-469a65e2b0bd.jpg',
                'api' => array (
                    'public' => 'https://www.bitmarket.net',
                    'private' => 'https://www.bitmarket.pl/api2/', // last slash is critical
                ),
                'www' => array (
                    'https://www.bitmarket.pl',
                    'https://www.bitmarket.net',
                ),
                'doc' => array (
                    'https://www.bitmarket.net/docs.php?file=api_public.html',
                    'https://www.bitmarket.net/docs.php?file=api_private.html',
                    'https://github.com/bitmarket-net/api',
                ),
                'referral' => 'https://www.bitmarket.net/?ref=23323',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'json/{market}/ticker',
                        'json/{market}/orderbook',
                        'json/{market}/trades',
                        'json/ctransfer',
                        'graphs/{market}/90m',
                        'graphs/{market}/6h',
                        'graphs/{market}/1d',
                        'graphs/{market}/7d',
                        'graphs/{market}/1m',
                        'graphs/{market}/3m',
                        'graphs/{market}/6m',
                        'graphs/{market}/1y',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'info',
                        'trade',
                        'cancel',
                        'orders',
                        'trades',
                        'history',
                        'withdrawals',
                        'tradingdesk',
                        'tradingdeskStatus',
                        'tradingdeskConfirm',
                        'cryptotradingdesk',
                        'cryptotradingdeskStatus',
                        'cryptotradingdeskConfirm',
                        'withdraw',
                        'withdrawFiat',
                        'withdrawPLNPP',
                        'withdrawFiatFast',
                        'deposit',
                        'transfer',
                        'transfers',
                        'marginList',
                        'marginOpen',
                        'marginClose',
                        'marginCancel',
                        'marginModify',
                        'marginBalanceAdd',
                        'marginBalanceRemove',
                        'swapList',
                        'swapOpen',
                        'swapClose',
                    ),
                ),
            ),
            'markets' => array (
                'BCH/PLN' => array( 'id' => 'BCCPLN', 'symbol' => 'BCH/PLN', 'base' => 'BCH', 'quote' => 'PLN', 'baseId' => 'BCC', 'quoteId' => 'PLN' ),
                'BTG/PLN' => array( 'id' => 'BTGPLN', 'symbol' => 'BTG/PLN', 'base' => 'BTG', 'quote' => 'PLN', 'baseId' => 'BTG', 'quoteId' => 'PLN' ),
                'BTC/PLN' => array( 'id' => 'BTCPLN', 'symbol' => 'BTC/PLN', 'base' => 'BTC', 'quote' => 'PLN', 'baseId' => 'BTC', 'quoteId' => 'PLN' ),
                'BTC/EUR' => array( 'id' => 'BTCEUR', 'symbol' => 'BTC/EUR', 'base' => 'BTC', 'quote' => 'EUR', 'baseId' => 'BTC', 'quoteId' => 'EUR' ),
                'LTC/PLN' => array( 'id' => 'LTCPLN', 'symbol' => 'LTC/PLN', 'base' => 'LTC', 'quote' => 'PLN', 'baseId' => 'LTC', 'quoteId' => 'PLN' ),
                'LTC/BTC' => array( 'id' => 'LTCBTC', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'baseId' => 'LTC', 'quoteId' => 'BTC' ),
                'LiteMineX/BTC' => array( 'id' => 'LiteMineXBTC', 'symbol' => 'LiteMineX/BTC', 'base' => 'LiteMineX', 'quote' => 'BTC', 'baseId' => 'LiteMineX', 'quoteId' => 'BTC' ),
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.45 / 100,
                    'maker' => 0.15 / 100,
                    'tiers' => array (
                        'taker' => [
                            [0, 0.45 / 100],
                            [99.99, 0.44 / 100],
                            [299.99, 0.43 / 100],
                            [499.99, 0.42 / 100],
                            [999.99, 0.41 / 100],
                            [1999.99, 0.40 / 100],
                            [2999.99, 0.39 / 100],
                            [4999.99, 0.38 / 100],
                            [9999.99, 0.37 / 100],
                            [19999.99, 0.36 / 100],
                            [29999.99, 0.35 / 100],
                            [49999.99, 0.34 / 100],
                            [99999.99, 0.33 / 100],
                            [199999.99, 0.32 / 100],
                            [299999.99, 0.31 / 100],
                            [499999.99, 0.0 / 100],
                        ],
                        'maker' => [
                            [0, 0.15 / 100],
                            [99.99, 0.14 / 100],
                            [299.99, 0.13 / 100],
                            [499.99, 0.12 / 100],
                            [999.99, 0.11 / 100],
                            [1999.99, 0.10 / 100],
                            [2999.99, 0.9 / 100],
                            [4999.99, 0.8 / 100],
                            [9999.99, 0.7 / 100],
                            [19999.99, 0.6 / 100],
                            [29999.99, 0.5 / 100],
                            [49999.99, 0.4 / 100],
                            [99999.99, 0.3 / 100],
                            [199999.99, 0.2 / 100],
                            [299999.99, 0.1 / 100],
                            [499999.99, 0.0 / 100],
                        ],
                    ),
                ),
                'funding' => array (
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array (
                        'BTC' => 0.0008,
                        'LTC' => 0.005,
                        'BCH' => 0.0008,
                        'BTG' => 0.0008,
                        'DOGE' => 1,
                        'EUR' => 2,
                        'PLN' => 2,
                    ),
                    'deposit' => array (
                        'BTC' => 0,
                        'LTC' => 0,
                        'BCH' => 0,
                        'BTG' => 0,
                        'DOGE' => 25,
                        'EUR' => 2, // SEPA. Transfer INT (SHA) => 5 EUR
                        'PLN' => 0,
                    ),
                ),
            ),
            'exceptions' => array (
                'exact' => array (
                    '501' => '\\ccxt\\AuthenticationError', // array("error":501,"errorMsg":"Invalid API key","time":1560869976)
                ),
                'broad' => array (
                ),
            ),
        ));
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostInfo ($params);
        $data = $this->safe_value($response, 'data', array());
        $balances = $this->safe_value($data, 'balances', array());
        $available = $this->safe_value($balances, 'available', array());
        $blocked = $this->safe_value($balances, 'blocked', array());
        $result = array( 'info' => $response );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count ($codes); $i++) {
            $code = $codes[$i];
            $currencyId = $this->currencyId ($code);
            $free = $this->safe_float($available, $currencyId);
            if ($free !== null) {
                $account = $this->account ();
                $account['free'] = $this->safe_float($available, $currencyId);
                $account['used'] = $this->safe_float($blocked, $currencyId);
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
        );
        $orderbook = $this->publicGetJsonMarketOrderbook (array_merge ($request, $params));
        return $this->parse_order_book($orderbook);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
        );
        $ticker = $this->publicGetJsonMarketTicker (array_merge ($request, $params));
        $timestamp = $this->milliseconds ();
        $vwap = $this->safe_float($ticker, 'vwap');
        $baseVolume = $this->safe_float($ticker, 'volume');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
        }
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        $side = ($trade['type'] === 'bid') ? 'buy' : 'sell';
        $timestamp = $this->safe_integer($trade, 'date');
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        $id = $this->safe_string($trade, 'tid');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        return array (
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'order' => null,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'market' => $market['id'],
        );
        $response = $this->publicGetJsonMarketTrades (array_merge ($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '90m', $since = null, $limit = null) {
        return [
            $ohlcv['time'] * 1000,
            floatval ($ohlcv['open']),
            floatval ($ohlcv['high']),
            floatval ($ohlcv['low']),
            floatval ($ohlcv['close']),
            floatval ($ohlcv['vol']),
        ];
    }

    public function fetch_ohlcv ($symbol, $timeframe = '90m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $method = 'publicGetGraphsMarket' . $this->timeframes[$timeframe];
        $market = $this->market ($symbol);
        $request = array (
            'market' => $market['id'],
        );
        $response = $this->$method (array_merge ($request, $params));
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
            'type' => $side,
            'amount' => $amount,
            'rate' => $price,
        );
        $response = $this->privatePostTrade (array_merge ($request, $params));
        $result = array (
            'info' => $response,
        );
        if (is_array($response['data']) && array_key_exists('id', $response['data'])) {
            $result['id'] = $response['id'];
        }
        return $result;
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        return $this->privatePostCancel (array( 'id' => $id ));
    }

    public function is_fiat ($currency) {
        if ($currency === 'EUR') {
            return true;
        }
        if ($currency === 'PLN') {
            return true;
        }
        return false;
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $method = null;
        $request = array (
            'currency' => $currency['id'],
            'quantity' => $amount,
        );
        if ($this->is_fiat ($code)) {
            $method = 'privatePostWithdrawFiat';
            if (is_array($params) && array_key_exists('account', $params)) {
                $request['account'] = $params['account']; // bank account $code for withdrawal
            } else {
                throw new ExchangeError($this->id . ' requires account parameter to withdraw fiat currency');
            }
            if (is_array($params) && array_key_exists('account2', $params)) {
                $request['account2'] = $params['account2']; // bank SWIFT $code (EUR only)
            } else {
                if ($currency === 'EUR') {
                    throw new ExchangeError($this->id . ' requires account2 parameter to withdraw EUR');
                }
            }
            if (is_array($params) && array_key_exists('withdrawal_note', $params)) {
                $request['withdrawal_note'] = $params['withdrawal_note']; // a 10-character user-specified withdrawal note (PLN only)
            } else {
                if ($currency === 'PLN') {
                    throw new ExchangeError($this->id . ' requires withdrawal_note parameter to withdraw PLN');
                }
            }
        } else {
            $method = 'privatePostWithdraw';
            $request['address'] = $address;
        }
        $response = $this->$method (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $response,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        if ($api === 'public') {
            $url .= '/' . $this->implode_params($path . '.json', $params);
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $query = array_merge (array (
                'tonce' => $nonce,
                'method' => $path,
            ), $params);
            $body = $this->urlencode ($query);
            $headers = array (
                'API-Key' => $this->apiKey,
                'API-Hash' => $this->hmac ($this->encode ($body), $this->encode ($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body, $response) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //
        //     array("error":501,"errorMsg":"Invalid API key","time":1560869976)
        //
        $code = $this->safe_string($response, 'error');
        $message = $this->safe_string($response, 'errorMsg');
        $feedback = $this->id . ' ' . $this->json ($response);
        $exact = $this->exceptions['exact'];
        if (is_array($exact) && array_key_exists($code, $exact)) {
            throw new $exact[$code]($feedback);
        } else if (is_array($exact) && array_key_exists($message, $exact)) {
            throw new $exact[$message]($feedback);
        }
        $broad = $this->exceptions['broad'];
        $broadKey = $this->findBroadlyMatchedKey ($broad, $message);
        if ($broadKey !== null) {
            throw new $broad[$broadKey]($feedback);
        }
        // throw new ExchangeError($feedback); // unknown message
    }
}

<?php

namespace ccxt;

use Exception as Exception; // a common import

class coinmate extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'coinmate',
            'name' => 'CoinMate',
            'countries' => array ( 'GB', 'CZ', 'EU' ), // UK, Czech Republic
            'rateLimit' => 1000,
            'has' => array (
                'CORS' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27811229-c1efb510-606c-11e7-9a36-84ba2ce412d8.jpg',
                'api' => 'https://coinmate.io/api',
                'www' => 'https://coinmate.io',
                'fees' => 'https://coinmate.io/fees',
                'doc' => array (
                    'https://coinmate.docs.apiary.io',
                    'https://coinmate.io/developers',
                ),
                'referral' => 'https://coinmate.io?referral=YTFkM1RsOWFObVpmY1ZjMGREQmpTRnBsWjJJNVp3PT0',
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'orderBook',
                        'ticker',
                        'transactions',
                        'tradingPairs',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'balances',
                        'bitcoinWithdrawal',
                        'bitcoinDepositAddresses',
                        'buyInstant',
                        'buyLimit',
                        'cancelOrder',
                        'cancelOrderWithInfo',
                        'createVoucher',
                        'openOrders',
                        'redeemVoucher',
                        'sellInstant',
                        'sellLimit',
                        'transactionHistory',
                        'unconfirmedBitcoinDeposits',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.05 / 100,
                    'taker' => 0.15 / 100,
                ),
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetTradingPairs ($params);
        //
        //     {
        //         "error":false,
        //         "errorMessage":null,
        //         "$data" => array (
        //             array (
        //                 "name":"BTC_EUR",
        //                 "firstCurrency":"BTC",
        //                 "secondCurrency":"EUR",
        //                 "priceDecimals":2,
        //                 "lotDecimals":8,
        //                 "minAmount":0.0002,
        //                 "tradesWebSocketChannelId":"trades-BTC_EUR",
        //                 "orderBookWebSocketChannelId":"order_book-BTC_EUR",
        //                 "tradeStatisticsWebSocketChannelId":"statistics-BTC_EUR"
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count ($data); $i++) {
            $market = $data[$i];
            $id = $this->safe_string($market, 'name');
            $baseId = $this->safe_string($market, 'firstCurrency');
            $quoteId = $this->safe_string($market, 'secondCurrency');
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
                'active' => null,
                'info' => $market,
                'precision' => array (
                    'price' => $this->safe_integer($market, 'priceDecimals'),
                    'amount' => $this->safe_integer($market, 'lotDecimals'),
                ),
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($market, 'minAmount'),
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalances ($params);
        $balances = $this->safe_value($response, 'data');
        $result = array( 'info' => $response );
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count ($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->common_currency_code($currencyId);
            $balance = $this->safe_value($balances, $currencyId);
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['used'] = $this->safe_float($balance, 'reserved');
            $account['total'] = $this->safe_float($balance, 'balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'currencyPair' => $this->market_id($symbol),
            'groupByPriceLimit' => 'False',
        );
        $response = $this->publicGetOrderBook (array_merge ($request, $params));
        $orderbook = $response['data'];
        $timestamp = $this->safe_integer($orderbook, 'timestamp');
        if ($timestamp !== null) {
            $timestamp *= 1000;
        }
        return $this->parse_order_book($orderbook, $timestamp, 'bids', 'asks', 'price', 'amount');
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'currencyPair' => $this->market_id($symbol),
        );
        $response = $this->publicGetTicker (array_merge ($request, $params));
        $ticker = $this->safe_value($response, 'data');
        $timestamp = $this->safe_integer($ticker, 'timestamp');
        if ($timestamp !== null) {
            $timestamp = $timestamp * 1000;
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
            'vwap' => null,
            'askVolume' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'amount'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($trade, 'currencyPair');
            if (is_array($this->markets_by_id[$marketId]) && array_key_exists($marketId, $this->markets_by_id[$marketId])) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        $id = $this->safe_string($trade, 'transactionId');
        $timestamp = $this->safe_integer($trade, 'timestamp');
        return array (
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
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

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'currencyPair' => $market['id'],
            'minutesIntoHistory' => 10,
        );
        $response = $this->publicGetTransactions (array_merge ($request, $params));
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $method = 'privatePost' . $this->capitalize ($side);
        $request = array (
            'currencyPair' => $this->market_id($symbol),
        );
        if ($type === 'market') {
            if ($side === 'buy') {
                $request['total'] = $amount; // $amount in fiat
            } else {
                $request['amount'] = $amount; // $amount in fiat
            }
            $method .= 'Instant';
        } else {
            $request['amount'] = $amount; // $amount in crypto
            $request['price'] = $price;
            $method .= $this->capitalize ($type);
        }
        $response = $this->$method (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => (string) $response['data'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        return $this->privatePostCancelOrder (array( 'orderId' => $id ));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode ($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce ();
            $auth = $nonce . $this->uid . $this->apiKey;
            $signature = $this->hmac ($this->encode ($auth), $this->encode ($this->secret));
            $body = $this->urlencode (array_merge (array (
                'clientId' => $this->uid,
                'nonce' => $nonce,
                'publicKey' => $this->apiKey,
                'signature' => strtoupper($signature),
            ), $params));
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('error', $response)) {
            if ($response['error']) {
                throw new ExchangeError($this->id . ' ' . $this->json ($response));
            }
        }
        return $response;
    }
}

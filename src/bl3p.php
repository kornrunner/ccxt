<?php

namespace ccxt;

use Exception; // a common import

class bl3p extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bl3p',
            'name' => 'BL3P',
            'countries' => array( 'NL', 'EU' ), // Netherlands, EU
            'rateLimit' => 1000,
            'version' => '1',
            'comment' => 'An exchange market by BitonicNL',
            'has' => array(
                'CORS' => false,
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/28501752-60c21b82-6feb-11e7-818b-055ee6d0e754.jpg',
                'api' => 'https://api.bl3p.eu',
                'www' => 'https://bl3p.eu', // 'https://bitonic.nl'
                'doc' => array(
                    'https://github.com/BitonicNL/bl3p-api/tree/master/docs',
                    'https://bl3p.eu/api',
                    'https://bitonic.nl/en/api',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        '{market}/ticker',
                        '{market}/orderbook',
                        '{market}/trades',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        '{market}/money/depth/full',
                        '{market}/money/order/add',
                        '{market}/money/order/cancel',
                        '{market}/money/order/result',
                        '{market}/money/orders',
                        '{market}/money/orders/history',
                        '{market}/money/trades/fetch',
                        'GENMKT/money/info',
                        'GENMKT/money/deposit_address',
                        'GENMKT/money/new_deposit_address',
                        'GENMKT/money/wallet/history',
                        'GENMKT/money/withdraw',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/EUR' => array( 'id' => 'BTCEUR', 'symbol' => 'BTC/EUR', 'base' => 'BTC', 'quote' => 'EUR', 'baseId' => 'BTC', 'quoteId' => 'EUR', 'maker' => 0.0025, 'taker' => 0.0025 ),
                'LTC/EUR' => array( 'id' => 'LTCEUR', 'symbol' => 'LTC/EUR', 'base' => 'LTC', 'quote' => 'EUR', 'baseId' => 'LTC', 'quoteId' => 'EUR', 'maker' => 0.0025, 'taker' => 0.0025 ),
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGENMKTMoneyInfo ($params);
        $data = $this->safe_value($response, 'data', array());
        $wallets = $this->safe_value($data, 'wallets');
        $result = array( 'info' => $data );
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $currency = $this->currency($code);
            $currencyId = $currency['id'];
            $wallet = $this->safe_value($wallets, $currencyId, array());
            $available = $this->safe_value($wallet, 'available', array());
            $balance = $this->safe_value($wallet, 'balance', array());
            $account = $this->account();
            $account['free'] = $this->safe_number($available, 'value');
            $account['total'] = $this->safe_number($balance, 'value');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_bid_ask($bidask, $priceKey = 0, $amountKey = 1) {
        $price = $this->safe_number($bidask, $priceKey);
        $size = $this->safe_number($bidask, $amountKey);
        return array(
            $price / 100000.0,
            $size / 100000000.0,
        );
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetMarketOrderbook (array_merge($request, $params));
        $orderbook = $this->safe_value($response, 'data');
        return $this->parse_order_book($orderbook, null, 'bids', 'asks', 'price_int', 'amount_int');
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $request = array(
            'market' => $this->market_id($symbol),
        );
        $ticker = $this->publicGetMarketTicker (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($ticker, 'timestamp');
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker['volume'], '24h'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $id = $this->safe_string($trade, 'trade_id');
        $timestamp = $this->safe_integer($trade, 'date');
        $price = $this->safe_number($trade, 'price_int');
        if ($price !== null) {
            $price /= 100000.0;
        }
        $amount = $this->safe_number($trade, 'amount_int');
        if ($amount !== null) {
            $amount /= 100000000.0;
        }
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
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
        $market = $this->market($symbol);
        $response = $this->publicGetMarketTrades (array_merge(array(
            'market' => $market['id'],
        ), $params));
        $result = $this->parse_trades($response['data']['trades'], $market, $since, $limit);
        return $result;
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $market = $this->market($symbol);
        $order = array(
            'market' => $market['id'],
            'amount_int' => intval($amount * 100000000),
            'fee_currency' => $market['quote'],
            'type' => ($side === 'buy') ? 'bid' : 'ask',
        );
        if ($type === 'limit') {
            $order['price_int'] = intval($price * 100000.0);
        }
        $response = $this->privatePostMarketMoneyOrderAdd (array_merge($order, $params));
        $orderId = $this->safe_string($response['data'], 'order_id');
        return array(
            'info' => $response,
            'id' => $orderId,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'order_id' => $id,
        );
        return $this->privatePostMarketMoneyOrderCancel (array_merge($request, $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = $this->implode_params($path, $params);
        $url = $this->urls['api'] . '/' . $this->version . '/' . $request;
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array( 'nonce' => $nonce ), $query));
            $secret = base64_decode($this->secret);
            // eslint-disable-next-line quotes
            $auth = $request . "\0" . $body;
            $signature = $this->hmac($this->encode($auth), $secret, 'sha512', 'base64');
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Rest-Key' => $this->apiKey,
                'Rest-Sign' => $signature,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

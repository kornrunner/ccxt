<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class flowbtc extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'flowbtc',
            'name' => 'flowBTC',
            'countries' => array( 'BR' ), // Brazil
            'version' => 'v1',
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87443317-01c0d080-c5fe-11ea-95c2-9ebe1a8fafd9.jpg',
                'api' => 'https://publicapi.flowbtc.com.br',
                'www' => 'https://www.flowbtc.com.br',
                'doc' => 'https://www.flowbtc.com.br/api.html',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array(
                'public' => array(
                    'post' => array(
                        'GetTicker',
                        'GetTrades',
                        'GetTradesByDate',
                        'GetOrderBook',
                        'GetProductPairs',
                        'GetProducts',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'CreateAccount',
                        'GetUserInfo',
                        'SetUserInfo',
                        'GetAccountInfo',
                        'GetAccountTrades',
                        'GetDepositAddresses',
                        'Withdraw',
                        'CreateOrder',
                        'ModifyOrder',
                        'CancelOrder',
                        'CancelAllOrders',
                        'GetAccountOpenOrders',
                        'GetOrderFee',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.0025,
                    'taker' => 0.005,
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicPostGetProductPairs ($params);
        $markets = $this->safe_value($response, 'productPairs');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'name');
            $baseId = $this->safe_string($market, 'product1Label');
            $quoteId = $this->safe_string($market, 'product2Label');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $precision = array(
                'amount' => $this->safe_integer($market, 'product1DecimalPlaces'),
                'price' => $this->safe_integer($market, 'product2DecimalPlaces'),
            );
            $symbol = $base . '/' . $quote;
            $result[$symbol] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $market,
                'active' => null,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetAccountInfo ($params);
        $balances = $this->safe_value($response, 'currencies');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $balance['name'];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'balance');
            $account['total'] = $this->safe_number($balance, 'hold');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'productPair' => $market['id'],
        );
        $response = $this->publicPostGetOrderBook (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'bids', 'asks', 'px', 'qty');
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'productPair' => $market['id'],
        );
        $ticker = $this->publicPostGetTicker (array_merge($request, $params));
        $timestamp = $this->milliseconds();
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
            'baseVolume' => $this->safe_number($ticker, 'volume24hr'),
            'quoteVolume' => $this->safe_number($ticker, 'volume24hrProduct2'),
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market) {
        $timestamp = $this->safe_timestamp($trade, 'unixtime');
        $side = ($trade['incomingOrderSide'] === 0) ? 'buy' : 'sell';
        $id = $this->safe_string($trade, 'tid');
        $price = $this->safe_number($trade, 'px');
        $amount = $this->safe_number($trade, 'qty');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $market['symbol'],
            'id' => $id,
            'order' => null,
            'type' => null,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'takerOrMaker' => null,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'ins' => $market['id'],
            'startIndex' => -1,
        );
        $response = $this->publicPostGetTrades (array_merge($request, $params));
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $orderType = ($type === 'market') ? 1 : 0;
        $request = array(
            'ins' => $this->market_id($symbol),
            'side' => $side,
            'orderType' => $orderType,
            'qty' => $amount,
            'px' => $this->price_to_precision($symbol, $price),
        );
        $response = $this->privatePostCreateOrder (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['serverOrderId'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        if (is_array($params) && array_key_exists('ins', $params)) {
            $request = array(
                'serverOrderId' => $id,
            );
            return $this->privatePostCancelOrder (array_merge($request, $params));
        }
        throw new ExchangeError($this->id . ' cancelOrder() requires an `ins` $symbol parameter for cancelling an order');
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $body = $this->json($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $auth = (string) $nonce . $this->uid . $this->apiKey;
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret));
            $body = $this->json(array_merge(array(
                'apiKey' => $this->apiKey,
                'apiNonce' => $nonce,
                'apiSig' => strtoupper($signature),
            ), $params));
            $headers = array(
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('isAccepted', $response)) {
            if ($response['isAccepted']) {
                return $response;
            }
        }
        throw new ExchangeError($this->id . ' ' . $this->json($response));
    }
}

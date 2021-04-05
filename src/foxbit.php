<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class foxbit extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'foxbit',
            'name' => 'FoxBit',
            'countries' => array( 'BR' ),
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'rateLimit' => 1000,
            'version' => 'v1',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87443320-01c0d080-c5fe-11ea-92e2-4ef56d32b026.jpg',
                'api' => array(
                    'public' => 'https://api.blinktrade.com/api',
                    'private' => 'https://api.blinktrade.com/tapi',
                ),
                'www' => 'https://foxbit.com.br/exchange',
                'doc' => 'https://foxbit.com.br/api/',
            ),
            'comment' => 'Blinktrade API',
            'api' => array(
                'public' => array(
                    'get' => array(
                        '{currency}/ticker',    // ?crypto_currency=BTC
                        '{currency}/orderbook', // ?crypto_currency=BTC
                        '{currency}/trades',    // ?crypto_currency=BTC&since=<TIMESTAMP>&limit=<NUMBER>
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'D',   // order
                        'F',   // cancel order
                        'U2',  // balance
                        'U4',  // my orders
                        'U6',  // withdraw
                        'U18', // deposit
                        'U24', // confirm withdrawal
                        'U26', // list withdrawals
                        'U30', // list deposits
                        'U34', // ledger
                        'U70', // cancel withdrawal
                    ),
                ),
            ),
            'markets' => array(
                'BTC/VEF' => array( 'id' => 'BTCVEF', 'symbol' => 'BTC/VEF', 'base' => 'BTC', 'quote' => 'VEF', 'brokerId' => 1, 'broker' => 'SurBitcoin' ),
                'BTC/VND' => array( 'id' => 'BTCVND', 'symbol' => 'BTC/VND', 'base' => 'BTC', 'quote' => 'VND', 'brokerId' => 3, 'broker' => 'VBTC' ),
                'BTC/BRL' => array( 'id' => 'BTCBRL', 'symbol' => 'BTC/BRL', 'base' => 'BTC', 'quote' => 'BRL', 'brokerId' => 4, 'broker' => 'FoxBit' ),
                'BTC/PKR' => array( 'id' => 'BTCPKR', 'symbol' => 'BTC/PKR', 'base' => 'BTC', 'quote' => 'PKR', 'brokerId' => 8, 'broker' => 'UrduBit' ),
                'BTC/CLP' => array( 'id' => 'BTCCLP', 'symbol' => 'BTC/CLP', 'base' => 'BTC', 'quote' => 'CLP', 'brokerId' => 9, 'broker' => 'ChileBit' ),
            ),
            'options' => array(
                'brokerId' => '4', // https://blinktrade.com/docs/#brokers
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $request = array(
            'BalanceReqID' => $this->nonce(),
        );
        $response = $this->privatePostU2 (array_merge($request, $params));
        $balances = $this->safe_value($response['Responses'], $this->options['brokerId']);
        $result = array( 'info' => $response );
        if ($balances !== null) {
            $currencyIds = is_array($this->currencies_by_id) ? array_keys($this->currencies_by_id) : array();
            for ($i = 0; $i < count($currencyIds); $i++) {
                $currencyId = $currencyIds[$i];
                $code = $this->safe_currency_code($currencyId);
                // we only set the balance for the currency if that currency is present in $response
                // otherwise we will lose the info if the currency balance has been funded or traded or not
                if (is_array($balances) && array_key_exists($currencyId, $balances)) {
                    $account = $this->account();
                    $used = $this->safe_number($balances, $currencyId . '_locked');
                    if ($used !== null) {
                        $used *= 1e-8;
                    }
                    $total = $this->safe_number($balances, $currencyId);
                    if ($total !== null) {
                        $total *= 1e-8;
                    }
                    $account['used'] = $used;
                    $account['total'] = $total;
                    $result[$code] = $account;
                }
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['quote'],
            'crypto_currency' => $market['base'],
        );
        $response = $this->publicGetCurrencyOrderbook (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['quote'],
            'crypto_currency' => $market['base'],
        );
        $ticker = $this->publicGetCurrencyTicker (array_merge($request, $params));
        $timestamp = $this->milliseconds();
        $lowercaseQuote = strtolower($market['quote']);
        $quoteVolume = 'vol_' . $lowercaseQuote;
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'vol'),
            'quoteVolume' => $this->safe_number($ticker, $quoteVolume),
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $id = $this->safe_string($trade, 'tid');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_string($trade, 'side');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
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
            'currency' => $market['quote'],
            'crypto_currency' => $market['base'],
        );
        $response = $this->publicGetCurrencyTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $market = $this->market($symbol);
        $orderSide = ($side === 'buy') ? '1' : '2';
        $request = array(
            'ClOrdID' => $this->nonce(),
            'Symbol' => $market['id'],
            'Side' => $orderSide,
            'OrdType' => '2',
            'Price' => $price,
            'OrderQty' => $amount,
            'BrokerID' => $market['brokerId'],
        );
        $response = $this->privatePostD (array_merge($request, $params));
        $indexed = $this->index_by($response['Responses'], 'MsgType');
        $execution = $indexed['8'];
        return array(
            'info' => $response,
            'id' => $execution['OrderID'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        return $this->privatePostF (array_merge(array(
            'ClOrdID' => $id,
        ), $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $request = array_merge(array( 'MsgType' => $path ), $query);
            $body = $this->json($request);
            $headers = array(
                'APIKey' => $this->apiKey,
                'Nonce' => $nonce,
                'Signature' => $this->hmac($this->encode($nonce), $this->encode($this->secret)),
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('Status', $response)) {
            if ($response['Status'] !== 200) {
                throw new ExchangeError($this->id . ' ' . $this->json($response));
            }
        }
        return $response;
    }
}

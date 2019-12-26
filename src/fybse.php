<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class fybse extends Exchange {

    public function describe () {
        return array_replace_recursive(parent::describe (), array(
            'id' => 'fybse',
            'name' => 'FYB-SE',
            'countries' => array( 'SE' ), // Sweden
            'has' => array(
                'CORS' => false,
            ),
            'rateLimit' => 1500,
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766512-31019772-5edb-11e7-8241-2e675e6797f1.jpg',
                'api' => 'https://www.fybse.se/api/SEK',
                'www' => 'https://www.fybse.se',
                'doc' => 'https://fyb.docs.apiary.io',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ticker',
                        'tickerdetailed',
                        'orderbook',
                        'trades',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'test',
                        'getaccinfo',
                        'getpendingorders',
                        'getorderhistory',
                        'cancelpendingorder',
                        'placeorder',
                        'withdraw',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/SEK' => array( 'id' => 'SEK', 'symbol' => 'BTC/SEK', 'base' => 'BTC', 'quote' => 'SEK' ),
            ),
        ));
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetaccinfo ($params);
        $btc = $this->safe_float($response, 'btcBal');
        $symbol = $this->symbols[0];
        $quote = $this->markets[$symbol]['quote'];
        $lowercase = strtolower($quote) . 'Bal';
        $fiat = $this->safe_float($response, $lowercase);
        $crypto = $this->account ();
        $crypto['total'] = $btc;
        $result = array( 'BTC' => $crypto );
        $result[$quote] = $this->account ();
        $result[$quote]['total'] = $fiat;
        $result['info'] = $response;
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetOrderbook ($params);
        return $this->parse_order_book($response);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $ticker = $this->publicGetTickerdetailed ($params);
        $timestamp = $this->milliseconds ();
        $last = $this->safe_float($ticker, 'last');
        $volume = $this->safe_float($ticker, 'vol');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => null,
            'low' => null,
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $volume,
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
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
        return array(
            'id' => $id,
            'info' => $trade,
            'order' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => null,
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
        $response = $this->publicGetTrades ($params);
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'qty' => $amount,
            'price' => $price,
            'type' => strtoupper($side[0]),
        );
        $response = $this->privatePostPlaceorder (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['pending_oid'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderNo' => $id,
        );
        return $this->privatePostCancelpendingorder (array_merge($request, $params));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $path;
        if ($api === 'public') {
            $url .= '.json';
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $body = $this->urlencode (array_merge(array( 'timestamp' => $nonce ), $params));
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'key' => $this->apiKey,
                'sig' => $this->hmac ($this->encode ($body), $this->encode ($this->secret), 'sha1'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if ($api === 'private') {
            if (is_array($response) && array_key_exists('error', $response)) {
                if ($response['error']) {
                    throw new ExchangeError($this->id . ' ' . $this->json ($response));
                }
            }
        }
        return $response;
    }
}

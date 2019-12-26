<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\NotSupported;

class bitlish extends Exchange {

    public function describe () {
        return array_replace_recursive(parent::describe (), array(
            'id' => 'bitlish',
            'name' => 'Bitlish',
            'countries' => array( 'GB', 'EU', 'RU' ),
            'rateLimit' => 1500,
            'version' => 'v1',
            'has' => array(
                'CORS' => false,
                'fetchTickers' => true,
                'fetchOHLCV' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1h' => 3600,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766275-dcfc6c30-5ed3-11e7-839d-00a846385d0b.jpg',
                'api' => 'https://bitlish.com/api',
                'www' => 'https://bitlish.com',
                'doc' => 'https://bitlish.com/api',
                'fees' => 'https://bitlish.com/fees',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => false,
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.3 / 100, // anonymous 0.3%, verified 0.2%
                    'maker' => 0.2 / 100, // anonymous 0.2%, verified 0.1%
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(
                        'BTC' => 0.001,
                        'LTC' => 0.001,
                        'DOGE' => 0.001,
                        'ETH' => 0.001,
                        'XMR' => 0,
                        'ZEC' => 0.001,
                        'DASH' => 0.0001,
                        'EUR' => 50,
                    ),
                    'deposit' => array(
                        'BTC' => 0,
                        'LTC' => 0,
                        'DOGE' => 0,
                        'ETH' => 0,
                        'XMR' => 0,
                        'ZEC' => 0,
                        'DASH' => 0,
                        'EUR' => 0,
                    ),
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'instruments',
                        'ohlcv',
                        'pairs',
                        'tickers',
                        'trades_depth',
                        'trades_history',
                    ),
                    'post' => array(
                        'instruments',
                        'ohlcv',
                        'pairs',
                        'tickers',
                        'trades_depth',
                        'trades_history',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'accounts_operations',
                        'balance',
                        'cancel_trade',
                        'cancel_trades_by_ids',
                        'cancel_all_trades',
                        'create_bcode',
                        'create_template_wallet',
                        'create_trade',
                        'deposit',
                        'list_accounts_operations_from_ts',
                        'list_active_trades',
                        'list_bcodes',
                        'list_my_matches_from_ts',
                        'list_my_trades',
                        'list_my_trads_from_ts',
                        'list_payment_methods',
                        'list_payments',
                        'redeem_code',
                        'resign',
                        'signin',
                        'signout',
                        'trade_details',
                        'trade_options',
                        'withdraw',
                        'withdraw_by_id',
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'DSH' => 'DASH',
                'XDG' => 'DOGE',
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetPairs ($params);
        $result = array();
        $keys = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $market = $response[$key];
            $id = $this->safe_string($market, 'id');
            $name = $this->safe_string($market, 'name');
            list($baseId, $quoteId) = explode('/', $name);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $result[] = array(
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

    public function parse_ticker ($ticker, $market) {
        $timestamp = $this->milliseconds ();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'last');
        return array(
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'high' => $this->safe_float($ticker, 'max'),
            'low' => $this->safe_float($ticker, 'min'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'first'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $this->safe_float($ticker, 'prc') * 100,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'sum'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $tickers = $this->publicGetTickers ($params);
        $ids = is_array($tickers) ? array_keys($tickers) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $this->safe_value($this->markets_by_id, $id);
            $symbol = null;
            if ($market !== null) {
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
        return $result;
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTickers ($params);
        $marketId = $market['id'];
        return $this->parse_ticker($response[$marketId], $market);
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1h', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // $market = $this->market ($symbol);
        $now = $this->seconds ();
        $start = $now - 86400 * 30; // last 30 days
        if ($since !== null) {
            $start = intval ($since / 1000);
        }
        $interval = array( (string) $start, null );
        $request = array(
            'time_range' => $interval,
        );
        return $this->publicPostOhlcv (array_merge($request, $params));
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair_id' => $this->market_id($symbol),
        );
        $response = $this->publicGetTradesDepth (array_merge($request, $params));
        $timestamp = null;
        $last = $this->safe_integer($response, 'last');
        if ($last !== null) {
            $timestamp = intval ($last / 1000);
        }
        return $this->parse_order_book($response, $timestamp, 'bid', 'ask', 'price', 'volume');
    }

    public function parse_trade ($trade, $market = null) {
        $side = ($trade['dir'] === 'bid') ? 'buy' : 'sell';
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($trade, 'created');
        if ($timestamp !== null) {
            $timestamp = intval ($timestamp / 1000);
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        return array(
            'id' => null,
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
        $response = $this->publicGetTradesHistory (array_merge(array(
            'pair_id' => $market['id'],
        ), $params));
        return $this->parse_trades($response['list'], $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalance ($params);
        $result = array( 'info' => $response );
        $currencyIds = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account ();
            $balance = $this->safe_value($response, $currencyId, array());
            $account['free'] = $this->safe_float($balance, 'funds');
            $account['used'] = $this->safe_float($balance, 'holded');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function sign_in ($params = array ()) {
        $request = array(
            'login' => $this->login,
            'passwd' => $this->password,
        );
        return $this->privatePostSignin (array_merge($request, $params));
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair_id' => $this->market_id($symbol),
            'dir' => ($side === 'buy') ? 'bid' : 'ask',
            'amount' => $amount,
        );
        if ($type === 'limit') {
            $request['price'] = $price;
        }
        $response = $this->privatePostCreateTrade (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        return $this->privatePostCancelTrade (array_merge($request, $params));
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        if ($code !== 'BTC') {
            // they did not document other types...
            throw new NotSupported($this->id . ' currently supports BTC withdrawals only, until they document other currencies...');
        }
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array(
            'currency' => $currency['id'],
            'amount' => floatval ($amount),
            'account' => $address,
            'payment_method' => 'bitcoin', // they did not document other types...
        );
        $response = $this->privatePostWithdraw (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['message_id'],
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            if ($method === 'GET') {
                if ($params) {
                    $url .= '?' . $this->urlencode ($params);
                }
            } else {
                $body = $this->json ($params);
                $headers = array( 'Content-Type' => 'application/json' );
            }
        } else {
            $this->check_required_credentials();
            $body = $this->json (array_merge(array( 'token' => $this->apiKey ), $params));
            $headers = array( 'Content-Type' => 'application/json' );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class btctradeua extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'btctradeua',
            'name' => 'BTC Trade UA',
            'countries' => array( 'UA' ), // Ukraine,
            'rateLimit' => 3000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchOpenOrders' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
                'signIn' => true,
            ),
            'urls' => array(
                'referral' => 'https://btc-trade.com.ua/registration/22689',
                'logo' => 'https://user-images.githubusercontent.com/1294454/27941483-79fc7350-62d9-11e7-9f61-ac47f28fcd96.jpg',
                'api' => 'https://btc-trade.com.ua/api',
                'www' => 'https://btc-trade.com.ua',
                'doc' => 'https://docs.google.com/document/d/1ocYA0yMy_RXd561sfG3qEPZ80kyll36HUxvCRe5GbhE/edit',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'deals/{symbol}',
                        'trades/sell/{symbol}',
                        'trades/buy/{symbol}',
                        'japan_stat/high/{symbol}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'auth',
                        'ask/{symbol}',
                        'balance',
                        'bid/{symbol}',
                        'buy/{symbol}',
                        'my_orders/{symbol}',
                        'order/status/{id}',
                        'remove/order/{id}',
                        'sell/{symbol}',
                    ),
                ),
            ),
            'markets' => array(
                'BCH/UAH' => array( 'id' => 'bch_uah', 'symbol' => 'BCH/UAH', 'base' => 'BCH', 'quote' => 'UAH', 'baseId' => 'bch', 'quoteId' => 'uah' ),
                'BTC/UAH' => array( 'id' => 'btc_uah', 'symbol' => 'BTC/UAH', 'base' => 'BTC', 'quote' => 'UAH', 'baseId' => 'btc', 'quoteId' => 'uah', 'precision' => array( 'price' => 1 ), 'limits' => array( 'amount' => array( 'min' => 0.0000000001 ))),
                'DASH/BTC' => array( 'id' => 'dash_btc', 'symbol' => 'DASH/BTC', 'base' => 'DASH', 'quote' => 'BTC', 'baseId' => 'dash', 'quoteId' => 'btc' ),
                'DASH/UAH' => array( 'id' => 'dash_uah', 'symbol' => 'DASH/UAH', 'base' => 'DASH', 'quote' => 'UAH', 'baseId' => 'dash', 'quoteId' => 'uah' ),
                'DOGE/BTC' => array( 'id' => 'doge_btc', 'symbol' => 'DOGE/BTC', 'base' => 'DOGE', 'quote' => 'BTC', 'baseId' => 'doge', 'quoteId' => 'btc' ),
                'DOGE/UAH' => array( 'id' => 'doge_uah', 'symbol' => 'DOGE/UAH', 'base' => 'DOGE', 'quote' => 'UAH', 'baseId' => 'doge', 'quoteId' => 'uah' ),
                'ETH/UAH' => array( 'id' => 'eth_uah', 'symbol' => 'ETH/UAH', 'base' => 'ETH', 'quote' => 'UAH', 'baseId' => 'eth', 'quoteId' => 'uah' ),
                'ITI/UAH' => array( 'id' => 'iti_uah', 'symbol' => 'ITI/UAH', 'base' => 'ITI', 'quote' => 'UAH', 'baseId' => 'iti', 'quoteId' => 'uah' ),
                'KRB/UAH' => array( 'id' => 'krb_uah', 'symbol' => 'KRB/UAH', 'base' => 'KRB', 'quote' => 'UAH', 'baseId' => 'krb', 'quoteId' => 'uah' ),
                'LTC/BTC' => array( 'id' => 'ltc_btc', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'baseId' => 'ltc', 'quoteId' => 'btc' ),
                'LTC/UAH' => array( 'id' => 'ltc_uah', 'symbol' => 'LTC/UAH', 'base' => 'LTC', 'quote' => 'UAH', 'baseId' => 'ltc', 'quoteId' => 'uah' ),
                'NVC/BTC' => array( 'id' => 'nvc_btc', 'symbol' => 'NVC/BTC', 'base' => 'NVC', 'quote' => 'BTC', 'baseId' => 'nvc', 'quoteId' => 'btc' ),
                'NVC/UAH' => array( 'id' => 'nvc_uah', 'symbol' => 'NVC/UAH', 'base' => 'NVC', 'quote' => 'UAH', 'baseId' => 'nvc', 'quoteId' => 'uah' ),
                'PPC/BTC' => array( 'id' => 'ppc_btc', 'symbol' => 'PPC/BTC', 'base' => 'PPC', 'quote' => 'BTC', 'baseId' => 'ppc', 'quoteId' => 'btc' ),
                'SIB/UAH' => array( 'id' => 'sib_uah', 'symbol' => 'SIB/UAH', 'base' => 'SIB', 'quote' => 'UAH', 'baseId' => 'sib', 'quoteId' => 'uah' ),
                'XMR/UAH' => array( 'id' => 'xmr_uah', 'symbol' => 'XMR/UAH', 'base' => 'XMR', 'quote' => 'UAH', 'baseId' => 'xmr', 'quoteId' => 'uah' ),
                'ZEC/UAH' => array( 'id' => 'zec_uah', 'symbol' => 'ZEC/UAH', 'base' => 'ZEC', 'quote' => 'UAH', 'baseId' => 'zec', 'quoteId' => 'uah' ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.1 / 100,
                    'taker' => 0.1 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => 0.0006,
                        'LTC' => 0.01,
                        'NVC' => 0.01,
                        'DOGE' => 10,
                    ),
                ),
            ),
        ));
    }

    public function sign_in($params = array ()) {
        return $this->privatePostAuth ($params);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalance ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'accounts');
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_number($balance, 'balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $bids = $this->publicGetTradesBuySymbol (array_merge($request, $params));
        $asks = $this->publicGetTradesSellSymbol (array_merge($request, $params));
        $orderbook = array(
            'bids' => array(),
            'asks' => array(),
        );
        if ($bids) {
            if (is_array($bids) && array_key_exists('list', $bids)) {
                $orderbook['bids'] = $bids['list'];
            }
        }
        if ($asks) {
            if (is_array($asks) && array_key_exists('list', $asks)) {
                $orderbook['asks'] = $asks['list'];
            }
        }
        return $this->parse_order_book($orderbook, null, 'bids', 'asks', 'price', 'currency_trade');
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        $response = $this->publicGetJapanStatHighSymbol (array_merge($request, $params));
        $ticker = $this->safe_value($response, 'trades');
        $timestamp = $this->milliseconds();
        $result = array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => null,
            'last' => null,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => null,
            'info' => $ticker,
        );
        $tickerLength = is_array($ticker) ? count($ticker) : 0;
        if ($tickerLength > 0) {
            $start = max ($tickerLength - 48, 0);
            for ($i = $start; $i < count($ticker); $i++) {
                $candle = $ticker[$i];
                if ($result['open'] === null) {
                    $result['open'] = $candle[1];
                }
                if (($result['high'] === null) || ($result['high'] < $candle[2])) {
                    $result['high'] = $candle[2];
                }
                if (($result['low'] === null) || ($result['low'] > $candle[3])) {
                    $result['low'] = $candle[3];
                }
                if ($result['baseVolume'] === null) {
                    $result['baseVolume'] = -$candle[5];
                } else {
                    $result['baseVolume'] -= $candle[5];
                }
            }
            $last = $tickerLength - 1;
            $result['last'] = $ticker[$last][4];
            $result['close'] = $result['last'];
            $result['baseVolume'] = -1 * $result['baseVolume'];
        }
        return $result;
    }

    public function convert_cyrillic_month_name_to_string($cyrillic) {
        $months = array(
            'января' => '01',
            'февраля' => '02',
            'марта' => '03',
            'апреля' => '04',
            'мая' => '05',
            'июня' => '06',
            'июля' => '07',
            'августа' => '08',
            'сентября' => '09',
            'октября' => '10',
            'ноября' => '11',
            'декабря' => '12',
        );
        return $this->safe_string($months, $cyrillic);
    }

    public function parse_cyrillic_datetime($cyrillic) {
        $parts = explode(' ', $cyrillic);
        $day = $parts[0];
        $month = $this->convert_cyrillic_month_name_to_string($parts[1]);
        if (!$month) {
            throw new ExchangeError($this->id . ' parseTrade() null $month name => ' . $cyrillic);
        }
        $year = $parts[2];
        $hms = $parts[4];
        $hmsLength = is_array($hms) ? count($hms) : 0;
        if ($hmsLength === 7) {
            $hms = '0' . $hms;
        }
        if (strlen($day) === 1) {
            $day = '0' . $day;
        }
        $ymd = implode('-', array($year, $month, $day));
        $ymdhms = $ymd . 'T' . $hms;
        $timestamp = $this->parse8601($ymdhms);
        // server reports local time, adjust to UTC
        $md = implode('', array($month, $day));
        $md = intval($md);
        // a special case for DST
        // subtract 2 hours during winter
        if ($md < 325 || $md > 1028) {
            return $timestamp - 7200000;
        }
        // subtract 3 hours during summer
        return $timestamp - 10800000;
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse_cyrillic_datetime($this->safe_string($trade, 'pub_date'));
        $id = $this->safe_string($trade, 'id');
        $type = 'limit';
        $side = $this->safe_string($trade, 'type');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amnt_trade');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
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
            'type' => $type,
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
            'symbol' => $market['id'],
        );
        $response = $this->publicGetDealsSymbol (array_merge($request, $params));
        // they report each trade twice (once for both of the two sides of the fill)
        // deduplicate $trades for that reason
        $trades = array();
        for ($i = 0; $i < count($response); $i++) {
            if (fmod($response[$i]['id'], 2)) {
                $trades[] = $response[$i];
            }
        }
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $method = 'privatePost' . $this->capitalize($side) . 'Id';
        $request = array(
            'count' => $amount,
            'currency1' => $market['quoteId'],
            'currency' => $market['baseId'],
            'price' => $price,
        );
        return $this->$method (array_merge($request, $params));
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => $id,
        );
        return $this->privatePostRemoveOrderId (array_merge($request, $params));
    }

    public function parse_order($order, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'id' => $this->safe_string($order, 'id'),
            'clientOrderId' => null,
            'timestamp' => $timestamp, // until they fix their $timestamp
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => 'open',
            'symbol' => $symbol,
            'type' => null,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $this->safe_string($order, 'type'),
            'price' => $this->safe_number($order, 'price'),
            'stopPrice' => null,
            'amount' => $this->safe_number($order, 'amnt_trade'),
            'filled' => 0,
            'remaining' => $this->safe_number($order, 'amnt_trade'),
            'trades' => null,
            'info' => $order,
            'cost' => null,
            'average' => null,
            'fee' => null,
        );
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->privatePostMyOrdersSymbol (array_merge($request, $params));
        $orders = $this->safe_value($response, 'your_open_orders');
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= $this->implode_params($path, $query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array(
                'out_order_id' => $nonce,
                'nonce' => $nonce,
            ), $query));
            $auth = $body . $this->secret;
            $headers = array(
                'public-key' => $this->apiKey,
                'api-sign' => $this->hash($this->encode($auth), 'sha256'),
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

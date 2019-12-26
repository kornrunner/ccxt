<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;

class kkex extends Exchange {

    public function describe () {
        return array_replace_recursive(parent::describe (), array(
            'id' => 'kkex',
            'name' => 'KKEX',
            'countries' => array( 'CN', 'US', 'JP' ),
            'version' => 'v2',
            'has' => array(
                'CORS' => false,
                'fetchBalance' => true,
                'fetchTickers' => true,
                'fetchOrders' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'createMarketOrder' => true,
                'fetchOrder' => true,
            ),
            'timeframes' => array(
                '1m' => '1min',
                '5m' => '5min',
                '15m' => '15min',
                '30m' => '30min',
                '1h' => '1hour',
                '4h' => '4hour',
                '12h' => '12hour',
                '1d' => '1day',
                '1w' => '1week',
                '1M' => '1month',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/47401462-2e59f800-d74a-11e8-814f-e4ae17b4968a.jpg',
                'api' => array(
                    'public' => 'https://kkex.com/api/v1',
                    'private' => 'https://kkex.com/api/v2',
                    'v1' => 'https://kkex.com/api/v1',
                ),
                'www' => 'https://kkex.com',
                'doc' => 'https://kkex.com/api_wiki/cn/',
                'fees' => 'https://intercom.help/kkex/fee',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'exchange_rate',
                        'products',
                        'assets',
                        'tickers',
                        'ticker',
                        'depth',
                        'trades',
                        'kline',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'profile',
                        'trade',
                        'batch_trade',
                        'cancel_order',
                        'cancel_all_orders',
                        'order_history',
                        'userinfo',
                        'order_info',
                        'orders_info',
                    ),
                ),
                'v1' => array(
                    'post' => array(
                        'process_strategy',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.002,
                    'maker' => 0.002,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(),
                    'deposit' => array(),
                ),
            ),
            'options' => array(
                'lastNonceTimestamp' => 0,
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $tickers = $this->publicGetTickers ($params);
        $tickers = $tickers['tickers'];
        $products = $this->publicGetProducts ($params);
        $products = $products['products'];
        $markets = array();
        for ($k = 0; $k < count($tickers); $k++) {
            $keys = is_array($tickers[$k]) ? array_keys($tickers[$k]) : array();
            $markets[] = $keys[0];
        }
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $id = $markets[$i];
            $market = $markets[$i];
            $baseId = '';
            $quoteId = '';
            $precision = array();
            $limits = array();
            for ($j = 0; $j < count($products); $j++) {
                $p = $products[$j];
                if ($p['mark_asset'] . $p['base_asset'] === $market) {
                    $quoteId = $p['base_asset'];
                    $baseId = $p['mark_asset'];
                    $price_scale_str = (string) $p['price_scale'];
                    $scale = strlen($price_scale_str) - 1;
                    $precision = array(
                        'price' => $scale,
                        'amount' => $scale,
                    );
                    $limits = array(
                        'amount' => array(
                            'min' => max ($this->safe_float($p, 'min_bid_size'), $this->safe_float($p, 'min_ask_size')),
                            'max' => min ($this->safe_float($p, 'max_bid_size'), $this->safe_float($p, 'max_ask_size')),
                        ),
                        'price' => array(
                            'min' => $this->safe_float($p, 'min_price'),
                            'max' => $this->safe_float($p, 'max_price'),
                        ),
                    );
                    $limits['cost'] = array(
                        'min' => $this->safe_float($p, 'min_bid_amount'),
                        'max' => $this->safe_float($p, 'max_bid_amount'),
                    );
                }
            }
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
                'active' => true,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'date');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->markets[$symbol];
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $ticker = array_merge($response['ticker'], $this->omit ($response, 'ticker'));
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        //
        //     {    date =>    1540350657,
        //       $tickers => array( { ENUBTC => array( sell => "0.00000256",
        //                               buy => "0.00000253",
        //                              last => "0.00000253",
        //                               vol => "138686.828804",
        //                              high => "0.00000278",
        //                               low => "0.00000253",
        //                              open => "0.0000027"      } ),
        //                  { ENUEOS => { sell => "0.00335",
        //                               buy => "0.002702",
        //                              last => "0.0034",
        //                               vol => "15084.9",
        //                              high => "0.0034",
        //                               low => "0.003189",
        //                              open => "0.003189"  } }           ),
        //        $result =>    true                                          }
        //
        $tickers = $this->safe_value($response, 'tickers');
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ids = is_array($tickers[$i]) ? array_keys($tickers[$i]) : array();
            $id = $ids[0];
            $market = $this->safe_value($this->markets_by_id, $id);
            if ($market !== null) {
                $symbol = $market['symbol'];
                $ticker = array_merge($tickers[$i][$id], $this->omit ($response, 'tickers'));
                $result[$symbol] = $this->parse_ticker($ticker, $market);
            }
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->publicGetDepth (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->safe_integer($trade, 'date_ms');
        $datetime = $this->iso8601 ($timestamp);
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
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
        $id = $this->safe_string($trade, 'tid');
        $type = null;
        $side = $this->safe_string($trade, 'type');
        return array(
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $datetime,
            'symbol' => $symbol,
            'order' => null,
            'type' => $type,
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
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostUserinfo ($params);
        $balances = $this->safe_value($response, 'info');
        $result = array( 'info' => $response );
        $funds = $this->safe_value($balances, 'funds');
        $free = $this->safe_value($funds, 'free', array());
        $freezed = $this->safe_value($funds, 'freezed', array());
        $currencyIds = is_array($free) ? array_keys($free) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account ();
            $account['free'] = $this->safe_float($free, $currencyId);
            $account['used'] = $this->safe_float($freezed, $currencyId);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        if (!$symbol) {
            throw new ArgumentsRequired($this->id . ' fetchOrder requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'order_id' => $id,
            'symbol' => $market['id'],
        );
        $response = $this->privatePostOrderInfo (array_merge($request, $params));
        if ($response['result']) {
            return $this->parse_order($response['order'], $market);
        }
        throw new OrderNotFound($this->id . ' order ' . $id . ' not found');
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '1m', $since = null, $limit = null) {
        return [
            intval ($ohlcv[0]),
            floatval ($ohlcv[1]),
            floatval ($ohlcv[2]),
            floatval ($ohlcv[3]),
            floatval ($ohlcv[4]),
            floatval ($ohlcv[5]),
        ];
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
            'type' => $this->timeframes[$timeframe],
        );
        if ($since !== null) {
            // $since = $this->milliseconds () - $this->parse_timeframe($timeframe) * $limit * 1000;
            $request['since'] = intval ($since / 1000);
        }
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->publicGetKline (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             "1521072000000",
        //             "0.000002",
        //             "0.00003",
        //             "0.000002",
        //             "0.00003",
        //             "3.106889"
        //         ),
        //         array(
        //             "1517356800000",
        //             "0.1",
        //             "0.1",
        //             "0.00000013",
        //             "0.000001",
        //             "542832.83114"
        //         )
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function parse_order_status ($status) {
        $statuses = array(
            '-1' => 'canceled',
            '0' => 'open',
            '1' => 'open',
            '2' => 'closed',
            '3' => 'open',
            '4' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order ($order, $market = null) {
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_string($order, 'side');
        if ($side === null) {
            $side = $this->safe_string($order, 'type');
        }
        $timestamp = $this->safe_integer($order, 'create_date');
        $id = $this->safe_string_2($order, 'order_id', 'id');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount');
        $filled = $this->safe_float($order, 'deal_amount');
        $average = $this->safe_float($order, 'avg_price');
        $average = $this->safe_float($order, 'price_avg', $average);
        $remaining = null;
        $cost = null;
        if ($filled !== null) {
            if ($amount !== null) {
                $remaining = $amount - $filled;
            }
            if ($average !== null) {
                $cost = $average * $filled;
            }
        }
        return array(
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'average' => $average,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'fee' => null,
            'info' => $order,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
            'type' => $side,
        );
        if ($type === 'market') {
            // for $market buy it requires the $amount of quote currency to spend
            if ($side === 'buy') {
                if ($this->options['createMarketBuyOrderRequiresPrice']) {
                    if ($price === null) {
                        throw new InvalidOrder($this->id . " createOrder() requires the $price argument with $market buy orders to calculate total order cost ($amount to spend), where cost = $amount * $price-> Supply a $price argument to createOrder() call if you want the cost to be calculated for you from $price and $amount, or, alternatively, add .options['createMarketBuyOrderRequiresPrice'] = false to supply the cost in the $amount argument (the exchange-specific behaviour)");
                    } else {
                        $request['amount'] = $this->cost_to_precision($symbol, floatval ($amount) * floatval ($price));
                    }
                }
                $request['price'] = $this->cost_to_precision($symbol, $amount);
            } else {
                $request['amount'] = $this->amount_to_precision($symbol, $amount);
            }
            $request['type'] .= '_' . $type;
        } else {
            $request['amount'] = $this->amount_to_precision($symbol, $amount);
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostTrade (array_merge($request, $params));
        $id = $this->safe_string($response, 'order_id');
        return array(
            'info' => $response,
            'id' => $id,
            'datetime' => null,
            'timestamp' => null,
            'lastTradeTimestamp' => null,
            'status' => 'open',
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => null,
            'amount' => $amount,
            'filled' => null,
            'remaining' => null,
            'trades' => null,
            'fee' => null,
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'order_id' => $id,
            'symbol' => $market['id'],
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['page_length'] = $limit; // 20 by default
        }
        $response = $this->privatePostOrderHistory (array_merge($request, $params));
        return $this->parse_orders($response['orders'], $market, $since, $limit);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 0,
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 1,
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $path;
        if ($api === 'public') {
            $url .= '?' . $this->urlencode ($params);
            $headers = array( 'Content-Type' => 'application/json' );
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $signature = array_merge(array(
                'nonce' => $nonce,
                'api_key' => $this->apiKey,
            ), $params);
            $signature = $this->urlencode ($this->keysort ($signature));
            $signature .= '&secret_key=' . $this->secret;
            $signature = $this->hash ($this->encode ($signature), 'md5');
            $signature = strtoupper($signature);
            $body = array_merge(array(
                'api_key' => $this->apiKey,
                'sign' => $signature,
                'nonce' => $nonce,
            ), $params);
            $body = $this->urlencode ($body);
            $headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

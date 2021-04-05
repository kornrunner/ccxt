<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;

class fcoin extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'fcoin',
            'name' => 'FCoin',
            'countries' => array( 'CN' ),
            'rateLimit' => 2000,
            'userAgent' => $this->userAgents['chrome39'],
            'version' => 'v2',
            'accounts' => null,
            'accountsById' => null,
            'hostname' => 'fcoin.com',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => false,
                'fetchDepositAddress' => false,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrderBooks' => false,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchTradingLimits' => false,
                'withdraw' => false,
            ),
            'timeframes' => array(
                '1m' => 'M1',
                '3m' => 'M3',
                '5m' => 'M5',
                '15m' => 'M15',
                '30m' => 'M30',
                '1h' => 'H1',
                '1d' => 'D1',
                '1w' => 'W1',
                '1M' => 'MN',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/42244210-c8c42e1e-7f1c-11e8-8710-a5fb63b165c4.jpg',
                'api' => array(
                    'public' => 'https://api.{hostname}',
                    'private' => 'https://api.{hostname}',
                    'market' => 'https://api.{hostname}',
                    'openapi' => 'https://www.{hostname}',
                ),
                'www' => 'https://www.fcoin.com',
                'referral' => 'https://www.fcoin.com/i/Z5P7V',
                'doc' => 'https://developer.fcoin.com',
                'fees' => 'https://fcoinjp.zendesk.com/hc/en-us/articles/360018727371',
            ),
            'api' => array(
                'openapi' => array(
                    'get' => array(
                        'symbols',
                    ),
                ),
                'market' => array(
                    'get' => array(
                        'ticker/{symbol}',
                        'depth/{level}/{symbol}',
                        'trades/{symbol}',
                        'candles/{timeframe}/{symbol}',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'symbols',
                        'currencies',
                        'server-time',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts/balance',
                        'assets/accounts/balance',
                        'broker/otc/suborders',
                        'broker/otc/suborders/{id}',
                        'broker/otc/suborders/{id}/payments',
                        'broker/otc/users',
                        'broker/otc/users/me/balances',
                        'broker/otc/users/me/balance',
                        'broker/leveraged_accounts/account',
                        'broker/leveraged_accounts',
                        'orders',
                        'orders/{order_id}',
                        'orders/{order_id}/match-results', // check order result
                    ),
                    'post' => array(
                        'assets/accounts/assets-to-spot',
                        'accounts/spot-to-assets',
                        'broker/otc/assets/transfer/in',
                        'broker/otc/assets/transfer/out',
                        'broker/otc/suborders',
                        'broker/otc/suborders/{id}/pay_confirm',
                        'broker/otc/suborders/{id}/cancel',
                        'broker/leveraged/assets/transfer/in',
                        'broker/leveraged/assets/transfer/out',
                        'orders',
                        'orders/{order_id}/submit-cancel', // cancel order
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => -0.0002,
                    'taker' => 0.0003,
                ),
            ),
            'limits' => array(
                'amount' => array( 'min' => 0.01, 'max' => 100000 ),
            ),
            'options' => array(
                'createMarketBuyOrderRequiresPrice' => true,
                'fetchMarketsMethod' => 'fetch_markets_from_open_api', // or 'fetch_markets_from_api'
                'limits' => array(
                    'BTM/USDT' => array( 'amount' => array( 'min' => 0.1, 'max' => 10000000 )),
                    'ETC/USDT' => array( 'amount' => array( 'min' => 0.001, 'max' => 400000 )),
                    'ETH/USDT' => array( 'amount' => array( 'min' => 0.001, 'max' => 10000 )),
                    'LTC/USDT' => array( 'amount' => array( 'min' => 0.001, 'max' => 40000 )),
                    'BCH/USDT' => array( 'amount' => array( 'min' => 0.001, 'max' => 5000 )),
                    'BTC/USDT' => array( 'amount' => array( 'min' => 0.001, 'max' => 1000 )),
                    'ICX/ETH' => array( 'amount' => array( 'min' => 0.01, 'max' => 3000000 )),
                    'OMG/ETH' => array( 'amount' => array( 'min' => 0.01, 'max' => 500000 )),
                    'FT/USDT' => array( 'amount' => array( 'min' => 1, 'max' => 10000000 )),
                    'ZIL/ETH' => array( 'amount' => array( 'min' => 1, 'max' => 10000000 )),
                    'ZIP/ETH' => array( 'amount' => array( 'min' => 1, 'max' => 10000000 )),
                    'FT/BTC' => array( 'amount' => array( 'min' => 1, 'max' => 10000000 )),
                    'FT/ETH' => array( 'amount' => array( 'min' => 1, 'max' => 10000000 )),
                ),
            ),
            'exceptions' => array(
                '400' => '\\ccxt\\NotSupported', // Bad Request
                '401' => '\\ccxt\\AuthenticationError',
                '405' => '\\ccxt\\NotSupported',
                '429' => '\\ccxt\\RateLimitExceeded', // Too Many Requests, exceed api request limit
                '1002' => '\\ccxt\\ExchangeNotAvailable', // System busy
                '1016' => '\\ccxt\\InsufficientFunds',
                '2136' => '\\ccxt\\AuthenticationError', // The API key is expired
                '3008' => '\\ccxt\\InvalidOrder',
                '6004' => '\\ccxt\\InvalidNonce',
                '6005' => '\\ccxt\\AuthenticationError', // Illegal API Signature
                '40003' => '\\ccxt\\BadSymbol',
            ),
            'commonCurrencies' => array(
                'DAG' => 'DAGX',
                'PAI' => 'PCHAIN',
                'MT' => 'Mariana Token',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $method = $this->safe_string($this->options, 'fetchMarketsMethod', 'fetch_markets_from_open_api');
        return $this->$method ($params);
    }

    public function fetch_markets_from_open_api($params = array ()) {
        // https://github.com/ccxt/ccxt/issues/5648
        $response = $this->openapiGetSymbols ($params);
        //
        //     {
        //         "status":"ok",
        //         "$data":{
        //             "categories":array( "fone::coinforce", ... ),
        //             "symbols":{
        //                 "mdaeth":array(
        //                     "price_decimal":8,
        //                     "amount_decimal":2,
        //                     "base_currency":"mda",
        //                     "quote_currency":"eth",
        //                     "$symbol":"mdaeth",
        //                     "category":"fone::bitangel",
        //                     "leveraged_multiple":null,
        //                     "tradeable":false,
        //                     "market_order_enabled":false,
        //                     "limit_amount_min":"1",
        //                     "limit_amount_max":"10000000",
        //                     "main_tag":"",
        //                     "daily_open_at":"",
        //                     "daily_close_at":""
        //                 ),
        //             }
        //             "category_ref":{
        //                 "fone::coinforce":array( "btcusdt", ... ),
        //             }
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $markets = $this->safe_value($data, 'symbols', array());
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $market = $markets[$key];
            $id = $this->safe_string($market, 'symbol');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quote_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'price' => $this->safe_integer($market, 'price_decimal'),
                'amount' => $this->safe_integer($market, 'amount_decimal'),
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_number($market, 'limit_amount_min'),
                    'max' => $this->safe_number($market, 'limit_amount_max'),
                ),
                'price' => array(
                    'min' => pow(10, -$precision['price']),
                    'max' => pow(10, $precision['price']),
                ),
                'cost' => array(
                    'min' => null,
                    'max' => null,
                ),
            );
            $active = $this->safe_value($market, 'tradeable', false);
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_markets_from_api($params = array ()) {
        $response = $this->publicGetSymbols ($params);
        //
        //     {
        //         "status":0,
        //         "data":array(
        //             array(
        //                 "name":"dapusdt",
        //                 "base_currency":"dap",
        //                 "quote_currency":"usdt",
        //                 "price_decimal":6,
        //                 "amount_decimal":2,
        //                 "tradable":true
        //             ),
        //         )
        //     }
        //
        $result = array();
        $markets = $this->safe_value($response, 'data');
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'name');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quote_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'price' => $market['price_decimal'],
                'amount' => $market['amount_decimal'],
            );
            $limits = array(
                'price' => array(
                    'min' => pow(10, -$precision['price']),
                    'max' => pow(10, $precision['price']),
                ),
            );
            $active = $this->safe_value($market, 'tradable', false);
            if (is_array($this->options['limits']) && array_key_exists($symbol, $this->options['limits'])) {
                $limits = array_merge($this->options['limits'][$symbol], $limits);
            }
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccountsBalance ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'data');
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'available');
            $account['total'] = $this->safe_number($balance, 'balance');
            $account['used'] = $this->safe_number($balance, 'frozen');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_bids_asks($orders, $priceKey = 0, $amountKey = 1) {
        $result = array();
        $length = is_array($orders) ? count($orders) : 0;
        $halfLength = intval($length / 2);
        // += 2 in the for loop below won't transpile
        for ($i = 0; $i < $halfLength; $i++) {
            $index = $i * 2;
            $priceField = $this->sum($index, $priceKey);
            $amountField = $this->sum($index, $amountKey);
            $result[] = array(
                $this->safe_number($orders, $priceField),
                $this->safe_number($orders, $amountField),
            );
        }
        return $result;
    }

    public function fetch_order_book($symbol = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($limit !== null) {
            if (($limit === 20) || ($limit === 150)) {
                $limit = 'L' . (string) $limit;
            } else {
                throw new ExchangeError($this->id . ' fetchOrderBook supports $limit of 20 or 150. Other values are not accepted');
            }
        } else {
            $limit = 'L20';
        }
        $request = array(
            'symbol' => $this->market_id($symbol),
            'level' => $limit, // L20, L150
        );
        $response = $this->marketGetDepthLevelSymbol (array_merge($request, $params));
        $orderbook = $this->safe_value($response, 'data');
        return $this->parse_order_book($orderbook, $orderbook['ts'], 'bids', 'asks', 0, 1);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $ticker = $this->marketGetTickerSymbol (array_merge($request, $params));
        return $this->parse_ticker($ticker['data'], $market);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = null;
        $symbol = null;
        if ($market === null) {
            $tickerType = $this->safe_string($ticker, 'type');
            if ($tickerType !== null) {
                $parts = explode('.', $tickerType);
                $id = $parts[1];
                $symbol = $this->safe_symbol($id, $market);
            }
        }
        $values = $ticker['ticker'];
        $last = $this->safe_number($values, 0);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($values, 7),
            'low' => $this->safe_number($values, 8),
            'bid' => $this->safe_number($values, 2),
            'bidVolume' => $this->safe_number($values, 3),
            'ask' => $this->safe_number($values, 4),
            'askVolume' => $this->safe_number($values, 5),
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($values, 9),
            'quoteVolume' => $this->safe_number($values, 10),
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($trade, 'ts');
        $side = $this->safe_string_lower($trade, 'side');
        $id = $this->safe_string($trade, 'id');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        $fee = null;
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'order' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetServerTime ($params);
        //
        //     {
        //         "status" => 0,
        //         "data" => 1523430502977
        //     }
        //
        return $this->safe_integer($response, 'data');
    }

    public function fetch_trades($symbol, $since = null, $limit = 50, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'limit' => $limit,
        );
        if ($since !== null) {
            $request['timestamp'] = intval($since / 1000);
        }
        $response = $this->marketGetTradesSymbol (array_merge($request, $params));
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
            'side' => $side,
            'type' => $type,
        );
        // for market buy it requires the $amount of quote currency to spend
        if (($type === 'market') && ($side === 'buy')) {
            if ($this->options['createMarketBuyOrderRequiresPrice']) {
                if ($price === null) {
                    throw new InvalidOrder($this->id . " createOrder() requires the $price argument with market buy orders to calculate total order cost ($amount to spend), where cost = $amount * $price-> Supply a $price argument to createOrder() call if you want the cost to be calculated for you from $price and $amount, or, alternatively, add .options['createMarketBuyOrderRequiresPrice'] = false to supply the cost in the $amount argument (the exchange-specific behaviour)");
                } else {
                    $request['amount'] = $this->cost_to_precision($symbol, $amount * $price);
                }
            } else {
                $request['amount'] = $this->cost_to_precision($symbol, $amount);
            }
        } else {
            $request['amount'] = $this->amount_to_precision($symbol, $amount);
        }
        if (($type === 'limit') || ($type === 'ioc') || ($type === 'fok')) {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['data'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => $id,
        );
        $response = $this->privatePostOrdersOrderIdSubmitCancel (array_merge($request, $params));
        $order = $this->parse_order($response);
        return array_merge($order, array(
            'id' => $id,
            'status' => 'canceled',
        ));
    }

    public function parse_order_status($status) {
        $statuses = array(
            'submitted' => 'open',
            'canceled' => 'canceled',
            'partial_filled' => 'open',
            'partial_canceled' => 'canceled',
            'filled' => 'closed',
            'pending_cancel' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "$id" => "string",
        //         "$symbol" => "string",
        //         "type" => "limit",
        //         "$side" => "buy",
        //         "$price" => "string",
        //         "$amount" => "string",
        //         "state" => "submitted",
        //         "executed_value" => "string",
        //         "fill_fees" => "string",
        //         "filled_amount" => "string",
        //         "created_at" => 0,
        //         "source" => "web"
        //     }
        //
        $id = $this->safe_string($order, 'id');
        $side = $this->safe_string($order, 'side');
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $marketId = $this->safe_string($order, 'symbol');
        $market = $this->safe_market($marketId, $market);
        $symbol = $market['symbol'];
        $orderType = $this->safe_string($order, 'type');
        $timestamp = $this->safe_integer($order, 'created_at');
        $amount = $this->safe_number($order, 'amount');
        $filled = $this->safe_number($order, 'filled_amount');
        $price = $this->safe_number($order, 'price');
        $cost = $this->safe_number($order, 'executed_value');
        $feeCurrency = null;
        $feeCost = null;
        $feeRebate = $this->safe_number($order, 'fees_income');
        if (($feeRebate !== null) && ($feeRebate > 0)) {
            if ($market !== null) {
                $feeCurrency = ($side === 'buy') ? $market['quote'] : $market['base'];
            }
            $feeCost = -$feeRebate;
        } else {
            $feeCost = $this->safe_number($order, 'fill_fees');
            if ($market !== null) {
                $feeCurrency = ($side === 'buy') ? $market['base'] : $market['quote'];
            }
        }
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $orderType,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'remaining' => null,
            'filled' => $filled,
            'average' => null,
            'status' => $status,
            'fee' => array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            ),
            'trades' => null,
        ));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => $id,
        );
        $response = $this->privateGetOrdersOrderId (array_merge($request, $params));
        return $this->parse_order($response['data']);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array( 'states' => 'submitted,partial_filled' );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array( 'states' => 'partial_canceled,filled' );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders() requires a `$symbol` argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'states' => 'submitted,partial_filled,partial_canceled,filled,canceled',
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        return $this->parse_orders($response['data'], $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        return array(
            $this->safe_timestamp($ohlcv, 'id'),
            $this->safe_number($ohlcv, 'open'),
            $this->safe_number($ohlcv, 'high'),
            $this->safe_number($ohlcv, 'low'),
            $this->safe_number($ohlcv, 'close'),
            $this->safe_number($ohlcv, 'base_vol'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 20; // default is 20
        }
        $request = array(
            'symbol' => $market['id'],
            'timeframe' => $this->timeframes[$timeframe],
            'limit' => $limit,
        );
        if ($since !== null) {
            $sinceInSeconds = intval($since / 1000);
            $timerange = $limit * $this->parse_timeframe($timeframe);
            $request['before'] = $this->sum($sinceInSeconds, $timerange) - 1;
        }
        $response = $this->marketGetCandlesTimeframeSymbol (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/';
        $openAPI = ($api === 'openapi');
        $privateAPI = ($api === 'private');
        $request .= $openAPI ? ($api . '/') : '';
        $request .= $this->version . '/';
        $request .= ($privateAPI || $openAPI) ? '' : ($api . '/');
        $request .= $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->implode_params($this->urls['api'][$api], array(
            'hostname' => $this->hostname,
        ));
        $url .= $request;
        if ($privateAPI) {
            $this->check_required_credentials();
            $timestamp = (string) $this->nonce();
            $query = $this->keysort($query);
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->rawencode($query);
                }
            }
            // HTTP_METHOD . HTTP_REQUEST_URI . TIMESTAMP . POST_BODY
            $auth = $method . $url . $timestamp;
            if ($method === 'POST') {
                if ($query) {
                    $body = $this->json($query);
                    $auth .= $this->urlencode($query);
                }
            }
            $payload = base64_encode($auth);
            $signature = $this->hmac($payload, $this->encode($this->secret), 'sha1', 'binary');
            $signature = $this->decode(base64_encode($signature));
            $headers = array(
                'FC-ACCESS-KEY' => $this->apiKey,
                'FC-ACCESS-SIGNATURE' => $signature,
                'FC-ACCESS-TIMESTAMP' => $timestamp,
                'Content-Type' => 'application/json',
            );
        } else {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        $status = $this->safe_string($response, 'status');
        if ($status !== '0' && $status !== 'ok') {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions, $status, $feedback);
            throw new ExchangeError($feedback);
        }
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidAddress;

class gateio extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'gateio',
            'name' => 'Gate.io',
            'countries' => array( 'CN' ),
            'version' => '2',
            'rateLimit' => 1000,
            'pro' => true,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createDepositAddress' => true,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => false,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchOrderTrades' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => 60,
                '5m' => 300,
                '10m' => 600,
                '15m' => 900,
                '30m' => 1800,
                '1h' => 3600,
                '2h' => 7200,
                '4h' => 14400,
                '6h' => 21600,
                '12h' => 43200,
                '1d' => 86400,
                '1w' => 604800,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/31784029-0313c702-b509-11e7-9ccc-bc0da6a0e435.jpg',
                'api' => array(
                    'public' => 'https://data.gate.io/api',
                    'private' => 'https://data.gate.io/api',
                ),
                'www' => 'https://gate.io/',
                'doc' => 'https://gate.io/api2',
                'fees' => array(
                    'https://gate.io/fee',
                    'https://support.gate.io/hc/en-us/articles/115003577673',
                ),
                'referral' => 'https://www.gate.io/signup/2436035',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'candlestick2/{id}',
                        'pairs',
                        'coininfo',
                        'marketinfo',
                        'marketlist',
                        'coininfo',
                        'tickers',
                        'ticker/{id}',
                        'orderBook/{id}',
                        'trade/{id}',
                        'tradeHistory/{id}',
                        'tradeHistory/{id}/{tid}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'balances',
                        'depositAddress',
                        'newAddress',
                        'depositsWithdrawals',
                        'buy',
                        'sell',
                        'cancelOrder',
                        'cancelAllOrders',
                        'getOrder',
                        'openOrders',
                        'tradeHistory',
                        'feelist',
                        'withdraw',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'maker' => 0.002,
                    'taker' => 0.002,
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    '4' => '\\ccxt\\DDoSProtection',
                    '5' => '\\ccxt\\AuthenticationError', // array( result => "false", code =>  5, message => "Error => invalid key or sign, please re-generate it from your account" )
                    '6' => '\\ccxt\\AuthenticationError', // array( result => 'false', code => 6, message => 'Error => invalid data  ' )
                    '7' => '\\ccxt\\NotSupported',
                    '8' => '\\ccxt\\NotSupported',
                    '9' => '\\ccxt\\NotSupported',
                    '15' => '\\ccxt\\DDoSProtection',
                    '16' => '\\ccxt\\OrderNotFound',
                    '17' => '\\ccxt\\OrderNotFound',
                    '20' => '\\ccxt\\InvalidOrder',
                    '21' => '\\ccxt\\InsufficientFunds',
                ),
                // https://gate.io/api2#errCode
                'errorCodeNames' => array(
                    '1' => 'Invalid request',
                    '2' => 'Invalid version',
                    '3' => 'Invalid request',
                    '4' => 'Too many attempts',
                    '5' => 'Invalid sign',
                    '6' => 'Invalid sign',
                    '7' => 'Currency is not supported',
                    '8' => 'Currency is not supported',
                    '9' => 'Currency is not supported',
                    '10' => 'Verified failed',
                    '11' => 'Obtaining address failed',
                    '12' => 'Empty params',
                    '13' => 'Internal error, please report to administrator',
                    '14' => 'Invalid user',
                    '15' => 'Cancel order too fast, please wait 1 min and try again',
                    '16' => 'Invalid order id or order is already closed',
                    '17' => 'Invalid orderid',
                    '18' => 'Invalid amount',
                    '19' => 'Not permitted or trade is disabled',
                    '20' => 'Your order size is too small',
                    '21' => 'You don\'t have enough fund',
                ),
            ),
            'options' => array(
                'limits' => array(
                    'cost' => array(
                        'min' => array(
                            'BTC' => 0.0001,
                            'ETH' => 0.001,
                            'USDT' => 1,
                        ),
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'BOX' => 'DefiBox',
                'BTCBEAR' => 'BEAR',
                'BTCBULL' => 'BULL',
                'SBTC' => 'Super Bitcoin',
                'TNC' => 'Trinity Network Credit',
            ),
        ));
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCoininfo ($params);
        //
        //     {
        //         "$result":"true",
        //         "$coins":array(
        //             {
        //                 "CNYX":array(
        //                     "$delisted":0,
        //                     "withdraw_disabled":1,
        //                     "withdraw_delayed":0,
        //                     "deposit_disabled":0,
        //                     "trade_disabled":0
        //                 }
        //             ),
        //             {
        //                 "USDT_ETH":{
        //                     "$delisted":0,
        //                     "withdraw_disabled":1,
        //                     "withdraw_delayed":0,
        //                     "deposit_disabled":0,
        //                     "trade_disabled":1
        //                 }
        //             }
        //         )
        //     }
        //
        $coins = $this->safe_value($response, 'coins');
        if (!$coins) {
            throw new ExchangeError($this->id . ' fetchCurrencies got an unrecognized response');
        }
        $result = array();
        for ($i = 0; $i < count($coins); $i++) {
            $coin = $coins[$i];
            $ids = is_array($coin) ? array_keys($coin) : array();
            for ($j = 0; $j < count($ids); $j++) {
                $id = $ids[$j];
                $currency = $coin[$id];
                $code = $this->safe_currency_code($id);
                $delisted = $this->safe_value($currency, 'delisted', 0);
                $withdrawDisabled = $this->safe_value($currency, 'withdraw_disabled', 0);
                $depositDisabled = $this->safe_value($currency, 'deposit_disabled', 0);
                $tradeDisabled = $this->safe_value($currency, 'trade_disabled', 0);
                $listed = ($delisted === 0);
                $withdrawEnabled = ($withdrawDisabled === 0);
                $depositEnabled = ($depositDisabled === 0);
                $tradeEnabled = ($tradeDisabled === 0);
                $active = $listed && $withdrawEnabled && $depositEnabled && $tradeEnabled;
                $result[$code] = array(
                    'id' => $id,
                    'code' => $code,
                    'active' => $active,
                    'info' => $currency,
                    'name' => null,
                    'fee' => null,
                    'precision' => null,
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
                        'withdraw' => array(
                            'min' => null,
                            'max' => null,
                        ),
                    ),
                );
            }
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetMarketinfo ($params);
        //
        //     {
        //         "$result":"true",
        //         "pairs":array(
        //             {
        //                 "usdt_cnyx":array(
        //                     "decimal_places":3,
        //                     "amount_decimal_places":3,
        //                     "min_amount":1,
        //                     "min_amount_a":1,
        //                     "min_amount_b":3,
        //                     "$fee":0.02,
        //                     "trade_disabled":0,
        //                     "buy_disabled":0,
        //                     "sell_disabled":0
        //                 }
        //             ),
        //         )
        //     }
        //
        $markets = $this->safe_value($response, 'pairs');
        if (!$markets) {
            throw new ExchangeError($this->id . ' fetchMarkets got an unrecognized response');
        }
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $keys = is_array($market) ? array_keys($market) : array();
            $id = $this->safe_string($keys, 0);
            $details = $market[$id];
            // all of their symbols are separated with an underscore
            // but not boe_eth_eth (BOE_ETH/ETH) which has two underscores
            // https://github.com/ccxt/ccxt/issues/4894
            $parts = explode('_', $id);
            $numParts = is_array($parts) ? count($parts) : 0;
            $baseId = $parts[0];
            $quoteId = $parts[1];
            if ($numParts > 2) {
                $baseId = $parts[0] . '_' . $parts[1];
                $quoteId = $parts[2];
            }
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($details, 'amount_decimal_places'),
                'price' => $this->safe_integer($details, 'decimal_places'),
            );
            $amountLimits = array(
                'min' => $this->safe_float($details, 'min_amount'),
                'max' => null,
            );
            $priceLimits = array(
                'min' => pow(10, -$precision['price']),
                'max' => null,
            );
            $defaultCost = $amountLimits['min'] * $priceLimits['min'];
            $minCost = $this->safe_float($this->options['limits']['cost']['min'], $quote, $defaultCost);
            $costLimits = array(
                'min' => $minCost,
                'max' => null,
            );
            $limits = array(
                'amount' => $amountLimits,
                'price' => $priceLimits,
                'cost' => $costLimits,
            );
            $disabled = $this->safe_value($details, 'trade_disabled');
            $active = !$disabled;
            $uppercaseId = strtoupper($id);
            $fee = $this->safe_float($details, 'fee');
            $result[] = array(
                'id' => $id,
                'uppercaseId' => $uppercaseId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => $active,
                'maker' => $fee / 100,
                'taker' => $fee / 100,
                'precision' => $precision,
                'limits' => $limits,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalances ($params);
        $result = array( 'info' => $response );
        $available = $this->safe_value($response, 'available', array());
        if (gettype($available) === 'array' && count(array_filter(array_keys($available), 'is_string')) == 0) {
            $available = array();
        }
        $locked = $this->safe_value($response, 'locked', array());
        $currencyIds = is_array($available) ? array_keys($available) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($available, $currencyId);
            $account['used'] = $this->safe_float($locked, $currencyId);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $response = $this->publicGetOrderBookId (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        // they return array( Timestamp, Volume, Close, High, Low, Open )
        return array(
            $this->safe_integer($ohlcv, 0), // t
            $this->safe_float($ohlcv, 5), // o
            $this->safe_float($ohlcv, 3), // h
            $this->safe_float($ohlcv, 4), // l
            $this->safe_float($ohlcv, 2), // c
            $this->safe_float($ohlcv, 1), // v
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
            'group_sec' => $this->timeframes[$timeframe],
        );
        // max $limit = 1001
        if ($limit !== null) {
            $periodDurationInSeconds = $this->parse_timeframe($timeframe);
            $hours = intval(($periodDurationInSeconds * $limit) / 3600);
            $request['range_hour'] = max (0, $hours - 1);
        }
        $response = $this->publicGetCandlestick2Id (array_merge($request, $params));
        //
        //     {
        //         "elapsed" => "15ms",
        //         "result" => "true",
        //         "$data" => array(
        //             array( "1553930820000", "1.005299", "4081.05", "4086.18", "4081.05", "4086.18" ),
        //             array( "1553930880000", "0.110923277", "4095.2", "4095.23", "4091.15", "4091.15" ),
        //             ...
        //             array( "1553934420000", "0", "4089.42", "4089.42", "4089.42", "4089.42" ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'last');
        $percentage = $this->safe_float($ticker, 'percentChange');
        $open = null;
        $change = null;
        $average = null;
        if (($last !== null) && ($percentage !== null)) {
            $relativeChange = $percentage / 100;
            $open = $last / $this->sum(1, $relativeChange);
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
        }
        $open = $this->safe_float($ticker, 'open', $open);
        $change = $this->safe_float($ticker, 'change', $change);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float_2($ticker, 'high24hr', 'high'),
            'low' => $this->safe_float_2($ticker, 'low24hr', 'low'),
            'bid' => $this->safe_float($ticker, 'highestBid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'lowestAsk'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'quoteVolume'), // gateio has them reversed
            'quoteVolume' => $this->safe_float($ticker, 'baseVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        $result = array();
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $this->safe_market($id, null, '_');
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($response[$id], $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $ticker = $this->publicGetTickerId (array_merge(array(
            'id' => $market['id'],
        ), $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade($trade, $market = null) {
        // array(
        //     "tradeID" => 3175762,
        //     "date" => "2017-08-25 07:24:28",
        //     "$type" => "sell",
        //     "rate" => 29011,
        //     "$amount" => 0.0019,
        //     "total" => 55.1209,
        //     "$fee" => "0",
        //     "fee_coin" => "btc",
        //     "gt_fee":"0",
        //     "point_fee":"0.1213",
        // ),
        $timestamp = $this->safe_timestamp_2($trade, 'timestamp', 'time_unix');
        $timestamp = $this->safe_timestamp($trade, 'time', $timestamp);
        $id = $this->safe_string_2($trade, 'tradeID', 'id');
        // take either of orderid or $orderId
        $orderId = $this->safe_string_2($trade, 'orderid', 'orderNumber');
        $price = $this->safe_float_2($trade, 'rate', 'price');
        $amount = $this->safe_float($trade, 'amount');
        $type = $this->safe_string($trade, 'type');
        $takerOrMaker = $this->safe_string($trade, 'role');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $fee = null;
        $feeCurrency = $this->safe_currency_code($this->safe_string($trade, 'fee_coin'));
        $feeCost = $this->safe_float($trade, 'point_fee');
        if (($feeCost === null) || ($feeCost === 0)) {
            $feeCost = $this->safe_float($trade, 'gt_fee');
            if (($feeCost === null) || ($feeCost === 0)) {
                $feeCost = $this->safe_float($trade, 'fee');
            } else {
                $feeCurrency = $this->safe_currency_code('GT');
            }
        } else {
            $feeCurrency = $this->safe_currency_code('POINT');
        }
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => null,
            'side' => $type,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        $method = null;
        if (is_array($params) && array_key_exists('tid', $params)) {
            $method = 'publicGetTradeHistoryIdTid';
        } else {
            $method = 'publicGetTradeHistoryId';
        }
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->privatePostOpenOrders ($params);
        return $this->parse_orders($response['orders'], null, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderNumber' => $id,
            'currencyPair' => $this->market_id($symbol),
        );
        $response = $this->privatePostGetOrder (array_merge($request, $params));
        return $this->parse_order($response['order']);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'cancelled' => 'canceled',
            // 'closed' => 'closed', // these two $statuses aren't actually needed
            // 'open' => 'open', // as they are mapped one-to-one
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //        "fee" => "0 ZEC",
        //         "code" => 0,
        //         "rate" => "0.0055",
        //         "$side" => 2,
        //         "type" => "buy",
        //         "ctime" => 1586460839.138,
        //         "$market" => "ZEC_BTC",
        //         "result" => "true",
        //         "$status" => "open",
        //         "iceberg" => "0",
        //         "message" => "Success",
        //         "feeValue" => "0",
        //         "filledRate" => "0.005500000",
        //         "leftAmount" => "0.60607456",
        //         "feeCurrency" => "ZEC",
        //         "orderNumber" => 10755887009,
        //         "filledAmount" => "0",
        //         "feePercentage" => 0.002,
        //         "initialAmount" => "0.60607456"
        //     }
        //
        //     {
        //         'amount' => '0.00000000',
        //         'currencyPair' => 'xlm_usdt',
        //         'fee' => '0.0113766632239302 USDT',
        //         'feeCurrency' => 'USDT',
        //         'feePercentage' => 0.18,
        //         'feeValue' => '0.0113766632239302',
        //         'filledAmount' => '30.14004987',
        //         'filledRate' => 0.2097,
        //         'initialAmount' => '30.14004987',
        //         'initialRate' => '0.2097',
        //         'left' => 0,
        //         'orderNumber' => '998307286',
        //         'rate' => '0.2097',
        //         'status' => 'closed',
        //         'timestamp' => 1531158583,
        //         'type' => 'sell'
        //     }
        //
        //     {
        //         "orderNumber" => 10802237760,
        //         "orderType" => 1,
        //         "type" => "buy",
        //         "rate" => "0.54250000",
        //         "$amount" => "45.55638518",
        //         "total" => "24.71433896",
        //         "initialRate" => "0.54250000",
        //         "initialAmount" => "45.55638518",
        //         "filledRate" => "0.54250000",
        //         "filledAmount" => "0",
        //         "currencyPair" => "nano_usdt",
        //         "$timestamp" => 1586556143,
        //         "$status" => "open"
        //     }
        //
        $id = $this->safe_string_2($order, 'orderNumber', 'id');
        $marketId = $this->safe_string($order, 'currencyPair');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $timestamp = $this->safe_timestamp_2($order, 'timestamp', 'ctime');
        $lastTradeTimestamp = $this->safe_timestamp($order, 'mtime');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $side = $this->safe_string($order, 'type');
        // handling for $order->update messages
        if ($side === '1') {
            $side = 'sell';
        } else if ($side === '2') {
            $side = 'buy';
        }
        $price = $this->safe_float_2($order, 'initialRate', 'rate');
        $average = $this->safe_float($order, 'filledRate');
        $amount = $this->safe_float_2($order, 'initialAmount', 'amount');
        $filled = $this->safe_float($order, 'filledAmount');
        // In the $order $status response, this field has a different name.
        $remaining = $this->safe_float_2($order, 'leftAmount', 'left');
        $feeCost = $this->safe_float($order, 'feeValue');
        $feeCurrencyId = $this->safe_string($order, 'feeCurrency');
        $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
        $feeRate = $this->safe_float($order, 'feePercentage');
        if ($feeRate !== null) {
            $feeRate = $feeRate / 100;
        }
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'average' => $average,
            'trades' => null,
            'fee' => array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
                'rate' => $feeRate,
            ),
            'info' => $order,
        ));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $method = 'privatePost' . $this->capitalize($side);
        $market = $this->market($symbol);
        $request = array(
            'currencyPair' => $market['id'],
            'rate' => $price,
            'amount' => $amount,
        );
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_order(array_merge(array(
            'status' => 'open',
            'type' => $side,
            'initialAmount' => $amount,
        ), $response), $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires $symbol argument');
        }
        $this->load_markets();
        $request = array(
            'orderNumber' => $id,
            'currencyPair' => $this->market_id($symbol),
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function query_deposit_address($method, $code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $method = 'privatePost' . $method . 'Address';
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->$method (array_merge($request, $params));
        $address = $this->safe_string($response, 'addr');
        $tag = null;
        if (($address !== null) && (mb_strpos($address, 'address') !== false)) {
            throw new InvalidAddress($this->id . ' queryDepositAddress ' . $address);
        }
        if (($code === 'XRP') || ($code === 'HBAR') || ($code === 'STEEM') || ($code === 'XLM') || ($code === 'EOS')) {
            $parts = explode(' ', $address);
            $address = $parts[0];
            $tag = $parts[1];
        }
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function create_deposit_address($code, $params = array ()) {
        return $this->query_deposit_address('New', $code, $params);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        return $this->query_deposit_address('Deposit', $code, $params);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $response = $this->privatePostOpenOrders ($params);
        return $this->parse_orders($response['orders'], $market, $since, $limit);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrderTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currencyPair' => $market['id'],
            'orderNumber' => $id,
        );
        $response = $this->privatePostTradeHistory (array_merge($request, $params));
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currencyPair' => $market['id'],
        );
        $response = $this->privatePostTradeHistory (array_merge($request, $params));
        return $this->parse_trades($response['trades'], $market, $since, $limit);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address, // Address must exist in you AddressBook in security settings
        );
        if ($tag !== null) {
            $request['address'] .= ' ' . $tag;
        }
        $response = $this->privatePostWithdraw (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => null,
        );
    }

    public function fetch_transactions_by_type($type = null, $code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($since !== null) {
            $request['start'] = $since;
        }
        $response = $this->privatePostDepositsWithdrawals (array_merge($request, $params));
        $transactions = null;
        if ($type === null) {
            $deposits = $this->safe_value($response, 'deposits', array());
            $withdrawals = $this->safe_value($response, 'withdraws', array());
            $transactions = $this->array_concat($deposits, $withdrawals);
        } else {
            $transactions = $this->safe_value($response, $type, array());
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        return $this->parse_transactions($transactions, $currency, $since, $limit);
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type(null, $code, $since, $limit, $params);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type('deposits', $code, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type('withdraws', $code, $since, $limit, $params);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // deposit
        //
        //     {
        //         'id' => 'd16520849',
        //         'currency' => 'NEO',
        //         'address' => False,
        //         'amount' => '1',
        //         'txid' => '01acf6b8ce4d24a....',
        //         'timestamp' => '1553125968',
        //         'status' => 'DONE',
        //         'type' => 'deposit'
        //     }
        //
        // withdrawal
        //
        //     {
        //         "$id" => "w6754336",
        //         "$fee" => "0.1",
        //         "$txid" => "zzyy",
        //         "$amount" => "1",
        //         "$status" => "DONE",
        //         "$address" => "tz11234",
        //         "$currency" => "XTZ",
        //         "$timestamp" => "1561030206"
        //    }
        //
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $id = $this->safe_string($transaction, 'id');
        $txid = $this->safe_string($transaction, 'txid');
        $amount = $this->safe_float($transaction, 'amount');
        $address = $this->safe_string($transaction, 'address');
        if ($address === 'false') {
            $address = null;
        }
        $timestamp = $this->safe_timestamp($transaction, 'timestamp');
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $type = $this->parse_transaction_type($id[0]);
        $feeCost = $this->safe_float($transaction, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $fee = array(
                'currency' => $code,
                'cost' => $feeCost,
            );
            if ($amount !== null) {
                $amount = $amount - $feeCost;
            }
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => null,
            'status' => $status,
            'type' => $type,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => $fee,
        );
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'PEND' => 'pending',
            'REQUEST' => 'pending',
            'DMOVE' => 'pending',
            'CANCEL' => 'failed',
            'DONE' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction_type($type) {
        $types = array(
            'd' => 'deposit',
            'w' => 'withdrawal',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        $resultString = $this->safe_string($response, 'result', '');
        if ($resultString !== 'false') {
            return;
        }
        $errorCode = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message', $body);
        if ($errorCode !== null) {
            $feedback = $this->safe_string($this->exceptions['errorCodeNames'], $errorCode, $message);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
        }
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $prefix = ($api === 'private') ? ($api . '/') : '';
        $url = $this->urls['api'][$api] . $this->version . '/1/' . $prefix . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $request = array( 'nonce' => $nonce );
            $body = $this->rawencode(array_merge($request, $query));
            // gateio does not like the plus sign in the URL $query
            // https://github.com/ccxt/ccxt/issues/4529
            $body = str_replace('+', ' ', $body);
            $signature = $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512');
            $headers = array(
                'Key' => $this->apiKey,
                'Sign' => $signature,
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

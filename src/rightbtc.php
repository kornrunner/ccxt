<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\OrderNotFound;

class rightbtc extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'rightbtc',
            'name' => 'RightBTC',
            'countries' => array( 'AE' ),
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'privateAPI' => false,
                'fetchBalance' => true,
                'fetchClosedOrders' => false,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => 'emulated',
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'timeframes' => array(
                '1m' => 'min1',
                '5m' => 'min5',
                '15m' => 'min15',
                '30m' => 'min30',
                '1h' => 'hr1',
                '1d' => 'day1',
                '1w' => 'week',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87182092-1f372700-c2ec-11ea-8f9e-01b4d3ff8941.jpg',
                'api' => 'https://www.rightbtc.com/api',
                'www' => 'https://www.rightbtc.com',
                'doc' => array(
                    'https://docs.rightbtc.com/api/',
                ),
                // eslint-disable-next-line no-useless-escape
                // 'fees' => 'https://www.rightbtc.com/\#\!/support/fee',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        // 'getAssetsTradingPairs/zh', // 404
                        'trading_pairs',
                        'ticker/{trading_pair}',
                        'tickers',
                        'depth/{trading_pair}',
                        'depth/{trading_pair}/{count}',
                        'trades/{trading_pair}',
                        'trades/{trading_pair}/{count}',
                        'candlestick/latest/{trading_pair}',
                        'candlestick/{timeSymbol}/{trading_pair}',
                        'candlestick/{timeSymbol}/{trading_pair}/{count}',
                    ),
                ),
                'trader' => array(
                    'get' => array(
                        'balance/{symbol}',
                        'balances',
                        'deposits/{asset}/{page}',
                        'withdrawals/{asset}/{page}',
                        'orderpage/{trading_pair}/{cursor}',
                        'orders/{trading_pair}/{ids}', // ids are a slash-separated list of {id}/{id}/{id}/...
                        'history/{trading_pair}/{ids}',
                        'historys/{trading_pair}/{page}',
                        'trading_pairs',
                    ),
                    'post' => array(
                        'order',
                    ),
                    'delete' => array(
                        'order/{trading_pair}/{ids}',
                    ),
                ),
            ),
            // HARDCODING IS DEPRECATED, THE FEES BELOW SHOULD BE REWRITTEN
            'fees' => array(
                'trading' => array(
                    // min trading fees
                    // 0.0001 BTC
                    // 0.01 ETP
                    // 0.001 ETH
                    // 0.1 BITCNY
                    'maker' => 0.1 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        // 'BTM' => n => 3 . n * (1 / 100),
                        // 'ZDC' => n => 1 . n * (0.5 / 100),
                        // 'ZGC' => n => 0.5 . n * (0.5 / 100),
                        // 'BTS' => n => 1 . n * (1 / 100),
                        // 'DLT' => n => 3 . n * (1 / 100),
                        // 'SNT' => n => 10 . n * (1 / 100),
                        // 'XNC' => n => 1 . n * (1 / 100),
                        // 'ICO' => n => 3 . n * (1 / 100),
                        // 'CMC' => n => 1 . n * (0.5 / 100),
                        // 'GXS' => n => 0.2 . n * (1 / 100),
                        // 'OBITS' => n => 0.3 . n * (1 / 100),
                        // 'ICS' => n => 2 . n * (1 / 100),
                        // 'TIC' => n => 2 . n * (1 / 100),
                        // 'IND' => n => 20 . n * (1 / 100),
                        // 'MVC' => n => 20 . n * (1 / 100),
                        // 'BitCNY' => n => 0.1 . n * (1 / 100),
                        // 'MTX' => n => 1 . n * (1 / 100),
                        'ETP' => 0.01,
                        'BTC' => 0.0005,
                        'ETH' => 0.005,
                        'ETC' => 0.01,
                        'STORJ' => 3,
                        'LTC' => 0.01,
                        'ZEC' => 0.001,
                        'BCC' => 0.001,
                        'XRB' => 0,
                        'NXS' => 0.1,
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'XRB' => 'NANO',
            ),
            'exceptions' => array(
                'ERR_USERTOKEN_NOT_FOUND' => '\\ccxt\\AuthenticationError',
                'ERR_ASSET_NOT_EXISTS' => '\\ccxt\\ExchangeError',
                'ERR_ASSET_NOT_AVAILABLE' => '\\ccxt\\ExchangeError',
                'ERR_BALANCE_NOT_ENOUGH' => '\\ccxt\\InsufficientFunds',
                'ERR_CREATE_ORDER' => '\\ccxt\\InvalidOrder',
                'ERR_CANDLESTICK_DATA' => '\\ccxt\\ExchangeError',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        // $zh = $this->publicGetGetAssetsTradingPairsZh ();
        $markets = $this->publicGetTradingPairs ($params);
        $marketIds = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $id = $marketIds[$i];
            $market = $markets[$id];
            $baseId = $this->safe_string($market, 'bid_asset_symbol');
            $quoteId = $this->safe_string($market, 'ask_asset_symbol');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'bid_asset_decimals'),
                'price' => $this->safe_integer($market, 'ask_asset_decimals'),
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => pow(10, $precision['price']),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => pow(10, $precision['price']),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function divide_safe_float($x, $key, $divisor) {
        $value = $this->safe_float($x, $key);
        if ($value !== null) {
            return $value / $divisor;
        }
        return $value;
    }

    public function parse_ticker($ticker, $market = null) {
        $symbol = $market['symbol'];
        $timestamp = $this->safe_integer($ticker, 'date');
        $last = $this->divide_safe_float($ticker, 'last', 1e8);
        $high = $this->divide_safe_float($ticker, 'high', 1e8);
        $low = $this->divide_safe_float($ticker, 'low', 1e8);
        $bid = $this->divide_safe_float($ticker, 'buy', 1e8);
        $ask = $this->divide_safe_float($ticker, 'sell', 1e8);
        $baseVolume = $this->divide_safe_float($ticker, 'vol24h', 1e8);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $high,
            'low' => $low,
            'bid' => $bid,
            'bidVolume' => null,
            'ask' => $ask,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
        );
        $response = $this->publicGetTickerTradingPair (array_merge($request, $params));
        $result = $this->safe_value($response, 'result');
        if ($result === null) {
            throw new ExchangeError($this->id . ' fetchTicker returned an empty $response for $symbol ' . $symbol);
        }
        return $this->parse_ticker($result, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        $tickers = $response['result'];
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $tickers[$i];
            $id = $ticker['market'];
            if (!(is_array($this->marketsById) && array_key_exists($id, $this->marketsById))) {
                continue;
            }
            $market = $this->marketsById[$id];
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'trading_pair' => $this->market_id($symbol),
        );
        $method = 'publicGetDepthTradingPair';
        if ($limit !== null) {
            $method .= 'Count';
            $request['count'] = $limit;
        }
        $response = $this->$method (array_merge($request, $params));
        $bidsasks = array();
        $types = ['bid', 'ask'];
        for ($ti = 0; $ti < count($types); $ti++) {
            $type = $types[$ti];
            $bidsasks[$type] = array();
            for ($i = 0; $i < count($response['result'][$type]); $i++) {
                list($price, $amount, $total) = $response['result'][$type][$i];
                $bidsasks[$type][] = array(
                    $price / 1e8,
                    $amount / 1e8,
                    $total / 1e8,
                );
            }
        }
        return $this->parse_order_book($bidsasks, null, 'bid', 'ask');
    }

    public function parse_trade($trade, $market = null) {
        //
        //     {
        //         "order_id" => 118735,
        //         "trade_id" => 7,
        //         "trading_pair" => "BTCCNY",
        //         "$side" => "B",
        //         "quantity" => 1000000000,
        //         "$price" => 900000000,
        //         "created_at" => "2017-06-06T20:45:27.000Z"
        //     }
        //
        $timestamp = $this->safe_integer($trade, 'date');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        }
        $id = $this->safe_string($trade, 'tid');
        $id = $this->safe_string($trade, 'trade_id', $id);
        $orderId = $this->safe_string($trade, 'order_id');
        $price = $this->divide_safe_float($trade, 'price', 1e8);
        $amount = $this->safe_float($trade, 'amount');
        $amount = $this->safe_float($trade, 'quantity', $amount);
        if ($amount !== null) {
            $amount = $amount / 1e8;
        }
        $marketId = $this->safe_string($trade, 'trading_pair');
        $symbol = $this->safe_symbol($marketId, $market);
        $cost = $this->cost_to_precision($symbol, $price * $amount);
        $cost = floatval($cost);
        $side = $this->safe_string_lower($trade, 'side');
        if ($side === 'b') {
            $side = 'buy';
        } else if ($side === 's') {
            $side = 'sell';
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => 'limit',
            'side' => $side,
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
            'trading_pair' => $market['id'],
        );
        $response = $this->publicGetTradesTradingPair (array_merge($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        return [
            $this->safe_integer($ohlcv, 0),
            floatval($ohlcv[2]) / 1e8,
            floatval($ohlcv[3]) / 1e8,
            floatval($ohlcv[4]) / 1e8,
            floatval($ohlcv[5]) / 1e8,
            floatval($ohlcv[1]) / 1e8,
        ];
    }

    public function fetch_ohlcv($symbol, $timeframe = '5m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
            'timeSymbol' => $this->timeframes[$timeframe],
        );
        $response = $this->publicGetCandlestickTimeSymbolTradingPair (array_merge($request, $params));
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_ohlcvs($result, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->traderGetBalances ($params);
        //
        //     {
        //         "status" => array(
        //             "success" => 1,
        //             "message" => "GET_BALANCES"
        //         ),
        //         "$result" => array(
        //             array(
        //                 "asset" => "ETP",
        //                 "$balance" => "5000000000000",
        //                 "frozen" => "0",
        //                 "state" => "1"
        //             ),
        //             {
        //                 "asset" => "CNY",
        //                 "$balance" => "10000000000000",
        //                 "frozen" => "240790000",
        //                 "state" => "1"
        //             }
        //         )
        //     }
        //
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'result', array());
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'asset');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            // https://github.com/ccxt/ccxt/issues/3873
            $account['free'] = $this->divide_safe_float($balance, 'balance', 1e8);
            $account['used'] = $this->divide_safe_float($balance, 'frozen', 1e8);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $order = array(
            'trading_pair' => $market['id'],
            // We need to use decimalToPrecision here, since
            //   0.036*1e8 === 3599999.9999999995
            // which would get truncated to 3599999 after intval            // which would then be rejected by rightBtc because it's too precise
            'quantity' => intval($this->decimal_to_precision($amount * 1e8, ROUND, 0, $this->precisionMode)),
            'limit' => intval($this->decimal_to_precision($price * 1e8, ROUND, 0, $this->precisionMode)),
            'type' => strtoupper($type),
            'side' => strtoupper($side),
        );
        $response = $this->traderPostOrder (array_merge($order, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
            'ids' => $id,
        );
        $response = $this->traderDeleteOrderTradingPairIds (array_merge($request, $params));
        return $response;
    }

    public function parse_order_status($status) {
        $statuses = array(
            'NEW' => 'open',
            'TRADE' => 'closed', // TRADE means filled or partially filled orders
            'CANCEL' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOrder / fetchOpenOrders
        //
        //     {
        //         "$id" => 4180528,
        //         "quantity" => 20000000,
        //         "rest" => 20000000,
        //         "limit" => 1000000,
        //         "$price" => null,
        //         "$side" => "BUY",
        //         "created" => 1496005693738
        //     }
        //
        // fetchOrders
        //
        //     {
        //         "trading_pair" => "ETPCNY",
        //         "$status" => "TRADE",
        //         "$fee" => 0.23,
        //         "min_fee" => 10000000,
        //         "created_at" => "2017-05-25T00:12:27.000Z",
        //         "$cost" => 1152468000000,
        //         "limit" => 3600000000,
        //         "$id" => 11060,
        //         "quantity" => 32013000000,
        //         "filled_quantity" => 32013000000
        //     }
        //
        $id = $this->safe_string($order, 'id');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'trading_pair');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->safe_integer($order, 'created');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        }
        if (is_array($order) && array_key_exists('time', $order)) {
            $timestamp = $order['time'];
        } else if (is_array($order) && array_key_exists('transactTime', $order)) {
            $timestamp = $order['transactTime'];
        }
        $price = $this->safe_float_2($order, 'limit', 'price');
        if ($price !== null) {
            $price = $price / 1e8;
        }
        $amount = $this->divide_safe_float($order, 'quantity', 1e8);
        $filled = $this->divide_safe_float($order, 'filled_quantity', 1e8);
        $remaining = $this->divide_safe_float($order, 'rest', 1e8);
        $cost = $this->divide_safe_float($order, 'cost', 1e8);
        // lines 483-494 should be generalized into a base class method
        if ($amount !== null) {
            if ($remaining === null) {
                if ($filled !== null) {
                    $remaining = max (0, $amount - $filled);
                }
            }
            if ($filled === null) {
                if ($remaining !== null) {
                    $filled = max (0, $amount - $remaining);
                }
            }
        }
        $type = 'limit';
        $side = $this->safe_string_lower($order, 'side');
        $feeCost = $this->divide_safe_float($order, 'min_fee', 1e8);
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrency = null;
            if ($market !== null) {
                $feeCurrency = $market['quote'];
            }
            $fee = array(
                'rate' => $this->safe_float($order, 'fee'),
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        $trades = null;
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'cost' => $cost,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
            'average' => null,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
            'ids' => $id,
        );
        $response = $this->traderGetOrdersTradingPairIds (array_merge($request, $params));
        //
        // $response = {
        //         "status" => array(
        //             "success" => 1,
        //             "message" => "SUC_LIST_AVTICE_ORDERS"
        //         ),
        //         "result" => array(
        //             {
        //                 "$id" => 4180528,
        //                 "quantity" => 20000000,
        //                 "rest" => 20000000,
        //                 "limit" => 1000000,
        //                 "price" => null,
        //                 "side" => "BUY",
        //                 "created" => 1496005693738
        //             }
        //         )
        //     }
        //
        $orders = $this->parse_orders($response['result'], $market);
        $ordersById = $this->index_by($orders, 'id');
        if (!(is_array($ordersById) && array_key_exists($id, $ordersById))) {
            throw new OrderNotFound($this->id . ' fetchOrder could not find order ' . (string) $id . ' in open $orders->');
        }
        return $ordersById[$id];
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
            'cursor' => 0,
        );
        $response = $this->traderGetOrderpageTradingPairCursor (array_merge($request, $params));
        //
        // $response = {
        //         "status" => array(
        //             "success" => 1,
        //             "message" => "SUC_LIST_AVTICE_ORDERS_PAGE"
        //         ),
        //         "result" => {
        //             "cursor" => "0",
        //             "orders" => array(
        //                 {
        //                     "id" => 4180528,
        //                     "quantity" => 20000000,
        //                     "rest" => 20000000,
        //                     "$limit" => 1000000,
        //                     "price" => null,
        //                     "side" => "BUY",
        //                     "created" => 1496005693738
        //                 }
        //             )
        //         }
        //     }
        //
        return $this->parse_orders($response['result']['orders'], $market, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $ids = $this->safe_string($params, 'ids');
        if (($symbol === null) || ($ids === null)) {
            throw new ArgumentsRequired($this->id . " fetchOrders() requires a 'symbol' argument and an extra 'ids' parameter. The 'ids' should be an array or a string of one or more order $ids separated with slashes."); // eslint-disable-line quotes
        }
        if (gettype($ids) === 'array' && count(array_filter(array_keys($ids), 'is_string')) == 0) {
            $ids = implode('/', $ids);
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
            'ids' => $ids,
        );
        $response = $this->traderGetHistoryTradingPairIds (array_merge($request, $params));
        //
        // $response = {
        //         "status" => array(
        //             "success" => 1,
        //             "message" => null
        //         ),
        //         "result" => array(
        //             {
        //                 "trading_pair" => "ETPCNY",
        //                 "status" => "TRADE",
        //                 "fee" => 0.23,
        //                 "min_fee" => 10000000,
        //                 "created_at" => "2017-05-25T00:12:27.000Z",
        //                 "cost" => 1152468000000,
        //                 "$limit" => 3600000000,
        //                 "id" => 11060,
        //                 "quantity" => 32013000000,
        //                 "filled_quantity" => 32013000000
        //             }
        //         )
        //     }
        //
        return $this->parse_orders($response['result'], null, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'trading_pair' => $market['id'],
            'page' => 0,
        );
        $response = $this->traderGetHistorysTradingPairPage (array_merge($request, $params));
        //
        // $response = {
        //         "status" => array(
        //             "success" => 1,
        //             "message" => null
        //         ),
        //         "result" => array(
        //             array(
        //                 "order_id" => 118735,
        //                 "trade_id" => 7,
        //                 "trading_pair" => "BTCCNY",
        //                 "side" => "B",
        //                 "quantity" => 1000000000,
        //                 "price" => 900000000,
        //                 "created_at" => "2017-06-06T20:45:27.000Z"
        //             ),
        //             {
        //                 "order_id" => 118734,
        //                 "trade_id" => 7,
        //                 "trading_pair" => "BTCCNY",
        //                 "side" => "S",
        //                 "quantity" => 1000000000,
        //                 "price" => 900000000,
        //                 "created_at" => "2017-06-06T20:45:27.000Z"
        //             }
        //         )
        //     }
        //
        return $this->parse_trades($response['result'], null, $since, $limit);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->urls['api'] . '/' . $api . '/' . $this->implode_params($path, $params);
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $headers = array(
                'apikey' => $this->apiKey,
                'signature' => $this->secret,
            );
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            } else {
                $body = $this->json($query);
                $headers['Content-Type'] = 'application/json';
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        $status = $this->safe_value($response, 'status');
        if ($status !== null) {
            //
            //     array("$status":array("$success":0,"$message":"ERR_USERTOKEN_NOT_FOUND"))
            //
            $success = $this->safe_string($status, 'success');
            if ($success !== '1') {
                $message = $this->safe_string($status, 'message');
                $feedback = $this->id . ' ' . $body;
                $this->throw_exactly_matched_exception($this->exceptions, $message, $feedback);
                throw new ExchangeError($feedback);
            }
        }
    }
}

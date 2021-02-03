<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class latoken extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'latoken',
            'name' => 'Latoken',
            'countries' => array( 'KY' ), // Cayman Islands
            'version' => 'v1',
            'rateLimit' => 2000,
            'certified' => false,
            'userAgent' => $this->userAgents['chrome'],
            'has' => array(
                'CORS' => false,
                'publicAPI' => true,
                'privateAPI' => true,
                'cancelOrder' => true,
                'cancelAllOrders' => true,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCanceledOrders' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => false,
                'fetchOrdersByStatus' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/61511972-24c39f00-aa01-11e9-9f7c-471f1d6e5214.jpg',
                'api' => 'https://api.latoken.com',
                'www' => 'https://latoken.com',
                'doc' => array(
                    'https://api.latoken.com',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ExchangeInfo/time',
                        'ExchangeInfo/limits',
                        'ExchangeInfo/pairs',
                        'ExchangeInfo/pairs/{currency}',
                        'ExchangeInfo/pair',
                        'ExchangeInfo/currencies',
                        'ExchangeInfo/currencies/{symbol}',
                        'MarketData/tickers',
                        'MarketData/ticker/{symbol}',
                        'MarketData/orderBook/{symbol}',
                        'MarketData/orderBook/{symbol}/{limit}',
                        'MarketData/trades/{symbol}',
                        'MarketData/trades/{symbol}/{limit}',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'Account/balances',
                        'Account/balances/{currency}',
                        'Order/status',
                        'Order/active',
                        'Order/get_order',
                        'Order/trades',
                    ),
                    'post' => array(
                        'Order/new',
                        'Order/test-order',
                        'Order/cancel',
                        'Order/cancel_all',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.1 / 100,
                    'taker' => 0.1 / 100,
                ),
            ),
            'commonCurrencies' => array(
                'MT' => 'Monarch',
                'TSL' => 'Treasure SL',
            ),
            'options' => array(
                'createOrderMethod' => 'private_post_order_new', // private_post_order_test_order
            ),
            'exceptions' => array(
                'exact' => array(
                    'Signature or ApiKey is not valid' => '\\ccxt\\AuthenticationError',
                    'Request is out of time' => '\\ccxt\\InvalidNonce',
                    'Symbol must be specified' => '\\ccxt\\BadRequest',
                ),
                'broad' => array(
                    'Request limit reached' => '\\ccxt\\DDoSProtection',
                    'Pair' => '\\ccxt\\BadRequest',
                    'Price needs to be greater than' => '\\ccxt\\InvalidOrder',
                    'Amount needs to be greater than' => '\\ccxt\\InvalidOrder',
                    'The Symbol field is required' => '\\ccxt\\InvalidOrder',
                    'OrderType is not valid' => '\\ccxt\\InvalidOrder',
                    'Side is not valid' => '\\ccxt\\InvalidOrder',
                    'Cancelable order whit' => '\\ccxt\\OrderNotFound',
                    'Order' => '\\ccxt\\OrderNotFound',
                ),
            ),
        ));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetExchangeInfoTime ($params);
        //
        //     {
        //         "time" => "2019-04-18T9:00:00.0Z",
        //         "unixTimeSeconds" => 1555578000,
        //         "unixTimeMiliseconds" => 1555578000000
        //     }
        //
        return $this->safe_integer($response, 'unixTimeMiliseconds');
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetExchangeInfoPairs ($params);
        //
        //     array(
        //         {
        //             "pairId" => 502,
        //             "$symbol" => "LAETH",
        //             "baseCurrency" => "LA",
        //             "quotedCurrency" => "ETH",
        //             "makerFee" => 0.01,
        //             "takerFee" => 0.01,
        //             "pricePrecision" => 8,
        //             "amountPrecision" => 8,
        //             "minQty" => 0.1
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'symbol');
            // the exchange shows them inverted
            $baseId = $this->safe_string($market, 'baseCurrency');
            $quoteId = $this->safe_string($market, 'quotedCurrency');
            $numericId = $this->safe_integer($market, 'pairId');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'price' => $this->safe_integer($market, 'pricePrecision'),
                'amount' => $this->safe_integer($market, 'amountPrecision'),
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'minQty'),
                    'max' => null,
                ),
                'price' => array(
                    'min' => pow(10, -$precision['price']),
                    'max' => null,
                ),
                'cost' => array(
                    'min' => null,
                    'max' => null,
                ),
            );
            $result[] = array(
                'id' => $id,
                'numericId' => $numericId,
                'info' => $market,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => null, // assuming true
                'precision' => $precision,
                'limits' => $limits,
            );
        }
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetExchangeInfoCurrencies ($params);
        //
        //     array(
        //         {
        //             "currencyId" => 102,
        //             "symbol" => "LA",
        //             "name" => "Latoken",
        //             "precission" => 8,
        //             "type" => "ERC20",
        //             "$fee" => 0.1
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'symbol');
            $numericId = $this->safe_integer($currency, 'currencyId');
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer($currency, 'precission');
            $fee = $this->safe_float($currency, 'fee');
            $active = null;
            $result[$code] = array(
                'id' => $id,
                'numericId' => $numericId,
                'code' => $code,
                'info' => $currency,
                'name' => $code,
                'active' => $active,
                'fee' => $fee,
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
                    'withdraw' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $key = 'quote';
        $rate = $market[$takerOrMaker];
        $cost = $amount * $rate;
        $precision = $market['precision']['price'];
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
            $precision = $market['precision']['amount'];
        }
        $cost = $this->decimal_to_precision($cost, ROUND, $precision, $this->precisionMode);
        return array(
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval($cost),
        );
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccountBalances ($params);
        //
        //     array(
        //         {
        //             "$currencyId" => 102,
        //             "symbol" => "LA",
        //             "name" => "Latoken",
        //             "amount" => 1054.66,
        //             "available" => 900.66,
        //             "$frozen" => 154,
        //             "$pending" => 0
        //         }
        //     )
        //
        $result = array(
            'info' => $response,
        );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'symbol');
            $code = $this->safe_currency_code($currencyId);
            $frozen = $this->safe_float($balance, 'frozen');
            $pending = $this->safe_float($balance, 'pending');
            $used = $this->sum($frozen, $pending);
            $account = array(
                'free' => $this->safe_float($balance, 'available'),
                'used' => $used,
                'total' => $this->safe_float($balance, 'amount'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'limit' => 10,
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 10, max 100
        }
        $response = $this->publicGetMarketDataOrderBookSymbolLimit (array_merge($request, $params));
        //
        //     {
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "spread" => 0.07,
        //         "asks" => array(
        //             array( "price" => 136.3, "quantity" => 7.024 )
        //         ),
        //         "bids" => array(
        //             array( "price" => 136.2, "quantity" => 6.554 )
        //         )
        //     }
        //
        return $this->parse_order_book($response, null, 'bids', 'asks', 'price', 'quantity');
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {
        //         "pairId":"63b41092-f3f6-4ea4-9e7c-4525ed250dad",
        //         "$symbol":"ETHBTC",
        //         "volume":11317.037494474000000000,
        //         "$open":0.020033000000000000,
        //         "low":0.019791000000000000,
        //         "high":0.020375000000000000,
        //         "$close":0.019923000000000000,
        //         "priceChange":-0.1500
        //     }
        //
        $marketId = $this->safe_string($ticker, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $open = $this->safe_float($ticker, 'open');
        $close = $this->safe_float($ticker, 'close');
        $change = null;
        if ($open !== null && $close !== null) {
            $change = $close - $open;
        }
        $percentage = $this->safe_float($ticker, 'priceChange');
        $timestamp = $this->nonce();
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'low' => $this->safe_float($ticker, 'low'),
            'high' => $this->safe_float($ticker, 'high'),
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $close,
            'last' => $close,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => null,
            'quoteVolume' => $this->safe_float($ticker, 'volume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetMarketDataTickerSymbol (array_merge($request, $params));
        //
        //     {
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "volume" => 1023314.3202,
        //         "open" => 134.82,
        //         "low" => 133.95,
        //         "high" => 136.22,
        //         "close" => 135.12,
        //         "priceChange" => 0.22
        //     }
        //
        return $this->parse_ticker($response, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetMarketDataTickers ($params);
        //
        //     array(
        //         {
        //             "pairId" => 502,
        //             "$symbol" => "LAETH",
        //             "volume" => 1023314.3202,
        //             "open" => 134.82,
        //             "low" => 133.95,
        //             "high" => 136.22,
        //             "close" => 135.12,
        //             "priceChange" => 0.22
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         $side => 'buy',
        //         $price => 0.33634,
        //         $amount => 0.01,
        //         $timestamp => 1564240008000 // milliseconds
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         $id => '1564223032.892829.3.tg15',
        //         $orderId => '1564223032.671436.707548@1379:1',
        //         commission => 0,
        //         $side => 'buy',
        //         $price => 0.32874,
        //         $amount => 0.607,
        //         $timestamp => 1564223033 // seconds
        //     }
        //
        $type = null;
        $timestamp = $this->safe_integer_2($trade, 'timestamp', 'time');
        if ($timestamp !== null) {
            // 03 Jan 2009 - first block
            if ($timestamp < 1230940800000) {
                $timestamp *= 1000;
            }
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $side = $this->safe_string($trade, 'side');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $amount * $price;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $id = $this->safe_string($trade, 'id');
        $orderId = $this->safe_string($trade, 'orderId');
        $feeCost = $this->safe_float($trade, 'commission');
        $fee = null;
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => null,
            );
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $orderId,
            'type' => $type,
            'takerOrMaker' => null,
            'side' => $side,
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
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 50, max 100
        }
        $response = $this->publicGetMarketDataTradesSymbol (array_merge($request, $params));
        //
        //     {
        //         "pairId":370,
        //         "$symbol":"ETHBTC",
        //         "tradeCount":51,
        //         "$trades" => array(
        //             {
        //                 side => 'buy',
        //                 price => 0.33634,
        //                 amount => 0.01,
        //                 timestamp => 1564240008000 // milliseconds
        //             }
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->privateGetOrderTrades (array_merge($request, $params));
        //
        //     {
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "tradeCount" => 1,
        //         "$trades" => array(
        //             {
        //                 id => '1564223032.892829.3.tg15',
        //                 orderId => '1564223032.671436.707548@1379:1',
        //                 commission => 0,
        //                 side => 'buy',
        //                 price => 0.32874,
        //                 amount => 0.607,
        //                 timestamp => 1564223033 // seconds
        //             }
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'active' => 'open',
            'partiallyFilled' => 'open',
            'filled' => 'closed',
            'cancelled' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "orderId":"1563460093.134037.704945@0370:2",
        //         "cliOrdId":"",
        //         "pairId":370,
        //         "$symbol":"ETHBTC",
        //         "$side":"sell",
        //         "orderType":"limit",
        //         "$price":1.0,
        //         "$amount":1.0
        //     }
        //
        // cancelOrder, fetchOrder, fetchOpenOrders, fetchClosedOrders, fetchCanceledOrders
        //
        //     {
        //         "orderId" => "1555492358.126073.126767@0502:2",
        //         "cliOrdId" => "myNewOrder",
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "$side" => "buy",
        //         "orderType" => "limit",
        //         "$price" => 136.2,
        //         "$amount" => 0.57,
        //         "orderStatus" => "partiallyFilled",
        //         "executedAmount" => 0.27,
        //         "reaminingAmount" => 0.3,
        //         "timeCreated" => 155551580736,
        //         "$timeFilled" => 0
        //     }
        //
        $id = $this->safe_string($order, 'orderId');
        $timestamp = $this->safe_timestamp($order, 'timeCreated');
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $side = $this->safe_string($order, 'side');
        $type = $this->safe_string($order, 'orderType');
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount');
        $filled = $this->safe_float($order, 'executedAmount');
        $remaining = null;
        if ($amount !== null) {
            if ($filled !== null) {
                $remaining = $amount - $filled;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'orderStatus'));
        $cost = null;
        if ($filled !== null) {
            if ($price !== null) {
                $cost = $filled * $price;
            }
        }
        $timeFilled = $this->safe_timestamp($order, 'timeFilled');
        $lastTradeTimestamp = null;
        if (($timeFilled !== null) && ($timeFilled > 0)) {
            $lastTradeTimestamp = $timeFilled;
        }
        $clientOrderId = $this->safe_string($order, 'cliOrdId');
        return array(
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'average' => null,
            'remaining' => $remaining,
            'fee' => null,
            'trades' => null,
        );
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method('private_get_order_active', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_status('filled', $symbol, $since, $limit, $params);
    }

    public function fetch_canceled_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_status('cancelled', $symbol, $since, $limit, $params);
    }

    public function fetch_orders_by_status($status, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => $status,
        );
        return $this->fetch_orders_with_method('private_get_order_status', $symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_orders_with_method($method, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrdersWithMethod() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 100
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "orderId" => "1555492358.126073.126767@0502:2",
        //             "cliOrdId" => "myNewOrder",
        //             "pairId" => 502,
        //             "$symbol" => "LAETH",
        //             "side" => "buy",
        //             "orderType" => "$limit",
        //             "price" => 136.2,
        //             "amount" => 0.57,
        //             "orderStatus" => "partiallyFilled",
        //             "executedAmount" => 0.27,
        //             "reaminingAmount" => 0.3,
        //             "timeCreated" => 155551580736,
        //             "timeFilled" => 0
        //         }
        //     )
        //
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => $id,
        );
        $response = $this->privateGetOrderGetOrder (array_merge($request, $params));
        //
        //     {
        //         "orderId" => "1555492358.126073.126767@0502:2",
        //         "cliOrdId" => "myNewOrder",
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "side" => "buy",
        //         "orderType" => "limit",
        //         "price" => 136.2,
        //         "amount" => 0.57,
        //         "orderStatus" => "partiallyFilled",
        //         "executedAmount" => 0.27,
        //         "reaminingAmount" => 0.3,
        //         "timeCreated" => 155551580736,
        //         "timeFilled" => 0
        //     }
        //
        return $this->parse_order($response);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $request = array(
            'symbol' => $this->market_id($symbol),
            'side' => $side,
            'price' => $this->price_to_precision($symbol, $price),
            'amount' => $this->amount_to_precision($symbol, $amount),
            'orderType' => $type,
        );
        $method = $this->safe_string($this->options, 'createOrderMethod', 'private_post_order_new');
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "orderId":"1563460093.134037.704945@0370:2",
        //         "cliOrdId":"",
        //         "pairId":370,
        //         "$symbol":"ETHBTC",
        //         "$side":"sell",
        //         "orderType":"limit",
        //         "$price":1.0,
        //         "$amount":1.0
        //     }
        //
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => $id,
        );
        $response = $this->privatePostOrderCancel (array_merge($request, $params));
        //
        //     {
        //         "orderId" => "1555492358.126073.126767@0502:2",
        //         "cliOrdId" => "myNewOrder",
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "side" => "buy",
        //         "orderType" => "limit",
        //         "price" => 136.2,
        //         "amount" => 0.57,
        //         "orderStatus" => "partiallyFilled",
        //         "executedAmount" => 0.27,
        //         "reaminingAmount" => 0.3,
        //         "timeCreated" => 155551580736,
        //         "timeFilled" => 0
        //     }
        //
        return $this->parse_order($response);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelAllOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $marketId = $this->market_id($symbol);
        $request = array(
            'symbol' => $marketId,
        );
        $response = $this->privatePostOrderCancelAll (array_merge($request, $params));
        //
        //     {
        //         "pairId" => 502,
        //         "$symbol" => "LAETH",
        //         "cancelledOrders" => array(
        //             "1555492358.126073.126767@0502:2"
        //         )
        //     }
        //
        $result = array();
        $canceledOrders = $this->safe_value($response, 'cancelledOrders', array());
        for ($i = 0; $i < count($canceledOrders); $i++) {
            $order = $this->parse_order(array(
                'symbol' => $marketId,
                'orderId' => $canceledOrders[$i],
                'orderStatus' => 'canceled',
            ));
            $result[] = $order;
        }
        return $result;
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = null, $headers = null, $body = null) {
        $request = '/api/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'private') {
            $nonce = $this->nonce();
            $query = array_merge(array(
                'timestamp' => $nonce,
            ), $query);
        }
        $urlencodedQuery = $this->urlencode($query);
        if ($query) {
            $request .= '?' . $urlencodedQuery;
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $signature = $this->hmac($this->encode($request), $this->encode($this->secret));
            $headers = array(
                'X-LA-KEY' => $this->apiKey,
                'X-LA-SIGNATURE' => $signature,
            );
            if ($method === 'POST') {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                $body = $urlencodedQuery;
            }
        }
        $url = $this->urls['api'] . $request;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (!$response) {
            return;
        }
        //
        //     array( "$message" => "Request limit reached!", "details" => "Request limit reached. Maximum allowed => 1 per 1s. Please try again in 1 second(s)." )
        //     array( "$error" => array( "$message" => "Pair 370 is not found","errorType":"RequestError","statusCode":400 ))
        //     array( "$error" => array( "$message" => "Signature or ApiKey is not valid","errorType":"RequestError","statusCode":400 ))
        //     array( "$error" => array( "$message" => "Request is out of time", "errorType" => "RequestError", "statusCode":400 ))
        //     array( "$error" => array( "$message" => "Price needs to be greater than 0","errorType":"ValidationError","statusCode":400 ))
        //     array( "$error" => array( "$message" => "Side is not valid, Price needs to be greater than 0, Amount needs to be greater than 0, The Symbol field is required., OrderType is not valid","errorType":"ValidationError","statusCode":400 ))
        //     array( "$error" => array( "$message" => "Cancelable order whit ID 1563460289.571254.704945@0370:1 not found","errorType":"RequestError","statusCode":400 ))
        //     array( "$error" => array( "$message" => "Symbol must be specified","errorType":"RequestError","statusCode":400 ))
        //     array( "$error" => array( "$message" => "Order 1563460289.571254.704945@0370:1 is not found","errorType":"RequestError","statusCode":400 ))
        //
        $message = $this->safe_string($response, 'message');
        $feedback = $this->id . ' ' . $body;
        if ($message !== null) {
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
        }
        $error = $this->safe_value($response, 'error', array());
        $errorMessage = $this->safe_string($error, 'message');
        if ($errorMessage !== null) {
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorMessage, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorMessage, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

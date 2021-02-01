<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class bigone extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bigone',
            'name' => 'BigONE',
            'countries' => array( 'CN' ),
            'version' => 'v3',
            'rateLimit' => 1200, // 500 request per 10 minutes
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrders' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => 'min1',
                '5m' => 'min5',
                '15m' => 'min15',
                '30m' => 'min30',
                '1h' => 'hour1',
                '3h' => 'hour3',
                '4h' => 'hour4',
                '6h' => 'hour6',
                '12h' => 'hour12',
                '1d' => 'day1',
                '1w' => 'week1',
                '1M' => 'month1',
            ),
            'hostname' => 'big.one', // or 'bigone.com'
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/69354403-1d532180-0c91-11ea-88ed-44c06cefdf87.jpg',
                'api' => array(
                    'public' => 'https://{hostname}/api/v3',
                    'private' => 'https://{hostname}/api/v3/viewer',
                ),
                'www' => 'https://big.one',
                'doc' => 'https://open.big.one/docs/api.html',
                'fees' => 'https://bigone.zendesk.com/hc/en-us/articles/115001933374-BigONE-Fee-Policy',
                'referral' => 'https://b1.run/users/new?code=D3LLBVFT',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ping',
                        'asset_pairs',
                        'asset_pairs/{asset_pair_name}/depth',
                        'asset_pairs/{asset_pair_name}/trades',
                        'asset_pairs/{asset_pair_name}/ticker',
                        'asset_pairs/{asset_pair_name}/candles',
                        'asset_pairs/tickers',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts',
                        'fund/accounts',
                        'assets/{asset_symbol}/address',
                        'orders',
                        'orders/{id}',
                        'orders/multi',
                        'trades',
                        'withdrawals',
                        'deposits',
                    ),
                    'post' => array(
                        'orders',
                        'orders/{id}/cancel',
                        'orders/cancel',
                        'withdrawals',
                        'transfer',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.1 / 100,
                    'taker' => 0.1 / 100,
                ),
                'funding' => array(
                    // HARDCODING IS DEPRECATED THE FEES BELOW ARE TO BE REMOVED SOON
                    'withdraw' => array(
                        'BTC' => 0.001,
                        'ETH' => 0.005,
                        'EOS' => 0.01,
                        'ZEC' => 0.003,
                        'LTC' => 0.01,
                        'QTUM' => 0.01,
                        // 'INK' => 0.01 QTUM,
                        // 'BOT' => 0.01 QTUM,
                        'ETC' => 0.01,
                        'GAS' => 0.0,
                        'BTS' => 1.0,
                        'GXS' => 0.1,
                        'BITCNY' => 19.0,
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    '10001' => '\\ccxt\\BadRequest', // syntax error
                    '10005' => '\\ccxt\\ExchangeError', // internal error
                    "Amount's scale must greater than AssetPair's base scale" => '\\ccxt\\InvalidOrder',
                    '10007' => '\\ccxt\\BadRequest', // parameter error, array("code":10007,"message":"Amount's scale must greater than AssetPair's base scale")
                    '10011' => '\\ccxt\\ExchangeError', // system error
                    '10013' => '\\ccxt\\OrderNotFound', // array("code":10013,"message":"Resource not found")
                    '10014' => '\\ccxt\\InsufficientFunds', // array("code":10014,"message":"Insufficient funds")
                    '10403' => '\\ccxt\\PermissionDenied', // permission denied
                    '10429' => '\\ccxt\\RateLimitExceeded', // too many requests
                    '40004' => '\\ccxt\\AuthenticationError', // array("code":40004,"message":"invalid jwt")
                    '40103' => '\\ccxt\\AuthenticationError', // invalid otp code
                    '40104' => '\\ccxt\\AuthenticationError', // invalid asset pin code
                    '40301' => '\\ccxt\\PermissionDenied', // array("code":40301,"message":"Permission denied withdrawal create")
                    '40302' => '\\ccxt\\ExchangeError', // already requested
                    '40601' => '\\ccxt\\ExchangeError', // resource is locked
                    '40602' => '\\ccxt\\ExchangeError', // resource is depleted
                    '40603' => '\\ccxt\\InsufficientFunds', // insufficient resource
                    '40120' => '\\ccxt\\InvalidOrder', // Order is in trading
                    '40121' => '\\ccxt\\InvalidOrder', // Order is already cancelled or filled
                ),
                'broad' => array(
                ),
            ),
            'commonCurrencies' => array(
                'MBN' => 'Mobilian Coin',
                'ONE' => 'BigONE Token',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetAssetPairs ($params);
        //
        //     {
        //         "code":0,
        //         "data":array(
        //             array(
        //                 "$id":"01e48809-b42f-4a38-96b1-c4c547365db1",
        //                 "name":"PCX-BTC",
        //                 "quote_scale":7,
        //                 "quote_asset":array(
        //                     "$id":"0df9c3c3-255a-46d7-ab82-dedae169fba9",
        //                     "$symbol":"BTC",
        //                     "name":"Bitcoin",
        //                 ),
        //                 "base_asset":array(
        //                     "$id":"405484f7-4b03-4378-a9c1-2bd718ecab51",
        //                     "$symbol":"PCX",
        //                     "name":"ChainX",
        //                 ),
        //                 "base_scale":3,
        //                 "min_quote_value":"0.0001",
        //             ),
        //         )
        //     }
        //
        $markets = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'name');
            $uuid = $this->safe_string($market, 'id');
            $baseAsset = $this->safe_value($market, 'base_asset', array());
            $quoteAsset = $this->safe_value($market, 'quote_asset', array());
            $baseId = $this->safe_string($baseAsset, 'symbol');
            $quoteId = $this->safe_string($quoteAsset, 'symbol');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'base_scale'),
                'price' => $this->safe_integer($market, 'quote_scale'),
            );
            $minCost = $this->safe_integer($market, 'min_quote_value');
            $entry = array(
                'id' => $id,
                'uuid' => $uuid,
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
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $minCost,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
            $result[] = $entry;
        }
        return $result;
    }

    public function load_markets($reload = false, $params = array ()) {
        $markets = parent::load_markets($reload, $params);
        $marketsByUuid = $this->safe_value($this->options, 'marketsByUuid');
        if (($marketsByUuid === null) || $reload) {
            $marketsByUuid = array();
            for ($i = 0; $i < count($this->symbols); $i++) {
                $symbol = $this->symbols[$i];
                $market = $this->markets[$symbol];
                $uuid = $this->safe_string($market, 'uuid');
                $marketsByUuid[$uuid] = $market;
            }
            $this->options['marketsByUuid'] = $marketsByUuid;
        }
        return $markets;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {
        //         "asset_pair_name":"ETH-BTC",
        //         "$bid":array("price":"0.021593","order_count":1,"quantity":"0.20936"),
        //         "$ask":array("price":"0.021613","order_count":1,"quantity":"2.87064"),
        //         "open":"0.021795",
        //         "high":"0.021795",
        //         "low":"0.021471",
        //         "$close":"0.021613",
        //         "volume":"117078.90431",
        //         "daily_change":"-0.000182"
        //     }
        //
        $marketId = $this->safe_string($ticker, 'asset_pair_name');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $timestamp = null;
        $close = $this->safe_float($ticker, 'close');
        $bid = $this->safe_value($ticker, 'bid', array());
        $ask = $this->safe_value($ticker, 'ask', array());
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($bid, 'price'),
            'bidVolume' => $this->safe_float($bid, 'quantity'),
            'ask' => $this->safe_float($ask, 'price'),
            'askVolume' => $this->safe_float($ask, 'quantity'),
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'open'),
            'close' => $close,
            'last' => $close,
            'previousClose' => null,
            'change' => $this->safe_float($ticker, 'daily_change'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'asset_pair_name' => $market['id'],
        );
        $response = $this->publicGetAssetPairsAssetPairNameTicker (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "data":{
        //             "asset_pair_name":"ETH-BTC",
        //             "bid":array("price":"0.021593","order_count":1,"quantity":"0.20936"),
        //             "ask":array("price":"0.021613","order_count":1,"quantity":"2.87064"),
        //             "open":"0.021795",
        //             "high":"0.021795",
        //             "low":"0.021471",
        //             "close":"0.021613",
        //             "volume":"117078.90431",
        //             "daily_change":"-0.000182"
        //         }
        //     }
        //
        $ticker = $this->safe_value($response, 'data', array());
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbols !== null) {
            $ids = $this->market_ids($symbols);
            $request['pair_names'] = implode(',', $ids);
        }
        $response = $this->publicGetAssetPairsTickers (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "data":array(
        //             array(
        //                 "asset_pair_name":"PCX-BTC",
        //                 "bid":array("price":"0.000234","order_count":1,"quantity":"0.518"),
        //                 "ask":array("price":"0.0002348","order_count":1,"quantity":"2.348"),
        //                 "open":"0.0002343",
        //                 "high":"0.0002348",
        //                 "low":"0.0002162",
        //                 "close":"0.0002348",
        //                 "volume":"12887.016",
        //                 "daily_change":"0.0000005"
        //             ),
        //             {
        //                 "asset_pair_name":"GXC-USDT",
        //                 "bid":array("price":"0.5054","order_count":1,"quantity":"40.53"),
        //                 "ask":array("price":"0.5055","order_count":1,"quantity":"38.53"),
        //                 "open":"0.5262",
        //                 "high":"0.5323",
        //                 "low":"0.5055",
        //                 "close":"0.5055",
        //                 "volume":"603963.05",
        //                 "daily_change":"-0.0207"
        //             }
        //         )
        //     }
        //
        $tickers = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $this->parse_ticker($tickers[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetPing ($params);
        //
        //     {
        //         "$data" => {
        //             "$timestamp" => 1527665262168391000
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $timestamp = $this->safe_integer($data, 'timestamp');
        return intval($timestamp / 1000000);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'asset_pair_name' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 50, max 200
        }
        $response = $this->publicGetAssetPairsAssetPairNameDepth (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "data" => {
        //             "asset_pair_name" => "EOS-BTC",
        //             "bids" => array(
        //                 array( "price" => "42", "order_count" => 4, "quantity" => "23.33363711" )
        //             ),
        //             "asks" => array(
        //                 array( "price" => "45", "order_count" => 2, "quantity" => "4193.3283464" )
        //             )
        //         }
        //     }
        //
        $orderbook = $this->safe_value($response, 'data', array());
        return $this->parse_order_book($orderbook, null, 'bids', 'asks', 'price', 'quantity');
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "$id" => 38199941,
        //         "$price" => "3378.67",
        //         "$amount" => "0.019812",
        //         "taker_side" => "ASK",
        //         "created_at" => "2019-01-29T06:05:56Z"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     array(
        //         "$id" => 10854280,
        //         "asset_pair_name" => "XIN-USDT",
        //         "$price" => "70",
        //         "$amount" => "1",
        //         "taker_side" => "ASK",
        //         "maker_order_id" => 58284908,
        //         "taker_order_id" => 58284909,
        //         "maker_fee" => "0.0008",
        //         "taker_fee" => "0.07",
        //         "$side" => "SELF_TRADING",
        //         "inserted_at" => "2019-04-16T12:00:01Z"
        //     ),
        //
        //     {
        //         "$id" => 10854263,
        //         "asset_pair_name" => "XIN-USDT",
        //         "$price" => "75.7",
        //         "$amount" => "12.743149",
        //         "taker_side" => "BID",
        //         "maker_order_id" => null,
        //         "taker_order_id" => 58284888,
        //         "maker_fee" => null,
        //         "taker_fee" => "0.0025486298",
        //         "$side" => "BID",
        //         "inserted_at" => "2019-04-15T06:20:57Z"
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string_2($trade, 'created_at', 'inserted_at'));
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $marketId = $this->safe_string($trade, 'asset_pair_name');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $this->cost_to_precision($symbol, $price * $amount);
            }
        }
        $side = $this->safe_string($trade, 'side');
        $takerSide = $this->safe_string($trade, 'taker_side');
        $takerOrMaker = null;
        if (($takerSide !== null) && ($side !== null) && ($side !== 'SELF_TRADING')) {
            $takerOrMaker = ($takerSide === $side) ? 'taker' : 'maker';
        }
        if ($side === null) {
            // taker $side is not related to buy/sell $side
            // the following code is probably a mistake
            $side = ($takerSide === 'ASK') ? 'sell' : 'buy';
        } else {
            if ($side === 'BID') {
                $side = 'buy';
            } else if ($side === 'ASK') {
                $side = 'sell';
            }
        }
        $makerOrderId = $this->safe_string($trade, 'maker_order_id');
        $takerOrderId = $this->safe_string($trade, 'taker_order_id');
        $orderId = null;
        if ($makerOrderId !== null) {
            if ($takerOrderId !== null) {
                $orderId = array( $makerOrderId, $takerOrderId );
            } else {
                $orderId = $makerOrderId;
            }
        } else if ($takerOrderId !== null) {
            $orderId = $takerOrderId;
        }
        $id = $this->safe_string($trade, 'id');
        $result = array(
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => 'limit',
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => floatval($cost),
            'info' => $trade,
        );
        $makerCurrencyCode = null;
        $takerCurrencyCode = null;
        if (($market !== null) && ($takerOrMaker !== null)) {
            if ($side === 'buy') {
                if ($takerOrMaker === 'maker') {
                    $makerCurrencyCode = $market['base'];
                    $takerCurrencyCode = $market['quote'];
                } else {
                    $makerCurrencyCode = $market['quote'];
                    $takerCurrencyCode = $market['base'];
                }
            } else {
                if ($takerOrMaker === 'maker') {
                    $makerCurrencyCode = $market['quote'];
                    $takerCurrencyCode = $market['base'];
                } else {
                    $makerCurrencyCode = $market['base'];
                    $takerCurrencyCode = $market['quote'];
                }
            }
        } else if ($side === 'SELF_TRADING') {
            if ($takerSide === 'BID') {
                $makerCurrencyCode = $market['quote'];
                $takerCurrencyCode = $market['base'];
            } else if ($takerSide === 'ASK') {
                $makerCurrencyCode = $market['base'];
                $takerCurrencyCode = $market['quote'];
            }
        }
        $makerFeeCost = $this->safe_float($trade, 'maker_fee');
        $takerFeeCost = $this->safe_float($trade, 'taker_fee');
        if ($makerFeeCost !== null) {
            if ($takerFeeCost !== null) {
                $result['fees'] = array(
                    array( 'cost' => $makerFeeCost, 'currency' => $makerCurrencyCode ),
                    array( 'cost' => $takerFeeCost, 'currency' => $takerCurrencyCode ),
                );
            } else {
                $result['fee'] = array( 'cost' => $makerFeeCost, 'currency' => $makerCurrencyCode );
            }
        } else if ($takerFeeCost !== null) {
            $result['fee'] = array( 'cost' => $takerFeeCost, 'currency' => $takerCurrencyCode );
        } else {
            $result['fee'] = null;
        }
        return $result;
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'asset_pair_name' => $market['id'],
        );
        $response = $this->publicGetAssetPairsAssetPairNameTrades (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "data" => array(
        //             array(
        //                 "id" => 38199941,
        //                 "price" => "3378.67",
        //                 "amount" => "0.019812",
        //                 "taker_side" => "ASK",
        //                 "created_at" => "2019-01-29T06:05:56Z"
        //             ),
        //             {
        //                 "id" => 38199934,
        //                 "price" => "3376.14",
        //                 "amount" => "0.019384",
        //                 "taker_side" => "ASK",
        //                 "created_at" => "2019-01-29T06:05:40Z"
        //             }
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'data', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         close => '0.021562',
        //         high => '0.021563',
        //         low => '0.02156',
        //         open => '0.021563',
        //         time => '2019-11-21T07:54:00Z',
        //         volume => '59.84376'
        //     }
        //
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'time')),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 100; // default 100, max 500
        }
        $request = array(
            'asset_pair_name' => $market['id'],
            'period' => $this->timeframes[$timeframe],
            'limit' => $limit,
        );
        if ($since !== null) {
            // $start = intval($since / 1000);
            $end = $this->sum($since, $limit * $this->parse_timeframe($timeframe) * 1000);
            $request['time'] = $this->iso8601($end);
        }
        $response = $this->publicGetAssetPairsAssetPairNameCandles (array_merge($request, $params));
        //
        //     {
        //         code => 0,
        //         $data => array(
        //             array(
        //                 close => '0.021656',
        //                 high => '0.021658',
        //                 low => '0.021652',
        //                 open => '0.021652',
        //                 time => '2019-11-21T09:30:00Z',
        //                 volume => '53.08664'
        //             ),
        //             array(
        //                 close => '0.021652',
        //                 high => '0.021656',
        //                 low => '0.021652',
        //                 open => '0.021656',
        //                 time => '2019-11-21T09:29:00Z',
        //                 volume => '88.39861'
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $type = $this->safe_string($params, 'type', '');
        $params = $this->omit($params, 'type');
        $method = 'privateGet' . $this->capitalize($type) . 'Accounts';
        $response = $this->$method ($params);
        //
        //     {
        //         "$code":0,
        //         "data":array(
        //             array("asset_symbol":"NKC","$balance":"0","locked_balance":"0"),
        //             array("asset_symbol":"UBTC","$balance":"0","locked_balance":"0"),
        //             array("asset_symbol":"READ","$balance":"0","locked_balance":"0"),
        //         ),
        //     }
        //
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'data', array());
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $symbol = $this->safe_string($balance, 'asset_symbol');
            $code = $this->safe_currency_code($symbol);
            $account = $this->account();
            $account['total'] = $this->safe_float($balance, 'balance');
            $account['used'] = $this->safe_float($balance, 'locked_balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order($order, $market = null) {
        //
        //    {
        //        "$id" => 10,
        //        "asset_pair_name" => "EOS-BTC",
        //        "$price" => "10.00",
        //        "$amount" => "10.00",
        //        "filled_amount" => "9.0",
        //        "avg_deal_price" => "12.0",
        //        "$side" => "ASK",
        //        "state" => "FILLED",
        //        "created_at":"2019-01-29T06:05:56Z",
        //        "updated_at":"2019-01-29T06:05:56Z",
        //    }
        //
        $id = $this->safe_string($order, 'id');
        $marketId = $this->safe_string($order, 'asset_pair_name');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount');
        $filled = $this->safe_float($order, 'filled_amount');
        $remaining = null;
        if ($amount !== null && $filled !== null) {
            $remaining = max (0, $amount - $filled);
        }
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $side = $this->safe_string($order, 'side');
        if ($side === 'BID') {
            $side = 'buy';
        } else {
            $side = 'sell';
        }
        $cost = null;
        if ($filled !== null) {
            if ($price !== null) {
                $cost = $filled * $price;
            }
        }
        $lastTradeTimestamp = $this->parse8601($this->safe_string($order, 'updated_at'));
        $average = $this->safe_float($order, 'avg_deal_price');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => null,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $side = ($side === 'buy') ? 'BID' : 'ASK';
        $uppercaseType = strtoupper($type);
        $request = array(
            'asset_pair_name' => $market['id'], // asset pair name BTC-USDT, required
            'side' => $side, // $order $side one of "ASK"/"BID", required
            'amount' => $this->amount_to_precision($symbol, $amount), // $order $amount, string, required
            // 'price' => $this->price_to_precision($symbol, $price), // $order $price, string, required
            'type' => $uppercaseType,
            // 'operator' => 'GTE', // stop orders only, GTE greater than and equal, LTE less than and equal
            // 'immediate_or_cancel' => false, // limit orders only, must be false when post_only is true
            // 'post_only' => false, // limit orders only, must be false when immediate_or_cancel is true
        );
        if ($uppercaseType === 'LIMIT') {
            $request['price'] = $this->price_to_precision($symbol, $price);
        } else {
            $isStopLimit = ($uppercaseType === 'STOP_LIMIT');
            $isStopMarket = ($uppercaseType === 'STOP_MARKET');
            if ($isStopLimit || $isStopMarket) {
                $stopPrice = $this->safe_float_2($params, 'stop_price', 'stopPrice');
                if ($stopPrice === null) {
                    throw new ArgumentsRequired($this->id . ' createOrder() requires a stop_price parameter');
                }
                $request['stop_price'] = $this->price_to_precision($symbol, $stopPrice);
                $params = $this->omit($params, array( 'stop_price', 'stopPrice' ));
            }
            if ($isStopLimit) {
                $request['price'] = $this->price_to_precision($symbol, $price);
            }
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //    {
        //        "id" => 10,
        //        "asset_pair_name" => "EOS-BTC",
        //        "$price" => "10.00",
        //        "$amount" => "10.00",
        //        "filled_amount" => "9.0",
        //        "avg_deal_price" => "12.0",
        //        "$side" => "ASK",
        //        "state" => "FILLED",
        //        "created_at":"2019-01-29T06:05:56Z",
        //        "updated_at":"2019-01-29T06:05:56Z"
        //    }
        //
        $order = $this->safe_value($response, 'data');
        return $this->parse_order($order, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array( 'id' => $id );
        $response = $this->privatePostOrdersIdCancel (array_merge($request, $params));
        //    {
        //        "$id" => 10,
        //        "asset_pair_name" => "EOS-BTC",
        //        "price" => "10.00",
        //        "amount" => "10.00",
        //        "filled_amount" => "9.0",
        //        "avg_deal_price" => "12.0",
        //        "side" => "ASK",
        //        "state" => "CANCELLED",
        //        "created_at":"2019-01-29T06:05:56Z",
        //        "updated_at":"2019-01-29T06:05:56Z"
        //    }
        $order = $this->safe_value($response, 'data');
        return $this->parse_order($order);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'asset_pair_name' => $market['id'],
        );
        $response = $this->privatePostOrdersCancel (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "data" => {
        //             "cancelled":array(
        //                 58272370,
        //                 58272377
        //             ),
        //             "failed" => array()
        //         }
        //     }
        //
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array( 'id' => $id );
        $response = $this->privateGetOrdersId (array_merge($request, $params));
        $order = $this->safe_value($response, 'data', array());
        return $this->parse_order($order);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'asset_pair_name' => $market['id'],
            // 'page_token' => 'dxzef', // $request page after this page token
            // 'side' => 'ASK', // 'ASK' or 'BID', optional
            // 'state' => 'FILLED', // 'CANCELLED', 'FILLED', 'PENDING'
            // 'limit' 20, // default 20, max 200
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 20, max 200
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        //
        //    {
        //        "code":0,
        //        "data" => array(
        //             array(
        //                 "id" => 10,
        //                 "asset_pair_name" => "ETH-BTC",
        //                 "price" => "10.00",
        //                 "amount" => "10.00",
        //                 "filled_amount" => "9.0",
        //                 "avg_deal_price" => "12.0",
        //                 "side" => "ASK",
        //                 "state" => "FILLED",
        //                 "created_at":"2019-01-29T06:05:56Z",
        //                 "updated_at":"2019-01-29T06:05:56Z",
        //             ),
        //         ),
        //        "page_token":"dxzef",
        //    }
        //
        $orders = $this->safe_value($response, 'data', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $market = $this->market($symbol);
        $request = array(
            'asset_pair_name' => $market['id'],
            // 'page_token' => 'dxzef', // $request page after this page token
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 20, max 200
        }
        $response = $this->privateGetTrades (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "data" => array(
        //             array(
        //                 "id" => 10854280,
        //                 "asset_pair_name" => "XIN-USDT",
        //                 "price" => "70",
        //                 "amount" => "1",
        //                 "taker_side" => "ASK",
        //                 "maker_order_id" => 58284908,
        //                 "taker_order_id" => 58284909,
        //                 "maker_fee" => "0.0008",
        //                 "taker_fee" => "0.07",
        //                 "side" => "SELF_TRADING",
        //                 "inserted_at" => "2019-04-16T12:00:01Z"
        //             ),
        //             {
        //                 "id" => 10854263,
        //                 "asset_pair_name" => "XIN-USDT",
        //                 "price" => "75.7",
        //                 "amount" => "12.743149",
        //                 "taker_side" => "BID",
        //                 "maker_order_id" => null,
        //                 "taker_order_id" => 58284888,
        //                 "maker_fee" => null,
        //                 "taker_fee" => "0.0025486298",
        //                 "side" => "BID",
        //                 "inserted_at" => "2019-04-15T06:20:57Z"
        //             }
        //         ),
        //         "page_token":"dxfv"
        //     }
        //
        $trades = $this->safe_value($response, 'data', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'PENDING' => 'open',
            'FILLED' => 'closed',
            'CANCELLED' => 'canceled',
        );
        return $this->safe_string($statuses, $status);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'state' => 'PENDING',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'state' => 'FILLED',
        );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function nonce() {
        return $this->microseconds() * 1000;
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = $this->omit($params, $this->extract_params($path));
        $baseUrl = $this->implode_params($this->urls['api'][$api], array( 'hostname' => $this->hostname ));
        $url = $baseUrl . '/' . $this->implode_params($path, $params);
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $request = array(
                'type' => 'OpenAPIV2',
                'sub' => $this->apiKey,
                'nonce' => $nonce,
                // 'recv_window' => '30', // default 30
            );
            $jwt = $this->jwt($request, $this->encode($this->secret));
            $headers = array(
                'Authorization' => 'Bearer ' . $jwt,
            );
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            } else if ($method === 'POST') {
                $headers['Content-Type'] = 'application/json';
                $body = $this->json($query);
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'asset_symbol' => $currency['id'],
        );
        $response = $this->privateGetAssetsAssetSymbolAddress (array_merge($request, $params));
        //
        // the actual $response format is not the same as the documented one
        // the $data key contains an array in the actual $response
        //
        //     {
        //         "$code":0,
        //         "message":"",
        //         "$data":array(
        //             {
        //                 "id":5521878,
        //                 "chain":"Bitcoin",
        //                 "value":"1GbmyKoikhpiQVZ1C9sbF17mTyvBjeobVe",
        //                 "memo":""
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $dataLength = is_array($data) ? count($data) : 0;
        if ($dataLength < 1) {
            throw new ExchangeError($this->id . 'fetchDepositAddress() returned empty $address response');
        }
        $firstElement = $data[0];
        $address = $this->safe_string($firstElement, 'value');
        $tag = $this->safe_string($firstElement, 'memo');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            // what are other $statuses here?
            'WITHHOLD' => 'ok', // deposits
            'UNCONFIRMED' => 'pending',
            'CONFIRMED' => 'ok', // withdrawals
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         "$amount" => "25.0",
        //         "asset_symbol" => "BTS"
        //         "confirms" => 100,
        //         "$id" => 5,
        //         "inserted_at" => "2018-02-16T11:39:58.000Z",
        //         "is_internal" => false,
        //         "kind" => "default",
        //         "memo" => "",
        //         "state" => "WITHHOLD",
        //         "$txid" => "72e03037d144dae3d32b68b5045462b1049a0755",
        //         "updated_at" => "2018-11-09T10:20:09.000Z",
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         "$amount" => "5",
        //         "asset_symbol" => "ETH",
        //         "completed_at" => "2018-03-15T16:13:45.610463Z",
        //         "customer_id" => "10",
        //         "$id" => 10,
        //         "inserted_at" => "2018-03-15T16:13:45.610463Z",
        //         "is_internal" => true,
        //         "note" => "2018-03-15T16:13:45.610463Z",
        //         "state" => "CONFIRMED",
        //         "target_address" => "0x4643bb6b393ac20a6175c713175734a72517c63d6f7"
        //         "$txid" => "0x4643bb6b393ac20a6175c713175734a72517c63d6f73a3ca90a15356f2e967da0",
        //     }
        //
        // withdraw
        //
        //     {
        //         "$id":1077391,
        //         "customer_id":1082679,
        //         "$amount":"21.9000000000000000",
        //         "$txid":"",
        //         "is_internal":false,
        //         "kind":"on_chain",
        //         "state":"PENDING",
        //         "inserted_at":"2020-06-03T00:50:57+00:00",
        //         "updated_at":"2020-06-03T00:50:57+00:00",
        //         "memo":"",
        //         "target_address":"rDYtYT3dBeuw376rvHqoZBKW3UmvguoBAf",
        //         "fee":"0.1000000000000000",
        //         "asset_symbol":"XRP"
        //     }
        //
        $currencyId = $this->safe_string($transaction, 'asset_symbol');
        $code = $this->safe_currency_code($currencyId);
        $id = $this->safe_integer($transaction, 'id');
        $amount = $this->safe_float($transaction, 'amount');
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'state'));
        $timestamp = $this->parse8601($this->safe_string($transaction, 'inserted_at'));
        $updated = $this->parse8601($this->safe_string_2($transaction, 'updated_at', 'completed_at'));
        $txid = $this->safe_string($transaction, 'txid');
        $address = $this->safe_string($transaction, 'target_address');
        $tag = $this->safe_string($transaction, 'memo');
        $type = (is_array($transaction) && array_key_exists('customer_id', $transaction)) ? 'deposit' : 'withdrawal';
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'addressFrom' => null,
            'address' => null,
            'addressTo' => $address,
            'tagFrom' => null,
            'tag' => $tag,
            'tagTo' => null,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => null,
        );
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'page_token' => 'dxzef', // $request page after this page token
            // 'limit' => 50, // optional, default 50
            // 'kind' => 'string', // optional - air_drop, big_holder_dividend, default, eosc_to_eos, internal, equally_airdrop, referral_mining, one_holder_dividend, single_customer, snapshotted_airdrop, trade_mining
            // 'asset_symbol' => 'BTC', // optional
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset_symbol'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 50
        }
        $response = $this->privateGetDeposits (array_merge($request, $params));
        //
        //     {
        //         "$code" => 0,
        //         "page_token" => "NQ==",
        //         "data" => array(
        //             {
        //                 "id" => 5,
        //                 "amount" => "25.0",
        //                 "confirms" => 100,
        //                 "txid" => "72e03037d144dae3d32b68b5045462b1049a0755",
        //                 "is_internal" => false,
        //                 "inserted_at" => "2018-02-16T11:39:58.000Z",
        //                 "updated_at" => "2018-11-09T10:20:09.000Z",
        //                 "kind" => "default",
        //                 "memo" => "",
        //                 "state" => "WITHHOLD",
        //                 "asset_symbol" => "BTS"
        //             }
        //         )
        //     }
        //
        $deposits = $this->safe_value($response, 'data', array());
        return $this->parse_transactions($deposits, $code, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'page_token' => 'dxzef', // $request page after this page token
            // 'limit' => 50, // optional, default 50
            // 'kind' => 'string', // optional - air_drop, big_holder_dividend, default, eosc_to_eos, internal, equally_airdrop, referral_mining, one_holder_dividend, single_customer, snapshotted_airdrop, trade_mining
            // 'asset_symbol' => 'BTC', // optional
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset_symbol'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 50
        }
        $response = $this->privateGetWithdrawals (array_merge($request, $params));
        //
        //     {
        //         "$code" => 0,
        //         "data" => array(
        //             {
        //                 "id" => 10,
        //                 "customer_id" => "10",
        //                 "asset_symbol" => "ETH",
        //                 "amount" => "5",
        //                 "state" => "CONFIRMED",
        //                 "note" => "2018-03-15T16:13:45.610463Z",
        //                 "txid" => "0x4643bb6b393ac20a6175c713175734a72517c63d6f73a3ca90a15356f2e967da0",
        //                 "completed_at" => "2018-03-15T16:13:45.610463Z",
        //                 "inserted_at" => "2018-03-15T16:13:45.610463Z",
        //                 "is_internal" => true,
        //                 "target_address" => "0x4643bb6b393ac20a6175c713175734a72517c63d6f7"
        //             }
        //         ),
        //         "page_token":"dxvf"
        //     }
        //
        $withdrawals = $this->safe_value($response, 'data', array());
        return $this->parse_transactions($withdrawals, $code, $since, $limit);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'symbol' => $currency['id'],
            'target_address' => $address,
            'amount' => $this->currency_to_precision($code, $amount),
        );
        if ($tag !== null) {
            $request['memo'] = $tag;
        }
        // requires write permission on the wallet
        $response = $this->privatePostWithdrawals (array_merge($request, $params));
        //
        //     {
        //         "$code":0,
        //         "message":"",
        //         "$data":{
        //             "id":1077391,
        //             "customer_id":1082679,
        //             "$amount":"21.9000000000000000",
        //             "txid":"",
        //             "is_internal":false,
        //             "kind":"on_chain",
        //             "state":"PENDING",
        //             "inserted_at":"2020-06-03T00:50:57+00:00",
        //             "updated_at":"2020-06-03T00:50:57+00:00",
        //             "memo":"",
        //             "target_address":"rDYtYT3dBeuw376rvHqoZBKW3UmvguoBAf",
        //             "fee":"0.1000000000000000",
        //             "asset_symbol":"XRP"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_transaction($data, $currency);
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //
        //      array("$code":10013,"$message":"Resource not found")
        //      array("$code":40004,"$message":"invalid jwt")
        //
        $code = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message');
        if ($code !== '0') {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;

class kucoin extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'kucoin',
            'name' => 'KuCoin',
            'countries' => array( 'SC' ),
            'rateLimit' => 334,
            'version' => 'v2',
            'certified' => false,
            'pro' => true,
            'comment' => 'Platform 2.0',
            'has' => array(
                'CORS' => false,
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'createDepositAddress' => true,
                'createOrder' => true,
                'fetchAccounts' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchFundingFee' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchStatus' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
                'transfer' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87295558-132aaf80-c50e-11ea-9801-a2fb0c57c799.jpg',
                'referral' => 'https://www.kucoin.com/?rcode=E5wkqe',
                'api' => array(
                    'public' => 'https://openapi-v2.kucoin.com',
                    'private' => 'https://openapi-v2.kucoin.com',
                    'futuresPrivate' => 'https://api-futures.kucoin.com',
                ),
                'test' => array(
                    'public' => 'https://openapi-sandbox.kucoin.com',
                    'private' => 'https://openapi-sandbox.kucoin.com',
                ),
                'www' => 'https://www.kucoin.com',
                'doc' => array(
                    'https://docs.kucoin.com',
                ),
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'password' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'timestamp',
                        'status',
                        'symbols',
                        'markets',
                        'market/allTickers',
                        'market/orderbook/level{level}_{limit}',
                        'market/orderbook/level{level}',
                        'market/orderbook/level2',
                        'market/orderbook/level2_20',
                        'market/orderbook/level2_100',
                        'market/orderbook/level3',
                        'market/histories',
                        'market/candles',
                        'market/stats',
                        'currencies',
                        'currencies/{currency}',
                        'prices',
                        'mark-price/{symbol}/current',
                        'margin/config',
                    ),
                    'post' => array(
                        'bullet-public',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts',
                        'accounts/{accountId}',
                        'accounts/{accountId}/ledgers',
                        'accounts/{accountId}/holds',
                        'accounts/transferable',
                        'sub/user',
                        'sub-accounts',
                        'sub-accounts/{subUserId}',
                        'deposit-addresses',
                        'deposits',
                        'hist-deposits',
                        'hist-orders',
                        'hist-withdrawals',
                        'withdrawals',
                        'withdrawals/quotas',
                        'orders',
                        'order/client-order/{clientOid}',
                        'orders/{orderId}',
                        'limit/orders',
                        'fills',
                        'limit/fills',
                        'margin/account',
                        'margin/borrow',
                        'margin/borrow/outstanding',
                        'margin/borrow/borrow/repaid',
                        'margin/lend/active',
                        'margin/lend/done',
                        'margin/lend/trade/unsettled',
                        'margin/lend/trade/settled',
                        'margin/lend/assets',
                        'margin/market',
                        'margin/trade/last',
                        'stop-order/{orderId}',
                        'stop-order',
                        'stop-order/queryOrderByClientOid',
                    ),
                    'post' => array(
                        'accounts',
                        'accounts/inner-transfer',
                        'accounts/sub-transfer',
                        'deposit-addresses',
                        'withdrawals',
                        'orders',
                        'orders/multi',
                        'margin/borrow',
                        'margin/order',
                        'margin/repay/all',
                        'margin/repay/single',
                        'margin/lend',
                        'margin/toggle-auto-lend',
                        'bullet-private',
                        'stop-order',
                    ),
                    'delete' => array(
                        'withdrawals/{withdrawalId}',
                        'orders',
                        'orders/client-order/{clientOid}',
                        'orders/{orderId}',
                        'margin/lend/{orderId}',
                        'stop-order/cancelOrderByClientOid',
                        'stop-order/{orderId}',
                        'stop-order/cancel',
                    ),
                ),
                'futuresPrivate' => array(
                    'get' => array(
                        'account-overview',
                        'positions',
                    ),
                    'post' => array(
                        'transfer-out',
                    ),
                ),
            ),
            'timeframes' => array(
                '1m' => '1min',
                '3m' => '3min',
                '5m' => '5min',
                '15m' => '15min',
                '30m' => '30min',
                '1h' => '1hour',
                '2h' => '2hour',
                '4h' => '4hour',
                '6h' => '6hour',
                '8h' => '8hour',
                '12h' => '12hour',
                '1d' => '1day',
                '1w' => '1week',
            ),
            'exceptions' => array(
                'exact' => array(
                    'order not exist' => '\\ccxt\\OrderNotFound',
                    'order not exist.' => '\\ccxt\\OrderNotFound', // duplicated error temporarily
                    'order_not_exist' => '\\ccxt\\OrderNotFound', // array("code":"order_not_exist","msg":"order_not_exist") ¯\_(ツ)_/¯
                    'order_not_exist_or_not_allow_to_cancel' => '\\ccxt\\InvalidOrder', // array("code":"400100","msg":"order_not_exist_or_not_allow_to_cancel")
                    'Order size below the minimum requirement.' => '\\ccxt\\InvalidOrder', // array("code":"400100","msg":"Order size below the minimum requirement.")
                    'The withdrawal amount is below the minimum requirement.' => '\\ccxt\\ExchangeError', // array("code":"400100","msg":"The withdrawal amount is below the minimum requirement.")
                    'Unsuccessful! Exceeded the max. funds out-transfer limit' => '\\ccxt\\InsufficientFunds', // array("code":"200000","msg":"Unsuccessful! Exceeded the max. funds out-transfer limit")
                    '400' => '\\ccxt\\BadRequest',
                    '401' => '\\ccxt\\AuthenticationError',
                    '403' => '\\ccxt\\NotSupported',
                    '404' => '\\ccxt\\NotSupported',
                    '405' => '\\ccxt\\NotSupported',
                    '429' => '\\ccxt\\RateLimitExceeded',
                    '500' => '\\ccxt\\ExchangeNotAvailable', // Internal Server Error -- We had a problem with our server. Try again later.
                    '503' => '\\ccxt\\ExchangeNotAvailable',
                    '101030' => '\\ccxt\\PermissionDenied', // array("code":"101030","msg":"You haven't yet enabled the margin trading")
                    '200004' => '\\ccxt\\InsufficientFunds',
                    '230003' => '\\ccxt\\InsufficientFunds', // array("code":"230003","msg":"Balance insufficient!")
                    '260100' => '\\ccxt\\InsufficientFunds', // array("code":"260100","msg":"account.noBalance")
                    '300000' => '\\ccxt\\InvalidOrder',
                    '400000' => '\\ccxt\\BadSymbol',
                    '400001' => '\\ccxt\\AuthenticationError',
                    '400002' => '\\ccxt\\InvalidNonce',
                    '400003' => '\\ccxt\\AuthenticationError',
                    '400004' => '\\ccxt\\AuthenticationError',
                    '400005' => '\\ccxt\\AuthenticationError',
                    '400006' => '\\ccxt\\AuthenticationError',
                    '400007' => '\\ccxt\\AuthenticationError',
                    '400008' => '\\ccxt\\NotSupported',
                    '400100' => '\\ccxt\\BadRequest',
                    '411100' => '\\ccxt\\AccountSuspended',
                    '415000' => '\\ccxt\\BadRequest', // array("code":"415000","msg":"Unsupported Media Type")
                    '500000' => '\\ccxt\\ExchangeError',
                ),
                'broad' => array(
                    'Exceeded the access frequency' => '\\ccxt\\RateLimitExceeded',
                    'require more permission' => '\\ccxt\\PermissionDenied',
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.001,
                    'maker' => 0.001,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(),
                    'deposit' => array(),
                ),
            ),
            'commonCurrencies' => array(
                'HOT' => 'HOTNOW',
                'EDGE' => 'DADI', // https://github.com/ccxt/ccxt/issues/5756
                'WAX' => 'WAXP',
                'TRY' => 'Trias',
            ),
            'options' => array(
                'version' => 'v1',
                'symbolSeparator' => '-',
                'fetchMyTradesMethod' => 'private_get_fills',
                'fetchBalance' => 'trade',
                // endpoint versions
                'versions' => array(
                    'public' => array(
                        'GET' => array(
                            'status' => 'v1',
                            'market/orderbook/level2' => 'v2',
                            'market/orderbook/level3' => 'v2',
                            'market/orderbook/level2_20' => 'v1',
                            'market/orderbook/level2_100' => 'v1',
                            'market/orderbook/level{level}' => 'v2',
                            'market/orderbook/level{level}_{limit}' => 'v1',
                        ),
                    ),
                    'private' => array(
                        'POST' => array(
                            'accounts/inner-transfer' => 'v2',
                            'accounts/sub-transfer' => 'v2',
                        ),
                    ),
                    'futuresPrivate' => array(
                        'GET' => array(
                            'account-overview' => 'v1',
                            'positions' => 'v1',
                        ),
                        'POST' => array(
                            'transfer-out' => 'v2',
                        ),
                    ),
                ),
                'accountsByType' => array(
                    'trade' => 'trade',
                    'trading' => 'trade',
                    'margin' => 'margin',
                    'main' => 'main',
                    'futures' => 'contract',
                    'contract' => 'contract',
                    'pool' => 'pool',
                    'pool-x' => 'pool',
                ),
            ),
        ));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function load_time_difference($params = array ()) {
        $response = $this->publicGetTimestamp ($params);
        $after = $this->milliseconds();
        $kucoinTime = $this->safe_integer($response, 'data');
        $this->options['timeDifference'] = intval($after - $kucoinTime);
        return $this->options['timeDifference'];
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetTimestamp ($params);
        //
        //     {
        //         "code":"200000",
        //         "msg":"success",
        //         "data":1546837113087
        //     }
        //
        return $this->safe_integer($response, 'data');
    }

    public function fetch_status($params = array ()) {
        $response = $this->publicGetStatus ($params);
        //
        //     {
        //         "code":"200000",
        //         "$data":{
        //             "msg":"",
        //             "$status":"open"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $status = $this->safe_value($data, 'status');
        if ($status !== null) {
            $status = ($status === 'open') ? 'ok' : 'maintenance';
            $this->status = array_merge($this->status, array(
                'status' => $status,
                'updated' => $this->milliseconds(),
            ));
        }
        return $this->status;
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetSymbols ($params);
        //
        //     {
        //         quoteCurrency => 'BTC',
        //         $symbol => 'KCS-BTC',
        //         $quoteMaxSize => '9999999',
        //         $quoteIncrement => '0.000001',
        //         $baseMinSize => '0.01',
        //         $quoteMinSize => '0.00001',
        //         enableTrading => true,
        //         priceIncrement => '0.00000001',
        //         name => 'KCS-BTC',
        //         baseIncrement => '0.01',
        //         $baseMaxSize => '9999999',
        //         baseCurrency => 'KCS'
        //     }
        //
        $data = $response['data'];
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $market = $data[$i];
            $id = $this->safe_string($market, 'symbol');
            list($baseId, $quoteId) = explode('-', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $active = $this->safe_value($market, 'enableTrading');
            $baseMaxSize = $this->safe_number($market, 'baseMaxSize');
            $baseMinSize = $this->safe_number($market, 'baseMinSize');
            $quoteMaxSize = $this->safe_number($market, 'quoteMaxSize');
            $quoteMinSize = $this->safe_number($market, 'quoteMinSize');
            // $quoteIncrement = $this->safe_number($market, 'quoteIncrement');
            $precision = array(
                'amount' => $this->precision_from_string($this->safe_string($market, 'baseIncrement')),
                'price' => $this->precision_from_string($this->safe_string($market, 'priceIncrement')),
            );
            $limits = array(
                'amount' => array(
                    'min' => $baseMinSize,
                    'max' => $baseMaxSize,
                ),
                'price' => array(
                    'min' => $this->safe_number($market, 'priceIncrement'),
                    'max' => $quoteMaxSize / $baseMinSize,
                ),
                'cost' => array(
                    'min' => $quoteMinSize,
                    'max' => $quoteMaxSize,
                ),
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'base' => $base,
                'quote' => $quote,
                'active' => $active,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     {
        //         "currency" => "OMG",
        //         "$name" => "OMG",
        //         "fullName" => "OmiseGO",
        //         "$precision" => 8,
        //         "confirms" => 12,
        //         "withdrawalMinSize" => "4",
        //         "withdrawalMinFee" => "1.25",
        //         "$isWithdrawEnabled" => false,
        //         "$isDepositEnabled" => false,
        //         "isMarginEnabled" => false,
        //         "isDebitEnabled" => false
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $entry = $data[$i];
            $id = $this->safe_string($entry, 'currency');
            $name = $this->safe_string($entry, 'fullName');
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer($entry, 'precision');
            $isWithdrawEnabled = $this->safe_value($entry, 'isWithdrawEnabled', false);
            $isDepositEnabled = $this->safe_value($entry, 'isDepositEnabled', false);
            $fee = $this->safe_number($entry, 'withdrawalMinFee');
            $active = ($isWithdrawEnabled && $isDepositEnabled);
            $result[$code] = array(
                'id' => $id,
                'name' => $name,
                'code' => $code,
                'precision' => $precision,
                'info' => $entry,
                'active' => $active,
                'fee' => $fee,
                'limits' => $this->limits,
            );
        }
        return $result;
    }

    public function fetch_accounts($params = array ()) {
        $response = $this->privateGetAccounts ($params);
        //
        //     {
        //         $code => "200000",
        //         $data => array(
        //             array(
        //                 balance => "0.00009788",
        //                 available => "0.00009788",
        //                 holds => "0",
        //                 currency => "BTC",
        //                 id => "5c6a4fd399a1d81c4f9cc4d0",
        //                 $type => "trade"
        //             ),
        //             {
        //                 balance => "0.00000001",
        //                 available => "0.00000001",
        //                 holds => "0",
        //                 currency => "ETH",
        //                 id => "5c6a49ec99a1d819392e8e9f",
        //                 $type => "trade"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $account = $data[$i];
            $accountId = $this->safe_string($account, 'id');
            $currencyId = $this->safe_string($account, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $type = $this->safe_string($account, 'type');  // main or trade
            $result[] = array(
                'id' => $accountId,
                'type' => $type,
                'currency' => $code,
                'info' => $account,
            );
        }
        return $result;
    }

    public function fetch_funding_fee($code, $params = array ()) {
        $currencyId = $this->currency_id($code);
        $request = array(
            'currency' => $currencyId,
        );
        $response = $this->privateGetWithdrawalsQuotas (array_merge($request, $params));
        $data = $response['data'];
        $withdrawFees = array();
        $withdrawFees[$code] = $this->safe_number($data, 'withdrawMinFee');
        return array(
            'info' => $response,
            'withdraw' => $withdrawFees,
            'deposit' => array(),
        );
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {
        //         $symbol => "ETH-BTC",
        //         high => "0.019518",
        //         vol => "7997.82836194",
        //         $last => "0.019329",
        //         low => "0.019",
        //         buy => "0.019329",
        //         sell => "0.01933",
        //         changePrice => "-0.000139",
        //         time =>  1580553706304,
        //         averagePrice => "0.01926386",
        //         changeRate => "-0.0071",
        //         volValue => "154.40791568183474"
        //     }
        //
        //     {
        //         "trading" => true,
        //         "$symbol" => "KCS-BTC",
        //         "buy" => 0.00011,
        //         "sell" => 0.00012,
        //         "sort" => 100,
        //         "volValue" => 3.13851792584,   //total
        //         "baseCurrency" => "KCS",
        //         "$market" => "BTC",
        //         "quoteCurrency" => "BTC",
        //         "symbolCode" => "KCS-BTC",
        //         "datetime" => 1548388122031,
        //         "high" => 0.00013,
        //         "vol" => 27514.34842,
        //         "low" => 0.0001,
        //         "changePrice" => -1.0e-5,
        //         "changeRate" => -0.0769,
        //         "lastTradedPrice" => 0.00012,
        //         "board" => 0,
        //         "mark" => 0
        //     }
        //
        $percentage = $this->safe_number($ticker, 'changeRate');
        if ($percentage !== null) {
            $percentage = $percentage * 100;
        }
        $last = $this->safe_number_2($ticker, 'last', 'lastTradedPrice');
        $marketId = $this->safe_string($ticker, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $baseVolume = $this->safe_number($ticker, 'vol');
        $quoteVolume = $this->safe_number($ticker, 'volValue');
        $vwap = $this->vwap($baseVolume, $quoteVolume);
        $timestamp = $this->safe_integer_2($ticker, 'time', 'datetime');
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
            'vwap' => $vwap,
            'open' => $this->safe_number($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_number($ticker, 'changePrice'),
            'percentage' => $percentage,
            'average' => $this->safe_number($ticker, 'averagePrice'),
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetMarketAllTickers ($params);
        //
        //     {
        //         "code" => "200000",
        //         "$data" => array(
        //             "date" => 1550661940645,
        //             "$ticker" => array(
        //                 'buy' => '0.00001168',
        //                 'changePrice' => '-0.00000018',
        //                 'changeRate' => '-0.0151',
        //                 'datetime' => 1550661146316,
        //                 'high' => '0.0000123',
        //                 'last' => '0.00001169',
        //                 'low' => '0.00001159',
        //                 'sell' => '0.00001182',
        //                 'symbol' => 'LOOM-BTC',
        //                 'vol' => '44399.5669'
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $tickers = $this->safe_value($data, 'ticker', array());
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $this->parse_ticker($tickers[$i]);
            $symbol = $this->safe_string($ticker, 'symbol');
            if ($symbol !== null) {
                $result[$symbol] = $ticker;
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetMarketStats (array_merge($request, $params));
        //
        //     {
        //         "code" => "200000",
        //         "data" => array(
        //             'buy' => '0.00001168',
        //             'changePrice' => '-0.00000018',
        //             'changeRate' => '-0.0151',
        //             'datetime' => 1550661146316,
        //             'high' => '0.0000123',
        //             'last' => '0.00001169',
        //             'low' => '0.00001159',
        //             'sell' => '0.00001182',
        //             'symbol' => 'LOOM-BTC',
        //             'vol' => '44399.5669'
        //         ),
        //     }
        //
        return $this->parse_ticker($response['data'], $market);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         "1545904980",             // Start time of the candle cycle
        //         "0.058",                  // opening price
        //         "0.049",                  // closing price
        //         "0.058",                  // highest price
        //         "0.049",                  // lowest price
        //         "0.018",                  // base volume
        //         "0.000945",               // quote volume
        //     )
        //
        return array(
            $this->safe_timestamp($ohlcv, 0),
            $this->safe_number($ohlcv, 1),
            $this->safe_number($ohlcv, 3),
            $this->safe_number($ohlcv, 4),
            $this->safe_number($ohlcv, 2),
            $this->safe_number($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '15m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $marketId = $market['id'];
        $request = array(
            'symbol' => $marketId,
            'type' => $this->timeframes[$timeframe],
        );
        $duration = $this->parse_timeframe($timeframe) * 1000;
        $endAt = $this->milliseconds(); // required param
        if ($since !== null) {
            $request['startAt'] = intval((int) floor($since / 1000));
            if ($limit === null) {
                // https://docs.kucoin.com/#get-klines
                // https://docs.kucoin.com/#details
                // For each query, the system would return at most 1500 pieces of $data->
                // To obtain more $data, please page the $data by time.
                $limit = $this->safe_integer($this->options, 'fetchOHLCVLimit', 1500);
            }
            $endAt = $this->sum($since, $limit * $duration);
        } else if ($limit !== null) {
            $since = $endAt - $limit * $duration;
            $request['startAt'] = intval((int) floor($since / 1000));
        }
        $request['endAt'] = intval((int) floor($endAt / 1000));
        $response = $this->publicGetMarketCandles (array_merge($request, $params));
        //
        //     {
        //         "code":"200000",
        //         "$data":[
        //             ["1591517700","0.025078","0.025069","0.025084","0.025064","18.9883256","0.4761861079404"],
        //             ["1591516800","0.025089","0.025079","0.025089","0.02506","99.4716622","2.494143499081"],
        //             ["1591515900","0.025079","0.02509","0.025091","0.025068","59.83701271","1.50060885172798"],
        //         ]
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currencyId = $this->currency_id($code);
        $request = array( 'currency' => $currencyId );
        $response = $this->privatePostDepositAddresses (array_merge($request, $params));
        // BCH array("$code":"200000","$data":array("$address":"bitcoincash:qza3m4nj9rx7l9r0cdadfqxts6f92shvhvr5ls4q7z","memo":""))
        // BTC array("$code":"200000","$data":array("$address":"36SjucKqQpQSvsak9A7h6qzFjrVXpRNZhE","memo":""))
        $data = $this->safe_value($response, 'data', array());
        $address = $this->safe_string($data, 'address');
        // BCH/BSV is returned with a "bitcoincash:" prefix, which we cut off here and only keep the $address
        if ($address !== null) {
            $address = str_replace('bitcoincash:', '', $address);
        }
        $tag = $this->safe_string($data, 'memo');
        if ($code !== 'NIM') {
            // contains spaces
            $this->check_address($address);
        }
        return array(
            'info' => $response,
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currencyId = $this->currency_id($code);
        $request = array( 'currency' => $currencyId );
        $response = $this->privateGetDepositAddresses (array_merge($request, $params));
        // BCH array("$code":"200000","$data":array("$address":"bitcoincash:qza3m4nj9rx7l9r0cdadfqxts6f92shvhvr5ls4q7z","memo":""))
        // BTC array("$code":"200000","$data":array("$address":"36SjucKqQpQSvsak9A7h6qzFjrVXpRNZhE","memo":""))
        $data = $this->safe_value($response, 'data', array());
        $address = $this->safe_string($data, 'address');
        $tag = $this->safe_string($data, 'memo');
        if ($code !== 'NIM') {
            // contains spaces
            $this->check_address($address);
        }
        return array(
            'info' => $response,
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
        );
    }

    public function fetch_l3_order_book($symbol, $limit = null, $params = array ()) {
        return $this->fetch_order_book($symbol, $limit, array( 'level' => 3 ));
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $marketId = $this->market_id($symbol);
        $level = $this->safe_integer($params, 'level', 2);
        $request = array( 'symbol' => $marketId, 'level' => $level );
        $method = 'publicGetMarketOrderbookLevelLevel';
        if ($level === 2) {
            if ($limit !== null) {
                if (($limit === 20) || ($limit === 100)) {
                    $request['limit'] = $limit;
                    $method = 'publicGetMarketOrderbookLevelLevelLimit';
                } else {
                    throw new ExchangeError($this->id . ' fetchOrderBook $limit argument must be null, 20 or 100');
                }
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // 'market/orderbook/level2'
        // 'market/orderbook/level2_20'
        // 'market/orderbook/level2_100'
        //
        //     {
        //         "code":"200000",
        //         "$data":{
        //             "sequence":"1583235112106",
        //             "asks":[
        //                 // ...
        //                 ["0.023197","12.5067468"],
        //                 ["0.023194","1.8"],
        //                 ["0.023191","8.1069672"]
        //             ],
        //             "bids":[
        //                 ["0.02319","1.6000002"],
        //                 ["0.023189","2.2842325"],
        //             ],
        //             "time":1586584067274
        //         }
        //     }
        //
        // 'market/orderbook/level3'
        //
        //     {
        //         "code":"200000",
        //         "$data":{
        //             "sequence":"1583731857120",
        //             "asks":[
        //                 // id, price, size, $timestamp in nanoseconds
        //                 ["5e915f8acd26670009675300","6925.7","0.2","1586585482194286069"],
        //                 ["5e915f8ace35a200090bba48","6925.7","0.001","1586585482229569826"],
        //                 ["5e915f8a8857740009ca7d33","6926","0.00001819","1586585482149148621"],
        //             ],
        //             "bids":[
        //                 ["5e915f8acca406000ac88194","6925.6","0.05","1586585482384384842"],
        //                 ["5e915f93cd26670009676075","6925.6","0.08","1586585491334914600"],
        //                 ["5e915f906aa6e200099b49f6","6925.4","0.2","1586585488941126340"],
        //             ],
        //             "time":1586585492487
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $timestamp = $this->safe_integer($data, 'time');
        $orderbook = $this->parse_order_book($data, $timestamp, 'bids', 'asks', $level - 2, $level - 1);
        $orderbook['nonce'] = $this->safe_integer($data, 'sequence');
        return $orderbook;
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $marketId = $this->market_id($symbol);
        // required param, cannot be used twice
        $clientOrderId = $this->safe_string_2($params, 'clientOid', 'clientOrderId', $this->uuid());
        $params = $this->omit($params, array( 'clientOid', 'clientOrderId' ));
        $request = array(
            'clientOid' => $clientOrderId,
            'side' => $side,
            'symbol' => $marketId,
            'type' => $type, // limit or market
            // 'remark' => '', // optional remark for the $order, length cannot exceed 100 utf8 characters
            // 'stp' => '', // self trade prevention, CN, CO, CB or DC
            // To improve the system performance and to accelerate $order placing and processing, KuCoin has added a new interface for margin orders
            // The current one will no longer accept margin orders by May 1st, 2021 (UTC)
            // At the time, KuCoin will notify users via the announcement, please pay attention to it
            // 'tradeType' => 'TRADE', // TRADE, MARGIN_TRADE // not used with margin orders
            // limit orders ---------------------------------------------------
            // 'timeInForce' => 'GTC', // GTC, GTT, IOC, or FOK (default is GTC), limit orders only
            // 'cancelAfter' => long, // cancel after n seconds, requires timeInForce to be GTT
            // 'postOnly' => false, // Post only flag, invalid when timeInForce is IOC or FOK
            // 'hidden' => false, // Order will not be displayed in the $order book
            // 'iceberg' => false, // Only a portion of the $order is displayed in the $order book
            // 'visibleSize' => $this->amount_to_precision($symbol, visibleSize), // The maximum visible size of an iceberg $order
            // market orders --------------------------------------------------
            // 'size' => $this->amount_to_precision($symbol, $amount), // Amount in base currency
            // 'funds' => $this->cost_to_precision($symbol, cost), // Amount of quote currency to use
            // stop orders ----------------------------------------------------
            // 'stop' => 'loss', // loss or entry, the default is loss, requires stopPrice
            // 'stopPrice' => $this->price_to_precision($symbol, $amount), // need to be defined if stop is specified
            // margin orders --------------------------------------------------
            // 'marginMode' => 'cross', // cross (cross mode) and isolated (isolated mode), set to cross by default, the isolated mode will be released soon, stay tuned
            // 'autoBorrow' => false, // The system will first borrow you funds at the optimal interest rate and then place an $order for you
        );
        $quoteAmount = $this->safe_number_2($params, 'cost', 'funds');
        if ($type === 'market') {
            if ($quoteAmount !== null) {
                $params = $this->omit($params, array( 'cost', 'funds' ));
                // kucoin uses base precision even for quote values
                $request['funds'] = $this->amount_to_precision($symbol, $quoteAmount);
            } else {
                $request['size'] = $this->amount_to_precision($symbol, $amount);
            }
        } else {
            $request['price'] = $this->price_to_precision($symbol, $price);
            $request['size'] = $this->amount_to_precision($symbol, $amount);
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //     {
        //         code => '200000',
        //         $data => {
        //             "orderId" => "5bd6e9286d99522a52e458de"
        //         }
        //    }
        //
        $data = $this->safe_value($response, 'data', array());
        $timestamp = $this->milliseconds();
        $id = $this->safe_string($data, 'orderId');
        $order = array(
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'info' => $data,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => null,
            'cost' => null,
            'average' => null,
            'filled' => null,
            'remaining' => null,
            'status' => null,
            'fee' => null,
            'trades' => null,
        );
        if ($quoteAmount === null) {
            $order['amount'] = $amount;
        } else {
            $order['cost'] = $quoteAmount;
        }
        return $order;
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $clientOrderId = $this->safe_string_2($params, 'clientOid', 'clientOrderId');
        $method = 'privateDeleteOrdersOrderId';
        if ($clientOrderId !== null) {
            $request['clientOid'] = $clientOrderId;
            $method = 'privateDeleteOrdersClientOrderClientOid';
        } else {
            $request['orderId'] = $id;
        }
        $params = $this->omit($params, array( 'clientOid', 'clientOrderId' ));
        return $this->$method (array_merge($request, $params));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'symbol' => $market['id'],
            // 'tradeType' => 'TRADE', // default is to cancel the spot trading order
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        return $this->privateDeleteOrders (array_merge($request, $params));
    }

    public function fetch_orders_by_status($status, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'status' => $status,
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['startAt'] = $since;
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        //
        //     {
        //         code => '200000',
        //         data => {
        //             "currentPage" => 1,
        //             "pageSize" => 1,
        //             "totalNum" => 153408,
        //             "totalPage" => 153408,
        //             "items" => array(
        //                 array(
        //                     "id" => "5c35c02703aa673ceec2a168",   //orderid
        //                     "$symbol" => "BTC-USDT",   //symbol
        //                     "opType" => "DEAL",      // operation type,deal is pending order,cancel is cancel order
        //                     "type" => "$limit",       // order type,e.g. $limit,markrt,stop_limit.
        //                     "side" => "buy",         // transaction direction,include buy and sell
        //                     "price" => "10",         // order price
        //                     "size" => "2",           // order quantity
        //                     "funds" => "0",          // order funds
        //                     "dealFunds" => "0.166",  // deal funds
        //                     "dealSize" => "2",       // deal quantity
        //                     "fee" => "0",            // fee
        //                     "feeCurrency" => "USDT", // charge fee currency
        //                     "stp" => "",             // self trade prevention,include CN,CO,DC,CB
        //                     "stop" => "",            // stop type
        //                     "stopTriggered" => false,  // stop order is triggered
        //                     "stopPrice" => "0",      // stop price
        //                     "timeInForce" => "GTC",  // time InForce,include GTC,GTT,IOC,FOK
        //                     "postOnly" => false,     // postOnly
        //                     "hidden" => false,       // hidden order
        //                     "iceberg" => false,      // iceberg order
        //                     "visibleSize" => "0",    // display quantity for iceberg order
        //                     "cancelAfter" => 0,      // cancel $orders time，requires timeInForce to be GTT
        //                     "channel" => "IOS",      // order source
        //                     "clientOid" => "",       // user-entered order unique mark
        //                     "remark" => "",          // remark
        //                     "tags" => "",            // tag order source
        //                     "isActive" => false,     // $status before unfilled or uncancelled
        //                     "cancelExist" => false,   // order cancellation transaction record
        //                     "createdAt" => 1547026471000  // time
        //                 ),
        //             )
        //         }
        //    }
        $responseData = $this->safe_value($response, 'data', array());
        $orders = $this->safe_value($responseData, 'items', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_status('done', $symbol, $since, $limit, $params);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_status('active', $symbol, $since, $limit, $params);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $clientOrderId = $this->safe_string_2($params, 'clientOid', 'clientOrderId');
        $method = 'privateGetOrdersOrderId';
        if ($clientOrderId !== null) {
            $request['clientOid'] = $clientOrderId;
            $method = 'privateGetOrdersClientOrderClientOid';
        } else {
            // a special case for null ids
            // otherwise a wrong endpoint for all orders will be triggered
            // https://github.com/ccxt/ccxt/issues/7234
            if ($id === null) {
                throw new InvalidOrder($this->id . ' fetchOrder() requires an order id');
            }
            $request['orderId'] = $id;
        }
        $params = $this->omit($params, array( 'clientOid', 'clientOrderId' ));
        $response = $this->$method (array_merge($request, $params));
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $responseData = $this->safe_value($response, 'data');
        return $this->parse_order($responseData, $market);
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "id" => "5c35c02703aa673ceec2a168",   //orderid
        //         "$symbol" => "BTC-USDT",   //symbol
        //         "opType" => "DEAL",      // operation $type,deal is pending $order,cancel is cancel $order
        //         "$type" => "limit",       // $order $type,e.g. limit,markrt,stop_limit.
        //         "$side" => "buy",         // transaction direction,include buy and sell
        //         "$price" => "10",         // $order $price
        //         "size" => "2",           // $order quantity
        //         "funds" => "0",          // $order funds
        //         "dealFunds" => "0.166",  // deal funds
        //         "dealSize" => "2",       // deal quantity
        //         "$fee" => "0",            // $fee
        //         "$feeCurrency" => "USDT", // charge $fee currency
        //         "stp" => "",             // self trade prevention,include CN,CO,DC,CB
        //         "stop" => "",            // stop $type
        //         "stopTriggered" => false,  // stop $order is triggered
        //         "$stopPrice" => "0",      // stop $price
        //         "$timeInForce" => "GTC",  // time InForce,include GTC,GTT,IOC,FOK
        //         "$postOnly" => false,     // $postOnly
        //         "hidden" => false,       // hidden $order
        //         "iceberg" => false,      // iceberg $order
        //         "visibleSize" => "0",    // display quantity for iceberg $order
        //         "cancelAfter" => 0,      // cancel orders time，requires $timeInForce to be GTT
        //         "channel" => "IOS",      // $order source
        //         "clientOid" => "",       // user-entered $order unique mark
        //         "remark" => "",          // remark
        //         "tags" => "",            // tag $order source
        //         "$isActive" => false,     // $status before unfilled or uncancelled
        //         "$cancelExist" => false,   // $order cancellation transaction record
        //         "createdAt" => 1547026471000  // time
        //     }
        //
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $orderId = $this->safe_string($order, 'id');
        $type = $this->safe_string($order, 'type');
        $timestamp = $this->safe_integer($order, 'createdAt');
        $datetime = $this->iso8601($timestamp);
        $price = $this->safe_number($order, 'price');
        if ($price === 0.0) {
            // $market orders
            $price = null;
        }
        $side = $this->safe_string($order, 'side');
        $feeCurrencyId = $this->safe_string($order, 'feeCurrency');
        $feeCurrency = $this->safe_currency_code($feeCurrencyId);
        $feeCost = $this->safe_number($order, 'fee');
        $amount = $this->safe_number($order, 'size');
        $filled = $this->safe_number($order, 'dealSize');
        $cost = $this->safe_number($order, 'dealFunds');
        // bool
        $isActive = $this->safe_value($order, 'isActive', false);
        $cancelExist = $this->safe_value($order, 'cancelExist', false);
        $status = $isActive ? 'open' : 'closed';
        $status = $cancelExist ? 'canceled' : $status;
        $fee = array(
            'currency' => $feeCurrency,
            'cost' => $feeCost,
        );
        $clientOrderId = $this->safe_string($order, 'clientOid');
        $timeInForce = $this->safe_string($order, 'timeInForce');
        $stopPrice = $this->safe_number($order, 'stopPrice');
        $postOnly = $this->safe_value($order, 'postOnly');
        return $this->safe_order(array(
            'id' => $orderId,
            'clientOrderId' => $clientOrderId,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => $timeInForce,
            'postOnly' => $postOnly,
            'side' => $side,
            'amount' => $amount,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'cost' => $cost,
            'filled' => $filled,
            'remaining' => null,
            'timestamp' => $timestamp,
            'datetime' => $datetime,
            'fee' => $fee,
            'status' => $status,
            'info' => $order,
            'lastTradeTimestamp' => null,
            'average' => null,
            'trades' => null,
        ));
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $method = $this->options['fetchMyTradesMethod'];
        $parseResponseData = false;
        if ($method === 'private_get_fills') {
            // does not return $trades earlier than 2019-02-18T00:00:00Z
            if ($since !== null) {
                // only returns $trades up to one week after the $since param
                $request['startAt'] = $since;
            }
        } else if ($method === 'private_get_limit_fills') {
            // does not return $trades earlier than 2019-02-18T00:00:00Z
            // takes no $params
            // only returns first 1000 $trades (not only "in the last 24 hours" as stated in the docs)
            $parseResponseData = true;
        } else if ($method === 'private_get_hist_orders') {
            // despite that this endpoint is called `HistOrders`
            // it returns historical $trades instead of orders
            // returns $trades earlier than 2019-02-18T00:00:00Z only
            if ($since !== null) {
                $request['startAt'] = intval($since / 1000);
            }
        } else {
            throw new ExchangeError($this->id . ' invalid fetchClosedOrder method');
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "currentPage" => 1,
        //         "pageSize" => 50,
        //         "totalNum" => 1,
        //         "totalPage" => 1,
        //         "items" => array(
        //             array(
        //                 "$symbol":"BTC-USDT",       // $symbol
        //                 "tradeId":"5c35c02709e4f67d5266954e",        // trade id
        //                 "orderId":"5c35c02703aa673ceec2a168",        // order id
        //                 "counterOrderId":"5c1ab46003aa676e487fa8e3", // counter order id
        //                 "side":"buy",              // transaction direction,include buy and sell
        //                 "liquidity":"taker",       // include taker and maker
        //                 "forceTaker":true,         // forced to become taker
        //                 "price":"0.083",           // order price
        //                 "size":"0.8424304",        // order quantity
        //                 "funds":"0.0699217232",    // order funds
        //                 "fee":"0",                 // fee
        //                 "feeRate":"0",             // fee rate
        //                 "feeCurrency":"USDT",      // charge fee currency
        //                 "stop":"",                 // stop type
        //                 "type":"$limit",            // order type, e.g. $limit, $market, stop_limit.
        //                 "createdAt":1547026472000  // time
        //             ),
        //             //------------------------------------------------------
        //             // v1 (historical) trade $response structure
        //             {
        //                 "$symbol" => "SNOV-ETH",
        //                 "dealPrice" => "0.0000246",
        //                 "dealValue" => "0.018942",
        //                 "amount" => "770",
        //                 "fee" => "0.00001137",
        //                 "side" => "sell",
        //                 "createdAt" => 1540080199
        //                 "id":"5c4d389e4c8c60413f78e2e5",
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $trades = null;
        if ($parseResponseData) {
            $trades = $data;
        } else {
            $trades = $this->safe_value($data, 'items', array());
        }
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($since !== null) {
            $request['startAt'] = (int) floor($since / 1000);
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $response = $this->publicGetMarketHistories (array_merge($request, $params));
        //
        //     {
        //         "code" => "200000",
        //         "data" => array(
        //             {
        //                 "sequence" => "1548764654235",
        //                 "side" => "sell",
        //                 "size":"0.6841354",
        //                 "price":"0.03202",
        //                 "time":1548848575203567174
        //             }
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'data', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "sequence" => "1548764654235",
        //         "$side" => "sell",
        //         "size":"0.6841354",
        //         "$price":"0.03202",
        //         "time":1548848575203567174
        //     }
        //
        //     {
        //         sequence => '1568787654360',
        //         $symbol => 'BTC-USDT',
        //         $side => 'buy',
        //         size => '0.00536577',
        //         $price => '9345',
        //         takerOrderId => '5e356c4a9f1a790008f8d921',
        //         time => '1580559434436443257',
        //         $type => 'match',
        //         makerOrderId => '5e356bffedf0010008fa5d7f',
        //         tradeId => '5e356c4aeefabd62c62a1ece'
        //     }
        //
        // fetchMyTrades (private) v2
        //
        //     {
        //         "$symbol":"BTC-USDT",
        //         "tradeId":"5c35c02709e4f67d5266954e",
        //         "$orderId":"5c35c02703aa673ceec2a168",
        //         "counterOrderId":"5c1ab46003aa676e487fa8e3",
        //         "$side":"buy",
        //         "liquidity":"taker",
        //         "forceTaker":true,
        //         "$price":"0.083",
        //         "size":"0.8424304",
        //         "funds":"0.0699217232",
        //         "$fee":"0",
        //         "feeRate":"0",
        //         "$feeCurrency":"USDT",
        //         "stop":"",
        //         "$type":"limit",
        //         "createdAt":1547026472000
        //     }
        //
        // fetchMyTrades v2 alternative format since 2019-05-21 https://github.com/ccxt/ccxt/pull/5162
        //
        //     {
        //         $symbol => "OPEN-BTC",
        //         forceTaker =>  false,
        //         $orderId => "5ce36420054b4663b1fff2c9",
        //         $fee => "0",
        //         $feeCurrency => "",
        //         $type => "",
        //         feeRate => "0",
        //         createdAt => 1558417615000,
        //         size => "12.8206",
        //         stop => "",
        //         $price => "0",
        //         funds => "0",
        //         tradeId => "5ce390cf6e0db23b861c6e80"
        //     }
        //
        // fetchMyTrades (private) v1 (historical)
        //
        //     {
        //         "$symbol" => "SNOV-ETH",
        //         "dealPrice" => "0.0000246",
        //         "dealValue" => "0.018942",
        //         "$amount" => "770",
        //         "$fee" => "0.00001137",
        //         "$side" => "sell",
        //         "createdAt" => 1540080199
        //         "$id":"5c4d389e4c8c60413f78e2e5",
        //     }
        //
        $marketId = $this->safe_string($trade, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $id = $this->safe_string_2($trade, 'tradeId', 'id');
        $orderId = $this->safe_string($trade, 'orderId');
        $takerOrMaker = $this->safe_string($trade, 'liquidity');
        $amount = $this->safe_number_2($trade, 'size', 'amount');
        $timestamp = $this->safe_integer($trade, 'time');
        if ($timestamp !== null) {
            $timestamp = intval($timestamp / 1000000);
        } else {
            $timestamp = $this->safe_integer($trade, 'createdAt');
            // if it's a historical v1 $trade, the exchange returns $timestamp in seconds
            if ((is_array($trade) && array_key_exists('dealValue', $trade)) && ($timestamp !== null)) {
                $timestamp = $timestamp * 1000;
            }
        }
        $price = $this->safe_number_2($trade, 'price', 'dealPrice');
        $side = $this->safe_string($trade, 'side');
        $fee = null;
        $feeCost = $this->safe_number($trade, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'feeCurrency');
            $feeCurrency = $this->safe_currency_code($feeCurrencyId);
            if ($feeCurrency === null) {
                if ($market !== null) {
                    $feeCurrency = ($side === 'sell') ? $market['quote'] : $market['base'];
                }
            }
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
                'rate' => $this->safe_number($trade, 'feeRate'),
            );
        }
        $type = $this->safe_string($trade, 'type');
        if ($type === 'match') {
            $type = null;
        }
        $cost = $this->safe_number_2($trade, 'funds', 'dealValue');
        if ($cost === null) {
            if ($amount !== null) {
                if ($price !== null) {
                    $cost = $amount * $price;
                }
            }
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'takerOrMaker' => $takerOrMaker,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $this->check_address($address);
        $currency = $this->currency_id($code);
        $request = array(
            'currency' => $currency,
            'address' => $address,
            'amount' => $amount,
        );
        if ($tag !== null) {
            $request['memo'] = $tag;
        }
        $response = $this->privatePostWithdrawals (array_merge($request, $params));
        //
        // https://github.com/ccxt/ccxt/issues/5558
        //
        //     {
        //         "$code" =>  200000,
        //         "$data" => {
        //             "withdrawalId" =>  "abcdefghijklmnopqrstuvwxyz"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return array(
            'id' => $this->safe_string($data, 'withdrawalId'),
            'info' => $response,
        );
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'SUCCESS' => 'ok',
            'PROCESSING' => 'ok',
            'FAILURE' => 'failed',
        );
        return $this->safe_string($statuses, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         "$address" => "0x5f047b29041bcfdbf0e4478cdfa753a336ba6989",
        //         "memo" => "5c247c8a03aa677cea2a251d",
        //         "$amount" => 1,
        //         "$fee" => 0.0001,
        //         "$currency" => "KCS",
        //         "isInner" => false,
        //         "walletTxId" => "5bbb57386d99522d9f954c5a@test004",
        //         "$status" => "SUCCESS",
        //         "createdAt" => 1544178843000,
        //         "updatedAt" => 1544178891000
        //         "remark":"foobar"
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         "$id" => "5c2dc64e03aa675aa263f1ac",
        //         "$address" => "0x5bedb060b8eb8d823e2414d82acce78d38be7fe9",
        //         "memo" => "",
        //         "$currency" => "ETH",
        //         "$amount" => 1.0000000,
        //         "$fee" => 0.0100000,
        //         "walletTxId" => "3e2414d82acce78d38be7fe9",
        //         "isInner" => false,
        //         "$status" => "FAILURE",
        //         "createdAt" => 1546503758000,
        //         "updatedAt" => 1546504603000
        //         "remark":"foobar"
        //     }
        //
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $address = $this->safe_string($transaction, 'address');
        $amount = $this->safe_number($transaction, 'amount');
        $txid = $this->safe_string($transaction, 'walletTxId');
        if ($txid !== null) {
            $txidParts = explode('@', $txid);
            $numTxidParts = is_array($txidParts) ? count($txidParts) : 0;
            if ($numTxidParts > 1) {
                if ($address === null) {
                    if (strlen($txidParts[1]) > 1) {
                        $address = $txidParts[1];
                    }
                }
            }
            $txid = $txidParts[0];
        }
        $type = ($txid === null) ? 'withdrawal' : 'deposit';
        $rawStatus = $this->safe_string($transaction, 'status');
        $status = $this->parse_transaction_status($rawStatus);
        $fee = null;
        $feeCost = $this->safe_number($transaction, 'fee');
        if ($feeCost !== null) {
            $rate = null;
            if ($amount !== null) {
                $rate = $feeCost / $amount;
            }
            $fee = array(
                'cost' => $feeCost,
                'rate' => $rate,
                'currency' => $code,
            );
        }
        $tag = $this->safe_string($transaction, 'memo');
        $timestamp = $this->safe_integer_2($transaction, 'createdAt', 'createAt');
        $id = $this->safe_string($transaction, 'id');
        $updated = $this->safe_integer($transaction, 'updatedAt');
        $isV1 = !(is_array($transaction) && array_key_exists('createdAt', $transaction));
        // if it's a v1 structure
        if ($isV1) {
            $type = (is_array($transaction) && array_key_exists('address', $transaction)) ? 'withdrawal' : 'deposit';
            if ($timestamp !== null) {
                $timestamp = $timestamp * 1000;
            }
            if ($updated !== null) {
                $updated = $updated * 1000;
            }
        }
        $comment = $this->safe_string($transaction, 'remark');
        return array(
            'id' => $id,
            'info' => $transaction,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'addressTo' => $address,
            'addressFrom' => null,
            'tag' => $tag,
            'tagTo' => $tag,
            'tagFrom' => null,
            'currency' => $code,
            'amount' => $amount,
            'txid' => $txid,
            'type' => $type,
            'status' => $status,
            'comment' => $comment,
            'fee' => $fee,
            'updated' => $updated,
        );
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $method = 'privateGetDeposits';
        if ($since !== null) {
            // if $since is earlier than 2019-02-18T00:00:00Z
            if ($since < 1550448000000) {
                $request['startAt'] = intval($since / 1000);
                $method = 'privateGetHistDeposits';
            } else {
                $request['startAt'] = $since;
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         $code => '200000',
        //         data => {
        //             "currentPage" => 1,
        //             "pageSize" => 5,
        //             "totalNum" => 2,
        //             "totalPage" => 1,
        //             "items" => array(
        //                 //--------------------------------------------------
        //                 // version 2 deposit $response structure
        //                 array(
        //                     "address" => "0x5f047b29041bcfdbf0e4478cdfa753a336ba6989",
        //                     "memo" => "5c247c8a03aa677cea2a251d",
        //                     "amount" => 1,
        //                     "fee" => 0.0001,
        //                     "$currency" => "KCS",
        //                     "isInner" => false,
        //                     "walletTxId" => "5bbb57386d99522d9f954c5a@test004",
        //                     "status" => "SUCCESS",
        //                     "createdAt" => 1544178843000,
        //                     "updatedAt" => 1544178891000
        //                     "remark":"foobar"
        //                 ),
        //                 //--------------------------------------------------
        //                 // version 1 (historical) deposit $response structure
        //                 {
        //                     "$currency" => "BTC",
        //                     "createAt" => 1528536998,
        //                     "amount" => "0.03266638",
        //                     "walletTxId" => "55c643bc2c68d6f17266383ac1be9e454038864b929ae7cee0bc408cc5c869e8@12ffGWmMMD1zA1WbFm7Ho3JZ1w6NYXjpFk@234",
        //                     "isInner" => false,
        //                     "status" => "SUCCESS",
        //                 }
        //             )
        //         }
        //     }
        //
        $responseData = $response['data']['items'];
        return $this->parse_transactions($responseData, $currency, $since, $limit, array( 'type' => 'deposit' ));
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $method = 'privateGetWithdrawals';
        if ($since !== null) {
            // if $since is earlier than 2019-02-18T00:00:00Z
            if ($since < 1550448000000) {
                $request['startAt'] = intval($since / 1000);
                $method = 'privateGetHistWithdrawals';
            } else {
                $request['startAt'] = $since;
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         $code => '200000',
        //         data => {
        //             "currentPage" => 1,
        //             "pageSize" => 5,
        //             "totalNum" => 2,
        //             "totalPage" => 1,
        //             "items" => array(
        //                 //--------------------------------------------------
        //                 // version 2 withdrawal $response structure
        //                 array(
        //                     "id" => "5c2dc64e03aa675aa263f1ac",
        //                     "address" => "0x5bedb060b8eb8d823e2414d82acce78d38be7fe9",
        //                     "memo" => "",
        //                     "$currency" => "ETH",
        //                     "amount" => 1.0000000,
        //                     "fee" => 0.0100000,
        //                     "walletTxId" => "3e2414d82acce78d38be7fe9",
        //                     "isInner" => false,
        //                     "status" => "FAILURE",
        //                     "createdAt" => 1546503758000,
        //                     "updatedAt" => 1546504603000
        //                 ),
        //                 //--------------------------------------------------
        //                 // version 1 (historical) withdrawal $response structure
        //                 {
        //                     "$currency" => "BTC",
        //                     "createAt" => 1526723468,
        //                     "amount" => "0.534",
        //                     "address" => "33xW37ZSW4tQvg443Pc7NLCAs167Yc2XUV",
        //                     "walletTxId" => "aeacea864c020acf58e51606169240e96774838dcd4f7ce48acf38e3651323f4",
        //                     "isInner" => false,
        //                     "status" => "SUCCESS"
        //                 }
        //             )
        //         }
        //     }
        //
        $responseData = $response['data']['items'];
        return $this->parse_transactions($responseData, $currency, $since, $limit, array( 'type' => 'withdrawal' ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string_2($this->options, 'fetchBalance', 'defaultType', 'trade');
        $requestedType = $this->safe_string($params, 'type', $defaultType);
        $accountsByType = $this->safe_value($this->options, 'accountsByType');
        $type = $this->safe_string($accountsByType, $requestedType);
        if ($type === null) {
            $keys = is_array($accountsByType) ? array_keys($accountsByType) : array();
            throw new ExchangeError($this->id . ' $type must be one of ' . implode(', ', $keys));
        }
        $params = $this->omit($params, 'type');
        if ($type === 'contract') {
            // futures api requires a futures apiKey
            // only fetches one $balance at a time
            // by default it will only fetch the BTC $balance of the futures $account
            // you can send 'currency' in $params to fetch other currencies
            // fetchBalance (array( 'type' => 'futures', 'currency' => 'USDT' ))
            $response = $this->futuresPrivateGetAccountOverview ($params);
            //
            //     {
            //         $code => '200000',
            //         $data => {
            //             accountEquity => 0.00005,
            //             unrealisedPNL => 0,
            //             marginBalance => 0.00005,
            //             positionMargin => 0,
            //             orderMargin => 0,
            //             frozenFunds => 0,
            //             availableBalance => 0.00005,
            //             currency => 'XBT'
            //         }
            //     }
            //
            $data = $this->safe_value($response, 'data');
            $currencyId = $this->safe_string($data, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($data, 'availableBalance');
            $account['total'] = $this->safe_number($data, 'accountEquity');
            $result = array( 'info' => $response );
            $result[$code] = $account;
            return $this->parse_balance($result);
        } else {
            $request = array(
                'type' => $type,
            );
            $response = $this->privateGetAccounts (array_merge($request, $params));
            //
            //     {
            //         "$code":"200000",
            //         "$data":array(
            //             array("$balance":"0.00009788","available":"0.00009788","holds":"0","currency":"BTC","id":"5c6a4fd399a1d81c4f9cc4d0","$type":"trade"),
            //             array("$balance":"3.41060034","available":"3.41060034","holds":"0","currency":"SOUL","id":"5c6a4d5d99a1d8182d37046d","$type":"trade"),
            //             array("$balance":"0.01562641","available":"0.01562641","holds":"0","currency":"NEO","id":"5c6a4f1199a1d8165a99edb1","$type":"trade"),
            //         )
            //     }
            //
            $data = $this->safe_value($response, 'data', array());
            $result = array( 'info' => $response );
            for ($i = 0; $i < count($data); $i++) {
                $balance = $data[$i];
                $balanceType = $this->safe_string($balance, 'type');
                if ($balanceType === $type) {
                    $currencyId = $this->safe_string($balance, 'currency');
                    $code = $this->safe_currency_code($currencyId);
                    $account = $this->account();
                    $account['total'] = $this->safe_number($balance, 'balance');
                    $account['free'] = $this->safe_number($balance, 'available');
                    $account['used'] = $this->safe_number($balance, 'holds');
                    $result[$code] = $account;
                }
            }
            return $this->parse_balance($result);
        }
    }

    public function transfer($code, $amount, $fromAccount, $toAccount, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $requestedAmount = $this->currency_to_precision($code, $amount);
        $accountsById = $this->safe_value($this->options, 'accountsByType', array());
        $fromId = $this->safe_string($accountsById, $fromAccount);
        if ($fromId === null) {
            $keys = is_array($accountsById) ? array_keys($accountsById) : array();
            throw new ExchangeError($this->id . ' $fromAccount must be one of ' . implode(', ', $keys));
        }
        $toId = $this->safe_string($accountsById, $toAccount);
        if ($toId === null) {
            $keys = is_array($accountsById) ? array_keys($accountsById) : array();
            throw new ExchangeError($this->id . ' $toAccount must be one of ' . implode(', ', $keys));
        }
        if ($fromId === 'contract') {
            if ($toId !== 'main') {
                throw new ExchangeError($this->id . ' only supports transferring from futures account to main account');
            }
            $request = array(
                'currency' => $currency['id'],
                'amount' => $requestedAmount,
            );
            if (!(is_array($params) && array_key_exists('bizNo', $params))) {
                // it doesn't like more than 24 characters
                $request['bizNo'] = $this->uuid22();
            }
            $response = $this->futuresPrivatePostTransferOut (array_merge($request, $params));
            //
            //     {
            //         $code => '200000',
            //         $data => {
            //             applyId => '605a87217dff1500063d485d',
            //             bizNo => 'bcd6e5e1291f4905af84dc',
            //             payAccountType => 'CONTRACT',
            //             payTag => 'DEFAULT',
            //             remark => '',
            //             recAccountType => 'MAIN',
            //             recTag => 'DEFAULT',
            //             recRemark => '',
            //             recSystem => 'KUCOIN',
            //             $status => 'PROCESSING',
            //             $currency => 'XBT',
            //             $amount => '0.00001',
            //             fee => '0',
            //             sn => '573688685663948',
            //             reason => '',
            //             createdAt => 1616545569000,
            //             updatedAt => 1616545569000
            //         }
            //     }
            //
            $data = $this->safe_value($response, 'data');
            $timestamp = $this->safe_integer($data, 'createdAt');
            $id = $this->safe_string($data, 'applyId');
            $currencyId = $this->safe_string($data, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $amount = $this->safe_number($data, 'amount');
            $rawStatus = $this->safe_string($data, 'status');
            $status = null;
            if ($rawStatus === 'PROCESSING') {
                $status = 'pending';
            }
            return array(
                'info' => $response,
                'currency' => $code,
                'timestamp' => $timestamp,
                'datetime' => $this->iso8601($timestamp),
                'amount' => $amount,
                'fromAccount' => $fromId,
                'toAccount' => $toId,
                'id' => $id,
                'status' => $status,
            );
        } else {
            $request = array(
                'currency' => $currency['id'],
                'from' => $fromId,
                'to' => $toId,
                'amount' => $requestedAmount,
            );
            if (!(is_array($params) && array_key_exists('clientOid', $params))) {
                $request['clientOid'] = $this->uuid();
            }
            $response = $this->privatePostAccountsInnerTransfer (array_merge($request, $params));
            // array( $code => '200000', $data => array( orderId => '605a6211e657f00006ad0ad6' ) )
            $data = $this->safe_value($response, 'data');
            $id = $this->safe_string($data, 'orderId');
            return array(
                'info' => $response,
                'id' => $id,
                'timestamp' => null,
                'datetime' => null,
                'currency' => $code,
                'amount' => $requestedAmount,
                'fromAccount' => $fromId,
                'toAccount' => $toId,
                'status' => null,
            );
        }
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        if ($code === null) {
            throw new ArgumentsRequired($this->id . ' fetchLedger() requires a $code param');
        }
        $this->load_markets();
        $this->load_accounts();
        $currency = $this->currency($code);
        $accountId = $this->safe_string($params, 'accountId');
        if ($accountId === null) {
            for ($i = 0; $i < count($this->accounts); $i++) {
                $account = $this->accounts[$i];
                if ($account['currency'] === $code && $account['type'] === 'main') {
                    $accountId = $account['id'];
                    break;
                }
            }
        }
        if ($accountId === null) {
            throw new ExchangeError($this->id . ' ' . $code . 'main $account is not loaded in loadAccounts');
        }
        $request = array(
            'accountId' => $accountId,
        );
        if ($since !== null) {
            $request['startAt'] = (int) floor($since / 1000);
        }
        $response = $this->privateGetAccountsAccountIdLedgers (array_merge($request, $params));
        //
        //     {
        //         $code => '200000',
        //         data => {
        //             totalNum => 1,
        //             totalPage => 1,
        //             pageSize => 50,
        //             currentPage => 1,
        //             $items => array(
        //                 {
        //                     createdAt => 1561897880000,
        //                     amount => '0.0111123',
        //                     bizType => 'Exchange',
        //                     balance => '0.13224427',
        //                     fee => '0.0000111',
        //                     context => 'array("symbol":"KCS-ETH","orderId":"5d18ab98c788c6426188296f","tradeId":"5d18ab9818996813f539a806")',
        //                     $currency => 'ETH',
        //                     direction => 'out'
        //                 }
        //             )
        //         }
        //     }
        //
        $items = $response['data']['items'];
        return $this->parse_ledger($items, $currency, $since, $limit);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        // trade
        //
        //     {
        //         createdAt => 1561897880000,
        //         $amount => '0.0111123',
        //         bizType => 'Exchange',
        //         balance => '0.13224427',
        //         $fee => '0.0000111',
        //         $context => 'array("symbol":"KCS-ETH","orderId":"5d18ab98c788c6426188296f","tradeId":"5d18ab9818996813f539a806")',
        //         $currency => 'ETH',
        //         $direction => 'out'
        //     }
        //
        // withdrawal
        //
        //     {
        //         createdAt => 1561900264000,
        //         $amount => '0.14333217',
        //         bizType => 'Withdrawal',
        //         balance => '0',
        //         $fee => '0.01',
        //         $context => 'array("orderId":"5d18b4e687111437cf1c48b9","txId":"0x1d136ee065c5c4c5caa293faa90d43e213c953d7cdd575c89ed0b54eb87228b8")',
        //         $currency => 'ETH',
        //         $direction => 'out'
        //     }
        //
        $currencyId = $this->safe_string($item, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $fee = array(
            'cost' => $this->safe_number($item, 'fee'),
            'code' => $code,
        );
        $amount = $this->safe_number($item, 'amount');
        $after = $this->safe_number($item, 'balance');
        $direction = $this->safe_string($item, 'direction');
        $before = null;
        if ($after !== null && $amount !== null) {
            $difference = ($direction === 'out') ? $amount : -$amount;
            $before = $this->sum($after, $difference);
        }
        $timestamp = $this->safe_integer($item, 'createdAt');
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'bizType'));
        $contextString = $this->safe_string($item, 'context');
        $id = null;
        $referenceId = null;
        if ($this->is_json_encoded_object($contextString)) {
            $context = $this->parse_json($contextString);
            $id = $this->safe_string($context, 'orderId');
            if ($type === 'trade') {
                $referenceId = $this->safe_string($context, 'tradeId');
            } else if ($type === 'transaction') {
                $referenceId = $this->safe_string($context, 'txId');
            }
        }
        return array(
            'id' => $id,
            'currency' => $code,
            'account' => null,
            'referenceAccount' => null,
            'referenceId' => $referenceId,
            'status' => null,
            'amount' => $amount,
            'before' => $before,
            'after' => $after,
            'fee' => $fee,
            'direction' => $direction,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'type' => $type,
            'info' => $item,
        );
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'Exchange' => 'trade',
            'Withdrawal' => 'transaction',
            'Deposit' => 'transaction',
            'Transfer' => 'transfer',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function fetch_positions($symbols = null, $params = array ()) {
        $response = $this->futuresPrivateGetPositions ($params);
        //
        //     {
        //         code => '200000',
        //         data => array(
        //             array(
        //                 id => '605a9772a229ab0006408258',
        //                 symbol => 'XBTUSDTM',
        //                 autoDeposit => false,
        //                 maintMarginReq => 0.005,
        //                 riskLimit => 200,
        //                 realLeverage => 0,
        //                 crossMode => false,
        //                 delevPercentage => 0,
        //                 currentTimestamp => 1616549746099,
        //                 currentQty => 0,
        //                 currentCost => 0,
        //                 currentComm => 0,
        //                 unrealisedCost => 0,
        //                 realisedGrossCost => 0,
        //                 realisedCost => 0,
        //                 isOpen => false,
        //                 markPrice => 54371.92,
        //                 markValue => 0,
        //                 posCost => 0,
        //                 posCross => 0,
        //                 posInit => 0,
        //                 posComm => 0,
        //                 posLoss => 0,
        //                 posMargin => 0,
        //                 posMaint => 0,
        //                 maintMargin => 0,
        //                 realisedGrossPnl => 0,
        //                 realisedPnl => 0,
        //                 unrealisedPnl => 0,
        //                 unrealisedPnlPcnt => 0,
        //                 unrealisedRoePcnt => 0,
        //                 avgEntryPrice => 0,
        //                 liquidationPrice => 0,
        //                 bankruptPrice => 0,
        //                 settleCurrency => 'USDT',
        //                 isInverse => false
        //             ),
        //             {
        //                 id => '605a9772026ac900066550df',
        //                 symbol => 'XBTUSDM',
        //                 autoDeposit => false,
        //                 maintMarginReq => 0.005,
        //                 riskLimit => 200,
        //                 realLeverage => 0,
        //                 crossMode => false,
        //                 delevPercentage => 0,
        //                 currentTimestamp => 1616549746110,
        //                 currentQty => 0,
        //                 currentCost => 0,
        //                 currentComm => 0,
        //                 unrealisedCost => 0,
        //                 realisedGrossCost => 0,
        //                 realisedCost => 0,
        //                 isOpen => false,
        //                 markPrice => 54354.76,
        //                 markValue => 0,
        //                 posCost => 0,
        //                 posCross => 0,
        //                 posInit => 0,
        //                 posComm => 0,
        //                 posLoss => 0,
        //                 posMargin => 0,
        //                 posMaint => 0,
        //                 maintMargin => 0,
        //                 realisedGrossPnl => 0,
        //                 realisedPnl => 0,
        //                 unrealisedPnl => 0,
        //                 unrealisedPnlPcnt => 0,
        //                 unrealisedRoePcnt => 0,
        //                 avgEntryPrice => 0,
        //                 liquidationPrice => 0,
        //                 bankruptPrice => 0,
        //                 settleCurrency => 'XBT',
        //                 isInverse => true
        //             }
        //         )
        //     }
        //
        return $this->safe_value($response, 'data', $response);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        //
        // the v2 URL is https://openapi-v2.kucoin.com/api/v1/endpoint
        //                                †                 ↑
        //
        $versions = $this->safe_value($this->options, 'versions', array());
        $apiVersions = $this->safe_value($versions, $api, array());
        $methodVersions = $this->safe_value($apiVersions, $method, array());
        $defaultVersion = $this->safe_string($methodVersions, $path, $this->options['version']);
        $version = $this->safe_string($params, 'version', $defaultVersion);
        $params = $this->omit($params, 'version');
        $endpoint = '/api/' . $version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        $endpart = '';
        $headers = ($headers !== null) ? $headers : array();
        if ($query) {
            if (($method === 'GET') || ($method === 'DELETE')) {
                $endpoint .= '?' . $this->urlencode($query);
            } else {
                $body = $this->json($query);
                $endpart = $body;
                $headers['Content-Type'] = 'application/json';
            }
        }
        $url = $this->urls['api'][$api] . $endpoint;
        if (($api === 'private') || ($api === 'futuresPrivate')) {
            $this->check_required_credentials();
            $timestamp = (string) $this->nonce();
            $headers = array_merge(array(
                'KC-API-KEY-VERSION' => '2',
                'KC-API-KEY' => $this->apiKey,
                'KC-API-TIMESTAMP' => $timestamp,
            ), $headers);
            $apiKeyVersion = $this->safe_string($headers, 'KC-API-KEY-VERSION');
            if ($apiKeyVersion === '2') {
                $passphrase = $this->hmac($this->encode($this->password), $this->encode($this->secret), 'sha256', 'base64');
                $headers['KC-API-PASSPHRASE'] = $passphrase;
            } else {
                $headers['KC-API-PASSPHRASE'] = $this->password;
            }
            $payload = $timestamp . $method . $endpoint . $endpart;
            $signature = $this->hmac($this->encode($payload), $this->encode($this->secret), 'sha256', 'base64');
            $headers['KC-API-SIGN'] = $signature;
            $partner = $this->safe_value($this->options, 'partner', array());
            $partnerId = $this->safe_string($partner, 'id');
            $partnerSecret = $this->safe_string($partner, 'secret');
            if (($partnerId !== null) && ($partnerSecret !== null)) {
                $partnerPayload = $timestamp . $partnerId . $this->apiKey;
                $partnerSignature = $this->hmac($this->encode($partnerPayload), $this->encode($partnerSecret), 'sha256', 'base64');
                $headers['KC-API-PARTNER-SIGN'] = $partnerSignature;
                $headers['KC-API-PARTNER'] = $partnerId;
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (!$response) {
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $body, $body);
            return;
        }
        //
        // bad
        //     array( "$code" => "400100", "msg" => "validation.createOrder.clientOidIsRequired" )
        // good
        //     array( $code => '200000', data => array( ... ))
        //
        $errorCode = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'msg', '');
        $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $this->id . ' ' . $message);
        $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $this->id . ' ' . $message);
    }
}

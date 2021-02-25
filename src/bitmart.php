<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;
use \ccxt\NotSupported;

class bitmart extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitmart',
            'name' => 'BitMart',
            'countries' => array( 'US', 'CN', 'HK', 'KR' ),
            'rateLimit' => 1000,
            'version' => 'v1',
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'cancelOrders' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCanceledOrders' => true,
                'fetchClosedOrders' => true,
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
                'fetchTime' => true,
                'fetchStatus' => true,
                'fetchTrades' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'hostname' => 'bitmart.com', // bitmart.info for Hong Kong users
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/61835713-a2662f80-ae85-11e9-9d00-6442919701fd.jpg',
                'api' => 'https://api-cloud.{hostname}', // bitmart.info for Hong Kong users
                'www' => 'https://www.bitmart.com/',
                'doc' => 'https://developer-pro.bitmart.com/',
                'referral' => 'http://www.bitmart.com/?r=rQCFLh',
                'fees' => 'https://www.bitmart.com/fee/en',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array(
                'public' => array(
                    'system' => array(
                        'get' => array(
                            'time', // https://api-cloud.bitmart.com/system/time
                            'service', // https://api-cloud.bitmart.com/system/service
                        ),
                    ),
                    'account' => array(
                        'get' => array(
                            'currencies', // https://api-cloud.bitmart.com/account/v1/currencies
                        ),
                    ),
                    'spot' => array(
                        'get' => array(
                            'currencies',
                            'symbols',
                            'symbols/details',
                            'ticker', // ?symbol=BTC_USDT
                            'steps', // ?symbol=BMX_ETH
                            'symbols/kline', // ?symbol=BMX_ETH&step=15&from=1525760116&to=1525769116
                            'symbols/book', // ?symbol=BMX_ETH&precision=6
                            'symbols/trades', // ?symbol=BMX_ETH
                        ),
                    ),
                    'contract' => array(
                        'get' => array(
                            'contracts', // https://api-cloud.bitmart.com/contract/v1/ifcontract/contracts
                            'pnls',
                            'indexes',
                            'tickers',
                            'quote',
                            'indexquote',
                            'trades',
                            'depth',
                            'fundingrate',
                        ),
                    ),
                ),
                'private' => array(
                    'account' => array(
                        'get' => array(
                            'wallet', // ?account_type=1
                            'deposit/address', // ?currency=USDT-TRC20
                            'withdraw/charge', // ?currency=BTC
                            'deposit-withdraw/history', // ?limit=10&offset=1&operationType=withdraw
                            'deposit-withdraw/detail', // ?id=1679952
                        ),
                        'post' => array(
                            'withdraw/apply',
                        ),
                    ),
                    'spot' => array(
                        'get' => array(
                            'wallet',
                            'order_detail',
                            'orders',
                            'trades',
                        ),
                        'post' => array(
                            'submit_order', // https://api-cloud.bitmart.com/spot/v1/submit_order
                            'cancel_order', // https://api-cloud.bitmart.com/spot/v2/cancel_order
                            'cancel_orders',
                        ),
                    ),
                    'contract' => array(
                        'get' => array(
                            'userOrders',
                            'userOrderInfo',
                            'userTrades',
                            'orderTrades',
                            'accounts',
                            'userPositions',
                            'userLiqRecords',
                            'positionFee',
                        ),
                        'post' => array(
                            'batchOrders',
                            'submitOrder',
                            'cancelOrders',
                            'marginOper',
                        ),
                    ),
                ),
            ),
            'timeframes' => array(
                '1m' => 1,
                '3m' => 3,
                '5m' => 5,
                '15m' => 15,
                '30m' => 30,
                '45m' => 45,
                '1h' => 60,
                '2h' => 120,
                '3h' => 180,
                '4h' => 240,
                '1d' => 1440,
                '1w' => 10080,
                '1M' => 43200,
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.0025,
                    'maker' => 0.0025,
                    'tiers' => array(
                        'taker' => [
                            [0, 0.20 / 100],
                            [10, 0.18 / 100],
                            [50, 0.16 / 100],
                            [250, 0.14 / 100],
                            [1000, 0.12 / 100],
                            [5000, 0.10 / 100],
                            [25000, 0.08 / 100],
                            [50000, 0.06 / 100],
                        ],
                        'maker' => [
                            [0, 0.1 / 100],
                            [10, 0.09 / 100],
                            [50, 0.08 / 100],
                            [250, 0.07 / 100],
                            [1000, 0.06 / 100],
                            [5000, 0.05 / 100],
                            [25000, 0.04 / 100],
                            [50000, 0.03 / 100],
                        ],
                    ),
                ),
            ),
            'precisionMode' => TICK_SIZE,
            'exceptions' => array(
                'exact' => array(
                    // general errors
                    '30000' => '\\ccxt\\ExchangeError', // 404, Not found
                    '30001' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-KEY is empty
                    '30002' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-KEY not found
                    '30003' => '\\ccxt\\AccountSuspended', // 401, Header X-BM-KEY has frozen
                    '30004' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-SIGN is empty
                    '30005' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-SIGN is wrong
                    '30006' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-TIMESTAMP is empty
                    '30007' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-TIMESTAMP range. Within a minute
                    '30008' => '\\ccxt\\AuthenticationError', // 401, Header X-BM-TIMESTAMP invalid format
                    '30010' => '\\ccxt\\PermissionDenied', // 403, IP is forbidden. We recommend enabling IP whitelist for API trading. After that reauth your account
                    '30011' => '\\ccxt\\AuthenticationError', // 403, Header X-BM-KEY over expire time
                    '30012' => '\\ccxt\\AuthenticationError', // 403, Header X-BM-KEY is forbidden to request it
                    '30013' => '\\ccxt\\RateLimitExceeded', // 429, Request too many requests
                    '30014' => '\\ccxt\\ExchangeNotAvailable', // 503, Service unavailable
                    // funding account errors
                    '60000' => '\\ccxt\\BadRequest', // 400, Invalid request (maybe the body is empty, or the int parameter passes string data)
                    '60001' => '\\ccxt\\BadRequest', // 400, Asset account type does not exist
                    '60002' => '\\ccxt\\BadRequest', // 400, currency does not exist
                    '60003' => '\\ccxt\\ExchangeError', // 400, Currency has been closed recharge channel, if there is any problem, please consult customer service
                    '60004' => '\\ccxt\\ExchangeError', // 400, Currency has been closed withdraw channel, if there is any problem, please consult customer service
                    '60005' => '\\ccxt\\ExchangeError', // 400, Minimum amount is %s
                    '60006' => '\\ccxt\\ExchangeError', // 400, Maximum withdraw precision is %d
                    '60007' => '\\ccxt\\InvalidAddress', // 400, Only withdrawals from added addresses are allowed
                    '60008' => '\\ccxt\\InsufficientFunds', // 400, Balance not enough
                    '60009' => '\\ccxt\\ExchangeError', // 400, Beyond the limit
                    '60010' => '\\ccxt\\ExchangeError', // 400, Withdraw id or deposit id not found
                    '60011' => '\\ccxt\\InvalidAddress', // 400, Address is not valid
                    '60012' => '\\ccxt\\ExchangeError', // 400, This action is not supported in this currency(If IOTA, HLX recharge and withdraw calls are prohibited)
                    '60020' => '\\ccxt\\PermissionDenied', // 403, Your account is not allowed to recharge
                    '60021' => '\\ccxt\\PermissionDenied', // 403, Your account is not allowed to withdraw
                    '60022' => '\\ccxt\\PermissionDenied', // 403, No withdrawals for 24 hours
                    '60030' => '\\ccxt\\BadRequest', // 405, Method Not Allowed
                    '60031' => '\\ccxt\\BadRequest', // 415, Unsupported Media Type
                    '60050' => '\\ccxt\\ExchangeError', // 500, User account not found
                    '60051' => '\\ccxt\\ExchangeError', // 500, Internal Server Error
                    // spot errors
                    '50000' => '\\ccxt\\BadRequest', // 400, Bad Request
                    '50001' => '\\ccxt\\BadSymbol', // 400, Symbol not found
                    '50002' => '\\ccxt\\BadRequest', // 400, From Or To format error
                    '50003' => '\\ccxt\\BadRequest', // 400, Step format error
                    '50004' => '\\ccxt\\BadRequest', // 400, Kline size over 500
                    '50005' => '\\ccxt\\OrderNotFound', // 400, Order Id not found
                    '50006' => '\\ccxt\\InvalidOrder', // 400, Minimum size is %s
                    '50007' => '\\ccxt\\InvalidOrder', // 400, Maximum size is %s
                    '50008' => '\\ccxt\\InvalidOrder', // 400, Minimum price is %s
                    '50009' => '\\ccxt\\InvalidOrder', // 400, Minimum count*price is %s
                    '50010' => '\\ccxt\\InvalidOrder', // 400, RequestParam size is required
                    '50011' => '\\ccxt\\InvalidOrder', // 400, RequestParam price is required
                    '50012' => '\\ccxt\\InvalidOrder', // 400, RequestParam notional is required
                    '50013' => '\\ccxt\\InvalidOrder', // 400, Maximum limit*offset is %d
                    '50014' => '\\ccxt\\BadRequest', // 400, RequestParam limit is required
                    '50015' => '\\ccxt\\BadRequest', // 400, Minimum limit is 1
                    '50016' => '\\ccxt\\BadRequest', // 400, Maximum limit is %d
                    '50017' => '\\ccxt\\BadRequest', // 400, RequestParam offset is required
                    '50018' => '\\ccxt\\BadRequest', // 400, Minimum offset is 1
                    '50019' => '\\ccxt\\BadRequest', // 400, Maximum price is %s
                    // '50019' => '\\ccxt\\ExchangeError', // 400, Invalid status. validate status is [1=Failed, 2=Success, 3=Frozen Failed, 4=Frozen Success, 5=Partially Filled, 6=Fully Fulled, 7=Canceling, 8=Canceled
                    '50020' => '\\ccxt\\InsufficientFunds', // 400, Balance not enough
                    '50021' => '\\ccxt\\BadRequest', // 400, Invalid %s
                    '50022' => '\\ccxt\\ExchangeNotAvailable', // 400, Service unavailable
                    '50023' => '\\ccxt\\BadSymbol', // 400, This Symbol can't place order by api
                    '53000' => '\\ccxt\\AccountSuspended', // 403, Your account is frozen due to security policies. Please contact customer service
                    '57001' => '\\ccxt\\BadRequest', // 405, Method Not Allowed
                    '58001' => '\\ccxt\\BadRequest', // 415, Unsupported Media Type
                    '59001' => '\\ccxt\\ExchangeError', // 500, User account not found
                    '59002' => '\\ccxt\\ExchangeError', // 500, Internal Server Error
                    // contract errors
                    '40001' => '\\ccxt\\ExchangeError', // 400, Cloud account not found
                    '40002' => '\\ccxt\\ExchangeError', // 400, out_trade_no not found
                    '40003' => '\\ccxt\\ExchangeError', // 400, out_trade_no already existed
                    '40004' => '\\ccxt\\ExchangeError', // 400, Cloud account count limit
                    '40005' => '\\ccxt\\ExchangeError', // 400, Transfer vol precision error
                    '40006' => '\\ccxt\\PermissionDenied', // 400, Invalid ip error
                    '40007' => '\\ccxt\\BadRequest', // 400, Parse parameter error
                    '40008' => '\\ccxt\\InvalidNonce', // 400, Check nonce error
                    '40009' => '\\ccxt\\BadRequest', // 400, Check ver error
                    '40010' => '\\ccxt\\BadRequest', // 400, Not found func error
                    '40011' => '\\ccxt\\BadRequest', // 400, Invalid request
                    '40012' => '\\ccxt\\ExchangeError', // 500, System error
                    '40013' => '\\ccxt\\ExchangeError', // 400, Access too often" CLIENT_TIME_INVALID, "Please check your system time.
                    '40014' => '\\ccxt\\BadSymbol', // 400, This contract is offline
                    '40015' => '\\ccxt\\BadSymbol', // 400, This contract's exchange has been paused
                    '40016' => '\\ccxt\\InvalidOrder', // 400, This order would trigger user position liquidate
                    '40017' => '\\ccxt\\InvalidOrder', // 400, It is not possible to open and close simultaneously in the same position
                    '40018' => '\\ccxt\\InvalidOrder', // 400, Your position is closed
                    '40019' => '\\ccxt\\ExchangeError', // 400, Your position is in liquidation delegating
                    '40020' => '\\ccxt\\InvalidOrder', // 400, Your position volume is not enough
                    '40021' => '\\ccxt\\ExchangeError', // 400, The position is not exsit
                    '40022' => '\\ccxt\\ExchangeError', // 400, The position is not isolated
                    '40023' => '\\ccxt\\ExchangeError', // 400, The position would liquidate when sub margin
                    '40024' => '\\ccxt\\ExchangeError', // 400, The position would be warnning of liquidation when sub margin
                    '40025' => '\\ccxt\\ExchangeError', // 400, The position’s margin shouldn’t be lower than the base limit
                    '40026' => '\\ccxt\\ExchangeError', // 400, You cross margin position is in liquidation delegating
                    '40027' => '\\ccxt\\InsufficientFunds', // 400, You contract account available balance not enough
                    '40028' => '\\ccxt\\PermissionDenied', // 400, Your plan order's count is more than system maximum limit.
                    '40029' => '\\ccxt\\InvalidOrder', // 400, The order's leverage is too large.
                    '40030' => '\\ccxt\\InvalidOrder', // 400, The order's leverage is too small.
                    '40031' => '\\ccxt\\InvalidOrder', // 400, The deviation between current price and trigger price is too large.
                    '40032' => '\\ccxt\\InvalidOrder', // 400, The plan order's life cycle is too long.
                    '40033' => '\\ccxt\\InvalidOrder', // 400, The plan order's life cycle is too short.
                    '40034' => '\\ccxt\\BadSymbol', // 400, This contract is not found
                ),
                'broad' => array(),
            ),
            'commonCurrencies' => array(
                'COT' => 'Community Coin',
                'CPC' => 'CPCoin',
                'ONE' => 'Menlo One',
                'PLA' => 'Plair',
            ),
            'options' => array(
                'defaultType' => 'spot', // 'spot', 'swap'
                'fetchBalance' => array(
                    'type' => 'spot', // 'spot', 'swap', 'contract', 'account'
                ),
                'createMarketBuyOrderRequiresPrice' => true,
            ),
        ));
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicSystemGetTime ($params);
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"c4e5e5b7-fe9f-4191-89f7-53f6c5bf9030",
        //         "$data":{
        //             "server_time":1599843709578
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->safe_integer($data, 'server_time');
    }

    public function fetch_status($params = array ()) {
        $options = $this->safe_value($this->options, 'fetchBalance', array());
        $defaultType = $this->safe_string($this->options, 'defaultType');
        $type = $this->safe_string($options, 'type', $defaultType);
        $type = $this->safe_string($params, 'type', $type);
        $params = $this->omit($params, 'type');
        $response = $this->publicSystemGetService ($params);
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "serivce":array(
        //                 array(
        //                     "title" => "Spot API Stop",
        //                     "service_type" => "spot",
        //                     "$status" => "2",
        //                     "start_time" => 1527777538000,
        //                     "end_time" => 1527777538000
        //                 ),
        //                 {
        //                     "title" => "Contract API Stop",
        //                     "service_type" => "contract",
        //                     "$status" => "2",
        //                     "start_time" => 1527777538000,
        //                     "end_time" => 1527777538000
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $services = $this->safe_value($data, 'service', array());
        $servicesByType = $this->index_by($services, 'service_type');
        if (($type === 'swap') || ($type === 'future')) {
            $type = 'contract';
        }
        $service = $this->safe_value($servicesByType, $type);
        $status = null;
        $eta = null;
        if ($service !== null) {
            $statusCode = $this->safe_integer($service, 'status');
            if ($statusCode === 2) {
                $status = 'ok';
            } else {
                $status = 'maintenance';
                $eta = $this->safe_integer($service, 'end_time');
            }
        }
        $this->status = array_merge($this->status, array(
            'status' => $status,
            'updated' => $this->milliseconds(),
            'eta' => $eta,
        ));
        return $this->status;
    }

    public function fetch_spot_markets($params = array ()) {
        $response = $this->publicSpotGetSymbolsDetails ($params);
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"a67c9146-086d-4d3f-9897-5636a9bb26e1",
        //         "$data":{
        //             "$symbols":array(
        //                 array(
        //                     "$symbol":"PRQ_BTC",
        //                     "symbol_id":1232,
        //                     "base_currency":"PRQ",
        //                     "quote_currency":"BTC",
        //                     "quote_increment":"1.0000000000",
        //                     "base_min_size":"1.0000000000",
        //                     "base_max_size":"10000000.0000000000",
        //                     "price_min_precision":8,
        //                     "price_max_precision":10,
        //                     "expiration":"NA",
        //                     "min_buy_amount":"0.0001000000",
        //                     "min_sell_amount":"0.0001000000"
        //                 ),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $symbols = $this->safe_value($data, 'symbols', array());
        $result = array();
        for ($i = 0; $i < count($symbols); $i++) {
            $market = $symbols[$i];
            $id = $this->safe_string($market, 'symbol');
            $numericId = $this->safe_integer($market, 'symbol_id');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quote_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            //
            // https://github.com/bitmartexchange/bitmart-official-api-docs/blob/master/rest/public/symbols_details.md#$response-details
            // from the above API doc:
            // quote_increment Minimum order price as well as the price increment
            // price_min_precision Minimum price $precision (digit) used to query price and kline
            // price_max_precision Maximum price $precision (digit) used to query price and kline
            //
            // the docs are wrong => https://github.com/ccxt/ccxt/issues/5612
            //
            $pricePrecision = $this->safe_integer($market, 'price_max_precision');
            $precision = array(
                'amount' => $this->safe_float($market, 'base_min_size'),
                'price' => floatval($this->decimal_to_precision(pow(10, -$pricePrecision), ROUND, 10)),
            );
            $minBuyCost = $this->safe_float($market, 'min_buy_amount');
            $minSellCost = $this->safe_float($market, 'min_sell_amount');
            $minCost = max ($minBuyCost, $minSellCost);
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'base_min_size'),
                    'max' => $this->safe_float($market, 'base_max_size'),
                ),
                'price' => array(
                    'min' => null,
                    'max' => null,
                ),
                'cost' => array(
                    'min' => $minCost,
                    'max' => null,
                ),
            );
            $result[] = array(
                'id' => $id,
                'numericId' => $numericId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'type' => 'spot',
                'spot' => true,
                'future' => false,
                'swap' => false,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
                'active' => null,
            );
        }
        return $result;
    }

    public function fetch_contract_markets($params = array ()) {
        $response = $this->publicContractGetContracts ($params);
        //
        //     {
        //         "errno":"OK",
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"7fcedfb5-a660-4780-8a7a-b36a9e2159f7",
        //         "$data":{
        //             "$contracts":array(
        //                 array(
        //                     "$contract":array(
        //                         "contract_id":1,
        //                         "index_id":1,
        //                         "name":"BTCUSDT",
        //                         "display_name":"BTCUSDT永续合约",
        //                         "display_name_en":"BTCUSDT_SWAP",
        //                         "contract_type":1,
        //                         "base_coin":"BTC",
        //                         "quote_coin":"USDT",
        //                         "price_coin":"BTC",
        //                         "exchange":"*",
        //                         "contract_size":"0.0001",
        //                         "begin_at":"2018-08-17T04:00:00Z",
        //                         "delive_at":"2020-08-15T12:00:00Z",
        //                         "delivery_cycle":28800,
        //                         "min_leverage":"1",
        //                         "max_leverage":"100",
        //                         "price_unit":"0.1",
        //                         "vol_unit":"1",
        //                         "value_unit":"0.0001",
        //                         "min_vol":"1",
        //                         "max_vol":"300000",
        //                         "liquidation_warn_ratio":"0.85",
        //                         "fast_liquidation_ratio":"0.8",
        //                         "settgle_type":1,
        //                         "open_type":3,
        //                         "compensate_type":1,
        //                         "status":3,
        //                         "block":1,
        //                         "rank":1,
        //                         "created_at":"2018-07-12T19:16:57Z",
        //                         "depth_bord":"1.001",
        //                         "base_coin_zh":"比特币",
        //                         "base_coin_en":"Bitcoin",
        //                         "max_rate":"0.00375",
        //                         "min_rate":"-0.00375"
        //                     ),
        //                     "risk_limit":array("contract_id":1,"base_limit":"1000000","step":"500000","maintenance_margin":"0.005","initial_margin":"0.01"),
        //                     "fee_config":array("contract_id":1,"maker_fee":"-0.0003","taker_fee":"0.001","settlement_fee":"0","created_at":"2018-07-12T20:47:22Z"),
        //                     "plan_order_config":array("contract_id":0,"min_scope":"0.001","max_scope":"2","max_count":10,"min_life_cycle":24,"max_life_cycle":168)
        //                 ),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $contracts = $this->safe_value($data, 'contracts', array());
        $result = array();
        for ($i = 0; $i < count($contracts); $i++) {
            $market = $contracts[$i];
            $contract = $this->safe_value($market, 'contract', array());
            $id = $this->safe_string($contract, 'contract_id');
            $numericId = $this->safe_integer($contract, 'contract_id');
            $baseId = $this->safe_string($contract, 'base_coin');
            $quoteId = $this->safe_string($contract, 'quote_coin');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $this->safe_string($contract, 'name');
            //
            // https://github.com/bitmartexchange/bitmart-official-api-docs/blob/master/rest/public/symbols_details.md#$response-details
            // from the above API doc:
            // quote_increment Minimum order price as well as the price increment
            // price_min_precision Minimum price $precision (digit) used to query price and kline
            // price_max_precision Maximum price $precision (digit) used to query price and kline
            //
            // the docs are wrong => https://github.com/ccxt/ccxt/issues/5612
            //
            $amountPrecision = $this->safe_float($contract, 'vol_unit');
            $pricePrecision = $this->safe_float($contract, 'price_unit');
            $precision = array(
                'amount' => $amountPrecision,
                'price' => $pricePrecision,
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($contract, 'min_vol'),
                    'max' => $this->safe_float($contract, 'max_vol'),
                ),
                'price' => array(
                    'min' => null,
                    'max' => null,
                ),
                'cost' => array(
                    'min' => null,
                    'max' => null,
                ),
            );
            $contractType = $this->safe_value($contract, 'contract_type');
            $future = false;
            $swap = false;
            $type = 'contract';
            if ($contractType === 1) {
                $type = 'swap';
                $swap = true;
            } else if ($contractType === 2) {
                $type = 'future';
                $future = true;
            }
            $feeConfig = $this->safe_value($market, 'fee_config', array());
            $maker = $this->safe_float($feeConfig, 'maker_fee');
            $taker = $this->safe_float($feeConfig, 'taker_fee');
            $result[] = array(
                'id' => $id,
                'numericId' => $numericId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'maker' => $maker,
                'taker' => $taker,
                'type' => $type,
                'spot' => false,
                'future' => $future,
                'swap' => $swap,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
                'active' => null,
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $spotMarkets = $this->fetch_spot_markets();
        $contractMarkets = $this->fetch_contract_markets();
        $allMarkets = $this->array_concat($spotMarkets, $contractMarkets);
        return $allMarkets;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // spot
        //
        //     {
        //         "$symbol":"ETH_BTC",
        //         "last_price":"0.036037",
        //         "quote_volume_24h":"4380.6660000000",
        //         "base_volume_24h":"159.3582006712",
        //         "high_24h":"0.036972",
        //         "low_24h":"0.035524",
        //         "open_24h":"0.036561",
        //         "close_24h":"0.036037",
        //         "best_ask":"0.036077",
        //         "best_ask_size":"9.9500",
        //         "best_bid":"0.035983",
        //         "best_bid_size":"4.2792",
        //         "fluctuation":"-0.0143",
        //         "url":"https://www.bitmart.com/trade?$symbol=ETH_BTC"
        //     }
        //
        // contract
        //
        //     {
        //         "last_price":"422.2",
        //         "$open":"430.5",
        //         "close":"422.2",
        //         "low":"421.9",
        //         "high":"436.9",
        //         "avg_price":"430.8569900089815372072",
        //         "volume":"2720",
        //         "total_volume":"18912248",
        //         "$timestamp":1597631495,
        //         "rise_fall_rate":"-0.0192799070847851336",
        //         "rise_fall_value":"-8.3",
        //         "contract_id":2,
        //         "position_size":"3067404",
        //         "volume_day":"9557384",
        //         "amount24":"80995537.0919999999999974153",
        //         "base_coin_volume":"189122.48",
        //         "quote_coin_volume":"81484742.475833810590837937856",
        //         "pps":"1274350547",
        //         "index_price":"422.135",
        //         "fair_price":"422.147253318507",
        //         "depth_price":array("bid_price":"421.9","ask_price":"422","mid_price":"421.95"),
        //         "fair_basis":"0.000029027013",
        //         "fair_value":"0.012253318507",
        //         "rate":array("quote_rate":"0.0006","base_rate":"0.0003","interest_rate":"0.000099999999"),
        //         "premium_index":"0.000045851604",
        //         "funding_rate":"0.000158",
        //         "next_funding_rate":"0.000099999999",
        //         "next_funding_at":"2020-08-17T04:00:00Z"
        //     }
        //
        $timestamp = $this->safe_timestamp($ticker, 'timestamp', $this->milliseconds());
        $marketId = $this->safe_string_2($ticker, 'symbol', 'contract_id');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $last = $this->safe_float_2($ticker, 'close_24h', 'last_price');
        $percentage = $this->safe_float($ticker, 'fluctuation', 'rise_fall_rate');
        if ($percentage !== null) {
            $percentage *= 100;
        }
        $baseVolume = $this->safe_float_2($ticker, 'base_volume_24h', 'base_coin_volume');
        $quoteVolume = $this->safe_float_2($ticker, 'quote_volume_24h', 'quote_coin_volume');
        $vwap = $this->vwap($baseVolume, $quoteVolume);
        $open = $this->safe_float_2($ticker, 'open_24h', 'open');
        $average = null;
        if (($last !== null) && ($open !== null)) {
            $average = $this->sum($last, $open) / 2;
        }
        $average = $this->safe_float($ticker, 'avg_price', $average);
        $price = $this->safe_value($ticker, 'depth_price', $ticker);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float_2($ticker, 'high', 'high_24h'),
            'low' => $this->safe_float_2($ticker, 'low', 'low_24h'),
            'bid' => $this->safe_float($price, 'best_bid', 'bid_price'),
            'bidVolume' => $this->safe_float($ticker, 'best_bid_size'),
            'ask' => $this->safe_float($price, 'best_ask', 'ask_price'),
            'askVolume' => $this->safe_float($ticker, 'best_ask_size'),
            'vwap' => $vwap,
            'open' => $this->safe_float($ticker, 'open_24h'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array();
        $method = null;
        if ($market['swap'] || $market['future']) {
            $method = 'publicContractGetTickers';
            $request['contractID'] = $market['id'];
        } else if ($market['spot']) {
            $method = 'publicSpotGetTicker';
            $request['symbol'] = $market['id'];
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"6aa5b923-2f57-46e3-876d-feca190e0b82",
        //         "$data":{
        //             "$tickers":array(
        //                 {
        //                     "$symbol":"ETH_BTC",
        //                     "last_price":"0.036037",
        //                     "quote_volume_24h":"4380.6660000000",
        //                     "base_volume_24h":"159.3582006712",
        //                     "high_24h":"0.036972",
        //                     "low_24h":"0.035524",
        //                     "open_24h":"0.036561",
        //                     "close_24h":"0.036037",
        //                     "best_ask":"0.036077",
        //                     "best_ask_size":"9.9500",
        //                     "best_bid":"0.035983",
        //                     "best_bid_size":"4.2792",
        //                     "fluctuation":"-0.0143",
        //                     "url":"https://www.bitmart.com/trade?$symbol=ETH_BTC"
        //                 }
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "errno":"OK",
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"d09b57c4-d99b-4a13-91a8-2df98f889909",
        //         "$data":{
        //             "$tickers":array(
        //                 {
        //                     "last_price":"422.2",
        //                     "open":"430.5",
        //                     "close":"422.2",
        //                     "low":"421.9",
        //                     "high":"436.9",
        //                     "avg_price":"430.8569900089815372072",
        //                     "volume":"2720",
        //                     "total_volume":"18912248",
        //                     "timestamp":1597631495,
        //                     "rise_fall_rate":"-0.0192799070847851336",
        //                     "rise_fall_value":"-8.3",
        //                     "contract_id":2,
        //                     "position_size":"3067404",
        //                     "volume_day":"9557384",
        //                     "amount24":"80995537.0919999999999974153",
        //                     "base_coin_volume":"189122.48",
        //                     "quote_coin_volume":"81484742.475833810590837937856",
        //                     "pps":"1274350547",
        //                     "index_price":"422.135",
        //                     "fair_price":"422.147253318507",
        //                     "depth_price":array("bid_price":"421.9","ask_price":"422","mid_price":"421.95"),
        //                     "fair_basis":"0.000029027013",
        //                     "fair_value":"0.012253318507",
        //                     "rate":array("quote_rate":"0.0006","base_rate":"0.0003","interest_rate":"0.000099999999"),
        //                     "premium_index":"0.000045851604",
        //                     "funding_rate":"0.000158",
        //                     "next_funding_rate":"0.000099999999",
        //                     "next_funding_at":"2020-08-17T04:00:00Z"
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $tickers = $this->safe_value($data, 'tickers', array());
        $tickersById = $this->index_by($tickers, 'symbol');
        $ticker = $this->safe_value($tickersById, $market['id']);
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $method = null;
        if (($type === 'swap') || ($type === 'future')) {
            $method = 'publicContractGetTickers';
        } else if ($type === 'spot') {
            $method = 'publicSpotGetTicker';
        }
        $response = $this->$method ($params);
        $data = $this->safe_value($response, 'data', array());
        $tickers = $this->safe_value($data, 'tickers', array());
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $this->parse_ticker($tickers[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicAccountGetCurrencies ($params);
        //
        //     {
        //         "message":"OK",
        //         "$code":1000,
        //         "trace":"8c768b3c-025f-413f-bec5-6d6411d46883",
        //         "$data":{
        //             "$currencies":array(
        //                 array("$currency":"MATIC","$name":"Matic Network","withdraw_enabled":true,"deposit_enabled":true),
        //                 array("$currency":"KTN","$name":"Kasoutuuka News","withdraw_enabled":true,"deposit_enabled":false),
        //                 array("$currency":"BRT","$name":"Berith","withdraw_enabled":true,"deposit_enabled":true),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $currencies = $this->safe_value($data, 'currencies', array());
        $result = array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'currency');
            $code = $this->safe_currency_code($id);
            $name = $this->safe_string($currency, 'name');
            $withdrawEnabled = $this->safe_value($currency, 'withdraw_enabled');
            $depositEnabled = $this->safe_value($currency, 'deposit_enabled');
            $active = $withdrawEnabled && $depositEnabled;
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'info' => $currency, // the original payload
                'active' => $active,
                'fee' => null,
                'precision' => null,
                'limits' => array(
                    'amount' => array( 'min' => null, 'max' => null ),
                    'price' => array( 'min' => null, 'max' => null ),
                    'cost' => array( 'min' => null, 'max' => null ),
                    'withdraw' => array( 'min' => null, 'max' => null ),
                ),
            );
        }
        return $result;
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array();
        $method = null;
        if ($market['spot']) {
            $method = 'publicSpotGetSymbolsBook';
            $request['symbol'] = $market['id'];
            // $request['precision'] = 4; // optional price precision / depth level whose range is defined in $symbol details
        } else if ($market['swap'] || $market['future']) {
            $method = 'publicContractGetDepth';
            $request['contractID'] = $market['id'];
            if ($limit !== null) {
                $request['count'] = $limit; // returns all records if size is omitted
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"8254f8fc-431d-404f-ad9a-e716339f66c7",
        //         "$data":{
        //             "buys":array(
        //                 array("amount":"4.7091","total":"4.71","price":"0.034047","count":"1"),
        //                 array("amount":"5.7439","total":"10.45","price":"0.034039","count":"1"),
        //                 array("amount":"2.5249","total":"12.98","price":"0.032937","count":"1"),
        //             ),
        //             "sells":array(
        //                 array("amount":"41.4365","total":"41.44","price":"0.034174","count":"1"),
        //                 array("amount":"4.2317","total":"45.67","price":"0.034183","count":"1"),
        //                 array("amount":"0.3000","total":"45.97","price":"0.034240","count":"1"),
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "errno":"OK",
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"c330dfca-ca5b-4f15-b350-9fef3f049b4f",
        //         "$data":{
        //             "sells":array(
        //                 array("price":"347.6","vol":"6678"),
        //                 array("price":"347.7","vol":"3452"),
        //                 array("price":"347.8","vol":"6331"),
        //             ),
        //             "buys":array(
        //                 array("price":"347.5","vol":"6222"),
        //                 array("price":"347.4","vol":"20979"),
        //                 array("price":"347.3","vol":"15179"),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        if ($market['spot']) {
            return $this->parse_order_book($data, null, 'buys', 'sells', 'price', 'amount');
        } else if ($market['swap'] || $market['future']) {
            return $this->parse_order_book($data, null, 'buys', 'sells', 'price', 'vol');
        }
    }

    public function parse_trade($trade, $market = null) {
        //
        // public fetchTrades spot
        //
        //     {
        //         "$amount":"0.005703",
        //         "order_time":1599652045394,
        //         "$price":"0.034029",
        //         "count":"0.1676",
        //         "$type":"sell"
        //     }
        //
        // public fetchTrades contract, private fetchMyTrades contract
        //
        //     {
        //         "order_id":109159616160,
        //         "trade_id":109159616197,
        //         "contract_id":2,
        //         "deal_price":"347.6",
        //         "deal_vol":"5623",
        //         "make_fee":"-5.8636644",
        //         "take_fee":"9.772774",
        //         "created_at":"2020-09-09T11:49:50.749170536Z",
        //         "$way":1,
        //         "fluctuation":"0"
        //     }
        //
        // private fetchMyTrades spot
        //
        //     {
        //         "detail_id":256348632,
        //         "order_id":2147484350,
        //         "$symbol":"BTC_USDT",
        //         "create_time":1590462303000,
        //         "$side":"buy",
        //         "fees":"0.00001350",
        //         "fee_coin_name":"BTC",
        //         "notional":"88.00000000",
        //         "price_avg":"8800.00",
        //         "size":"0.01000",
        //         "exec_type":"M"
        //     }
        //
        $id = $this->safe_string_2($trade, 'trade_id', 'detail_id');
        $timestamp = $this->safe_integer_2($trade, 'order_time', 'create_time');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        }
        $type = null;
        $way = $this->safe_integer($trade, 'way');
        $side = $this->safe_string_lower_2($trade, 'type', 'side');
        if (($side === null) && ($way !== null)) {
            if ($way < 5) {
                $side = 'buy';
            } else {
                $side = 'sell';
            }
        }
        $takerOrMaker = null;
        $execType = $this->safe_string($trade, 'exec_type');
        if ($execType !== null) {
            $takerOrMaker = ($execType === 'M') ? 'maker' : 'taker';
        }
        $price = $this->safe_float_2($trade, 'price', 'deal_price');
        $price = $this->safe_float($trade, 'price_avg', $price);
        $amount = $this->safe_float_2($trade, 'amount', 'deal_vol');
        $amount = $this->safe_float($trade, 'size', $amount);
        $cost = $this->safe_float_2($trade, 'count', 'notional');
        if (($cost === null) && ($price !== null) && ($amount !== null)) {
            $cost = $amount * $price;
        }
        $orderId = $this->safe_integer($trade, 'order_id');
        $marketId = $this->safe_string_2($trade, 'contract_id', 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $feeCost = $this->safe_float($trade, 'fees');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'fee_coin_name');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
            if (($feeCurrencyCode === null) && ($market !== null)) {
                $feeCurrencyCode = ($side === 'buy') ? $market['base'] : $market['quote'];
            }
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'takerOrMaker' => $takerOrMaker,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $method = null;
        if ($market['spot']) {
            $request['symbol'] = $market['id'];
            $method = 'publicSpotGetSymbolsTrades';
        } else if ($market['swap'] || $market['future']) {
            $method = 'publicContractGetTrades';
            $request['contractID'] = $market['id'];
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"222d74c0-8f6d-49d9-8e1b-98118c50eeba",
        //         "$data":{
        //             "$trades":array(
        //                 array(
        //                     "amount":"0.005703",
        //                     "order_time":1599652045394,
        //                     "price":"0.034029",
        //                     "count":"0.1676",
        //                     "type":"sell"
        //                 ),
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "errno":"OK",
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"782bc746-b86e-43bf-8d1a-c68b479c9bdd",
        //         "$data":{
        //             "$trades":array(
        //                 {
        //                     "order_id":109159616160,
        //                     "trade_id":109159616197,
        //                     "contract_id":2,
        //                     "deal_price":"347.6",
        //                     "deal_vol":"5623",
        //                     "make_fee":"-5.8636644",
        //                     "take_fee":"9.772774",
        //                     "created_at":"2020-09-09T11:49:50.749170536Z",
        //                     "way":1,
        //                     "fluctuation":"0"
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $trades = $this->safe_value($data, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        // spot
        //
        //     {
        //         "last_price":"0.034987",
        //         "timestamp":1598787420,
        //         "volume":"1.0198",
        //         "open":"0.035007",
        //         "close":"0.034987",
        //         "high":"0.035007",
        //         "low":"0.034986"
        //     }
        //
        // contract
        //
        //     {
        //         "low":"404.4",
        //         "high":"404.4",
        //         "open":"404.4",
        //         "close":"404.4",
        //         "last_price":"404.4",
        //         "avg_price":"404.4",
        //         "volume":"7670",
        //         "timestamp":1598758441,
        //         "rise_fall_rate":"0",
        //         "rise_fall_value":"0",
        //         "base_coin_volume":"76.7",
        //         "quote_coin_volume":"31017.48"
        //     }
        //
        return array(
            $this->safe_timestamp($ohlcv, 'timestamp'),
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
        $type = $market['type'];
        $method = null;
        $request = array();
        $duration = $this->parse_timeframe($timeframe);
        if ($type === 'spot') {
            $method = 'publicSpotGetSymbolsKline';
            $request['symbol'] = $market['id'];
            $request['step'] = $this->timeframes[$timeframe];
            // the exchange will return an empty array if more than 500 candles is requested
            $maxLimit = 500;
            if ($limit === null) {
                $limit = $maxLimit;
            }
            $limit = min ($maxLimit, $limit);
            if ($since === null) {
                $end = intval($this->milliseconds() / 1000);
                $start = $end - $limit * $duration;
                $request['from'] = $start;
                $request['to'] = $end;
            } else {
                $start = intval($since / 1000);
                $end = $this->sum($start, $limit * $duration);
                $request['from'] = $start;
                $request['to'] = $end;
            }
        } else if (($type === 'swap') || ($type === 'future')) {
            $method = 'publicContractGetQuote';
            $request['contractID'] = $market['id'];
            $defaultLimit = 500;
            if ($limit === null) {
                $limit = $defaultLimit;
            }
            if ($since === null) {
                $end = intval($this->milliseconds() / 1000);
                $start = $end - $limit * $duration;
                $request['startTime'] = $start;
                $request['endTime'] = $end;
            } else {
                $start = intval($since / 1000);
                $end = $this->sum($start, $limit * $duration);
                $request['startTime'] = $start;
                $request['endTime'] = $end;
            }
            $request['unit'] = $this->timeframes[$timeframe];
            $request['resolution'] = 'M';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"80d86378-ab4e-4c70-819e-b42146cf87ad",
        //         "$data":{
        //             "$klines":array(
        //                 array("last_price":"0.034987","timestamp":1598787420,"volume":"1.0198","open":"0.035007","close":"0.034987","high":"0.035007","low":"0.034986"),
        //                 array("last_price":"0.034986","timestamp":1598787480,"volume":"0.3959","open":"0.034982","close":"0.034986","high":"0.034986","low":"0.034980"),
        //                 array("last_price":"0.034978","timestamp":1598787540,"volume":"0.3259","open":"0.034987","close":"0.034978","high":"0.034987","low":"0.034977"),
        //             )
        //         }
        //     }
        //
        // swap
        //
        //     {
        //         "errno":"OK",
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"32965074-5804-4655-b693-e953e36026a0",
        //         "$data":array(
        //             array("low":"404.4","high":"404.4","open":"404.4","close":"404.4","last_price":"404.4","avg_price":"404.4","volume":"7670","timestamp":1598758441,"rise_fall_rate":"0","rise_fall_value":"0","base_coin_volume":"76.7","quote_coin_volume":"31017.48"),
        //             array("low":"404.1","high":"404.4","open":"404.4","close":"404.1","last_price":"404.1","avg_price":"404.15881086","volume":"12076","timestamp":1598758501,"rise_fall_rate":"-0.000741839762611276","rise_fall_value":"-0.3","base_coin_volume":"120.76","quote_coin_volume":"48806.2179994536"),
        //             array("low":"404","high":"404.3","open":"404.1","close":"404","last_price":"404","avg_price":"404.08918918","volume":"740","timestamp":1598758561,"rise_fall_rate":"-0.000247463499133878","rise_fall_value":"-0.1","base_coin_volume":"7.4","quote_coin_volume":"2990.259999932"),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        if (gettype($data) === 'array' && count(array_filter(array_keys($data), 'is_string')) == 0) {
            return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
        } else {
            $klines = $this->safe_value($data, 'klines', array());
            return $this->parse_ohlcvs($klines, $market, $timeframe, $since, $limit);
        }
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $method = null;
        $request = array();
        if ($market['spot']) {
            $request['symbol'] = $market['id'];
            $request['offset'] = 1; // max offset * $limit < 500
            if ($limit === null) {
                $limit = 100; // max 100
            }
            $request['limit'] = $limit;
            $method = 'privateSpotGetTrades';
        } else if ($market['swap'] || $market['future']) {
            $request['contractID'] = $market['id'];
            // $request['offset'] = 1;
            if ($limit !== null) {
                $request['size'] = $limit; // max 60
            }
            $method = 'privateContractGetUserTrades';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"a06a5c53-8e6f-42d6-8082-2ff4718d221c",
        //         "$data":{
        //             "current_page":1,
        //             "$trades":array(
        //                 array(
        //                     "detail_id":256348632,
        //                     "order_id":2147484350,
        //                     "$symbol":"BTC_USDT",
        //                     "create_time":1590462303000,
        //                     "side":"buy",
        //                     "fees":"0.00001350",
        //                     "fee_coin_name":"BTC",
        //                     "notional":"88.00000000",
        //                     "price_avg":"8800.00",
        //                     "size":"0.01000",
        //                     "exec_type":"M"
        //                 ),
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "$trades" => array(
        //                 {
        //                     "order_id" => 10116361,
        //                     "trade_id" => 10116363,
        //                     "contract_id" => 1,
        //                     "deal_price" => "16",
        //                     "deal_vol" => "10",
        //                     "make_fee" => "0.04",
        //                     "take_fee" => "0.12",
        //                     "created_at" => null,
        //                     "way" => 5,
        //                     "fluctuation" => "0"
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $trades = $this->safe_value($data, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrderTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $method = null;
        $request = array();
        if ($market['spot']) {
            $request['symbol'] = $market['id'];
            $request['order_id'] = $id;
            $method = 'privateSpotGetTrades';
        } else if ($market['swap'] || $market['future']) {
            $request['contractID'] = $market['id'];
            $request['orderID'] = $id;
            $method = 'privateContractGetOrderTrades';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"a06a5c53-8e6f-42d6-8082-2ff4718d221c",
        //         "$data":{
        //             "current_page":1,
        //             "$trades":array(
        //                 array(
        //                     "detail_id":256348632,
        //                     "order_id":2147484350,
        //                     "$symbol":"BTC_USDT",
        //                     "create_time":1590462303000,
        //                     "side":"buy",
        //                     "fees":"0.00001350",
        //                     "fee_coin_name":"BTC",
        //                     "notional":"88.00000000",
        //                     "price_avg":"8800.00",
        //                     "size":"0.01000",
        //                     "exec_type":"M"
        //                 ),
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "$trades" => array(
        //                 {
        //                     "order_id" => 10116361,
        //                     "trade_id" => 10116363,
        //                     "contract_id" => 1,
        //                     "deal_price" => "16",
        //                     "deal_vol" => "10",
        //                     "make_fee" => "0.04",
        //                     "take_fee" => "0.12",
        //                     "created_at" => null,
        //                     "way" => 5,
        //                     "fluctuation" => "0"
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $trades = $this->safe_value($data, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $method = null;
        $options = $this->safe_value($this->options, 'fetchBalance', array());
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $type = $this->safe_string($options, 'type', $defaultType);
        $type = $this->safe_string($params, 'type', $type);
        $params = $this->omit($params, 'type');
        if ($type === 'spot') {
            $method = 'privateSpotGetWallet';
        } else if ($type === 'account') {
            $method = 'privateAccountGetWallet';
        } else if (($type === 'swap') || ($type === 'future') || ($type === 'contract')) {
            $method = 'privateContractGetAccounts';
        }
        $response = $this->$method ($params);
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "$code":1000,
        //         "trace":"39069916-72f9-44c7-acde-2ad5afd21cad",
        //         "$data":{
        //             "$wallet":array(
        //                 array("id":"BTC","name":"Bitcoin","available":"0.00000062","frozen":"0.00000000"),
        //                 array("id":"ETH","name":"Ethereum","available":"0.00002277","frozen":"0.00000000"),
        //                 array("id":"BMX","name":"BitMart Token","available":"0.00000000","frozen":"0.00000000")
        //             )
        //         }
        //     }
        //
        // $account
        //
        //     {
        //         "message":"OK",
        //         "$code":1000,
        //         "trace":"5c3b7fc7-93b2-49ef-bb59-7fdc56915b59",
        //         "$data":{
        //             "$wallet":array(
        //                 array("currency":"BTC","name":"Bitcoin","available":"0.00000062","frozen":"0.00000000"),
        //                 array("currency":"ETH","name":"Ethereum","available":"0.00002277","frozen":"0.00000000")
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "$code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "accounts" => array(
        //                 {
        //                     "account_id" => 10,
        //                     "coin_code" => "USDT",
        //                     "freeze_vol" => "1201.8",
        //                     "available_vol" => "8397.65",
        //                     "cash_vol" => "0",
        //                     "realised_vol" => "-0.5",
        //                     "unrealised_vol" => "-0.5",
        //                     "earnings_vol" => "-0.5",
        //                     "created_at" => "2018-07-13T16:48:49+08:00",
        //                     "updated_at" => "2018-07-13T18:34:45.900387+08:00"
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $wallet = $this->safe_value_2($data, 'wallet', 'accounts', array());
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($wallet); $i++) {
            $balance = $wallet[$i];
            $currencyId = $this->safe_string_2($balance, 'id', 'currency');
            $currencyId = $this->safe_string($balance, 'coind_code', $currencyId);
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float_2($balance, 'available', 'available_vol');
            $account['used'] = $this->safe_float_2($balance, 'frozen', 'freeze_vol');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "order_id" => 2707217580
        //     }
        //
        // cancelOrder
        //
        //     '2707217580' // $order $id
        //
        // spot fetchOrder, fetchOrdersByStatus, fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "order_id":1736871726781,
        //         "$symbol":"BTC_USDT",
        //         "create_time":1591096004000,
        //         "$side":"sell",
        //         "$type":"$market",
        //         "$price":"0.00",
        //         "price_avg":"0.00",
        //         "size":"0.02000",
        //         "notional":"0.00000000",
        //         "filled_notional":"0.00000000",
        //         "filled_size":"0.00000",
        //         "$status":"8"
        //     }
        //
        // contract fetchOrder, fetchOrdersByStatus, fetchOpenOrders, fetchClosedOrders, fetchOrders
        //
        //     {
        //         "order_id" => 10539098,
        //         "contract_id" => 1,
        //         "position_id" => 10539088,
        //         "account_id" => 10,
        //         "$price" => "16",
        //         "vol" => "1",
        //         "done_avg_price" => "16",
        //         "done_vol" => "1",
        //         "way" => 3,
        //         "$category" => 1,
        //         "open_type" => 2,
        //         "make_fee" => "0.00025",
        //         "take_fee" => "0.012",
        //         "origin" => "",
        //         "created_at" => "2018-07-23T11:55:56.715305Z",
        //         "finished_at" => "2018-07-23T11:55:56.763941Z",
        //         "$status" => 4,
        //         "errno" => 0
        //     }
        //
        $id = null;
        if (gettype($order) === 'string') {
            $id = $order;
            $order = array();
        }
        $id = $this->safe_string($order, 'order_id', $id);
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $timestamp = $this->safe_integer($order, 'create_time', $timestamp);
        $marketId = $this->safe_string_2($order, 'symbol', 'contract_id');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $status = null;
        if ($market !== null) {
            $status = $this->parse_order_status_by_type($market['type'], $this->safe_string($order, 'status'));
        }
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float_2($order, 'price_avg', 'done_avg_price');
        $amount = $this->safe_float_2($order, 'size', 'vol');
        $cost = null;
        $filled = $this->safe_float_2($order, 'filled_size', 'done_vol');
        $remaining = null;
        if ($amount !== null) {
            if ($remaining !== null) {
                if ($filled === null) {
                    $filled = max (0, $amount - $remaining);
                }
            }
            if ($filled !== null) {
                if ($remaining === null) {
                    $remaining = max (0, $amount - $filled);
                }
                if ($cost === null) {
                    if ($average !== null) {
                        $cost = $average * $filled;
                    }
                }
            }
        }
        $side = $this->safe_string($order, 'side');
        // 1 = Open long
        // 2 = Close short
        // 3 = Close long
        // 4 = Open short
        $side = $this->safe_string($order, 'way', $side);
        $category = $this->safe_integer($order, 'category');
        $type = $this->safe_string($order, 'type');
        if ($category === 1) {
            $type = 'limit';
        } else if ($category === 2) {
            $type = 'market';
        }
        if ($type === 'market') {
            if ($price === 0.0) {
                $price = null;
            }
            if ($average === 0.0) {
                $average = null;
            }
        }
        return array(
            'id' => $id,
            'clientOrderId' => null,
            'info' => $order,
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
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        );
    }

    public function parse_order_status_by_type($type, $status) {
        $statusesByType = array(
            'spot' => array(
                '1' => 'failed', // Order failure
                '2' => 'open', // Placing order
                '3' => 'failed', // Order failure, Freeze failure
                '4' => 'open', // Order success, Pending for fulfilment
                '5' => 'open', // Partially filled
                '6' => 'closed', // Fully filled
                '7' => 'canceling', // Canceling
                '8' => 'canceled', // Canceled
            ),
            'swap' => array(
                '1' => 'open', // Submitting
                '2' => 'open', // Commissioned
                '4' => 'closed', // Completed
            ),
        );
        $statuses = $this->safe_value($statusesByType, $type, array());
        return $this->safe_string($statuses, $status, $status);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array();
        $method = null;
        if ($market['spot']) {
            $request['symbol'] = $market['id'];
            $request['side'] = $side;
            $request['type'] = $type;
            $method = 'privateSpotPostSubmitOrder';
            if ($type === 'limit') {
                $request['size'] = $this->amount_to_precision($symbol, $amount);
                $request['price'] = $this->price_to_precision($symbol, $price);
            } else if ($type === 'market') {
                // for $market buy it requires the $amount of quote currency to spend
                if ($side === 'buy') {
                    $notional = $this->safe_float($params, 'notional');
                    $createMarketBuyOrderRequiresPrice = $this->safe_value($this->options, 'createMarketBuyOrderRequiresPrice', true);
                    if ($createMarketBuyOrderRequiresPrice) {
                        if ($price !== null) {
                            if ($notional === null) {
                                $notional = $amount * $price;
                            }
                        } else if ($notional === null) {
                            throw new InvalidOrder($this->id . " createOrder() requires the $price argument with $market buy orders to calculate total order cost ($amount to spend), where cost = $amount * $price-> Supply a $price argument to createOrder() call if you want the cost to be calculated for you from $price and $amount, or, alternatively, add .options['createMarketBuyOrderRequiresPrice'] = false and supply the total cost value in the 'amount' argument or in the 'notional' extra parameter (the exchange-specific behaviour)");
                        }
                    } else {
                        $notional = ($notional === null) ? $amount : $notional;
                    }
                    $precision = $market['precision']['price'];
                    $request['notional'] = $this->decimal_to_precision($notional, TRUNCATE, $precision, $this->precisionMode);
                } else if ($side === 'sell') {
                    $request['size'] = $this->amount_to_precision($symbol, $amount);
                }
            }
        } else if ($market['swap'] || $market['future']) {
            $method = 'privateContractPostSubmitOrder';
            $request['contractID'] = $market['id'];
            if ($type === 'limit') {
                $request['category'] = 1;
            } else if ($type === 'market') {
                $request['category'] = 2;
            }
            $request['way'] = $side; // 1 = open long, 2 = close short, 3 = close long, 4 = open short
            $request['custom_id'] = $this->nonce();
            $request['open_type'] = 1; // 1 = cross margin, 2 = fixed margin
            $request['leverage'] = 1; // must meet the effective range of leverage configured in the contract
            $request['price'] = $this->price_to_precision($symbol, $price);
            $request['vol'] = $this->amount_to_precision($symbol, $amount);
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot and contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "order_id" => 2707217580
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array();
        $method = null;
        if ($market['spot']) {
            $method = 'privateSpotPostCancelOrder';
            $request['order_id'] = intval($id);
            $request['symbol'] = $market['id'];
        } else if ($market['swap'] || $market['future']) {
            $method = 'privateContractPostCancelOrders';
            $request['contractID'] = $market['id'];
            $request['orders'] = array( intval($id) );
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "$result" => true
        //         }
        //     }
        //
        // spot alternative
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => true
        //     }
        //
        // contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "succeed" => array(
        //                 2707219612
        //             ),
        //             "failed" => array()
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data');
        if ($data === true) {
            return $this->parse_order($id, $market);
        }
        $succeeded = $this->safe_value($data, 'succeed');
        if ($succeeded !== null) {
            $id = $this->safe_string($succeeded, 0);
            if ($id === null) {
                throw new InvalidOrder($this->id . ' cancelOrder() failed to cancel ' . $symbol . ' $order $id ' . $id);
            }
        } else {
            $result = $this->safe_value($data, 'result');
            if (!$result) {
                throw new InvalidOrder($this->id . ' cancelOrder() ' . $symbol . ' $order $id ' . $id . ' is filled or canceled');
            }
        }
        $order = $this->parse_order($id, $market);
        return array_merge($order, array( 'id' => $id ));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelAllOrders() requires a $symbol argument');
        }
        $side = $this->safe_string($params, 'side');
        if ($side === null) {
            throw new ArgumentsRequired($this->id . " cancelAllOrders() requires a `$side` parameter ('buy' or 'sell')");
        }
        $this->load_markets();
        $market = $this->market($symbol);
        if (!$market['spot']) {
            throw new NotSupported($this->id . ' cancelAllOrders() does not support ' . $market['type'] . ' orders, only spot orders are accepted');
        }
        $request = array(
            'symbol' => $market['id'],
            'side' => $side, // 'buy' or 'sell'
        );
        $response = $this->privateSpotPostCancelOrders (array_merge($request, $params));
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "data" => array()
        //     }
        //
        return $response;
    }

    public function cancel_orders($ids, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' canelOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        if (!$market['spot']) {
            throw new NotSupported($this->id . ' cancelOrders() does not support ' . $market['type'] . ' $orders, only contract $orders are accepted');
        }
        $orders = array();
        for ($i = 0; $i < count($ids); $i++) {
            $orders[] = intval($ids[$i]);
        }
        $request = array(
            'orders' => $orders,
        );
        $response = $this->privateContractPostCancelOrders (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "data" => {
        //             "result" => true
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "data" => {
        //             "succeed" => array(
        //                 2707219612
        //             ),
        //             "failed" => array()
        //         }
        //     }
        //
        return $response;
    }

    public function fetch_orders_by_status($status, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrdersByStatus() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array();
        $method = null;
        if ($market['spot']) {
            $method = 'privateSpotGetOrders';
            $request['symbol'] = $market['id'];
            $request['offset'] = 1; // max offset * $limit < 500
            $request['limit'] = 100; // max $limit is 100
            //  1 = Order failure
            //  2 = Placing order
            //  3 = Order failure, Freeze failure
            //  4 = Order success, Pending for fulfilment
            //  5 = Partially filled
            //  6 = Fully filled
            //  7 = Canceling
            //  8 = Canceled
            //  9 = Outstanding (4 and 5)
            // 10 = 6 and 8
            if ($status === 'open') {
                $request['status'] = 9;
            } else if ($status === 'closed') {
                $request['status'] = 6;
            } else {
                $request['status'] = $status;
            }
        } else if ($market['swap'] || $market['future']) {
            $method = 'privateContractGetUserOrders';
            $request['contractID'] = $market['id'];
            // $request['offset'] = 1;
            if ($limit !== null) {
                $request['size'] = $limit; // max 60
            }
            // 0 = All
            // 1 = Submitting
            // 2 = Commissioned
            // 3 = 1 and 2
            // 4 = Completed
            if ($status === 'open') {
                $request['status'] = 3;
            } else if ($status === 'closed') {
                $request['status'] = 4;
            } else {
                $request['status'] = $status;
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"70e7d427-7436-4fb8-8cdd-97e1f5eadbe9",
        //         "$data":{
        //             "current_page":1,
        //             "$orders":array(
        //                 {
        //                     "order_id":2147601241,
        //                     "$symbol":"BTC_USDT",
        //                     "create_time":1591099963000,
        //                     "side":"sell",
        //                     "type":"$limit",
        //                     "price":"9000.00",
        //                     "price_avg":"0.00",
        //                     "size":"1.00000",
        //                     "notional":"9000.00000000",
        //                     "filled_notional":"0.00000000",
        //                     "filled_size":"0.00000",
        //                     "$status":"4"
        //                 }
        //             )
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "$orders" => array(
        //                 {
        //                     "order_id" => 10284160,
        //                     "contract_id" => 1,
        //                     "price" => "8",
        //                     "vol" => "4",
        //                     "done_avg_price" => "0",
        //                     "done_vol" => "0",
        //                     "way" => 1,
        //                     "category" => 1,
        //                     "open_type" => 2,
        //                     "make_fee" => "0",
        //                     "take_fee" => "0",
        //                     "origin" => "",
        //                     "created_at" => "2018-07-17T07:24:13.410507Z",
        //                     "finished_at" => null,
        //                     "$status" => 2,
        //                     "errno" => 0
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $orders = $this->safe_value($data, 'orders', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_status('open', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_status('closed', $symbol, $since, $limit, $params);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        if (!($market['swap'] || $market['future'])) {
            throw new NotSupported($this->id . ' fetchOrders does not support ' . $market['type'] . ' markets, only contracts are supported');
        }
        return $this->fetch_orders_by_status(0, $symbol, $since, $limit, $params);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $request = array();
        $market = $this->market($symbol);
        $method = null;
        if (gettype($id) !== 'string') {
            $id = (string) $id;
        }
        if ($market['spot']) {
            $request['symbol'] = $market['id'];
            $request['order_id'] = $id;
            $method = 'privateSpotGetOrderDetail';
        } else if ($market['swap'] || $market['future']) {
            $request['contractID'] = $market['id'];
            $request['orderID'] = $id;
            $method = 'privateContractGetUserOrderInfo';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot
        //
        //     {
        //         "message":"OK",
        //         "code":1000,
        //         "trace":"a27c2cb5-ead4-471d-8455-1cfeda054ea6",
        //         "$data" => {
        //             "order_id":1736871726781,
        //             "$symbol":"BTC_USDT",
        //             "create_time":1591096004000,
        //             "side":"sell",
        //             "type":"$market",
        //             "price":"0.00",
        //             "price_avg":"0.00",
        //             "size":"0.02000",
        //             "notional":"0.00000000",
        //             "filled_notional":"0.00000000",
        //             "filled_size":"0.00000",
        //             "status":"8"
        //         }
        //     }
        //
        // contract
        //
        //     {
        //         "code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "$orders" => array(
        //                 {
        //                     "order_id" => 10539098,
        //                     "contract_id" => 1,
        //                     "position_id" => 10539088,
        //                     "account_id" => 10,
        //                     "price" => "16",
        //                     "vol" => "1",
        //                     "done_avg_price" => "16",
        //                     "done_vol" => "1",
        //                     "way" => 3,
        //                     "category" => 1,
        //                     "make_fee" => "0.00025",
        //                     "take_fee" => "0.012",
        //                     "origin" => "",
        //                     "created_at" => "2018-07-23T11:55:56.715305Z",
        //                     "finished_at" => "2018-07-23T11:55:56.763941Z",
        //                     "status" => 4,
        //                     "errno" => 0
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data');
        if (is_array($data) && array_key_exists('orders', $data)) {
            $orders = $this->safe_value($data, 'orders', array());
            $firstOrder = $this->safe_value($orders, 0);
            if ($firstOrder === null) {
                throw new OrderNotFound($this->id . ' fetchOrder() could not find ' . $symbol . ' order $id ' . $id);
            }
            return $this->parse_order($firstOrder, $market);
        } else {
            return $this->parse_order($data, $market);
        }
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateAccountGetDepositAddress (array_merge($request, $params));
        //
        //     {
        //         "message":"OK",
        //         "$code":1000,
        //         "trace":"0e6edd79-f77f-4251-abe5-83ba75d06c1a",
        //         "$data":{
        //             "$currency":"USDT-TRC20",
        //             "chain":"USDT-TRC20",
        //             "$address":"TGR3ghy2b5VLbyAYrmiE15jasR6aPHTvC5",
        //             "address_memo":""
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $address = $this->safe_string($data, 'address');
        $tag = $this->safe_string($data, 'address_memo');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'amount' => $amount,
            'destination' => 'To Digital Address', // To Digital Address, To Binance, To OKEX
            'address' => $address,
        );
        if ($tag !== null) {
            $request['address_memo'] = $tag;
        }
        $response = $this->privateAccountPostWithdrawApply (array_merge($request, $params));
        //
        //     {
        //         "$code" => 1000,
        //         "trace":"886fb6ae-456b-4654-b4e0-d681ac05cea1",
        //         "message" => "OK",
        //         "$data" => {
        //             "withdraw_id" => "121212"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data');
        $transaction = $this->parse_transaction($data, $currency);
        return array_merge($transaction, array(
            'code' => $code,
            'address' => $address,
            'tag' => $tag,
        ));
    }

    public function fetch_transactions_by_type($type, $code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($limit === null) {
            $limit = 50; // max 50
        }
        $request = array(
            'operation_type' => $type, // deposit or withdraw
            'offset' => 1,
            'limit' => $limit,
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currenc ($code);
            $request['currency'] = $currency['id'];
        }
        $response = $this->privateAccountGetDepositWithdrawHistory (array_merge($request, $params));
        //
        //     {
        //         "message":"OK",
        //         "$code":1000,
        //         "trace":"142bf92a-fc50-4689-92b6-590886f90b97",
        //         "$data":{
        //             "$records":array(
        //                 array(
        //                     "withdraw_id":"1679952",
        //                     "deposit_id":"",
        //                     "operation_type":"withdraw",
        //                     "$currency":"BMX",
        //                     "apply_time":1588867374000,
        //                     "arrival_amount":"59.000000000000",
        //                     "fee":"1.000000000000",
        //                     "status":0,
        //                     "address":"0xe57b69a8776b37860407965B73cdFFBDFe668Bb5",
        //                     "address_memo":"",
        //                     "tx_id":""
        //                 ),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $records = $this->safe_value($data, 'records', array());
        return $this->parse_transactions($records, $currency, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type('deposit', $code, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type('withdraw', $code, $since, $limit, $params);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            '0' => 'pending', // Create
            '1' => 'pending', // Submitted, waiting for withdrawal
            '2' => 'pending', // Processing
            '3' => 'ok', // Success
            '4' => 'canceled', // Cancel
            '5' => 'failed', // Fail
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // withdraw
        //
        //     {
        //         "withdraw_id" => "121212"
        //     }
        //
        // fetchDeposits, fetchWithdrawals
        //
        //     {
        //         "withdraw_id":"1679952",
        //         "deposit_id":"",
        //         "operation_type":"withdraw",
        //         "$currency":"BMX",
        //         "apply_time":1588867374000,
        //         "arrival_amount":"59.000000000000",
        //         "$fee":"1.000000000000",
        //         "$status":0,
        //         "$address":"0xe57b69a8776b37860407965B73cdFFBDFe668Bb5",
        //         "address_memo":"",
        //         "tx_id":""
        //     }
        //
        $id = null;
        $withdrawId = $this->safe_string($transaction, 'withdraw_id');
        $depositId = $this->safe_string($transaction, 'deposit_id');
        $type = null;
        if (($withdrawId !== null) && ($withdrawId !== '')) {
            $type = 'withdraw';
            $id = $withdrawId;
        } else if (($depositId !== null) && ($depositId !== '')) {
            $type = 'deposit';
            $id = $depositId;
        }
        $amount = $this->safe_float($transaction, 'arrival_amount');
        $timestamp = $this->safe_integer($transaction, 'tapply_timeime');
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $feeCost = $this->safe_float($transaction, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $code,
            );
        }
        $txid = $this->safe_string($transaction, 'tx_id');
        if ($txid === '') {
            $txid = null;
        }
        $address = $this->safe_string($transaction, 'address');
        $tag = $this->safe_string($transaction, 'address_memo');
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'addressFrom' => null,
            'addressTo' => null,
            'tag' => $tag,
            'tagFrom' => null,
            'tagTo' => null,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => $fee,
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $baseUrl = $this->implode_params($this->urls['api'], array( 'hostname' => $this->hostname ));
        $access = $this->safe_string($api, 0);
        $type = $this->safe_string($api, 1);
        $url = $baseUrl . '/' . $type;
        if ($type !== 'system') {
            $url .= '/' . $this->version;
        }
        if ($type === 'contract') {
            $url .= '/' . 'ifcontract';
        }
        $url .= '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($type === 'system') {
            if ($query) {
                // var_dump ($query);
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($access === 'public') {
            if ($query) {
                // var_dump ($query);
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($access === 'private') {
            $this->check_required_credentials();
            $timestamp = (string) $this->milliseconds();
            $queryString = '';
            $headers = array(
                'X-BM-KEY' => $this->apiKey,
                'X-BM-TIMESTAMP' => $timestamp,
            );
            if (($method === 'POST') || ($method === 'PUT')) {
                $headers['Content-Type'] = 'application/json';
                $body = $this->json($query);
                $queryString = $body;
            } else {
                if ($query) {
                    $queryString = $this->urlencode($query);
                    $url .= '?' . $queryString;
                }
            }
            $auth = $timestamp . '#' . $this->uid . '#' . $queryString;
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret));
            $headers['X-BM-SIGN'] = $signature;
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        //
        // spot
        //
        //     array("$message":"Bad Request [to is empty]","$code":50000,"trace":"f9d46e1b-4edb-4d07-a06e-4895fb2fc8fc","data":array())
        //     array("$message":"Bad Request [from is empty]","$code":50000,"trace":"579986f7-c93a-4559-926b-06ba9fa79d76","data":array())
        //     array("$message":"Kline size over 500","$code":50004,"trace":"d625caa8-e8ca-4bd2-b77c-958776965819","data":array())
        //     array("$message":"Balance not enough","$code":50020,"trace":"7c709d6a-3292-462c-98c5-32362540aeef","data":array())
        //
        // contract
        //
        //     array("errno":"OK","$message":"INVALID_PARAMETER","$code":49998,"trace":"eb5ebb54-23cd-4de2-9064-e090b6c3b2e3","data":null)
        //
        $message = $this->safe_string($response, 'message');
        $errorCode = $this->safe_string($response, 'code');
        if ((($errorCode !== null) && ($errorCode !== '1000')) || (($message !== null) && ($message !== 'OK'))) {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorCode, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

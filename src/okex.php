<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidAddress;
use \ccxt\InvalidOrder;
use \ccxt\NotSupported;
use \ccxt\ExchangeNotAvailable;

class okex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'okex',
            'name' => 'OKEX',
            'countries' => array( 'CN', 'US' ),
            'version' => 'v3',
            'rateLimit' => 1000, // up to 3000 requests per 5 minutes ≈ 600 requests per minute ≈ 10 requests per second ≈ 100 ms
            'pro' => true,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => false, // see below
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => false,
                'fetchOrderTrades' => true,
                'fetchTime' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'futures' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '60',
                '3m' => '180',
                '5m' => '300',
                '15m' => '900',
                '30m' => '1800',
                '1h' => '3600',
                '2h' => '7200',
                '4h' => '14400',
                '6h' => '21600',
                '12h' => '43200',
                '1d' => '86400',
                '1w' => '604800',
                '1M' => '2678400',
                '3M' => '8035200',
                '6M' => '16070400',
                '1y' => '31536000',
            ),
            'hostname' => 'okex.com',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/32552768-0d6dd3c6-c4a6-11e7-90f8-c043b64756a7.jpg',
                'api' => array(
                    'rest' => 'https://www.{hostname}',
                ),
                'www' => 'https://www.okex.com',
                'doc' => 'https://www.okex.com/docs/en/',
                'fees' => 'https://www.okex.com/pages/products/fees.html',
                'referral' => 'https://www.okex.com/join/1888677',
                'test' => array(
                    'rest' => 'https://testnet.okex.com',
                ),
            ),
            'api' => array(
                'general' => array(
                    'get' => array(
                        'time',
                    ),
                ),
                'account' => array(
                    'get' => array(
                        'wallet',
                        'sub-account',
                        'asset-valuation',
                        'wallet/{currency}',
                        'withdrawal/history',
                        'withdrawal/history/{currency}',
                        'ledger',
                        'deposit/address',
                        'deposit/history',
                        'deposit/history/{currency}',
                        'currencies',
                        'withdrawal/fee',
                    ),
                    'post' => array(
                        'transfer',
                        'withdrawal',
                    ),
                ),
                'spot' => array(
                    'get' => array(
                        'accounts',
                        'accounts/{currency}',
                        'accounts/{currency}/ledger',
                        'orders',
                        'amend_order/{instrument_id}',
                        'orders_pending',
                        'orders/{order_id}',
                        'orders/{client_oid}',
                        'trade_fee',
                        'fills',
                        'algo',
                        // public
                        'instruments',
                        'instruments/{instrument_id}/book',
                        'instruments/ticker',
                        'instruments/{instrument_id}/ticker',
                        'instruments/{instrument_id}/trades',
                        'instruments/{instrument_id}/candles',
                        'instruments/{instrument_id}/history/candles',
                    ),
                    'post' => array(
                        'order_algo',
                        'orders',
                        'batch_orders',
                        'cancel_orders/{order_id}',
                        'cancel_orders/{client_oid}',
                        'cancel_batch_algos',
                        'cancel_batch_orders',
                    ),
                ),
                'margin' => array(
                    'get' => array(
                        'accounts',
                        'accounts/{instrument_id}',
                        'accounts/{instrument_id}/ledger',
                        'accounts/availability',
                        'accounts/{instrument_id}/availability',
                        'accounts/borrowed',
                        'accounts/{instrument_id}/borrowed',
                        'orders',
                        'accounts/{instrument_id}/leverage',
                        'orders/{order_id}',
                        'orders/{client_oid}',
                        'orders_pending',
                        'fills',
                        // public
                        'instruments/{instrument_id}/mark_price',
                    ),
                    'post' => array(
                        'accounts/borrow',
                        'accounts/repayment',
                        'orders',
                        'batch_orders',
                        'cancel_orders',
                        'cancel_orders/{order_id}',
                        'cancel_orders/{client_oid}',
                        'cancel_batch_orders',
                        'accounts/{instrument_id}/leverage',
                    ),
                ),
                'futures' => array(
                    'get' => array(
                        'position',
                        '{instrument_id}/position',
                        'accounts',
                        'accounts/{underlying}',
                        'accounts/{underlying}/leverage',
                        'accounts/{underlying}/ledger',
                        'order_algo/{instrument_id}',
                        'orders/{instrument_id}',
                        'orders/{instrument_id}/{order_id}',
                        'orders/{instrument_id}/{client_oid}',
                        'fills',
                        'trade_fee',
                        'accounts/{instrument_id}/holds',
                        'order_algo/{instrument_id}',
                        // public
                        'instruments',
                        'instruments/{instrument_id}/book',
                        'instruments/ticker',
                        'instruments/{instrument_id}/ticker',
                        'instruments/{instrument_id}/trades',
                        'instruments/{instrument_id}/candles',
                        'instruments/{instrument_id}/history/candles',
                        'instruments/{instrument_id}/index',
                        'rate',
                        'instruments/{instrument_id}/estimated_price',
                        'instruments/{instrument_id}/open_interest',
                        'instruments/{instrument_id}/price_limit',
                        'instruments/{instrument_id}/mark_price',
                        'instruments/{instrument_id}/liquidation',
                    ),
                    'post' => array(
                        'accounts/{underlying}/leverage',
                        'order',
                        'amend_order/{instrument_id}',
                        'orders',
                        'cancel_order/{instrument_id}/{order_id}',
                        'cancel_order/{instrument_id}/{client_oid}',
                        'cancel_batch_orders/{instrument_id}',
                        'accounts/margin_mode',
                        'close_position',
                        'cancel_all',
                        'order_algo',
                        'cancel_algos',
                    ),
                ),
                'swap' => array(
                    'get' => array(
                        'position',
                        '{instrument_id}/position',
                        'accounts',
                        '{instrument_id}/accounts',
                        'accounts/{instrument_id}/settings',
                        'accounts/{instrument_id}/ledger',
                        'orders/{instrument_id}',
                        'orders/{instrument_id}/{order_id}',
                        'orders/{instrument_id}/{client_oid}',
                        'fills',
                        'accounts/{instrument_id}/holds',
                        'trade_fee',
                        'order_algo/{instrument_id}',
                        // public
                        'instruments',
                        'instruments/{instrument_id}/depth',
                        'instruments/ticker',
                        'instruments/{instrument_id}/ticker',
                        'instruments/{instrument_id}/trades',
                        'instruments/{instrument_id}/candles',
                        'instruments/{instrument_id}/history/candles',
                        'instruments/{instrument_id}/index',
                        'rate',
                        'instruments/{instrument_id}/open_interest',
                        'instruments/{instrument_id}/price_limit',
                        'instruments/{instrument_id}/liquidation',
                        'instruments/{instrument_id}/funding_time',
                        'instruments/{instrument_id}/mark_price',
                        'instruments/{instrument_id}/historical_funding_rate',
                    ),
                    'post' => array(
                        'accounts/{instrument_id}/leverage',
                        'order',
                        'amend_order/{instrument_id}',
                        'orders',
                        'cancel_order/{instrument_id}/{order_id}',
                        'cancel_order/{instrument_id}/{client_oid}',
                        'cancel_batch_orders/{instrument_id}',
                        'order_algo',
                        'cancel_algos',
                        'close_position',
                        'cancel_all',
                        'order_algo',
                        'cancel_algos',
                    ),
                ),
                'option' => array(
                    'get' => array(
                        'accounts',
                        'position',
                        '{underlying}/position',
                        'accounts/{underlying}',
                        'orders/{underlying}',
                        'fills/{underlying}',
                        'accounts/{underlying}/ledger',
                        'trade_fee',
                        'orders/{underlying}/{order_id}',
                        'orders/{underlying}/{client_oid}',
                        // public
                        'underlying',
                        'instruments/{underlying}',
                        'instruments/{underlying}/summary',
                        'instruments/{underlying}/summary/{instrument_id}',
                        'instruments/{instrument_id}/book',
                        'instruments/{instrument_id}/trades',
                        'instruments/{instrument_id}/ticker',
                        'instruments/{instrument_id}/candles',
                    ),
                    'post' => array(
                        'order',
                        'orders',
                        'cancel_order/{underlying}/{order_id}',
                        'cancel_order/{underlying}/{client_oid}',
                        'cancel_batch_orders/{underlying}',
                        'amend_order/{underlying}',
                        'amend_batch_orders/{underlying}',
                    ),
                ),
                'index' => array(
                    'get' => array(
                        '{instrument_id}/constituents',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'taker' => 0.0015,
                    'maker' => 0.0010,
                ),
                'spot' => array(
                    'taker' => 0.0015,
                    'maker' => 0.0010,
                ),
                'futures' => array(
                    'taker' => 0.0005,
                    'maker' => 0.0002,
                ),
                'swap' => array(
                    'taker' => 0.00075,
                    'maker' => 0.00020,
                ),
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'password' => true,
            ),
            'exceptions' => array(
                // http error codes
                // 400 Bad Request — Invalid request format
                // 401 Unauthorized — Invalid API Key
                // 403 Forbidden — You do not have access to the requested resource
                // 404 Not Found
                // 429 Client Error => Too Many Requests for url
                // 500 Internal Server Error — We had a problem with our server
                'exact' => array(
                    '1' => '\\ccxt\\ExchangeError', // array( "code" => 1, "message" => "System error" )
                    // undocumented
                    'failure to get a peer from the ring-balancer' => '\\ccxt\\ExchangeNotAvailable', // array( "message" => "failure to get a peer from the ring-balancer" )
                    'Server is busy, please try again.' => '\\ccxt\\ExchangeNotAvailable', // array( "message" => "Server is busy, please try again." )
                    'An unexpected error occurred' => '\\ccxt\\ExchangeError', // array( "message" => "An unexpected error occurred" )
                    'System error' => '\\ccxt\\ExchangeError', // array("error_message":"System error","message":"System error")
                    '4010' => '\\ccxt\\PermissionDenied', // array( "code" => 4010, "message" => "For the security of your funds, withdrawals are not permitted within 24 hours after changing fund password  / mobile number / Google Authenticator settings " )
                    // common
                    // '0' => '\\ccxt\\ExchangeError', // 200 successful,when the order placement / cancellation / operation is successful
                    '4001' => '\\ccxt\\ExchangeError', // no data received in 30s
                    '4002' => '\\ccxt\\ExchangeError', // Buffer full. cannot write data
                    // --------------------------------------------------------
                    '30001' => '\\ccxt\\AuthenticationError', // array( "code" => 30001, "message" => 'request header "OK_ACCESS_KEY" cannot be blank')
                    '30002' => '\\ccxt\\AuthenticationError', // array( "code" => 30002, "message" => 'request header "OK_ACCESS_SIGN" cannot be blank')
                    '30003' => '\\ccxt\\AuthenticationError', // array( "code" => 30003, "message" => 'request header "OK_ACCESS_TIMESTAMP" cannot be blank')
                    '30004' => '\\ccxt\\AuthenticationError', // array( "code" => 30004, "message" => 'request header "OK_ACCESS_PASSPHRASE" cannot be blank')
                    '30005' => '\\ccxt\\InvalidNonce', // array( "code" => 30005, "message" => "invalid OK_ACCESS_TIMESTAMP" )
                    '30006' => '\\ccxt\\AuthenticationError', // array( "code" => 30006, "message" => "invalid OK_ACCESS_KEY" )
                    '30007' => '\\ccxt\\BadRequest', // array( "code" => 30007, "message" => 'invalid Content_Type, please use "application/json" format')
                    '30008' => '\\ccxt\\RequestTimeout', // array( "code" => 30008, "message" => "timestamp request expired" )
                    '30009' => '\\ccxt\\ExchangeError', // array( "code" => 30009, "message" => "system error" )
                    '30010' => '\\ccxt\\AuthenticationError', // array( "code" => 30010, "message" => "API validation failed" )
                    '30011' => '\\ccxt\\PermissionDenied', // array( "code" => 30011, "message" => "invalid IP" )
                    '30012' => '\\ccxt\\AuthenticationError', // array( "code" => 30012, "message" => "invalid authorization" )
                    '30013' => '\\ccxt\\AuthenticationError', // array( "code" => 30013, "message" => "invalid sign" )
                    '30014' => '\\ccxt\\DDoSProtection', // array( "code" => 30014, "message" => "request too frequent" )
                    '30015' => '\\ccxt\\AuthenticationError', // array( "code" => 30015, "message" => 'request header "OK_ACCESS_PASSPHRASE" incorrect')
                    '30016' => '\\ccxt\\ExchangeError', // array( "code" => 30015, "message" => "you are using v1 apiKey, please use v1 endpoint. If you would like to use v3 endpoint, please subscribe to v3 apiKey" )
                    '30017' => '\\ccxt\\ExchangeError', // array( "code" => 30017, "message" => "apikey's broker id does not match" )
                    '30018' => '\\ccxt\\ExchangeError', // array( "code" => 30018, "message" => "apikey's domain does not match" )
                    '30019' => '\\ccxt\\ExchangeNotAvailable', // array( "code" => 30019, "message" => "Api is offline or unavailable" )
                    '30020' => '\\ccxt\\BadRequest', // array( "code" => 30020, "message" => "body cannot be blank" )
                    '30021' => '\\ccxt\\BadRequest', // array( "code" => 30021, "message" => "Json data format error" ), array( "code" => 30021, "message" => "json data format error" )
                    '30022' => '\\ccxt\\PermissionDenied', // array( "code" => 30022, "message" => "Api has been frozen" )
                    '30023' => '\\ccxt\\BadRequest', // array( "code" => 30023, "message" => "{0} parameter cannot be blank" )
                    '30024' => '\\ccxt\\BadSymbol', // array("code":30024,"message":"\"instrument_id\" is an invalid parameter")
                    '30025' => '\\ccxt\\BadRequest', // array( "code" => 30025, "message" => "{0} parameter category error" )
                    '30026' => '\\ccxt\\DDoSProtection', // array( "code" => 30026, "message" => "requested too frequent" )
                    '30027' => '\\ccxt\\AuthenticationError', // array( "code" => 30027, "message" => "login failure" )
                    '30028' => '\\ccxt\\PermissionDenied', // array( "code" => 30028, "message" => "unauthorized execution" )
                    '30029' => '\\ccxt\\AccountSuspended', // array( "code" => 30029, "message" => "account suspended" )
                    '30030' => '\\ccxt\\ExchangeNotAvailable', // array( "code" => 30030, "message" => "endpoint request failed. Please try again" )
                    '30031' => '\\ccxt\\BadRequest', // array( "code" => 30031, "message" => "token does not exist" )
                    '30032' => '\\ccxt\\BadSymbol', // array( "code" => 30032, "message" => "pair does not exist" )
                    '30033' => '\\ccxt\\BadRequest', // array( "code" => 30033, "message" => "exchange domain does not exist" )
                    '30034' => '\\ccxt\\ExchangeError', // array( "code" => 30034, "message" => "exchange ID does not exist" )
                    '30035' => '\\ccxt\\ExchangeError', // array( "code" => 30035, "message" => "trading is not supported in this website" )
                    '30036' => '\\ccxt\\ExchangeError', // array( "code" => 30036, "message" => "no relevant data" )
                    '30037' => '\\ccxt\\ExchangeNotAvailable', // array( "code" => 30037, "message" => "endpoint is offline or unavailable" )
                    // '30038' => '\\ccxt\\AuthenticationError', // array( "code" => 30038, "message" => "user does not exist" )
                    '30038' => '\\ccxt\\OnMaintenance', // array("client_oid":"","code":"30038","error_code":"30038","error_message":"Matching engine is being upgraded. Please try in about 1 minute.","message":"Matching engine is being upgraded. Please try in about 1 minute.","order_id":"-1","result":false)
                    '30044' => '\\ccxt\\RequestTimeout', // array( "code":30044, "message":"Endpoint request timeout" )
                    // futures
                    '32001' => '\\ccxt\\AccountSuspended', // array( "code" => 32001, "message" => "futures account suspended" )
                    '32002' => '\\ccxt\\PermissionDenied', // array( "code" => 32002, "message" => "futures account does not exist" )
                    '32003' => '\\ccxt\\CancelPending', // array( "code" => 32003, "message" => "canceling, please wait" )
                    '32004' => '\\ccxt\\ExchangeError', // array( "code" => 32004, "message" => "you have no unfilled orders" )
                    '32005' => '\\ccxt\\InvalidOrder', // array( "code" => 32005, "message" => "max order quantity" )
                    '32006' => '\\ccxt\\InvalidOrder', // array( "code" => 32006, "message" => "the order price or trigger price exceeds USD 1 million" )
                    '32007' => '\\ccxt\\InvalidOrder', // array( "code" => 32007, "message" => "leverage level must be the same for orders on the same side of the contract" )
                    '32008' => '\\ccxt\\InvalidOrder', // array( "code" => 32008, "message" => "Max. positions to open (cross margin)" )
                    '32009' => '\\ccxt\\InvalidOrder', // array( "code" => 32009, "message" => "Max. positions to open (fixed margin)" )
                    '32010' => '\\ccxt\\ExchangeError', // array( "code" => 32010, "message" => "leverage cannot be changed with open positions" )
                    '32011' => '\\ccxt\\ExchangeError', // array( "code" => 32011, "message" => "futures status error" )
                    '32012' => '\\ccxt\\ExchangeError', // array( "code" => 32012, "message" => "futures order update error" )
                    '32013' => '\\ccxt\\ExchangeError', // array( "code" => 32013, "message" => "token type is blank" )
                    '32014' => '\\ccxt\\ExchangeError', // array( "code" => 32014, "message" => "your number of contracts closing is larger than the number of contracts available" )
                    '32015' => '\\ccxt\\ExchangeError', // array( "code" => 32015, "message" => "margin ratio is lower than 100% before opening positions" )
                    '32016' => '\\ccxt\\ExchangeError', // array( "code" => 32016, "message" => "margin ratio is lower than 100% after opening position" )
                    '32017' => '\\ccxt\\ExchangeError', // array( "code" => 32017, "message" => "no BBO" )
                    '32018' => '\\ccxt\\ExchangeError', // array( "code" => 32018, "message" => "the order quantity is less than 1, please try again" )
                    '32019' => '\\ccxt\\ExchangeError', // array( "code" => 32019, "message" => "the order price deviates from the price of the previous minute by more than 3%" )
                    '32020' => '\\ccxt\\ExchangeError', // array( "code" => 32020, "message" => "the price is not in the range of the price limit" )
                    '32021' => '\\ccxt\\ExchangeError', // array( "code" => 32021, "message" => "leverage error" )
                    '32022' => '\\ccxt\\ExchangeError', // array( "code" => 32022, "message" => "this function is not supported in your country or region according to the regulations" )
                    '32023' => '\\ccxt\\ExchangeError', // array( "code" => 32023, "message" => "this account has outstanding loan" )
                    '32024' => '\\ccxt\\ExchangeError', // array( "code" => 32024, "message" => "order cannot be placed during delivery" )
                    '32025' => '\\ccxt\\ExchangeError', // array( "code" => 32025, "message" => "order cannot be placed during settlement" )
                    '32026' => '\\ccxt\\ExchangeError', // array( "code" => 32026, "message" => "your account is restricted from opening positions" )
                    '32027' => '\\ccxt\\ExchangeError', // array( "code" => 32027, "message" => "cancelled over 20 orders" )
                    '32028' => '\\ccxt\\ExchangeError', // array( "code" => 32028, "message" => "account is suspended and liquidated" )
                    '32029' => '\\ccxt\\ExchangeError', // array( "code" => 32029, "message" => "order info does not exist" )
                    '32030' => '\\ccxt\\InvalidOrder', // The order cannot be cancelled
                    '32031' => '\\ccxt\\ArgumentsRequired', // client_oid or order_id is required.
                    '32038' => '\\ccxt\\AuthenticationError', // User does not exist
                    '32040' => '\\ccxt\\ExchangeError', // User have open contract orders or position
                    '32044' => '\\ccxt\\ExchangeError', // array( "code" => 32044, "message" => "The margin ratio after submitting this order is lower than the minimum requirement ({0}) for your tier." )
                    '32045' => '\\ccxt\\ExchangeError', // String of commission over 1 million
                    '32046' => '\\ccxt\\ExchangeError', // Each user can hold up to 10 trade plans at the same time
                    '32047' => '\\ccxt\\ExchangeError', // system error
                    '32048' => '\\ccxt\\InvalidOrder', // Order strategy track range error
                    '32049' => '\\ccxt\\ExchangeError', // Each user can hold up to 10 track plans at the same time
                    '32050' => '\\ccxt\\InvalidOrder', // Order strategy rang error
                    '32051' => '\\ccxt\\InvalidOrder', // Order strategy ice depth error
                    '32052' => '\\ccxt\\ExchangeError', // String of commission over 100 thousand
                    '32053' => '\\ccxt\\ExchangeError', // Each user can hold up to 6 ice plans at the same time
                    '32057' => '\\ccxt\\ExchangeError', // The order price is zero. Market-close-all function cannot be executed
                    '32054' => '\\ccxt\\ExchangeError', // Trade not allow
                    '32055' => '\\ccxt\\InvalidOrder', // cancel order error
                    '32056' => '\\ccxt\\ExchangeError', // iceberg per order average should between {0}-{1} contracts
                    '32058' => '\\ccxt\\ExchangeError', // Each user can hold up to 6 initiative plans at the same time
                    '32059' => '\\ccxt\\InvalidOrder', // Total amount should exceed per order amount
                    '32060' => '\\ccxt\\InvalidOrder', // Order strategy type error
                    '32061' => '\\ccxt\\InvalidOrder', // Order strategy initiative limit error
                    '32062' => '\\ccxt\\InvalidOrder', // Order strategy initiative range error
                    '32063' => '\\ccxt\\InvalidOrder', // Order strategy initiative rate error
                    '32064' => '\\ccxt\\ExchangeError', // Time Stringerval of orders should set between 5-120s
                    '32065' => '\\ccxt\\ExchangeError', // Close amount exceeds the limit of Market-close-all (999 for BTC, and 9999 for the rest tokens)
                    '32066' => '\\ccxt\\ExchangeError', // You have open orders. Please cancel all open orders before changing your leverage level.
                    '32067' => '\\ccxt\\ExchangeError', // Account equity < required margin in this setting. Please adjust your leverage level again.
                    '32068' => '\\ccxt\\ExchangeError', // The margin for this position will fall short of the required margin in this setting. Please adjust your leverage level or increase your margin to proceed.
                    '32069' => '\\ccxt\\ExchangeError', // Target leverage level too low. Your account balance is insufficient to cover the margin required. Please adjust the leverage level again.
                    '32070' => '\\ccxt\\ExchangeError', // Please check open position or unfilled order
                    '32071' => '\\ccxt\\ExchangeError', // Your current liquidation mode does not support this action.
                    '32072' => '\\ccxt\\ExchangeError', // The highest available margin for your order’s tier is {0}. Please edit your margin and place a new order.
                    '32073' => '\\ccxt\\ExchangeError', // The action does not apply to the token
                    '32074' => '\\ccxt\\ExchangeError', // The number of contracts of your position, open orders, and the current order has exceeded the maximum order limit of this asset.
                    '32075' => '\\ccxt\\ExchangeError', // Account risk rate breach
                    '32076' => '\\ccxt\\ExchangeError', // Liquidation of the holding position(s) at market price will require cancellation of all pending close orders of the contracts.
                    '32077' => '\\ccxt\\ExchangeError', // Your margin for this asset in futures account is insufficient and the position has been taken over for liquidation. (You will not be able to place orders, close positions, transfer funds, or add margin during this period of time. Your account will be restored after the liquidation is complete.)
                    '32078' => '\\ccxt\\ExchangeError', // Please cancel all open orders before switching the liquidation mode(Please cancel all open orders before switching the liquidation mode)
                    '32079' => '\\ccxt\\ExchangeError', // Your open positions are at high risk.(Please add margin or reduce positions before switching the mode)
                    '32080' => '\\ccxt\\ExchangeError', // Funds cannot be transferred out within 30 minutes after futures settlement
                    '32083' => '\\ccxt\\ExchangeError', // The number of contracts should be a positive multiple of %%. Please place your order again
                    // token and margin trading
                    '33001' => '\\ccxt\\PermissionDenied', // array( "code" => 33001, "message" => "margin account for this pair is not enabled yet" )
                    '33002' => '\\ccxt\\AccountSuspended', // array( "code" => 33002, "message" => "margin account for this pair is suspended" )
                    '33003' => '\\ccxt\\InsufficientFunds', // array( "code" => 33003, "message" => "no loan balance" )
                    '33004' => '\\ccxt\\ExchangeError', // array( "code" => 33004, "message" => "loan amount cannot be smaller than the minimum limit" )
                    '33005' => '\\ccxt\\ExchangeError', // array( "code" => 33005, "message" => "repayment amount must exceed 0" )
                    '33006' => '\\ccxt\\ExchangeError', // array( "code" => 33006, "message" => "loan order not found" )
                    '33007' => '\\ccxt\\ExchangeError', // array( "code" => 33007, "message" => "status not found" )
                    '33008' => '\\ccxt\\InsufficientFunds', // array( "code" => 33008, "message" => "loan amount cannot exceed the maximum limit" )
                    '33009' => '\\ccxt\\ExchangeError', // array( "code" => 33009, "message" => "user ID is blank" )
                    '33010' => '\\ccxt\\ExchangeError', // array( "code" => 33010, "message" => "you cannot cancel an order during session 2 of call auction" )
                    '33011' => '\\ccxt\\ExchangeError', // array( "code" => 33011, "message" => "no new market data" )
                    '33012' => '\\ccxt\\ExchangeError', // array( "code" => 33012, "message" => "order cancellation failed" )
                    '33013' => '\\ccxt\\InvalidOrder', // array( "code" => 33013, "message" => "order placement failed" )
                    '33014' => '\\ccxt\\OrderNotFound', // array( "code" => 33014, "message" => "order does not exist" )
                    '33015' => '\\ccxt\\InvalidOrder', // array( "code" => 33015, "message" => "exceeded maximum limit" )
                    '33016' => '\\ccxt\\ExchangeError', // array( "code" => 33016, "message" => "margin trading is not open for this token" )
                    '33017' => '\\ccxt\\InsufficientFunds', // array( "code" => 33017, "message" => "insufficient balance" )
                    '33018' => '\\ccxt\\ExchangeError', // array( "code" => 33018, "message" => "this parameter must be smaller than 1" )
                    '33020' => '\\ccxt\\ExchangeError', // array( "code" => 33020, "message" => "request not supported" )
                    '33021' => '\\ccxt\\BadRequest', // array( "code" => 33021, "message" => "token and the pair do not match" )
                    '33022' => '\\ccxt\\InvalidOrder', // array( "code" => 33022, "message" => "pair and the order do not match" )
                    '33023' => '\\ccxt\\ExchangeError', // array( "code" => 33023, "message" => "you can only place market orders during call auction" )
                    '33024' => '\\ccxt\\InvalidOrder', // array( "code" => 33024, "message" => "trading amount too small" )
                    '33025' => '\\ccxt\\InvalidOrder', // array( "code" => 33025, "message" => "base token amount is blank" )
                    '33026' => '\\ccxt\\ExchangeError', // array( "code" => 33026, "message" => "transaction completed" )
                    '33027' => '\\ccxt\\InvalidOrder', // array( "code" => 33027, "message" => "cancelled order or order cancelling" )
                    '33028' => '\\ccxt\\InvalidOrder', // array( "code" => 33028, "message" => "the decimal places of the trading price exceeded the limit" )
                    '33029' => '\\ccxt\\InvalidOrder', // array( "code" => 33029, "message" => "the decimal places of the trading size exceeded the limit" )
                    '33034' => '\\ccxt\\ExchangeError', // array( "code" => 33034, "message" => "You can only place limit order after Call Auction has started" )
                    '33035' => '\\ccxt\\ExchangeError', // This type of order cannot be canceled(This type of order cannot be canceled)
                    '33036' => '\\ccxt\\ExchangeError', // Exceeding the limit of entrust order
                    '33037' => '\\ccxt\\ExchangeError', // The buy order price should be lower than 130% of the trigger price
                    '33038' => '\\ccxt\\ExchangeError', // The sell order price should be higher than 70% of the trigger price
                    '33039' => '\\ccxt\\ExchangeError', // The limit of callback rate is 0 < x <= 5%
                    '33040' => '\\ccxt\\ExchangeError', // The trigger price of a buy order should be lower than the latest transaction price
                    '33041' => '\\ccxt\\ExchangeError', // The trigger price of a sell order should be higher than the latest transaction price
                    '33042' => '\\ccxt\\ExchangeError', // The limit of price variance is 0 < x <= 1%
                    '33043' => '\\ccxt\\ExchangeError', // The total amount must be larger than 0
                    '33044' => '\\ccxt\\ExchangeError', // The average amount should be 1/1000 * total amount <= x <= total amount
                    '33045' => '\\ccxt\\ExchangeError', // The price should not be 0, including trigger price, order price, and price limit
                    '33046' => '\\ccxt\\ExchangeError', // Price variance should be 0 < x <= 1%
                    '33047' => '\\ccxt\\ExchangeError', // Sweep ratio should be 0 < x <= 100%
                    '33048' => '\\ccxt\\ExchangeError', // Per order limit => Total amount/1000 < x <= Total amount
                    '33049' => '\\ccxt\\ExchangeError', // Total amount should be X > 0
                    '33050' => '\\ccxt\\ExchangeError', // Time interval should be 5 <= x <= 120s
                    '33051' => '\\ccxt\\ExchangeError', // cancel order number not higher limit => plan and track entrust no more than 10, ice and time entrust no more than 6
                    '33059' => '\\ccxt\\BadRequest', // array( "code" => 33059, "message" => "client_oid or order_id is required" )
                    '33060' => '\\ccxt\\BadRequest', // array( "code" => 33060, "message" => "Only fill in either parameter client_oid or order_id" )
                    '33061' => '\\ccxt\\ExchangeError', // Value of a single market price order cannot exceed 100,000 USD
                    '33062' => '\\ccxt\\ExchangeError', // The leverage ratio is too high. The borrowed position has exceeded the maximum position of this leverage ratio. Please readjust the leverage ratio
                    '33063' => '\\ccxt\\ExchangeError', // Leverage multiple is too low, there is insufficient margin in the account, please readjust the leverage ratio
                    '33064' => '\\ccxt\\ExchangeError', // The setting of the leverage ratio cannot be less than 2, please readjust the leverage ratio
                    '33065' => '\\ccxt\\ExchangeError', // Leverage ratio exceeds maximum leverage ratio, please readjust leverage ratio
                    '33085' => '\\ccxt\\InvalidOrder', // The value of the position and buying order has reached the position limit, and no further buying is allowed.
                    // account
                    '21009' => '\\ccxt\\ExchangeError', // Funds cannot be transferred out within 30 minutes after swap settlement(Funds cannot be transferred out within 30 minutes after swap settlement)
                    '34001' => '\\ccxt\\PermissionDenied', // array( "code" => 34001, "message" => "withdrawal suspended" )
                    '34002' => '\\ccxt\\InvalidAddress', // array( "code" => 34002, "message" => "please add a withdrawal address" )
                    '34003' => '\\ccxt\\ExchangeError', // array( "code" => 34003, "message" => "sorry, this token cannot be withdrawn to xx at the moment" )
                    '34004' => '\\ccxt\\ExchangeError', // array( "code" => 34004, "message" => "withdrawal fee is smaller than minimum limit" )
                    '34005' => '\\ccxt\\ExchangeError', // array( "code" => 34005, "message" => "withdrawal fee exceeds the maximum limit" )
                    '34006' => '\\ccxt\\ExchangeError', // array( "code" => 34006, "message" => "withdrawal amount is lower than the minimum limit" )
                    '34007' => '\\ccxt\\ExchangeError', // array( "code" => 34007, "message" => "withdrawal amount exceeds the maximum limit" )
                    '34008' => '\\ccxt\\InsufficientFunds', // array( "code" => 34008, "message" => "insufficient balance" )
                    '34009' => '\\ccxt\\ExchangeError', // array( "code" => 34009, "message" => "your withdrawal amount exceeds the daily limit" )
                    '34010' => '\\ccxt\\ExchangeError', // array( "code" => 34010, "message" => "transfer amount must be larger than 0" )
                    '34011' => '\\ccxt\\ExchangeError', // array( "code" => 34011, "message" => "conditions not met" )
                    '34012' => '\\ccxt\\ExchangeError', // array( "code" => 34012, "message" => "the minimum withdrawal amount for NEO is 1, and the amount must be an integer" )
                    '34013' => '\\ccxt\\ExchangeError', // array( "code" => 34013, "message" => "please transfer" )
                    '34014' => '\\ccxt\\ExchangeError', // array( "code" => 34014, "message" => "transfer limited" )
                    '34015' => '\\ccxt\\ExchangeError', // array( "code" => 34015, "message" => "subaccount does not exist" )
                    '34016' => '\\ccxt\\PermissionDenied', // array( "code" => 34016, "message" => "transfer suspended" )
                    '34017' => '\\ccxt\\AccountSuspended', // array( "code" => 34017, "message" => "account suspended" )
                    '34018' => '\\ccxt\\AuthenticationError', // array( "code" => 34018, "message" => "incorrect trades password" )
                    '34019' => '\\ccxt\\PermissionDenied', // array( "code" => 34019, "message" => "please bind your email before withdrawal" )
                    '34020' => '\\ccxt\\PermissionDenied', // array( "code" => 34020, "message" => "please bind your funds password before withdrawal" )
                    '34021' => '\\ccxt\\InvalidAddress', // array( "code" => 34021, "message" => "Not verified address" )
                    '34022' => '\\ccxt\\ExchangeError', // array( "code" => 34022, "message" => "Withdrawals are not available for sub accounts" )
                    '34023' => '\\ccxt\\PermissionDenied', // array( "code" => 34023, "message" => "Please enable futures trading before transferring your funds" )
                    '34026' => '\\ccxt\\RateLimitExceeded', // transfer too frequently(transfer too frequently)
                    '34036' => '\\ccxt\\ExchangeError', // Parameter is incorrect, please refer to API documentation
                    '34037' => '\\ccxt\\ExchangeError', // Get the sub-account balance interface, account type is not supported
                    '34038' => '\\ccxt\\ExchangeError', // Since your C2C transaction is unusual, you are restricted from fund transfer. Please contact our customer support to cancel the restriction
                    '34039' => '\\ccxt\\ExchangeError', // You are now restricted from transferring out your funds due to abnormal trades on C2C Market. Please transfer your fund on our website or app instead to verify your identity
                    // swap
                    '35001' => '\\ccxt\\ExchangeError', // array( "code" => 35001, "message" => "Contract does not exist" )
                    '35002' => '\\ccxt\\ExchangeError', // array( "code" => 35002, "message" => "Contract settling" )
                    '35003' => '\\ccxt\\ExchangeError', // array( "code" => 35003, "message" => "Contract paused" )
                    '35004' => '\\ccxt\\ExchangeError', // array( "code" => 35004, "message" => "Contract pending settlement" )
                    '35005' => '\\ccxt\\AuthenticationError', // array( "code" => 35005, "message" => "User does not exist" )
                    '35008' => '\\ccxt\\InvalidOrder', // array( "code" => 35008, "message" => "Risk ratio too high" )
                    '35010' => '\\ccxt\\InvalidOrder', // array( "code" => 35010, "message" => "Position closing too large" )
                    '35012' => '\\ccxt\\InvalidOrder', // array( "code" => 35012, "message" => "Incorrect order size" )
                    '35014' => '\\ccxt\\InvalidOrder', // array( "code" => 35014, "message" => "Order price is not within limit" )
                    '35015' => '\\ccxt\\InvalidOrder', // array( "code" => 35015, "message" => "Invalid leverage level" )
                    '35017' => '\\ccxt\\ExchangeError', // array( "code" => 35017, "message" => "Open orders exist" )
                    '35019' => '\\ccxt\\InvalidOrder', // array( "code" => 35019, "message" => "Order size too large" )
                    '35020' => '\\ccxt\\InvalidOrder', // array( "code" => 35020, "message" => "Order price too high" )
                    '35021' => '\\ccxt\\InvalidOrder', // array( "code" => 35021, "message" => "Order size exceeded current tier limit" )
                    '35022' => '\\ccxt\\BadRequest', // array( "code" => 35022, "message" => "Contract status error" )
                    '35024' => '\\ccxt\\BadRequest', // array( "code" => 35024, "message" => "Contract not initialized" )
                    '35025' => '\\ccxt\\InsufficientFunds', // array( "code" => 35025, "message" => "No account balance" )
                    '35026' => '\\ccxt\\BadRequest', // array( "code" => 35026, "message" => "Contract settings not initialized" )
                    '35029' => '\\ccxt\\OrderNotFound', // array( "code" => 35029, "message" => "Order does not exist" )
                    '35030' => '\\ccxt\\InvalidOrder', // array( "code" => 35030, "message" => "Order size too large" )
                    '35031' => '\\ccxt\\InvalidOrder', // array( "code" => 35031, "message" => "Cancel order size too large" )
                    '35032' => '\\ccxt\\ExchangeError', // array( "code" => 35032, "message" => "Invalid user status" )
                    '35037' => '\\ccxt\\ExchangeError', // No last traded price in cache
                    '35039' => '\\ccxt\\InsufficientFunds', // array( "code" => 35039, "message" => "Open order quantity exceeds limit" )
                    '35040' => '\\ccxt\\InvalidOrder', // array("error_message":"Invalid order type","result":"true","error_code":"35040","order_id":"-1")
                    '35044' => '\\ccxt\\ExchangeError', // array( "code" => 35044, "message" => "Invalid order status" )
                    '35046' => '\\ccxt\\InsufficientFunds', // array( "code" => 35046, "message" => "Negative account balance" )
                    '35047' => '\\ccxt\\InsufficientFunds', // array( "code" => 35047, "message" => "Insufficient account balance" )
                    '35048' => '\\ccxt\\ExchangeError', // array( "code" => 35048, "message" => "User contract is frozen and liquidating" )
                    '35049' => '\\ccxt\\InvalidOrder', // array( "code" => 35049, "message" => "Invalid order type" )
                    '35050' => '\\ccxt\\InvalidOrder', // array( "code" => 35050, "message" => "Position settings are blank" )
                    '35052' => '\\ccxt\\InsufficientFunds', // array( "code" => 35052, "message" => "Insufficient cross margin" )
                    '35053' => '\\ccxt\\ExchangeError', // array( "code" => 35053, "message" => "Account risk too high" )
                    '35055' => '\\ccxt\\InsufficientFunds', // array( "code" => 35055, "message" => "Insufficient account balance" )
                    '35057' => '\\ccxt\\ExchangeError', // array( "code" => 35057, "message" => "No last traded price" )
                    '35058' => '\\ccxt\\ExchangeError', // array( "code" => 35058, "message" => "No limit" )
                    '35059' => '\\ccxt\\BadRequest', // array( "code" => 35059, "message" => "client_oid or order_id is required" )
                    '35060' => '\\ccxt\\BadRequest', // array( "code" => 35060, "message" => "Only fill in either parameter client_oid or order_id" )
                    '35061' => '\\ccxt\\BadRequest', // array( "code" => 35061, "message" => "Invalid instrument_id" )
                    '35062' => '\\ccxt\\InvalidOrder', // array( "code" => 35062, "message" => "Invalid match_price" )
                    '35063' => '\\ccxt\\InvalidOrder', // array( "code" => 35063, "message" => "Invalid order_size" )
                    '35064' => '\\ccxt\\InvalidOrder', // array( "code" => 35064, "message" => "Invalid client_oid" )
                    '35066' => '\\ccxt\\InvalidOrder', // Order interval error
                    '35067' => '\\ccxt\\InvalidOrder', // Time-weighted order ratio error
                    '35068' => '\\ccxt\\InvalidOrder', // Time-weighted order range error
                    '35069' => '\\ccxt\\InvalidOrder', // Time-weighted single transaction limit error
                    '35070' => '\\ccxt\\InvalidOrder', // Algo order type error
                    '35071' => '\\ccxt\\InvalidOrder', // Order total must be larger than single order limit
                    '35072' => '\\ccxt\\InvalidOrder', // Maximum 6 unfulfilled time-weighted orders can be held at the same time
                    '35073' => '\\ccxt\\InvalidOrder', // Order price is 0. Market-close-all not available
                    '35074' => '\\ccxt\\InvalidOrder', // Iceberg order single transaction average error
                    '35075' => '\\ccxt\\InvalidOrder', // Failed to cancel order
                    '35076' => '\\ccxt\\InvalidOrder', // LTC 20x leverage. Not allowed to open position
                    '35077' => '\\ccxt\\InvalidOrder', // Maximum 6 unfulfilled iceberg orders can be held at the same time
                    '35078' => '\\ccxt\\InvalidOrder', // Order amount exceeded 100,000
                    '35079' => '\\ccxt\\InvalidOrder', // Iceberg order price variance error
                    '35080' => '\\ccxt\\InvalidOrder', // Callback rate error
                    '35081' => '\\ccxt\\InvalidOrder', // Maximum 10 unfulfilled trail orders can be held at the same time
                    '35082' => '\\ccxt\\InvalidOrder', // Trail order callback rate error
                    '35083' => '\\ccxt\\InvalidOrder', // Each user can only hold a maximum of 10 unfulfilled stop-limit orders at the same time
                    '35084' => '\\ccxt\\InvalidOrder', // Order amount exceeded 1 million
                    '35085' => '\\ccxt\\InvalidOrder', // Order amount is not in the correct range
                    '35086' => '\\ccxt\\InvalidOrder', // Price exceeds 100 thousand
                    '35087' => '\\ccxt\\InvalidOrder', // Price exceeds 100 thousand
                    '35088' => '\\ccxt\\InvalidOrder', // Average amount error
                    '35089' => '\\ccxt\\InvalidOrder', // Price exceeds 100 thousand
                    '35090' => '\\ccxt\\ExchangeError', // No stop-limit orders available for cancelation
                    '35091' => '\\ccxt\\ExchangeError', // No trail orders available for cancellation
                    '35092' => '\\ccxt\\ExchangeError', // No iceberg orders available for cancellation
                    '35093' => '\\ccxt\\ExchangeError', // No trail orders available for cancellation
                    '35094' => '\\ccxt\\ExchangeError', // Stop-limit order last traded price error
                    '35095' => '\\ccxt\\BadRequest', // Instrument_id error
                    '35096' => '\\ccxt\\ExchangeError', // Algo order status error
                    '35097' => '\\ccxt\\ExchangeError', // Order status and order ID cannot exist at the same time
                    '35098' => '\\ccxt\\ExchangeError', // An order status or order ID must exist
                    '35099' => '\\ccxt\\ExchangeError', // Algo order ID error
                    '35102' => '\\ccxt\\RateLimitExceeded', // array("error_message":"The operation that close all at market price is too frequent","result":"true","error_code":"35102","order_id":"-1")
                    // option
                    '36001' => '\\ccxt\\BadRequest', // Invalid underlying index.
                    '36002' => '\\ccxt\\BadRequest', // Instrument does not exist.
                    '36005' => '\\ccxt\\ExchangeError', // Instrument status is invalid.
                    '36101' => '\\ccxt\\AuthenticationError', // Account does not exist.
                    '36102' => '\\ccxt\\PermissionDenied', // Account status is invalid.
                    '36103' => '\\ccxt\\PermissionDenied', // Account is suspended due to ongoing liquidation.
                    '36104' => '\\ccxt\\PermissionDenied', // Account is not enabled for options trading.
                    '36105' => '\\ccxt\\PermissionDenied', // Please enable the account for option contract.
                    '36106' => '\\ccxt\\PermissionDenied', // Funds cannot be transferred in or out, as account is suspended.
                    '36107' => '\\ccxt\\PermissionDenied', // Funds cannot be transferred out within 30 minutes after option exercising or settlement.
                    '36108' => '\\ccxt\\InsufficientFunds', // Funds cannot be transferred in or out, as equity of the account is less than zero.
                    '36109' => '\\ccxt\\PermissionDenied', // Funds cannot be transferred in or out during option exercising or settlement.
                    '36201' => '\\ccxt\\PermissionDenied', // New order function is blocked.
                    '36202' => '\\ccxt\\PermissionDenied', // Account does not have permission to short option.
                    '36203' => '\\ccxt\\InvalidOrder', // Invalid format for client_oid.
                    '36204' => '\\ccxt\\ExchangeError', // Invalid format for request_id.
                    '36205' => '\\ccxt\\BadRequest', // Instrument id does not match underlying index.
                    '36206' => '\\ccxt\\BadRequest', // Order_id and client_oid can not be used at the same time.
                    '36207' => '\\ccxt\\InvalidOrder', // Either order price or fartouch price must be present.
                    '36208' => '\\ccxt\\InvalidOrder', // Either order price or size must be present.
                    '36209' => '\\ccxt\\InvalidOrder', // Either order_id or client_oid must be present.
                    '36210' => '\\ccxt\\InvalidOrder', // Either order_ids or client_oids must be present.
                    '36211' => '\\ccxt\\InvalidOrder', // Exceeding max batch size for order submission.
                    '36212' => '\\ccxt\\InvalidOrder', // Exceeding max batch size for oder cancellation.
                    '36213' => '\\ccxt\\InvalidOrder', // Exceeding max batch size for order amendment.
                    '36214' => '\\ccxt\\ExchangeError', // Instrument does not have valid bid/ask quote.
                    '36216' => '\\ccxt\\OrderNotFound', // Order does not exist.
                    '36217' => '\\ccxt\\InvalidOrder', // Order submission failed.
                    '36218' => '\\ccxt\\InvalidOrder', // Order cancellation failed.
                    '36219' => '\\ccxt\\InvalidOrder', // Order amendment failed.
                    '36220' => '\\ccxt\\InvalidOrder', // Order is pending cancel.
                    '36221' => '\\ccxt\\InvalidOrder', // Order qty is not valid multiple of lot size.
                    '36222' => '\\ccxt\\InvalidOrder', // Order price is breaching highest buy limit.
                    '36223' => '\\ccxt\\InvalidOrder', // Order price is breaching lowest sell limit.
                    '36224' => '\\ccxt\\InvalidOrder', // Exceeding max order size.
                    '36225' => '\\ccxt\\InvalidOrder', // Exceeding max open order count for instrument.
                    '36226' => '\\ccxt\\InvalidOrder', // Exceeding max open order count for underlying.
                    '36227' => '\\ccxt\\InvalidOrder', // Exceeding max open size across all orders for underlying
                    '36228' => '\\ccxt\\InvalidOrder', // Exceeding max available qty for instrument.
                    '36229' => '\\ccxt\\InvalidOrder', // Exceeding max available qty for underlying.
                    '36230' => '\\ccxt\\InvalidOrder', // Exceeding max position limit for underlying.
                ),
                'broad' => array(
                ),
            ),
            'precisionMode' => TICK_SIZE,
            'options' => array(
                'fetchOHLCV' => array(
                    'type' => 'Candles', // Candles or HistoryCandles
                ),
                'createMarketBuyOrderRequiresPrice' => true,
                'fetchMarkets' => array( 'spot', 'futures', 'swap', 'option' ),
                'defaultType' => 'spot', // 'account', 'spot', 'margin', 'futures', 'swap', 'option'
                'auth' => array(
                    'time' => 'public',
                    'currencies' => 'private',
                    'instruments' => 'public',
                    'rate' => 'public',
                    '{instrument_id}/constituents' => 'public',
                ),
            ),
            'commonCurrencies' => array(
                // OKEX refers to ERC20 version of Aeternity (AEToken)
                'AE' => 'AET', // https://github.com/ccxt/ccxt/issues/4981
                'BOX' => 'DefiBox',
                'HOT' => 'Hydro Protocol',
                'HSR' => 'HC',
                'MAG' => 'Maggie',
                'SBTC' => 'Super Bitcoin',
                'YOYO' => 'YOYOW',
                'WIN' => 'WinToken', // https://github.com/ccxt/ccxt/issues/5701
            ),
        ));
    }

    public function fetch_time($params = array ()) {
        $response = $this->generalGetTime ($params);
        //
        //     {
        //         "iso" => "2015-01-07T23:47:25.201Z",
        //         "epoch" => 1420674445.201
        //     }
        //
        return $this->parse8601($this->safe_string($response, 'iso'));
    }

    public function fetch_markets($params = array ()) {
        $types = $this->safe_value($this->options, 'fetchMarkets');
        $result = array();
        for ($i = 0; $i < count($types); $i++) {
            $markets = $this->fetch_markets_by_type($types[$i], $params);
            $result = $this->array_concat($result, $markets);
        }
        return $result;
    }

    public function parse_markets($markets) {
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $result[] = $this->parse_market($markets[$i]);
        }
        return $result;
    }

    public function parse_market($market) {
        //
        // $spot markets
        //
        //     {
        //         base_currency => "EOS",
        //         instrument_id => "EOS-OKB",
        //         min_size => "0.01",
        //         quote_currency => "OKB",
        //         size_increment => "0.000001",
        //         tick_size => "0.0001"
        //     }
        //
        // futures markets
        //
        //     {
        //         instrument_id => "XRP-USD-200320",
        //         underlying_index => "XRP",
        //         quote_currency => "USD",
        //         tick_size => "0.0001",
        //         contract_val => "10",
        //         listing => "2020-03-06",
        //         delivery => "2020-03-20",
        //         trade_increment => "1",
        //         alias => "this_week",
        //         $underlying => "XRP-USD",
        //         base_currency => "XRP",
        //         settlement_currency => "XRP",
        //         is_inverse => "true",
        //         contract_val_currency => "USD",
        //     }
        //
        // $swap markets
        //
        //     {
        //         instrument_id => "BSV-USD-SWAP",
        //         underlying_index => "BSV",
        //         quote_currency => "USD",
        //         coin => "BSV",
        //         contract_val => "10",
        //         listing => "2018-12-21T07:53:47.000Z",
        //         delivery => "2020-03-14T08:00:00.000Z",
        //         size_increment => "1",
        //         tick_size => "0.01",
        //         base_currency => "BSV",
        //         $underlying => "BSV-USD",
        //         settlement_currency => "BSV",
        //         is_inverse => "true",
        //         contract_val_currency => "USD"
        //     }
        //
        // options markets
        //
        //     {
        //         instrument_id => 'BTC-USD-200327-4000-C',
        //         $underlying => 'BTC-USD',
        //         settlement_currency => 'BTC',
        //         contract_val => '0.1000',
        //         option_type => 'C',
        //         strike => '4000',
        //         tick_size => '0.0005',
        //         lot_size => '1.0000',
        //         listing => '2019-12-25T08:30:36.302Z',
        //         delivery => '2020-03-27T08:00:00.000Z',
        //         state => '2',
        //         trading_start_time => '2019-12-25T08:30:36.302Z',
        //         timestamp => '2020-03-13T08:05:09.456Z',
        //     }
        //
        $id = $this->safe_string($market, 'instrument_id');
        $marketType = 'spot';
        $spot = true;
        $future = false;
        $swap = false;
        $option = false;
        $baseId = $this->safe_string($market, 'base_currency');
        $quoteId = $this->safe_string($market, 'quote_currency');
        $contractVal = $this->safe_number($market, 'contract_val');
        if ($contractVal !== null) {
            if (is_array($market) && array_key_exists('option_type', $market)) {
                $marketType = 'option';
                $spot = false;
                $option = true;
                $underlying = $this->safe_string($market, 'underlying');
                $parts = explode('-', $underlying);
                $baseId = $this->safe_string($parts, 0);
                $quoteId = $this->safe_string($parts, 1);
            } else {
                $marketType = 'swap';
                $spot = false;
                $swap = true;
                $futuresAlias = $this->safe_string($market, 'alias');
                if ($futuresAlias !== null) {
                    $swap = false;
                    $future = true;
                    $marketType = 'futures';
                    $baseId = $this->safe_string($market, 'underlying_index');
                }
            }
        }
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        $symbol = $spot ? ($base . '/' . $quote) : $id;
        $lotSize = $this->safe_number_2($market, 'lot_size', 'trade_increment');
        $precision = array(
            'amount' => $this->safe_number($market, 'size_increment', $lotSize),
            'price' => $this->safe_number($market, 'tick_size'),
        );
        $minAmount = $this->safe_number_2($market, 'min_size', 'base_min_size');
        $active = true;
        $fees = $this->safe_value_2($this->fees, $marketType, 'trading', array());
        return array_merge($fees, array(
            'id' => $id,
            'symbol' => $symbol,
            'base' => $base,
            'quote' => $quote,
            'baseId' => $baseId,
            'quoteId' => $quoteId,
            'info' => $market,
            'type' => $marketType,
            'spot' => $spot,
            'futures' => $future,
            'swap' => $swap,
            'option' => $option,
            'active' => $active,
            'precision' => $precision,
            'limits' => array(
                'amount' => array(
                    'min' => $minAmount,
                    'max' => null,
                ),
                'price' => array(
                    'min' => $precision['price'],
                    'max' => null,
                ),
                'cost' => array(
                    'min' => $precision['price'],
                    'max' => null,
                ),
            ),
        ));
    }

    public function fetch_markets_by_type($type, $params = array ()) {
        if ($type === 'option') {
            $underlying = $this->optionGetUnderlying ($params);
            $result = array();
            for ($i = 0; $i < count($underlying); $i++) {
                $response = $this->optionGetInstrumentsUnderlying (array(
                    'underlying' => $underlying[$i],
                ));
                //
                // options markets
                //
                //     array(
                //         array(
                //             instrument_id => 'BTC-USD-200327-4000-C',
                //             $underlying => 'BTC-USD',
                //             settlement_currency => 'BTC',
                //             contract_val => '0.1000',
                //             option_type => 'C',
                //             strike => '4000',
                //             tick_size => '0.0005',
                //             lot_size => '1.0000',
                //             listing => '2019-12-25T08:30:36.302Z',
                //             delivery => '2020-03-27T08:00:00.000Z',
                //             state => '2',
                //             trading_start_time => '2019-12-25T08:30:36.302Z',
                //             timestamp => '2020-03-13T08:05:09.456Z',
                //         ),
                //     )
                //
                $result = $this->array_concat($result, $response);
            }
            return $this->parse_markets($result);
        } else if (($type === 'spot') || ($type === 'futures') || ($type === 'swap')) {
            $method = $type . 'GetInstruments';
            $response = $this->$method ($params);
            //
            // spot markets
            //
            //     array(
            //         {
            //             base_currency => "EOS",
            //             instrument_id => "EOS-OKB",
            //             min_size => "0.01",
            //             quote_currency => "OKB",
            //             size_increment => "0.000001",
            //             tick_size => "0.0001"
            //         }
            //     )
            //
            // futures markets
            //
            //     array(
            //         {
            //             instrument_id => "XRP-USD-200320",
            //             underlying_index => "XRP",
            //             quote_currency => "USD",
            //             tick_size => "0.0001",
            //             contract_val => "10",
            //             listing => "2020-03-06",
            //             delivery => "2020-03-20",
            //             trade_increment => "1",
            //             alias => "this_week",
            //             $underlying => "XRP-USD",
            //             base_currency => "XRP",
            //             settlement_currency => "XRP",
            //             is_inverse => "true",
            //             contract_val_currency => "USD",
            //         }
            //     )
            //
            // swap markets
            //
            //     array(
            //         {
            //             instrument_id => "BSV-USD-SWAP",
            //             underlying_index => "BSV",
            //             quote_currency => "USD",
            //             coin => "BSV",
            //             contract_val => "10",
            //             listing => "2018-12-21T07:53:47.000Z",
            //             delivery => "2020-03-14T08:00:00.000Z",
            //             size_increment => "1",
            //             tick_size => "0.01",
            //             base_currency => "BSV",
            //             $underlying => "BSV-USD",
            //             settlement_currency => "BSV",
            //             is_inverse => "true",
            //             contract_val_currency => "USD"
            //         }
            //     )
            //
            return $this->parse_markets($response);
        } else {
            throw new NotSupported($this->id . ' fetchMarketsByType does not support market $type ' . $type);
        }
    }

    public function fetch_currencies($params = array ()) {
        // has['fetchCurrencies'] is currently set to false
        // despite that their docs say these endpoints are public:
        //     https://www.okex.com/api/account/v3/withdrawal/fee
        //     https://www.okex.com/api/account/v3/currencies
        // it will still reply with array( "$code":30001, "message" => "OK-ACCESS-KEY header is required" )
        // if you attempt to access it without authentication
        $response = $this->accountGetCurrencies ($params);
        //
        //     array(
        //         array(
        //             $name => '',
        //             $currency => 'BTC',
        //             can_withdraw => '1',
        //             can_deposit => '1',
        //             min_withdrawal => '0.0100000000000000'
        //         ),
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'currency');
            $code = $this->safe_currency_code($id);
            $precision = 0.00000001; // default $precision, todo => fix "magic constants"
            $name = $this->safe_string($currency, 'name');
            $canDeposit = $this->safe_integer($currency, 'can_deposit');
            $canWithdraw = $this->safe_integer($currency, 'can_withdraw');
            $active = ($canDeposit && $canWithdraw) ? true : false;
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'type' => null,
                'name' => $name,
                'active' => $active,
                'fee' => null, // todo => redesign
                'precision' => $precision,
                'limits' => array(
                    'amount' => array( 'min' => null, 'max' => null ),
                    'price' => array( 'min' => null, 'max' => null ),
                    'cost' => array( 'min' => null, 'max' => null ),
                    'withdraw' => array(
                        'min' => $this->safe_number($currency, 'min_withdrawal'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $method = $market['type'] . 'GetInstrumentsInstrumentId';
        $method .= ($market['type'] === 'swap') ? 'Depth' : 'Book';
        $request = array(
            'instrument_id' => $market['id'],
        );
        if ($limit !== null) {
            $request['size'] = $limit; // max 200
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {      asks => [ ["0.02685268", "0.242571", "1"],
        //                    ["0.02685493", "0.164085", "1"],
        //                    ...
        //                    ["0.02779", "1.039", "1"],
        //                    ["0.027813", "0.0876", "1"]        ],
        //            bids => [ ["0.02684052", "10.371849", "1"],
        //                    ["0.02684051", "3.707", "4"],
        //                    ...
        //                    ["0.02634963", "0.132934", "1"],
        //                    ["0.02634962", "0.264838", "2"]    ],
        //       $timestamp =>   "2018-12-17T20:24:16.159Z"            }
        //
        $timestamp = $this->parse8601($this->safe_string($response, 'timestamp'));
        return $this->parse_order_book($response, $timestamp);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {         best_ask => "0.02665472",
        //               best_bid => "0.02665221",
        //          instrument_id => "ETH-BTC",
        //             product_id => "ETH-BTC",
        //                   $last => "0.02665472",
        //                    ask => "0.02665472", // missing in the docs
        //                    bid => "0.02665221", // not mentioned in the docs
        //               open_24h => "0.02645482",
        //               high_24h => "0.02714633",
        //                low_24h => "0.02614109",
        //        base_volume_24h => "572298.901923",
        //              $timestamp => "2018-12-17T21:20:07.856Z",
        //       quote_volume_24h => "15094.86831261"            }
        //
        $timestamp = $this->parse8601($this->safe_string($ticker, 'timestamp'));
        $symbol = null;
        $marketId = $this->safe_string($ticker, 'instrument_id');
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
            $symbol = $market['symbol'];
        } else if ($marketId !== null) {
            $parts = explode('-', $marketId);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts === 2) {
                list($baseId, $quoteId) = $parts;
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            } else {
                $symbol = $marketId;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_number($ticker, 'last');
        $open = $this->safe_number($ticker, 'open_24h');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high_24h'),
            'low' => $this->safe_number($ticker, 'low_24h'),
            'bid' => $this->safe_number($ticker, 'best_bid'),
            'bidVolume' => $this->safe_number($ticker, 'best_bid_size'),
            'ask' => $this->safe_number($ticker, 'best_ask'),
            'askVolume' => $this->safe_number($ticker, 'best_ask_size'),
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'base_volume_24h'),
            'quoteVolume' => $this->safe_number($ticker, 'quote_volume_24h'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $method = $market['type'] . 'GetInstrumentsInstrumentIdTicker';
        $request = array(
            'instrument_id' => $market['id'],
        );
        $response = $this->$method (array_merge($request, $params));
        //
        //     {         best_ask => "0.02665472",
        //               best_bid => "0.02665221",
        //          instrument_id => "ETH-BTC",
        //             product_id => "ETH-BTC",
        //                   last => "0.02665472",
        //                    ask => "0.02665472",
        //                    bid => "0.02665221",
        //               open_24h => "0.02645482",
        //               high_24h => "0.02714633",
        //                low_24h => "0.02614109",
        //        base_volume_24h => "572298.901923",
        //              timestamp => "2018-12-17T21:20:07.856Z",
        //       quote_volume_24h => "15094.86831261"            }
        //
        return $this->parse_ticker($response);
    }

    public function fetch_tickers_by_type($type, $symbols = null, $params = array ()) {
        $this->load_markets();
        $method = $type . 'GetInstrumentsTicker';
        $response = $this->$method ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $defaultType = $this->safe_string_2($this->options, 'fetchTickers', 'defaultType');
        $type = $this->safe_string($params, 'type', $defaultType);
        return $this->fetch_tickers_by_type($type, $symbols, $this->omit($params, 'type'));
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     spot trades
        //
        //         {
        //             time => "2018-12-17T23:31:08.268Z",
        //             $timestamp => "2018-12-17T23:31:08.268Z",
        //             trade_id => "409687906",
        //             $price => "0.02677805",
        //             size => "0.923467",
        //             $side => "sell"
        //         }
        //
        //     futures trades, swap trades
        //
        //         {
        //             trade_id => "1989230840021013",
        //             $side => "buy",
        //             $price => "92.42",
        //             qty => "184", // missing in swap markets
        //             size => "5", // missing in futures markets
        //             $timestamp => "2018-12-17T23:26:04.613Z"
        //         }
        //
        // fetchOrderTrades (private)
        //
        //     spot trades, margin trades
        //
        //         array(
        //             "created_at":"2019-03-15T02:52:56.000Z",
        //             "exec_type":"T", // whether the order is taker or maker
        //             "$fee":"0.00000082",
        //             "instrument_id":"BTC-USDT",
        //             "ledger_id":"3963052721",
        //             "liquidity":"T", // whether the order is taker or maker
        //             "order_id":"2482659399697408",
        //             "$price":"3888.6",
        //             "product_id":"BTC-USDT",
        //             "$side":"buy",
        //             "size":"0.00055306",
        //             "$timestamp":"2019-03-15T02:52:56.000Z"
        //         ),
        //
        //     futures trades, swap trades
        //
        //         {
        //             "trade_id":"197429674631450625",
        //             "instrument_id":"EOS-USD-SWAP",
        //             "order_id":"6a-7-54d663a28-0",
        //             "$price":"3.633",
        //             "order_qty":"1.0000",
        //             "$fee":"-0.000551",
        //             "created_at":"2019-03-21T04:41:58.0Z", // missing in swap trades
        //             "$timestamp":"2019-03-25T05:56:31.287Z", // missing in futures trades
        //             "exec_type":"M", // whether the order is taker or maker
        //             "$side":"short", // "buy" in futures trades
        //         }
        //
        $symbol = null;
        $marketId = $this->safe_string($trade, 'instrument_id');
        $base = null;
        $quote = null;
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
            $symbol = $market['symbol'];
            $base = $market['base'];
            $quote = $market['quote'];
        } else if ($marketId !== null) {
            $parts = explode('-', $marketId);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts === 2) {
                list($baseId, $quoteId) = $parts;
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            } else {
                $symbol = $marketId;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
            $base = $market['base'];
            $quote = $market['quote'];
        }
        $timestamp = $this->parse8601($this->safe_string_2($trade, 'timestamp', 'created_at'));
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number_2($trade, 'size', 'qty');
        $amount = $this->safe_number($trade, 'order_qty', $amount);
        $takerOrMaker = $this->safe_string_2($trade, 'exec_type', 'liquidity');
        if ($takerOrMaker === 'M') {
            $takerOrMaker = 'maker';
        } else if ($takerOrMaker === 'T') {
            $takerOrMaker = 'taker';
        }
        $side = $this->safe_string($trade, 'side');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $amount * $price;
            }
        }
        $feeCost = $this->safe_number($trade, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrency = ($side === 'buy') ? $base : $quote;
            $fee = array(
                // $fee is either a positive number (invitation rebate)
                // or a negative number (transaction $fee deduction)
                // therefore we need to invert the $fee
                // more about it https://github.com/ccxt/ccxt/issues/5909
                'cost' => -$feeCost,
                'currency' => $feeCurrency,
            );
        }
        $orderId = $this->safe_string($trade, 'order_id');
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $this->safe_string_2($trade, 'trade_id', 'ledger_id'),
            'order' => $orderId,
            'type' => null,
            'takerOrMaker' => $takerOrMaker,
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
        $method = $market['type'] . 'GetInstrumentsInstrumentIdTrades';
        if (($limit === null) || ($limit > 100)) {
            $limit = 100; // maximum = default = 100
        }
        $request = array(
            'instrument_id' => $market['id'],
            'limit' => $limit,
            // from => 'id',
            // to => 'id',
        );
        $response = $this->$method (array_merge($request, $params));
        //
        // spot markets
        //
        //     array(
        //         {
        //             time => "2018-12-17T23:31:08.268Z",
        //             timestamp => "2018-12-17T23:31:08.268Z",
        //             trade_id => "409687906",
        //             price => "0.02677805",
        //             size => "0.923467",
        //             side => "sell"
        //         }
        //     )
        //
        // futures markets, swap markets
        //
        //     array(
        //         {
        //             trade_id => "1989230840021013",
        //             side => "buy",
        //             price => "92.42",
        //             qty => "184", // missing in swap markets
        //             size => "5", // missing in futures markets
        //             timestamp => "2018-12-17T23:26:04.613Z"
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        // spot markets
        //
        //     {
        //         close => "0.02684545",
        //         high => "0.02685084",
        //         low => "0.02683312",
        //         open => "0.02683894",
        //         time => "2018-12-17T20:28:00.000Z",
        //         volume => "101.457222"
        //     }
        //
        // futures markets
        //
        //     array(
        //         1545072720000,
        //         0.3159,
        //         0.3161,
        //         0.3144,
        //         0.3149,
        //         22886,
        //         725179.26172331,
        //     )
        //
        if (gettype($ohlcv) === 'array' && count(array_filter(array_keys($ohlcv), 'is_string')) == 0) {
            $numElements = is_array($ohlcv) ? count($ohlcv) : 0;
            $volumeIndex = ($numElements > 6) ? 6 : 5;
            $timestamp = $this->safe_value($ohlcv, 0);
            if (gettype($timestamp) === 'string') {
                $timestamp = $this->parse8601($timestamp);
            }
            return array(
                $timestamp, // $timestamp
                $this->safe_number($ohlcv, 1),            // Open
                $this->safe_number($ohlcv, 2),            // High
                $this->safe_number($ohlcv, 3),            // Low
                $this->safe_number($ohlcv, 4),            // Close
                // $this->safe_number($ohlcv, 5),         // Quote Volume
                // $this->safe_number($ohlcv, 6),         // Base Volume
                $this->safe_number($ohlcv, $volumeIndex),  // Volume, okex will return base volume in the 7th element for future markets
            );
        } else {
            return array(
                $this->parse8601($this->safe_string($ohlcv, 'time')),
                $this->safe_number($ohlcv, 'open'),    // Open
                $this->safe_number($ohlcv, 'high'),    // High
                $this->safe_number($ohlcv, 'low'),     // Low
                $this->safe_number($ohlcv, 'close'),   // Close
                $this->safe_number($ohlcv, 'volume'),  // Base Volume
            );
        }
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $duration = $this->parse_timeframe($timeframe);
        $request = array(
            'instrument_id' => $market['id'],
            'granularity' => $this->timeframes[$timeframe],
        );
        $options = $this->safe_value($this->options, 'fetchOHLCV', array());
        $defaultType = $this->safe_string($options, 'type', 'Candles'); // Candles or HistoryCandles
        $type = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $method = $market['type'] . 'GetInstrumentsInstrumentId' . $type;
        if ($type === 'Candles') {
            if ($since !== null) {
                if ($limit !== null) {
                    $request['end'] = $this->iso8601($this->sum($since, $limit * $duration * 1000));
                }
                $request['start'] = $this->iso8601($since);
            } else {
                if ($limit !== null) {
                    $now = $this->milliseconds();
                    $request['start'] = $this->iso8601($now - $limit * $duration * 1000);
                    $request['end'] = $this->iso8601($now);
                }
            }
        } else if ($type === 'HistoryCandles') {
            if ($market['option']) {
                throw new NotSupported($this->id . ' fetchOHLCV does not have ' . $type . ' for ' . $market['type'] . ' markets');
            }
            if ($since !== null) {
                if ($limit === null) {
                    $limit = 300; // default
                }
                $request['start'] = $this->iso8601($this->sum($since, $limit * $duration * 1000));
                $request['end'] = $this->iso8601($since);
            } else {
                if ($limit !== null) {
                    $now = $this->milliseconds();
                    $request['end'] = $this->iso8601($now - $limit * $duration * 1000);
                    $request['start'] = $this->iso8601($now);
                }
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot markets
        //
        //     array(
        //         array(
        //             close => "0.02683401",
        //             high => "0.02683401",
        //             low => "0.02683401",
        //             open => "0.02683401",
        //             time => "2018-12-17T23:47:00.000Z",
        //             volume => "0"
        //         ),
        //         {
        //             close => "0.02684545",
        //             high => "0.02685084",
        //             low => "0.02683312",
        //             open => "0.02683894",
        //             time => "2018-12-17T20:28:00.000Z",
        //             volume => "101.457222"
        //         }
        //     )
        //
        // futures
        //
        //     array(
        //         array(
        //             1545090660000,
        //             0.3171,
        //             0.3174,
        //             0.3171,
        //             0.3173,
        //             1648,
        //             51930.38579450868
        //         ),
        //         array(
        //             1545072720000,
        //             0.3159,
        //             0.3161,
        //             0.3144,
        //             0.3149,
        //             22886,
        //             725179.26172331
        //         )
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function parse_account_balance($response) {
        //
        // $account
        //
        //     array(
        //         array(
        //             $balance =>  0,
        //             available =>  0,
        //             currency => "BTC",
        //             hold =>  0
        //         ),
        //         {
        //             $balance =>  0,
        //             available =>  0,
        //             currency => "ETH",
        //             hold =>  0
        //         }
        //     )
        //
        // spot
        //
        //     array(
        //         array(
        //             frozen => "0",
        //             hold => "0",
        //             id => "2149632",
        //             currency => "BTC",
        //             $balance => "0.0000000497717339",
        //             available => "0.0000000497717339",
        //             holds => "0"
        //         ),
        //         {
        //             frozen => "0",
        //             hold => "0",
        //             id => "2149632",
        //             currency => "ICN",
        //             $balance => "0.00000000925",
        //             available => "0.00000000925",
        //             holds => "0"
        //         }
        //     )
        //
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_number($balance, 'balance');
            $account['used'] = $this->safe_number($balance, 'hold');
            $account['free'] = $this->safe_number($balance, 'available');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_margin_balance($response) {
        //
        //     array(
        //         array(
        //             "currency:BTC" => array(
        //                 "available":"0",
        //                 "$balance":"0",
        //                 "borrowed":"0",
        //                 "can_withdraw":"0",
        //                 "frozen":"0",
        //                 "hold":"0",
        //                 "holds":"0",
        //                 "lending_fee":"0"
        //             ),
        //             "currency:USDT" => array(
        //                 "available":"100",
        //                 "$balance":"100",
        //                 "borrowed":"0",
        //                 "can_withdraw":"100",
        //                 "frozen":"0",
        //                 "hold":"0",
        //                 "holds":"0",
        //                 "lending_fee":"0"
        //             ),
        //             "instrument_id":"BTC-USDT",
        //             "liquidation_price":"0",
        //             "product_id":"BTC-USDT",
        //             "risk_rate":""
        //         ),
        //     )
        //
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $marketId = $this->safe_string($balance, 'instrument_id');
            $market = $this->safe_value($this->markets_by_id, $marketId);
            $symbol = null;
            if ($market === null) {
                list($baseId, $quoteId) = explode('-', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            } else {
                $symbol = $market['symbol'];
            }
            $omittedBalance = $this->omit($balance, array(
                'instrument_id',
                'liquidation_price',
                'product_id',
                'risk_rate',
                'margin_ratio',
                'maint_margin_ratio',
                'tiers',
            ));
            $keys = is_array($omittedBalance) ? array_keys($omittedBalance) : array();
            $accounts = array();
            for ($k = 0; $k < count($keys); $k++) {
                $key = $keys[$k];
                $marketBalance = $balance[$key];
                if (mb_strpos($key, ':') !== false) {
                    $parts = explode(':', $key);
                    $currencyId = $parts[1];
                    $code = $this->safe_currency_code($currencyId);
                    $account = $this->account();
                    $account['total'] = $this->safe_number($marketBalance, 'balance');
                    $account['used'] = $this->safe_number($marketBalance, 'hold');
                    $account['free'] = $this->safe_number($marketBalance, 'available');
                    $accounts[$code] = $account;
                } else {
                    throw new NotSupported($this->id . ' margin $balance $response format has changed!');
                }
            }
            $result[$symbol] = $this->parse_balance($accounts);
        }
        return $result;
    }

    public function parse_futures_balance($response) {
        //
        //     {
        //         "$info":{
        //             "eos":array(
        //                 "auto_margin":"0",
        //                 "$contracts" => array(
        //                     array(
        //                         "available_qty":"40.37069445",
        //                         "fixed_balance":"0",
        //                         "instrument_id":"EOS-USD-190329",
        //                         "margin_for_unfilled":"0",
        //                         "margin_frozen":"0",
        //                         "realized_pnl":"0",
        //                         "unrealized_pnl":"0"
        //                     ),
        //                     array(
        //                         "available_qty":"40.37069445",
        //                         "fixed_balance":"14.54895721",
        //                         "instrument_id":"EOS-USD-190628",
        //                         "margin_for_unfilled":"0",
        //                         "margin_frozen":"10.64042157",
        //                         "realized_pnl":"-3.90853564",
        //                         "unrealized_pnl":"-0.259"
        //                     ),
        //                 ),
        //                 "equity":"50.75220665",
        //                 "margin_mode":"fixed",
        //                 "total_avail_balance":"40.37069445"
        //             ),
        //         }
        //     }
        //
        // their root field name is "$info", so our $info will contain their $info
        $result = array( 'info' => $response );
        $info = $this->safe_value($response, 'info', array());
        $ids = is_array($info) ? array_keys($info) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $code = $this->safe_currency_code($id);
            $balance = $this->safe_value($info, $id, array());
            $account = $this->account();
            $totalAvailBalance = $this->safe_number($balance, 'total_avail_balance');
            if ($this->safe_string($balance, 'margin_mode') === 'fixed') {
                $contracts = $this->safe_value($balance, 'contracts', array());
                $free = $totalAvailBalance;
                for ($i = 0; $i < count($contracts); $i++) {
                    $contract = $contracts[$i];
                    $fixedBalance = $this->safe_number($contract, 'fixed_balance');
                    $realizedPnl = $this->safe_number($contract, 'realized_pnl');
                    $marginFrozen = $this->safe_number($contract, 'margin_frozen');
                    $marginForUnfilled = $this->safe_number($contract, 'margin_for_unfilled');
                    $margin = $this->sum($fixedBalance, $realizedPnl) - $marginFrozen - $marginForUnfilled;
                    $free = $this->sum($free, $margin);
                }
                $account['free'] = $free;
            } else {
                $realizedPnl = $this->safe_number($balance, 'realized_pnl');
                $unrealizedPnl = $this->safe_number($balance, 'unrealized_pnl');
                $marginFrozen = $this->safe_number($balance, 'margin_frozen');
                $marginForUnfilled = $this->safe_number($balance, 'margin_for_unfilled');
                $account['free'] = $this->sum($totalAvailBalance, $realizedPnl, $unrealizedPnl) - $marginFrozen - $marginForUnfilled;
            }
            // it may be incorrect to use total, $free and used for swap accounts
            $account['total'] = $this->safe_number($balance, 'equity');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_swap_balance($response) {
        //
        //     {
        //         "$info" => array(
        //             {
        //                 "equity":"3.0139",
        //                 "fixed_balance":"0.0000",
        //                 "instrument_id":"EOS-USD-SWAP",
        //                 "margin":"0.5523",
        //                 "margin_frozen":"0.0000",
        //                 "margin_mode":"crossed",
        //                 "margin_ratio":"1.0913",
        //                 "realized_pnl":"-0.0006",
        //                 "timestamp":"2019-03-25T03:46:10.336Z",
        //                 "total_avail_balance":"3.0000",
        //                 "unrealized_pnl":"0.0145"
        //             }
        //         )
        //     }
        //
        // their root field name is "$info", so our $info will contain their $info
        $result = array( 'info' => $response );
        $info = $this->safe_value($response, 'info', array());
        for ($i = 0; $i < count($info); $i++) {
            $balance = $info[$i];
            $marketId = $this->safe_string($balance, 'instrument_id');
            $symbol = $marketId;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $symbol = $this->markets_by_id[$marketId]['symbol'];
            }
            $account = $this->account();
            // it may be incorrect to use total, free and used for swap accounts
            $account['total'] = $this->safe_number($balance, 'equity');
            $account['free'] = $this->safe_number($balance, 'total_avail_balance');
            $result[$symbol] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_balance($params = array ()) {
        $defaultType = $this->safe_string_2($this->options, 'fetchBalance', 'defaultType');
        $type = $this->safe_string($params, 'type', $defaultType);
        if ($type === null) {
            throw new ArgumentsRequired($this->id . " fetchBalance() requires a $type parameter (one of 'account', 'spot', 'margin', 'futures', 'swap')");
        }
        $this->load_markets();
        $suffix = ($type === 'account') ? 'Wallet' : 'Accounts';
        $method = $type . 'Get' . $suffix;
        $query = $this->omit($params, 'type');
        $response = $this->$method ($query);
        //
        // account
        //
        //     array(
        //         array(
        //             balance =>  0,
        //             available =>  0,
        //             currency => "BTC",
        //             hold =>  0
        //         ),
        //         {
        //             balance =>  0,
        //             available =>  0,
        //             currency => "ETH",
        //             hold =>  0
        //         }
        //     )
        //
        // spot
        //
        //     array(
        //         array(
        //             frozen => "0",
        //             hold => "0",
        //             id => "2149632",
        //             currency => "BTC",
        //             balance => "0.0000000497717339",
        //             available => "0.0000000497717339",
        //             holds => "0"
        //         ),
        //         {
        //             frozen => "0",
        //             hold => "0",
        //             id => "2149632",
        //             currency => "ICN",
        //             balance => "0.00000000925",
        //             available => "0.00000000925",
        //             holds => "0"
        //         }
        //     )
        //
        // margin
        //
        //     array(
        //         array(
        //             "currency:BTC" => array(
        //                 "available":"0",
        //                 "balance":"0",
        //                 "borrowed":"0",
        //                 "can_withdraw":"0",
        //                 "frozen":"0",
        //                 "hold":"0",
        //                 "holds":"0",
        //                 "lending_fee":"0"
        //             ),
        //             "currency:USDT" => array(
        //                 "available":"100",
        //                 "balance":"100",
        //                 "borrowed":"0",
        //                 "can_withdraw":"100",
        //                 "frozen":"0",
        //                 "hold":"0",
        //                 "holds":"0",
        //                 "lending_fee":"0"
        //             ),
        //             "instrument_id":"BTC-USDT",
        //             "liquidation_price":"0",
        //             "product_id":"BTC-USDT",
        //             "risk_rate":""
        //         ),
        //     )
        //
        // futures
        //
        //     {
        //         "info":{
        //             "eos":array(
        //                 "auto_margin":"0",
        //                 "contracts" => array(
        //                     array(
        //                         "available_qty":"40.37069445",
        //                         "fixed_balance":"0",
        //                         "instrument_id":"EOS-USD-190329",
        //                         "margin_for_unfilled":"0",
        //                         "margin_frozen":"0",
        //                         "realized_pnl":"0",
        //                         "unrealized_pnl":"0"
        //                     ),
        //                     array(
        //                         "available_qty":"40.37069445",
        //                         "fixed_balance":"14.54895721",
        //                         "instrument_id":"EOS-USD-190628",
        //                         "margin_for_unfilled":"0",
        //                         "margin_frozen":"10.64042157",
        //                         "realized_pnl":"-3.90853564",
        //                         "unrealized_pnl":"-0.259"
        //                     ),
        //                 ),
        //                 "equity":"50.75220665",
        //                 "margin_mode":"fixed",
        //                 "total_avail_balance":"40.37069445"
        //             ),
        //         }
        //     }
        //
        // swap
        //
        //     {
        //         "info" => array(
        //             {
        //                 "equity":"3.0139",
        //                 "fixed_balance":"0.0000",
        //                 "instrument_id":"EOS-USD-SWAP",
        //                 "margin":"0.5523",
        //                 "margin_frozen":"0.0000",
        //                 "margin_mode":"crossed",
        //                 "margin_ratio":"1.0913",
        //                 "realized_pnl":"-0.0006",
        //                 "timestamp":"2019-03-25T03:46:10.336Z",
        //                 "total_avail_balance":"3.0000",
        //                 "unrealized_pnl":"0.0145"
        //             }
        //         )
        //     }
        //
        return $this->parse_balance_by_type($type, $response);
    }

    public function parse_balance_by_type($type, $response) {
        if (($type === 'account') || ($type === 'spot')) {
            return $this->parse_account_balance($response);
        } else if ($type === 'margin') {
            return $this->parse_margin_balance($response);
        } else if ($type === 'futures') {
            return $this->parse_futures_balance($response);
        } else if ($type === 'swap') {
            return $this->parse_swap_balance($response);
        }
        throw new NotSupported($this->id . " fetchBalance does not support the '" . $type . "' $type (the $type must be one of 'account', 'spot', 'margin', 'futures', 'swap')");
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument_id' => $market['id'],
            // 'client_oid' => 'abcdef1234567890', // [a-z0-9]array(1,32)
            // 'order_type' => '0', // 0 = Normal limit $order, 1 = Post only, 2 = Fill Or Kill, 3 = Immediatel Or Cancel, 4 = Market for futures only
        );
        $clientOrderId = $this->safe_string_2($params, 'client_oid', 'clientOrderId');
        if ($clientOrderId !== null) {
            $request['client_oid'] = $clientOrderId;
            $params = $this->omit($params, array( 'client_oid', 'clientOrderId' ));
        }
        $method = null;
        if ($market['futures'] || $market['swap']) {
            $size = $market['futures'] ? $this->number_to_string($amount) : $this->amount_to_precision($symbol, $amount);
            $request = array_merge($request, array(
                'type' => $type, // 1:open long 2:open short 3:close long 4:close short for futures
                'size' => $size,
                // 'match_price' => '0', // Order at best counter party $price? (0:no 1:yes). The default is 0. If it is set as 1, the $price parameter will be ignored. When posting orders at best bid $price, order_type can only be 0 (regular $order).
            ));
            $orderType = $this->safe_string($params, 'order_type');
            // order_type === '4' means a $market $order
            $isMarketOrder = ($type === 'market') || ($orderType === '4');
            if ($isMarketOrder) {
                $request['order_type'] = '4';
            } else {
                $request['price'] = $this->price_to_precision($symbol, $price);
            }
            if ($market['futures']) {
                $request['leverage'] = '10'; // or '20'
            }
            $method = $market['type'] . 'PostOrder';
        } else {
            $marginTrading = $this->safe_string($params, 'margin_trading', '1');  // 1 = spot, 2 = margin
            $request = array_merge($request, array(
                'side' => $side,
                'type' => $type, // limit/market
                'margin_trading' => $marginTrading, // 1 = spot, 2 = margin
            ));
            if ($type === 'limit') {
                $request['price'] = $this->price_to_precision($symbol, $price);
                $request['size'] = $this->amount_to_precision($symbol, $amount);
            } else if ($type === 'market') {
                // for $market buy it requires the $amount of quote currency to spend
                if ($side === 'buy') {
                    $notional = $this->safe_number($params, 'notional');
                    $createMarketBuyOrderRequiresPrice = $this->safe_value($this->options, 'createMarketBuyOrderRequiresPrice', true);
                    if ($createMarketBuyOrderRequiresPrice) {
                        if ($price !== null) {
                            if ($notional === null) {
                                $notional = $amount * $price;
                            }
                        } else if ($notional === null) {
                            throw new InvalidOrder($this->id . " createOrder() requires the $price argument with $market buy orders to calculate total $order cost ($amount to spend), where cost = $amount * $price-> Supply a $price argument to createOrder() call if you want the cost to be calculated for you from $price and $amount, or, alternatively, add .options['createMarketBuyOrderRequiresPrice'] = false and supply the total cost value in the 'amount' argument or in the 'notional' extra parameter (the exchange-specific behaviour)");
                        }
                    } else {
                        $notional = ($notional === null) ? $amount : $notional;
                    }
                    $precision = $market['precision']['price'];
                    $request['notional'] = $this->decimal_to_precision($notional, TRUNCATE, $precision, $this->precisionMode);
                } else {
                    $request['size'] = $this->amount_to_precision($symbol, $amount);
                }
            }
            $method = ($marginTrading === '2') ? 'marginPostOrders' : 'spotPostOrders';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "client_oid":"oktspot79",
        //         "error_code":"",
        //         "error_message":"",
        //         "order_id":"2510789768709120",
        //         "result":true
        //     }
        //
        $order = $this->parse_order($response, $market);
        return array_merge($order, array(
            'type' => $type,
            'side' => $side,
        ));
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $type = null;
        if ($market['futures'] || $market['swap']) {
            $type = $market['type'];
        } else {
            $defaultType = $this->safe_string_2($this->options, 'cancelOrder', 'defaultType', $market['type']);
            $type = $this->safe_string($params, 'type', $defaultType);
        }
        if ($type === null) {
            throw new ArgumentsRequired($this->id . " cancelOrder() requires a $type parameter (one of 'spot', 'margin', 'futures', 'swap').");
        }
        $method = $type . 'PostCancelOrder';
        $request = array(
            'instrument_id' => $market['id'],
        );
        if ($market['futures'] || $market['swap']) {
            $method .= 'InstrumentId';
        } else {
            $method .= 's';
        }
        $clientOrderId = $this->safe_string_2($params, 'client_oid', 'clientOrderId');
        if ($clientOrderId !== null) {
            $method .= 'ClientOid';
            $request['client_oid'] = $clientOrderId;
        } else {
            $method .= 'OrderId';
            $request['order_id'] = $id;
        }
        $query = $this->omit($params, array( 'type', 'client_oid', 'clientOrderId' ));
        $response = $this->$method (array_merge($request, $query));
        $result = (is_array($response) && array_key_exists('result', $response)) ? $response : $this->safe_value($response, $market['id'], array());
        //
        // spot, margin
        //
        //     {
        //         "btc-usdt" => array(
        //             {
        //                 "$result":true,
        //                 "client_oid":"a123",
        //                 "order_id" => "2510832677225473"
        //             }
        //         )
        //     }
        //
        // futures, swap
        //
        //     {
        //         "$result" => true,
        //         "client_oid" => "oktfuture10", // missing if requested by order_id
        //         "order_id" => "2517535534836736",
        //         "instrument_id" => "EOS-USD-190628"
        //     }
        //
        return $this->parse_order($result, $market);
    }

    public function parse_order_status($status) {
        $statuses = array(
            '-2' => 'failed',
            '-1' => 'canceled',
            '0' => 'open',
            '1' => 'open',
            '2' => 'closed',
            '3' => 'open',
            '4' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order_side($side) {
        $sides = array(
            '1' => 'buy', // open long
            '2' => 'sell', // open short
            '3' => 'sell', // close long
            '4' => 'buy', // close short
        );
        return $this->safe_string($sides, $side, $side);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "client_oid":"oktspot79",
        //         "error_code":"",
        //         "error_message":"",
        //         "order_id":"2510789768709120",
        //         "result":true
        //     }
        //
        // cancelOrder
        //
        //     {
        //         "result" => true,
        //         "client_oid" => "oktfuture10", // missing if requested by order_id
        //         "order_id" => "2517535534836736",
        //         // instrument_id is missing for spot/margin orders
        //         // available in futures and swap orders only
        //         "instrument_id" => "EOS-USD-190628",
        //     }
        //
        // fetchOrder, fetchOrdersByState, fetchOpenOrders, fetchClosedOrders
        //
        //     // spot and margin orders
        //
        //     {
        //         "client_oid":"oktspot76",
        //         "created_at":"2019-03-18T07:26:49.000Z",
        //         "filled_notional":"3.9734",
        //         "filled_size":"0.001", // filled_qty in futures and swap orders
        //         "funds":"", // this is most likely the same as notional
        //         "instrument_id":"BTC-USDT",
        //         "notional":"",
        //         "order_id":"2500723297813504",
        //         "order_type":"0",
        //         "$price":"4013",
        //         "product_id":"BTC-USDT", // missing in futures and swap orders
        //         "$side":"buy",
        //         "size":"0.001",
        //         "$status":"$filled",
        //         "state" => "2",
        //         "$timestamp":"2019-03-18T07:26:49.000Z",
        //         "$type":"limit"
        //     }
        //
        //     // futures and swap orders
        //
        //     {
        //         "instrument_id":"EOS-USD-190628",
        //         "size":"10",
        //         "$timestamp":"2019-03-20T10:04:55.000Z",
        //         "filled_qty":"10", // filled_size in spot and margin orders
        //         "$fee":"-0.00841043",
        //         "order_id":"2512669605501952",
        //         "$price":"3.668",
        //         "price_avg":"3.567", // missing in spot and margin orders
        //         "$status":"2",
        //         "state" => "2",
        //         "$type":"4",
        //         "contract_val":"10",
        //         "leverage":"10", // missing in swap, spot and margin orders
        //         "client_oid":"",
        //         "pnl":"1.09510794", // missing in swap, spo and margin orders
        //         "order_type":"0"
        //     }
        //
        $id = $this->safe_string($order, 'order_id');
        $timestamp = $this->parse8601($this->safe_string($order, 'timestamp'));
        $side = $this->safe_string($order, 'side');
        $type = $this->safe_string($order, 'type');
        if (($side !== 'buy') && ($side !== 'sell')) {
            $side = $this->parse_order_side($type);
        }
        $symbol = null;
        $marketId = $this->safe_string($order, 'instrument_id');
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
            $symbol = $market['symbol'];
        } else {
            $symbol = $marketId;
        }
        if ($market !== null) {
            if ($symbol === null) {
                $symbol = $market['symbol'];
            }
        }
        $amount = $this->safe_number($order, 'size');
        $filled = $this->safe_number_2($order, 'filled_size', 'filled_qty');
        $remaining = null;
        if ($amount !== null) {
            if ($filled !== null) {
                $amount = max ($amount, $filled);
                $remaining = max (0, $amount - $filled);
            }
        }
        if ($type === 'market') {
            $remaining = 0;
        }
        $cost = $this->safe_number_2($order, 'filled_notional', 'funds');
        $price = $this->safe_number($order, 'price');
        $average = $this->safe_number($order, 'price_avg');
        if ($cost === null) {
            if ($filled !== null && $average !== null) {
                $cost = $average * $filled;
            }
        } else {
            if (($average === null) && ($filled !== null) && ($filled > 0)) {
                $average = $cost / $filled;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $feeCost = $this->safe_number($order, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrency = null;
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        $clientOrderId = $this->safe_string($order, 'client_oid');
        if (($clientOrderId !== null) && (strlen($clientOrderId) < 1)) {
            $clientOrderId = null; // fix empty $clientOrderId string
        }
        $stopPrice = $this->safe_number($order, 'trigger_price');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'average' => $average,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string_2($this->options, 'fetchOrder', 'defaultType', $market['type']);
        $type = $this->safe_string($params, 'type', $defaultType);
        if ($type === null) {
            throw new ArgumentsRequired($this->id . " fetchOrder() requires a $type parameter (one of 'spot', 'margin', 'futures', 'swap').");
        }
        $instrumentId = ($market['futures'] || $market['swap']) ? 'InstrumentId' : '';
        $method = $type . 'GetOrders' . $instrumentId;
        $request = array(
            'instrument_id' => $market['id'],
            // 'client_oid' => 'abcdef12345', // optional, [a-z0-9]array(1,32)
            // 'order_id' => $id,
        );
        $clientOid = $this->safe_string($params, 'client_oid');
        if ($clientOid !== null) {
            $method .= 'ClientOid';
            $request['client_oid'] = $clientOid;
        } else {
            $method .= 'OrderId';
            $request['order_id'] = $id;
        }
        $query = $this->omit($params, 'type');
        $response = $this->$method (array_merge($request, $query));
        //
        // spot, margin
        //
        //     {
        //         "client_oid":"oktspot70",
        //         "created_at":"2019-03-15T02:52:56.000Z",
        //         "filled_notional":"3.8886",
        //         "filled_size":"0.001",
        //         "funds":"",
        //         "instrument_id":"BTC-USDT",
        //         "notional":"",
        //         "order_id":"2482659399697408",
        //         "order_type":"0",
        //         "price":"3927.3",
        //         "product_id":"BTC-USDT",
        //         "side":"buy",
        //         "size":"0.001",
        //         "status":"filled",
        //         "state" => "2",
        //         "timestamp":"2019-03-15T02:52:56.000Z",
        //         "$type":"limit"
        //     }
        //
        // futures, swap
        //
        //     {
        //         "instrument_id":"EOS-USD-190628",
        //         "size":"10",
        //         "timestamp":"2019-03-20T02:46:38.000Z",
        //         "filled_qty":"10",
        //         "fee":"-0.0080819",
        //         "order_id":"2510946213248000",
        //         "price":"3.712",
        //         "price_avg":"3.712",
        //         "status":"2",
        //         "state" => "2",
        //         "$type":"2",
        //         "contract_val":"10",
        //         "leverage":"10",
        //         "client_oid":"", // missing in swap orders
        //         "pnl":"0", // missing in swap orders
        //         "order_type":"0"
        //     }
        //
        return $this->parse_order($response);
    }

    public function fetch_orders_by_state($state, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrdersByState() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $type = null;
        if ($market['futures'] || $market['swap']) {
            $type = $market['type'];
        } else {
            $defaultType = $this->safe_string_2($this->options, 'fetchOrder', 'defaultType', $market['type']);
            $type = $this->safe_string($params, 'type', $defaultType);
        }
        if ($type === null) {
            throw new ArgumentsRequired($this->id . " fetchOrdersByState() requires a $type parameter (one of 'spot', 'margin', 'futures', 'swap').");
        }
        $request = array(
            'instrument_id' => $market['id'],
            // '-2' => failed,
            // '-1' => cancelled,
            //  '0' => open ,
            //  '1' => partially filled,
            //  '2' => fully filled,
            //  '3' => submitting,
            //  '4' => cancelling,
            //  '6' => incomplete（open+partially filled),
            //  '7' => complete（cancelled+fully filled),
            'state' => $state,
        );
        $method = $type . 'GetOrders';
        if ($market['futures'] || $market['swap']) {
            $method .= 'InstrumentId';
        }
        $query = $this->omit($params, 'type');
        $response = $this->$method (array_merge($request, $query));
        //
        // spot, margin
        //
        //     array(
        //         // in fact, this documented API $response does not correspond
        //         // to their actual API $response for spot markets
        //         // OKEX v3 API returns a plain array of $orders (see below)
        //         array(
        //             array(
        //                 "client_oid":"oktspot76",
        //                 "created_at":"2019-03-18T07:26:49.000Z",
        //                 "filled_notional":"3.9734",
        //                 "filled_size":"0.001",
        //                 "funds":"",
        //                 "instrument_id":"BTC-USDT",
        //                 "notional":"",
        //                 "order_id":"2500723297813504",
        //                 "order_type":"0",
        //                 "price":"4013",
        //                 "product_id":"BTC-USDT",
        //                 "side":"buy",
        //                 "size":"0.001",
        //                 "status":"filled",
        //                 "$state" => "2",
        //                 "timestamp":"2019-03-18T07:26:49.000Z",
        //                 "$type":"$limit"
        //             ),
        //         ),
        //         {
        //             "$before":"2500723297813504",
        //             "after":"2500650881647616"
        //         }
        //     )
        //
        // futures, swap
        //
        //     {
        //         "result":true,  // missing in swap $orders
        //         "order_info" => array(
        //             array(
        //                 "instrument_id":"EOS-USD-190628",
        //                 "size":"10",
        //                 "timestamp":"2019-03-20T10:04:55.000Z",
        //                 "filled_qty":"10",
        //                 "fee":"-0.00841043",
        //                 "order_id":"2512669605501952",
        //                 "price":"3.668",
        //                 "price_avg":"3.567",
        //                 "status":"2",
        //                 "$state" => "2",
        //                 "$type":"4",
        //                 "contract_val":"10",
        //                 "leverage":"10", // missing in swap $orders
        //                 "client_oid":"",
        //                 "pnl":"1.09510794", // missing in swap $orders
        //                 "order_type":"0"
        //             ),
        //         )
        //     }
        //
        $orders = null;
        if ($market['swap'] || $market['futures']) {
            $orders = $this->safe_value($response, 'order_info', array());
        } else {
            $orders = $response;
            $responseLength = is_array($response) ? count($response) : 0;
            if ($responseLength < 1) {
                return array();
            }
            // in fact, this documented API $response does not correspond
            // to their actual API $response for spot markets
            // OKEX v3 API returns a plain array of $orders
            if ($responseLength > 1) {
                $before = $this->safe_value($response[1], 'before');
                if ($before !== null) {
                    $orders = $response[0];
                }
            }
        }
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        // '-2' => failed,
        // '-1' => cancelled,
        //  '0' => open ,
        //  '1' => partially filled,
        //  '2' => fully filled,
        //  '3' => submitting,
        //  '4' => cancelling,
        //  '6' => incomplete（open+partially filled),
        //  '7' => complete（cancelled+fully filled),
        return $this->fetch_orders_by_state('6', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        // '-2' => failed,
        // '-1' => cancelled,
        //  '0' => open ,
        //  '1' => partially filled,
        //  '2' => fully filled,
        //  '3' => submitting,
        //  '4' => cancelling,
        //  '6' => incomplete（open+partially filled),
        //  '7' => complete（cancelled+fully filled),
        return $this->fetch_orders_by_state('7', $symbol, $since, $limit, $params);
    }

    public function parse_deposit_address($depositAddress, $currency = null) {
        //
        //     {
        //         $address => '0x696abb81974a8793352cbd33aadcf78eda3cfdfa',
        //         $currency => 'eth'
        //         $tag => 'abcde12345', // will be missing if the token does not require a deposit $tag
        //         payment_id => 'abcde12345', // will not be returned if the token does not require a payment_id
        //         // can_deposit => 1, // 0 or 1, documented but missing
        //         // can_withdraw => 1, // 0 or 1, documented but missing
        //     }
        //
        $address = $this->safe_string($depositAddress, 'address');
        $tag = $this->safe_string_2($depositAddress, 'tag', 'payment_id');
        $tag = $this->safe_string($depositAddress, 'memo', $tag);
        $currencyId = $this->safe_string($depositAddress, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $depositAddress,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $parts = explode('-', $code);
        $currency = $this->currency($parts[0]);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->accountGetDepositAddress (array_merge($request, $params));
        //
        //     array(
        //         {
        //             $address => '0x696abb81974a8793352cbd33aadcf78eda3cfdfa',
        //             $currency => 'eth'
        //         }
        //     )
        //
        $addressesByCode = $this->parse_deposit_addresses($response);
        $address = $this->safe_value($addressesByCode, $code);
        if ($address === null) {
            throw new InvalidAddress($this->id . ' fetchDepositAddress cannot return nonexistent addresses, you should create withdrawal addresses with the exchange website first');
        }
        return $address;
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        if ($tag) {
            $address = $address . ':' . $tag;
        }
        $fee = $this->safe_string($params, 'fee');
        if ($fee === null) {
            throw new ArgumentsRequired($this->id . " withdraw() requires a `$fee` string parameter, network transaction $fee must be ≥ 0. Withdrawals to OKCoin or OKEx are $fee-free, please set '0'. Withdrawing to external digital asset $address requires network transaction $fee->");
        }
        $request = array(
            'currency' => $currency['id'],
            'to_address' => $address,
            'destination' => '4', // 2 = OKCoin International, 3 = OKEx 4 = others
            'amount' => $this->number_to_string($amount),
            'fee' => $fee, // String. Network transaction $fee ≥ 0. Withdrawals to OKCoin or OKEx are $fee-free, please set as 0. Withdrawal to external digital asset $address requires network transaction $fee->
        );
        if (is_array($params) && array_key_exists('password', $params)) {
            $request['trade_pwd'] = $params['password'];
        } else if (is_array($params) && array_key_exists('trade_pwd', $params)) {
            $request['trade_pwd'] = $params['trade_pwd'];
        } else if ($this->password) {
            $request['trade_pwd'] = $this->password;
        }
        $query = $this->omit($params, array( 'fee', 'password', 'trade_pwd' ));
        if (!(is_array($request) && array_key_exists('trade_pwd', $request))) {
            throw new ExchangeError($this->id . ' withdraw() requires $this->password set on the exchange instance or a password / trade_pwd parameter');
        }
        $response = $this->accountPostWithdrawal (array_merge($request, $query));
        //
        //     {
        //         "$amount":"0.1",
        //         "withdrawal_id":"67485",
        //         "$currency":"btc",
        //         "result":true
        //     }
        //
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'withdrawal_id'),
        );
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $method = 'accountGetDepositHistory';
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
            $method .= 'Currency';
        }
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_transactions($response, $currency, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $method = 'accountGetWithdrawalHistory';
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
            $method .= 'Currency';
        }
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_transactions($response, $currency, $since, $limit, $params);
    }

    public function parse_transaction_status($status) {
        //
        // deposit $statuses
        //
        //     {
        //         '0' => 'waiting for confirmation',
        //         '1' => 'confirmation account',
        //         '2' => 'recharge success'
        //     }
        //
        // withdrawal statues
        //
        //     {
        //        '-3' => 'pending cancel',
        //        '-2' => 'cancelled',
        //        '-1' => 'failed',
        //         '0' => 'pending',
        //         '1' => 'sending',
        //         '2' => 'sent',
        //         '3' => 'email confirmation',
        //         '4' => 'manual confirmation',
        //         '5' => 'awaiting identity confirmation'
        //     }
        //
        $statuses = array(
            '-3' => 'pending',
            '-2' => 'canceled',
            '-1' => 'failed',
            '0' => 'pending',
            '1' => 'pending',
            '2' => 'ok',
            '3' => 'pending',
            '4' => 'pending',
            '5' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // withdraw
        //
        //     {
        //         "$amount":"0.1",
        //         "withdrawal_id":"67485",
        //         "$currency":"btc",
        //         "result":true
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         $amount => "4.72100000",
        //         withdrawal_id => "1729116",
        //         fee => "0.01000000eth",
        //         $txid => "0xf653125bbf090bcfe4b5e8e7b8f586a9d87aa7de94598702758c0802b…",
        //         $currency => "ETH",
        //         from => "7147338839",
        //         to => "0x26a3CB49578F07000575405a57888681249c35Fd",
        //         $timestamp => "2018-08-17T07:03:42.000Z",
        //         $status => "2"
        //     }
        //
        // fetchDeposits
        //
        //     {
        //         "$amount" => "4.19511659",
        //         "$txid" => "14c9a8c925647cdb7e5b2937ea9aefe2b29b2c273150ad3f44b3b8a4635ed437",
        //         "$currency" => "XMR",
        //         "from" => "",
        //         "to" => "48PjH3ksv1fiXniKvKvyH5UtFs5WhfS2Vf7U3TwzdRJtCc7HJWvCQe56dRahyhQyTAViXZ8Nzk4gQg6o4BJBMUoxNy8y8g7",
        //         "tag" => "1234567",
        //         "deposit_id" => 11571659, <-- we can use this
        //         "$timestamp" => "2019-10-01T14:54:19.000Z",
        //         "$status" => "2"
        //     }
        //
        $type = null;
        $id = null;
        $address = null;
        $withdrawalId = $this->safe_string($transaction, 'withdrawal_id');
        $addressFrom = $this->safe_string($transaction, 'from');
        $addressTo = $this->safe_string($transaction, 'to');
        $tagTo = $this->safe_string($transaction, 'tag');
        if ($withdrawalId !== null) {
            $type = 'withdrawal';
            $id = $withdrawalId;
            $address = $addressTo;
        } else {
            // the payment_id will appear on new deposits but appears to be removed from the response after 2 months
            $id = $this->safe_string_2($transaction, 'payment_id', 'deposit_id');
            $type = 'deposit';
            $address = $addressTo;
        }
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $amount = $this->safe_number($transaction, 'amount');
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $txid = $this->safe_string($transaction, 'txid');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'timestamp'));
        $feeCost = null;
        if ($type === 'deposit') {
            $feeCost = 0;
        } else {
            if ($currencyId !== null) {
                $feeWithCurrencyId = $this->safe_string($transaction, 'fee');
                if ($feeWithCurrencyId !== null) {
                    // https://github.com/ccxt/ccxt/pull/5748
                    $lowercaseCurrencyId = strtolower($currencyId);
                    $feeWithoutCurrencyId = str_replace($lowercaseCurrencyId, '', $feeWithCurrencyId);
                    $feeCost = floatval($feeWithoutCurrencyId);
                }
            }
        }
        // todo parse tags
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'addressFrom' => $addressFrom,
            'addressTo' => $addressTo,
            'address' => $address,
            'tagFrom' => null,
            'tagTo' => $tagTo,
            'tag' => $tagTo,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => array(
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function parse_my_trade($pair, $market = null) {
        // check that trading symbols match in both entries
        $userTrade = $this->safe_value($pair, 1);
        $otherTrade = $this->safe_value($pair, 0);
        $firstMarketId = $this->safe_string($otherTrade, 'instrument_id');
        $secondMarketId = $this->safe_string($userTrade, 'instrument_id');
        if ($firstMarketId !== $secondMarketId) {
            throw new NotSupported($this->id . ' parseMyTrade() received unrecognized response format, differing instrument_ids in one fill, the exchange API might have changed, paste your verbose output => https://github.com/ccxt/ccxt/wiki/FAQ#what-is-required-to-get-help');
        }
        $marketId = $firstMarketId;
        $market = $this->safe_market($marketId, $market);
        $symbol = $market['symbol'];
        $quoteId = $market['quoteId'];
        $side = null;
        $amount = null;
        $cost = null;
        $receivedCurrencyId = $this->safe_string($userTrade, 'currency');
        $feeCurrencyId = null;
        if ($receivedCurrencyId === $quoteId) {
            $side = $this->safe_string($otherTrade, 'side');
            $amount = $this->safe_number($otherTrade, 'size');
            $cost = $this->safe_number($userTrade, 'size');
            $feeCurrencyId = $this->safe_string($otherTrade, 'currency');
        } else {
            $side = $this->safe_string($userTrade, 'side');
            $amount = $this->safe_number($userTrade, 'size');
            $cost = $this->safe_number($otherTrade, 'size');
            $feeCurrencyId = $this->safe_string($userTrade, 'currency');
        }
        $id = $this->safe_string($userTrade, 'trade_id');
        $price = $this->safe_number($userTrade, 'price');
        $feeCostFirst = $this->safe_number($otherTrade, 'fee');
        $feeCostSecond = $this->safe_number($userTrade, 'fee');
        $feeCurrencyCodeFirst = $this->safe_currency_code($this->safe_string($otherTrade, 'currency'));
        $feeCurrencyCodeSecond = $this->safe_currency_code($this->safe_string($userTrade, 'currency'));
        $fee = null;
        $fees = null;
        // $fee is either a positive number (invitation rebate)
        // or a negative number (transaction $fee deduction)
        // therefore we need to invert the $fee
        // more about it https://github.com/ccxt/ccxt/issues/5909
        if (($feeCostFirst !== null) && ($feeCostFirst !== 0)) {
            if (($feeCostSecond !== null) && ($feeCostSecond !== 0)) {
                $fees = array(
                    array(
                        'cost' => -$feeCostFirst,
                        'currency' => $feeCurrencyCodeFirst,
                    ),
                    array(
                        'cost' => -$feeCostSecond,
                        'currency' => $feeCurrencyCodeSecond,
                    ),
                );
            } else {
                $fee = array(
                    'cost' => -$feeCostFirst,
                    'currency' => $feeCurrencyCodeFirst,
                );
            }
        } else if (($feeCostSecond !== null) && ($feeCostSecond !== 0)) {
            $fee = array(
                'cost' => -$feeCostSecond,
                'currency' => $feeCurrencyCodeSecond,
            );
        } else {
            $fee = array(
                'cost' => 0,
                'currency' => $this->safe_currency_code($feeCurrencyId),
            );
        }
        //
        // simplified structures to show the underlying semantics
        //
        //     // market/limit sell
        //
        //     array(
        //         "currency":"USDT",
        //         "$fee":"-0.04647925", // ←--- $fee in received quote currency
        //         "$price":"129.13", // ←------ $price
        //         "size":"30.98616393", // ←-- $cost
        //     ),
        //     array(
        //         "currency":"ETH",
        //         "$fee":"0",
        //         "$price":"129.13",
        //         "size":"0.23996099", // ←--- $amount
        //     ),
        //
        //     // market/limit buy
        //
        //     array(
        //         "currency":"ETH",
        //         "$fee":"-0.00036049", // ←--- $fee in received base currency
        //         "$price":"129.16", // ←------ $price
        //         "size":"0.240322", // ←----- $amount
        //     ),
        //     {
        //         "currency":"USDT",
        //         "$fee":"0",
        //         "$price":"129.16",
        //         "size":"31.03998952", // ←-- $cost
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string_2($userTrade, 'timestamp', 'created_at'));
        $takerOrMaker = $this->safe_string_2($userTrade, 'exec_type', 'liquidity');
        if ($takerOrMaker === 'M') {
            $takerOrMaker = 'maker';
        } else if ($takerOrMaker === 'T') {
            $takerOrMaker = 'taker';
        }
        $orderId = $this->safe_string($userTrade, 'order_id');
        $result = array(
            'info' => $pair,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $orderId,
            'type' => null,
            'takerOrMaker' => $takerOrMaker,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
        if ($fees !== null) {
            $result['fees'] = $fees;
        }
        return $result;
    }

    public function parse_my_trades($trades, $market = null, $since = null, $limit = null, $params = array ()) {
        $grouped = $this->group_by($trades, 'trade_id');
        $tradeIds = is_array($grouped) ? array_keys($grouped) : array();
        $result = array();
        for ($i = 0; $i < count($tradeIds); $i++) {
            $tradeId = $tradeIds[$i];
            $pair = $grouped[$tradeId];
            // make sure it has exactly 2 $trades, no more, no less
            $numTradesInPair = is_array($pair) ? count($pair) : 0;
            if ($numTradesInPair === 2) {
                $trade = $this->parse_my_trade($pair);
                $result[] = $trade;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return $this->filter_by_symbol_since_limit($result, $symbol, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        // okex actually returns ledger entries instead of fills here, so each fill in the order
        // is represented by two trades with opposite buy/sell sides, not one :\
        // this aspect renders the 'fills' endpoint unusable for fetchOrderTrades
        // until either OKEX fixes the API or we workaround this on our side somehow
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        if (($limit !== null) && ($limit > 100)) {
            $limit = 100;
        }
        $request = array(
            'instrument_id' => $market['id'],
            // 'order_id' => id, // string
            // 'after' => '1', // pagination of data to return records earlier than the requested ledger_id
            // 'before' => '1', // P=pagination of data to return records newer than the requested ledger_id
            // 'limit' => $limit, // optional, number of results per $request, default = maximum = 100
        );
        $defaultType = $this->safe_string_2($this->options, 'fetchMyTrades', 'defaultType');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        $method = $type . 'GetFills';
        $response = $this->$method (array_merge($request, $query));
        //
        //     array(
        //         // sell
        //         array(
        //             "created_at":"2020-03-29T11:55:25.000Z",
        //             "currency":"USDT",
        //             "exec_type":"T",
        //             "fee":"-0.04647925",
        //             "instrument_id":"ETH-USDT",
        //             "ledger_id":"10562924353",
        //             "liquidity":"T",
        //             "order_id":"4636470489136128",
        //             "price":"129.13",
        //             "product_id":"ETH-USDT",
        //             "side":"buy",
        //             "size":"30.98616393",
        //             "timestamp":"2020-03-29T11:55:25.000Z",
        //             "trade_id":"18551601"
        //         ),
        //         array(
        //             "created_at":"2020-03-29T11:55:25.000Z",
        //             "currency":"ETH",
        //             "exec_type":"T",
        //             "fee":"0",
        //             "instrument_id":"ETH-USDT",
        //             "ledger_id":"10562924352",
        //             "liquidity":"T",
        //             "order_id":"4636470489136128",
        //             "price":"129.13",
        //             "product_id":"ETH-USDT",
        //             "side":"sell",
        //             "size":"0.23996099",
        //             "timestamp":"2020-03-29T11:55:25.000Z",
        //             "trade_id":"18551601"
        //         ),
        //         // buy
        //         array(
        //             "created_at":"2020-03-29T11:55:16.000Z",
        //             "currency":"ETH",
        //             "exec_type":"T",
        //             "fee":"-0.00036049",
        //             "instrument_id":"ETH-USDT",
        //             "ledger_id":"10562922669",
        //             "liquidity":"T",
        //             "order_id" => "4636469894136832",
        //             "price":"129.16",
        //             "product_id":"ETH-USDT",
        //             "side":"buy",
        //             "size":"0.240322",
        //             "timestamp":"2020-03-29T11:55:16.000Z",
        //             "trade_id":"18551600"
        //         ),
        //         {
        //             "created_at":"2020-03-29T11:55:16.000Z",
        //             "currency":"USDT",
        //             "exec_type":"T",
        //             "fee":"0",
        //             "instrument_id":"ETH-USDT",
        //             "ledger_id":"10562922668",
        //             "liquidity":"T",
        //             "order_id":"4636469894136832",
        //             "price":"129.16",
        //             "product_id":"ETH-USDT",
        //             "side":"sell",
        //             "size":"31.03998952",
        //             "timestamp":"2020-03-29T11:55:16.000Z",
        //             "trade_id":"18551600"
        //         }
        //     )
        //
        return $this->parse_my_trades($response, $market, $since, $limit, $params);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            // 'instrument_id' => market['id'],
            'order_id' => $id,
            // 'after' => '1', // return the page after the specified page number
            // 'before' => '1', // return the page before the specified page number
            // 'limit' => $limit, // optional, number of results per $request, default = maximum = 100
        );
        return $this->fetch_my_trades($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_position($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $method = null;
        $request = array(
            'instrument_id' => $market['id'],
            // 'order_id' => id, // string
            // 'after' => '1', // pagination of data to return records earlier than the requested ledger_id
            // 'before' => '1', // P=pagination of data to return records newer than the requested ledger_id
            // 'limit' => limit, // optional, number of results per $request, default = maximum = 100
        );
        $type = $market['type'];
        if (($type === 'futures') || ($type === 'swap')) {
            $method = $type . 'GetInstrumentIdPosition';
        } else if ($type === 'option') {
            $underlying = $this->safe_string($params, 'underlying');
            if ($underlying === null) {
                throw new ArgumentsRequired($this->id . ' fetchPosition() requires an $underlying parameter for ' . $type . ' $market ' . $symbol);
            }
            $method = $type . 'GetUnderlyingPosition';
        } else {
            throw new NotSupported($this->id . ' fetchPosition() does not support ' . $type . ' $market ' . $symbol . ', supported $market types are futures, swap or option');
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // futures
        //
        //     crossed margin mode
        //
        //     {
        //         "result" => true,
        //         "holding" => array(
        //             {
        //                 "long_qty" => "2",
        //                 "long_avail_qty" => "2",
        //                 "long_avg_cost" => "8260",
        //                 "long_settlement_price" => "8260",
        //                 "realised_pnl" => "0.00020928",
        //                 "short_qty" => "2",
        //                 "short_avail_qty" => "2",
        //                 "short_avg_cost" => "8259.99",
        //                 "short_settlement_price" => "8259.99",
        //                 "liquidation_price" => "113.81",
        //                 "instrument_id" => "BTC-USD-191227",
        //                 "leverage" => "10",
        //                 "created_at" => "2019-09-25T07:58:42.129Z",
        //                 "updated_at" => "2019-10-08T14:02:51.029Z",
        //                 "margin_mode" => "crossed",
        //                 "short_margin" => "0.00242197",
        //                 "short_pnl" => "6.63E-6",
        //                 "short_pnl_ratio" => "0.002477997",
        //                 "short_unrealised_pnl" => "6.63E-6",
        //                 "long_margin" => "0.00242197",
        //                 "long_pnl" => "-6.65E-6",
        //                 "long_pnl_ratio" => "-0.002478",
        //                 "long_unrealised_pnl" => "-6.65E-6",
        //                 "long_settled_pnl" => "0",
        //                 "short_settled_pnl" => "0",
        //                 "last" => "8257.57"
        //             }
        //         ),
        //         "margin_mode" => "crossed"
        //     }
        //
        //     fixed margin mode
        //
        //     {
        //         "result" => true,
        //         "holding" => array(
        //             {
        //                 "long_qty" => "4",
        //                 "long_avail_qty" => "4",
        //                 "long_margin" => "0.00323844",
        //                 "long_liqui_price" => "7762.09",
        //                 "long_pnl_ratio" => "0.06052306",
        //                 "long_avg_cost" => "8234.43",
        //                 "long_settlement_price" => "8234.43",
        //                 "realised_pnl" => "-0.00000296",
        //                 "short_qty" => "2",
        //                 "short_avail_qty" => "2",
        //                 "short_margin" => "0.00241105",
        //                 "short_liqui_price" => "9166.74",
        //                 "short_pnl_ratio" => "0.03318052",
        //                 "short_avg_cost" => "8295.13",
        //                 "short_settlement_price" => "8295.13",
        //                 "instrument_id" => "BTC-USD-191227",
        //                 "long_leverage" => "15",
        //                 "short_leverage" => "10",
        //                 "created_at" => "2019-09-25T07:58:42.129Z",
        //                 "updated_at" => "2019-10-08T13:12:09.438Z",
        //                 "margin_mode" => "fixed",
        //                 "short_margin_ratio" => "0.10292507",
        //                 "short_maint_margin_ratio" => "0.005",
        //                 "short_pnl" => "7.853E-5",
        //                 "short_unrealised_pnl" => "7.853E-5",
        //                 "long_margin_ratio" => "0.07103743",
        //                 "long_maint_margin_ratio" => "0.005",
        //                 "long_pnl" => "1.9841E-4",
        //                 "long_unrealised_pnl" => "1.9841E-4",
        //                 "long_settled_pnl" => "0",
        //                 "short_settled_pnl" => "0",
        //                 "last" => "8266.99"
        //             }
        //         ),
        //         "margin_mode" => "fixed"
        //     }
        //
        // swap
        //
        //     crossed margin mode
        //
        //     {
        //         "margin_mode" => "crossed",
        //         "timestamp" => "2019-09-27T03:49:02.018Z",
        //         "holding" => array(
        //             array(
        //                 "avail_position" => "3",
        //                 "avg_cost" => "59.49",
        //                 "instrument_id" => "LTC-USD-SWAP",
        //                 "last" => "55.98",
        //                 "leverage" => "10.00",
        //                 "liquidation_price" => "4.37",
        //                 "maint_margin_ratio" => "0.0100",
        //                 "margin" => "0.0536",
        //                 "position" => "3",
        //                 "realized_pnl" => "0.0000",
        //                 "unrealized_pnl" => "0",
        //                 "settled_pnl" => "-0.0330",
        //                 "settlement_price" => "55.84",
        //                 "side" => "long",
        //                 "timestamp" => "2019-09-27T03:49:02.018Z"
        //             ),
        //         )
        //     }
        //
        //     fixed margin mode
        //
        //     {
        //         "margin_mode" => "fixed",
        //         "timestamp" => "2019-09-27T03:47:37.230Z",
        //         "holding" => array(
        //             {
        //                 "avail_position" => "20",
        //                 "avg_cost" => "8025.0",
        //                 "instrument_id" => "BTC-USD-SWAP",
        //                 "last" => "8113.1",
        //                 "leverage" => "15.00",
        //                 "liquidation_price" => "7002.6",
        //                 "maint_margin_ratio" => "0.0050",
        //                 "margin" => "0.0454",
        //                 "position" => "20",
        //                 "realized_pnl" => "-0.0001",
        //                 "unrealized_pnl" => "0",
        //                 "settled_pnl" => "0.0076",
        //                 "settlement_price" => "8279.2",
        //                 "side" => "long",
        //                 "timestamp" => "2019-09-27T03:47:37.230Z"
        //             }
        //         )
        //     }
        //
        // option
        //
        //     {
        //         "holding":array(
        //             array(
        //                 "instrument_id":"BTC-USD-190927-12500-C",
        //                 "position":"20",
        //                 "avg_cost":"3.26",
        //                 "avail_position":"20",
        //                 "settlement_price":"0.017",
        //                 "total_pnl":"50",
        //                 "pnl_ratio":"0.3",
        //                 "realized_pnl":"40",
        //                 "unrealized_pnl":"10",
        //                 "pos_margin":"100",
        //                 "option_value":"70",
        //                 "created_at":"2019-08-30T03:09:20.315Z",
        //                 "updated_at":"2019-08-30T03:40:18.318Z"
        //             ),
        //             {
        //                 "instrument_id":"BTC-USD-190927-12500-P",
        //                 "position":"20",
        //                 "avg_cost":"3.26",
        //                 "avail_position":"20",
        //                 "settlement_price":"0.019",
        //                 "total_pnl":"50",
        //                 "pnl_ratio":"0.3",
        //                 "realized_pnl":"40",
        //                 "unrealized_pnl":"10",
        //                 "pos_margin":"100",
        //                 "option_value":"70",
        //                 "created_at":"2019-08-30T03:09:20.315Z",
        //                 "updated_at":"2019-08-30T03:40:18.318Z"
        //             }
        //         )
        //     }
        //
        // todo unify parsePosition/parsePositions
        return $response;
    }

    public function fetch_positions($symbols = null, $params = array ()) {
        $this->load_markets();
        $method = null;
        $defaultType = $this->safe_string_2($this->options, 'fetchPositions', 'defaultType');
        $type = $this->safe_string($params, 'type', $defaultType);
        if (($type === 'futures') || ($type === 'swap')) {
            $method = $type . 'GetPosition';
        } else if ($type === 'option') {
            $underlying = $this->safe_string($params, 'underlying');
            if ($underlying === null) {
                throw new ArgumentsRequired($this->id . ' fetchPositions() requires an $underlying parameter for ' . $type . ' markets');
            }
            $method = $type . 'GetUnderlyingPosition';
        } else {
            throw new NotSupported($this->id . ' fetchPositions() does not support ' . $type . ' markets, supported market types are futures, swap or option');
        }
        $params = $this->omit($params, 'type');
        $response = $this->$method ($params);
        //
        // futures
        //
        //     ...
        //
        //
        // swap
        //
        //     ...
        //
        // option
        //
        //     {
        //         "holding":array(
        //             array(
        //                 "instrument_id":"BTC-USD-190927-12500-C",
        //                 "position":"20",
        //                 "avg_cost":"3.26",
        //                 "avail_position":"20",
        //                 "settlement_price":"0.017",
        //                 "total_pnl":"50",
        //                 "pnl_ratio":"0.3",
        //                 "realized_pnl":"40",
        //                 "unrealized_pnl":"10",
        //                 "pos_margin":"100",
        //                 "option_value":"70",
        //                 "created_at":"2019-08-30T03:09:20.315Z",
        //                 "updated_at":"2019-08-30T03:40:18.318Z"
        //             ),
        //             {
        //                 "instrument_id":"BTC-USD-190927-12500-P",
        //                 "position":"20",
        //                 "avg_cost":"3.26",
        //                 "avail_position":"20",
        //                 "settlement_price":"0.019",
        //                 "total_pnl":"50",
        //                 "pnl_ratio":"0.3",
        //                 "realized_pnl":"40",
        //                 "unrealized_pnl":"10",
        //                 "pos_margin":"100",
        //                 "option_value":"70",
        //                 "created_at":"2019-08-30T03:09:20.315Z",
        //                 "updated_at":"2019-08-30T03:40:18.318Z"
        //             }
        //         )
        //     }
        //
        // todo unify parsePosition/parsePositions
        return $response;
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string_2($this->options, 'fetchLedger', 'defaultType');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        $suffix = ($type === 'account') ? '' : 'Accounts';
        $argument = '';
        $request = array(
            // 'from' => 'id',
            // 'to' => 'id',
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $currency = null;
        if ($type === 'spot') {
            if ($code === null) {
                throw new ArgumentsRequired($this->id . " fetchLedger() requires a $currency $code $argument for '" . $type . "' markets");
            }
            $argument = 'Currency';
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        } else if ($type === 'futures') {
            if ($code === null) {
                throw new ArgumentsRequired($this->id . " fetchLedger() requires an underlying symbol for '" . $type . "' markets");
            }
            $argument = 'Underlying';
            $market = $this->market($code); // we intentionally put a $market inside here for the margin and swap ledgers
            $marketInfo = $this->safe_value($market, 'info', array());
            $settlementCurrencyId = $this->safe_string($marketInfo, 'settlement_currency');
            $settlementCurrencyСode = $this->safe_currency_code($settlementCurrencyId);
            $currency = $this->currency($settlementCurrencyСode);
            $underlyingId = $this->safe_string($marketInfo, 'underlying');
            $request['underlying'] = $underlyingId;
        } else if (($type === 'margin') || ($type === 'swap')) {
            if ($code === null) {
                throw new ArgumentsRequired($this->id . " fetchLedger() requires a $code $argument (a $market symbol) for '" . $type . "' markets");
            }
            $argument = 'InstrumentId';
            $market = $this->market($code); // we intentionally put a $market inside here for the margin and swap ledgers
            $currency = $this->currency($market['base']);
            $request['instrument_id'] = $market['id'];
            //
            //     if ($type === 'margin') {
            //         //
            //         //      3. Borrow
            //         //      4. Repayment
            //         //      5. Interest
            //         //      7. Buy
            //         //      8. Sell
            //         //      9. From capital account
            //         //     10. From C2C
            //         //     11. From Futures
            //         //     12. From Spot
            //         //     13. From ETT
            //         //     14. To capital account
            //         //     15. To C2C
            //         //     16. To Spot
            //         //     17. To Futures
            //         //     18. To ETT
            //         //     19. Mandatory Repayment
            //         //     20. From Piggybank
            //         //     21. To Piggybank
            //         //     22. From Perpetual
            //         //     23. To Perpetual
            //         //     24. Liquidation Fee
            //         //     54. Clawback
            //         //     59. Airdrop Return.
            //         //
            //         $request['type'] = 'number'; // All types will be returned if this filed is left blank
            //     }
            //
        } else if ($type === 'account') {
            if ($code !== null) {
                $currency = $this->currency($code);
                $request['currency'] = $currency['id'];
            }
            //
            //     //
            //     //      1. deposit
            //     //      2. withdrawal
            //     //     13. cancel withdrawal
            //     //     18. into futures account
            //     //     19. out of futures account
            //     //     20. into sub account
            //     //     21. out of sub account
            //     //     28. claim
            //     //     29. into ETT account
            //     //     30. out of ETT account
            //     //     31. into C2C account
            //     //     32. out of C2C account
            //     //     33. into margin account
            //     //     34. out of margin account
            //     //     37. into spot account
            //     //     38. out of spot account
            //     //
            //     $request['type'] = 'number';
            //
        } else {
            throw new NotSupported($this->id . " fetchLedger does not support the '" . $type . "' $type (the $type must be one of 'account', 'spot', 'margin', 'futures', 'swap')");
        }
        $method = $type . 'Get' . $suffix . $argument . 'Ledger';
        $response = $this->$method (array_merge($request, $query));
        //
        // transfer     funds transfer in/out
        // trade        funds moved as a result of a trade, spot and margin accounts only
        // rebate       fee rebate as per fee schedule, spot and margin accounts only
        // match        open long/open short/close long/close short (futures) or a change in the amount because of trades (swap)
        // fee          fee, futures only
        // settlement   settlement/clawback/settle long/settle short
        // liquidation  force close long/force close short/deliver close long/deliver close short
        // funding      funding fee, swap only
        // margin       a change in the amount after adjusting margin, swap only
        //
        // account
        //
        //     array(
        //         {
        //             "amount":0.00051843,
        //             "balance":0.00100941,
        //             "$currency":"BTC",
        //             "fee":0,
        //             "ledger_id":8987285,
        //             "timestamp":"2018-10-12T11:01:14.000Z",
        //             "typename":"Get from activity"
        //         }
        //     )
        //
        // spot
        //
        //     array(
        //         {
        //             "timestamp":"2019-03-18T07:08:25.000Z",
        //             "ledger_id":"3995334780",
        //             "created_at":"2019-03-18T07:08:25.000Z",
        //             "$currency":"BTC",
        //             "amount":"0.0009985",
        //             "balance":"0.0029955",
        //             "$type":"trade",
        //             "details":{
        //                 "instrument_id":"BTC-USDT",
        //                 "order_id":"2500650881647616",
        //                 "product_id":"BTC-USDT"
        //             }
        //         }
        //     )
        //
        // margin
        //
        //     array(
        //         array(
        //             {
        //                 "created_at":"2019-03-20T03:45:05.000Z",
        //                 "ledger_id":"78918186",
        //                 "timestamp":"2019-03-20T03:45:05.000Z",
        //                 "$currency":"EOS",
        //                 "amount":"0", // ?
        //                 "balance":"0.59957711",
        //                 "$type":"transfer",
        //                 "details":{
        //                     "instrument_id":"EOS-USDT",
        //                     "order_id":"787057",
        //                     "product_id":"EOS-USDT"
        //                 }
        //             }
        //         ),
        //         {
        //             "before":"78965766",
        //             "after":"78918186"
        //         }
        //     )
        //
        // futures
        //
        //     array(
        //         {
        //             "ledger_id":"2508090544914461",
        //             "timestamp":"2019-03-19T14:40:24.000Z",
        //             "amount":"-0.00529521",
        //             "balance":"0",
        //             "$currency":"EOS",
        //             "$type":"fee",
        //             "details":{
        //                 "order_id":"2506982456445952",
        //                 "instrument_id":"EOS-USD-190628"
        //             }
        //         }
        //     )
        //
        // swap
        //
        //     array(
        //         array(
        //             "amount":"0.004742",
        //             "fee":"-0.000551",
        //             "$type":"match",
        //             "instrument_id":"EOS-USD-SWAP",
        //             "ledger_id":"197429674941902848",
        //             "timestamp":"2019-03-25T05:56:31.286Z"
        //         ),
        //     )
        //
        $responseLength = is_array($response) ? count($response) : 0;
        if ($responseLength < 1) {
            return array();
        }
        $isArray = gettype($response[0]) === 'array' && count(array_filter(array_keys($response[0]), 'is_string')) == 0;
        $isMargin = ($type === 'margin');
        $entries = ($isMargin && $isArray) ? $response[0] : $response;
        if ($type === 'swap') {
            $ledgerEntries = $this->parse_ledger($entries);
            return $this->filter_by_symbol_since_limit($ledgerEntries, $code, $since, $limit);
        }
        return $this->parse_ledger($entries, $currency, $since, $limit);
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'transfer' => 'transfer', // // funds transfer in/out
            'trade' => 'trade', // funds moved as a result of a trade, spot and margin accounts only
            'rebate' => 'rebate', // fee rebate as per fee schedule, spot and margin accounts only
            'match' => 'trade', // open long/open short/close long/close short (futures) or a change in the amount because of trades (swap)
            'fee' => 'fee', // fee, futures only
            'settlement' => 'trade', // settlement/clawback/settle long/settle short
            'liquidation' => 'trade', // force close long/force close short/deliver close long/deliver close short
            'funding' => 'fee', // funding fee, swap only
            'margin' => 'margin', // a change in the amount after adjusting margin, swap only
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        //
        // $account
        //
        //     {
        //         "$amount":0.00051843,
        //         "balance":0.00100941,
        //         "$currency":"BTC",
        //         "$fee":0,
        //         "ledger_id":8987285,
        //         "$timestamp":"2018-10-12T11:01:14.000Z",
        //         "typename":"Get from activity"
        //     }
        //
        // spot
        //
        //     {
        //         "$timestamp":"2019-03-18T07:08:25.000Z",
        //         "ledger_id":"3995334780",
        //         "created_at":"2019-03-18T07:08:25.000Z",
        //         "$currency":"BTC",
        //         "$amount":"0.0009985",
        //         "balance":"0.0029955",
        //         "$type":"trade",
        //         "$details":{
        //             "instrument_id":"BTC-USDT",
        //             "order_id":"2500650881647616",
        //             "product_id":"BTC-USDT"
        //         }
        //     }
        //
        // margin
        //
        //     {
        //         "created_at":"2019-03-20T03:45:05.000Z",
        //         "ledger_id":"78918186",
        //         "$timestamp":"2019-03-20T03:45:05.000Z",
        //         "$currency":"EOS",
        //         "$amount":"0", // ?
        //         "balance":"0.59957711",
        //         "$type":"transfer",
        //         "$details":{
        //             "instrument_id":"EOS-USDT",
        //             "order_id":"787057",
        //             "product_id":"EOS-USDT"
        //         }
        //     }
        //
        // futures
        //
        //     {
        //         "ledger_id":"2508090544914461",
        //         "$timestamp":"2019-03-19T14:40:24.000Z",
        //         "$amount":"-0.00529521",
        //         "balance":"0",
        //         "$currency":"EOS",
        //         "$type":"$fee",
        //         "$details":{
        //             "order_id":"2506982456445952",
        //             "instrument_id":"EOS-USD-190628"
        //         }
        //     }
        //
        // swap
        //
        //     array(
        //         "$amount":"0.004742",
        //         "$fee":"-0.000551",
        //         "$type":"match",
        //         "instrument_id":"EOS-USD-SWAP",
        //         "ledger_id":"197429674941902848",
        //         "$timestamp":"2019-03-25T05:56:31.286Z"
        //     ),
        //
        $id = $this->safe_string($item, 'ledger_id');
        $account = null;
        $details = $this->safe_value($item, 'details', array());
        $referenceId = $this->safe_string($details, 'order_id');
        $referenceAccount = null;
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'type'));
        $code = $this->safe_currency_code($this->safe_string($item, 'currency'), $currency);
        $amount = $this->safe_number($item, 'amount');
        $timestamp = $this->parse8601($this->safe_string($item, 'timestamp'));
        $fee = array(
            'cost' => $this->safe_number($item, 'fee'),
            'currency' => $code,
        );
        $before = null;
        $after = $this->safe_number($item, 'balance');
        $status = 'ok';
        $marketId = $this->safe_string($item, 'instrument_id');
        $symbol = null;
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $item,
            'id' => $id,
            'account' => $account,
            'referenceId' => $referenceId,
            'referenceAccount' => $referenceAccount,
            'type' => $type,
            'currency' => $code,
            'symbol' => $symbol,
            'amount' => $amount,
            'before' => $before, // balance $before
            'after' => $after, // balance $after
            'status' => $status,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => $fee,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $isArray = gettype($params) === 'array' && count(array_filter(array_keys($params), 'is_string')) == 0;
        $request = '/api/' . $api . '/' . $this->version . '/';
        $request .= $isArray ? $path : $this->implode_params($path, $params);
        $query = $isArray ? $params : $this->omit($params, $this->extract_params($path));
        $url = $this->implode_params($this->urls['api']['rest'], array( 'hostname' => $this->hostname )) . $request;
        $type = $this->get_path_authentication_type($path);
        if ($type === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($type === 'private') {
            $this->check_required_credentials();
            $timestamp = $this->iso8601($this->milliseconds());
            $headers = array(
                'OK-ACCESS-KEY' => $this->apiKey,
                'OK-ACCESS-PASSPHRASE' => $this->password,
                'OK-ACCESS-TIMESTAMP' => $timestamp,
                // 'OK-FROM' => '',
                // 'OK-TO' => '',
                // 'OK-LIMIT' => '',
            );
            $auth = $timestamp . $method . $request;
            if ($method === 'GET') {
                if ($query) {
                    $urlencodedQuery = '?' . $this->urlencode($query);
                    $url .= $urlencodedQuery;
                    $auth .= $urlencodedQuery;
                }
            } else {
                if ($isArray || $query) {
                    $body = $this->json($query);
                    $auth .= $body;
                }
                $headers['Content-Type'] = 'application/json';
            }
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha256', 'base64');
            $headers['OK-ACCESS-SIGN'] = $signature;
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function get_path_authentication_type($path) {
        // https://github.com/ccxt/ccxt/issues/6651
        // a special case to handle the optionGetUnderlying interefering with
        // other endpoints containing this keyword
        if ($path === 'underlying') {
            return 'public';
        }
        $auth = $this->safe_value($this->options, 'auth', array());
        $key = $this->find_broadly_matched_key($auth, $path);
        return $this->safe_string($auth, $key, 'private');
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (!$response) {
            return; // fallback to default error handler
        }
        $feedback = $this->id . ' ' . $body;
        if ($code === 503) {
            // array("$message":"name resolution failed")
            throw new ExchangeNotAvailable($feedback);
        }
        //
        //     array("error_message":"Order does not exist","result":"true","error_code":"35029","order_id":"-1")
        //
        $message = $this->safe_string($response, 'message');
        $errorCode = $this->safe_string_2($response, 'code', 'error_code');
        $nonEmptyMessage = (($message !== null) && ($message !== ''));
        $nonZeroErrorCode = ($errorCode !== null) && ($errorCode !== '0');
        if ($nonEmptyMessage) {
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
        }
        if ($nonZeroErrorCode) {
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
        }
        if ($nonZeroErrorCode || $nonEmptyMessage) {
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

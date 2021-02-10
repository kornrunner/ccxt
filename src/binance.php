<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;
use \ccxt\NotSupported;
use \ccxt\DDoSProtection;

class binance extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'binance',
            'name' => 'Binance',
            'countries' => array( 'JP', 'MT' ), // Japan, Malta
            'rateLimit' => 500,
            'certified' => true,
            'pro' => true,
            // new metainfo interface
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchBidsAsks' => true,
                'fetchClosedOrders' => 'emulated',
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchFundingFees' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrders' => true,
                'fetchOrderBook' => true,
                'fetchStatus' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchTradingFee' => true,
                'fetchTradingFees' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '3m' => '3m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '2h' => '2h',
                '4h' => '4h',
                '6h' => '6h',
                '8h' => '8h',
                '12h' => '12h',
                '1d' => '1d',
                '3d' => '3d',
                '1w' => '1w',
                '1M' => '1M',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/29604020-d5483cdc-87ee-11e7-94c7-d1a8d9169293.jpg',
                'test' => array(
                    'dapiPublic' => 'https://testnet.binancefuture.com/dapi/v1',
                    'dapiPrivate' => 'https://testnet.binancefuture.com/dapi/v1',
                    'fapiPublic' => 'https://testnet.binancefuture.com/fapi/v1',
                    'fapiPrivate' => 'https://testnet.binancefuture.com/fapi/v1',
                    'fapiPrivateV2' => 'https://testnet.binancefuture.com/fapi/v2',
                    'public' => 'https://testnet.binance.vision/api/v3',
                    'private' => 'https://testnet.binance.vision/api/v3',
                    'v3' => 'https://testnet.binance.vision/api/v3',
                    'v1' => 'https://testnet.binance.vision/api/v1',
                ),
                'api' => array(
                    'wapi' => 'https://api.binance.com/wapi/v3',
                    'sapi' => 'https://api.binance.com/sapi/v1',
                    'dapiPublic' => 'https://dapi.binance.com/dapi/v1',
                    'dapiPrivate' => 'https://dapi.binance.com/dapi/v1',
                    'dapiData' => 'https://dapi.binance.com/futures/data',
                    'fapiPublic' => 'https://fapi.binance.com/fapi/v1',
                    'fapiPrivate' => 'https://fapi.binance.com/fapi/v1',
                    'fapiData' => 'https://fapi.binance.com/futures/data',
                    'fapiPrivateV2' => 'https://fapi.binance.com/fapi/v2',
                    'public' => 'https://api.binance.com/api/v3',
                    'private' => 'https://api.binance.com/api/v3',
                    'v3' => 'https://api.binance.com/api/v3',
                    'v1' => 'https://api.binance.com/api/v1',
                ),
                'www' => 'https://www.binance.com',
                'referral' => 'https://www.binance.com/?ref=10205187',
                'doc' => array(
                    'https://binance-docs.github.io/apidocs/spot/en',
                ),
                'api_management' => 'https://www.binance.com/en/usercenter/settings/api-management',
                'fees' => 'https://www.binance.com/en/fee/schedule',
            ),
            'api' => array(
                // the API structure below will need 3-layer apidefs
                'sapi' => array(
                    'get' => array(
                        'accountSnapshot',
                        // these endpoints require $this->apiKey
                        'margin/asset',
                        'margin/pair',
                        'margin/allAssets',
                        'margin/allPairs',
                        'margin/priceIndex',
                        // these endpoints require $this->apiKey . $this->secret
                        'asset/assetDividend',
                        'asset/transfer',
                        'margin/loan',
                        'margin/repay',
                        'margin/account',
                        'margin/transfer',
                        'margin/interestHistory',
                        'margin/forceLiquidationRec',
                        'margin/order',
                        'margin/openOrders',
                        'margin/allOrders',
                        'margin/myTrades',
                        'margin/maxBorrowable',
                        'margin/maxTransferable',
                        'margin/isolated/transfer',
                        'margin/isolated/account',
                        'margin/isolated/pair',
                        'margin/isolated/allPairs',
                        'futures/transfer',
                        'futures/loan/borrow/history',
                        'futures/loan/repay/history',
                        'futures/loan/wallet',
                        'futures/loan/configs',
                        'futures/loan/calcAdjustLevel',
                        'futures/loan/calcMaxAdjustAmount',
                        'futures/loan/adjustCollateral/history',
                        'futures/loan/liquidationHistory',
                        // https://binance-docs.github.io/apidocs/spot/en/#withdraw-sapi
                        'capital/config/getall', // get networks for withdrawing USDT ERC20 vs USDT Omni
                        'capital/deposit/address',
                        'capital/deposit/hisrec',
                        'capital/deposit/subAddress',
                        'capital/deposit/subHisrec',
                        'capital/withdraw/history',
                        'sub-account/futures/account',
                        'sub-account/futures/accountSummary',
                        'sub-account/futures/positionRisk',
                        'sub-account/futures/internalTransfer',
                        'sub-account/margin/account',
                        'sub-account/margin/accountSummary',
                        'sub-account/spotSummary',
                        'sub-account/status',
                        'sub-account/transfer/subUserHistory',
                        'sub-account/universalTransfer',
                        // lending endpoints
                        'lending/daily/product/list',
                        'lending/daily/userLeftQuota',
                        'lending/daily/userRedemptionQuota',
                        'lending/daily/token/position',
                        'lending/union/account',
                        'lending/union/purchaseRecord',
                        'lending/union/redemptionRecord',
                        'lending/union/interestHistory',
                        'lending/project/list',
                        'lending/project/position/list',
                        // mining endpoints
                        'mining/pub/algoList',
                        'mining/pub/coinList',
                        'mining/worker/detail',
                        'mining/worker/list',
                        'mining/payment/list',
                        'mining/statistics/user/status',
                        'mining/statistics/user/list',
                        // liquid swap endpoints
                        'bswap/pools',
                        'bswap/liquidity',
                        'bswap/liquidityOps',
                        'bswap/quote',
                        'bswap/swap',
                        // leveraged token endpoints
                        'blvt/tokenInfo',
                        'blvt/subscribe/record',
                        'blvt/redeem/record',
                        'blvt/userLimit',
                        // broker api
                        'apiReferral/ifNewUser',
                        'apiReferral/customization',
                        'apiReferral/userCustomization',
                        'apiReferral/rebate/recentRecord',
                        'apiReferral/rebate/historicalRecord',
                        'apiReferral/kickback/recentRecord',
                        'apiReferral/kickback/historicalRecord',
                    ),
                    'post' => array(
                        'asset/dust',
                        'asset/transfer',
                        'account/disableFastWithdrawSwitch',
                        'account/enableFastWithdrawSwitch',
                        'capital/withdraw/apply',
                        'margin/transfer',
                        'margin/loan',
                        'margin/repay',
                        'margin/order',
                        'margin/isolated/create',
                        'margin/isolated/transfer',
                        'sub-account/margin/transfer',
                        'sub-account/margin/enable',
                        'sub-account/margin/enable',
                        'sub-account/futures/enable',
                        'sub-account/futures/transfer',
                        'sub-account/futures/internalTransfer',
                        'sub-account/transfer/subToSub',
                        'sub-account/transfer/subToMaster',
                        'sub-account/universalTransfer',
                        'userDataStream',
                        'userDataStream/isolated',
                        'futures/transfer',
                        'futures/loan/borrow',
                        'futures/loan/repay',
                        'futures/loan/adjustCollateral',
                        // lending
                        'lending/customizedFixed/purchase',
                        'lending/daily/purchase',
                        'lending/daily/redeem',
                        // liquid swap endpoints
                        'bswap/liquidityAdd',
                        'bswap/liquidityRemove',
                        'bswap/swap',
                        // leveraged token endpoints
                        'blvt/subscribe',
                        'blvt/redeem',
                        // broker api
                        'apiReferral/customization',
                        'apiReferral/userCustomization',
                        'apiReferral/rebate/historicalRecord',
                        'apiReferral/kickback/historicalRecord',
                    ),
                    'put' => array(
                        'userDataStream',
                        'userDataStream/isolated',
                    ),
                    'delete' => array(
                        'margin/openOrders',
                        'margin/order',
                        'userDataStream',
                        'userDataStream/isolated',
                    ),
                ),
                'wapi' => array(
                    'post' => array(
                        'withdraw',
                        'sub-account/transfer',
                    ),
                    'get' => array(
                        'depositHistory',
                        'withdrawHistory',
                        'depositAddress',
                        'accountStatus',
                        'systemStatus',
                        'apiTradingStatus',
                        'userAssetDribbletLog',
                        'tradeFee',
                        'assetDetail',
                        'sub-account/list',
                        'sub-account/transfer/history',
                        'sub-account/assets',
                    ),
                ),
                'dapiPublic' => array(
                    'get' => array(
                        'ping',
                        'time',
                        'exchangeInfo',
                        'depth',
                        'trades',
                        'historicalTrades',
                        'aggTrades',
                        'premiumIndex',
                        'fundingRate',
                        'klines',
                        'continuousKlines',
                        'indexPriceKlines',
                        'markPriceKlines',
                        'ticker/24hr',
                        'ticker/price',
                        'ticker/bookTicker',
                        'allForceOrders',
                        'openInterest',
                    ),
                ),
                'dapiData' => array(
                    'get' => array(
                        'openInterestHist',
                        'topLongShortAccountRatio',
                        'topLongShortPositionRatio',
                        'globalLongShortAccountRatio',
                        'takerBuySellVol',
                        'basis',
                    ),
                ),
                'dapiPrivate' => array(
                    'get' => array(
                        'positionSide/dual',
                        'order',
                        'openOrder',
                        'openOrders',
                        'allOrders',
                        'balance',
                        'account',
                        'positionMargin/history',
                        'positionRisk',
                        'userTrades',
                        'income',
                        'leverageBracket',
                        'forceOrders',
                        'adlQuantile',
                    ),
                    'post' => array(
                        'positionSide/dual',
                        'order',
                        'batchOrders',
                        'countdownCancelAll',
                        'leverage',
                        'marginType',
                        'positionMargin',
                        'listenKey',
                    ),
                    'put' => array(
                        'listenKey',
                    ),
                    'delete' => array(
                        'order',
                        'allOpenOrders',
                        'batchOrders',
                        'listenKey',
                    ),
                ),
                'fapiPublic' => array(
                    'get' => array(
                        'ping',
                        'time',
                        'exchangeInfo',
                        'depth',
                        'trades',
                        'historicalTrades',
                        'aggTrades',
                        'klines',
                        'fundingRate',
                        'premiumIndex',
                        'ticker/24hr',
                        'ticker/price',
                        'ticker/bookTicker',
                        'allForceOrders',
                        'openInterest',
                        'indexInfo',
                    ),
                ),
                'fapiData' => array(
                    'get' => array(
                        'openInterestHist',
                        'topLongShortAccountRatio',
                        'topLongShortPositionRatio',
                        'globalLongShortAccountRatio',
                        'takerlongshortRatio',
                    ),
                ),
                'fapiPrivate' => array(
                    'get' => array(
                        'allForceOrders',
                        'allOrders',
                        'openOrder',
                        'openOrders',
                        'order',
                        'account',
                        'balance',
                        'leverageBracket',
                        'positionMargin/history',
                        'positionRisk',
                        'positionSide/dual',
                        'userTrades',
                        'income',
                        // broker endpoints
                        'apiReferral/ifNewUser',
                        'apiReferral/customization',
                        'apiReferral/userCustomization',
                        'apiReferral/traderNum',
                        'apiReferral/overview',
                        'apiReferral/tradeVol',
                        'apiReferral/rebateVol',
                        'apiReferral/traderSummary',
                    ),
                    'post' => array(
                        'batchOrders',
                        'positionSide/dual',
                        'positionMargin',
                        'marginType',
                        'order',
                        'leverage',
                        'listenKey',
                        'countdownCancelAll',
                        // broker endpoints
                        'apiReferral/customization',
                        'apiReferral/userCustomization',
                    ),
                    'put' => array(
                        'listenKey',
                    ),
                    'delete' => array(
                        'batchOrders',
                        'order',
                        'allOpenOrders',
                        'listenKey',
                    ),
                ),
                'fapiPrivateV2' => array(
                    'get' => array(
                        'account',
                        'balance',
                        'positionRisk',
                    ),
                ),
                'v3' => array(
                    'get' => array(
                        'ticker/price',
                        'ticker/bookTicker',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'ping',
                        'time',
                        'depth',
                        'trades',
                        'aggTrades',
                        'historicalTrades',
                        'klines',
                        'ticker/24hr',
                        'ticker/price',
                        'ticker/bookTicker',
                        'exchangeInfo',
                    ),
                    'put' => array( 'userDataStream' ),
                    'post' => array( 'userDataStream' ),
                    'delete' => array( 'userDataStream' ),
                ),
                'private' => array(
                    'get' => array(
                        'allOrderList', // oco
                        'openOrderList', // oco
                        'orderList', // oco
                        'order',
                        'openOrders',
                        'allOrders',
                        'account',
                        'myTrades',
                    ),
                    'post' => array(
                        'order/oco',
                        'order',
                        'order/test',
                    ),
                    'delete' => array(
                        'openOrders', // added on 2020-04-25 for canceling all open orders per symbol
                        'orderList', // oco
                        'order',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.001,
                    'maker' => 0.001,
                ),
            ),
            'commonCurrencies' => array(
                'BCC' => 'BCC', // kept for backward-compatibility https://github.com/ccxt/ccxt/issues/4848
                'YOYO' => 'YOYOW',
            ),
            // exchange-specific options
            'options' => array(
                // 'fetchTradesMethod' => 'publicGetAggTrades', // publicGetTrades, publicGetHistoricalTrades
                'defaultTimeInForce' => 'GTC', // 'GTC' = Good To Cancel (default), 'IOC' = Immediate Or Cancel
                'defaultType' => 'spot', // 'spot', 'future', 'margin', 'delivery'
                'hasAlreadyAuthenticatedSuccessfully' => false,
                'warnOnFetchOpenOrdersWithoutSymbol' => true,
                'recvWindow' => 5 * 1000, // 5 sec, binance default
                'timeDifference' => 0, // the difference between system clock and Binance clock
                'adjustForTimeDifference' => false, // controls the adjustment logic upon instantiation
                'parseOrderToPrecision' => false, // force amounts and costs in parseOrder to precision
                'newOrderRespType' => array(
                    'market' => 'FULL', // 'ACK' for order id, 'RESULT' for full order or 'FULL' for order with fills
                    'limit' => 'RESULT', // we change it from 'ACK' by default to 'RESULT'
                ),
                'quoteOrderQty' => true, // whether market orders support amounts in quote currency
                'broker' => array(
                    'spot' => 'x-R4BD3S82',
                    'margin' => 'x-R4BD3S82',
                    'future' => 'x-xcKtGhcu',
                    'delivery' => 'x-xcKtGhcu',
                ),
            ),
            // https://binance-docs.github.io/apidocs/spot/en/#error-codes-2
            'exceptions' => array(
                'System abnormality' => '\\ccxt\\ExchangeError', // array("code":-1000,"msg":"System abnormality")
                'You are not authorized to execute this request.' => '\\ccxt\\PermissionDenied', // array("msg":"You are not authorized to execute this request.")
                'API key does not exist' => '\\ccxt\\AuthenticationError',
                'Order would trigger immediately.' => '\\ccxt\\OrderImmediatelyFillable',
                'Stop price would trigger immediately.' => '\\ccxt\\OrderImmediatelyFillable', // array("code":-2010,"msg":"Stop price would trigger immediately.")
                'Order would immediately match and take.' => '\\ccxt\\OrderImmediatelyFillable', // array("code":-2010,"msg":"Order would immediately match and take.")
                'Account has insufficient balance for requested action.' => '\\ccxt\\InsufficientFunds',
                'Rest API trading is not enabled.' => '\\ccxt\\ExchangeNotAvailable',
                "You don't have permission." => '\\ccxt\\PermissionDenied', // array("msg":"You don't have permission.","success":false)
                'Market is closed.' => '\\ccxt\\ExchangeNotAvailable', // array("code":-1013,"msg":"Market is closed.")
                'Too many requests.' => '\\ccxt\\DDoSProtection', // array("msg":"Too many requests. Please try again later.","success":false)
                '-1000' => '\\ccxt\\ExchangeNotAvailable', // array("code":-1000,"msg":"An unknown error occured while processing the request.")
                '-1001' => '\\ccxt\\ExchangeNotAvailable', // 'Internal error; unable to process your request. Please try again.'
                '-1002' => '\\ccxt\\AuthenticationError', // 'You are not authorized to execute this request.'
                '-1003' => '\\ccxt\\RateLimitExceeded', // array("code":-1003,"msg":"Too much request weight used, current limit is 1200 request weight per 1 MINUTE. Please use the websocket for live updates to avoid polling the API.")
                '-1013' => '\\ccxt\\InvalidOrder', // createOrder -> 'invalid quantity'/'invalid price'/MIN_NOTIONAL
                '-1015' => '\\ccxt\\RateLimitExceeded', // 'Too many new orders; current limit is %s orders per %s.'
                '-1016' => '\\ccxt\\ExchangeNotAvailable', // 'This service is no longer available.',
                '-1020' => '\\ccxt\\BadRequest', // 'This operation is not supported.'
                '-1021' => '\\ccxt\\InvalidNonce', // 'your time is ahead of server'
                '-1022' => '\\ccxt\\AuthenticationError', // array("code":-1022,"msg":"Signature for this request is not valid.")
                '-1100' => '\\ccxt\\BadRequest', // createOrder(symbol, 1, asdf) -> 'Illegal characters found in parameter 'price'
                '-1101' => '\\ccxt\\BadRequest', // Too many parameters; expected %s and received %s.
                '-1102' => '\\ccxt\\BadRequest', // Param %s or %s must be sent, but both were empty
                '-1103' => '\\ccxt\\BadRequest', // An unknown parameter was sent.
                '-1104' => '\\ccxt\\BadRequest', // Not all sent parameters were read, read 8 parameters but was sent 9
                '-1105' => '\\ccxt\\BadRequest', // Parameter %s was empty.
                '-1106' => '\\ccxt\\BadRequest', // Parameter %s sent when not required.
                '-1111' => '\\ccxt\\BadRequest', // Precision is over the maximum defined for this asset.
                '-1112' => '\\ccxt\\InvalidOrder', // No orders on book for symbol.
                '-1114' => '\\ccxt\\BadRequest', // TimeInForce parameter sent when not required.
                '-1115' => '\\ccxt\\BadRequest', // Invalid timeInForce.
                '-1116' => '\\ccxt\\BadRequest', // Invalid orderType.
                '-1117' => '\\ccxt\\BadRequest', // Invalid side.
                '-1118' => '\\ccxt\\BadRequest', // New client order ID was empty.
                '-1119' => '\\ccxt\\BadRequest', // Original client order ID was empty.
                '-1120' => '\\ccxt\\BadRequest', // Invalid interval.
                '-1121' => '\\ccxt\\BadSymbol', // Invalid symbol.
                '-1125' => '\\ccxt\\AuthenticationError', // This listenKey does not exist.
                '-1127' => '\\ccxt\\BadRequest', // More than %s hours between startTime and endTime.
                '-1128' => '\\ccxt\\BadRequest', // array("code":-1128,"msg":"Combination of optional parameters invalid.")
                '-1130' => '\\ccxt\\BadRequest', // Data sent for paramter %s is not valid.
                '-1131' => '\\ccxt\\BadRequest', // recvWindow must be less than 60000
                '-2010' => '\\ccxt\\ExchangeError', // generic error code for createOrder -> 'Account has insufficient balance for requested action.', array("code":-2010,"msg":"Rest API trading is not enabled."), etc...
                '-2011' => '\\ccxt\\OrderNotFound', // cancelOrder(1, 'BTC/USDT') -> 'UNKNOWN_ORDER'
                '-2013' => '\\ccxt\\OrderNotFound', // fetchOrder (1, 'BTC/USDT') -> 'Order does not exist'
                '-2014' => '\\ccxt\\AuthenticationError', // array( "code":-2014, "msg" => "API-key format invalid." )
                '-2015' => '\\ccxt\\AuthenticationError', // "Invalid API-key, IP, or permissions for action."
                '-2019' => '\\ccxt\\InsufficientFunds', // array("code":-2019,"msg":"Margin is insufficient.")
                '-3005' => '\\ccxt\\InsufficientFunds', // array("code":-3005,"msg":"Transferring out not allowed. Transfer out amount exceeds max amount.")
                '-3008' => '\\ccxt\\InsufficientFunds', // array("code":-3008,"msg":"Borrow not allowed. Your borrow amount has exceed maximum borrow amount.")
                '-3010' => '\\ccxt\\ExchangeError', // array("code":-3010,"msg":"Repay not allowed. Repay amount exceeds borrow amount.")
                '-3022' => '\\ccxt\\AccountSuspended', // You account's trading is banned.
                '-4028' => '\\ccxt\\BadRequest', // array("code":-4028,"msg":"Leverage 100 is not valid")
            ),
        ));
    }

    public function nonce() {
        return $this->milliseconds() - $this->options['timeDifference'];
    }

    public function fetch_time($params = array ()) {
        $type = $this->safe_string_2($this->options, 'fetchTime', 'defaultType', 'spot');
        $method = 'publicGetTime';
        if ($type === 'future') {
            $method = 'fapiPublicGetTime';
        } else if ($type === 'delivery') {
            $method = 'dapiPublicGetTime';
        }
        $response = $this->$method ($params);
        return $this->safe_integer($response, 'serverTime');
    }

    public function load_time_difference($params = array ()) {
        $serverTime = $this->fetch_time($params);
        $after = $this->milliseconds();
        $this->options['timeDifference'] = $after - $serverTime;
        return $this->options['timeDifference'];
    }

    public function fetch_markets($params = array ()) {
        $defaultType = $this->safe_string_2($this->options, 'fetchMarkets', 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        if (($type !== 'spot') && ($type !== 'future') && ($type !== 'margin') && ($type !== 'delivery')) {
            throw new ExchangeError($this->id . " does not support '" . $type . "' $type, set exchange.options['defaultType'] to 'spot', 'margin', 'delivery' or 'future'"); // eslint-disable-line quotes
        }
        $method = 'publicGetExchangeInfo';
        if ($type === 'future') {
            $method = 'fapiPublicGetExchangeInfo';
        } else if ($type === 'delivery') {
            $method = 'dapiPublicGetExchangeInfo';
        }
        $response = $this->$method ($query);
        //
        // $spot / $margin
        //
        //     {
        //         "timezone":"UTC",
        //         "serverTime":1575416692969,
        //         "rateLimits":array(
        //             array("rateLimitType":"REQUEST_WEIGHT","interval":"MINUTE","intervalNum":1,"limit":1200),
        //             array("rateLimitType":"ORDERS","interval":"SECOND","intervalNum":10,"limit":100),
        //             array("rateLimitType":"ORDERS","interval":"DAY","intervalNum":1,"limit":200000)
        //         ),
        //         "exchangeFilters":array(),
        //         "symbols":[
        //             array(
        //                 "$symbol":"ETHBTC",
        //                 "$status":"TRADING",
        //                 "baseAsset":"ETH",
        //                 "baseAssetPrecision":8,
        //                 "quoteAsset":"BTC",
        //                 "quotePrecision":8,
        //                 "baseCommissionPrecision":8,
        //                 "quoteCommissionPrecision":8,
        //                 "orderTypes":["LIMIT","LIMIT_MAKER","MARKET","STOP_LOSS_LIMIT","TAKE_PROFIT_LIMIT"],
        //                 "icebergAllowed":true,
        //                 "ocoAllowed":true,
        //                 "quoteOrderQtyMarketAllowed":true,
        //                 "isSpotTradingAllowed":true,
        //                 "isMarginTradingAllowed":true,
        //                 "$filters":array(
        //                     array("filterType":"PRICE_FILTER","minPrice":"0.00000100","$maxPrice":"100000.00000000","tickSize":"0.00000100"),
        //                     array("filterType":"PERCENT_PRICE","multiplierUp":"5","multiplierDown":"0.2","avgPriceMins":5),
        //                     array("filterType":"LOT_SIZE","minQty":"0.00100000","maxQty":"100000.00000000","$stepSize":"0.00100000"),
        //                     array("filterType":"MIN_NOTIONAL","minNotional":"0.00010000","applyToMarket":true,"avgPriceMins":5),
        //                     array("filterType":"ICEBERG_PARTS","limit":10),
        //                     array("filterType":"MARKET_LOT_SIZE","minQty":"0.00000000","maxQty":"63100.00000000","$stepSize":"0.00000000"),
        //                     array("filterType":"MAX_NUM_ALGO_ORDERS","maxNumAlgoOrders":5)
        //                 )
        //             ),
        //         ],
        //     }
        //
        // futures/usdt-margined (fapi)
        //
        //     {
        //         "timezone":"UTC",
        //         "serverTime":1575417244353,
        //         "rateLimits":array(
        //             array("rateLimitType":"REQUEST_WEIGHT","interval":"MINUTE","intervalNum":1,"limit":1200),
        //             array("rateLimitType":"ORDERS","interval":"MINUTE","intervalNum":1,"limit":1200)
        //         ),
        //         "exchangeFilters":array(),
        //         "symbols":array(
        //             {
        //                 "$symbol":"BTCUSDT",
        //                 "$status":"TRADING",
        //                 "maintMarginPercent":"2.5000",
        //                 "requiredMarginPercent":"5.0000",
        //                 "baseAsset":"BTC",
        //                 "quoteAsset":"USDT",
        //                 "pricePrecision":2,
        //                 "quantityPrecision":3,
        //                 "baseAssetPrecision":8,
        //                 "quotePrecision":8,
        //                 "$filters":[
        //                     array("minPrice":"0.01","$maxPrice":"100000","filterType":"PRICE_FILTER","tickSize":"0.01"),
        //                     array("$stepSize":"0.001","filterType":"LOT_SIZE","maxQty":"1000","minQty":"0.001"),
        //                     array("$stepSize":"0.001","filterType":"MARKET_LOT_SIZE","maxQty":"1000","minQty":"0.001"),
        //                     array("limit":200,"filterType":"MAX_NUM_ORDERS"),
        //                     array("multiplierDown":"0.8500","multiplierUp":"1.1500","multiplierDecimal":"4","filterType":"PERCENT_PRICE")
        //                 ),
        //                 "orderTypes":["LIMIT","MARKET","STOP"],
        //                 "timeInForce":["GTC","IOC","FOK","GTX"]
        //             }
        //         ]
        //     }
        //
        // delivery/coin-margined (dapi)
        //
        //     {
        //         "timezone" => "UTC",
        //         "serverTime" => 1597667052958,
        //         "rateLimits" => array(
        //             array("rateLimitType":"REQUEST_WEIGHT","interval":"MINUTE","intervalNum":1,"limit":6000),
        //             array("rateLimitType":"ORDERS","interval":"MINUTE","intervalNum":1,"limit":6000)
        //         ),
        //         "exchangeFilters" => array(),
        //         "symbols" => array(
        //             array(
        //                 "$symbol" => "BTCUSD_200925",
        //                 "pair" => "BTCUSD",
        //                 "$contractType" => "CURRENT_QUARTER",
        //                 "deliveryDate" => 1601020800000,
        //                 "onboardDate" => 1590739200000,
        //                 "contractStatus" => "TRADING",
        //                 "contractSize" => 100,
        //                 "marginAsset" => "BTC",
        //                 "maintMarginPercent" => "2.5000",
        //                 "requiredMarginPercent" => "5.0000",
        //                 "baseAsset" => "BTC",
        //                 "quoteAsset" => "USD",
        //                 "pricePrecision" => 1,
        //                 "quantityPrecision" => 0,
        //                 "baseAssetPrecision" => 8,
        //                 "quotePrecision" => 8,
        //                 "equalQtyPrecision" => 4,
        //                 "$filters" => [
        //                     array("minPrice":"0.1","$maxPrice":"100000","filterType":"PRICE_FILTER","tickSize":"0.1"),
        //                     array("$stepSize":"1","filterType":"LOT_SIZE","maxQty":"100000","minQty":"1"),
        //                     array("$stepSize":"0","filterType":"MARKET_LOT_SIZE","maxQty":"100000","minQty":"1"),
        //                     array("limit":200,"filterType":"MAX_NUM_ORDERS"),
        //                     array("multiplierDown":"0.9500","multiplierUp":"1.0500","multiplierDecimal":"4","filterType":"PERCENT_PRICE")
        //                 ),
        //                 "orderTypes" => ["LIMIT","MARKET","STOP","STOP_MARKET","TAKE_PROFIT","TAKE_PROFIT_MARKET","TRAILING_STOP_MARKET"],
        //                 "timeInForce" => ["GTC","IOC","FOK","GTX"]
        //             ),
        //             {
        //                 "$symbol" => "BTCUSD_PERP",
        //                 "pair" => "BTCUSD",
        //                 "$contractType" => "PERPETUAL",
        //                 "deliveryDate" => 4133404800000,
        //                 "onboardDate" => 1596006000000,
        //                 "contractStatus" => "TRADING",
        //                 "contractSize" => 100,
        //                 "marginAsset" => "BTC",
        //                 "maintMarginPercent" => "2.5000",
        //                 "requiredMarginPercent" => "5.0000",
        //                 "baseAsset" => "BTC",
        //                 "quoteAsset" => "USD",
        //                 "pricePrecision" => 1,
        //                 "quantityPrecision" => 0,
        //                 "baseAssetPrecision" => 8,
        //                 "quotePrecision" => 8,
        //                 "equalQtyPrecision" => 4,
        //                 "$filters" => array(
        //                     array("minPrice":"0.1","$maxPrice":"100000","filterType":"PRICE_FILTER","tickSize":"0.1"),
        //                     array("$stepSize":"1","filterType":"LOT_SIZE","maxQty":"100000","minQty":"1"),
        //                     array("$stepSize":"1","filterType":"MARKET_LOT_SIZE","maxQty":"100000","minQty":"1"),
        //                     array("limit":200,"filterType":"MAX_NUM_ORDERS"),
        //                     array("multiplierDown":"0.8500","multiplierUp":"1.1500","multiplierDecimal":"4","filterType":"PERCENT_PRICE")
        //                 ),
        //                 "orderTypes" => ["LIMIT","MARKET","STOP","STOP_MARKET","TAKE_PROFIT","TAKE_PROFIT_MARKET","TRAILING_STOP_MARKET"],
        //                 "timeInForce" => ["GTC","IOC","FOK","GTX"]
        //             }
        //         ]
        //     }
        //
        if ($this->options['adjustForTimeDifference']) {
            $this->load_time_difference();
        }
        $markets = $this->safe_value($response, 'symbols');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $spot = ($type === 'spot');
            $future = ($type === 'future');
            $delivery = ($type === 'delivery');
            $id = $this->safe_string($market, 'symbol');
            $lowercaseId = $this->safe_string_lower($market, 'symbol');
            $baseId = $this->safe_string($market, 'baseAsset');
            $quoteId = $this->safe_string($market, 'quoteAsset');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $contractType = $this->safe_string($market, 'contractType');
            $idSymbol = ($future || $delivery) && ($contractType !== 'PERPETUAL');
            $symbol = $idSymbol ? $id : ($base . '/' . $quote);
            $filters = $this->safe_value($market, 'filters', array());
            $filtersByType = $this->index_by($filters, 'filterType');
            $precision = array(
                'base' => $this->safe_integer($market, 'baseAssetPrecision'),
                'quote' => $this->safe_integer($market, 'quotePrecision'),
                'amount' => $this->safe_integer($market, 'baseAssetPrecision'),
                'price' => $this->safe_integer($market, 'quotePrecision'),
            );
            $status = $this->safe_string_2($market, 'status', 'contractStatus');
            $active = ($status === 'TRADING');
            $margin = $this->safe_value($market, 'isMarginTradingAllowed', $future || $delivery);
            $entry = array(
                'id' => $id,
                'lowercaseId' => $lowercaseId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'type' => $type,
                'spot' => $spot,
                'margin' => $margin,
                'future' => $future,
                'delivery' => $delivery,
                'active' => $active,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
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
                ),
            );
            if (is_array($filtersByType) && array_key_exists('PRICE_FILTER', $filtersByType)) {
                $filter = $this->safe_value($filtersByType, 'PRICE_FILTER', array());
                // PRICE_FILTER reports zero values for $maxPrice
                // since they updated $filter types in November 2018
                // https://github.com/ccxt/ccxt/issues/4286
                // therefore limits['price']['max'] doesn't have any meaningful value except null
                $entry['limits']['price'] = array(
                    'min' => $this->safe_float($filter, 'minPrice'),
                    'max' => null,
                );
                $maxPrice = $this->safe_float($filter, 'maxPrice');
                if (($maxPrice !== null) && ($maxPrice > 0)) {
                    $entry['limits']['price']['max'] = $maxPrice;
                }
                $entry['precision']['price'] = $this->precision_from_string($filter['tickSize']);
            }
            if (is_array($filtersByType) && array_key_exists('LOT_SIZE', $filtersByType)) {
                $filter = $this->safe_value($filtersByType, 'LOT_SIZE', array());
                $stepSize = $this->safe_string($filter, 'stepSize');
                $entry['precision']['amount'] = $this->precision_from_string($stepSize);
                $entry['limits']['amount'] = array(
                    'min' => $this->safe_float($filter, 'minQty'),
                    'max' => $this->safe_float($filter, 'maxQty'),
                );
            }
            if (is_array($filtersByType) && array_key_exists('MARKET_LOT_SIZE', $filtersByType)) {
                $filter = $this->safe_value($filtersByType, 'MARKET_LOT_SIZE', array());
                $entry['limits']['market'] = array(
                    'min' => $this->safe_float($filter, 'minQty'),
                    'max' => $this->safe_float($filter, 'maxQty'),
                );
            }
            if (is_array($filtersByType) && array_key_exists('MIN_NOTIONAL', $filtersByType)) {
                $filter = $this->safe_value($filtersByType, 'MIN_NOTIONAL', array());
                $entry['limits']['cost']['min'] = $this->safe_float_2($filter, 'minNotional', 'notional');
            }
            $result[] = $entry;
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
        $defaultType = $this->safe_string_2($this->options, 'fetchBalance', 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $method = 'privateGetAccount';
        if ($type === 'future') {
            $options = $this->safe_value($this->options, 'future', array());
            $fetchBalanceOptions = $this->safe_value($options, 'fetchBalance', array());
            $method = $this->safe_string($fetchBalanceOptions, 'method', 'fapiPrivateV2GetAccount');
        } else if ($type === 'delivery') {
            $options = $this->safe_value($this->options, 'delivery', array());
            $fetchBalanceOptions = $this->safe_value($options, 'fetchBalance', array());
            $method = $this->safe_string($fetchBalanceOptions, 'method', 'dapiPrivateGetAccount');
        } else if ($type === 'margin') {
            $method = 'sapiGetMarginAccount';
        }
        $query = $this->omit($params, 'type');
        $response = $this->$method ($query);
        //
        // spot
        //
        //     {
        //         makerCommission => 10,
        //         takerCommission => 10,
        //         buyerCommission => 0,
        //         sellerCommission => 0,
        //         canTrade => true,
        //         canWithdraw => true,
        //         canDeposit => true,
        //         updateTime => 1575357359602,
        //         accountType => "MARGIN",
        //         $balances => array(
        //             array( asset => "BTC", free => "0.00219821", locked => "0.00000000"  ),
        //         )
        //     }
        //
        // margin
        //
        //     {
        //         "borrowEnabled":true,
        //         "marginLevel":"999.00000000",
        //         "totalAssetOfBtc":"0.00000000",
        //         "totalLiabilityOfBtc":"0.00000000",
        //         "totalNetAssetOfBtc":"0.00000000",
        //         "tradeEnabled":true,
        //         "transferEnabled":true,
        //         "userAssets":array(
        //             array("asset":"MATIC","borrowed":"0.00000000","free":"0.00000000","interest":"0.00000000","locked":"0.00000000","netAsset":"0.00000000"),
        //             array("asset":"VET","borrowed":"0.00000000","free":"0.00000000","interest":"0.00000000","locked":"0.00000000","netAsset":"0.00000000"),
        //             array("asset":"USDT","borrowed":"0.00000000","free":"0.00000000","interest":"0.00000000","locked":"0.00000000","netAsset":"0.00000000")
        //         ),
        //     }
        //
        // futures (fapi)
        //
        //     fapiPrivateGetAccount
        //
        //     {
        //         "feeTier":0,
        //         "canTrade":true,
        //         "canDeposit":true,
        //         "canWithdraw":true,
        //         "updateTime":0,
        //         "totalInitialMargin":"0.00000000",
        //         "totalMaintMargin":"0.00000000",
        //         "totalWalletBalance":"4.54000000",
        //         "totalUnrealizedProfit":"0.00000000",
        //         "totalMarginBalance":"4.54000000",
        //         "totalPositionInitialMargin":"0.00000000",
        //         "totalOpenOrderInitialMargin":"0.00000000",
        //         "maxWithdrawAmount":"4.54000000",
        //         "assets":array(
        //             {
        //                 "asset":"USDT",
        //                 "walletBalance":"4.54000000",
        //                 "unrealizedProfit":"0.00000000",
        //                 "marginBalance":"4.54000000",
        //                 "maintMargin":"0.00000000",
        //                 "initialMargin":"0.00000000",
        //                 "positionInitialMargin":"0.00000000",
        //                 "openOrderInitialMargin":"0.00000000",
        //                 "maxWithdrawAmount":"4.54000000"
        //             }
        //         ),
        //         "positions":array(
        //             {
        //                 "symbol":"BTCUSDT",
        //                 "initialMargin":"0.00000",
        //                 "maintMargin":"0.00000",
        //                 "unrealizedProfit":"0.00000000",
        //                 "positionInitialMargin":"0.00000",
        //                 "openOrderInitialMargin":"0.00000"
        //             }
        //         )
        //     }
        //
        //     fapiPrivateV2GetAccount
        //
        //     {
        //         "feeTier":0,
        //         "canTrade":true,
        //         "canDeposit":true,
        //         "canWithdraw":true,
        //         "updateTime":0,
        //         "totalInitialMargin":"0.00000000",
        //         "totalMaintMargin":"0.00000000",
        //         "totalWalletBalance":"0.00000000",
        //         "totalUnrealizedProfit":"0.00000000",
        //         "totalMarginBalance":"0.00000000",
        //         "totalPositionInitialMargin":"0.00000000",
        //         "totalOpenOrderInitialMargin":"0.00000000",
        //         "totalCrossWalletBalance":"0.00000000",
        //         "totalCrossUnPnl":"0.00000000",
        //         "availableBalance":"0.00000000",
        //         "maxWithdrawAmount":"0.00000000",
        //         "assets":array(
        //             {
        //                 "asset":"BNB",
        //                 "walletBalance":"0.01000000",
        //                 "unrealizedProfit":"0.00000000",
        //                 "marginBalance":"0.01000000",
        //                 "maintMargin":"0.00000000",
        //                 "initialMargin":"0.00000000",
        //                 "positionInitialMargin":"0.00000000",
        //                 "openOrderInitialMargin":"0.00000000",
        //                 "maxWithdrawAmount":"0.01000000",
        //                 "crossWalletBalance":"0.01000000",
        //                 "crossUnPnl":"0.00000000",
        //                 "availableBalance":"0.01000000"
        //             }
        //         ),
        //         "positions":array(
        //             array(
        //                 "symbol":"BTCUSDT",
        //                 "initialMargin":"0",
        //                 "maintMargin":"0",
        //                 "unrealizedProfit":"0.00000000",
        //                 "positionInitialMargin":"0",
        //                 "openOrderInitialMargin":"0",
        //                 "leverage":"20",
        //                 "isolated":false,
        //                 "entryPrice":"0.00000",
        //                 "maxNotional":"5000000",
        //                 "positionSide":"BOTH"
        //             ),
        //         )
        //     }
        //
        //     fapiPrivateV2GetBalance
        //
        //     array(
        //         {
        //             "accountAlias":"FzFzXquXXqoC",
        //             "asset":"BNB",
        //             "$balance":"0.01000000",
        //             "crossWalletBalance":"0.01000000",
        //             "crossUnPnl":"0.00000000",
        //             "availableBalance":"0.01000000",
        //             "maxWithdrawAmount":"0.01000000"
        //         }
        //     )
        //
        $result = array( 'info' => $response );
        if (($type === 'spot') || ($type === 'margin')) {
            $balances = $this->safe_value_2($response, 'balances', 'userAssets', array());
            for ($i = 0; $i < count($balances); $i++) {
                $balance = $balances[$i];
                $currencyId = $this->safe_string($balance, 'asset');
                $code = $this->safe_currency_code($currencyId);
                $account = $this->account();
                $account['free'] = $this->safe_float($balance, 'free');
                $account['used'] = $this->safe_float($balance, 'locked');
                $result[$code] = $account;
            }
        } else {
            $balances = $response;
            if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) != 0) {
                $balances = $this->safe_value($response, 'assets', array());
            }
            for ($i = 0; $i < count($balances); $i++) {
                $balance = $balances[$i];
                $currencyId = $this->safe_string($balance, 'asset');
                $code = $this->safe_currency_code($currencyId);
                $account = $this->account();
                $account['free'] = $this->safe_float($balance, 'availableBalance');
                $account['used'] = $this->safe_float($balance, 'initialMargin');
                $account['total'] = $this->safe_float_2($balance, 'marginBalance', 'balance');
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 100, max 5000, see https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#order-book
        }
        $method = 'publicGetDepth';
        if ($market['future']) {
            $method = 'fapiPublicGetDepth';
        } else if ($market['delivery']) {
            $method = 'dapiPublicGetDepth';
        }
        $response = $this->$method (array_merge($request, $params));
        $orderbook = $this->parse_order_book($response);
        $orderbook['nonce'] = $this->safe_integer($response, 'lastUpdateId');
        return $orderbook;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {
        //         $symbol => 'ETHBTC',
        //         priceChange => '0.00068700',
        //         priceChangePercent => '2.075',
        //         weightedAvgPrice => '0.03342681',
        //         prevClosePrice => '0.03310300',
        //         lastPrice => '0.03378900',
        //         lastQty => '0.07700000',
        //         bidPrice => '0.03378900',
        //         bidQty => '7.16800000',
        //         askPrice => '0.03379000',
        //         askQty => '24.00000000',
        //         openPrice => '0.03310200',
        //         highPrice => '0.03388900',
        //         lowPrice => '0.03306900',
        //         volume => '205478.41000000',
        //         quoteVolume => '6868.48826294',
        //         openTime => 1601469986932,
        //         closeTime => 1601556386932,
        //         firstId => 196098772,
        //         lastId => 196186315,
        //         count => 87544
        //     }
        //
        $timestamp = $this->safe_integer($ticker, 'closeTime');
        $marketId = $this->safe_string($ticker, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $last = $this->safe_float($ticker, 'lastPrice');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'highPrice'),
            'low' => $this->safe_float($ticker, 'lowPrice'),
            'bid' => $this->safe_float($ticker, 'bidPrice'),
            'bidVolume' => $this->safe_float($ticker, 'bidQty'),
            'ask' => $this->safe_float($ticker, 'askPrice'),
            'askVolume' => $this->safe_float($ticker, 'askQty'),
            'vwap' => $this->safe_float($ticker, 'weightedAvgPrice'),
            'open' => $this->safe_float($ticker, 'openPrice'),
            'close' => $last,
            'last' => $last,
            'previousClose' => $this->safe_float($ticker, 'prevClosePrice'), // previous day close
            'change' => $this->safe_float($ticker, 'priceChange'),
            'percentage' => $this->safe_float($ticker, 'priceChangePercent'),
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_status($params = array ()) {
        $response = $this->wapiGetSystemStatus ($params);
        $status = $this->safe_value($response, 'status');
        if ($status !== null) {
            $status = ($status === 0) ? 'ok' : 'maintenance';
            $this->status = array_merge($this->status, array(
                'status' => $status,
                'updated' => $this->milliseconds(),
            ));
        }
        return $this->status;
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $method = 'publicGetTicker24hr';
        if ($market['future']) {
            $method = 'fapiPublicGetTicker24hr';
        } else if ($market['delivery']) {
            $method = 'dapiPublicGetTicker24hr';
        }
        $response = $this->$method (array_merge($request, $params));
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) == 0) {
            $firstTicker = $this->safe_value($response, 0, array());
            return $this->parse_ticker($firstTicker, $market);
        }
        return $this->parse_ticker($response, $market);
    }

    public function parse_tickers($rawTickers, $symbols = null) {
        $tickers = array();
        for ($i = 0; $i < count($rawTickers); $i++) {
            $tickers[] = $this->parse_ticker($rawTickers[$i]);
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function fetch_bids_asks($symbols = null, $params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string_2($this->options, 'fetchBidsAsks', 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        $method = null;
        if ($type === 'future') {
            $method = 'fapiPublicGetTickerBookTicker';
        } else if ($type === 'delivery') {
            $method = 'dapiPublicGetTickerBookTicker';
        } else {
            $method = 'publicGetTickerBookTicker';
        }
        $response = $this->$method ($query);
        return $this->parse_tickers($response, $symbols);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string_2($this->options, 'fetchTickers', 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        $defaultMethod = null;
        if ($type === 'future') {
            $defaultMethod = 'fapiPublicGetTicker24hr';
        } else if ($type === 'delivery') {
            $defaultMethod = 'dapiPublicGetTicker24hr';
        } else {
            $defaultMethod = 'publicGetTicker24hr';
        }
        $method = $this->safe_string($this->options, 'fetchTickersMethod', $defaultMethod);
        $response = $this->$method ($query);
        return $this->parse_tickers($response, $symbols);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1591478520000,
        //         "0.02501300",
        //         "0.02501800",
        //         "0.02500000",
        //         "0.02500000",
        //         "22.19000000",
        //         1591478579999,
        //         "0.55490906",
        //         40,
        //         "10.92900000",
        //         "0.27336462",
        //         "0"
        //     )
        //
        return array(
            $this->safe_integer($ohlcv, 0),
            $this->safe_float($ohlcv, 1),
            $this->safe_float($ohlcv, 2),
            $this->safe_float($ohlcv, 3),
            $this->safe_float($ohlcv, 4),
            $this->safe_float($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        // binance docs say that the default $limit 500, max 1500 for futures, max 1000 for spot markets
        // the reality is that the time range wider than 500 candles won't work right
        $defaultLimit = 500;
        $maxLimit = 1500;
        $limit = ($limit === null) ? $defaultLimit : min ($limit, $maxLimit);
        $request = array(
            'symbol' => $market['id'],
            'interval' => $this->timeframes[$timeframe],
            'limit' => $limit,
        );
        $duration = $this->parse_timeframe($timeframe);
        if ($since !== null) {
            $request['startTime'] = $since;
            if ($since > 0) {
                $endTime = $this->sum($since, $limit * $duration * 1000 - 1);
                $now = $this->milliseconds();
                $request['endTime'] = min ($now, $endTime);
            }
        }
        $method = 'publicGetKlines';
        if ($market['future']) {
            $method = 'fapiPublicGetKlines';
        } else if ($market['delivery']) {
            $method = 'dapiPublicGetKlines';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     [
        //         [1591478520000,"0.02501300","0.02501800","0.02500000","0.02500000","22.19000000",1591478579999,"0.55490906",40,"10.92900000","0.27336462","0"],
        //         [1591478580000,"0.02499600","0.02500900","0.02499400","0.02500300","21.34700000",1591478639999,"0.53370468",24,"7.53800000","0.18850725","0"],
        //         [1591478640000,"0.02500800","0.02501100","0.02500300","0.02500800","154.14200000",1591478699999,"3.85405839",97,"5.32300000","0.13312641","0"],
        //     ]
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        if (is_array($trade) && array_key_exists('isDustTrade', $trade)) {
            return $this->parse_dust_trade($trade, $market);
        }
        //
        // aggregate trades
        // https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#compressedaggregate-trades-list
        //
        //     {
        //         "a" => 26129,         // Aggregate tradeId
        //         "p" => "0.01633102",  // Price
        //         "q" => "4.70443515",  // Quantity
        //         "f" => 27781,         // First tradeId
        //         "l" => 27781,         // Last tradeId
        //         "T" => 1498793709153, // Timestamp
        //         "m" => true,          // Was the buyer the maker?
        //         "M" => true           // Was the $trade the best $price match?
        //     }
        //
        // recent public trades and old public trades
        // https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#recent-trades-list
        // https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#old-$trade-lookup-market_data
        //
        //     {
        //         "$id" => 28457,
        //         "$price" => "4.00000100",
        //         "qty" => "12.00000000",
        //         "time" => 1499865549590,
        //         "isBuyerMaker" => true,
        //         "isBestMatch" => true
        //     }
        //
        // private trades
        // https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#account-$trade-list-user_data
        //
        //     {
        //         "$symbol" => "BNBBTC",
        //         "$id" => 28457,
        //         "$orderId" => 100234,
        //         "$price" => "4.00000100",
        //         "qty" => "12.00000000",
        //         "commission" => "10.10000000",
        //         "commissionAsset" => "BNB",
        //         "time" => 1499865549590,
        //         "isBuyer" => true,
        //         "isMaker" => false,
        //         "isBestMatch" => true
        //     }
        //
        // futures trades
        // https://binance-docs.github.io/apidocs/futures/en/#account-$trade-list-user_data
        //
        //     {
        //       "accountId" => 20,
        //       "buyer" => False,
        //       "commission" => "-0.07819010",
        //       "commissionAsset" => "USDT",
        //       "counterPartyId" => 653,
        //       "$id" => 698759,
        //       "maker" => False,
        //       "$orderId" => 25851813,
        //       "$price" => "7819.01",
        //       "qty" => "0.002",
        //       "quoteQty" => "0.01563",
        //       "realizedPnl" => "-0.91539999",
        //       "$side" => "SELL",
        //       "$symbol" => "BTCUSDT",
        //       "time" => 1569514978020
        //     }
        //
        $timestamp = $this->safe_integer_2($trade, 'T', 'time');
        $price = $this->safe_float_2($trade, 'p', 'price');
        $amount = $this->safe_float_2($trade, 'q', 'qty');
        $id = $this->safe_string_2($trade, 'a', 'id');
        $side = null;
        $orderId = $this->safe_string($trade, 'orderId');
        if (is_array($trade) && array_key_exists('m', $trade)) {
            $side = $trade['m'] ? 'sell' : 'buy'; // this is reversed intentionally
        } else if (is_array($trade) && array_key_exists('isBuyerMaker', $trade)) {
            $side = $trade['isBuyerMaker'] ? 'sell' : 'buy';
        } else if (is_array($trade) && array_key_exists('side', $trade)) {
            $side = $this->safe_string_lower($trade, 'side');
        } else {
            if (is_array($trade) && array_key_exists('isBuyer', $trade)) {
                $side = $trade['isBuyer'] ? 'buy' : 'sell'; // this is a true $side
            }
        }
        $fee = null;
        if (is_array($trade) && array_key_exists('commission', $trade)) {
            $fee = array(
                'cost' => $this->safe_float($trade, 'commission'),
                'currency' => $this->safe_currency_code($this->safe_string($trade, 'commissionAsset')),
            );
        }
        $takerOrMaker = null;
        if (is_array($trade) && array_key_exists('isMaker', $trade)) {
            $takerOrMaker = $trade['isMaker'] ? 'maker' : 'taker';
        }
        $marketId = $this->safe_string($trade, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $cost = null;
        if (($price !== null) && ($amount !== null)) {
            $cost = $price * $amount;
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $orderId,
            'type' => null,
            'side' => $side,
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
            'symbol' => $market['id'],
            // 'fromId' => 123,    // ID to get aggregate trades from INCLUSIVE.
            // 'startTime' => 456, // Timestamp in ms to get aggregate trades from INCLUSIVE.
            // 'endTime' => 789,   // Timestamp in ms to get aggregate trades until INCLUSIVE.
            // 'limit' => 500,     // default = 500, maximum = 1000
        );
        $defaultType = $this->safe_string_2($this->options, 'fetchTrades', 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        $defaultMethod = null;
        if ($type === 'future') {
            $defaultMethod = 'fapiPublicGetAggTrades';
        } else if ($type === 'delivery') {
            $defaultMethod = 'dapiPublicGetAggTrades';
        } else {
            $defaultMethod = 'publicGetAggTrades';
        }
        $method = $this->safe_string($this->options, 'fetchTradesMethod', $defaultMethod);
        if ($method === 'publicGetAggTrades') {
            if ($since !== null) {
                $request['startTime'] = $since;
                // https://github.com/ccxt/ccxt/issues/6400
                // https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#compressedaggregate-trades-list
                $request['endTime'] = $this->sum($since, 3600000);
            }
            if ($type === 'future') {
                $method = 'fapiPublicGetAggTrades';
            } else if ($type === 'delivery') {
                $method = 'dapiPublicGetAggTrades';
            }
        } else if ($method === 'publicGetHistoricalTrades') {
            if ($type === 'future') {
                $method = 'fapiPublicGetHistoricalTrades';
            } else if ($type === 'delivery') {
                $method = 'dapiPublicGetHistoricalTrades';
            }
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 500, maximum = 1000
        }
        //
        // Caveats:
        // - default $limit (500) applies only if no other parameters set, trades up
        //   to the maximum $limit may be returned to satisfy other parameters
        // - if both $limit and time window is set and time window contains more
        //   trades than the $limit then the last trades from the window are returned
        // - 'tradeId' accepted and returned by this $method is "aggregate" trade id
        //   which is different from actual trade id
        // - setting both fromId and time window results in error
        $response = $this->$method (array_merge($request, $query));
        //
        // aggregate trades
        //
        //     array(
        //         {
        //             "a" => 26129,         // Aggregate tradeId
        //             "p" => "0.01633102",  // Price
        //             "q" => "4.70443515",  // Quantity
        //             "f" => 27781,         // First tradeId
        //             "l" => 27781,         // Last tradeId
        //             "T" => 1498793709153, // Timestamp
        //             "m" => true,          // Was the buyer the maker?
        //             "M" => true           // Was the trade the best price match?
        //         }
        //     )
        //
        // recent public trades and historical public trades
        //
        //     array(
        //         {
        //             "id" => 28457,
        //             "price" => "4.00000100",
        //             "qty" => "12.00000000",
        //             "time" => 1499865549590,
        //             "isBuyerMaker" => true,
        //             "isBestMatch" => true
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'NEW' => 'open',
            'PARTIALLY_FILLED' => 'open',
            'FILLED' => 'closed',
            'CANCELED' => 'canceled',
            'PENDING_CANCEL' => 'canceling', // currently unused
            'REJECTED' => 'rejected',
            'EXPIRED' => 'expired',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //  spot
        //
        //     {
        //         "$symbol" => "LTCBTC",
        //         "orderId" => 1,
        //         "$clientOrderId" => "myOrder1",
        //         "$price" => "0.1",
        //         "origQty" => "1.0",
        //         "executedQty" => "0.0",
        //         "cummulativeQuoteQty" => "0.0",
        //         "$status" => "NEW",
        //         "$timeInForce" => "GTC",
        //         "$type" => "LIMIT",
        //         "$side" => "BUY",
        //         "$stopPrice" => "0.0",
        //         "icebergQty" => "0.0",
        //         "time" => 1499827319559,
        //         "updateTime" => 1499827319559,
        //         "isWorking" => true
        //     }
        //
        //  futures
        //
        //     {
        //         "$symbol" => "BTCUSDT",
        //         "orderId" => 1,
        //         "$clientOrderId" => "myOrder1",
        //         "$price" => "0.1",
        //         "origQty" => "1.0",
        //         "executedQty" => "1.0",
        //         "cumQuote" => "10.0",
        //         "$status" => "NEW",
        //         "$timeInForce" => "GTC",
        //         "$type" => "LIMIT",
        //         "$side" => "BUY",
        //         "$stopPrice" => "0.0",
        //         "updateTime" => 1499827319559
        //     }
        //
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = null;
        if (is_array($order) && array_key_exists('time', $order)) {
            $timestamp = $this->safe_integer($order, 'time');
        } else if (is_array($order) && array_key_exists('transactTime', $order)) {
            $timestamp = $this->safe_integer($order, 'transactTime');
        }
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'origQty');
        $filled = $this->safe_float($order, 'executedQty');
        $remaining = null;
        // - Spot/Margin $market => cummulativeQuoteQty
        // - Futures $market => cumQuote.
        //   Note this is not the actual $cost, since Binance futures uses leverage to calculate margins.
        $cost = $this->safe_float_2($order, 'cummulativeQuoteQty', 'cumQuote');
        if ($filled !== null) {
            if ($amount !== null) {
                $remaining = $amount - $filled;
                if ($this->options['parseOrderToPrecision']) {
                    $remaining = floatval($this->amount_to_precision($symbol, $remaining));
                }
                $remaining = max ($remaining, 0.0);
            }
            if ($price !== null) {
                if ($cost === null) {
                    $cost = $price * $filled;
                }
            }
        }
        $id = $this->safe_string($order, 'orderId');
        $type = $this->safe_string_lower($order, 'type');
        if ($type === 'market') {
            if ($price === 0.0) {
                if (($cost !== null) && ($filled !== null)) {
                    if (($cost > 0) && ($filled > 0)) {
                        $price = $cost / $filled;
                        if ($this->options['parseOrderToPrecision']) {
                            $price = floatval($this->price_to_precision($symbol, $price));
                        }
                    }
                }
            }
        } else if ($type === 'limit_maker') {
            $type = 'limit';
        }
        $side = $this->safe_string_lower($order, 'side');
        $fee = null;
        $trades = null;
        $fills = $this->safe_value($order, 'fills');
        if ($fills !== null) {
            $trades = $this->parse_trades($fills, $market);
            $numTrades = is_array($trades) ? count($trades) : 0;
            if ($numTrades > 0) {
                $cost = $trades[0]['cost'];
                $fee = array(
                    'cost' => $trades[0]['fee']['cost'],
                    'currency' => $trades[0]['fee']['currency'],
                );
                for ($i = 1; $i < count($trades); $i++) {
                    $cost = $this->sum($cost, $trades[$i]['cost']);
                    $fee['cost'] = $this->sum($fee['cost'], $trades[$i]['fee']['cost']);
                }
            }
        }
        $average = null;
        if ($cost !== null) {
            if ($filled) {
                $average = $cost / $filled;
                if ($this->options['parseOrderToPrecision']) {
                    $average = floatval($this->price_to_precision($symbol, $average));
                }
            }
            if ($this->options['parseOrderToPrecision']) {
                $cost = floatval($this->cost_to_precision($symbol, $cost));
            }
        }
        $clientOrderId = $this->safe_string($order, 'clientOrderId');
        $timeInForce = $this->safe_string($order, 'timeInForce');
        $postOnly = ($type === 'limit_maker') || ($timeInForce === 'GTX');
        $stopPrice = $this->safe_float($order, 'stopPrice');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => $timeInForce,
            'postOnly' => $postOnly,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string_2($this->options, 'createOrder', 'defaultType', $market['type']);
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $clientOrderId = $this->safe_string_2($params, 'newClientOrderId', 'clientOrderId');
        $params = $this->omit($params, array( 'type', 'newClientOrderId', 'clientOrderId' ));
        $method = 'privatePostOrder';
        if ($orderType === 'future') {
            $method = 'fapiPrivatePostOrder';
        } else if ($orderType === 'delivery') {
            $method = 'dapiPrivatePostOrder';
        } else if ($orderType === 'margin') {
            $method = 'sapiPostMarginOrder';
        }
        // the next 5 lines are added to support for testing orders
        if ($market['spot']) {
            $test = $this->safe_value($params, 'test', false);
            if ($test) {
                $method .= 'Test';
            }
            $params = $this->omit($params, 'test');
        }
        $uppercaseType = strtoupper($type);
        $validOrderTypes = $this->safe_value($market['info'], 'orderTypes');
        if (!$this->in_array($uppercaseType, $validOrderTypes)) {
            throw new InvalidOrder($this->id . ' ' . $type . ' is not a valid order $type in ' . $market['type'] . ' $market ' . $symbol);
        }
        $request = array(
            'symbol' => $market['id'],
            'type' => $uppercaseType,
            'side' => strtoupper($side),
        );
        if ($clientOrderId === null) {
            $broker = $this->safe_value($this->options, 'broker');
            if ($broker) {
                $brokerId = $this->safe_string($broker, $orderType);
                if ($brokerId !== null) {
                    $request['newClientOrderId'] = $brokerId . $this->uuid22();
                }
            }
        } else {
            $request['newClientOrderId'] = $clientOrderId;
        }
        if ($market['spot']) {
            $request['newOrderRespType'] = $this->safe_value($this->options['newOrderRespType'], $type, 'RESULT'); // 'ACK' for order id, 'RESULT' for full order or 'FULL' for order with fills
        }
        // additional required fields depending on the order $type
        $timeInForceIsRequired = false;
        $priceIsRequired = false;
        $stopPriceIsRequired = false;
        $quantityIsRequired = false;
        //
        // spot/margin
        //
        //     LIMIT                timeInForce, quantity, $price
        //     MARKET               quantity or $quoteOrderQty
        //     STOP_LOSS            quantity, $stopPrice
        //     STOP_LOSS_LIMIT      timeInForce, quantity, $price, $stopPrice
        //     TAKE_PROFIT          quantity, $stopPrice
        //     TAKE_PROFIT_LIMIT    timeInForce, quantity, $price, $stopPrice
        //     LIMIT_MAKER          quantity, $price
        //
        // futures
        //
        //     LIMIT                timeInForce, quantity, $price
        //     MARKET               quantity
        //     STOP/TAKE_PROFIT     quantity, $price, $stopPrice
        //     STOP_MARKET          $stopPrice
        //     TAKE_PROFIT_MARKET   $stopPrice
        //     TRAILING_STOP_MARKET $callbackRate
        //
        if ($uppercaseType === 'MARKET') {
            $quoteOrderQty = $this->safe_value($this->options, 'quoteOrderQty', false);
            if ($quoteOrderQty) {
                $quoteOrderQty = $this->safe_float($params, 'quoteOrderQty');
                $precision = $market['precision']['price'];
                if ($quoteOrderQty !== null) {
                    $request['quoteOrderQty'] = $this->decimal_to_precision($quoteOrderQty, TRUNCATE, $precision, $this->precisionMode);
                    $params = $this->omit($params, 'quoteOrderQty');
                } else if ($price !== null) {
                    $request['quoteOrderQty'] = $this->decimal_to_precision($amount * $price, TRUNCATE, $precision, $this->precisionMode);
                } else {
                    $quantityIsRequired = true;
                }
            } else {
                $quantityIsRequired = true;
            }
        } else if ($uppercaseType === 'LIMIT') {
            $priceIsRequired = true;
            $timeInForceIsRequired = true;
            $quantityIsRequired = true;
        } else if (($uppercaseType === 'STOP_LOSS') || ($uppercaseType === 'TAKE_PROFIT')) {
            $stopPriceIsRequired = true;
            $quantityIsRequired = true;
            if ($market['future']) {
                $priceIsRequired = true;
            }
        } else if (($uppercaseType === 'STOP_LOSS_LIMIT') || ($uppercaseType === 'TAKE_PROFIT_LIMIT')) {
            $quantityIsRequired = true;
            $stopPriceIsRequired = true;
            $priceIsRequired = true;
            $timeInForceIsRequired = true;
        } else if ($uppercaseType === 'LIMIT_MAKER') {
            $priceIsRequired = true;
            $quantityIsRequired = true;
        } else if ($uppercaseType === 'STOP') {
            $quantityIsRequired = true;
            $stopPriceIsRequired = true;
            $priceIsRequired = true;
        } else if (($uppercaseType === 'STOP_MARKET') || ($uppercaseType === 'TAKE_PROFIT_MARKET')) {
            $closePosition = $this->safe_value($params, 'closePosition');
            if ($closePosition === null) {
                $quantityIsRequired = true;
            }
            $stopPriceIsRequired = true;
        } else if ($uppercaseType === 'TRAILING_STOP_MARKET') {
            $quantityIsRequired = true;
            $callbackRate = $this->safe_float($params, 'callbackRate');
            if ($callbackRate === null) {
                throw new InvalidOrder($this->id . ' createOrder() requires a $callbackRate extra param for a ' . $type . ' order');
            }
        }
        if ($quantityIsRequired) {
            $request['quantity'] = $this->amount_to_precision($symbol, $amount);
        }
        if ($priceIsRequired) {
            if ($price === null) {
                throw new InvalidOrder($this->id . ' createOrder() requires a $price argument for a ' . $type . ' order');
            }
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        if ($timeInForceIsRequired) {
            $request['timeInForce'] = $this->options['defaultTimeInForce']; // 'GTC' = Good To Cancel (default), 'IOC' = Immediate Or Cancel
        }
        if ($stopPriceIsRequired) {
            $stopPrice = $this->safe_float($params, 'stopPrice');
            if ($stopPrice === null) {
                throw new InvalidOrder($this->id . ' createOrder() requires a $stopPrice extra param for a ' . $type . ' order');
            } else {
                $params = $this->omit($params, 'stopPrice');
                $request['stopPrice'] = $this->price_to_precision($symbol, $stopPrice);
            }
        }
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_order($response, $market);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string_2($this->options, 'fetchOrder', 'defaultType', $market['type']);
        $type = $this->safe_string($params, 'type', $defaultType);
        $method = 'privateGetOrder';
        if ($type === 'future') {
            $method = 'fapiPrivateGetOrder';
        } else if ($type === 'delivery') {
            $method = 'dapiPrivateGetOrder';
        } else if ($type === 'margin') {
            $method = 'sapiGetMarginOrder';
        }
        $request = array(
            'symbol' => $market['id'],
        );
        $clientOrderId = $this->safe_value_2($params, 'origClientOrderId', 'clientOrderId');
        if ($clientOrderId !== null) {
            $request['origClientOrderId'] = $clientOrderId;
        } else {
            $request['orderId'] = $id;
        }
        $query = $this->omit($params, array( 'type', 'clientOrderId', 'origClientOrderId' ));
        $response = $this->$method (array_merge($request, $query));
        return $this->parse_order($response, $market);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string_2($this->options, 'fetchOrders', 'defaultType', $market['type']);
        $type = $this->safe_string($params, 'type', $defaultType);
        $method = 'privateGetAllOrders';
        if ($type === 'future') {
            $method = 'fapiPrivateGetAllOrders';
        } else if ($type === 'delivery') {
            $method = 'dapiPrivateGetAllOrders';
        } else if ($type === 'margin') {
            $method = 'sapiGetMarginAllOrders';
        }
        $request = array(
            'symbol' => $market['id'],
        );
        if ($since !== null) {
            $request['startTime'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $query = $this->omit($params, 'type');
        $response = $this->$method (array_merge($request, $query));
        //
        //  spot
        //
        //     array(
        //         {
        //             "$symbol" => "LTCBTC",
        //             "orderId" => 1,
        //             "clientOrderId" => "myOrder1",
        //             "price" => "0.1",
        //             "origQty" => "1.0",
        //             "executedQty" => "0.0",
        //             "cummulativeQuoteQty" => "0.0",
        //             "status" => "NEW",
        //             "timeInForce" => "GTC",
        //             "$type" => "LIMIT",
        //             "side" => "BUY",
        //             "stopPrice" => "0.0",
        //             "icebergQty" => "0.0",
        //             "time" => 1499827319559,
        //             "updateTime" => 1499827319559,
        //             "isWorking" => true
        //         }
        //     )
        //
        //  futures
        //
        //     array(
        //         {
        //             "$symbol" => "BTCUSDT",
        //             "orderId" => 1,
        //             "clientOrderId" => "myOrder1",
        //             "price" => "0.1",
        //             "origQty" => "1.0",
        //             "executedQty" => "1.0",
        //             "cumQuote" => "10.0",
        //             "status" => "NEW",
        //             "timeInForce" => "GTC",
        //             "$type" => "LIMIT",
        //             "side" => "BUY",
        //             "stopPrice" => "0.0",
        //             "updateTime" => 1499827319559
        //         }
        //     )
        //
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $query = null;
        $type = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
            $defaultType = $this->safe_string_2($this->options, 'fetchOpenOrders', 'defaultType', $market['type']);
            $type = $this->safe_string($params, 'type', $defaultType);
            $query = $this->omit($params, 'type');
        } else if ($this->options['warnOnFetchOpenOrdersWithoutSymbol']) {
            $symbols = $this->symbols;
            $numSymbols = is_array($symbols) ? count($symbols) : 0;
            $fetchOpenOrdersRateLimit = intval($numSymbols / 2);
            throw new ExchangeError($this->id . ' fetchOpenOrders WARNING => fetching open orders without specifying a $symbol is rate-limited to one call per ' . (string) $fetchOpenOrdersRateLimit . ' seconds. Do not call this $method frequently to avoid ban. Set ' . $this->id . '.options["warnOnFetchOpenOrdersWithoutSymbol"] = false to suppress this warning message.');
        } else {
            $defaultType = $this->safe_string_2($this->options, 'fetchOpenOrders', 'defaultType', 'spot');
            $type = $this->safe_string($params, 'type', $defaultType);
            $query = $this->omit($params, 'type');
        }
        $method = 'privateGetOpenOrders';
        if ($type === 'future') {
            $method = 'fapiPrivateGetOpenOrders';
        } else if ($type === 'delivery') {
            $method = 'dapiPrivateGetOpenOrders';
        } else if ($type === 'margin') {
            $method = 'sapiGetMarginOpenOrders';
        }
        $response = $this->$method (array_merge($request, $query));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        return $this->filter_by($orders, 'status', 'closed');
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string_2($this->options, 'fetchOpenOrders', 'defaultType', $market['type']);
        $type = $this->safe_string($params, 'type', $defaultType);
        // https://github.com/ccxt/ccxt/issues/6507
        $origClientOrderId = $this->safe_value_2($params, 'origClientOrderId', 'clientOrderId');
        $request = array(
            'symbol' => $market['id'],
            // 'orderId' => $id,
            // 'origClientOrderId' => $id,
        );
        if ($origClientOrderId === null) {
            $request['orderId'] = $id;
        } else {
            $request['origClientOrderId'] = $origClientOrderId;
        }
        $method = 'privateDeleteOrder';
        if ($type === 'future') {
            $method = 'fapiPrivateDeleteOrder';
        } else if ($type === 'delivery') {
            $method = 'dapiPrivateDeleteOrder';
        } else if ($type === 'margin') {
            $method = 'sapiDeleteMarginOrder';
        }
        $query = $this->omit($params, array( 'type', 'origClientOrderId', 'clientOrderId' ));
        $response = $this->$method (array_merge($request, $query));
        return $this->parse_order($response);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelAllOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $defaultType = $this->safe_string_2($this->options, 'cancelAllOrders', 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $query = $this->omit($params, 'type');
        $method = 'privateDeleteOpenOrders';
        if ($type === 'margin') {
            $method = 'sapiDeleteMarginOpenOrders';
        } else if ($type === 'future') {
            $method = 'fapiPrivateDeleteAllOpenOrders';
        } else if ($type === 'delivery') {
            $method = 'dapiPrivateDeleteAllOpenOrders';
        }
        $response = $this->$method (array_merge($request, $query));
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) == 0) {
            return $this->parse_orders($response, $market);
        } else {
            return $response;
        }
    }

    public function fetch_positions($symbols = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->fetch_balance($params);
        $info = $this->safe_value($response, 'info', array());
        //
        // futures, delivery
        //
        //     {
        //         "feeTier":0,
        //         "canTrade":true,
        //         "canDeposit":true,
        //         "canWithdraw":true,
        //         "updateTime":0,
        //         "assets":array(
        //             {
        //                 "asset":"ETH",
        //                 "walletBalance":"0.09886711",
        //                 "unrealizedProfit":"0.00000000",
        //                 "marginBalance":"0.09886711",
        //                 "maintMargin":"0.00000000",
        //                 "initialMargin":"0.00000000",
        //                 "positionInitialMargin":"0.00000000",
        //                 "openOrderInitialMargin":"0.00000000",
        //                 "maxWithdrawAmount":"0.09886711",
        //                 "crossWalletBalance":"0.09886711",
        //                 "crossUnPnl":"0.00000000",
        //                 "availableBalance":"0.09886711"
        //             }
        //         ),
        //         "$positions":array(
        //             array(
        //                 "symbol":"BTCUSD_201225",
        //                 "initialMargin":"0",
        //                 "maintMargin":"0",
        //                 "unrealizedProfit":"0.00000000",
        //                 "positionInitialMargin":"0",
        //                 "openOrderInitialMargin":"0",
        //                 "leverage":"20",
        //                 "isolated":false,
        //                 "positionSide":"BOTH",
        //                 "entryPrice":"0.00000000",
        //                 "maxQty":"250", // "maxNotional" on futures
        //             ),
        //         )
        //     }
        //
        $positions = $this->safe_value_2($info, 'positions', 'userAssets', array());
        // todo unify parsePosition/parsePositions
        return $positions;
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string_2($this->options, 'fetchMyTrades', 'defaultType', $market['type']);
        $type = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $method = null;
        if ($type === 'spot') {
            $method = 'privateGetMyTrades';
        } else if ($type === 'margin') {
            $method = 'sapiGetMarginMyTrades';
        } else if ($type === 'future') {
            $method = 'fapiPrivateGetUserTrades';
        } else if ($type === 'delivery') {
            $method = 'dapiPrivateGetUserTrades';
        }
        $request = array(
            'symbol' => $market['id'],
        );
        if ($since !== null) {
            $request['startTime'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->$method (array_merge($request, $params));
        //
        // spot trade
        //
        //     array(
        //         {
        //             "$symbol" => "BNBBTC",
        //             "id" => 28457,
        //             "orderId" => 100234,
        //             "price" => "4.00000100",
        //             "qty" => "12.00000000",
        //             "commission" => "10.10000000",
        //             "commissionAsset" => "BNB",
        //             "time" => 1499865549590,
        //             "isBuyer" => true,
        //             "isMaker" => false,
        //             "isBestMatch" => true,
        //         }
        //     )
        //
        // futures trade
        //
        //     array(
        //         {
        //             "accountId" => 20,
        //             "buyer" => False,
        //             "commission" => "-0.07819010",
        //             "commissionAsset" => "USDT",
        //             "counterPartyId" => 653,
        //             "id" => 698759,
        //             "maker" => False,
        //             "orderId" => 25851813,
        //             "price" => "7819.01",
        //             "qty" => "0.002",
        //             "quoteQty" => "0.01563",
        //             "realizedPnl" => "-0.91539999",
        //             "side" => "SELL",
        //             "$symbol" => "BTCUSDT",
        //             "time" => 1569514978020
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_dust_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        //
        // Binance provides an opportunity to trade insignificant ($i->e. non-tradable and non-withdrawable)
        // token leftovers (of any asset) into `BNB` coin which in turn can be used to pay trading fees with it.
        // The corresponding $trades history is called the `Dust Log` and can be requested via the following end-point:
        // https://github.com/binance-exchange/binance-official-api-docs/blob/master/wapi-api.md#dustlog-user_data
        //
        $this->load_markets();
        $response = $this->wapiGetUserAssetDribbletLog ($params);
        // { success =>    true,
        //   $results => { total =>    1,
        //               $rows => array( {     transfered_total => "1.06468458",
        //                         service_charge_total => "0.02172826",
        //                                      tran_id => 2701371634,
        //                                         $logs => array( {              tranId =>  2701371634,
        //                                                   serviceChargeAmount => "0.00012819",
        //                                                                   uid => "35103861",
        //                                                                amount => "0.8012",
        //                                                           operateTime => "2018-10-07 17:56:07",
        //                                                      transferedAmount => "0.00628141",
        //                                                             fromAsset => "ADA"                  } ),
        //                                 operate_time => "2018-10-07 17:56:06"                                } ) } }
        $results = $this->safe_value($response, 'results', array());
        $rows = $this->safe_value($results, 'rows', array());
        $data = array();
        for ($i = 0; $i < count($rows); $i++) {
            $logs = $rows[$i]['logs'];
            for ($j = 0; $j < count($logs); $j++) {
                $logs[$j]['isDustTrade'] = true;
                $data[] = $logs[$j];
            }
        }
        $trades = $this->parse_trades($data, null, $since, $limit);
        return $this->filter_by_since_limit($trades, $since, $limit);
    }

    public function parse_dust_trade($trade, $market = null) {
        // array(              tranId =>  2701371634,
        //   serviceChargeAmount => "0.00012819",
        //                   uid => "35103861",
        //                $amount => "0.8012",
        //           operateTime => "2018-10-07 17:56:07",
        //      transferedAmount => "0.00628141",
        //             fromAsset => "ADA"                  ),
        $orderId = $this->safe_string($trade, 'tranId');
        $timestamp = $this->parse8601($this->safe_string($trade, 'operateTime'));
        $tradedCurrency = $this->safe_currency_code($this->safe_string($trade, 'fromAsset'));
        $earnedCurrency = $this->currency('BNB')['code'];
        $applicantSymbol = $earnedCurrency . '/' . $tradedCurrency;
        $tradedCurrencyIsQuote = false;
        if (is_array($this->markets) && array_key_exists($applicantSymbol, $this->markets)) {
            $tradedCurrencyIsQuote = true;
        }
        //
        // Warning
        // Binance dust $trade `$fee` is already excluded from the `BNB` earning reported in the `Dust Log`.
        // So the parser should either set the `$fee->cost` to `0` or add it on top of the earned
        // BNB `$amount` (or `$cost` depending on the $trade `$side`). The second of the above options
        // is much more illustrative and therefore preferable.
        //
        $fee = array(
            'currency' => $earnedCurrency,
            'cost' => $this->safe_float($trade, 'serviceChargeAmount'),
        );
        $symbol = null;
        $amount = null;
        $cost = null;
        $side = null;
        if ($tradedCurrencyIsQuote) {
            $symbol = $applicantSymbol;
            $amount = $this->sum($this->safe_float($trade, 'transferedAmount'), $fee['cost']);
            $cost = $this->safe_float($trade, 'amount');
            $side = 'buy';
        } else {
            $symbol = $tradedCurrency . '/' . $earnedCurrency;
            $amount = $this->safe_float($trade, 'amount');
            $cost = $this->sum($this->safe_float($trade, 'transferedAmount'), $fee['cost']);
            $side = 'sell';
        }
        $price = null;
        if ($cost !== null) {
            if ($amount) {
                $price = $cost / $amount;
            }
        }
        $id = null;
        $type = null;
        $takerOrMaker = null;
        return array(
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => $type,
            'takerOrMaker' => $takerOrMaker,
            'side' => $side,
            'amount' => $amount,
            'price' => $price,
            'cost' => $cost,
            'fee' => $fee,
            'info' => $trade,
        );
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array();
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset'] = $currency['id'];
        }
        if ($since !== null) {
            $request['startTime'] = $since;
            // max 3 months range https://github.com/ccxt/ccxt/issues/6495
            $request['endTime'] = $this->sum($since, 7776000000);
        }
        $response = $this->wapiGetDepositHistory (array_merge($request, $params));
        //
        //     {     success =>    true,
        //       depositList => array( { insertTime =>  1517425007000,
        //                            amount =>  0.3,
        //                           address => "0x0123456789abcdef",
        //                        addressTag => "",
        //                              txId => "0x0123456789abcdef",
        //                             asset => "ETH",
        //                            status =>  1                                                                    } ) }
        //
        return $this->parse_transactions($response['depositList'], $currency, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array();
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset'] = $currency['id'];
        }
        if ($since !== null) {
            $request['startTime'] = $since;
            // max 3 months range https://github.com/ccxt/ccxt/issues/6495
            $request['endTime'] = $this->sum($since, 7776000000);
        }
        $response = $this->wapiGetWithdrawHistory (array_merge($request, $params));
        //
        //     { withdrawList => array( array(      amount =>  14,
        //                             address => "0x0123456789abcdef...",
        //                         successTime =>  1514489710000,
        //                      transactionFee =>  0.01,
        //                          addressTag => "",
        //                                txId => "0x0123456789abcdef...",
        //                                  id => "0123456789abcdef...",
        //                               asset => "ETH",
        //                           applyTime =>  1514488724000,
        //                              status =>  6                       ),
        //                       {      amount =>  7600,
        //                             address => "0x0123456789abcdef...",
        //                         successTime =>  1515323226000,
        //                      transactionFee =>  0.01,
        //                          addressTag => "",
        //                                txId => "0x0123456789abcdef...",
        //                                  id => "0123456789abcdef...",
        //                               asset => "ICN",
        //                           applyTime =>  1515322539000,
        //                              status =>  6                       }  ),
        //            success =>    true                                         }
        //
        return $this->parse_transactions($response['withdrawList'], $currency, $since, $limit);
    }

    public function parse_transaction_status_by_type($status, $type = null) {
        $statusesByType = array(
            'deposit' => array(
                '0' => 'pending',
                '1' => 'ok',
            ),
            'withdrawal' => array(
                '0' => 'pending', // Email Sent
                '1' => 'canceled', // Cancelled (different from 1 = ok in deposits)
                '2' => 'pending', // Awaiting Approval
                '3' => 'failed', // Rejected
                '4' => 'pending', // Processing
                '5' => 'failed', // Failure
                '6' => 'ok', // Completed
            ),
        );
        $statuses = $this->safe_value($statusesByType, $type, array());
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         $insertTime =>  1517425007000,
        //         $amount =>  0.3,
        //         $address => "0x0123456789abcdef",
        //         addressTag => "",
        //         txId => "0x0123456789abcdef",
        //         asset => "ETH",
        //         $status =>  1
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         $amount =>  14,
        //         $address => "0x0123456789abcdef...",
        //         successTime =>  1514489710000,
        //         transactionFee =>  0.01,
        //         addressTag => "",
        //         txId => "0x0123456789abcdef...",
        //         $id => "0123456789abcdef...",
        //         asset => "ETH",
        //         $applyTime =>  1514488724000,
        //         $status =>  6
        //     }
        //
        $id = $this->safe_string($transaction, 'id');
        $address = $this->safe_string($transaction, 'address');
        $tag = $this->safe_string($transaction, 'addressTag'); // set but unused
        if ($tag !== null) {
            if (strlen($tag) < 1) {
                $tag = null;
            }
        }
        $txid = $this->safe_string($transaction, 'txId');
        if (($txid !== null) && (mb_strpos($txid, 'Internal transfer ') !== false)) {
            $txid = mb_substr($txid, 18);
        }
        $currencyId = $this->safe_string($transaction, 'asset');
        $code = $this->safe_currency_code($currencyId, $currency);
        $timestamp = null;
        $insertTime = $this->safe_integer($transaction, 'insertTime');
        $applyTime = $this->safe_integer($transaction, 'applyTime');
        $type = $this->safe_string($transaction, 'type');
        if ($type === null) {
            if (($insertTime !== null) && ($applyTime === null)) {
                $type = 'deposit';
                $timestamp = $insertTime;
            } else if (($insertTime === null) && ($applyTime !== null)) {
                $type = 'withdrawal';
                $timestamp = $applyTime;
            }
        }
        $status = $this->parse_transaction_status_by_type($this->safe_string($transaction, 'status'), $type);
        $amount = $this->safe_float($transaction, 'amount');
        $feeCost = $this->safe_float($transaction, 'transactionFee');
        $fee = null;
        if ($feeCost !== null) {
            $fee = array( 'currency' => $code, 'cost' => $feeCost );
        }
        $updated = $this->safe_integer($transaction, 'successTime');
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'addressTo' => $address,
            'addressFrom' => null,
            'tag' => $tag,
            'tagTo' => $tag,
            'tagFrom' => null,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'coin' => $currency['id'],
            // 'network' => 'ETH', // 'BSC', 'XMR', you can get network and isDefault in networkList in the $response of sapiGetCapitalConfigDetail
        );
        // has support for the 'network' parameter
        // https://binance-docs.github.io/apidocs/spot/en/#deposit-$address-supporting-network-user_data
        $response = $this->sapiGetCapitalDepositAddress (array_merge($request, $params));
        //
        //     {
        //         $currency => 'XRP',
        //         $address => 'rEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLh',
        //         $tag => '108618262',
        //         info => {
        //             coin => 'XRP',
        //             $address => 'rEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLh',
        //             $tag => '108618262',
        //             url => 'https://bithomp.com/explorer/rEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLh'
        //         }
        //     }
        //
        $address = $this->safe_string($response, 'address');
        $tag = $this->safe_string($response, 'tag');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_funding_fees($codes = null, $params = array ()) {
        $response = $this->wapiGetAssetDetail ($params);
        //
        //     {
        //         "success" => true,
        //         "assetDetail" => {
        //             "CTR" => array(
        //                 "minWithdrawAmount" => "70.00000000", //min withdraw amount
        //                 "depositStatus" => false,//deposit status
        //                 "withdrawFee" => 35, // withdraw fee
        //                 "withdrawStatus" => true, //withdraw status
        //                 "depositTip" => "Delisted, Deposit Suspended" //reason
        //             ),
        //             "SKY" => {
        //                 "minWithdrawAmount" => "0.02000000",
        //                 "depositStatus" => true,
        //                 "withdrawFee" => 0.01,
        //                 "withdrawStatus" => true
        //             }
        //         }
        //     }
        //
        $detail = $this->safe_value($response, 'assetDetail', array());
        $ids = is_array($detail) ? array_keys($detail) : array();
        $withdrawFees = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $code = $this->safe_currency_code($id);
            $withdrawFees[$code] = $this->safe_float($detail[$id], 'withdrawFee');
        }
        return array(
            'withdraw' => $withdrawFees,
            'deposit' => array(),
            'info' => $response,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        // $name is optional, can be overrided via $params
        $name = mb_substr($address, 0, 20 - 0);
        $request = array(
            'asset' => $currency['id'],
            'address' => $address,
            'amount' => floatval($amount),
            'name' => $name, // $name is optional, can be overrided via $params
            // https://binance-docs.github.io/apidocs/spot/en/#withdraw-sapi
            // issue sapiGetCapitalConfigGetall () to get networks for withdrawing USDT ERC20 vs USDT Omni
            // 'network' => 'ETH', // 'BTC', 'TRX', etc, optional
        );
        if ($tag !== null) {
            $request['addressTag'] = $tag;
        }
        $response = $this->wapiPostWithdraw (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'id'),
        );
    }

    public function parse_trading_fee($fee, $market = null) {
        //
        //     {
        //         "$symbol" => "ADABNB",
        //         "maker" => 0.9000,
        //         "taker" => 1.0000
        //     }
        //
        $marketId = $this->safe_string($fee, 'symbol');
        $symbol = $this->safe_symbol($marketId);
        return array(
            'info' => $fee,
            'symbol' => $symbol,
            'maker' => $this->safe_float($fee, 'maker'),
            'taker' => $this->safe_float($fee, 'taker'),
        );
    }

    public function fetch_trading_fee($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->wapiGetTradeFee (array_merge($request, $params));
        //
        //     {
        //         "$tradeFee" => array(
        //             {
        //                 "$symbol" => "ADABNB",
        //                 "maker" => 0.9000,
        //                 "taker" => 1.0000
        //             }
        //         ),
        //         "success" => true
        //     }
        //
        $tradeFee = $this->safe_value($response, 'tradeFee', array());
        $first = $this->safe_value($tradeFee, 0, array());
        return $this->parse_trading_fee($first);
    }

    public function fetch_trading_fees($params = array ()) {
        $this->load_markets();
        $response = $this->wapiGetTradeFee ($params);
        //
        //     {
        //         "$tradeFee" => array(
        //             {
        //                 "$symbol" => "ADABNB",
        //                 "maker" => 0.9000,
        //                 "taker" => 1.0000
        //             }
        //         ),
        //         "success" => true
        //     }
        //
        $tradeFee = $this->safe_value($response, 'tradeFee', array());
        $result = array();
        for ($i = 0; $i < count($tradeFee); $i++) {
            $fee = $this->parse_trading_fee($tradeFee[$i]);
            $symbol = $fee['symbol'];
            $result[$symbol] = $fee;
        }
        return $result;
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        if (!(is_array($this->urls['api']) && array_key_exists($api, $this->urls['api']))) {
            throw new NotSupported($this->id . ' does not have a testnet/sandbox URL for ' . $api . ' endpoints');
        }
        $url = $this->urls['api'][$api];
        $url .= '/' . $path;
        if ($api === 'wapi') {
            $url .= '.html';
        }
        $userDataStream = ($path === 'userDataStream') || ($path === 'listenKey');
        if ($path === 'historicalTrades') {
            if ($this->apiKey) {
                $headers = array(
                    'X-MBX-APIKEY' => $this->apiKey,
                );
            } else {
                throw new AuthenticationError($this->id . ' historicalTrades endpoint requires `apiKey` credential');
            }
        } else if ($userDataStream) {
            if ($this->apiKey) {
                // v1 special case for $userDataStream
                $body = $this->urlencode($params);
                $headers = array(
                    'X-MBX-APIKEY' => $this->apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                );
            } else {
                throw new AuthenticationError($this->id . ' $userDataStream endpoint requires `apiKey` credential');
            }
        }
        if (($api === 'private') || ($api === 'sapi') || ($api === 'wapi' && $path !== 'systemStatus') || ($api === 'dapiPrivate') || ($api === 'fapiPrivate') || ($api === 'fapiPrivateV2')) {
            $this->check_required_credentials();
            $query = null;
            $recvWindow = $this->safe_integer($this->options, 'recvWindow', 5000);
            if (($api === 'sapi') && ($path === 'asset/dust')) {
                $query = $this->urlencode_with_array_repeat(array_merge(array(
                    'timestamp' => $this->nonce(),
                    'recvWindow' => $recvWindow,
                ), $params));
            } else if (($path === 'batchOrders') || (mb_strpos($path, 'sub-account') !== false)) {
                $query = $this->rawencode(array_merge(array(
                    'timestamp' => $this->nonce(),
                    'recvWindow' => $recvWindow,
                ), $params));
            } else {
                $query = $this->urlencode(array_merge(array(
                    'timestamp' => $this->nonce(),
                    'recvWindow' => $recvWindow,
                ), $params));
            }
            $signature = $this->hmac($this->encode($query), $this->encode($this->secret));
            $query .= '&' . 'signature=' . $signature;
            $headers = array(
                'X-MBX-APIKEY' => $this->apiKey,
            );
            if (($method === 'GET') || ($method === 'DELETE') || ($api === 'wapi')) {
                $url .= '?' . $query;
            } else {
                $body = $query;
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }
        } else {
            // $userDataStream endpoints are public, but POST, PUT, DELETE
            // therefore they don't accept URL $query arguments
            // https://github.com/ccxt/ccxt/issues/5224
            if (!$userDataStream) {
                if ($params) {
                    $url .= '?' . $this->urlencode($params);
                }
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (($code === 418) || ($code === 429)) {
            throw new DDoSProtection($this->id . ' ' . (string) $code . ' ' . $reason . ' ' . $body);
        }
        // $error $response in a form => array( "$code" => -1013, "msg" => "Invalid quantity." )
        // following block cointains legacy checks against $message patterns in "msg" property
        // will switch "$code" checks eventually, when we know all of them
        if ($code >= 400) {
            if (mb_strpos($body, 'Price * QTY is zero or less') !== false) {
                throw new InvalidOrder($this->id . ' order cost = amount * price is zero or less ' . $body);
            }
            if (mb_strpos($body, 'LOT_SIZE') !== false) {
                throw new InvalidOrder($this->id . ' order amount should be evenly divisible by lot size ' . $body);
            }
            if (mb_strpos($body, 'PRICE_FILTER') !== false) {
                throw new InvalidOrder($this->id . ' order price is invalid, i.e. exceeds allowed price precision, exceeds min price or max price limits or is invalid float value in general, use $this->price_to_precision(symbol, amount) ' . $body);
            }
        }
        if ($response === null) {
            return; // fallback to default $error handler
        }
        // check $success value for wapi endpoints
        // $response in format array('msg' => 'The coin does not exist.', 'success' => true/false)
        $success = $this->safe_value($response, 'success', true);
        if (!$success) {
            $message = $this->safe_string($response, 'msg');
            $parsedMessage = null;
            if ($message !== null) {
                try {
                    $parsedMessage = json_decode($message, $as_associative_array = true);
                } catch (Exception $e) {
                    // do nothing
                    $parsedMessage = null;
                }
                if ($parsedMessage !== null) {
                    $response = $parsedMessage;
                }
            }
        }
        $message = $this->safe_string($response, 'msg');
        if ($message !== null) {
            $this->throw_exactly_matched_exception($this->exceptions, $message, $this->id . ' ' . $message);
        }
        // checks against $error codes
        $error = $this->safe_string($response, 'code');
        if ($error !== null) {
            // https://github.com/ccxt/ccxt/issues/6501
            // https://github.com/ccxt/ccxt/issues/7742
            if (($error === '200') || ($error === '0')) {
                return;
            }
            // a workaround for array("$code":-2015,"msg":"Invalid API-key, IP, or permissions for action.")
            // despite that their $message is very confusing, it is raised by Binance
            // on a temporary ban, the API key is valid, but disabled for a while
            if (($error === '-2015') && $this->options['hasAlreadyAuthenticatedSuccessfully']) {
                throw new DDoSProtection($this->id . ' temporary banned => ' . $body);
            }
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions, $error, $feedback);
            throw new ExchangeError($feedback);
        }
        if (!$success) {
            throw new ExchangeError($this->id . ' ' . $body);
        }
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        // a workaround for array("code":-2015,"msg":"Invalid API-key, IP, or permissions for action.")
        if (($api === 'private') || ($api === 'wapi')) {
            $this->options['hasAlreadyAuthenticatedSuccessfully'] = true;
        }
        return $response;
    }
}

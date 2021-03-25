<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\BadRequest;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;
use \ccxt\DDoSProtection;
use \ccxt\ExchangeNotAvailable;
use \ccxt\RequestTimeout;

class crex24 extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'crex24',
            'name' => 'CREX24',
            'countries' => array( 'EE' ), // Estonia
            'rateLimit' => 500,
            'version' => 'v2',
            // new metainfo interface
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchBidsAsks' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchFundingFees' => false,
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
                'fetchTradingFee' => false, // actually, true, but will be implemented later
                'fetchTradingFees' => false, // actually, true, but will be implemented later
                'fetchTransactions' => true,
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
                '4h' => '4h',
                '1d' => '1d',
                '1w' => '1w',
                '1M' => '1mo',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/47813922-6f12cc00-dd5d-11e8-97c6-70f957712d47.jpg',
                'api' => 'https://api.crex24.com',
                'www' => 'https://crex24.com',
                'referral' => 'https://crex24.com/?refid=slxsjsjtil8xexl9hksr',
                'doc' => 'https://docs.crex24.com/trade-api/v2',
                'fees' => 'https://crex24.com/fees',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencies',
                        'instruments',
                        'tickers',
                        'recentTrades',
                        'orderBook',
                        'ohlcv',
                    ),
                ),
                'trading' => array(
                    'get' => array(
                        'orderStatus',
                        'orderTrades',
                        'activeOrders',
                        'orderHistory',
                        'tradeHistory',
                        'tradeFee',
                        // this is in trading API according to their docs, but most likely a typo in their docs
                        'moneyTransferStatus',
                    ),
                    'post' => array(
                        'placeOrder',
                        'modifyOrder',
                        'cancelOrdersById',
                        'cancelOrdersByInstrument',
                        'cancelAllOrders',
                    ),
                ),
                'account' => array(
                    'get' => array(
                        'balance',
                        'depositAddress',
                        'moneyTransfers',
                        // this is in trading API according to their docs, but most likely a typo in their docs
                        'moneyTransferStatus',
                        'previewWithdrawal',
                    ),
                    'post' => array(
                        'withdraw',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.001,
                    'maker' => -0.0001,
                ),
                // should be deleted, these are outdated and inaccurate
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(),
                    'deposit' => array(),
                ),
            ),
            'commonCurrencies' => array(
                'BCC' => 'BCH',
                'BIT' => 'BitMoney',
                'BULL' => 'BuySell',
                'CREDIT' => 'TerraCredit',
                'GHOST' => 'GHOSTPRISM',
                'IQ' => 'IQ.Cash',
                'PUT' => 'PutinCoin',
                'SBTC' => 'SBTCT', // SiamBitcoin
                'UNI' => 'Universe',
                'YOYO' => 'YOYOW',
            ),
            // exchange-specific options
            'options' => array(
                'fetchOrdersMethod' => 'tradingGetOrderHistory', // or 'tradingGetActiveOrders'
                'fetchClosedOrdersMethod' => 'tradingGetOrderHistory', // or 'tradingGetActiveOrders'
                'fetchTickersMethod' => 'publicGetTicker24hr',
                'defaultTimeInForce' => 'GTC', // 'GTC' = Good To Cancel (default), 'IOC' = Immediate Or Cancel
                'hasAlreadyAuthenticatedSuccessfully' => false,
                'warnOnFetchOpenOrdersWithoutSymbol' => true,
                'parseOrderToPrecision' => false, // force amounts and costs in parseOrder to precision
                'newOrderRespType' => 'RESULT', // 'ACK' for order id, 'RESULT' for full order or 'FULL' for order with fills
            ),
            'exceptions' => array(
                'exact' => array(
                    "Parameter 'filter' contains invalid value." => '\\ccxt\\BadRequest', // eslint-disable-quotes
                    "Mandatory parameter 'instrument' is missing." => '\\ccxt\\BadRequest', // eslint-disable-quotes
                    "The value of parameter 'till' must be greater than or equal to the value of parameter 'from'." => '\\ccxt\\BadRequest', // eslint-disable-quotes
                    'Failed to verify request signature.' => '\\ccxt\\AuthenticationError', // eslint-disable-quotes
                    "Nonce error. Make sure that the value passed in the 'X-CREX24-API-NONCE' header is greater in each consecutive request than in the previous one for the corresponding API-Key provided in 'X-CREX24-API-KEY' header." => '\\ccxt\\InvalidNonce',
                    'Market orders are not supported by the instrument currently.' => '\\ccxt\\InvalidOrder',
                    "Parameter 'instrument' contains invalid value." => '\\ccxt\\BadSymbol',
                ),
                'broad' => array(
                    'try again later' => '\\ccxt\\ExchangeNotAvailable', // array("errorDescription":"Failed to process the request. Please, try again later.")
                    'API Key' => '\\ccxt\\AuthenticationError', // "API Key '9edc48de-d5b0-4248-8e7e-f59ffcd1c7f1' doesn't exist."
                    'Insufficient funds' => '\\ccxt\\InsufficientFunds', // "Insufficient funds => new order requires 10 ETH which is more than the available balance."
                    'has been delisted.' => '\\ccxt\\BadSymbol', // array("errorDescription":"Instrument '$PAC-BTC' has been delisted.")
                    'is currently suspended.' => '\\ccxt\\BadSymbol', // array(is_array(BITG-BTC is currently suspended.") && array_key_exists("errorDescription":"Trading, BITG-BTC is currently suspended."))
                    'Mandatory parameter' => '\\ccxt\\BadRequest', // array("errorDescription":"Mandatory parameter 'feeCurrency' is missing.")
                ),
            ),
        ));
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetInstruments ($params);
        //
        //     [ array(              $symbol =>   "$PAC-BTC",
        //                baseCurrency =>   "$PAC",
        //               quoteCurrency =>   "BTC",
        //                 feeCurrency =>   "BTC",
        //                    $tickSize =>    1e-8,
        //                    $minPrice =>    1e-8,
        //                   minVolume =>    1,
        //         supportedOrderTypes => ["limit"],
        //                       state =>   "$active"    ),
        //       {              $symbol =>   "ZZC-USD",
        //                baseCurrency =>   "ZZC",
        //               quoteCurrency =>   "USD",
        //                 feeCurrency =>   "USD",
        //                    $tickSize =>    0.0001,
        //                    $minPrice =>    0.0001,
        //                   minVolume =>    1,
        //         supportedOrderTypes => ["limit"],
        //                       state =>   "$active"   }        ]
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'symbol');
            $baseId = $this->safe_string($market, 'baseCurrency');
            $quoteId = $this->safe_string($market, 'quoteCurrency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $tickSize = $this->safe_value($market, 'tickSize');
            $minPrice = $this->safe_value($market, 'minPrice');
            $minAmount = $this->safe_float($market, 'minVolume');
            $precision = array(
                'amount' => $this->precision_from_string($this->number_to_string($minAmount)),
                'price' => $this->precision_from_string($this->number_to_string($tickSize)),
            );
            $active = ($market['state'] === 'active');
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => $active,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $minAmount,
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => $minPrice,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     array( array(                   symbol => "$PAC",
        //                             name => "PACCoin",
        //                           isFiat =>  false,
        //                  depositsAllowed =>  true,
        //         depositConfirmationCount =>  8,
        //                       minDeposit =>  0,
        //               withdrawalsAllowed =>  true,
        //              withdrawalPrecision =>  8,
        //                    minWithdrawal =>  4,
        //                    maxWithdrawal =>  1000000000,
        //                flatWithdrawalFee =>  2,
        //                       isDelisted =>  false       ),
        //       {                   symbol => "ZZC",
        //                             name => "Zozo",
        //                           isFiat =>  false,
        //                  depositsAllowed =>  false,
        //         depositConfirmationCount =>  8,
        //                       minDeposit =>  0,
        //               withdrawalsAllowed =>  false,
        //              withdrawalPrecision =>  8,
        //                    minWithdrawal =>  0.2,
        //                    maxWithdrawal =>  1000000000,
        //                flatWithdrawalFee =>  0.1,
        //                       isDelisted =>  false       } )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'symbol');
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer($currency, 'withdrawalPrecision');
            $address = $this->safe_value($currency, 'BaseAddress');
            $active = ($currency['depositsAllowed'] && $currency['withdrawalsAllowed'] && !$currency['isDelisted']);
            $type = $currency['isFiat'] ? 'fiat' : 'crypto';
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'address' => $address,
                'info' => $currency,
                'type' => $type,
                'name' => $this->safe_string($currency, 'name'),
                'active' => $active,
                'fee' => $this->safe_float($currency, 'flatWithdrawalFee'), // todo => redesign
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'deposit' => array(
                        'min' => $this->safe_float($currency, 'minDeposit'),
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_float($currency, 'minWithdrawal'),
                        'max' => $this->safe_float($currency, 'maxWithdrawal'),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $request = array(
            // 'currency' => 'ETH', // comma-separated list of currency ids
            // 'nonZeroOnly' => 'false', // true by default
        );
        $response = $this->accountGetBalance (array_merge($request, $params));
        //
        //     array(
        //         {
        //           "currency" => "ETH",
        //           "available" => 0.0,
        //           "reserved" => 0.0
        //         }
        //     )
        //
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['used'] = $this->safe_float($balance, 'reserved');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = maximum = 100
        }
        $response = $this->publicGetOrderBook (array_merge($request, $params));
        //
        //     array(  buyLevels => array( { price => 0.03099, volume => 0.00610063 ),
        //                     array( price => 0.03097, volume => 1.33455158 ),
        //                     array( price => 0.03096, volume => 0.0830889 ),
        //                     array( price => 0.03095, volume => 0.0820356 ),
        //                     array( price => 0.03093, volume => 0.5499419 ),
        //                     array( price => 0.03092, volume => 0.23317494 ),
        //                     array( price => 0.03091, volume => 0.62105322 ),
        //                     array( price => 0.00620041, volume => 0.003 )    ),
        //       sellLevels => array( array( price => 0.03117, volume => 5.47492315 ),
        //                     array( price => 0.03118, volume => 1.97744139 ),
        //                     array( price => 0.03119, volume => 0.012 ),
        //                     array( price => 0.03121, volume => 0.741242 ),
        //                     array( price => 0.03122, volume => 0.96178089 ),
        //                     array( price => 0.03123, volume => 0.152326 ),
        //                     array( price => 0.03124, volume => 2.63462933 ),
        //                     array( price => 0.069, volume => 0.004 )            ) }
        //
        return $this->parse_order_book($response, null, 'buyLevels', 'sellLevels', 'price', 'volume');
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //       {    instrument => "ZZC-USD",
        //                  $last =>  0.065,
        //         percentChange =>  0,
        //                   low =>  0.065,
        //                  high =>  0.065,
        //            baseVolume =>  0,
        //           quoteVolume =>  0,
        //           volumeInBtc =>  0,
        //           volumeInUsd =>  0,
        //                   ask =>  0.5,
        //                   bid =>  0.0007,
        //             $timestamp => "2018-10-31T09:21:25Z" }   ]
        //
        $timestamp = $this->parse8601($this->safe_string($ticker, 'timestamp'));
        $marketId = $this->safe_string($ticker, 'instrument');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $last = $this->safe_float($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null, // previous day close
            'change' => null,
            'percentage' => $this->safe_float($ticker, 'percentChange'),
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'baseVolume'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument' => $market['id'],
        );
        $response = $this->publicGetTickers (array_merge($request, $params));
        //
        //     array( {    instrument => "$PAC-BTC",
        //                  last =>  3.3e-7,
        //         percentChange =>  3.125,
        //                   low =>  2.7e-7,
        //                  high =>  3.3e-7,
        //            baseVolume =>  191700.79823187,
        //           quoteVolume =>  0.0587930939346704,
        //           volumeInBtc =>  0.0587930939346704,
        //           volumeInUsd =>  376.2006339435353,
        //                   ask =>  3.3e-7,
        //                   bid =>  3.1e-7,
        //             timestamp => "2018-10-31T09:21:25Z" }   )
        //
        $numTickers = is_array($response) ? count($response) : 0;
        if ($numTickers < 1) {
            throw new ExchangeError($this->id . ' fetchTicker could not load quotes for $symbol ' . $symbol);
        }
        return $this->parse_ticker($response[0], $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbols !== null) {
            $ids = $this->market_ids($symbols);
            $request['instrument'] = implode(',', $ids);
        }
        $response = $this->publicGetTickers (array_merge($request, $params));
        //
        //     array( array(    instrument => "$PAC-BTC",
        //                  last =>  3.3e-7,
        //         percentChange =>  3.125,
        //                   low =>  2.7e-7,
        //                  high =>  3.3e-7,
        //            baseVolume =>  191700.79823187,
        //           quoteVolume =>  0.0587930939346704,
        //           volumeInBtc =>  0.0587930939346704,
        //           volumeInUsd =>  376.2006339435353,
        //                   ask =>  3.3e-7,
        //                   bid =>  3.1e-7,
        //             timestamp => "2018-10-31T09:21:25Z" ),
        //       {    instrument => "ZZC-USD",
        //                  last =>  0.065,
        //         percentChange =>  0,
        //                   low =>  0.065,
        //                  high =>  0.065,
        //            baseVolume =>  0,
        //           quoteVolume =>  0,
        //           volumeInBtc =>  0,
        //           volumeInUsd =>  0,
        //                   ask =>  0.5,
        //                   bid =>  0.0007,
        //             timestamp => "2018-10-31T09:21:25Z" }   )
        //
        return $this->parse_tickers($response, $symbols);
    }

    public function parse_trade($trade, $market = null) {
        //
        // public fetchTrades
        //
        //       {     $price =>  0.03105,
        //            volume =>  0.11,
        //              $side => "sell",
        //         $timestamp => "2018-10-31T04:19:35Z" }  ]
        //
        // private fetchMyTrades
        //
        //     {
        //         "$id" => 3005866,
        //         "$orderId" => 468533093,
        //         "$timestamp" => "2018-06-02T16:26:27Z",
        //         "instrument" => "BCH-ETH",
        //         "$side" => "buy",
        //         "$price" => 1.78882,
        //         "volume" => 0.027,
        //         "$fee" => 0.0000483,
        //         "feeCurrency" => "ETH"
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string($trade, 'timestamp'));
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'volume');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        $id = $this->safe_string($trade, 'id');
        $side = $this->safe_string($trade, 'side');
        $orderId = $this->safe_string($trade, 'orderId');
        $marketId = $this->safe_string($trade, 'instrument');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $fee = null;
        $feeCurrencyId = $this->safe_string($trade, 'feeCurrency');
        $feeCode = $this->safe_currency_code($feeCurrencyId);
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCode,
            );
        }
        $takerOrMaker = null;
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $orderId,
            'type' => null,
            'takerOrMaker' => $takerOrMaker,
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // min 1, max 1000, default 100
        }
        $response = $this->publicGetRecentTrades (array_merge($request, $params));
        //
        //     array( array(     price =>  0.03117,
        //            volume =>  0.02597403,
        //              side => "buy",
        //         timestamp => "2018-10-31T09:37:46Z" ),
        //       {     price =>  0.03105,
        //            volume =>  0.11,
        //              side => "sell",
        //         timestamp => "2018-10-31T04:19:35Z" }  )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         timestamp => '2019-09-21T10:36:00Z',
        //         open => 0.02152,
        //         high => 0.02156,
        //         low => 0.02152,
        //         close => 0.02156,
        //         volume => 0.01741259
        //     }
        //
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'timestamp')),
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
        $request = array(
            'granularity' => $this->timeframes[$timeframe],
            'instrument' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // Accepted values => 1 - 1000. If the parameter is not specified, the number of results is limited to 100
        }
        $response = $this->publicGetOhlcv (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "timestamp" => "2020-06-06T17:36:00Z",
        //             "open" => 0.025,
        //             "high" => 0.025,
        //             "low" => 0.02499,
        //             "close" => 0.02499,
        //             "volume" => 0.00643127
        //         }
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'submitting' => 'open', // A newly created limit order has a $status "submitting" until it has been processed.
            // This $status changes during the lifetime of an order and can have different values depending on the value of the parameter Time In Force.
            'unfilledActive' => 'open', // order is active, no trades have been made
            'partiallyFilledActive' => 'open', // part of the order has been filled, the other part is active
            'filled' => 'closed', // order has been filled entirely
            'partiallyFilledCancelled' => 'canceled', // part of the order has been filled, the other part has been cancelled either by the trader or by the system (see the value of cancellationReason of an Order for more details on the reason of cancellation)
            'unfilledCancelled' => 'canceled', // order has been cancelled, no trades have taken place (see the value of cancellationReason of an Order for more details on the reason of cancellation)
        );
        return (is_array($statuses) && array_key_exists($status, $statuses)) ? $statuses[$status] : $status;
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "$id" => 469594855,
        //         "$timestamp" => "2018-06-08T16:59:44Z",
        //         "instrument" => "BTS-BTC",
        //         "$side" => "buy",
        //         "$type" => "limit",
        //         "$status" => "submitting",
        //         "cancellationReason" => null,
        //         "$timeInForce" => "GTC",
        //         "volume" => 4.0,
        //         "$price" => 0.000025,
        //         "$stopPrice" => null,
        //         "remainingVolume" => 4.0,
        //         "lastUpdate" => null,
        //         "parentOrderId" => null,
        //         "childOrderId" => null
        //     }
        //
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'instrument');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $timestamp = $this->parse8601($this->safe_string($order, 'timestamp'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'volume');
        $remaining = $this->safe_float($order, 'remainingVolume');
        $lastTradeTimestamp = $this->parse8601($this->safe_string($order, 'lastUpdate'));
        $id = $this->safe_string($order, 'id');
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side');
        $fee = null;
        $trades = null;
        $average = null;
        $timeInForce = $this->safe_string($order, 'timeInForce');
        $stopPrice = $this->safe_float($order, 'stopPrice');
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => $timeInForce,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'amount' => $amount,
            'cost' => null,
            'average' => $average,
            'filled' => null,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
        ));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'instrument' => $market['id'],
            'volume' => $this->amount_to_precision($symbol, $amount),
            // The value must comply with the list of order types supported by the instrument (see the value of parameter supportedOrderTypes of the Instrument)
            // If the parameter is not specified, the default value "limit" is used
            // More about order types in the corresponding section of documentation
            'type' => $type, // 'limit', 'market', 'stopLimit', in fact as of 2018-10-31, only 'limit' orders are supported for all markets
            'side' => $side, // 'buy' or 'sell'
            // "GTC" - Good-Til-Cancelled
            // "IOC" - Immediate-Or-Cancel (currently not supported by the exchange API, reserved for future use)
            // "FOK" - Fill-Or-Kill (currently not supported by the exchange API, reserved for future use)
            // 'timeInForce' => 'GTC', // IOC', 'FOK'
            // 'strictValidation' => false, // false - prices will be rounded to meet the requirement, true - execution of the method will be aborted and an error message will be returned
        );
        $priceIsRequired = false;
        $stopPriceIsRequired = false;
        if ($type === 'limit') {
            $priceIsRequired = true;
        } else if ($type === 'stopLimit') {
            $priceIsRequired = true;
            $stopPriceIsRequired = true;
        }
        if ($priceIsRequired) {
            if ($price === null) {
                throw new InvalidOrder($this->id . ' createOrder() requires a $price argument for a ' . $type . ' order');
            }
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        if ($stopPriceIsRequired) {
            $stopPrice = $this->safe_float($params, 'stopPrice');
            if ($stopPrice === null) {
                throw new InvalidOrder($this->id . ' createOrder() requires a $stopPrice extra param for a ' . $type . ' order');
            } else {
                $request['stopPrice'] = $this->price_to_precision($symbol, $stopPrice);
            }
            $params = $this->omit($params, 'stopPrice');
        }
        $response = $this->tradingPostPlaceOrder (array_merge($request, $params));
        //
        //     {
        //         "id" => 469594855,
        //         "timestamp" => "2018-06-08T16:59:44Z",
        //         "instrument" => "BTS-BTC",
        //         "$side" => "buy",
        //         "$type" => "limit",
        //         "status" => "submitting",
        //         "cancellationReason" => null,
        //         "timeInForce" => "GTC",
        //         "volume" => 4.0,
        //         "$price" => 0.000025,
        //         "$stopPrice" => null,
        //         "remainingVolume" => 4.0,
        //         "lastUpdate" => null,
        //         "parentOrderId" => null,
        //         "childOrderId" => null
        //     }
        //
        return $this->parse_order($response, $market);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->tradingGetOrderStatus (array_merge($request, $params));
        //
        //     array(
        //         {
        //           "$id" => 466747915,
        //           "timestamp" => "2018-05-26T06:43:49Z",
        //           "instrument" => "UNI-BTC",
        //           "side" => "sell",
        //           "type" => "limit",
        //           "status" => "partiallyFilledActive",
        //           "cancellationReason" => null,
        //           "timeInForce" => "GTC",
        //           "volume" => 5700.0,
        //           "price" => 0.000005,
        //           "stopPrice" => null,
        //           "remainingVolume" => 1.948051948052,
        //           "lastUpdate" => null,
        //           "parentOrderId" => null,
        //           "childOrderId" => null
        //         }
        //     )
        //
        $numOrders = is_array($response) ? count($response) : 0;
        if ($numOrders < 1) {
            throw new OrderNotFound($this->id . ' fetchOrder could not fetch order $id ' . $id);
        }
        return $this->parse_order($response[0]);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($since !== null) {
            $request['from'] = $this->ymdhms($since, 'T');
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['instrument'] = $market['id'];
        }
        $method = $this->safe_string($this->options, 'fetchOrdersMethod', 'tradingGetOrderHistory');
        $response = $this->$method (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "id" => 468535711,
        //             "timestamp" => "2018-06-02T16:42:40Z",
        //             "instrument" => "BTC-EUR",
        //             "side" => "sell",
        //             "type" => "$limit",
        //             "status" => "submitting",
        //             "cancellationReason" => null,
        //             "timeInForce" => "GTC",
        //             "volume" => 0.00770733,
        //             "price" => 6724.9,
        //             "stopPrice" => null,
        //             "remainingVolume" => 0.00770733,
        //             "lastUpdate" => "2018-06-02T16:42:40Z",
        //             "parentOrderId" => null,
        //             "childOrderId" => null
        //         }
        //     )
        //
        return $this->parse_orders($response);
    }

    public function fetch_orders_by_ids($ids = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => implode(',', $ids),
        );
        $response = $this->tradingGetOrderStatus (array_merge($request, $params));
        //
        //     array(
        //         {
        //           "id" => 466747915,
        //           "timestamp" => "2018-05-26T06:43:49Z",
        //           "instrument" => "UNI-BTC",
        //           "side" => "sell",
        //           "type" => "$limit",
        //           "status" => "partiallyFilledActive",
        //           "cancellationReason" => null,
        //           "timeInForce" => "GTC",
        //           "volume" => 5700.0,
        //           "price" => 0.000005,
        //           "stopPrice" => null,
        //           "remainingVolume" => 1.948051948052,
        //           "lastUpdate" => null,
        //           "parentOrderId" => null,
        //           "childOrderId" => null
        //         }
        //     )
        //
        return $this->parse_orders($response, null, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['instrument'] = $market['id'];
        }
        $response = $this->tradingGetActiveOrders (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             "id" => 466747915,
        //             "timestamp" => "2018-05-26T06:43:49Z",
        //             "instrument" => "UNI-BTC",
        //             "side" => "sell",
        //             "type" => "$limit",
        //             "status" => "partiallyFilledActive",
        //             "cancellationReason" => null,
        //             "timeInForce" => "GTC",
        //             "volume" => 5700.0,
        //             "price" => 0.000005,
        //             "stopPrice" => null,
        //             "remainingVolume" => 1.948051948052,
        //             "lastUpdate" => null,
        //             "parentOrderId" => null,
        //             "childOrderId" => null
        //         ),
        //         array(
        //             "id" => 466748077,
        //             "timestamp" => "2018-05-26T06:45:29Z",
        //             "instrument" => "PRJ-BTC",
        //             "side" => "sell",
        //             "type" => "$limit",
        //             "status" => "partiallyFilledActive",
        //             "cancellationReason" => null,
        //             "timeInForce" => "GTC",
        //             "volume" => 10000.0,
        //             "price" => 0.0000007,
        //             "stopPrice" => null,
        //             "remainingVolume" => 9975.0,
        //             "lastUpdate" => null,
        //             "parentOrderId" => null,
        //             "childOrderId" => null
        //         ),
        //         ...
        //     )
        //
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['instrument'] = $market['id'];
        }
        if ($since !== null) {
            $request['from'] = $this->ymdhms($since, 'T');
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // min 1, max 1000, default 100
        }
        $method = $this->safe_string($this->options, 'fetchClosedOrdersMethod', 'tradingGetOrderHistory');
        $response = $this->$method (array_merge($request, $params));
        //     array(
        //         array(
        //             "id" => 468535711,
        //             "timestamp" => "2018-06-02T16:42:40Z",
        //             "instrument" => "BTC-EUR",
        //             "side" => "sell",
        //             "type" => "$limit",
        //             "status" => "submitting",
        //             "cancellationReason" => null,
        //             "timeInForce" => "GTC",
        //             "volume" => 0.00770733,
        //             "price" => 6724.9,
        //             "stopPrice" => null,
        //             "remainingVolume" => 0.00770733,
        //             "lastUpdate" => null,
        //             "parentOrderId" => null,
        //             "childOrderId" => null
        //         ),
        //         array(
        //             "id" => 468535707,
        //             "timestamp" => "2018-06-02T16:42:37Z",
        //             "instrument" => "BTG-BTC",
        //             "side" => "buy",
        //             "type" => "$limit",
        //             "status" => "unfilledActive",
        //             "cancellationReason" => null,
        //             "timeInForce" => "GTC",
        //             "volume" => 0.0173737,
        //             "price" => 0.00589027,
        //             "stopPrice" => null,
        //             "remainingVolume" => 0.0173737,
        //             "lastUpdate" => null,
        //             "parentOrderId" => null,
        //             "childOrderId" => null
        //         ),
        //         ...
        //     )
        //
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'ids' => array(
                intval($id),
            ),
        );
        $response = $this->tradingPostCancelOrdersById (array_merge($request, $params));
        //
        //     array(
        //         465448358,
        //         468364313
        //     )
        //
        return $this->parse_order($response);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $response = $this->tradingPostCancelAllOrders ($params);
        //
        //     array(
        //         465448358,
        //         468364313
        //     )
        //
        return $response;
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['instrument'] = $market['id'];
        }
        if ($since !== null) {
            $request['from'] = $this->ymdhms($since, 'T');
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // min 1, max 1000, default 100
        }
        $response = $this->tradingGetTradeHistory (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             "id" => 3005866,
        //             "orderId" => 468533093,
        //             "timestamp" => "2018-06-02T16:26:27Z",
        //             "instrument" => "BCH-ETH",
        //             "side" => "buy",
        //             "price" => 1.78882,
        //             "volume" => 0.027,
        //             "fee" => 0.0000483,
        //             "feeCurrency" => "ETH"
        //         ),
        //         array(
        //             "id" => 3005812,
        //             "orderId" => 468515771,
        //             "timestamp" => "2018-06-02T16:16:05Z",
        //             "instrument" => "ETC-BTC",
        //             "side" => "sell",
        //             "price" => 0.00210958,
        //             "volume" => 0.05994006,
        //             "fee" => -0.000000063224,
        //             "feeCurrency" => "BTC"
        //         ),
        //         ...
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array();
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($since !== null) {
            $request['from'] = $this->ymd($since, 'T');
        }
        $response = $this->accountGetMoneyTransfers (array_merge($request, $params));
        //
        //     array(
        //         array(
        //           "id" => 756446,
        //           "type" => "deposit",
        //           "$currency" => "ETH",
        //           "address" => "0x451d5a1b7519aa75164f440df78c74aac96023fe",
        //           "paymentId" => null,
        //           "amount" => 0.142,
        //           "fee" => null,
        //           "txId" => "0x2b49098749840a9482c4894be94f94864b498a1306b6874687a5640cc9871918",
        //           "createdAt" => "2018-06-02T19:30:28Z",
        //           "processedAt" => "2018-06-02T21:10:41Z",
        //           "confirmationsRequired" => 12,
        //           "confirmationCount" => 12,
        //           "status" => "success",
        //           "errorDescription" => null
        //         ),
        //         array(
        //           "id" => 754618,
        //           "type" => "deposit",
        //           "$currency" => "BTC",
        //           "address" => "1IgNfmERVcier4IhfGEfutkLfu4AcmeiUC",
        //           "paymentId" => null,
        //           "amount" => 0.09,
        //           "fee" => null,
        //           "txId" => "6876541687a9187e987c9187654f7198b9718af974641687b19a87987f91874f",
        //           "createdAt" => "2018-06-02T16:19:44Z",
        //           "processedAt" => "2018-06-02T16:20:50Z",
        //           "confirmationsRequired" => 1,
        //           "confirmationCount" => 1,
        //           "status" => "success",
        //           "errorDescription" => null
        //         ),
        //         ...
        //     )
        //
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'type' => 'deposit',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'type' => 'withdrawal',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'pending' => 'pending', // transfer is in progress
            'success' => 'ok', // completed successfully
            'failed' => 'failed', // aborted at some point (money will be credited back to the account of origin)
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //     {
        //         "$id" => 756446,
        //         "$type" => "deposit",
        //         "$currency" => "ETH",
        //         "$address" => "0x451d5a1b7519aa75164f440df78c74aac96023fe",
        //         "paymentId" => null,
        //         "$amount" => 0.142,
        //         "$fee" => null,
        //         "txId" => "0x2b49098749840a9482c4894be94f94864b498a1306b6874687a5640cc9871918",
        //         "createdAt" => "2018-06-02T19:30:28Z",
        //         "processedAt" => "2018-06-02T21:10:41Z",
        //         "confirmationsRequired" => 12,
        //         "confirmationCount" => 12,
        //         "$status" => "success",
        //         "errorDescription" => null,
        //     }
        //
        $id = $this->safe_string($transaction, 'id');
        $address = $this->safe_string($transaction, 'address');
        $tag = $this->safe_string($transaction, 'paymentId');
        $txid = $this->safe_value($transaction, 'txId');
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $type = $this->safe_string($transaction, 'type');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'createdAt'));
        $updated = $this->parse8601($this->safe_string($transaction, 'processedAt'));
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $amount = $this->safe_float($transaction, 'amount');
        $feeCost = $this->safe_float($transaction, 'fee');
        $fee = array(
            'cost' => $feeCost,
            'currency' => $code,
        );
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'tag' => $tag,
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
            'currency' => $currency['id'],
        );
        $response = $this->accountGetDepositAddress (array_merge($request, $params));
        //
        //     {
        //         "$currency" => "BTS",
        //         "$address" => "crex24",
        //         "paymentId" => "0fg4da4186741579"
        //     }
        //
        $address = $this->safe_string($response, 'address');
        $tag = $this->safe_string($response, 'paymentId');
        return array(
            'currency' => $code,
            'address' => $this->check_address($address),
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
            'address' => $address,
            'amount' => floatval($this->currency_to_precision($code, $amount)),
            // sets whether the specified $amount includes fee, can have either of the two values
            // true - balance will be decreased by $amount, whereas [$amount - fee] will be transferred to the specified $address
            // false - $amount will be deposited to the specified $address, whereas the balance will be decreased by [$amount . fee]
            // 'includeFee' => false, // the default value is false
            'feeCurrency' => $currency['id'], // https://github.com/ccxt/ccxt/issues/7544
        );
        if ($tag !== null) {
            $request['paymentId'] = $tag;
        }
        $response = $this->accountPostWithdraw (array_merge($request, $params));
        return $this->parse_transaction($response);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/' . $this->version . '/' . $api . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($method === 'GET') {
            if ($query) {
                $request .= '?' . $this->urlencode($query);
            }
        }
        $url = $this->urls['api'] . $request;
        if (($api === 'trading') || ($api === 'account')) {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $secret = base64_decode($this->secret);
            $auth = $request . $nonce;
            $headers = array(
                'X-CREX24-API-KEY' => $this->apiKey,
                'X-CREX24-API-NONCE' => $nonce,
            );
            if ($method === 'POST') {
                $headers['Content-Type'] = 'application/json';
                $body = $this->json($params);
                $auth .= $body;
            }
            $headers['X-CREX24-API-SIGN'] = $this->hmac($this->encode($auth), $secret, 'sha512', 'base64');
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (!$this->is_json_encoded_object($body)) {
            return; // fallback to default error handler
        }
        if (($code >= 200) && ($code < 300)) {
            return; // no error
        }
        $message = $this->safe_string($response, 'errorDescription');
        $feedback = $this->id . ' ' . $body;
        $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
        $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
        if ($code === 400) {
            throw new BadRequest($feedback);
        } else if ($code === 401) {
            throw new AuthenticationError($feedback);
        } else if ($code === 403) {
            throw new AuthenticationError($feedback);
        } else if ($code === 429) {
            throw new DDoSProtection($feedback);
        } else if ($code === 500) {
            throw new ExchangeError($feedback);
        } else if ($code === 503) {
            throw new ExchangeNotAvailable($feedback);
        } else if ($code === 504) {
            throw new RequestTimeout($feedback);
        }
        throw new ExchangeError($feedback); // unknown message
    }
}

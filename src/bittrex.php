<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\BadRequest;
use \ccxt\InvalidAddress;
use \ccxt\AddressPending;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;
use \ccxt\DDoSProtection;

class bittrex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bittrex',
            'name' => 'Bittrex',
            'countries' => array( 'US' ),
            'version' => 'v3',
            'rateLimit' => 1500,
            'certified' => true,
            'pro' => true,
            // new metainfo interface
            'has' => array(
                'CORS' => false,
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'createDepositAddress' => true,
                'createMarketOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchDeposits' => true,
                'fetchDepositAddress' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => 'emulated',
                'fetchOHLCV' => true,
                'fetchOrder' => true,
                'fetchOrderTrades' => true,
                'fetchOrderBook' => true,
                'fetchOpenOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => 'MINUTE_1',
                '5m' => 'MINUTE_5',
                '1h' => 'HOUR_1',
                '1d' => 'DAY_1',
            ),
            'hostname' => 'bittrex.com',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87153921-edf53180-c2c0-11ea-96b9-f2a9a95a455b.jpg',
                'api' => array(
                    'public' => 'https://api.bittrex.com',
                    'private' => 'https://api.bittrex.com',
                ),
                'www' => 'https://bittrex.com',
                'doc' => array(
                    'https://bittrex.github.io/api/v3',
                ),
                'fees' => array(
                    'https://bittrex.zendesk.com/hc/en-us/articles/115003684371-BITTREX-SERVICE-FEES-AND-WITHDRAWAL-LIMITATIONS',
                    'https://bittrex.zendesk.com/hc/en-us/articles/115000199651-What-fees-does-Bittrex-charge-',
                ),
                'referral' => 'https://bittrex.com/Account/Register?referralCode=1ZE-G0G-M3B',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ping',
                        'currencies',
                        'currencies/{symbol}',
                        'markets',
                        'markets/tickers',
                        'markets/summaries',
                        'markets/{marketSymbol}',
                        'markets/{marketSymbol}/summary',
                        'markets/{marketSymbol}/orderbook',
                        'markets/{marketSymbol}/trades',
                        'markets/{marketSymbol}/ticker',
                        'markets/{marketSymbol}/candles/{candleInterval}/recent',
                        'markets/{marketSymbol}/candles/{candleInterval}/historical/{year}/{month}/{day}',
                        'markets/{marketSymbol}/candles/{candleInterval}/historical/{year}/{month}',
                        'markets/{marketSymbol}/candles/{candleInterval}/historical/{year}',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'account',
                        'account/volume',
                        'addresses',
                        'addresses/{currencySymbol}',
                        'balances',
                        'balances/{currencySymbol}',
                        'deposits/open',
                        'deposits/closed',
                        'deposits/ByTxId/{txId}',
                        'deposits/{depositId}',
                        'orders/closed',
                        'orders/open',
                        'orders/{orderId}',
                        'orders/{orderId}/executions',
                        'ping',
                        'subaccounts/{subaccountId}',
                        'subaccounts',
                        'withdrawals/open',
                        'withdrawals/closed',
                        'withdrawals/ByTxId/{txId}',
                        'withdrawals/{withdrawalId}',
                        'withdrawals/whitelistAddresses',
                        'conditional-orders/{conditionalOrderId}',
                        'conditional-orders/closed',
                        'conditional-orders/open',
                        'transfers/sent',
                        'transfers/received',
                        'transfers/{transferId}',
                    ),
                    'post' => array(
                        'addresses',
                        'orders',
                        'subaccounts',
                        'withdrawals',
                        'conditional-orders',
                        'transfers',
                    ),
                    'delete' => array(
                        'orders/open',
                        'orders/{orderId}',
                        'withdrawals/{withdrawalId}',
                        'conditional-orders/{conditionalOrderId}',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'maker' => 0.0035,
                    'taker' => 0.0035,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'BAD_REQUEST' => '\\ccxt\\BadRequest', // array("code":"BAD_REQUEST","detail":"Refer to the data field for specific field validation failures.","data":array("invalidRequestParameter":"day"))
                    'STARTDATE_OUT_OF_RANGE' => '\\ccxt\\BadRequest', // array("code":"STARTDATE_OUT_OF_RANGE")
                    // 'Call to Cancel was throttled. Try again in 60 seconds.' => '\\ccxt\\DDoSProtection',
                    // 'Call to GetBalances was throttled. Try again in 60 seconds.' => '\\ccxt\\DDoSProtection',
                    'APISIGN_NOT_PROVIDED' => '\\ccxt\\AuthenticationError',
                    'INVALID_SIGNATURE' => '\\ccxt\\AuthenticationError',
                    'INVALID_CURRENCY' => '\\ccxt\\ExchangeError',
                    'INVALID_PERMISSION' => '\\ccxt\\AuthenticationError',
                    'INSUFFICIENT_FUNDS' => '\\ccxt\\InsufficientFunds',
                    'INVALID_CEILING_MARKET_BUY' => '\\ccxt\\InvalidOrder',
                    'INVALID_FIAT_ACCOUNT' => '\\ccxt\\InvalidOrder',
                    'INVALID_ORDER_TYPE' => '\\ccxt\\InvalidOrder',
                    'QUANTITY_NOT_PROVIDED' => '\\ccxt\\InvalidOrder',
                    'MIN_TRADE_REQUIREMENT_NOT_MET' => '\\ccxt\\InvalidOrder',
                    'ORDER_NOT_OPEN' => '\\ccxt\\OrderNotFound',
                    'INVALID_ORDER' => '\\ccxt\\InvalidOrder',
                    'UUID_INVALID' => '\\ccxt\\OrderNotFound',
                    'RATE_NOT_PROVIDED' => '\\ccxt\\InvalidOrder', // createLimitBuyOrder ('ETH/BTC', 1, 0)
                    'INVALID_MARKET' => '\\ccxt\\BadSymbol', // array("success":false,"message":"INVALID_MARKET","result":null,"explanation":null)
                    'WHITELIST_VIOLATION_IP' => '\\ccxt\\PermissionDenied',
                    'DUST_TRADE_DISALLOWED_MIN_VALUE' => '\\ccxt\\InvalidOrder',
                    'RESTRICTED_MARKET' => '\\ccxt\\BadSymbol',
                    'We are down for scheduled maintenance, but we\u2019ll be back up shortly.' => '\\ccxt\\OnMaintenance', // array("success":false,"message":"We are down for scheduled maintenance, but we\u2019ll be back up shortly.","result":null,"explanation":null)
                ),
                'broad' => array(
                    'throttled' => '\\ccxt\\DDoSProtection',
                    'problem' => '\\ccxt\\ExchangeNotAvailable',
                ),
            ),
            'options' => array(
                'fetchTicker' => array(
                    'method' => 'publicGetMarketsMarketSymbolTicker', // publicGetMarketsMarketSymbolSummary
                ),
                'fetchTickers' => array(
                    'method' => 'publicGetMarketsTickers', // publicGetMarketsSummaries
                ),
                'parseOrderStatus' => false,
                'hasAlreadyAuthenticatedSuccessfully' => false, // a workaround for APIKEY_INVALID
                // With certain currencies, like
                // AEON, BTS, GXS, NXT, SBD, STEEM, STR, XEM, XLM, XMR, XRP
                // an additional tag / memo / payment id is usually required by exchanges.
                // With Bittrex some currencies imply the "base address . tag" logic.
                // The base address for depositing is stored on $this->currencies[code]
                // The base address identifies the exchange as the recipient
                // while the tag identifies the user account within the exchange
                // and the tag is retrieved with fetchDepositAddress.
                'tag' => array(
                    'NXT' => true, // NXT, BURST
                    'CRYPTO_NOTE_PAYMENTID' => true, // AEON, XMR
                    'BITSHAREX' => true, // BTS
                    'RIPPLE' => true, // XRP
                    'NEM' => true, // XEM
                    'STELLAR' => true, // XLM
                    'STEEM' => true, // SBD, GOLOS
                    // https://github.com/ccxt/ccxt/issues/4794
                    // 'LISK' => true, // LSK
                ),
                'subaccountId' => null,
                // see the implementation of fetchClosedOrdersV3 below
                // 'fetchClosedOrdersMethod' => 'fetch_closed_orders_v3',
                'fetchClosedOrdersFilterBySince' => true,
                // 'createOrderMethod' => 'create_order_v1',
            ),
            'commonCurrencies' => array(
                'REPV2' => 'REP',
            ),
        ));
    }

    public function cost_to_precision($symbol, $cost) {
        return $this->decimal_to_precision($cost, TRUNCATE, $this->markets[$symbol]['precision']['price'], DECIMAL_PLACES);
    }

    public function fee_to_precision($symbol, $fee) {
        return $this->decimal_to_precision($fee, TRUNCATE, $this->markets[$symbol]['precision']['price'], DECIMAL_PLACES);
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetMarkets ($params);
        //
        //     array(
        //         array(
        //             "$symbol":"LTC-BTC",
        //             "baseCurrencySymbol":"LTC",
        //             "quoteCurrencySymbol":"BTC",
        //             "minTradeSize":"0.01686767",
        //             "$precision":8,
        //             "$status":"ONLINE", // "OFFLINE"
        //             "createdAt":"2014-02-13T00:00:00Z"
        //         ),
        //         {
        //             "$symbol":"VDX-USDT",
        //             "baseCurrencySymbol":"VDX",
        //             "quoteCurrencySymbol":"USDT",
        //             "minTradeSize":"300.00000000",
        //             "$precision":8,
        //             "$status":"ONLINE", // "OFFLINE"
        //             "createdAt":"2019-05-23T00:41:21.843Z",
        //             "notice":"USDT has swapped to an ERC20-based token as of August 5, 2019."
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $baseId = $this->safe_string($market, 'baseCurrencySymbol');
            $quoteId = $this->safe_string($market, 'quoteCurrencySymbol');
            $id = $this->safe_string($market, 'symbol');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $pricePrecision = $this->safe_integer($market, 'precision', 8);
            $precision = array(
                'amount' => 8,
                'price' => $pricePrecision,
            );
            $status = $this->safe_string($market, 'status');
            $active = ($status === 'ONLINE');
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'info' => $market,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'minTradeSize'),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => 1 / pow(10, $precision['price']),
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

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balances = $this->privateGetBalances ($params);
        $result = array( 'info' => $balances );
        $indexed = $this->index_by($balances, 'currencySymbol');
        $currencyIds = is_array($indexed) ? array_keys($indexed) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $balance = $indexed[$currencyId];
            $account['free'] = $this->safe_float($balance, 'available');
            $account['total'] = $this->safe_float($balance, 'total');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'marketSymbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            if (($limit !== 1) && ($limit !== 25) && ($limit !== 500)) {
                throw new BadRequest($this->id . ' fetchOrderBook() $limit argument must be null, 1, 25 or 500, default is 25');
            }
            $request['depth'] = $limit;
        }
        $response = $this->publicGetMarketsMarketSymbolOrderbook (array_merge($request, $params));
        //
        //     {
        //         "bid":array(
        //             array("quantity":"0.01250000","rate":"10718.56200003"),
        //             array("quantity":"0.10000000","rate":"10718.56200002"),
        //             array("quantity":"0.39648292","rate":"10718.56200001"),
        //         ),
        //         "ask":array(
        //             array("quantity":"0.05100000","rate":"10724.30099631"),
        //             array("quantity":"0.10000000","rate":"10724.30099632"),
        //             array("quantity":"0.26000000","rate":"10724.30099634"),
        //         )
        //     }
        //
        $sequence = $this->safe_integer($this->last_response_headers, 'Sequence');
        $orderbook = $this->parse_order_book($response, null, 'bid', 'ask', 'rate', 'quantity');
        $orderbook['nonce'] = $sequence;
        return $orderbook;
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     array(
        //         {
        //             "symbol":"1ST",
        //             "name":"Firstblood",
        //             "coinType":"ETH_CONTRACT",
        //             "status":"ONLINE",
        //             "minConfirmations":36,
        //             "notice":"",
        //             "txFee":"4.50000000",
        //             "logoUrl":"https://bittrexblobstorage.blob.core.windows.net/public/5685a7be-1edf-4ba0-a313-b5309bb204f8.png",
        //             "prohibitedIn":array(),
        //             "baseAddress":"0xfbb1b73c4f0bda4f67dca266ce6ef42f520fbb98",
        //             "associatedTermsOfService":array()
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'symbol');
            $code = $this->safe_currency_code($id);
            $precision = 8; // default $precision, todo => fix "magic constants"
            $fee = $this->safe_float($currency, 'txFee'); // todo => redesign
            $isActive = $this->safe_string($currency, 'status');
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'address' => $this->safe_string($currency, 'baseAddress'),
                'info' => $currency,
                'type' => $this->safe_string($currency, 'coinType'),
                'name' => $this->safe_string($currency, 'name'),
                'active' => ($isActive === 'ONLINE'),
                'fee' => $fee,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => 1 / pow(10, $precision),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => 1 / pow(10, $precision),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $fee,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // $ticker
        //
        //     {
        //         "$symbol":"ETH-BTC",
        //         "lastTradeRate":"0.03284496",
        //         "bidRate":"0.03284523",
        //         "askRate":"0.03286857"
        //     }
        //
        // summary
        //
        //     {
        //         "$symbol":"ETH-BTC",
        //         "high":"0.03369528",
        //         "low":"0.03282442",
        //         "volume":"4307.83794556",
        //         "quoteVolume":"143.08608869",
        //         "percentChange":"0.79",
        //         "updatedAt":"2020-09-29T07:36:57.823Z"
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string($ticker, 'updatedAt'));
        $marketId = $this->safe_string($ticker, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $percentage = $this->safe_float($ticker, 'percentChange');
        $last = $this->safe_float($ticker, 'lastTradeRate');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bidRate'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'askRate'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $options = $this->safe_value($this->options, 'fetchTickers', array());
        $defaultMethod = $this->safe_string($options, 'method', 'publicGetMarketsTickers');
        $method = $this->safe_string($params, 'method', $defaultMethod);
        $params = $this->omit($params, 'method');
        $response = $this->$method ($params);
        //
        // publicGetMarketsTickers
        //
        //     array(
        //         {
        //             "symbol":"4ART-BTC",
        //             "lastTradeRate":"0.00000210",
        //             "bidRate":"0.00000210",
        //             "askRate":"0.00000215"
        //         }
        //     )
        //
        // publicGetMarketsSummaries
        //
        //     array(
        //         {
        //             "symbol":"4ART-BTC",
        //             "high":"0.00000206",
        //             "low":"0.00000196",
        //             "volume":"14871.32000233",
        //             "quoteVolume":"0.02932756",
        //             "percentChange":"1.48",
        //             "updatedAt":"2020-09-29T07:34:32.757Z"
        //         }
        //     )
        //
        $tickers = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $tickers[] = $ticker;
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'marketSymbol' => $market['id'],
        );
        $options = $this->safe_value($this->options, 'fetchTicker', array());
        $defaultMethod = $this->safe_string($options, 'method', 'publicGetMarketsMarketSymbolTicker');
        $method = $this->safe_string($params, 'method', $defaultMethod);
        $params = $this->omit($params, 'method');
        $response = $this->$method (array_merge($request, $params));
        //
        // publicGetMarketsMarketSymbolTicker
        //
        //     {
        //         "$symbol":"ETH-BTC",
        //         "lastTradeRate":"0.03284496",
        //         "bidRate":"0.03284523",
        //         "askRate":"0.03286857"
        //     }
        //
        //
        // publicGetMarketsMarketSymbolSummary
        //
        //     {
        //         "$symbol":"ETH-BTC",
        //         "high":"0.03369528",
        //         "low":"0.03282442",
        //         "volume":"4307.83794556",
        //         "quoteVolume":"143.08608869",
        //         "percentChange":"0.79",
        //         "updatedAt":"2020-09-29T07:36:57.823Z"
        //     }
        //
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market = null) {
        //
        // public fetchTrades
        //
        //     {
        //         "$id":"9c5589db-42fb-436c-b105-5e2edcb95673",
        //         "executedAt":"2020-10-03T11:48:43.38Z",
        //         "quantity":"0.17939626",
        //         "rate":"0.03297952",
        //         "takerSide":"BUY"
        //     }
        //
        // private fetchOrderTrades
        //
        //     {
        //         "$id" => "aaa3e9bd-5b86-4a21-8b3d-1275c1d30b8e",
        //         "marketSymbol" => "OMG-BTC",
        //         "executedAt" => "2020-10-02T16:00:30.3Z",
        //         "quantity" => "7.52710000",
        //         "rate" => "0.00034907",
        //         "orderId" => "3a3dbd33-3a30-4ae5-a41d-68d3c1ac537e",
        //         "commission" => "0.00000525",
        //         "$isTaker" => false
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string($trade, 'executedAt'));
        $id = $this->safe_string($trade, 'id');
        $order = $this->safe_string($trade, 'orderId');
        $marketId = $this->safe_string($trade, 'marketSymbol');
        $market = $this->safe_market($marketId, $market, '-');
        $cost = null;
        $price = $this->safe_float($trade, 'rate');
        $amount = $this->safe_float($trade, 'quantity');
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        $takerOrMaker = null;
        $isTaker = $this->safe_value($trade, 'isTaker');
        if ($isTaker !== null) {
            $takerOrMaker = $isTaker ? 'taker' : 'maker';
        }
        $fee = null;
        $feeCost = $this->safe_float($trade, 'commission');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $market['quote'],
            );
        }
        $side = $this->safe_string_lower($trade, 'takerSide');
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $market['symbol'],
            'id' => $id,
            'order' => $order,
            'takerOrMaker' => $takerOrMaker,
            'type' => null,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetPing ($params);
        //
        //     {
        //         "serverTime" => 1594596023162
        //     }
        //
        return $this->safe_integer($response, 'serverTime');
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'marketSymbol' => $this->market_id($symbol),
        );
        $response = $this->publicGetMarketsMarketSymbolTrades (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "id":"9c5589db-42fb-436c-b105-5e2edcb95673",
        //             "executedAt":"2020-10-03T11:48:43.38Z",
        //             "quantity":"0.17939626",
        //             "rate":"0.03297952",
        //             "takerSide":"BUY"
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "startsAt":"2020-06-12T02:35:00Z",
        //         "open":"0.02493753",
        //         "high":"0.02493753",
        //         "low":"0.02493753",
        //         "close":"0.02493753",
        //         "volume":"0.09590123",
        //         "quoteVolume":"0.00239153"
        //     }
        //
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'startsAt')),
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
        $reverseId = $market['baseId'] . '-' . $market['quoteId'];
        $request = array(
            'candleInterval' => $this->timeframes[$timeframe],
            'marketSymbol' => $reverseId,
        );
        $method = 'publicGetMarketsMarketSymbolCandlesCandleIntervalRecent';
        if ($since !== null) {
            $now = $this->milliseconds();
            $difference = abs($now - $since);
            $sinceDate = $this->ymd($since);
            $parts = explode('-', $sinceDate);
            $sinceYear = $this->safe_integer($parts, 0);
            $sinceMonth = $this->safe_integer($parts, 1);
            $sinceDay = $this->safe_integer($parts, 2);
            if ($timeframe === '1d') {
                // if the $since argument is beyond one year into the past
                if ($difference > 31622400000) {
                    $method = 'publicGetMarketsMarketSymbolCandlesCandleIntervalHistoricalYear';
                    $request['year'] = $sinceYear;
                }
                // $request['year'] = year;
            } else if ($timeframe === '1h') {
                // if the $since argument is beyond 31 days into the past
                if ($difference > 2678400000) {
                    $method = 'publicGetMarketsMarketSymbolCandlesCandleIntervalHistoricalYearMonth';
                    $request['year'] = $sinceYear;
                    $request['month'] = $sinceMonth;
                }
            } else {
                // if the $since argument is beyond 1 day into the past
                if ($difference > 86400000) {
                    $method = 'publicGetMarketsMarketSymbolCandlesCandleIntervalHistoricalYearMonthDay';
                    $request['year'] = $sinceYear;
                    $request['month'] = $sinceMonth;
                    $request['day'] = $sinceDay;
                }
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     array(
        //         array("startsAt":"2020-06-12T02:35:00Z","open":"0.02493753","high":"0.02493753","low":"0.02493753","close":"0.02493753","volume":"0.09590123","quoteVolume":"0.00239153"),
        //         array("startsAt":"2020-06-12T02:40:00Z","open":"0.02491874","high":"0.02491874","low":"0.02490970","close":"0.02490970","volume":"0.04515695","quoteVolume":"0.00112505"),
        //         array("startsAt":"2020-06-12T02:45:00Z","open":"0.02490753","high":"0.02493143","low":"0.02490753","close":"0.02493143","volume":"0.17769640","quoteVolume":"0.00442663")
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['marketSymbol'] = $market['id'];
        }
        $response = $this->privateGetOrdersOpen (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => $id,
        );
        $response = $this->privateGetOrdersOrderIdExecutions (array_merge($request, $params));
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        // A ceiling order is a $market or limit order that allows you to specify
        // the $amount of quote currency you want to spend (or receive, if selling)
        // instead of the quantity of the $market currency (e.g. buy $100 USD of BTC
        // at the current $market BTC $price)
        $this->load_markets();
        $market = $this->market($symbol);
        $uppercaseType = strtoupper($type);
        $reverseId = $market['baseId'] . '-' . $market['quoteId'];
        $request = array(
            'marketSymbol' => $reverseId,
            'direction' => strtoupper($side),
            'type' => $uppercaseType, // LIMIT, MARKET, CEILING_LIMIT, CEILING_MARKET
            // 'quantity' => $this->amount_to_precision($symbol, $amount), // required for limit orders, excluded for ceiling orders
            // 'ceiling' => $this->price_to_precision($symbol, $price), // required for ceiling orders, excluded for non-ceiling orders
            // 'limit' => $this->price_to_precision($symbol, $price), // required for limit orders, excluded for $market orders
            // 'timeInForce' => 'GOOD_TIL_CANCELLED', // IMMEDIATE_OR_CANCEL, FILL_OR_KILL, POST_ONLY_GOOD_TIL_CANCELLED
            // 'useAwards' => false, // optional
        );
        $isCeilingLimit = ($uppercaseType === 'CEILING_LIMIT');
        $isCeilingMarket = ($uppercaseType === 'CEILING_MARKET');
        $isCeilingOrder = $isCeilingLimit || $isCeilingMarket;
        if ($isCeilingOrder) {
            $cost = null;
            if ($isCeilingLimit) {
                $request['limit'] = $this->price_to_precision($symbol, $price);
                $cost = $this->safe_float_2($params, 'ceiling', 'cost', $amount);
            } else if ($isCeilingMarket) {
                $cost = $this->safe_float_2($params, 'ceiling', 'cost');
                if ($cost === null) {
                    if ($price === null) {
                        $cost = $amount;
                    } else {
                        $cost = $amount * $price;
                    }
                }
            }
            $params = $this->omit($params, array( 'ceiling', 'cost' ));
            $request['ceiling'] = $this->cost_to_precision($symbol, $cost);
            // bittrex only accepts IMMEDIATE_OR_CANCEL or FILL_OR_KILL for ceiling orders
            $request['timeInForce'] = 'IMMEDIATE_OR_CANCEL';
        } else {
            $request['quantity'] = $this->amount_to_precision($symbol, $amount);
            if ($uppercaseType === 'LIMIT') {
                $request['limit'] = $this->price_to_precision($symbol, $price);
                $request['timeInForce'] = 'GOOD_TIL_CANCELLED';
            } else {
                // bittrex does not allow GOOD_TIL_CANCELLED for $market orders
                $request['timeInForce'] = 'IMMEDIATE_OR_CANCEL';
            }
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //     {
        //         id => 'f03d5e98-b5ac-48fb-8647-dd4db828a297',
        //         marketSymbol => 'BTC-USDT',
        //         direction => 'SELL',
        //         $type => 'LIMIT',
        //         quantity => '0.01',
        //         limit => '6000',
        //         timeInForce => 'GOOD_TIL_CANCELLED',
        //         fillQuantity => '0.00000000',
        //         commission => '0.00000000',
        //         proceeds => '0.00000000',
        //         status => 'OPEN',
        //         createdAt => '2020-03-18T02:37:33.42Z',
        //         updatedAt => '2020-03-18T02:37:33.42Z'
        //       }
        //
        return $this->parse_order($response, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => $id,
        );
        $response = $this->privateDeleteOrdersOrderId (array_merge($request, $params));
        return array_merge($this->parse_order($response), array(
            'id' => $id,
            'info' => $response,
            'status' => 'canceled',
        ));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['marketSymbol'] = $market['id'];
        }
        $response = $this->privateDeleteOrdersOpen (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "id":"66582be0-5337-4d8c-b212-c356dd525801",
        //             "statusCode":"SUCCESS",
        //             "$result":{
        //                 "id":"66582be0-5337-4d8c-b212-c356dd525801",
        //                 "marketSymbol":"BTC-USDT",
        //                 "direction":"BUY",
        //                 "type":"LIMIT",
        //                 "quantity":"0.01000000",
        //                 "limit":"3000.00000000",
        //                 "timeInForce":"GOOD_TIL_CANCELLED",
        //                 "fillQuantity":"0.00000000",
        //                 "commission":"0.00000000",
        //                 "proceeds":"0.00000000",
        //                 "status":"CLOSED",
        //                 "createdAt":"2020-10-06T12:31:53.39Z",
        //                 "updatedAt":"2020-10-06T12:54:28.8Z",
        //                 "closedAt":"2020-10-06T12:54:28.8Z"
        //             }
        //         }
        //     )
        //
        $orders = array();
        for ($i = 0; $i < count($response); $i++) {
            $result = $this->safe_value($response[$i], 'result', array());
            $orders[] = $result;
        }
        return $this->parse_orders($orders, $market);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // https://support.bittrex.com/hc/en-us/articles/115003723911
        $request = array();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currencySymbol'] = $currency['id'];
        }
        $response = $this->privateGetDepositsClosed (array_merge($request, $params));
        // we cannot filter by `$since` timestamp, as it isn't set by Bittrex
        // see https://github.com/ccxt/ccxt/issues/4067
        // return $this->parse_transactions($response, $currency, $since, $limit);
        return $this->parse_transactions($response, $currency, null, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // https://support.bittrex.com/hc/en-us/articles/115003723911
        $request = array();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currencySymbol'] = $currency['id'];
        }
        $response = $this->privateGetWithdrawalsClosed (array_merge($request, $params));
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //     {
        //         "$id" => "d00fdf2e-df9e-48f1-....",
        //         "currencySymbol" => "BTC",
        //         "quantity" => "0.00550000",
        //         "cryptoAddress" => "1PhmYjnJPZH5NUwV8AU...",
        //         "txId" => "d1f1afffe1b9b6614eaee7e8133c85d98...",
        //         "confirmations" => 2,
        //         "updatedAt" => "2020-01-12T16:49:30.41Z",
        //         "completedAt" => "2020-01-12T16:49:30.41Z",
        //         "$status" => "COMPLETED",
        //         "source" => "BLOCKCHAIN"
        //     }
        //
        // fetchWithdrawals
        //     {
        //         "PaymentUuid" : "e293da98-788c-4188-a8f9-8ec2c33fdfcf",
        //         "Currency" : "XC",
        //         "Amount" : 7513.75121715,
        //         "Address" : "EVnSMgAd7EonF2Dgc4c9K14L12RBaW5S5J",
        //         "Opened" : "2014-07-08T23:13:31.83",
        //         "Authorized" : true,
        //         "PendingPayment" : false,
        //         "TxCost" : 0.00002000,
        //         "TxId" : "b4a575c2a71c7e56d02ab8e26bb1ef0a2f6cf2094f6ca2116476a569c1e84f6e",
        //         "Canceled" : false,
        //         "InvalidAddress" : false
        //     }
        //
        $id = $this->safe_string($transaction, 'id');
        $amount = $this->safe_float($transaction, 'quantity');
        $address = $this->safe_string($transaction, 'cryptoAddress');
        $txid = $this->safe_string($transaction, 'txId');
        $updated = $this->parse8601($this->safe_string($transaction, 'updatedAt'));
        $opened = $this->parse8601($this->safe_string($transaction, 'createdAt'));
        $timestamp = $opened ? $opened : $updated;
        $type = ($opened === null) ? 'deposit' : 'withdrawal';
        $currencyId = $this->safe_string($transaction, 'currencySymbol');
        $code = $this->safe_currency_code($currencyId, $currency);
        $status = 'pending';
        if ($type === 'deposit') {
            //
            // deposits $numConfirmations never reach the $minConfirmations number
            // we set all of them to 'ok', otherwise they'd all be 'pending'
            //
            //     $numConfirmations = $this->safe_integer($transaction, 'Confirmations', 0);
            //     $minConfirmations = $this->safe_integer($currency['info'], 'MinConfirmation');
            //     if ($numConfirmations >= $minConfirmations) {
            //         $status = 'ok';
            //     }
            //
            $status = 'ok';
        } else {
            $responseStatus = $this->safe_string($transaction, 'status');
            if ($responseStatus === 'ERROR_INVALID_ADDRESS') {
                $status = 'failed';
            } else if ($responseStatus === 'CANCELLED') {
                $status = 'canceled';
            } else if ($responseStatus === 'PENDING') {
                $status = 'pending';
            } else if ($responseStatus === 'COMPLETED') {
                $status = 'ok';
            } else if ($responseStatus === 'AUTHORIZED' && ($txid !== null)) {
                $status = 'ok';
            }
        }
        $feeCost = $this->safe_float($transaction, 'txCost');
        if ($feeCost === null) {
            if ($type === 'deposit') {
                // according to https://support.bittrex.com/hc/en-us/articles/115000199651-What-fees-does-Bittrex-charge-
                $feeCost = 0;
            }
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => null,
            'status' => $status,
            'type' => $type,
            'updated' => $updated,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => array(
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function parse_time_in_force($timeInForce) {
        $timeInForces = array(
            'GOOD_TIL_CANCELLED' => 'GTC',
            'IMMEDIATE_OR_CANCEL' => 'IOC',
            'FILL_OR_KILL' => 'FOK',
            'POST_ONLY_GOOD_TIL_CANCELLED' => 'PO',
        );
        return $this->safe_string($timeInForces, $timeInForce, $timeInForce);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         id => '1be35109-b763-44ce-b6ea-05b6b0735c0c',
        //         $marketSymbol => 'LTC-ETH',
        //         $direction => 'BUY',
        //         $type => 'LIMIT',
        //         $quantity => '0.50000000',
        //         $limit => '0.17846699',
        //         $timeInForce => 'GOOD_TIL_CANCELLED',
        //         $fillQuantity => '0.50000000',
        //         $commission => '0.00022286',
        //         $proceeds => '0.08914915',
        //         $status => 'CLOSED',
        //         $createdAt => '2018-06-23T13:14:28.613Z',
        //         $updatedAt => '2018-06-23T13:14:30.19Z',
        //         $closedAt => '2018-06-23T13:14:30.19Z'
        //     }
        //
        $marketSymbol = $this->safe_string($order, 'marketSymbol');
        $symbol = null;
        $feeCurrency = null;
        if ($marketSymbol !== null) {
            list($baseId, $quoteId) = explode('-', $marketSymbol);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $feeCurrency = $quote;
        }
        $direction = $this->safe_string_lower($order, 'direction');
        $createdAt = $this->safe_string($order, 'createdAt');
        $updatedAt = $this->safe_string($order, 'updatedAt');
        $closedAt = $this->safe_string($order, 'closedAt');
        $lastTradeTimestamp = null;
        if ($closedAt !== null) {
            $lastTradeTimestamp = $this->parse8601($closedAt);
        } else if ($updatedAt) {
            $lastTradeTimestamp = $this->parse8601($updatedAt);
        }
        $timestamp = $this->parse8601($createdAt);
        $type = $this->safe_string_lower($order, 'type');
        $quantity = $this->safe_float($order, 'quantity');
        $limit = $this->safe_float($order, 'limit');
        $fillQuantity = $this->safe_float($order, 'fillQuantity');
        $commission = $this->safe_float($order, 'commission');
        $proceeds = $this->safe_float($order, 'proceeds');
        $average = null;
        $remaining = null;
        if ($fillQuantity !== null) {
            if ($proceeds !== null) {
                if ($fillQuantity > 0) {
                    $average = $proceeds / $fillQuantity;
                } else if ($proceeds === 0) {
                    $average = 0;
                }
            }
            if ($quantity !== null) {
                $remaining = $quantity - $fillQuantity;
            }
        }
        $status = $this->safe_string_lower($order, 'status');
        if (($status === 'closed') && ($remaining !== null) && ($remaining > 0)) {
            $status = 'canceled';
        }
        $timeInForce = $this->parse_time_in_force($this->safe_string($order, 'timeInForce'));
        $postOnly = ($timeInForce === 'PO');
        return array(
            'id' => $this->safe_string($order, 'id'),
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => $timeInForce,
            'postOnly' => $postOnly,
            'side' => $direction,
            'price' => $limit,
            'stopPrice' => null,
            'cost' => $proceeds,
            'average' => $average,
            'amount' => $quantity,
            'filled' => $fillQuantity,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => array(
                'cost' => $commission,
                'currency' => $feeCurrency,
            ),
            'info' => $order,
            'trades' => null,
        );
    }

    public function parse_orders($orders, $market = null, $since = null, $limit = null, $params = array ()) {
        if ($this->options['fetchClosedOrdersFilterBySince']) {
            return parent::parse_orders($orders, $market, $since, $limit, $params);
        } else {
            return parent::parse_orders($orders, $market, null, $limit, $params);
        }
    }

    public function parse_order_status($status) {
        $statuses = array(
            'CLOSED' => 'closed',
            'OPEN' => 'open',
            'CANCELLED' => 'canceled',
            'CANCELED' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = null;
        try {
            $request = array(
                'orderId' => $id,
            );
            $response = $this->privateGetOrdersOrderId (array_merge($request, $params));
        } catch (Exception $e) {
            if ($this->last_json_response) {
                $message = $this->safe_string($this->last_json_response, 'message');
                if ($message === 'UUID_INVALID') {
                    throw new OrderNotFound($this->id . ' fetchOrder() error => ' . $this->last_http_response);
                }
            }
            throw $e;
        }
        return $this->parse_order($response);
    }

    public function order_to_trade($order) {
        // this entire method should be moved to the base class
        $timestamp = $this->safe_integer_2($order, 'lastTradeTimestamp', 'timestamp');
        return array(
            'id' => $this->safe_string($order, 'id'),
            'side' => $this->safe_string($order, 'side'),
            'order' => $this->safe_string($order, 'id'),
            'type' => $this->safe_string($order, 'type'),
            'price' => $this->safe_float($order, 'average'),
            'amount' => $this->safe_float($order, 'filled'),
            'cost' => $this->safe_float($order, 'cost'),
            'symbol' => $this->safe_string($order, 'symbol'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => $this->safe_value($order, 'fee'),
            'info' => $order,
            'takerOrMaker' => null,
        );
    }

    public function orders_to_trades($orders) {
        // this entire method should be moved to the base class
        $result = array();
        for ($i = 0; $i < count($orders); $i++) {
            $result[] = $this->order_to_trade($orders[$i]);
        }
        return $result;
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        if ($since !== null) {
            $request['startDate'] = $this->ymdhms($since, 'T') . 'Z';
        }
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            // because of this line we will have to rethink the entire v3
            // in other words, markets define all the rest of the API
            // and v3 $market ids are reversed in comparison to v1
            // v3 has to be a completely separate implementation
            // otherwise we will have to shuffle symbols and currencies everywhere
            // which is prone to errors, as was shown here
            // https://github.com/ccxt/ccxt/pull/5219#issuecomment-499646209
            $request['marketSymbol'] = $market['base'] . '-' . $market['quote'];
        }
        $response = $this->privateGetOrdersClosed (array_merge($request, $params));
        $orders = $this->parse_orders($response, $market);
        $trades = $this->orders_to_trades($orders);
        return $this->filter_by_symbol_since_limit($trades, $symbol, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        if ($since !== null) {
            $request['startDate'] = $this->ymdhms($since, 'T') . 'Z';
        }
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            // because of this line we will have to rethink the entire v3
            // in other words, markets define all the rest of the API
            // and v3 $market ids are reversed in comparison to v1
            // v3 has to be a completely separate implementation
            // otherwise we will have to shuffle symbols and currencies everywhere
            // which is prone to errors, as was shown here
            // https://github.com/ccxt/ccxt/pull/5219#issuecomment-499646209
            $request['marketSymbol'] = $market['base'] . '-' . $market['quote'];
        }
        $response = $this->privateGetOrdersClosed (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currencySymbol' => $currency['id'],
        );
        $response = $this->privatePostAddressesCurrencySymbol (array_merge($request, $params));
        //
        //     {
        //         "status":"PROVISIONED",
        //         "currencySymbol":"XRP",
        //         "cryptoAddress":"rPVMhWBsfF9iMXYj3aAzJVkPDTFNSyWdKy",
        //         "cryptoAddressTag":"392034158"
        //     }
        //
        $address = $this->safe_string($response, 'cryptoAddress');
        $message = $this->safe_string($response, 'status');
        if (!$address || $message === 'REQUESTED') {
            throw new AddressPending($this->id . ' the $address for ' . $code . ' is being generated (pending, not ready yet, retry again later)');
        }
        $tag = $this->safe_string($response, 'cryptoAddressTag');
        if (($tag === null) && (is_array($this->options['tag']) && array_key_exists($currency['type'], $this->options['tag']))) {
            $tag = $address;
            $address = $currency['address'];
        }
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currencySymbol' => $currency['id'],
        );
        $response = $this->privateGetAddressesCurrencySymbol (array_merge($request, $params));
        //
        //     {
        //         "status":"PROVISIONED",
        //         "currencySymbol":"XRP",
        //         "cryptoAddress":"rPVMhWBsfF9iMXYj3aAzJVkPDTFNSyWdKy",
        //         "cryptoAddressTag":"392034158"
        //     }
        //
        $address = $this->safe_string($response, 'cryptoAddress');
        $message = $this->safe_string($response, 'status');
        if (!$address || $message === 'REQUESTED') {
            throw new AddressPending($this->id . ' the $address for ' . $code . ' is being generated (pending, not ready yet, retry again later)');
        }
        $tag = $this->safe_string($response, 'cryptoAddressTag');
        if (($tag === null) && (is_array($this->options['tag']) && array_key_exists($currency['type'], $this->options['tag']))) {
            $tag = $address;
            $address = $currency['address'];
        }
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
            'currencySymbol' => $currency['id'],
            'quantity' => $amount,
            'cryptoAddress' => $address,
        );
        if ($tag !== null) {
            $request['cryptoAddressTag'] = $tag;
        }
        $response = $this->privatePostWithdrawals (array_merge($request, $params));
        $id = $this->safe_string($response, 'id');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function sign($path, $api = 'v3', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->implode_params($this->urls['api'][$api], array(
            'hostname' => $this->hostname,
        )) . '/';
        if ($api === 'private') {
            $url .= $this->version . '/';
            $this->check_required_credentials();
            $url .= $this->implode_params($path, $params);
            $params = $this->omit($params, $this->extract_params($path));
            $hashString = '';
            if ($method === 'POST') {
                $body = $this->json($params);
                $hashString = $body;
            } else {
                if ($params) {
                    $url .= '?' . $this->rawencode($params);
                }
            }
            $contentHash = $this->hash($this->encode($hashString), 'sha512', 'hex');
            $timestamp = (string) $this->milliseconds();
            $auth = $timestamp . $url . $method . $contentHash;
            $subaccountId = $this->safe_value($this->options, 'subaccountId');
            if ($subaccountId !== null) {
                $auth .= $subaccountId;
            }
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha512');
            $headers = array(
                'Api-Key' => $this->apiKey,
                'Api-Timestamp' => $timestamp,
                'Api-Content-Hash' => $contentHash,
                'Api-Signature' => $signature,
            );
            if ($subaccountId !== null) {
                $headers['Api-Subaccount-Id'] = $subaccountId;
            }
            if ($method === 'POST') {
                $headers['Content-Type'] = 'application/json';
            }
        } else {
            if ($api === 'public') {
                $url .= $this->version . '/';
            }
            $url .= $this->implode_params($path, $params);
            $params = $this->omit($params, $this->extract_params($path));
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //
        //     array( $success => false, $message => "$message" )
        //
        if ($body[0] === '{') {
            $feedback = $this->id . ' ' . $body;
            $success = $this->safe_value($response, 'success');
            if ($success === null) {
                $code = $this->safe_string($response, 'code');
                if ($code !== null) {
                    $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
                    $this->throw_broadly_matched_exception($this->exceptions['broad'], $code, $feedback);
                }
                if (($code === 'NOT_FOUND') && (mb_strpos($url, 'addresses') !== false)) {
                    throw new InvalidAddress($feedback);
                }
                // throw new ExchangeError($this->id . ' malformed $response ' . $this->json($response));
                return;
            }
            if (gettype($success) === 'string') {
                // bleutrade uses string instead of boolean
                $success = ($success === 'true');
            }
            if (!$success) {
                $message = $this->safe_string($response, 'message');
                if ($message === 'APIKEY_INVALID') {
                    if ($this->options['hasAlreadyAuthenticatedSuccessfully']) {
                        throw new DDoSProtection($feedback);
                    } else {
                        throw new AuthenticationError($feedback);
                    }
                }
                // https://github.com/ccxt/ccxt/issues/4932
                // the following two lines are now redundant, see line 171 in describe()
                //
                //     if ($message === 'DUST_TRADE_DISALLOWED_MIN_VALUE_50K_SAT')
                //         throw new InvalidOrder($this->id . ' order cost should be over 50k satoshi ' . $this->json($response));
                //
                if ($message === 'INVALID_ORDER') {
                    // Bittrex will return an ambiguous INVALID_ORDER $message
                    // upon canceling already-canceled and closed orders
                    // therefore this special case for cancelOrder
                    // $url = 'https://bittrex.com/api/v1.1/market/cancel?apikey=API_KEY&uuid=ORDER_UUID'
                    $cancel = 'cancel';
                    $indexOfCancel = mb_strpos($url, $cancel);
                    if ($indexOfCancel >= 0) {
                        $urlParts = explode('?', $url);
                        $numParts = is_array($urlParts) ? count($urlParts) : 0;
                        if ($numParts > 1) {
                            $query = $urlParts[1];
                            $params = explode('&', $query);
                            $numParams = is_array($params) ? count($params) : 0;
                            $orderId = null;
                            for ($i = 0; $i < $numParams; $i++) {
                                $param = $params[$i];
                                $keyValue = explode('=', $param);
                                if ($keyValue[0] === 'uuid') {
                                    $orderId = $keyValue[1];
                                    break;
                                }
                            }
                            if ($orderId !== null) {
                                throw new OrderNotFound($this->id . ' cancelOrder ' . $orderId . ' ' . $this->json($response));
                            } else {
                                throw new OrderNotFound($this->id . ' cancelOrder ' . $this->json($response));
                            }
                        }
                    }
                }
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
                if ($message !== null) {
                    $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
                }
                throw new ExchangeError($feedback);
            }
        }
    }
}

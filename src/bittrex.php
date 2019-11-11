<?php

namespace ccxt;

use Exception; // a common import

class bittrex extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bittrex',
            'name' => 'Bittrex',
            'countries' => array ( 'US' ),
            'version' => 'v1.1',
            'rateLimit' => 1500,
            'certified' => true,
            // new metainfo interface
            'has' => array (
                'CORS' => true,
                'createMarketOrder' => false,
                'fetchDepositAddress' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchMyTrades' => 'emulated',
                'fetchOHLCV' => true,
                'fetchOrder' => true,
                'fetchOpenOrders' => true,
                'fetchTickers' => true,
                'withdraw' => true,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
                'fetchTransactions' => false,
            ),
            'timeframes' => array (
                '1m' => 'oneMin',
                '5m' => 'fiveMin',
                '30m' => 'thirtyMin',
                '1h' => 'hour',
                '1d' => 'day',
            ),
            'hostname' => 'bittrex.com',
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766352-cf0b3c26-5ed5-11e7-82b7-f3826b7a97d8.jpg',
                'api' => array (
                    'public' => 'https://{hostname}/api',
                    'account' => 'https://{hostname}/api',
                    'market' => 'https://{hostname}/api',
                    'v2' => 'https://{hostname}/api/v2.0/pub',
                    'v3' => 'https://api.bittrex.com/v3',
                    'v3public' => 'https://api.bittrex.com/v3',
                ),
                'www' => 'https://bittrex.com',
                'doc' => array (
                    'https://bittrex.github.io/api/',
                    'https://bittrex.github.io/api/v3',
                    'https://www.npmjs.com/package/bittrex-node',
                ),
                'fees' => array (
                    'https://bittrex.zendesk.com/hc/en-us/articles/115003684371-BITTREX-SERVICE-FEES-AND-WITHDRAWAL-LIMITATIONS',
                    'https://bittrex.zendesk.com/hc/en-us/articles/115000199651-What-fees-does-Bittrex-charge-',
                ),
            ),
            'api' => array (
                'v3' => array (
                    'get' => array (
                        'account',
                        'addresses',
                        'addresses/{currencySymbol}',
                        'balances',
                        'balances/{currencySymbol}',
                        'currencies',
                        'currencies/{symbol}',
                        'deposits/open',
                        'deposits/closed',
                        'deposits/ByTxId/{txId}',
                        'deposits/{depositId}',
                        'orders/closed',
                        'orders/open',
                        'orders/{orderId}',
                        'ping',
                        'subaccounts/{subaccountId}',
                        'subaccounts',
                        'withdrawals/open',
                        'withdrawals/closed',
                        'withdrawals/ByTxId/{txId}',
                        'withdrawals/{withdrawalId}',
                    ),
                    'post' => array (
                        'addresses',
                        'orders',
                        'subaccounts',
                        'withdrawals',
                    ),
                    'delete' => array (
                        'orders/{orderId}',
                        'withdrawals/{withdrawalId}',
                    ),
                ),
                'v3public' => array (
                    'get' => array (
                        'markets',
                        'markets/summaries',
                        'markets/{marketSymbol}',
                        'markets/{marketSymbol}/summary',
                        'markets/{marketSymbol}/orderbook',
                        'markets/{marketSymbol}/trades',
                        'markets/{marketSymbol}/ticker',
                        'markets/{marketSymbol}/candles',
                    ),
                ),
                'v2' => array (
                    'get' => array (
                        'currencies/GetBTCPrice',
                        'market/GetTicks',
                        'market/GetLatestTick',
                        'Markets/GetMarketSummaries',
                        'market/GetLatestTick',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'currencies',
                        'markethistory',
                        'markets',
                        'marketsummaries',
                        'marketsummary',
                        'orderbook',
                        'ticker',
                    ),
                ),
                'account' => array (
                    'get' => array (
                        'balance',
                        'balances',
                        'depositaddress',
                        'deposithistory',
                        'order',
                        'orders',
                        'orderhistory',
                        'withdrawalhistory',
                        'withdraw',
                    ),
                ),
                'market' => array (
                    'get' => array (
                        'buylimit',
                        'buymarket',
                        'cancel',
                        'openorders',
                        'selllimit',
                        'sellmarket',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.0025,
                    'taker' => 0.0025,
                ),
                'funding' => array (
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array (
                        'BTC' => 0.0005,
                        'LTC' => 0.01,
                        'DOGE' => 2,
                        'VTC' => 0.02,
                        'PPC' => 0.02,
                        'FTC' => 0.2,
                        'RDD' => 2,
                        'NXT' => 2,
                        'DASH' => 0.05,
                        'POT' => 0.002,
                        'BLK' => 0.02,
                        'EMC2' => 0.2,
                        'XMY' => 0.2,
                        'GLD' => 0.0002,
                        'SLR' => 0.2,
                        'GRS' => 0.2,
                    ),
                    'deposit' => array (
                        'BTC' => 0,
                        'LTC' => 0,
                        'DOGE' => 0,
                        'VTC' => 0,
                        'PPC' => 0,
                        'FTC' => 0,
                        'RDD' => 0,
                        'NXT' => 0,
                        'DASH' => 0,
                        'POT' => 0,
                        'BLK' => 0,
                        'EMC2' => 0,
                        'XMY' => 0,
                        'GLD' => 0,
                        'SLR' => 0,
                        'GRS' => 0,
                    ),
                ),
            ),
            'exceptions' => array (
                // 'Call to Cancel was throttled. Try again in 60 seconds.' => '\\ccxt\\DDoSProtection',
                // 'Call to GetBalances was throttled. Try again in 60 seconds.' => '\\ccxt\\DDoSProtection',
                'APISIGN_NOT_PROVIDED' => '\\ccxt\\AuthenticationError',
                'INVALID_SIGNATURE' => '\\ccxt\\AuthenticationError',
                'INVALID_CURRENCY' => '\\ccxt\\ExchangeError',
                'INVALID_PERMISSION' => '\\ccxt\\AuthenticationError',
                'INSUFFICIENT_FUNDS' => '\\ccxt\\InsufficientFunds',
                'QUANTITY_NOT_PROVIDED' => '\\ccxt\\InvalidOrder',
                'MIN_TRADE_REQUIREMENT_NOT_MET' => '\\ccxt\\InvalidOrder',
                'ORDER_NOT_OPEN' => '\\ccxt\\OrderNotFound',
                'INVALID_ORDER' => '\\ccxt\\InvalidOrder',
                'UUID_INVALID' => '\\ccxt\\OrderNotFound',
                'RATE_NOT_PROVIDED' => '\\ccxt\\InvalidOrder', // createLimitBuyOrder ('ETH/BTC', 1, 0)
                'WHITELIST_VIOLATION_IP' => '\\ccxt\\PermissionDenied',
                'DUST_TRADE_DISALLOWED_MIN_VALUE' => '\\ccxt\\InvalidOrder',
                'RESTRICTED_MARKET' => '\\ccxt\\BadSymbol',
                'We are down for scheduled maintenance, but we\u2019ll be back up shortly.' => '\\ccxt\\OnMaintenance', // array("success":false,"message":"We are down for scheduled maintenance, but we\u2019ll be back up shortly.","result":null,"explanation":null)
            ),
            'options' => array (
                'parseOrderStatus' => false,
                'hasAlreadyAuthenticatedSuccessfully' => false, // a workaround for APIKEY_INVALID
                'symbolSeparator' => '-',
                // With certain currencies, like
                // AEON, BTS, GXS, NXT, SBD, STEEM, STR, XEM, XLM, XMR, XRP
                // an additional tag / memo / payment id is usually required by exchanges.
                // With Bittrex some currencies imply the "base address . tag" logic.
                // The base address for depositing is stored on $this->currencies[code]
                // The base address identifies the exchange as the recipient
                // while the tag identifies the user account within the exchange
                // and the tag is retrieved with fetchDepositAddress.
                'tag' => array (
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
                'fetchClosedOrdersMethod' => 'fetch_closed_orders_v3',
                'fetchClosedOrdersFilterBySince' => true,
            ),
            'commonCurrencies' => array (
                'BITS' => 'SWIFT',
                'CPC' => 'Capricoin',
            ),
        ));
    }

    public function cost_to_precision ($symbol, $cost) {
        return $this->decimal_to_precision($cost, TRUNCATE, $this->markets[$symbol]['precision']['price'], DECIMAL_PLACES);
    }

    public function fee_to_precision ($symbol, $fee) {
        return $this->decimal_to_precision($fee, TRUNCATE, $this->markets[$symbol]['precision']['price'], DECIMAL_PLACES);
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->v3publicGetMarkets ($params);
        //
        //     array (
        //         array (
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
        // $markets = $this->safe_value($response, 'result');
        for ($i = 0; $i < count ($response); $i++) {
            $market = $response[$i];
            $baseId = $this->safe_string($market, 'baseCurrencySymbol');
            $quoteId = $this->safe_string($market, 'quoteCurrencySymbol');
            // bittrex v2 uses inverted pairs, v3 uses regular pairs
            // we use v3 for fetchMarkets and v2 throughout the rest of this implementation
            // therefore we swap the $base ←→ $quote here to be v2-compatible
            // https://github.com/ccxt/ccxt/issues/5634
            // $id = $this->safe_string($market, 'symbol');
            $id = $quoteId . $this->options['symbolSeparator'] . $baseId;
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $pricePrecision = $this->safe_integer($market, 'precision', 8);
            $precision = array (
                'amount' => 8,
                'price' => $pricePrecision,
            );
            $status = $this->safe_string($market, 'status');
            $active = ($status === 'ONLINE');
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'info' => $market,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($market, 'minTradeSize'),
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->accountGetBalances ($params);
        $balances = $this->safe_value($response, 'result');
        $result = array( 'info' => $balances );
        $indexed = $this->index_by($balances, 'Currency');
        $currencyIds = is_array($indexed) ? array_keys($indexed) : array();
        for ($i = 0; $i < count ($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account ();
            $balance = $indexed[$currencyId];
            $account['free'] = $this->safe_float($balance, 'Available');
            $account['total'] = $this->safe_float($balance, 'Balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
            'type' => 'both',
        );
        $response = $this->publicGetOrderbook (array_merge ($request, $params));
        $orderbook = $response['result'];
        if (is_array($params) && array_key_exists('type', $params)) {
            if ($params['type'] === 'buy') {
                $orderbook = array (
                    'buy' => $response['result'],
                    'sell' => array(),
                );
            } else if ($params['type'] === 'sell') {
                $orderbook = array (
                    'buy' => array(),
                    'sell' => $response['result'],
                );
            }
        }
        return $this->parse_order_book($orderbook, null, 'buy', 'sell', 'Rate', 'Quantity');
    }

    public function fetch_currencies ($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     {
        //         "success" => true,
        //         "message" => "",
        //         "$result" => array (
        //             array (
        //                 "Currency" => "BTC",
        //                 "CurrencyLong":"Bitcoin",
        //                 "MinConfirmation":2,
        //                 "TxFee":0.00050000,
        //                 "IsActive":true,
        //                 "IsRestricted":false,
        //                 "CoinType":"BITCOIN",
        //                 "BaseAddress":"1N52wHoVR79PMDishab2XmRHsbekCdGquK",
        //                 "Notice":null
        //             ),
        //             ...,
        //         )
        //     }
        //
        $currencies = $this->safe_value($response, 'result', array());
        $result = array();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'Currency');
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/deposit methods and
            // differentiated fees for each particular method
            $code = $this->safe_currency_code($id);
            $precision = 8; // default $precision, todo => fix "magic constants"
            $address = $this->safe_value($currency, 'BaseAddress');
            $fee = $this->safe_float($currency, 'TxFee'); // todo => redesign
            $result[$code] = array (
                'id' => $id,
                'code' => $code,
                'address' => $address,
                'info' => $currency,
                'type' => $currency['CoinType'],
                'name' => $currency['CurrencyLong'],
                'active' => $currency['IsActive'],
                'fee' => $fee,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => pow(10, -$precision),
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => pow(10, -$precision),
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array (
                        'min' => $fee,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        //
        //     {
        //         "MarketName":"BTC-ETH",
        //         "High":0.02127099,
        //         "Low":0.02035064,
        //         "Volume":10288.40271571,
        //         "Last":0.02070510,
        //         "BaseVolume":214.64663206,
        //         "TimeStamp":"2019-09-18T21:03:59.897",
        //         "Bid":0.02070509,
        //         "Ask":0.02070510,
        //         "OpenBuyOrders":1228,
        //         "OpenSellOrders":5899,
        //         "PrevDay":0.02082823,
        //         "Created":"2015-08-14T09:02:24.817"
        //     }
        //
        $timestamp = $this->parse8601 ($this->safe_string($ticker, 'TimeStamp'));
        $symbol = null;
        $marketId = $this->safe_string($ticker, 'MarketName');
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                $symbol = $this->parse_symbol ($marketId);
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $previous = $this->safe_float($ticker, 'PrevDay');
        $last = $this->safe_float($ticker, 'Last');
        $change = null;
        $percentage = null;
        if ($last !== null) {
            if ($previous !== null) {
                $change = $last - $previous;
                if ($previous > 0) {
                    $percentage = ($change / $previous) * 100;
                }
            }
        }
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'High'),
            'low' => $this->safe_float($ticker, 'Low'),
            'bid' => $this->safe_float($ticker, 'Bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'Ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $previous,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'Volume'),
            'quoteVolume' => $this->safe_float($ticker, 'BaseVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetMarketsummaries ($params);
        $result = $this->safe_value($response, 'result');
        $tickers = array();
        for ($i = 0; $i < count ($result); $i++) {
            $ticker = $this->parse_ticker($result[$i]);
            $tickers[] = $ticker;
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'market' => $market['id'],
        );
        $response = $this->publicGetMarketsummary (array_merge ($request, $params));
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "result":array (
        //             {
        //                 "MarketName":"BTC-ETH",
        //                 "High":0.02127099,
        //                 "Low":0.02035064,
        //                 "Volume":10288.40271571,
        //                 "Last":0.02070510,
        //                 "BaseVolume":214.64663206,
        //                 "TimeStamp":"2019-09-18T21:03:59.897",
        //                 "Bid":0.02070509,
        //                 "Ask":0.02070510,
        //                 "OpenBuyOrders":1228,
        //                 "OpenSellOrders":5899,
        //                 "PrevDay":0.02082823,
        //                 "Created":"2015-08-14T09:02:24.817"
        //             }
        //         )
        //     }
        //
        $ticker = $response['result'][0];
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->parse8601 ($trade['TimeStamp'] . '+00:00');
        $side = null;
        if ($trade['OrderType'] === 'BUY') {
            $side = 'buy';
        } else if ($trade['OrderType'] === 'SELL') {
            $side = 'sell';
        }
        $id = $this->safe_string_2($trade, 'Id', 'ID');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $cost = null;
        $price = $this->safe_float($trade, 'Price');
        $amount = $this->safe_float($trade, 'Quantity');
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        return array (
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => null,
            'type' => 'limit',
            'takerOrMaker' => null,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'market' => $market['id'],
        );
        $response = $this->publicGetMarkethistory (array_merge ($request, $params));
        if (is_array($response) && array_key_exists('result', $response)) {
            if ($response['result'] !== null) {
                return $this->parse_trades($response['result'], $market, $since, $limit);
            }
        }
        throw new ExchangeError($this->id . ' fetchTrades() returned null response');
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '1d', $since = null, $limit = null) {
        $timestamp = $this->parse8601 ($ohlcv['T'] . '+00:00');
        return [
            $timestamp,
            $ohlcv['O'],
            $ohlcv['H'],
            $ohlcv['L'],
            $ohlcv['C'],
            $ohlcv['V'],
        ];
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'tickInterval' => $this->timeframes[$timeframe],
            'marketName' => $market['id'],
        );
        $response = $this->v2GetMarketGetTicks (array_merge ($request, $params));
        if (is_array($response) && array_key_exists('result', $response)) {
            if ($response['result']) {
                return $this->parse_ohlcvs($response['result'], $market, $timeframe, $since, $limit);
            }
        }
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['market'] = $market['id'];
        }
        $response = $this->marketGetOpenorders (array_merge ($request, $params));
        $result = $this->safe_value($response, 'result', array());
        $orders = $this->parse_orders($result, $market, $since, $limit);
        return $this->filter_by_symbol($orders, $symbol);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $method = 'marketGet' . $this->capitalize ($side) . $type;
        $request = array (
            'market' => $market['id'],
            'quantity' => $this->amount_to_precision($symbol, $amount),
            'rate' => $this->price_to_precision($symbol, $price),
        );
        // if ($type == 'limit')
        //     order['rate'] = $this->price_to_precision($symbol, $price);
        $response = $this->$method (array_merge ($request, $params));
        $orderIdField = $this->get_order_id_field ();
        $orderId = $this->safe_string($response['result'], $orderIdField);
        return array (
            'info' => $response,
            'id' => $orderId,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'status' => 'open',
        );
    }

    public function get_order_id_field () {
        return 'uuid';
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $orderIdField = $this->get_order_id_field ();
        $request = array();
        $request[$orderIdField] = $id;
        $response = $this->marketGetCancel (array_merge ($request, $params));
        //
        //     {
        //         "success" => true,
        //         "message" => "''",
        //         "result" => {
        //             "uuid" => "614c34e4-8d71-11e3-94b5-425861b86ab6"
        //         }
        //     }
        //
        return array_merge ($this->parse_order($response), array (
            'status' => 'canceled',
        ));
    }

    public function fetch_deposits ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // https://support.bittrex.com/hc/en-us/articles/115003723911
        $request = array();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
            $request['currency'] = $currency['id'];
        }
        $response = $this->accountGetDeposithistory (array_merge ($request, $params));
        //
        //     { success =>    true,
        //       message =>   "",
        //        result => array ( {            Id =>  22578097,
        //                           Amount =>  0.3,
        //                         Currency => "ETH",
        //                    Confirmations =>  15,
        //                      LastUpdated => "2018-06-10T07:12:10.57",
        //                             TxId => "0xf50b5ba2ca5438b58f93516eaa523eaf35b4420ca0f24061003df1be7…",
        //                    CryptoAddress => "0xb25f281fa51f1635abd4a60b0870a62d2a7fa404"                    } ) }
        //
        // we cannot filter by `$since` timestamp, as it isn't set by Bittrex
        // see https://github.com/ccxt/ccxt/issues/4067
        // return $this->parse_transactions($response['result'], $currency, $since, $limit);
        return $this->parse_transactions($response['result'], $currency, null, $limit);
    }

    public function fetch_withdrawals ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // https://support.bittrex.com/hc/en-us/articles/115003723911
        $request = array();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
            $request['currency'] = $currency['id'];
        }
        $response = $this->accountGetWithdrawalhistory (array_merge ($request, $params));
        //
        //     {
        //         "success" : true,
        //         "message" : "",
        //         "result" : [array (
        //                 "PaymentUuid" : "b32c7a5c-90c6-4c6e-835c-e16df12708b1",
        //                 "Currency" : "BTC",
        //                 "Amount" : 17.00000000,
        //                 "Address" : "1DfaaFBdbB5nrHj87x3NHS4onvw1GPNyAu",
        //                 "Opened" : "2014-07-09T04:24:47.217",
        //                 "Authorized" : true,
        //                 "PendingPayment" : false,
        //                 "TxCost" : 0.00020000,
        //                 "TxId" : null,
        //                 "Canceled" : true,
        //                 "InvalidAddress" : false
        //             ), {
        //                 "PaymentUuid" : "d193da98-788c-4188-a8f9-8ec2c33fdfcf",
        //                 "Currency" : "XC",
        //                 "Amount" : 7513.75121715,
        //                 "Address" : "TcnSMgAd7EonF2Dgc4c9K14L12RBaW5S5J",
        //                 "Opened" : "2014-07-08T23:13:31.83",
        //                 "Authorized" : true,
        //                 "PendingPayment" : false,
        //                 "TxCost" : 0.00002000,
        //                 "TxId" : "d8a575c2a71c7e56d02ab8e26bb1ef0a2f6cf2094f6ca2116476a569c1e84f6e",
        //                 "Canceled" : false,
        //                 "InvalidAddress" : false
        //             }
        //         ]
        //     }
        //
        return $this->parse_transactions($response['result'], $currency, $since, $limit);
    }

    public function parse_transaction ($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         Id =>  72578097,
        //         Amount =>  0.3,
        //         Currency => "ETH",
        //         Confirmations =>  15,
        //         LastUpdated => "2018-06-17T07:12:14.57",
        //         TxId => "0xb31b5ba2ca5438b58f93516eaa523eaf35b4420ca0f24061003df1be7…",
        //         CryptoAddress => "0x2d5f281fa51f1635abd4a60b0870a62d2a7fa404"
        //     }
        //
        // fetchWithdrawals
        //
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
        $id = $this->safe_string_2($transaction, 'Id', 'PaymentUuid');
        $amount = $this->safe_float($transaction, 'Amount');
        $address = $this->safe_string_2($transaction, 'CryptoAddress', 'Address');
        $txid = $this->safe_string($transaction, 'TxId');
        $updated = $this->parse8601 ($this->safe_string($transaction, 'LastUpdated'));
        $opened = $this->parse8601 ($this->safe_string($transaction, 'Opened'));
        $timestamp = $opened ? $opened : $updated;
        $type = ($opened === null) ? 'deposit' : 'withdrawal';
        $currencyId = $this->safe_string($transaction, 'Currency');
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
            $authorized = $this->safe_value($transaction, 'Authorized', false);
            $pendingPayment = $this->safe_value($transaction, 'PendingPayment', false);
            $canceled = $this->safe_value($transaction, 'Canceled', false);
            $invalidAddress = $this->safe_value($transaction, 'InvalidAddress', false);
            if ($invalidAddress) {
                $status = 'failed';
            } else if ($canceled) {
                $status = 'canceled';
            } else if ($pendingPayment) {
                $status = 'pending';
            } else if ($authorized && ($txid !== null)) {
                $status = 'ok';
            }
        }
        $feeCost = $this->safe_float($transaction, 'TxCost');
        if ($feeCost === null) {
            if ($type === 'deposit') {
                // according to https://support.bittrex.com/hc/en-us/articles/115000199651-What-fees-does-Bittrex-charge-
                $feeCost = 0; // FIXME => remove hardcoded value that may change any time
            }
        }
        return array (
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
            'datetime' => $this->iso8601 ($timestamp),
            'fee' => array (
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function parse_symbol ($id) {
        list($quoteId, $baseId) = explode($this->options['symbolSeparator'], $id);
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        return $base . '/' . $quote;
    }

    public function parse_order ($order, $market = null) {
        if (is_array($order) && array_key_exists('marketSymbol', $order)) {
            return $this->parse_order_v3 ($order, $market);
        } else {
            return $this->parse_order_v2 ($order, $market);
        }
    }

    public function parse_orders ($orders, $market = null, $since = null, $limit = null, $params = array ()) {
        if ($this->options['fetchClosedOrdersFilterBySince']) {
            return parent::parse_orders($orders, $market, $since, $limit, $params);
        } else {
            return parent::parse_orders($orders, $market, null, $limit, $params);
        }
    }

    public function parse_order_status ($status) {
        $statuses = array (
            'CLOSED' => 'closed',
            'OPEN' => 'open',
            'CANCELLED' => 'canceled',
            'CANCELED' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order_v3 ($order, $market = null) {
        //
        //     {
        //         id => '1be35109-b763-44ce-b6ea-05b6b0735c0c',
        //         $marketSymbol => 'LTC-ETH',
        //         $direction => 'BUY',
        //         $type => 'LIMIT',
        //         $quantity => '0.50000000',
        //         $limit => '0.17846699',
        //         timeInForce => 'GOOD_TIL_CANCELLED',
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
            $lastTradeTimestamp = $this->parse8601 ($closedAt);
        } else if ($updatedAt) {
            $lastTradeTimestamp = $this->parse8601 ($updatedAt);
        }
        $timestamp = $this->parse8601 ($createdAt);
        $type = $this->safe_string_lower($order, 'type');
        $quantity = $this->safe_float($order, 'quantity');
        $limit = $this->safe_float($order, 'limit');
        $fillQuantity = $this->safe_float($order, 'fillQuantity');
        $commission = $this->safe_float($order, 'commission');
        $proceeds = $this->safe_float($order, 'proceeds');
        $status = $this->safe_string_lower($order, 'status');
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
        return array (
            'id' => $this->safe_string($order, 'id'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $direction,
            'price' => $limit,
            'cost' => $proceeds,
            'average' => $average,
            'amount' => $quantity,
            'filled' => $fillQuantity,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => array (
                'cost' => $commission,
                'currency' => $feeCurrency,
            ),
            'info' => $order,
        );
    }

    public function parse_order_v2 ($order, $market = null) {
        //
        //     {
        //         "Uuid" => "string (uuid)",
        //         "OrderUuid" => "8925d746-bc9f-4684-b1aa-e507467aaa99",
        //         "Exchange" => "BTC-LTC",
        //         "OrderType" => "string",
        //         "Quantity" => 100000,
        //         "QuantityRemaining" => 100000,
        //         "Limit" => 1e-8,
        //         "CommissionPaid" => 0,
        //         "Price" => 0,
        //         "PricePerUnit" => null,
        //         "Opened" => "2014-07-09T03:55:48.583",
        //         "Closed" => null,
        //         "CancelInitiated" => "boolean",
        //         "ImmediateOrCancel" => "boolean",
        //         "IsConditional" => "boolean"
        //     }
        //
        $side = $this->safe_string_2($order, 'OrderType', 'Type');
        $isBuyOrder = ($side === 'LIMIT_BUY') || ($side === 'BUY');
        $isSellOrder = ($side === 'LIMIT_SELL') || ($side === 'SELL');
        if ($isBuyOrder) {
            $side = 'buy';
        }
        if ($isSellOrder) {
            $side = 'sell';
        }
        // We parse different fields in a very specific $order->
        // Order might well be $closed and then canceled.
        $status = null;
        if ((is_array($order) && array_key_exists('Opened', $order)) && $order['Opened']) {
            $status = 'open';
        }
        if ((is_array($order) && array_key_exists('Closed', $order)) && $order['Closed']) {
            $status = 'closed';
        }
        if ((is_array($order) && array_key_exists('CancelInitiated', $order)) && $order['CancelInitiated']) {
            $status = 'canceled';
        }
        if ((is_array($order) && array_key_exists('Status', $order)) && $this->options['parseOrderStatus']) {
            $status = $this->parse_order_status($this->safe_string($order, 'Status'));
        }
        $symbol = null;
        if (is_array($order) && array_key_exists('Exchange', $order)) {
            $marketId = $this->safe_string($order, 'Exchange');
            if ($marketId !== null) {
                if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                    $market = $this->markets_by_id[$marketId];
                    $symbol = $market['symbol'];
                } else {
                    $symbol = $this->parse_symbol ($marketId);
                }
            }
        } else {
            if ($market !== null) {
                $symbol = $market['symbol'];
            }
        }
        $timestamp = null;
        $opened = $this->safe_string($order, 'Opened');
        if ($opened !== null) {
            $timestamp = $this->parse8601 ($opened . '+00:00');
        }
        $created = $this->safe_string($order, 'Created');
        if ($created !== null) {
            $timestamp = $this->parse8601 ($created . '+00:00');
        }
        $lastTradeTimestamp = null;
        $lastTimestamp = $this->safe_string($order, 'TimeStamp');
        if ($lastTimestamp !== null) {
            $lastTradeTimestamp = $this->parse8601 ($lastTimestamp . '+00:00');
        }
        $closed = $this->safe_string($order, 'Closed');
        if ($closed !== null) {
            $lastTradeTimestamp = $this->parse8601 ($closed . '+00:00');
        }
        if ($timestamp === null) {
            $timestamp = $lastTradeTimestamp;
        }
        $fee = null;
        $feeCost = $this->safe_float_2($order, 'Commission', 'CommissionPaid');
        if ($feeCost !== null) {
            $fee = array (
                'cost' => $feeCost,
            );
            if ($market !== null) {
                $fee['currency'] = $market['quote'];
            } else if ($symbol !== null) {
                $currencyIds = explode('/', $symbol);
                $quoteCurrencyId = $currencyIds[1];
                $fee['currency'] = $this->safe_currency_code($quoteCurrencyId);
            }
        }
        $price = $this->safe_float($order, 'Limit');
        $cost = $this->safe_float($order, 'Price');
        $amount = $this->safe_float($order, 'Quantity');
        $remaining = $this->safe_float($order, 'QuantityRemaining');
        $filled = null;
        if ($amount !== null && $remaining !== null) {
            $filled = $amount - $remaining;
            if (($status === 'closed') && ($remaining > 0)) {
                $status = 'canceled';
            }
        }
        if (!$cost) {
            if ($price && $filled) {
                $cost = $price * $filled;
            }
        }
        if (!$price) {
            if ($cost && $filled) {
                $price = $cost / $filled;
            }
        }
        $average = $this->safe_float($order, 'PricePerUnit');
        $id = $this->safe_string_2($order, 'OrderUuid', 'OrderId');
        return array (
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'average' => $average,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
        );
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = null;
        try {
            $orderIdField = $this->get_order_id_field ();
            $request = array();
            $request[$orderIdField] = $id;
            $response = $this->accountGetOrder (array_merge ($request, $params));
        } catch (Exception $e) {
            if ($this->last_json_response) {
                $message = $this->safe_string($this->last_json_response, 'message');
                if ($message === 'UUID_INVALID') {
                    throw new OrderNotFound($this->id . ' fetchOrder() error => ' . $this->last_http_response);
                }
            }
            throw $e;
        }
        if (!$response['result']) {
            throw new OrderNotFound($this->id . ' order ' . $id . ' not found');
        }
        return $this->parse_order($response['result']);
    }

    public function order_to_trade ($order) {
        // this entire method should be moved to the base class
        $timestamp = $this->safe_integer_2($order, 'lastTradeTimestamp', 'timestamp');
        return array (
            'id' => $this->safe_string($order, 'id'),
            'side' => $this->safe_string($order, 'side'),
            'order' => $this->safe_string($order, 'id'),
            'type' => $this->safe_string($order, 'type'),
            'price' => $this->safe_float($order, 'average'),
            'amount' => $this->safe_float($order, 'filled'),
            'cost' => $this->safe_float($order, 'cost'),
            'symbol' => $this->safe_string($order, 'symbol'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'fee' => $this->safe_value($order, 'fee'),
            'info' => $order,
        );
    }

    public function orders_to_trades ($orders) {
        // this entire method should be moved to the base class
        $result = array();
        for ($i = 0; $i < count ($orders); $i++) {
            $result[] = $this->order_to_trade ($orders[$i]);
        }
        return $result;
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_closed_orders ($symbol, $since, $limit, $params);
        return $this->orders_to_trades ($orders);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $method = $this->safe_string($this->options, 'fetchClosedOrdersMethod', 'fetch_closed_orders_v3');
        return $this->$method ($symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders_v2 ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['market'] = $market['id'];
        }
        $response = $this->accountGetOrderhistory (array_merge ($request, $params));
        $result = $this->safe_value($response, 'result', array());
        $orders = $this->parse_orders($result, $market, $since, $limit);
        if ($symbol !== null) {
            return $this->filter_by_symbol($orders, $symbol);
        }
        return $orders;
    }

    public function fetch_closed_orders_v3 ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        if ($since !== null) {
            $request['startDate'] = $this->ymdhms ($since, 'T') . 'Z';
        }
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            // because of this line we will have to rethink the entire v3
            // in other words, markets define all the rest of the API
            // and v3 $market ids are reversed in comparison to v2
            // v3 has to be a completely separate implementation
            // otherwise we will have to shuffle symbols and currencies everywhere
            // which is prone to errors, as was shown here
            // https://github.com/ccxt/ccxt/pull/5219#issuecomment-499646209
            $request['marketSymbol'] = $market['base'] . '-' . $market['quote'];
        }
        $response = $this->v3GetOrdersClosed (array_merge ($request, $params));
        $orders = $this->parse_orders($response, $market, $since, $limit);
        if ($symbol !== null) {
            return $this->filter_by_symbol($orders, $symbol);
        }
        return $orders;
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->accountGetDepositaddress (array_merge ($request, $params));
        //
        //     array( "success" => false, "$message" => "ADDRESS_GENERATING", "result" => null )
        //
        //     { success =>    true,
        //       $message =>   "",
        //        result => { Currency => "INCNT",
        //                   Address => "3PHvQt9bK21f7eVQVdJzrNPcsMzXabEA5Ha" } } }
        //
        $address = $this->safe_string($response['result'], 'Address');
        $message = $this->safe_string($response, 'message');
        if (!$address || $message === 'ADDRESS_GENERATING') {
            throw new AddressPending($this->id . ' the $address for ' . $code . ' is being generated (pending, not ready yet, retry again later)');
        }
        $tag = null;
        if (is_array($this->options['tag']) && array_key_exists($currency['type'], $this->options['tag'])) {
            $tag = $address;
            $address = $currency['address'];
        }
        $this->check_address($address);
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'quantity' => $amount,
            'address' => $address,
        );
        if ($tag !== null) {
            $request['paymentid'] = $tag;
        }
        $response = $this->accountGetWithdraw (array_merge ($request, $params));
        $result = $this->safe_value($response, 'result', array());
        $id = $this->safe_string($result, 'uuid');
        return array (
            'info' => $response,
            'id' => $id,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->implode_params($this->urls['api'][$api], array (
            'hostname' => $this->hostname,
        )) . '/';
        if ($api !== 'v2' && $api !== 'v3' && $api !== 'v3public') {
            $url .= $this->version . '/';
        }
        if ($api === 'public') {
            $url .= $api . '/' . strtolower($method) . $path;
            if ($params) {
                $url .= '?' . $this->urlencode ($params);
            }
        } else if ($api === 'v3public') {
            $url .= $path;
            if ($params) {
                $url .= '?' . $this->urlencode ($params);
            }
        } else if ($api === 'v2') {
            $url .= $path;
            if ($params) {
                $url .= '?' . $this->urlencode ($params);
            }
        } else if ($api === 'v3') {
            $url .= $path;
            if ($params) {
                $url .= '?' . $this->rawencode ($params);
            }
            $contentHash = $this->hash ($this->encode (''), 'sha512', 'hex');
            $timestamp = (string) $this->milliseconds ();
            $auth = $timestamp . $url . $method . $contentHash;
            $subaccountId = $this->safe_value($this->options, 'subaccountId');
            if ($subaccountId !== null) {
                $auth .= $subaccountId;
            }
            $signature = $this->hmac ($this->encode ($auth), $this->encode ($this->secret), 'sha512');
            $headers = array (
                'Api-Key' => $this->apiKey,
                'Api-Timestamp' => $timestamp,
                'Api-Content-Hash' => $contentHash,
                'Api-Signature' => $signature,
            );
            if ($subaccountId !== null) {
                $headers['Api-Subaccount-Id'] = $subaccountId;
            }
        } else {
            $this->check_required_credentials();
            $url .= $api . '/';
            if ((($api === 'account') && ($path !== 'withdraw')) || ($path === 'openorders')) {
                $url .= strtolower($method);
            }
            $request = array (
                'apikey' => $this->apiKey,
            );
            $disableNonce = $this->safe_value($this->options, 'disableNonce');
            if (($disableNonce === null) || !$disableNonce) {
                $request['nonce'] = $this->nonce ();
            }
            $url .= $path . '?' . $this->urlencode (array_merge ($request, $params));
            $signature = $this->hmac ($this->encode ($url), $this->encode ($this->secret), 'sha512');
            $headers = array( 'apisign' => $signature );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        //
        //     array( $success => false, $message => "$message" )
        //
        if ($body[0] === '{') {
            $success = $this->safe_value($response, 'success');
            if ($success === null) {
                throw new ExchangeError($this->id . ' => malformed $response => ' . $this->json ($response));
            }
            if (gettype ($success) === 'string') {
                // bleutrade uses string instead of boolean
                $success = ($success === 'true') ? true : false;
            }
            if (!$success) {
                $message = $this->safe_string($response, 'message');
                $feedback = $this->id . ' ' . $this->json ($response);
                $exceptions = $this->exceptions;
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
                //         throw new InvalidOrder($this->id . ' order cost should be over 50k satoshi ' . $this->json ($response));
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
                        $numParts = is_array ($urlParts) ? count ($urlParts) : 0;
                        if ($numParts > 1) {
                            $query = $urlParts[1];
                            $params = explode('&', $query);
                            $numParams = is_array ($params) ? count ($params) : 0;
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
                                throw new OrderNotFound($this->id . ' cancelOrder ' . $orderId . ' ' . $this->json ($response));
                            } else {
                                throw new OrderNotFound($this->id . ' cancelOrder ' . $this->json ($response));
                            }
                        }
                    }
                }
                if (is_array($exceptions) && array_key_exists($message, $exceptions)) {
                    throw new $exceptions[$message]($feedback);
                }
                if ($message !== null) {
                    if (mb_strpos($message, 'throttled. Try again') !== false) {
                        throw new DDoSProtection($feedback);
                    }
                    if (mb_strpos($message, 'problem') !== false) {
                        throw new ExchangeNotAvailable($feedback); // 'There was a problem processing your request.  If this problem persists, please contact...')
                    }
                }
                throw new ExchangeError($feedback);
            }
        }
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        // a workaround for APIKEY_INVALID
        if (($api === 'account') || ($api === 'market')) {
            $this->options['hasAlreadyAuthenticatedSuccessfully'] = true;
        }
        return $response;
    }
}

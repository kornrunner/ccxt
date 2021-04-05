<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;

class bitbay extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitbay',
            'name' => 'BitBay',
            'countries' => array( 'MT', 'EU' ), // Malta
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
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
                '3d' => '259200',
                '1w' => '604800',
            ),
            'urls' => array(
                'referral' => 'https://auth.bitbay.net/ref/jHlbB4mIkdS1',
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766132-978a7bd8-5ece-11e7-9540-bc96d1e9bbb8.jpg',
                'www' => 'https://bitbay.net',
                'api' => array(
                    'public' => 'https://bitbay.net/API/Public',
                    'private' => 'https://bitbay.net/API/Trading/tradingApi.php',
                    'v1_01Public' => 'https://api.bitbay.net/rest',
                    'v1_01Private' => 'https://api.bitbay.net/rest',
                ),
                'doc' => array(
                    'https://bitbay.net/public-api',
                    'https://bitbay.net/en/private-api',
                    'https://bitbay.net/account/tab-api',
                    'https://github.com/BitBayNet/API',
                    'https://docs.bitbay.net/v1.0.1-en/reference',
                ),
                'support' => 'https://support.bitbay.net',
                'fees' => 'https://bitbay.net/en/fees',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        '{id}/all',
                        '{id}/market',
                        '{id}/orderbook',
                        '{id}/ticker',
                        '{id}/trades',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'info',
                        'trade',
                        'cancel',
                        'orderbook',
                        'orders',
                        'transfer',
                        'withdraw',
                        'history',
                        'transactions',
                    ),
                ),
                'v1_01Public' => array(
                    'get' => array(
                        'trading/ticker',
                        'trading/ticker/{symbol}',
                        'trading/stats',
                        'trading/orderbook/{symbol}',
                        'trading/transactions/{symbol}',
                        'trading/candle/history/{symbol}/{resolution}',
                    ),
                ),
                'v1_01Private' => array(
                    'get' => array(
                        'payments/withdrawal/{detailId}',
                        'payments/deposit/{detailId}',
                        'trading/offer',
                        'trading/config/{symbol}',
                        'trading/history/transactions',
                        'balances/BITBAY/history',
                        'balances/BITBAY/balance',
                        'fiat_cantor/rate/{baseId}/{quoteId}',
                        'fiat_cantor/history',
                    ),
                    'post' => array(
                        'trading/offer/{symbol}',
                        'trading/config/{symbol}',
                        'balances/BITBAY/balance',
                        'balances/BITBAY/balance/transfer/{source}/{destination}',
                        'fiat_cantor/exchange',
                    ),
                    'delete' => array(
                        'trading/offer/{symbol}/{id}/{side}/{price}',
                    ),
                    'put' => array(
                        'balances/BITBAY/balance/{id}',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.0,
                    'taker' => 0.1 / 100,
                    'percentage' => true,
                    'tierBased' => false,
                ),
                'fiat' => array(
                    'maker' => 0.30 / 100,
                    'taker' => 0.43 / 100,
                    'percentage' => true,
                    'tierBased' => true,
                    'tiers' => array(
                        'taker' => array(
                            array( 0.0043, 0 ),
                            array( 0.0042, 1250 ),
                            array( 0.0041, 3750 ),
                            array( 0.0040, 7500 ),
                            array( 0.0039, 10000 ),
                            array( 0.0038, 15000 ),
                            array( 0.0037, 20000 ),
                            array( 0.0036, 25000 ),
                            array( 0.0035, 37500 ),
                            array( 0.0034, 50000 ),
                            array( 0.0033, 75000 ),
                            array( 0.0032, 100000 ),
                            array( 0.0031, 150000 ),
                            array( 0.0030, 200000 ),
                            array( 0.0029, 250000 ),
                            array( 0.0028, 375000 ),
                            array( 0.0027, 500000 ),
                            array( 0.0026, 625000 ),
                            array( 0.0025, 875000 ),
                        ),
                        'maker' => array(
                            array( 0.0030, 0 ),
                            array( 0.0029, 1250 ),
                            array( 0.0028, 3750 ),
                            array( 0.0028, 7500 ),
                            array( 0.0027, 10000 ),
                            array( 0.0026, 15000 ),
                            array( 0.0025, 20000 ),
                            array( 0.0025, 25000 ),
                            array( 0.0024, 37500 ),
                            array( 0.0023, 50000 ),
                            array( 0.0023, 75000 ),
                            array( 0.0022, 100000 ),
                            array( 0.0021, 150000 ),
                            array( 0.0021, 200000 ),
                            array( 0.0020, 250000 ),
                            array( 0.0019, 375000 ),
                            array( 0.0018, 500000 ),
                            array( 0.0018, 625000 ),
                            array( 0.0017, 875000 ),
                        ),
                    ),
                ),
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => 0.0009,
                        'LTC' => 0.005,
                        'ETH' => 0.00126,
                        'LSK' => 0.2,
                        'BCH' => 0.0006,
                        'GAME' => 0.005,
                        'DASH' => 0.001,
                        'BTG' => 0.0008,
                        'PLN' => 4,
                        'EUR' => 1.5,
                    ),
                ),
            ),
            'options' => array(
                'fiatCurrencies' => array( 'EUR', 'USD', 'GBP', 'PLN' ),
            ),
            'exceptions' => array(
                '400' => '\\ccxt\\ExchangeError', // At least one parameter wasn't set
                '401' => '\\ccxt\\InvalidOrder', // Invalid order type
                '402' => '\\ccxt\\InvalidOrder', // No orders with specified currencies
                '403' => '\\ccxt\\InvalidOrder', // Invalid payment currency name
                '404' => '\\ccxt\\InvalidOrder', // Error. Wrong transaction type
                '405' => '\\ccxt\\InvalidOrder', // Order with this id doesn't exist
                '406' => '\\ccxt\\InsufficientFunds', // No enough money or crypto
                // code 407 not specified are not specified in their docs
                '408' => '\\ccxt\\InvalidOrder', // Invalid currency name
                '501' => '\\ccxt\\AuthenticationError', // Invalid public key
                '502' => '\\ccxt\\AuthenticationError', // Invalid sign
                '503' => '\\ccxt\\InvalidNonce', // Invalid moment parameter. Request time doesn't match current server time
                '504' => '\\ccxt\\ExchangeError', // Invalid method
                '505' => '\\ccxt\\AuthenticationError', // Key has no permission for this action
                '506' => '\\ccxt\\AccountSuspended', // Account locked. Please contact with customer service
                // codes 507 and 508 are not specified in their docs
                '509' => '\\ccxt\\ExchangeError', // The BIC/SWIFT is required for this currency
                '510' => '\\ccxt\\BadSymbol', // Invalid market name
                'FUNDS_NOT_SUFFICIENT' => '\\ccxt\\InsufficientFunds',
                'OFFER_FUNDS_NOT_EXCEEDING_MINIMUMS' => '\\ccxt\\InvalidOrder',
                'OFFER_NOT_FOUND' => '\\ccxt\\OrderNotFound',
                'OFFER_WOULD_HAVE_BEEN_PARTIALLY_FILLED' => '\\ccxt\\OrderImmediatelyFillable',
                'ACTION_LIMIT_EXCEEDED' => '\\ccxt\\RateLimitExceeded',
                'UNDER_MAINTENANCE' => '\\ccxt\\OnMaintenance',
                'REQUEST_TIMESTAMP_TOO_OLD' => '\\ccxt\\InvalidNonce',
                'PERMISSIONS_NOT_SUFFICIENT' => '\\ccxt\\PermissionDenied',
            ),
            'commonCurrencies' => array(
                'GGC' => 'Global Game Coin',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->v1_01PublicGetTradingTicker ($params);
        $fiatCurrencies = $this->safe_value($this->options, 'fiatCurrencies', array());
        //
        //     {
        //         status => 'Ok',
        //         $items => array(
        //             'BSV-USD' => array(
        //                 $market => array(
        //                     code => 'BSV-USD',
        //                     $first => array( currency => 'BSV', minOffer => '0.00035', scale => 8 ),
        //                     $second => array( currency => 'USD', minOffer => '5', scale => 2 )
        //                 ),
        //                 time => '1557569762154',
        //                 highestBid => '52.31',
        //                 lowestAsk => '62.99',
        //                 rate => '63',
        //                 previousRate => '51.21',
        //             ),
        //         ),
        //     }
        //
        $result = array();
        $items = $this->safe_value($response, 'items');
        $keys = is_array($items) ? array_keys($items) : array();
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $item = $items[$key];
            $market = $this->safe_value($item, 'market', array());
            $first = $this->safe_value($market, 'first', array());
            $second = $this->safe_value($market, 'second', array());
            $baseId = $this->safe_string($first, 'currency');
            $quoteId = $this->safe_string($second, 'currency');
            $id = $baseId . $quoteId;
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($first, 'scale'),
                'price' => $this->safe_integer($second, 'scale'),
            );
            $fees = $this->safe_value($this->fees, 'trading', array());
            if ($this->in_array($base, $fiatCurrencies) || $this->in_array($quote, $fiatCurrencies)) {
                $fees = $this->safe_value($this->fees, 'fiat', array());
            }
            $maker = $this->safe_number($fees, 'maker');
            $taker = $this->safe_number($fees, 'taker');
            // todo => check that the limits have ben interpreted correctly
            // todo => parse the $fees page
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'precision' => $precision,
                'active' => null,
                'maker' => $maker,
                'taker' => $taker,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_number($first, 'minOffer'),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $this->safe_number($second, 'minOffer'),
                        'max' => null,
                    ),
                ),
                'info' => $item,
            );
        }
        return $result;
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $response = $this->v1_01PrivateGetTradingOffer (array_merge($request, $params));
        $items = $this->safe_value($response, 'items', array());
        return $this->parse_orders($items, null, $since, $limit, array( 'status' => 'open' ));
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         $market => 'ETH-EUR',
        //         offerType => 'Sell',
        //         id => '93d3657b-d616-11e9-9248-0242ac110005',
        //         currentAmount => '0.04',
        //         lockedAmount => '0.04',
        //         rate => '280',
        //         startAmount => '0.04',
        //         time => '1568372806924',
        //         $postOnly => false,
        //         hidden => false,
        //         mode => 'limit',
        //         receivedAmount => '0.0',
        //         firstBalanceId => '5b816c3e-437c-4e43-9bef-47814ae7ebfc',
        //         secondBalanceId => 'ab43023b-4079-414c-b340-056e3430a3af'
        //     }
        //
        $marketId = $this->safe_string($order, 'market');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $timestamp = $this->safe_integer($order, 'time');
        $amount = $this->safe_number($order, 'startAmount');
        $remaining = $this->safe_number($order, 'currentAmount');
        $postOnly = $this->safe_value($order, 'postOnly');
        return $this->safe_order(array(
            'id' => $this->safe_string($order, 'id'),
            'clientOrderId' => null,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => null,
            'symbol' => $symbol,
            'type' => $this->safe_string($order, 'mode'),
            'timeInForce' => null,
            'postOnly' => $postOnly,
            'side' => $this->safe_string_lower($order, 'offerType'),
            'price' => $this->safe_number($order, 'rate'),
            'stopPrice' => null,
            'amount' => $amount,
            'cost' => null,
            'filled' => null,
            'remaining' => $remaining,
            'average' => null,
            'fee' => null,
            'trades' => null,
        ));
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbol) {
            $markets = array( $this->market_id($symbol) );
            $request['markets'] = $markets;
        }
        $query = array( 'query' => $this->json(array_merge($request, $params)) );
        $response = $this->v1_01PrivateGetTradingHistoryTransactions ($query);
        //
        //     {
        //         status => 'Ok',
        //         totalRows => '67',
        //         $items => array(
        //             array(
        //                 id => 'b54659a0-51b5-42a0-80eb-2ac5357ccee2',
        //                 market => 'BTC-EUR',
        //                 time => '1541697096247',
        //                 amount => '0.00003',
        //                 rate => '4341.44',
        //                 initializedBy => 'Sell',
        //                 wasTaker => false,
        //                 userAction => 'Buy',
        //                 offerId => 'bd19804a-6f89-4a69-adb8-eb078900d006',
        //                 commissionValue => null
        //             ),
        //         )
        //     }
        //
        $items = $this->safe_value($response, 'items');
        $result = $this->parse_trades($items, null, $since, $limit);
        if ($symbol === null) {
            return $result;
        }
        return $this->filter_by_symbol($result, $symbol);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->v1_01PrivateGetBalancesBITBAYBalance ($params);
        $balances = $this->safe_value($response, 'balances');
        if ($balances === null) {
            throw new ExchangeError($this->id . ' empty $balance $response ' . $this->json($response));
        }
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['used'] = $this->safe_number($balance, 'lockedFunds');
            $account['free'] = $this->safe_number($balance, 'availableFunds');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $orderbook = $this->publicGetIdOrderbook (array_merge($request, $params));
        return $this->parse_order_book($orderbook);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $ticker = $this->publicGetIdTicker (array_merge($request, $params));
        $timestamp = $this->milliseconds();
        $baseVolume = $this->safe_number($ticker, 'volume');
        $vwap = $this->safe_number($ticker, 'vwap');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
        }
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'max'),
            'low' => $this->safe_number($ticker, 'min'),
            'bid' => $this->safe_number($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_number($ticker, 'average'),
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $balanceCurrencies = array();
        if ($code !== null) {
            $currency = $this->currency($code);
            $balanceCurrencies[] = $currency['id'];
        }
        $request = array(
            'balanceCurrencies' => $balanceCurrencies,
        );
        if ($since !== null) {
            $request['fromTime'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $request = array_merge($request, $params);
        $response = $this->v1_01PrivateGetBalancesBITBAYHistory (array( 'query' => $this->json($request) ));
        $items = $response['items'];
        return $this->parse_ledger($items, null, $since, $limit);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        //    FUNDS_MIGRATION
        //    {
        //      "historyId" => "84ea7a29-7da5-4de5-b0c0-871e83cad765",
        //      "$balance" => array(
        //        "$id" => "821ec166-cb88-4521-916c-f4eb44db98df",
        //        "$currency" => "LTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "LTC"
        //      ),
        //      "detailId" => null,
        //      "time" => 1506128252968,
        //      "$type" => "FUNDS_MIGRATION",
        //      "value" => 0.0009957,
        //      "$fundsBefore" => array( "total" => 0, "available" => 0, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 0.0009957, "available" => 0.0009957, "locked" => 0 ),
        //      "$change" => array( "total" => 0.0009957, "available" => 0.0009957, "locked" => 0 )
        //    }
        //
        //    CREATE_BALANCE
        //    {
        //      "historyId" => "d0fabd8d-9107-4b5e-b9a6-3cab8af70d49",
        //      "$balance" => array(
        //        "$id" => "653ffcf2-3037-4ebe-8e13-d5ea1a01d60d",
        //        "$currency" => "BTG",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTG"
        //      ),
        //      "detailId" => null,
        //      "time" => 1508895244751,
        //      "$type" => "CREATE_BALANCE",
        //      "value" => 0,
        //      "$fundsBefore" => array( "total" => null, "available" => null, "locked" => null ),
        //      "$fundsAfter" => array( "total" => 0, "available" => 0, "locked" => 0 ),
        //      "$change" => array( "total" => 0, "available" => 0, "locked" => 0 )
        //    }
        //
        //    BITCOIN_GOLD_FORK
        //    {
        //      "historyId" => "2b4d52d3-611c-473d-b92c-8a8d87a24e41",
        //      "$balance" => array(
        //        "$id" => "653ffcf2-3037-4ebe-8e13-d5ea1a01d60d",
        //        "$currency" => "BTG",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTG"
        //      ),
        //      "detailId" => null,
        //      "time" => 1508895244778,
        //      "$type" => "BITCOIN_GOLD_FORK",
        //      "value" => 0.00453512,
        //      "$fundsBefore" => array( "total" => 0, "available" => 0, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 0.00453512, "available" => 0.00453512, "locked" => 0 ),
        //      "$change" => array( "total" => 0.00453512, "available" => 0.00453512, "locked" => 0 )
        //    }
        //
        //    ADD_FUNDS
        //    {
        //      "historyId" => "3158236d-dae5-4a5d-81af-c1fa4af340fb",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => "8e83a960-e737-4380-b8bb-259d6e236faa",
        //      "time" => 1520631178816,
        //      "$type" => "ADD_FUNDS",
        //      "value" => 0.628405,
        //      "$fundsBefore" => array( "total" => 0.00453512, "available" => 0.00453512, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 0.63294012, "available" => 0.63294012, "locked" => 0 ),
        //      "$change" => array( "total" => 0.628405, "available" => 0.628405, "locked" => 0 )
        //    }
        //
        //    TRANSACTION_PRE_LOCKING
        //    {
        //      "historyId" => "e7d19e0f-03b3-46a8-bc72-dde72cc24ead",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => null,
        //      "time" => 1520706403868,
        //      "$type" => "TRANSACTION_PRE_LOCKING",
        //      "value" => -0.1,
        //      "$fundsBefore" => array( "total" => 0.63294012, "available" => 0.63294012, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 0.63294012, "available" => 0.53294012, "locked" => 0.1 ),
        //      "$change" => array( "total" => 0, "available" => -0.1, "locked" => 0.1 )
        //    }
        //
        //    TRANSACTION_POST_OUTCOME
        //    {
        //      "historyId" => "c4010825-231d-4a9c-8e46-37cde1f7b63c",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => "bf2876bc-b545-4503-96c8-ef4de8233876",
        //      "time" => 1520706404032,
        //      "$type" => "TRANSACTION_POST_OUTCOME",
        //      "value" => -0.01771415,
        //      "$fundsBefore" => array( "total" => 0.63294012, "available" => 0.53294012, "locked" => 0.1 ),
        //      "$fundsAfter" => array( "total" => 0.61522597, "available" => 0.53294012, "locked" => 0.08228585 ),
        //      "$change" => array( "total" => -0.01771415, "available" => 0, "locked" => -0.01771415 )
        //    }
        //
        //    TRANSACTION_POST_INCOME
        //    {
        //      "historyId" => "7f18b7af-b676-4125-84fd-042e683046f6",
        //      "$balance" => array(
        //        "$id" => "ab43023b-4079-414c-b340-056e3430a3af",
        //        "$currency" => "EUR",
        //        "$type" => "FIAT",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "EUR"
        //      ),
        //      "detailId" => "f5fcb274-0cc7-4385-b2d3-bae2756e701f",
        //      "time" => 1520706404035,
        //      "$type" => "TRANSACTION_POST_INCOME",
        //      "value" => 628.78,
        //      "$fundsBefore" => array( "total" => 0, "available" => 0, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 628.78, "available" => 628.78, "locked" => 0 ),
        //      "$change" => array( "total" => 628.78, "available" => 628.78, "locked" => 0 )
        //    }
        //
        //    TRANSACTION_COMMISSION_OUTCOME
        //    {
        //      "historyId" => "843177fa-61bc-4cbf-8be5-b029d856c93b",
        //      "$balance" => array(
        //        "$id" => "ab43023b-4079-414c-b340-056e3430a3af",
        //        "$currency" => "EUR",
        //        "$type" => "FIAT",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "EUR"
        //      ),
        //      "detailId" => "f5fcb274-0cc7-4385-b2d3-bae2756e701f",
        //      "time" => 1520706404050,
        //      "$type" => "TRANSACTION_COMMISSION_OUTCOME",
        //      "value" => -2.71,
        //      "$fundsBefore" => array( "total" => 766.06, "available" => 766.06, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 763.35,"available" => 763.35, "locked" => 0 ),
        //      "$change" => array( "total" => -2.71, "available" => -2.71, "locked" => 0 )
        //    }
        //
        //    TRANSACTION_OFFER_COMPLETED_RETURN
        //    {
        //      "historyId" => "cac69b04-c518-4dc5-9d86-e76e91f2e1d2",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => null,
        //      "time" => 1520714886425,
        //      "$type" => "TRANSACTION_OFFER_COMPLETED_RETURN",
        //      "value" => 0.00000196,
        //      "$fundsBefore" => array( "total" => 0.00941208, "available" => 0.00941012, "locked" => 0.00000196 ),
        //      "$fundsAfter" => array( "total" => 0.00941208, "available" => 0.00941208, "locked" => 0 ),
        //      "$change" => array( "total" => 0, "available" => 0.00000196, "locked" => -0.00000196 )
        //    }
        //
        //    WITHDRAWAL_LOCK_FUNDS
        //    {
        //      "historyId" => "03de2271-66ab-4960-a786-87ab9551fc14",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => "6ad3dc72-1d6d-4ec2-8436-ca43f85a38a6",
        //      "time" => 1522245654481,
        //      "$type" => "WITHDRAWAL_LOCK_FUNDS",
        //      "value" => -0.8,
        //      "$fundsBefore" => array( "total" => 0.8, "available" => 0.8, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 0.8, "available" => 0, "locked" => 0.8 ),
        //      "$change" => array( "total" => 0, "available" => -0.8, "locked" => 0.8 )
        //    }
        //
        //    WITHDRAWAL_SUBTRACT_FUNDS
        //    {
        //      "historyId" => "b0308c89-5288-438d-a306-c6448b1a266d",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => "6ad3dc72-1d6d-4ec2-8436-ca43f85a38a6",
        //      "time" => 1522246526186,
        //      "$type" => "WITHDRAWAL_SUBTRACT_FUNDS",
        //      "value" => -0.8,
        //      "$fundsBefore" => array( "total" => 0.8, "available" => 0, "locked" => 0.8 ),
        //      "$fundsAfter" => array( "total" => 0, "available" => 0, "locked" => 0 ),
        //      "$change" => array( "total" => -0.8, "available" => 0, "locked" => -0.8 )
        //    }
        //
        //    TRANSACTION_OFFER_ABORTED_RETURN
        //    {
        //      "historyId" => "b1a3c075-d403-4e05-8f32-40512cdd88c0",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => null,
        //      "time" => 1522512298662,
        //      "$type" => "TRANSACTION_OFFER_ABORTED_RETURN",
        //      "value" => 0.0564931,
        //      "$fundsBefore" => array( "total" => 0.44951311, "available" => 0.39302001, "locked" => 0.0564931 ),
        //      "$fundsAfter" => array( "total" => 0.44951311, "available" => 0.44951311, "locked" => 0 ),
        //      "$change" => array( "total" => 0, "available" => 0.0564931, "locked" => -0.0564931 )
        //    }
        //
        //    WITHDRAWAL_UNLOCK_FUNDS
        //    {
        //      "historyId" => "0ed569a2-c330-482e-bb89-4cb553fb5b11",
        //      "$balance" => array(
        //        "$id" => "3a7e7a1e-0324-49d5-8f59-298505ebd6c7",
        //        "$currency" => "BTC",
        //        "$type" => "CRYPTO",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "BTC"
        //      ),
        //      "detailId" => "0c7be256-c336-4111-bee7-4eb22e339700",
        //      "time" => 1527866360785,
        //      "$type" => "WITHDRAWAL_UNLOCK_FUNDS",
        //      "value" => 0.05045,
        //      "$fundsBefore" => array( "total" => 0.86001578, "available" => 0.80956578, "locked" => 0.05045 ),
        //      "$fundsAfter" => array( "total" => 0.86001578, "available" => 0.86001578, "locked" => 0 ),
        //      "$change" => array( "total" => 0, "available" => 0.05045, "locked" => -0.05045 )
        //    }
        //
        //    TRANSACTION_COMMISSION_RETURN
        //    {
        //      "historyId" => "07c89c27-46f1-4d7a-8518-b73798bf168a",
        //      "$balance" => array(
        //        "$id" => "ab43023b-4079-414c-b340-056e3430a3af",
        //        "$currency" => "EUR",
        //        "$type" => "FIAT",
        //        "userId" => "a34d361d-7bad-49c1-888e-62473b75d877",
        //        "name" => "EUR"
        //      ),
        //      "detailId" => null,
        //      "time" => 1528304043063,
        //      "$type" => "TRANSACTION_COMMISSION_RETURN",
        //      "value" => 0.6,
        //      "$fundsBefore" => array( "total" => 0, "available" => 0, "locked" => 0 ),
        //      "$fundsAfter" => array( "total" => 0.6, "available" => 0.6, "locked" => 0 ),
        //      "$change" => array( "total" => 0.6, "available" => 0.6, "locked" => 0 )
        //    }
        //
        $timestamp = $this->safe_integer($item, 'time');
        $balance = $this->safe_value($item, 'balance', array());
        $currencyId = $this->safe_string($balance, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $change = $this->safe_value($item, 'change', array());
        $amount = $this->safe_number($change, 'total');
        $direction = 'in';
        if ($amount < 0) {
            $direction = 'out';
            $amount = -$amount;
        }
        $id = $this->safe_string($item, 'historyId');
        // there are 2 undocumented api calls => (v1_01PrivateGetPaymentsDepositDetailId and v1_01PrivateGetPaymentsWithdrawalDetailId)
        // that can be used to enrich the transfers with txid, address etc (you need to use info.detailId as a parameter)
        $referenceId = $this->safe_string($item, 'detailId');
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'type'));
        $fundsBefore = $this->safe_value($item, 'fundsBefore', array());
        $before = $this->safe_number($fundsBefore, 'total');
        $fundsAfter = $this->safe_value($item, 'fundsAfter', array());
        $after = $this->safe_number($fundsAfter, 'total');
        return array(
            'info' => $item,
            'id' => $id,
            'direction' => $direction,
            'account' => null,
            'referenceId' => $referenceId,
            'referenceAccount' => null,
            'type' => $type,
            'currency' => $code,
            'amount' => $amount,
            'before' => $before,
            'after' => $after,
            'status' => 'ok',
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => null,
        );
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'ADD_FUNDS' => 'transaction',
            'BITCOIN_GOLD_FORK' => 'transaction',
            'CREATE_BALANCE' => 'transaction',
            'FUNDS_MIGRATION' => 'transaction',
            'WITHDRAWAL_LOCK_FUNDS' => 'transaction',
            'WITHDRAWAL_SUBTRACT_FUNDS' => 'transaction',
            'WITHDRAWAL_UNLOCK_FUNDS' => 'transaction',
            'TRANSACTION_COMMISSION_OUTCOME' => 'fee',
            'TRANSACTION_COMMISSION_RETURN' => 'fee',
            'TRANSACTION_OFFER_ABORTED_RETURN' => 'trade',
            'TRANSACTION_OFFER_COMPLETED_RETURN' => 'trade',
            'TRANSACTION_POST_INCOME' => 'trade',
            'TRANSACTION_POST_OUTCOME' => 'trade',
            'TRANSACTION_PRE_LOCKING' => 'trade',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         '1582399800000',
        //         {
        //             o => '0.0001428',
        //             c => '0.0001428',
        //             h => '0.0001428',
        //             l => '0.0001428',
        //             v => '4',
        //             co => '1'
        //         }
        //     )
        //
        $first = $this->safe_value($ohlcv, 1, array());
        return array(
            $this->safe_integer($ohlcv, 0),
            $this->safe_number($first, 'o'),
            $this->safe_number($first, 'h'),
            $this->safe_number($first, 'l'),
            $this->safe_number($first, 'c'),
            $this->safe_number($first, 'v'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $tradingSymbol = $market['baseId'] . '-' . $market['quoteId'];
        $request = array(
            'symbol' => $tradingSymbol,
            'resolution' => $this->timeframes[$timeframe],
            // 'from' => 1574709092000, // unix timestamp in milliseconds, required
            // 'to' => 1574709092000, // unix timestamp in milliseconds, required
        );
        if ($limit === null) {
            $limit = 100;
        }
        $duration = $this->parse_timeframe($timeframe);
        $timerange = $limit * $duration * 1000;
        if ($since === null) {
            $request['to'] = $this->milliseconds();
            $request['from'] = $request['to'] - $timerange;
        } else {
            $request['from'] = intval($since);
            $request['to'] = $this->sum($request['from'], $timerange);
        }
        $response = $this->v1_01PublicGetTradingCandleHistorySymbolResolution (array_merge($request, $params));
        //
        //     {
        //         "status":"Ok",
        //         "$items":[
        //             ["1591503060000",array("o":"0.02509572","c":"0.02509438","h":"0.02509664","l":"0.02509438","v":"0.02082165","co":"17")],
        //             ["1591503120000",array("o":"0.02509606","c":"0.02509515","h":"0.02509606","l":"0.02509487","v":"0.04971703","co":"13")],
        //             ["1591503180000",array("o":"0.02509532","c":"0.02509589","h":"0.02509589","l":"0.02509454","v":"0.01332236","co":"7")],
        //         ]
        //     }
        //
        $items = $this->safe_value($response, 'items', array());
        return $this->parse_ohlcvs($items, $market, $timeframe, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        //
        // createOrder trades
        //
        //     {
        //         "rate" => "0.02195928",
        //         "$amount" => "0.00167952"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         $amount => "0.29285199",
        //         commissionValue => "0.00125927",
        //         id => "11c8203a-a267-11e9-b698-0242ac110007",
        //         initializedBy => "Buy",
        //         $market => "ETH-EUR",
        //         offerId => "11c82038-a267-11e9-b698-0242ac110007",
        //         rate => "277",
        //         time => "1562689917517",
        //         $userAction => "Buy",
        //         $wasTaker => true,
        //     }
        //
        // fetchTrades (public)
        //
        //     {
        //          id => 'df00b0da-e5e0-11e9-8c19-0242ac11000a',
        //          t => '1570108958831',
        //          a => '0.04776653',
        //          r => '0.02145854',
        //          ty => 'Sell'
        //     }
        //
        $timestamp = $this->safe_integer_2($trade, 'time', 't');
        $userAction = $this->safe_string($trade, 'userAction');
        $side = ($userAction === 'Buy') ? 'buy' : 'sell';
        $wasTaker = $this->safe_value($trade, 'wasTaker');
        $takerOrMaker = null;
        if ($wasTaker !== null) {
            $takerOrMaker = $wasTaker ? 'taker' : 'maker';
        }
        $price = $this->safe_number_2($trade, 'rate', 'r');
        $amount = $this->safe_number_2($trade, 'amount', 'a');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $price * $amount;
            }
        }
        $feeCost = $this->safe_number($trade, 'commissionValue');
        $marketId = $this->safe_string($trade, 'market');
        $market = $this->safe_market($marketId, $market, '-');
        $symbol = $market['symbol'];
        $fee = null;
        if ($feeCost !== null) {
            $feeCcy = ($side === 'buy') ? $market['base'] : $market['quote'];
            $fee = array(
                'currency' => $feeCcy,
                'cost' => $feeCost,
            );
        }
        $order = $this->safe_string($trade, 'offerId');
        // todo => check this logic
        $type = null;
        if ($order !== null) {
            $type = $order ? 'limit' : 'market';
        }
        return array(
            'id' => $this->safe_string($trade, 'id'),
            'order' => $order,
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
            'info' => $trade,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $tradingSymbol = $market['baseId'] . '-' . $market['quoteId'];
        $request = array(
            'symbol' => $tradingSymbol,
        );
        if ($since !== null) {
            $request['fromTime'] = $since - 1; // result does not include exactly `$since` time therefore decrease by 1
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default - 10, max - 300
        }
        $response = $this->v1_01PublicGetTradingTransactionsSymbol (array_merge($request, $params));
        $items = $this->safe_value($response, 'items');
        return $this->parse_trades($items, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $tradingSymbol = $market['baseId'] . '-' . $market['quoteId'];
        $request = array(
            'symbol' => $tradingSymbol,
            'offerType' => $side,
            'amount' => $amount,
            'mode' => $type,
        );
        if ($type === 'limit') {
            $request['rate'] = $price;
            $price = floatval($price);
        }
        $amount = floatval($amount);
        $response = $this->v1_01PrivatePostTradingOfferSymbol (array_merge($request, $params));
        //
        // unfilled (open order)
        //
        //     {
        //         $status => 'Ok',
        //         $completed => false, // can deduce $status from here
        //         offerId => 'ce9cc72e-d61c-11e9-9248-0242ac110005',
        //         $transactions => array(), // can deduce order info from here
        //     }
        //
        // $filled (closed order)
        //
        //     {
        //         "$status" => "Ok",
        //         "offerId" => "942a4a3e-e922-11e9-8c19-0242ac11000a",
        //         "$completed" => true,
        //         "$transactions" => array(
        //           array(
        //             "rate" => "0.02195928",
        //             "$amount" => "0.00167952"
        //           ),
        //           array(
        //             "rate" => "0.02195928",
        //             "$amount" => "0.00167952"
        //           ),
        //           {
        //             "rate" => "0.02196207",
        //             "$amount" => "0.27704177"
        //           }
        //         )
        //     }
        //
        // partially-$filled (open order)
        //
        //     {
        //         "$status" => "Ok",
        //         "offerId" => "d0ebefab-f4d7-11e9-8c19-0242ac11000a",
        //         "$completed" => false,
        //         "$transactions" => array(
        //           array(
        //             "rate" => "0.02106404",
        //             "$amount" => "0.0019625"
        //           ),
        //           array(
        //             "rate" => "0.02106404",
        //             "$amount" => "0.0019625"
        //           ),
        //           {
        //             "rate" => "0.02105901",
        //             "$amount" => "0.00975256"
        //           }
        //         )
        //     }
        //
        $timestamp = $this->milliseconds(); // the real $timestamp is missing in the $response
        $id = $this->safe_string($response, 'offerId');
        $completed = $this->safe_value($response, 'completed', false);
        $status = $completed ? 'closed' : 'open';
        $filled = 0;
        $cost = null;
        $transactions = $this->safe_value($response, 'transactions');
        $trades = null;
        if ($transactions !== null) {
            $trades = $this->parse_trades($transactions, $market, null, null, array(
                'timestamp' => $timestamp,
                'datetime' => $this->iso8601($timestamp),
                'symbol' => $symbol,
                'side' => $side,
                'type' => $type,
                'orderId' => $id,
            ));
            $cost = 0;
            for ($i = 0; $i < count($trades); $i++) {
                $filled = $this->sum($filled, $trades[$i]['amount']);
                $cost = $this->sum($cost, $trades[$i]['cost']);
            }
        }
        $remaining = $amount - $filled;
        return array(
            'id' => $id,
            'info' => $response,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'filled' => $filled,
            'remaining' => $remaining,
            'average' => null,
            'fee' => null,
            'trades' => $trades,
            'clientOrderId' => null,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $side = $this->safe_string($params, 'side');
        if ($side === null) {
            throw new ExchangeError($this->id . ' cancelOrder() requires a `$side` parameter ("buy" or "sell")');
        }
        $price = $this->safe_value($params, 'price');
        if ($price === null) {
            throw new ExchangeError($this->id . ' cancelOrder() requires a `$price` parameter (float or string)');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $tradingSymbol = $market['baseId'] . '-' . $market['quoteId'];
        $request = array(
            'symbol' => $tradingSymbol,
            'id' => $id,
            'side' => $side,
            'price' => $price,
        );
        // array( status => 'Fail', errors => array( 'NOT_RECOGNIZED_OFFER_TYPE' ) )  -- if required $params are missing
        // array( status => 'Ok', errors => array() )
        return $this->v1_01PrivateDeleteTradingOfferSymbolIdSidePrice (array_merge($request, $params));
    }

    public function is_fiat($currency) {
        $fiatCurrencies = array(
            'USD' => true,
            'EUR' => true,
            'PLN' => true,
        );
        return $this->safe_value($fiatCurrencies, $currency, false);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $method = null;
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'quantity' => $amount,
        );
        if ($this->is_fiat($code)) {
            $method = 'privatePostWithdraw';
            // $request['account'] = $params['account']; // they demand an account number
            // $request['express'] = $params['express']; // whatever it means, they don't explain
            // $request['bic'] = '';
        } else {
            $method = 'privatePostTransfer';
            if ($tag !== null) {
                $address .= '?dt=' . (string) $tag;
            }
            $request['address'] = $address;
        }
        $response = $this->$method (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => null,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        if ($api === 'public') {
            $query = $this->omit($params, $this->extract_params($path));
            $url .= '/' . $this->implode_params($path, $params) . '.json';
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($api === 'v1_01Public') {
            $query = $this->omit($params, $this->extract_params($path));
            $url .= '/' . $this->implode_params($path, $params);
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else if ($api === 'v1_01Private') {
            $this->check_required_credentials();
            $query = $this->omit($params, $this->extract_params($path));
            $url .= '/' . $this->implode_params($path, $params);
            $nonce = (string) $this->milliseconds();
            $payload = null;
            if ($method !== 'POST') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
                $payload = $this->apiKey . $nonce;
            } else if ($body === null) {
                $body = $this->json($query);
                $payload = $this->apiKey . $nonce . $body;
            }
            $headers = array(
                'Request-Timestamp' => $nonce,
                'Operation-Id' => $this->uuid(),
                'API-Key' => $this->apiKey,
                'API-Hash' => $this->hmac($this->encode($payload), $this->encode($this->secret), 'sha512'),
                'Content-Type' => 'application/json',
            );
        } else {
            $this->check_required_credentials();
            $body = $this->urlencode(array_merge(array(
                'method' => $path,
                'moment' => $this->nonce(),
            ), $params));
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'API-Key' => $this->apiKey,
                'API-Hash' => $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        if (is_array($response) && array_key_exists('code', $response)) {
            //
            // bitbay returns the integer 'success' => 1 key from their private API
            // or an integer 'code' value from 0 to 510 and an $error message
            //
            //      array( 'success' => 1, ... )
            //      array( 'code' => 502, 'message' => 'Invalid sign' )
            //      array( 'code' => 0, 'message' => 'offer funds not exceeding minimums' )
            //
            //      400 At least one parameter wasn't set
            //      401 Invalid order type
            //      402 No orders with specified currencies
            //      403 Invalid payment currency name
            //      404 Error. Wrong transaction type
            //      405 Order with this id doesn't exist
            //      406 No enough money or crypto
            //      408 Invalid currency name
            //      501 Invalid public key
            //      502 Invalid sign
            //      503 Invalid moment parameter. Request time doesn't match current server time
            //      504 Invalid $method
            //      505 Key has no permission for this action
            //      506 Account locked. Please contact with customer service
            //      509 The BIC/SWIFT is required for this currency
            //      510 Invalid market name
            //
            $code = $this->safe_string($response, 'code'); // always an integer
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions, $code, $feedback);
            throw new ExchangeError($feedback);
        } else if (is_array($response) && array_key_exists('status', $response)) {
            //
            //      array("$status":"Fail","$errors":["OFFER_FUNDS_NOT_EXCEEDING_MINIMUMS"])
            //
            $status = $this->safe_string($response, 'status');
            if ($status === 'Fail') {
                $errors = $this->safe_value($response, 'errors');
                $feedback = $this->id . ' ' . $body;
                for ($i = 0; $i < count($errors); $i++) {
                    $error = $errors[$i];
                    $this->throw_exactly_matched_exception($this->exceptions, $error, $feedback);
                }
                throw new ExchangeError($feedback);
            }
        }
    }
}

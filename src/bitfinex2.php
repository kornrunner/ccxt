<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InsufficientFunds;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;

class bitfinex2 extends bitfinex {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitfinex2',
            'name' => 'Bitfinex',
            'countries' => array( 'VG' ),
            'version' => 'v2',
            'certified' => false,
            'pro' => false,
            // new metainfo interface
            'has' => array(
                'CORS' => false,
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'createDepositAddress' => true,
                'createLimitOrder' => true,
                'createMarketOrder' => true,
                'createOrder' => true,
                'deposit' => false,
                'editOrder' => false,
                'fetchBalance' => true,
                'fetchClosedOrder' => true,
                'fetchClosedOrders' => false,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchFundingFees' => false,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrder' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => false,
                'fetchOrderTrades' => true,
                'fetchStatus' => true,
                'fetchTickers' => true,
                'fetchTradingFee' => false,
                'fetchTradingFees' => false,
                'fetchTransactions' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '3h' => '3h',
                '6h' => '6h',
                '12h' => '12h',
                '1d' => '1D',
                '1w' => '7D',
                '2w' => '14D',
                '1M' => '1M',
            ),
            'rateLimit' => 1500,
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766244-e328a50c-5ed2-11e7-947b-041416579bb3.jpg',
                'api' => array(
                    'v1' => 'https://api.bitfinex.com',
                    'public' => 'https://api-pub.bitfinex.com',
                    'private' => 'https://api.bitfinex.com',
                ),
                'www' => 'https://www.bitfinex.com',
                'doc' => array(
                    'https://docs.bitfinex.com/v2/docs/',
                    'https://github.com/bitfinexcom/bitfinex-api-node',
                ),
                'fees' => 'https://www.bitfinex.com/fees',
            ),
            'api' => array(
                'v1' => array(
                    'get' => array(
                        'symbols',
                        'symbols_details',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'conf/{config}',
                        'conf/pub:{action}:{object}',
                        'conf/pub:{action}:{object}:{detail}',
                        'conf/pub:map:{object}',
                        'conf/pub:map:{object}:{detail}',
                        'conf/pub:map:currency:{detail}',
                        'conf/pub:map:currency:sym', // maps symbols to their API symbols, BAB > BCH
                        'conf/pub:map:currency:label', // verbose friendly names, BNT > Bancor
                        'conf/pub:map:currency:unit', // maps symbols to unit of measure where applicable
                        'conf/pub:map:currency:undl', // maps derivatives symbols to their underlying currency
                        'conf/pub:map:currency:pool', // maps symbols to underlying network/protocol they operate on
                        'conf/pub:map:currency:explorer', // maps symbols to their recognised block explorer URLs
                        'conf/pub:map:currency:tx:fee', // maps currencies to their withdrawal fees https://github.com/ccxt/ccxt/issues/7745
                        'conf/pub:map:tx:method',
                        'conf/pub:list:{object}',
                        'conf/pub:list:{object}:{detail}',
                        'conf/pub:list:currency',
                        'conf/pub:list:pair:exchange',
                        'conf/pub:list:pair:margin',
                        'conf/pub:list:pair:futures',
                        'conf/pub:list:competitions',
                        'conf/pub:info:{object}',
                        'conf/pub:info:{object}:{detail}',
                        'conf/pub:info:pair',
                        'conf/pub:info:tx:status', // array( deposit, withdrawal ) statuses 1 = active, 0 = maintenance
                        'conf/pub:fees',
                        'platform/status',
                        'tickers',
                        'ticker/{symbol}',
                        'trades/{symbol}/hist',
                        'book/{symbol}/{precision}',
                        'book/{symbol}/P0',
                        'book/{symbol}/P1',
                        'book/{symbol}/P2',
                        'book/{symbol}/P3',
                        'book/{symbol}/R0',
                        'stats1/{key}:{size}:{symbol}:{side}/{section}',
                        'stats1/{key}:{size}:{symbol}:{side}/last',
                        'stats1/{key}:{size}:{symbol}:{side}/hist',
                        'stats1/{key}:{size}:{symbol}/{section}',
                        'stats1/{key}:{size}:{symbol}/last',
                        'stats1/{key}:{size}:{symbol}/hist',
                        'stats1/{key}:{size}:{symbol}:long/last',
                        'stats1/{key}:{size}:{symbol}:long/hist',
                        'stats1/{key}:{size}:{symbol}:short/last',
                        'stats1/{key}:{size}:{symbol}:short/hist',
                        'candles/trade:{timeframe}:{symbol}/{section}',
                        'candles/trade:{timeframe}:{symbol}/last',
                        'candles/trade:{timeframe}:{symbol}/hist',
                        'status/{type}',
                        'status/deriv',
                        'liquidations/hist',
                        'rankings/{key}:{timeframe}:{symbol}/{section}',
                        'rankings/{key}:{timeframe}:{symbol}/hist',
                    ),
                    'post' => array(
                        'calc/trade/avg',
                        'calc/fx',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        // 'auth/r/orders/{symbol}/new', // outdated
                        // 'auth/r/stats/perf:{timeframe}/hist', // outdated
                        'auth/r/wallets',
                        'auth/r/wallets/hist',
                        'auth/r/orders',
                        'auth/r/orders/{symbol}',
                        'auth/w/order/submit',
                        'auth/w/order/update',
                        'auth/w/order/cancel',
                        'auth/w/order/multi',
                        'auth/w/order/cancel/multi',
                        'auth/r/orders/{symbol}/hist',
                        'auth/r/orders/hist',
                        'auth/r/order/{symbol}:{id}/trades',
                        'auth/r/trades/{symbol}/hist',
                        'auth/r/trades/hist',
                        'auth/r/ledgers/{currency}/hist',
                        'auth/r/ledgers/hist',
                        'auth/r/info/margin/{key}',
                        'auth/r/info/margin/base',
                        'auth/r/info/margin/sym_all',
                        'auth/r/positions',
                        'auth/w/position/claim',
                        'auth/r/positions/hist',
                        'auth/r/positions/audit',
                        'auth/w/deriv/collateral/set',
                        'auth/w/deriv/collateral/limits',
                        'auth/r/funding/offers',
                        'auth/r/funding/offers/{symbol}',
                        'auth/w/funding/offer/submit',
                        'auth/w/funding/offer/cancel',
                        'auth/w/funding/offer/cancel/all',
                        'auth/w/funding/close',
                        'auth/w/funding/auto',
                        'auth/w/funding/keep',
                        'auth/r/funding/offers/{symbol}/hist',
                        'auth/r/funding/offers/hist',
                        'auth/r/funding/loans',
                        'auth/r/funding/loans/hist',
                        'auth/r/funding/loans/{symbol}',
                        'auth/r/funding/loans/{symbol}/hist',
                        'auth/r/funding/credits',
                        'auth/r/funding/credits/hist',
                        'auth/r/funding/credits/{symbol}',
                        'auth/r/funding/credits/{symbol}/hist',
                        'auth/r/funding/trades/{symbol}/hist',
                        'auth/r/funding/trades/hist',
                        'auth/r/info/funding/{key}',
                        'auth/r/info/user',
                        'auth/r/logins/hist',
                        'auth/w/transfer',
                        'auth/w/deposit/address',
                        'auth/w/deposit/invoice',
                        'auth/w/withdraw',
                        'auth/r/movements/{currency}/hist',
                        'auth/r/movements/hist',
                        'auth/r/alerts',
                        'auth/w/alert/set',
                        'auth/w/alert/price:{symbol}:{price}/del',
                        'auth/w/alert/{type}:{symbol}:{price}/del',
                        'auth/calc/order/avail',
                        'auth/w/settings/set',
                        'auth/r/settings',
                        'auth/w/settings/del',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.1 / 100,
                    'taker' => 0.2 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => 0.0004,
                        'BCH' => 0.0001,
                        'ETH' => 0.00135,
                        'EOS' => 0.0,
                        'LTC' => 0.001,
                        'OMG' => 0.15097,
                        'IOT' => 0.0,
                        'NEO' => 0.0,
                        'ETC' => 0.01,
                        'XRP' => 0.02,
                        'ETP' => 0.01,
                        'ZEC' => 0.001,
                        'BTG' => 0.0,
                        'DASH' => 0.01,
                        'XMR' => 0.0001,
                        'QTM' => 0.01,
                        'EDO' => 0.23687,
                        'DAT' => 9.8858,
                        'AVT' => 1.1251,
                        'SAN' => 0.35977,
                        'USDT' => 5.0,
                        'SPK' => 16.971,
                        'BAT' => 1.1209,
                        'GNT' => 2.8789,
                        'SNT' => 9.0848,
                        'QASH' => 1.726,
                        'YYW' => 7.9464,
                    ),
                ),
            ),
            'options' => array(
                'precision' => 'R0', // P0, P1, P2, P3, P4, R0
                // convert 'EXCHANGE MARKET' to lowercase 'market'
                // convert 'EXCHANGE LIMIT' to lowercase 'limit'
                // everything else remains uppercase
                'exchangeTypes' => array(
                    // 'MARKET' => null,
                    'EXCHANGE MARKET' => 'market',
                    // 'LIMIT' => null,
                    'EXCHANGE LIMIT' => 'limit',
                    // 'STOP' => null,
                    // 'EXCHANGE STOP' => null,
                    // 'TRAILING STOP' => null,
                    // 'EXCHANGE TRAILING STOP' => null,
                    // 'FOK' => null,
                    // 'EXCHANGE FOK' => null,
                    // 'STOP LIMIT' => null,
                    // 'EXCHANGE STOP LIMIT' => null,
                    // 'IOC' => null,
                    // 'EXCHANGE IOC' => null,
                ),
                // convert 'market' to 'EXCHANGE MARKET'
                // convert 'limit' 'EXCHANGE LIMIT'
                // everything else remains as is
                'orderTypes' => array(
                    'market' => 'EXCHANGE MARKET',
                    'limit' => 'EXCHANGE LIMIT',
                ),
                'fiat' => array(
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'JPY' => 'JPY',
                    'GBP' => 'GBP',
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    '10020' => '\\ccxt\\BadRequest',
                    '10100' => '\\ccxt\\AuthenticationError',
                    '10114' => '\\ccxt\\InvalidNonce',
                    '20060' => '\\ccxt\\OnMaintenance',
                ),
                'broad' => array(
                    'address' => '\\ccxt\\InvalidAddress',
                    'available balance is only' => '\\ccxt\\InsufficientFunds',
                    'not enough exchange balance' => '\\ccxt\\InsufficientFunds',
                    'Order not found' => '\\ccxt\\OrderNotFound',
                    'symbol => invalid' => '\\ccxt\\BadSymbol',
                    'Invalid order' => '\\ccxt\\InvalidOrder',
                ),
            ),
        ));
    }

    public function is_fiat($code) {
        return (is_array($this->options['fiat']) && array_key_exists($code, $this->options['fiat']));
    }

    public function get_currency_id($code) {
        return 'f' . $code;
    }

    public function fetch_status($params = array ()) {
        //
        //    [1] // operative
        //    [0] // maintenance
        //
        $response = $this->publicGetPlatformStatus ($params);
        $status = $this->safe_value($response, 0);
        $formattedStatus = ($status === 1) ? 'ok' : 'maintenance';
        $this->status = array_merge($this->status, array(
            'status' => $formattedStatus,
            'updated' => $this->milliseconds(),
        ));
        return $this->status;
    }

    public function fetch_markets($params = array ()) {
        // todo drop v1 in favor of v2 configs
        // pub:list:pair:exchange,pub:list:pair:margin,pub:list:pair:$futures,pub:info:pair
        $v2response = $this->publicGetConfPubListPairFutures ($params);
        $v1response = $this->v1GetSymbolsDetails ($params);
        $futuresMarketIds = $this->safe_value($v2response, 0, array());
        $result = array();
        for ($i = 0; $i < count($v1response); $i++) {
            $market = $v1response[$i];
            $id = $this->safe_string_upper($market, 'pair');
            $spot = true;
            if ($this->in_array($id, $futuresMarketIds)) {
                $spot = false;
            }
            $futures = !$spot;
            $type = $spot ? 'spot' : 'futures';
            $baseId = null;
            $quoteId = null;
            if (mb_strpos($id, ':') !== false) {
                $parts = explode(':', $id);
                $baseId = $parts[0];
                $quoteId = $parts[1];
            } else {
                $baseId = mb_substr($id, 0, 3 - 0);
                $quoteId = mb_substr($id, 3, 6 - 3);
            }
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $id = 't' . $id;
            $baseId = $this->get_currency_id($baseId);
            $quoteId = $this->get_currency_id($quoteId);
            $precision = array(
                'price' => $this->safe_integer($market, 'price_precision'),
                'amount' => 8, // https://github.com/ccxt/ccxt/issues/7310
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'minimum_order_size'),
                    'max' => $this->safe_float($market, 'maximum_order_size'),
                ),
                'price' => array(
                    'min' => pow(10, -$precision['price']),
                    'max' => pow(10, $precision['price']),
                ),
            );
            $limits['cost'] = array(
                'min' => $limits['amount']['min'] * $limits['price']['min'],
                'max' => null,
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
                'type' => $type,
                'swap' => false,
                'spot' => $spot,
                'futures' => $futures,
            );
        }
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $labels = array(
            'pub:list:currency',
            'pub:map:currency:sym', // maps symbols to their API symbols, BAB > BCH
            'pub:map:currency:label', // verbose friendly names, BNT > Bancor
            'pub:map:currency:unit', // maps symbols to unit of measure where applicable
            'pub:map:currency:undl', // maps derivatives symbols to their underlying currency
            'pub:map:currency:pool', // maps symbols to underlying network/protocol they operate on
            'pub:map:currency:explorer', // maps symbols to their recognised block explorer URLs
            'pub:map:currency:tx:fee', // maps currencies to their withdrawal $fees https://github.com/ccxt/ccxt/issues/7745
        );
        $config = implode(',', $labels);
        $request = array(
            'config' => $config,
        );
        $response = $this->publicGetConfConfig (array_merge($request, $params));
        //
        //     [
        //
        //         a list of symbols
        //         ["AAA","ABS","ADA"],
        //
        //         // sym
        //         // maps symbols to their API symbols, BAB > BCH
        //         array(
        //             array( 'BAB', 'BCH' ),
        //             array( 'CNHT', 'CNHt' ),
        //             array( 'DSH', 'DASH' ),
        //             array( 'IOT', 'IOTA' ),
        //             array( 'LES', 'LEO-EOS' ),
        //             array( 'LET', 'LEO-ERC20' ),
        //             array( 'STJ', 'STORJ' ),
        //             array( 'TSD', 'TUSD' ),
        //             array( 'UDC', 'USDC' ),
        //             array( 'USK', 'USDK' ),
        //             array( 'UST', 'USDt' ),
        //             array( 'USTF0', 'USDt0' ),
        //             array( 'XCH', 'XCHF' ),
        //             array( 'YYW', 'YOYOW' ),
        //             // ...
        //         ),
        //         // $label
        //         // verbose friendly names, BNT > Bancor
        //         array(
        //             array( 'BAB', 'Bitcoin Cash' ),
        //             array( 'BCH', 'Bitcoin Cash' ),
        //             array( 'LEO', 'Unus Sed LEO' ),
        //             array( 'LES', 'Unus Sed LEO (EOS)' ),
        //             array( 'LET', 'Unus Sed LEO (ERC20)' ),
        //             // ...
        //         ),
        //         // unit
        //         // maps symbols to unit of measure where applicable
        //         array(
        //             array( 'IOT', 'Mi|MegaIOTA' ),
        //         ),
        //         // undl
        //         // maps derivatives symbols to their underlying currency
        //         array(
        //             array( 'USTF0', 'UST' ),
        //             array( 'BTCF0', 'BTC' ),
        //             array( 'ETHF0', 'ETH' ),
        //         ),
        //         // $pool
        //         // maps symbols to underlying network/protocol they operate on
        //         array(
        //             array( 'SAN', 'ETH' ), array( 'OMG', 'ETH' ), array( 'AVT', 'ETH' ), array( 'EDO', 'ETH' ),
        //             array( 'ESS', 'ETH' ), array( 'ATD', 'EOS' ), array( 'ADD', 'EOS' ), array( 'MTO', 'EOS' ),
        //             array( 'PNK', 'ETH' ), array( 'BAB', 'BCH' ), array( 'WLO', 'XLM' ), array( 'VLD', 'ETH' ),
        //             array( 'BTT', 'TRX' ), array( 'IMP', 'ETH' ), array( 'SCR', 'ETH' ), array( 'GNO', 'ETH' ),
        //             // ...
        //         ),
        //         // explorer
        //         // maps symbols to their recognised block explorer URLs
        //         array(
        //             array(
        //                 'AIO',
        //                 array(
        //                     "https://mainnet.aion.network",
        //                     "https://mainnet.aion.network/#/account/VAL",
        //                     "https://mainnet.aion.network/#/transaction/VAL"
        //                 )
        //             ),
        //             // ...
        //         ),
        //         // $fee
        //         // maps currencies to their withdrawal $fees
        //         [
        //             ["AAA",[0,0]],
        //             ["ABS",[0,131.3]],
        //             ["ADA",[0,0.3]],
        //         ],
        //     ]
        //
        $indexed = array(
            'sym' => $this->index_by($this->safe_value($response, 1, array()), 0),
            'label' => $this->index_by($this->safe_value($response, 2, array()), 0),
            'unit' => $this->index_by($this->safe_value($response, 3, array()), 0),
            'undl' => $this->index_by($this->safe_value($response, 4, array()), 0),
            'pool' => $this->index_by($this->safe_value($response, 5, array()), 0),
            'explorer' => $this->index_by($this->safe_value($response, 6, array()), 0),
            'fees' => $this->index_by($this->safe_value($response, 7, array()), 0),
        );
        $ids = $this->safe_value($response, 0, array());
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $code = $this->safe_currency_code($id);
            $label = $this->safe_value($indexed['label'], $id, array());
            $name = $this->safe_string($label, 1);
            $pool = $this->safe_value($indexed['pool'], $id, array());
            $type = $this->safe_string($pool, 1);
            $feeValues = $this->safe_value($indexed['fees'], $id, array());
            $fees = $this->safe_value($feeValues, 1, array());
            $fee = $this->safe_float($fees, 1);
            $precision = 8; // default $precision, todo => fix "magic constants"
            $id = 'f' . $id;
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => array( $id, $label, $pool, $feeValues ),
                'type' => $type,
                'name' => $name,
                'active' => true,
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

    public function fetch_balance($params = array ()) {
        // this api call does not return the 'used' amount - use the v1 version instead (which also returns zero balances)
        $this->load_markets();
        $response = $this->privatePostAuthRWallets ($params);
        $balanceType = $this->safe_string($params, 'type', 'exchange');
        $result = array( 'info' => $response );
        for ($b = 0; $b < count($response); $b++) {
            $balance = $response[$b];
            $accountType = $balance[0];
            $currency = $balance[1];
            $total = $balance[2];
            $available = $balance[4];
            if ($accountType === $balanceType) {
                if ($currency[0] === 't') {
                    $currency = mb_substr($currency, 1);
                }
                $code = $this->safe_currency_code($currency);
                $account = $this->account();
                // do not fill in zeroes and missing values in the parser
                // rewrite and unify the following to use the unified parseBalance
                $account['total'] = $total;
                if (!$available) {
                    if ($available === 0) {
                        $account['free'] = 0;
                        $account['used'] = $total;
                    } else {
                        $account['free'] = $total;
                    }
                } else {
                    $account['free'] = $available;
                    $account['used'] = $account['total'] - $account['free'];
                }
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $precision = $this->safe_value($this->options, 'precision', 'R0');
        $request = array(
            'symbol' => $this->market_id($symbol),
            'precision' => $precision,
        );
        if ($limit !== null) {
            $request['len'] = $limit; // 25 or 100
        }
        $fullRequest = array_merge($request, $params);
        $orderbook = $this->publicGetBookSymbolPrecision ($fullRequest);
        $timestamp = $this->milliseconds();
        $result = array(
            'bids' => array(),
            'asks' => array(),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'nonce' => null,
        );
        $priceIndex = ($fullRequest['precision'] === 'R0') ? 1 : 0;
        for ($i = 0; $i < count($orderbook); $i++) {
            $order = $orderbook[$i];
            $price = $order[$priceIndex];
            $amount = abs($order[2]);
            $side = ($order[2] > 0) ? 'bids' : 'asks';
            $result[$side][] = array( $price, $amount );
        }
        $result['bids'] = $this->sort_by($result['bids'], 0, true);
        $result['asks'] = $this->sort_by($result['asks'], 0);
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $length = is_array($ticker) ? count($ticker) : 0;
        $last = $ticker[$length - 4];
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $ticker[$length - 2],
            'low' => $ticker[$length - 1],
            'bid' => $ticker[$length - 10],
            'bidVolume' => null,
            'ask' => $ticker[$length - 8],
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $ticker[$length - 6],
            'percentage' => $ticker[$length - 5] * 100,
            'average' => null,
            'baseVolume' => $ticker[$length - 3],
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbols !== null) {
            $ids = $this->market_ids($symbols);
            $request['symbols'] = implode(',', $ids);
        } else {
            $request['symbols'] = 'ALL';
        }
        $tickers = $this->publicGetTickers (array_merge($request, $params));
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $ticker = $tickers[$i];
            $id = $ticker[0];
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
                $result[$symbol] = $this->parse_ticker($ticker, $market);
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
        $ticker = $this->publicGetTickerSymbol (array_merge($request, $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_symbol($marketId) {
        if ($marketId === null) {
            return $marketId;
        }
        $marketId = str_replace('t', '', $marketId);
        $baseId = null;
        $quoteId = null;
        if (mb_strpos($marketId, ':') !== false) {
            $parts = explode(':', $marketId);
            $baseId = $parts[0];
            $quoteId = $parts[1];
        } else {
            $baseId = mb_substr($marketId, 0, 3 - 0);
            $quoteId = mb_substr($marketId, 3, 6 - 3);
        }
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        return $base . '/' . $quote;
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     array(
        //         ID,
        //         MTS, // $timestamp
        //         AMOUNT,
        //         PRICE
        //     )
        //
        // fetchMyTrades (private)
        //
        //     array(
        //         ID,
        //         PAIR,
        //         MTS_CREATE,
        //         ORDER_ID,
        //         EXEC_AMOUNT,
        //         EXEC_PRICE,
        //         ORDER_TYPE,
        //         ORDER_PRICE,
        //         MAKER,
        //         FEE,
        //         FEE_CURRENCY,
        //         ...
        //     )
        //
        $tradeLength = is_array($trade) ? count($trade) : 0;
        $isPrivate = ($tradeLength > 5);
        $id = (string) $trade[0];
        $amountIndex = $isPrivate ? 4 : 2;
        $amount = $trade[$amountIndex];
        $cost = null;
        $priceIndex = $isPrivate ? 5 : 3;
        $price = $trade[$priceIndex];
        $side = null;
        $orderId = null;
        $takerOrMaker = null;
        $type = null;
        $fee = null;
        $symbol = null;
        $timestampIndex = $isPrivate ? 2 : 1;
        $timestamp = $trade[$timestampIndex];
        if ($isPrivate) {
            $marketId = $trade[1];
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            } else {
                $symbol = $this->parse_symbol($marketId);
            }
            $orderId = (string) $trade[3];
            $takerOrMaker = ($trade[8] === 1) ? 'maker' : 'taker';
            $feeCost = $trade[9];
            $feeCurrency = $this->safe_currency_code($trade[10]);
            if ($feeCost !== null) {
                $feeCost = -$feeCost;
                if (is_array($this->markets) && array_key_exists($symbol, $this->markets)) {
                    $feeCost = $this->fee_to_precision($symbol, $feeCost);
                } else {
                    $currencyId = 'f' . $feeCurrency;
                    if (is_array($this->currencies_by_id) && array_key_exists($currencyId, $this->currencies_by_id)) {
                        $currency = $this->currencies_by_id[$currencyId];
                        $feeCost = $this->currency_to_precision($currency['code'], $feeCost);
                    }
                }
                $fee = array(
                    'cost' => floatval($feeCost),
                    'currency' => $feeCurrency,
                );
            }
            $orderType = $trade[6];
            $type = $this->safe_string($this->options['exchangeTypes'], $orderType);
        }
        if ($symbol === null) {
            if ($market !== null) {
                $symbol = $market['symbol'];
            }
        }
        if ($amount !== null) {
            $side = ($amount < 0) ? 'sell' : 'buy';
            $amount = abs($amount);
            if ($cost === null) {
                if ($price !== null) {
                    $cost = $amount * $price;
                }
            }
        }
        return array(
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'side' => $side,
            'type' => $type,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
            'info' => $trade,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $sort = '-1';
        $request = array(
            'symbol' => $market['id'],
        );
        if ($since !== null) {
            $request['start'] = $since;
            $sort = '1';
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 120, max 5000
        }
        $request['sort'] = $sort;
        $response = $this->publicGetTradesSymbolHist (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             ID,
        //             MTS, // timestamp
        //             AMOUNT,
        //             PRICE
        //         )
        //     )
        //
        $trades = $this->sort_by($response, 1);
        return $this->parse_trades($trades, $market, null, $limit);
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = 100, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 100; // default 100, max 5000
        }
        if ($since === null) {
            $since = $this->milliseconds() - $this->parse_timeframe($timeframe) * $limit * 1000;
        }
        $request = array(
            'symbol' => $market['id'],
            'timeframe' => $this->timeframes[$timeframe],
            'sort' => 1,
            'start' => $since,
            'limit' => $limit,
        );
        $response = $this->publicGetCandlesTradeTimeframeSymbolHist (array_merge($request, $params));
        //
        //     [
        //         [1591503840000,0.025069,0.025068,0.025069,0.025068,1.97828998],
        //         [1591504500000,0.025065,0.025065,0.025065,0.025065,1.0164],
        //         [1591504620000,0.025062,0.025062,0.025062,0.025062,0.5],
        //     ]
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function parse_order_status($status) {
        if ($status === null) {
            return $status;
        }
        $parts = explode(' ', $status);
        $state = $this->safe_string($parts, 0);
        $statuses = array(
            'ACTIVE' => 'open',
            'PARTIALLY' => 'open',
            'EXECUTED' => 'closed',
            'CANCELED' => 'canceled',
            'INSUFFICIENT' => 'canceled',
            'RSN_DUST' => 'rejected',
            'RSN_PAUSE' => 'rejected',
        );
        return $this->safe_string($statuses, $state, $status);
    }

    public function parse_order($order, $market = null) {
        $id = $this->safe_string($order, 0);
        $symbol = null;
        $marketId = $this->safe_string($order, 3);
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
        } else {
            $symbol = $this->parse_symbol($marketId);
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        // https://github.com/ccxt/ccxt/issues/6686
        // $timestamp = $this->safe_timestamp($order, 5);
        $timestamp = $this->safe_integer($order, 5);
        $remaining = abs($this->safe_float($order, 6));
        $amount = abs($this->safe_float($order, 7));
        $filled = $amount - $remaining;
        $side = ($order[7] < 0) ? 'sell' : 'buy';
        $orderType = $this->safe_string($order, 8);
        $type = $this->safe_string($this->safe_value($this->options, 'exchangeTypes'), $orderType);
        $status = null;
        $statusString = $this->safe_string($order, 13);
        if ($statusString !== null) {
            $parts = explode(' @ ', $statusString);
            $status = $this->parse_order_status($this->safe_string($parts, 0));
        }
        $price = $this->safe_float($order, 16);
        $average = $this->safe_float($order, 17);
        $cost = $price * $filled;
        $clientOrderId = $this->safe_string($order, 2);
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
        $orderTypes = $this->safe_value($this->options, 'orderTypes', array());
        $orderType = $this->safe_string_upper($orderTypes, $type, $type);
        $amount = ($side === 'sell') ? -$amount : $amount;
        $request = array(
            'symbol' => $market['id'],
            'type' => $orderType,
            'amount' => $this->number_to_string($amount),
        );
        if (($orderType === 'LIMIT') || ($orderType === 'EXCHANGE LIMIT')) {
            $request['price'] = $this->number_to_string($price);
        } else if (($orderType === 'STOP') || ($orderType === 'EXCHANGE STOP')) {
            $stopPrice = $this->safe_float($params, 'stopPrice', $price);
            $request['price'] = $this->number_to_string($stopPrice);
        } else if (($orderType === 'STOP LIMIT') || ($orderType === 'EXCHANGE STOP LIMIT')) {
            $priceAuxLimit = $this->safe_float($params, 'price_aux_limit');
            $stopPrice = $this->safe_float($params, 'stopPrice');
            if ($priceAuxLimit === null) {
                if ($stopPrice === null) {
                    throw new ArgumentsRequired($this->id . ' createOrder() requires a $stopPrice parameter or a price_aux_limit parameter for a ' . $orderType . ' order');
                } else {
                    $request['price_aux_limit'] = $this->number_to_string($price);
                }
            } else {
                $request['price_aux_limit'] = $this->number_to_string($priceAuxLimit);
                if ($stopPrice === null) {
                    $stopPrice = $price;
                }
            }
            $request['price'] = $this->number_to_string($stopPrice);
        } else if (($orderType === 'TRAILING STOP') || ($orderType === 'EXCHANGE TRAILING STOP')) {
            $priceTrailing = $this->safe_float($params, 'price_trailing');
            $request['price_trailing'] = $this->number_to_string($priceTrailing);
            $stopPrice = $this->safe_float($params, 'stopPrice', $price);
            $request['price'] = $this->number_to_string($stopPrice);
        } else if (($orderType === 'FOK') || ($orderType === 'EXCHANGE FOK') || ($orderType === 'IOC') || ($orderType === 'EXCHANGE IOC')) {
            $request['price'] = $this->number_to_string($price);
        }
        $params = $this->omit($params, array( 'stopPrice', 'price_aux_limit', 'price_trailing' ));
        $clientOrderId = $this->safe_value_2($params, 'cid', 'clientOrderId');
        if ($clientOrderId !== null) {
            $request['cid'] = $clientOrderId;
            $params = $this->omit($params, array( 'cid', 'clientOrderId' ));
        }
        $response = $this->privatePostAuthWOrderSubmit (array_merge($request, $params));
        //
        //     array(
        //         1578784364.748,    // Millisecond Time Stamp of the update
        //         "on-req",          // Purpose of notification ('on-req', 'oc-req', 'uca', 'fon-req', 'foc-req')
        //         null,              // Unique ID of the message
        //         null,              // Ignore
        //         array(
        //             array(
        //                 37271830598,           // Order ID
        //                 null,                  // Group ID
        //                 1578784364748,         // Client Order ID
        //                 "tBTCUST",             // Pair
        //                 1578784364748,         // Millisecond timestamp of creation
        //                 1578784364748,         // Millisecond timestamp of update
        //                 -0.005,                // Positive means buy, negative means sell
        //                 -0.005,                // Original $amount
        //                 "EXCHANGE LIMIT",      // Order $type (LIMIT, MARKET, STOP, TRAILING STOP, EXCHANGE MARKET, EXCHANGE LIMIT, EXCHANGE STOP, EXCHANGE TRAILING STOP, FOK, EXCHANGE FOK, IOC, EXCHANGE IOC)
        //                 null,                  // Previous $order $type
        //                 null,                  // Millisecond timestamp of Time-In-Force => automatic $order cancellation
        //                 null,                  // Ignore
        //                 0,                     // Flags (see https://docs.bitfinex.com/docs/flag-values)
        //                 "ACTIVE",              // Order Status
        //                 null,                  // Ignore
        //                 null,                  // Ignore
        //                 20000,                 // Price
        //                 0,                     // Average $price
        //                 0,                     // The trailing $price
        //                 0,                     // Auxiliary Limit $price (for STOP LIMIT)
        //                 null,                  // Ignore
        //                 null,                  // Ignore
        //                 null,                  // Ignore
        //                 0,                     // 1 - hidden $order
        //                 null,                  // If another $order caused this $order to be placed (OCO) this will be that other order's ID
        //                 null,                  // Ignore
        //                 null,                  // Ignore
        //                 null,                  // Ignore
        //                 "API>BFX",             // Origin of action => BFX, ETHFX, API>BFX, API>ETHFX
        //                 null,                  // Ignore
        //                 null,                  // Ignore
        //                 null                   // Meta
        //             )
        //         ),
        //         null,                  // Error code
        //         "SUCCESS",             // Status (SUCCESS, ERROR, FAILURE, ...)
        //         "Submitting 1 $orders->" // Text of the notification
        //     )
        //
        $status = $this->safe_string($response, 6);
        if ($status !== 'SUCCESS') {
            $errorCode = $response[5];
            $errorText = $response[7];
            throw new ExchangeError($this->id . ' ' . $response[6] . ' => ' . $errorText . ' (#' . $errorCode . ')');
        }
        $orders = $this->safe_value($response, 4, array());
        $order = $this->safe_value($orders, 0);
        return $this->parse_order($order, $market);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $request = array(
            'all' => 1,
        );
        $response = $this->privatePostAuthWOrderCancelMulti (array_merge($request, $params));
        $orders = $this->safe_value($response, 4, array());
        return $this->parse_orders($orders);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $cid = $this->safe_value_2($params, 'cid', 'clientOrderId'); // client $order $id
        $request = null;
        if ($cid !== null) {
            $cidDate = $this->safe_value($params, 'cidDate'); // client $order $id date
            if ($cidDate === null) {
                throw new InvalidOrder($this->id . " canceling an $order by clientOrderId ('cid') requires both 'cid' and 'cid_date' ('YYYY-MM-DD')");
            }
            $request = array(
                'cid' => $cid,
                'cid_date' => $cidDate,
            );
            $params = $this->omit($params, array( 'cid', 'clientOrderId' ));
        } else {
            $request = array(
                'id' => intval($id),
            );
        }
        $response = $this->privatePostAuthWOrderCancel (array_merge($request, $params));
        $order = $this->safe_value($response, 4);
        return $this->parse_order($order);
    }

    public function fetch_open_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => array( intval($id) ),
        );
        $orders = $this->fetch_open_orders($symbol, null, null, array_merge($request, $params));
        $order = $this->safe_value($orders, 0);
        if ($order === null) {
            throw new OrderNotFound($this->id . ' $order ' . $id . ' not found');
        }
        return $order;
    }

    public function fetch_closed_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'id' => array( intval($id) ),
        );
        $orders = $this->fetch_closed_orders($symbol, null, null, array_merge($request, $params));
        $order = $this->safe_value($orders, 0);
        if ($order === null) {
            throw new OrderNotFound($this->id . ' $order ' . $id . ' not found');
        }
        return $order;
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        $response = null;
        if ($symbol === null) {
            $response = $this->privatePostAuthROrders (array_merge($request, $params));
        } else {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
            $response = $this->privatePostAuthROrdersSymbol (array_merge($request, $params));
        }
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        // returns the most recent closed or canceled orders up to circa two weeks ago
        $this->load_markets();
        $request = array();
        $market = null;
        $response = null;
        if ($symbol === null) {
            $response = $this->privatePostAuthROrdersHist (array_merge($request, $params));
        } else {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
            $response = $this->privatePostAuthROrdersSymbolHist (array_merge($request, $params));
        }
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 25, max 2500
        }
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrderTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $orderId = intval($id);
        $request = array(
            'id' => $orderId,
            'symbol' => $market['id'],
        );
        // valid for trades upto 10 days old
        $response = $this->privatePostAuthROrderSymbolIdTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array(
            'end' => $this->milliseconds(),
        );
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 25, max 1000
        }
        $method = 'privatePostAuthRTradesHist';
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
            $method = 'privatePostAuthRTradesSymbolHist';
        }
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $request = array(
            'op_renew' => 1,
        );
        $response = $this->fetch_deposit_address($code, array_merge($request, $params));
        return $response;
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        // todo rewrite for https://api-pub.bitfinex.com//v2/conf/pub:map:tx:method
        $name = $this->getCurrencyName ($code);
        $request = array(
            'method' => $name,
            'wallet' => 'exchange', // 'exchange', 'margin', 'funding' and also old labels 'exchange', 'trading', 'deposit', respectively
            'op_renew' => 0, // a value of 1 will generate a new $address
        );
        $response = $this->privatePostAuthWDepositAddress (array_merge($request, $params));
        //
        //     array(
        //         1582269616687, // MTS Millisecond Time Stamp of the update
        //         'acc_dep', // TYPE Purpose of notification 'acc_dep' for account deposit
        //         null, // MESSAGE_ID unique ID of the message
        //         null, // not documented
        //         array(
        //             null, // PLACEHOLDER
        //             'BITCOIN', // METHOD Method of deposit
        //             'BTC', // CURRENCY_CODE Currency $code of new $address
        //             null, // PLACEHOLDER
        //             '1BC9PZqpUmjyEB54uggn8TFKj49zSDYzqG', // ADDRESS
        //             null, // POOL_ADDRESS
        //         ),
        //         null, // CODE null or integer work in progress
        //         'SUCCESS', // STATUS Status of the notification, SUCCESS, ERROR, FAILURE
        //         'success', // TEXT Text of the notification
        //     )
        //
        $result = $this->safe_value($response, 4, array());
        $poolAddress = $this->safe_string($result, 5);
        $address = ($poolAddress === null) ? $this->safe_string($result, 4) : $poolAddress;
        $tag = ($poolAddress === null) ? null : $this->safe_string($result, 4);
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
            'SUCCESS' => 'ok',
            'ERROR' => 'failed',
            'FAILURE' => 'failed',
            'CANCELED' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // withdraw
        //
        //     array(
        //         1582271520931, // MTS Millisecond Time Stamp of the update
        //         "acc_wd-req", // TYPE Purpose of notification 'acc_wd-req' account withdrawal request
        //         null, // MESSAGE_ID unique ID of the message
        //         null, // not documented
        //         array(
        //             0, // WITHDRAWAL_ID Unique Withdrawal ID
        //             null, // PLACEHOLDER
        //             "bitcoin", // METHOD Method of withdrawal
        //             null, // PAYMENT_ID Payment ID if relevant
        //             "exchange", // WALLET Sending wallet
        //             1, // AMOUNT Amount of Withdrawal less fee
        //             null, // PLACEHOLDER
        //             null, // PLACEHOLDER
        //             0.0004, // WITHDRAWAL_FEE Fee on withdrawal
        //         ),
        //         null, // CODE null or integer Work in progress
        //         "SUCCESS", // STATUS Status of the notification, it may vary over time SUCCESS, ERROR, FAILURE
        //         "Invalid bitcoin address (abcdef)", // TEXT Text of the notification
        //     )
        //
        // fetchTransactions
        //
        //     array(
        //         13293039, // ID
        //         'ETH', // CURRENCY
        //         'ETHEREUM', // CURRENCY_NAME
        //         null,
        //         null,
        //         1574175052000, // MTS_STARTED
        //         1574181326000, // MTS_UPDATED
        //         null,
        //         null,
        //         'CANCELED', // STATUS
        //         null,
        //         null,
        //         -0.24, // AMOUNT, negative for withdrawals
        //         -0.00135, // FEES
        //         null,
        //         null,
        //         'DESTINATION_ADDRESS',
        //         null,
        //         null,
        //         null,
        //         'TRANSACTION_ID',
        //         "Purchase of 100 pizzas", // WITHDRAW_TRANSACTION_NOTE
        //     )
        //
        $transactionLength = is_array($transaction) ? count($transaction) : 0;
        $timestamp = null;
        $updated = null;
        $code = null;
        $amount = null;
        $id = null;
        $status = null;
        $tag = null;
        $type = null;
        $feeCost = null;
        $txid = null;
        $addressTo = null;
        if ($transactionLength < 9) {
            $data = $this->safe_value($transaction, 4, array());
            $timestamp = $this->safe_integer($transaction, 0);
            if ($currency !== null) {
                $code = $currency['code'];
            }
            $feeCost = $this->safe_float($data, 8);
            if ($feeCost !== null) {
                $feeCost = -$feeCost;
            }
            $amount = $this->safe_float($data, 5);
            $id = $this->safe_value($data, 0);
            $status = 'ok';
            if ($id === 0) {
                $id = null;
                $status = 'failed';
            }
            $tag = $this->safe_string($data, 3);
            $type = 'withdrawal';
        } else {
            $id = $this->safe_string($transaction, 0);
            $timestamp = $this->safe_integer($transaction, 5);
            $updated = $this->safe_integer($transaction, 6);
            $status = $this->parse_transaction_status($this->safe_string($transaction, 9));
            $amount = $this->safe_float($transaction, 12);
            if ($amount !== null) {
                if ($amount < 0) {
                    $type = 'withdrawal';
                } else {
                    $type = 'deposit';
                }
            }
            $feeCost = $this->safe_float($transaction, 13);
            if ($feeCost !== null) {
                $feeCost = -$feeCost;
            }
            $addressTo = $this->safe_string($transaction, 16);
            $txid = $this->safe_string($transaction, 20);
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'addressFrom' => null,
            'address' => $addressTo, // this is actually the $tag for XRP transfers (the address is missing)
            'addressTo' => $addressTo,
            'tagFrom' => null,
            'tag' => $tag, // refix it properly for the $tag from description
            'tagTo' => $tag,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => array(
                'currency' => $code,
                'cost' => $feeCost,
                'rate' => null,
            ),
        );
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array();
        $method = 'privatePostAuthRMovementsHist';
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
            $method = 'privatePostAuthRMovementsCurrencyHist';
        }
        if ($since !== null) {
            $request['start'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // max 1000
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             13293039, // ID
        //             'ETH', // CURRENCY
        //             'ETHEREUM', // CURRENCY_NAME
        //             null,
        //             null,
        //             1574175052000, // MTS_STARTED
        //             1574181326000, // MTS_UPDATED
        //             null,
        //             null,
        //             'CANCELED', // STATUS
        //             null,
        //             null,
        //             -0.24, // AMOUNT, negative for withdrawals
        //             -0.00135, // FEES
        //             null,
        //             null,
        //             'DESTINATION_ADDRESS',
        //             null,
        //             null,
        //             null,
        //             'TRANSACTION_ID',
        //             "Purchase of 100 pizzas", // WITHDRAW_TRANSACTION_NOTE
        //         )
        //     )
        //
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        // todo rewrite for https://api-pub.bitfinex.com//v2/conf/pub:map:tx:method
        $name = $this->getCurrencyName ($code);
        $request = array(
            'method' => $name,
            'wallet' => 'exchange', // 'exchange', 'margin', 'funding' and also old labels 'exchange', 'trading', 'deposit', respectively
            'amount' => $this->number_to_string($amount),
            'address' => $address,
        );
        if ($tag !== null) {
            $request['payment_id'] = $tag;
        }
        $response = $this->privatePostAuthWWithdraw (array_merge($request, $params));
        //
        //     array(
        //         1582271520931, // MTS Millisecond Time Stamp of the update
        //         "acc_wd-req", // TYPE Purpose of notification 'acc_wd-req' account withdrawal $request
        //         null, // MESSAGE_ID unique ID of the message
        //         null, // not documented
        //         array(
        //             0, // WITHDRAWAL_ID Unique Withdrawal ID
        //             null, // PLACEHOLDER
        //             "bitcoin", // METHOD Method of withdrawal
        //             null, // PAYMENT_ID Payment ID if relevant
        //             "exchange", // WALLET Sending wallet
        //             1, // AMOUNT Amount of Withdrawal less fee
        //             null, // PLACEHOLDER
        //             null, // PLACEHOLDER
        //             0.0004, // WITHDRAWAL_FEE Fee on withdrawal
        //         ),
        //         null, // CODE null or integer Work in progress
        //         "SUCCESS", // STATUS Status of the notification, it may vary over time SUCCESS, ERROR, FAILURE
        //         "Invalid bitcoin $address (abcdef)", // TEXT Text of the notification
        //     )
        //
        $text = $this->safe_string($response, 7);
        if ($text !== 'success') {
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $text, $text);
        }
        $transaction = $this->parse_transaction($response, $currency);
        return array_merge($transaction, array(
            'address' => $address,
        ));
    }

    public function fetch_positions($symbols = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostPositions ($params);
        //
        //     array(
        //         array(
        //             "tBTCUSD", // SYMBOL
        //             "ACTIVE", // STATUS
        //             0.0195, // AMOUNT
        //             8565.0267019, // BASE_PRICE
        //             0, // MARGIN_FUNDING
        //             0, // MARGIN_FUNDING_TYPE
        //             -0.33455568705000516, // PL
        //             -0.0003117550117425625, // PL_PERC
        //             7045.876419249083, // PRICE_LIQ
        //             3.0673001895895604, // LEVERAGE
        //             null, // _PLACEHOLDER
        //             142355652, // POSITION_ID
        //             1574002216000, // MTS_CREATE
        //             1574002216000, // MTS_UPDATE
        //             null, // _PLACEHOLDER
        //             0, // TYPE
        //             null, // _PLACEHOLDER
        //             0, // COLLATERAL
        //             0, // COLLATERAL_MIN
        //             // META
        //             {
        //                 "reason":"TRADE",
        //                 "order_id":34271018124,
        //                 "liq_stage":null,
        //                 "trade_price":"8565.0267019",
        //                 "trade_amount":"0.0195",
        //                 "order_id_oppo":34277498022
        //             }
        //         )
        //     )
        //
        // todo unify parsePosition/parsePositions
        return $response;
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'v1') {
            $request = $api . $request;
        } else {
            $request = $this->version . $request;
        }
        $url = $this->urls['api'][$api] . '/' . $request;
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $body = $this->json($query);
            $auth = '/api/' . $request . $nonce . $body;
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha384');
            $headers = array(
                'bfx-nonce' => $nonce,
                'bfx-apikey' => $this->apiKey,
                'bfx-signature' => $signature,
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if ($response) {
            if (is_array($response) && array_key_exists('message', $response)) {
                if (mb_strpos($response['message'], 'not enough exchange balance') !== false) {
                    throw new InsufficientFunds($this->id . ' ' . $this->json($response));
                }
                throw new ExchangeError($this->id . ' ' . $this->json($response));
            }
            return $response;
        } else if ($response === '') {
            throw new ExchangeError($this->id . ' returned empty response');
        }
        return $response;
    }

    public function handle_errors($statusCode, $statusText, $url, $method, $responseHeaders, $responseBody, $response, $requestHeaders, $requestBody) {
        if ($statusCode === 500) {
            // See https://docs.bitfinex.com/docs/abbreviations-glossary#section-errorinfo-codes
            $errorCode = $this->number_to_string($response[1]);
            $errorText = $response[2];
            $feedback = $this->id . ' ' . $errorText;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorCode, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $errorText, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $errorText, $feedback);
            throw new ExchangeError($this->id . ' ' . $errorText . ' (#' . $errorCode . ')');
        }
    }
}

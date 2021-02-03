<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class bitz extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitz',
            'name' => 'Bit-Z',
            'countries' => array( 'HK' ),
            'rateLimit' => 2000,
            'version' => 'v2',
            'userAgent' => $this->userAgents['chrome'],
            'has' => array(
                'cancelOrder' => true,
                'cancelOrders' => true,
                'createOrder' => true,
                'createMarketOrder' => false,
                'fetchBalance' => true,
                'fetchDeposits' => true,
                'fetchClosedOrders' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1min',
                '5m' => '5min',
                '15m' => '15min',
                '30m' => '30min',
                '1h' => '60min',
                '4h' => '4hour',
                '1d' => '1day',
                '5d' => '5day',
                '1w' => '1week',
                '1M' => '1mon',
            ),
            'hostname' => 'apiv2.bitz.com',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87443304-fec5e000-c5fd-11ea-98f8-ba8e67f7eaff.jpg',
                'api' => array(
                    'market' => 'https://{hostname}',
                    'trade' => 'https://{hostname}',
                    'assets' => 'https://{hostname}',
                ),
                'www' => 'https://www.bitz.com',
                'doc' => 'https://apidoc.bitz.com/en/',
                'fees' => 'https://www.bitz.com/fee?type=1',
                'referral' => 'https://u.bitz.com/register?invite_code=1429193',
            ),
            'api' => array(
                'market' => array(
                    'get' => array(
                        'ticker',
                        'depth',
                        'order', // trades
                        'tickerall',
                        'kline',
                        'symbolList',
                        'getServerTime',
                        'currencyRate',
                        'currencyCoinRate',
                        'coinRate',
                    ),
                ),
                'trade' => array(
                    'post' => array(
                        'addEntrustSheet',
                        'cancelEntrustSheet',
                        'cancelAllEntrustSheet',
                        'coinOut', // withdraw
                        'getUserHistoryEntrustSheet', // closed orders
                        'getUserNowEntrustSheet', // open orders
                        'getEntrustSheetInfo', // order
                        'depositOrWithdraw', // transactions
                    ),
                ),
                'assets' => array(
                    'post' => array(
                        'getUserAssets',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.002,
                    'taker' => 0.002,
                ),
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => '0.5%',
                        'DKKT' => '0.5%',
                        'ETH' => 0.01,
                        'USDT' => '0.5%',
                        'LTC' => '0.5%',
                        'FCT' => '0.5%',
                        'LSK' => '0.5%',
                        'HXI' => '0.8%',
                        'ZEC' => '0.5%',
                        'DOGE' => '0.5%',
                        'MZC' => '0.5%',
                        'ETC' => '0.5%',
                        'GXS' => '0.5%',
                        'XPM' => '0.5%',
                        'PPC' => '0.5%',
                        'BLK' => '0.5%',
                        'XAS' => '0.5%',
                        'HSR' => '0.5%',
                        'NULS' => 5.0,
                        'VOISE' => 350.0,
                        'PAY' => 1.5,
                        'EOS' => 0.6,
                        'YBCT' => 35.0,
                        'OMG' => 0.3,
                        'OTN' => 0.4,
                        'BTX' => '0.5%',
                        'QTUM' => '0.5%',
                        'DASH' => '0.5%',
                        'GAME' => '0.5%',
                        'BCH' => '0.5%',
                        'GNT' => 9.0,
                        'SSS' => 1500.0,
                        'ARK' => '0.5%',
                        'PART' => '0.5%',
                        'LEO' => '0.5%',
                        'DGB' => '0.5%',
                        'ZSC' => 130.0,
                        'VIU' => 350.0,
                        'BTG' => '0.5%',
                        'ARN' => 10.0,
                        'VTC' => '0.5%',
                        'BCD' => '0.5%',
                        'TRX' => 200.0,
                        'HWC' => '0.5%',
                        'UNIT' => '0.5%',
                        'OXY' => '0.5%',
                        'MCO' => 0.3500,
                        'SBTC' => '0.5%',
                        'BCX' => '0.5%',
                        'ETF' => '0.5%',
                        'PYLNT' => 0.4000,
                        'XRB' => '0.5%',
                        'ETP' => '0.5%',
                    ),
                ),
            ),
            'precision' => array(
                'amount' => 8,
                'price' => 8,
            ),
            'options' => array(
                'fetchOHLCVVolume' => true,
                'fetchOHLCVWarning' => true,
                'lastNonceTimestamp' => 0,
            ),
            'commonCurrencies' => array(
                // https://github.com/ccxt/ccxt/issues/3881
                // https://support.bit-z.pro/hc/en-us/articles/360007500654-BOX-BOX-Token-
                'BOX' => 'BOX Token',
                'LEO' => 'LeoCoin',
                'XRB' => 'NANO',
                'PXC' => 'Pixiecoin',
                'VTC' => 'VoteCoin',
                'TTC' => 'TimesChain',
            ),
            'exceptions' => array(
                // '200' => Success
                '-102' => '\\ccxt\\ExchangeError', // Invalid parameter
                '-103' => '\\ccxt\\AuthenticationError', // Verification failed
                '-104' => '\\ccxt\\ExchangeNotAvailable', // Network Error-1
                '-105' => '\\ccxt\\AuthenticationError', // Invalid api signature
                '-106' => '\\ccxt\\ExchangeNotAvailable', // Network Error-2
                '-109' => '\\ccxt\\AuthenticationError', // Invalid scretKey
                '-110' => '\\ccxt\\DDoSProtection', // The number of access requests exceeded
                '-111' => '\\ccxt\\PermissionDenied', // Current IP is not in the range of trusted IP
                '-112' => '\\ccxt\\OnMaintenance', // Service is under maintenance
                '-114' => '\\ccxt\\RateLimitExceeded', // The number of daily requests has reached the limit
                '-117' => '\\ccxt\\AuthenticationError', // The apikey expires
                '-100015' => '\\ccxt\\AuthenticationError', // Trade password error
                '-100044' => '\\ccxt\\ExchangeError', // Fail to request data
                '-100101' => '\\ccxt\\ExchangeError', // Invalid symbol
                '-100201' => '\\ccxt\\ExchangeError', // Invalid symbol
                '-100301' => '\\ccxt\\ExchangeError', // Invalid symbol
                '-100401' => '\\ccxt\\ExchangeError', // Invalid symbol
                '-100302' => '\\ccxt\\ExchangeError', // Type of K-line error
                '-100303' => '\\ccxt\\ExchangeError', // Size of K-line error
                '-200003' => '\\ccxt\\AuthenticationError', // Please set trade password
                '-200005' => '\\ccxt\\PermissionDenied', // This account can not trade
                '-200025' => '\\ccxt\\ExchangeNotAvailable', // Temporary trading halt
                '-200027' => '\\ccxt\\InvalidOrder', // Price Error
                '-200028' => '\\ccxt\\InvalidOrder', // Amount must be greater than 0
                '-200029' => '\\ccxt\\InvalidOrder', // Number must be between %s and %d
                '-200030' => '\\ccxt\\InvalidOrder', // Over price range
                '-200031' => '\\ccxt\\InsufficientFunds', // Insufficient assets
                '-200032' => '\\ccxt\\ExchangeError', // System error. Please contact customer service
                '-200033' => '\\ccxt\\ExchangeError', // Fail to trade
                '-200034' => '\\ccxt\\OrderNotFound', // The order does not exist
                '-200035' => '\\ccxt\\OrderNotFound', // Cancellation error, order filled
                '-200037' => '\\ccxt\\InvalidOrder', // Trade direction error
                '-200038' => '\\ccxt\\ExchangeError', // Trading Market Error
                '-200055' => '\\ccxt\\OrderNotFound', // Order record does not exist
                '-300069' => '\\ccxt\\AuthenticationError', // api_key is illegal
                '-300101' => '\\ccxt\\ExchangeError', // Transaction type error
                '-300102' => '\\ccxt\\InvalidOrder', // Price or number cannot be less than 0
                '-300103' => '\\ccxt\\AuthenticationError', // Trade password error
                '-301001' => '\\ccxt\\ExchangeNotAvailable', // Network Error-3
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->marketGetSymbolList ($params);
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array(   ltc_btc => array(          $id => "1",
        //                                        name => "ltc_btc",
        //                                    coinFrom => "ltc",
        //                                      coinTo => "btc",
        //                                 numberFloat => "4",
        //                                  priceFloat => "8",
        //                                      status => "1",
        //                                    minTrade => "0.010",
        //                                    maxTrade => "500000000.000" ),
        //                    qtum_usdt => array(          $id => "196",
        //                                        name => "qtum_usdt",
        //                                    coinFrom => "qtum",
        //                                      coinTo => "usdt",
        //                                 numberFloat => "4",
        //                                  priceFloat => "2",
        //                                      status => "1",
        //                                    minTrade => "0.100",
        //                                    maxTrade => "500000000.000" ),  ),
        //            time =>    1535969146,
        //       microtime =>   "0.66955600 1535969146",
        //          source =>   "api"                                           }
        //
        $markets = $this->safe_value($response, 'data');
        $ids = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $markets[$id];
            $numericId = $this->safe_string($market, 'id');
            $baseId = $this->safe_string($market, 'coinFrom');
            $quoteId = $this->safe_string($market, 'coinTo');
            $base = strtoupper($baseId);
            $quote = strtoupper($quoteId);
            $base = $this->safe_currency_code($base);
            $quote = $this->safe_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'numberFloat'),
                'price' => $this->safe_integer($market, 'priceFloat'),
            );
            $result[] = array(
                'info' => $market,
                'id' => $id,
                'numericId' => $numericId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'minTrade'),
                        'max' => $this->safe_float($market, 'maxTrade'),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
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
        $response = $this->assetsPostGetUserAssets ($params);
        //
        //     {
        //         status => 200,
        //         msg => "",
        //         data => array(
        //             cny => 0,
        //             usd => 0,
        //             btc_total => 0,
        //             info => [array(
        //                 "name" => "zpr",
        //                 "num" => "37.49067275",
        //                 "over" => "37.49067275",
        //                 "lock" => "0.00000000",
        //                 "btc" => "0.00000000",
        //                 "usd" => "0.00000000",
        //                 "cny" => "0.00000000",
        //             )],
        //         ),
        //         time => 1535983966,
        //         microtime => "0.70400500 1535983966",
        //         source => "api",
        //     }
        //
        $balances = $this->safe_value($response['data'], 'info');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'name');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['used'] = $this->safe_float($balance, 'lock');
            $account['total'] = $this->safe_float($balance, 'num');
            $account['free'] = $this->safe_float($balance, 'over');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //      {          $symbol => "eth_btc",
        //            quoteVolume => "3905.72",
        //                 volume => "97058.21",
        //            priceChange => "-1.72",
        //         priceChange24h => "-1.65",
        //               askPrice => "0.03971272",
        //                 askQty => "0.0663",
        //               bidPrice => "0.03961469",
        //                 bidQty => "19.5451",
        //                   $open => "0.04036769",
        //                   high => "0.04062988",
        //                    low => "0.03956123",
        //                    now => "0.03970100",
        //                firstId =>  115567767,
        //                 lastId =>  115795316,
        //              dealCount =>  14078,
        //        numberPrecision =>  4,
        //         pricePrecision =>  8,
        //                    cny => "1959.05",
        //                    usd => "287.10",
        //                    krw => "318655.82"   }
        //
        $timestamp = null;
        $marketId = $this->safe_string($ticker, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $last = $this->safe_float($ticker, 'now');
        $open = $this->safe_float($ticker, 'open');
        $change = null;
        $average = null;
        if ($last !== null && $open !== null) {
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bidPrice'),
            'bidVolume' => $this->safe_float($ticker, 'bidQty'),
            'ask' => $this->safe_float($ticker, 'askPrice'),
            'askVolume' => $this->safe_float($ticker, 'askQty'),
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $this->safe_float($ticker, 'priceChange24h'),
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'info' => $ticker,
        );
    }

    public function parse_microtime($microtime) {
        if ($microtime === null) {
            return $microtime;
        }
        $parts = explode(' ', $microtime);
        $milliseconds = floatval($parts[0]);
        $seconds = intval($parts[1]);
        $total = $this->sum($seconds, $milliseconds);
        return intval($total * 1000);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->marketGetTicker (array_merge($request, $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array(          $symbol => "eth_btc",
        //                        quoteVolume => "3905.72",
        //                             volume => "97058.21",
        //                        priceChange => "-1.72",
        //                     priceChange24h => "-1.65",
        //                           askPrice => "0.03971272",
        //                             askQty => "0.0663",
        //                           bidPrice => "0.03961469",
        //                             bidQty => "19.5451",
        //                               open => "0.04036769",
        //                               high => "0.04062988",
        //                                low => "0.03956123",
        //                                now => "0.03970100",
        //                            firstId =>  115567767,
        //                             lastId =>  115795316,
        //                          dealCount =>  14078,
        //                    numberPrecision =>  4,
        //                     pricePrecision =>  8,
        //                                cny => "1959.05",
        //                                usd => "287.10",
        //                                krw => "318655.82"   ),
        //            time =>    1535970397,
        //       microtime =>   "0.76341900 1535970397",
        //          source =>   "api"                             }
        //
        $ticker = $this->parse_ticker($response['data'], $market);
        $timestamp = $this->parse_microtime($this->safe_string($response, 'microtime'));
        return array_merge($ticker, array(
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
        ));
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($symbols !== null) {
            $ids = $this->market_ids($symbols);
            $request['symbols'] = implode(',', $ids);
        }
        $response = $this->marketGetTickerall (array_merge($request, $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => {   ela_btc => array(          $symbol => "ela_btc",
        //                                     quoteVolume => "0.00",
        //                                          volume => "3.28",
        //                                     priceChange => "0.00",
        //                                  priceChange24h => "0.00",
        //                                        askPrice => "0.00147984",
        //                                          askQty => "5.4580",
        //                                        bidPrice => "0.00120230",
        //                                          bidQty => "12.5384",
        //                                            open => "0.00149078",
        //                                            high => "0.00149078",
        //                                             low => "0.00149078",
        //                                             now => "0.00149078",
        //                                         firstId =>  115581219,
        //                                          lastId =>  115581219,
        //                                       dealCount =>  1,
        //                                 numberPrecision =>  4,
        //                                  pricePrecision =>  8,
        //                                             cny => "73.66",
        //                                             usd => "10.79",
        //                                             krw => "11995.03"    }     ),
        //            time =>    1535971578,
        //       microtime =>   "0.39854200 1535971578",
        //          source =>   "api"                                                }
        //
        $tickers = $this->safe_value($response, 'data');
        $timestamp = $this->parse_microtime($this->safe_string($response, 'microtime'));
        $result = array();
        $ids = is_array($tickers) ? array_keys($tickers) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $ticker = $tickers[$id];
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
            }
            $ticker = $this->parse_ticker($tickers[$id], $market);
            $symbol = $ticker['symbol'];
            if ($symbol === null) {
                if ($market !== null) {
                    $symbol = $market['symbol'];
                } else {
                    list($baseId, $quoteId) = explode('_', $id);
                    $base = $this->safe_currency_code($baseId);
                    $quote = $this->safe_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                }
            }
            if ($symbol !== null) {
                $result[$symbol] = array_merge($ticker, array(
                    'timestamp' => $timestamp,
                    'datetime' => $this->iso8601($timestamp),
                ));
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_time($params = array ()) {
        $response = $this->marketGetGetServerTime ($params);
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":array(),
        //         "time":1555490875,
        //         "microtime":"0.35994200 1555490875",
        //         "source":"api"
        //     }
        //
        return $this->safe_timestamp($response, 'time');
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        $response = $this->marketGetDepth (array_merge($request, $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array(     asks => [ ["10.00000000", "0.4426", "4.4260"],
        //                                ["1.00000000", "0.8339", "0.8339"],
        //                                ["0.91700000", "0.0500", "0.0458"],
        //                                ["0.20000000", "0.1000", "0.0200"],
        //                                ["0.03987120", "16.1262", "0.6429"],
        //                                ["0.03986120", "9.7523", "0.3887"]   ],
        //                        bids => [ ["0.03976145", "0.0359", "0.0014"],
        //                                ["0.03973401", "20.9493", "0.8323"],
        //                                ["0.03967970", "0.0328", "0.0013"],
        //                                ["0.00000002", "10000.0000", "0.0002"],
        //                                ["0.00000001", "231840.7500", "0.0023"] ],
        //                    coinPair =>   "eth_btc"                                  ),
        //            time =>    1535974778,
        //       microtime =>   "0.04017400 1535974778",
        //          source =>   "api"                                                     }
        //
        $orderbook = $this->safe_value($response, 'data');
        $timestamp = $this->parse_microtime($this->safe_string($response, 'microtime'));
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //    array( $id =>  115807453,
        //       t => "19:36:24",
        //       T =>  1535974584,
        //       p => "0.03983296",
        //       n => "0.1000",
        //       s => "buy"         ),
        //
        $id = $this->safe_string($trade, 'id');
        $timestamp = $this->safe_timestamp($trade, 'T');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $price = $this->safe_float($trade, 'p');
        $amount = $this->safe_float($trade, 'n');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $this->price_to_precision($symbol, $amount * $price);
            }
        }
        $side = $this->safe_string($trade, 's');
        return array(
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => null,
            'type' => 'limit',
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->marketGetOrder (array_merge($request, $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array( array( id =>  115807453,
        //                       t => "19:36:24",
        //                       T =>  1535974584,
        //                       p => "0.03983296",
        //                       n => "0.1000",
        //                       s => "buy"         ),
        //                    { id =>  115806811,
        //                       t => "19:33:19",
        //                       T =>  1535974399,
        //                       p => "0.03981135",
        //                       n => "9.4612",
        //                       s => "sell"        }  ),
        //            time =>    1535974583,
        //       microtime =>   "0.57118100 1535974583",
        //          source =>   "api"                     }
        //
        return $this->parse_trades($response['data'], $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         time => "1535973420000",
        //         open => "0.03975084",
        //         high => "0.03975084",
        //         low => "0.03967700",
        //         close => "0.03967700",
        //         volume => "12.4733",
        //         datetime => "2018-09-03 19:17:00"
        //     }
        //
        return array(
            $this->safe_integer($ohlcv, 'time'),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $duration = $this->parse_timeframe($timeframe) * 1000;
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'resolution' => $this->timeframes[$timeframe],
        );
        if ($limit !== null) {
            $request['size'] = min ($limit, 300); // 1-300
            if ($since !== null) {
                $request['to'] = $this->sum($since, $limit * $duration * 1000);
            }
        } else {
            if ($since !== null) {
                throw new ArgumentsRequired($this->id . ' fetchOHLCV() requires a $limit argument if the $since argument is specified');
            }
        }
        $response = $this->marketGetKline (array_merge($request, $params));
        //
        //     {
        //         status => 200,
        //         msg => "",
        //         $data => array(
        //             $bars => array(
        //                 array( time => "1535973420000", open => "0.03975084", high => "0.03975084", low => "0.03967700", close => "0.03967700", volume => "12.4733", datetime => "2018-09-03 19:17:00" ),
        //                 array( time => "1535955480000", open => "0.04009900", high => "0.04016745", low => "0.04009900", close => "0.04012074", volume => "74.4803", datetime => "2018-09-03 14:18:00" ),
        //             ),
        //             resolution => "1min",
        //             $symbol => "eth_btc",
        //             from => "1535973420000",
        //             to => "1535955480000",
        //             size => 300
        //         ),
        //         time => 1535973435,
        //         microtime => "0.56462100 1535973435",
        //         source => "api"
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $bars = $this->safe_value($data, 'bars', array());
        return $this->parse_ohlcvs($bars, $market, $timeframe, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            '0' => 'open',
            '1' => 'open', // partially filled
            '2' => 'closed', // filled
            '3' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //    {
        //         "$id" => "693248739",   // $order $id
        //         "uId" => "2074056",    // uid
        //         "$price" => "100",      // $price
        //         "number" => "10",      // number
        //         "numberOver" => "10",  // undealed
        //         "flag" => "sale",      // flag
        //         "$status" => "0",       // unfilled
        //         "coinFrom" => "vtc",
        //         "coinTo" => "dkkt",
        //         "numberDeal" => "0"    // dealed
        //     }
        //
        $id = $this->safe_string($order, 'id');
        $symbol = null;
        if ($market === null) {
            $baseId = $this->safe_string($order, 'coinFrom');
            $quoteId = $this->safe_string($order, 'coinTo');
            if (($baseId !== null) && ($quoteId !== null)) {
                $marketId = $baseId . '_' . $quoteId;
                if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                    $market = $this->safe_value($this->markets_by_id, $marketId);
                } else {
                    $base = $this->safe_currency_code($baseId);
                    $quote = $this->safe_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                }
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_string($order, 'flag');
        if ($side !== null) {
            $side = ($side === 'sale') ? 'sell' : 'buy';
        }
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'number');
        $remaining = $this->safe_float($order, 'numberOver');
        $filled = $this->safe_float($order, 'numberDeal');
        $timestamp = $this->safe_integer($order, 'timestamp');
        if ($timestamp === null) {
            $timestamp = $this->safe_timestamp($order, 'created');
        }
        $cost = $this->safe_float($order, 'orderTotalPrice');
        if ($price !== null) {
            if ($filled !== null) {
                $cost = $filled * $price;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        return array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => null,
            'info' => $order,
            'average' => null,
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' createOrder allows limit orders only');
        }
        $market = $this->market($symbol);
        $orderType = ($side === 'buy') ? '1' : '2';
        if (!$this->password) {
            throw new ExchangeError($this->id . ' createOrder() requires you to set exchange.password = "YOUR_TRADING_PASSWORD" (a trade password is NOT THE SAME as your login password)');
        }
        $request = array(
            'symbol' => $market['id'],
            'type' => $orderType,
            'price' => $this->price_to_precision($symbol, $price),
            'number' => $this->amount_to_precision($symbol, $amount),
            'tradePwd' => $this->password,
        );
        $response = $this->tradePostAddEntrustSheet (array_merge($request, $params));
        //
        //     {
        //         "status" => 200,
        //         "msg" => "",
        //         "data" => array(
        //             "id" => "693248739",   // $order id
        //             "uId" => "2074056",    // uid
        //             "$price" => "100",      // $price
        //             "number" => "10",      // number
        //             "numberOver" => "10",  // undealed
        //             "flag" => "sale",      // flag
        //             "status" => "0",       // unfilled
        //             "coinFrom" => "vtc",
        //             "coinTo" => "dkkt",
        //             "numberDeal" => "0"    // dealed
        //         ),
        //         "time" => "1533035297",
        //         "microtime" => "0.41892000 1533035297",
        //         "source" => "api",
        //     }
        //
        $timestamp = $this->parse_microtime($this->safe_string($response, 'microtime'));
        $order = array_merge(array(
            'timestamp' => $timestamp,
        ), $response['data']);
        return $this->parse_order($order, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'entrustSheetId' => $id,
        );
        $response = $this->tradePostCancelEntrustSheet (array_merge($request, $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":{
        //             "updateAssetsData":array(
        //                 "coin":"bz",
        //                 "over":"1000.00000000",
        //                 "lock":"-1000.00000000"
        //             ),
        //             "assetsInfo":array(
        //                 "coin":"bz",
        //                 "over":"9999.99999999",
        //                 "lock":"9999.99999999"
        //             }
        //         ),
        //         "time":"1535464383",
        //         "microtime":"0.91558000 1535464383",
        //         "source":"api"
        //     }
        //
        return $response;
    }

    public function cancel_orders($ids, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'ids' => implode(',', $ids),
        );
        $response = $this->tradePostCancelEntrustSheet (array_merge($request, $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":{
        //             "744173808":{
        //                 "updateAssetsData":array(
        //                     "coin":"bz",
        //                     "over":"100.00000000",
        //                     "lock":"-100.00000000"
        //                 ),
        //                 "assetsInfo":array(
        //                     "coin":"bz",
        //                     "over":"899.99999999",
        //                     "lock":"19099.99999999"
        //                 }
        //             ),
        //             "744173809":{
        //                 "updateAssetsData":array(
        //                     "coin":"bz",
        //                     "over":"100.00000000",
        //                     "lock":"-100.00000000"
        //                 ),
        //                 "assetsInfo":array(
        //                     "coin":"bz",
        //                     "over":"999.99999999",
        //                     "lock":"18999.99999999"
        //                 }
        //             }
        //         ),
        //         "time":"1535525649",
        //         "microtime":"0.05009400 1535525649",
        //         "source":"api"
        //     }
        //
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'entrustSheetId' => $id,
        );
        $response = $this->tradePostGetEntrustSheetInfo (array_merge($request, $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":array(
        //             "$id":"708279852",
        //             "uId":"2074056",
        //             "price":"100.00000000",
        //             "number":"10.0000",
        //             "total":"0.00000000",
        //             "numberOver":"10.0000",
        //             "numberDeal":"0.0000",
        //             "flag":"sale",
        //             "status":"0",  //0:unfilled, 1:partial deal, 2:all transactions, 3:already cancelled
        //             "coinFrom":"bz",
        //             "coinTo":"usdt",
        //             "orderTotalPrice":"0",
        //             "created":"1533279876"
        //         ),
        //         "time":"1533280294",
        //         "microtime":"0.36859200 1533280294",
        //         "source":"api"
        //     }
        //
        return $this->parse_order($response['data']);
    }

    public function fetch_orders_with_method($method, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'coinFrom' => $market['baseId'],
            'coinTo' => $market['quoteId'],
            // 'type' => 1, // optional integer, 1 = buy, 2 = sell
            // 'page' => 1, // optional integer
            // 'pageSize' => 100, // optional integer, max 100
            // 'startTime' => 1510235730, // optional integer timestamp in seconds
            // 'endTime' => 1510235730, // optional integer timestamp in seconds
        );
        if ($limit !== null) {
            $request['page'] = 1;
            $request['pageSize'] = $limit;
        }
        if ($since !== null) {
            $request['startTime'] = intval($since / 1000);
            // $request['endTime'] = intval($since / 1000);
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "status" => 200,
        //         "msg" => "",
        //         "data" => {
        //             "data" => array(
        //                 array(
        //                     "id" => "693248739",
        //                     "uid" => "2074056",
        //                     "price" => "100.00000000",
        //                     "number" => "10.0000",
        //                     "total" => "0.00000000",
        //                     "numberOver" => "0.0000",
        //                     "numberDeal" => "0.0000",
        //                     "flag" => "sale",
        //                     "status" => "3", // 0:unfilled, 1:partial deal, 2:all transactions, 3:already cancelled
        //                     "isNew" => "N",
        //                     "coinFrom" => "vtc",
        //                     "coinTo" => "dkkt",
        //                     "created" => "1533035300",
        //                 ),
        //                 array(
        //                     "id" => "723086996",
        //                     "uid" => "2074056",
        //                     "price" => "100.00000000",
        //                     "number" => "10.0000",
        //                     "total" => "0.00000000",
        //                     "numberOver" => "0.0000",
        //                     "numberDeal" => "0.0000",
        //                     "flag" => "sale",
        //                     "status" => "3",
        //                     "isNew" => "N",
        //                     "coinFrom" => "bz",
        //                     "coinTo" => "usdt",
        //                     "created" => "1533523568",
        //                 ),
        //             ),
        //             "pageInfo" => array(
        //                 "$limit" => "10",
        //                 "offest" => "0",
        //                 "current_page" => "1",
        //                 "page_size" => "10",
        //                 "total_count" => "17",
        //                 "page_count" => "2",
        //             }
        //         ),
        //         "time" => "1533279329",
        //         "microtime" => "0.15305300 1533279329",
        //         "source" => "api"
        //     }
        //
        $orders = $this->safe_value($response['data'], 'data', array());
        return $this->parse_orders($orders, null, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method('tradePostGetUserHistoryEntrustSheet', $symbol, $since, $limit, $params);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method('tradePostGetUserNowEntrustSheet', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method('tradePostGetUserHistoryEntrustSheet', $symbol, $since, $limit, $params);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            '1' => 'pending',
            '2' => 'pending',
            '3' => 'pending',
            '4' => 'ok',
            '5' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //     {
        //         "id" => '96275',
        //         "uid" => '2109073',
        //         "wallet" => '0xf4c4141c0127bc37b1d0c409a091920eba13ada7',
        //         "txid" => '0xb7adfa52aa566f9ac112e3c01f77bd91179b19eab12092a9a5a8b33d5086e31d',
        //         "confirm" => '12',
        //         "number" => '0.50000000',
        //         "$status" => 4,
        //         "updated" => '1534944168605',
        //         "addressUrl" => 'https://etherscan.io/address/',
        //         "txidUrl" => 'https://etherscan.io/tx/',
        //         "description" => 'Ethereum',
        //         "coin" => 'eth',
        //         "memo" => ''
        //     }
        //
        //     {
        //         "id":"397574",
        //         "uid":"2033056",
        //         "wallet":"1AG1gZvQAYu3WBvgg7p4BMMghQD2gE693k",
        //         "txid":"",
        //         "confirm":"0",
        //         "number":"1000.00000000",
        //         "$status":1,
        //         "updated":"0",
        //         "addressUrl":"http://omniexplorer.info/lookupadd.aspx?address=",
        //         "txidUrl":"http://omniexplorer.info/lookuptx.aspx?txid=",
        //         "description":"Tether",
        //         "coin":"usdt",
        //         "memo":""
        //     }
        //
        //     {
        //         "id":"153606",
        //         "uid":"2033056",
        //         "wallet":"1AG1gZvQAYu3WBvgg7p4BMMghQD2gE693k",
        //         "txid":"aa2b179f84cd6dedafd41845e0fbf7f01e14c0d71ea3140d03d6f5a9ccd93199",
        //         "confirm":"0",
        //         "number":"761.11110000",
        //         "$status":4,
        //         "updated":"1536726133579",
        //         "addressUrl":"http://omniexplorer.info/lookupadd.aspx?address=",
        //         "txidUrl":"http://omniexplorer.info/lookuptx.aspx?txid=",
        //         "description":"Tether",
        //         "coin":"usdt",
        //         "memo":""
        //     }
        //
        // withdraw
        //
        //     {
        //         "id":397574,
        //         "email":"***@email.com",
        //         "coin":"usdt",
        //         "network_fee":"",
        //         "eid":23112
        //     }
        //
        $timestamp = $this->safe_integer($transaction, 'updated');
        if ($timestamp === 0) {
            $timestamp = null;
        }
        $currencyId = $this->safe_string($transaction, 'coin');
        $code = $this->safe_currency_code($currencyId, $currency);
        $type = $this->safe_string_lower($transaction, 'type');
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $fee = null;
        $feeCost = $this->safe_float($transaction, 'network_fee');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'code' => $code,
            );
        }
        return array(
            'id' => $this->safe_string($transaction, 'id'),
            'txid' => $this->safe_string($transaction, 'txid'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $this->safe_string($transaction, 'wallet'),
            'tag' => $this->safe_string($transaction, 'memo'),
            'type' => $type,
            'amount' => $this->safe_float($transaction, 'number'),
            'currency' => $code,
            'status' => $status,
            'updated' => $timestamp,
            'fee' => $fee,
            'info' => $transaction,
        );
    }

    public function parse_transactions_by_type($type, $transactions, $code = null, $since = null, $limit = null) {
        $result = array();
        for ($i = 0; $i < count($transactions); $i++) {
            $transaction = $this->parse_transaction(array_merge(array(
                'type' => $type,
            ), $transactions[$i]));
            $result[] = $transaction;
        }
        return $this->filter_by_currency_since_limit($result, $code, $since, $limit);
    }

    public function parse_transaction_type($type) {
        $types = array(
            'deposit' => 1,
            'withdrawal' => 2,
        );
        return $this->safe_integer($types, $type, $type);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_for_type('deposit', $code, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_for_type('withdrawal', $code, $since, $limit, $params);
    }

    public function fetch_transactions_for_type($type, $code = null, $since = null, $limit = null, $params = array ()) {
        if ($code === null) {
            throw new ArgumentsRequired($this->id . ' fetchTransactions() requires a $currency `$code` argument');
        }
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'coin' => $currency['id'],
            'type' => $this->parse_transaction_type($type),
        );
        if ($since !== null) {
            $request['startTime'] = intval($since / (string) 1000);
        }
        if ($limit !== null) {
            $request['page'] = 1;
            $request['pageSize'] = $limit;
        }
        $response = $this->tradePostDepositOrWithdraw (array_merge($request, $params));
        $transactions = $this->safe_value($response['data'], 'data', array());
        return $this->parse_transactions_by_type($type, $transactions, $code, $since, $limit);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'coin' => $currency['id'],
            'number' => $this->currency_to_precision($code, $amount),
            'address' => $address,
            // 'type' => 'erc20', // omni, trc20, optional
        );
        if ($tag !== null) {
            $request['memo'] = $tag;
        }
        $response = $this->tradePostCoinOut (array_merge($request, $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "$data":array(
        //             "id":397574,
        //             "email":"***@email.com",
        //             "coin":"usdt",
        //             "network_fee":"",
        //             "eid":23112
        //         ),
        //         "time":1552641646,
        //         "microtime":"0.70304500 1552641646",
        //         "source":"api"
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_transaction($data, $currency);
    }

    public function nonce() {
        $currentTimestamp = $this->seconds();
        if ($currentTimestamp > $this->options['lastNonceTimestamp']) {
            $this->options['lastNonceTimestamp'] = $currentTimestamp;
            $this->options['lastNonce'] = 100000;
        }
        $this->options['lastNonce'] = $this->sum($this->options['lastNonce'], 1);
        return $this->options['lastNonce'];
    }

    public function sign($path, $api = 'market', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $baseUrl = $this->implode_params($this->urls['api'][$api], array( 'hostname' => $this->hostname ));
        $url = $baseUrl . '/' . $this->capitalize($api) . '/' . $path;
        $query = null;
        if ($api === 'market') {
            $query = $this->urlencode($params);
            if (strlen($query)) {
                $url .= '?' . $query;
            }
        } else {
            $this->check_required_credentials();
            $body = $this->rawencode($this->keysort(array_merge(array(
                'apiKey' => $this->apiKey,
                'timeStamp' => $this->seconds(),
                'nonce' => $this->nonce(),
            ), $params)));
            $body .= '&sign=' . $this->hash($this->encode($body . $this->secret));
            $headers = array( 'Content-type' => 'application/x-www-form-urlencoded' );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        $status = $this->safe_string($response, 'status');
        if ($status !== null) {
            $feedback = $this->id . ' ' . $body;
            //
            //     array("$status":-107,"msg":"","data":"","time":1535968848,"microtime":"0.89092200 1535968848","source":"api")
            //
            if ($status === '200') {
                //
                //     array("$status":200,"msg":"","data":-200031,"time":1535999806,"microtime":"0.85476800 1535999806","source":"api")
                //
                $code = $this->safe_integer($response, 'data');
                if ($code !== null) {
                    $this->throw_exactly_matched_exception($this->exceptions, $code, $feedback);
                    throw new ExchangeError($feedback);
                } else {
                    return; // no error
                }
            }
            $this->throw_exactly_matched_exception($this->exceptions, $status, $feedback);
            throw new ExchangeError($feedback);
        }
    }
}

<?php

namespace ccxt;

use Exception as Exception; // a common import

class bitz extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitz',
            'name' => 'Bit-Z',
            'countries' => array ( 'HK' ),
            'rateLimit' => 2000,
            'version' => 'v2',
            'userAgent' => $this->userAgents['chrome'],
            'has' => array (
                'fetchTickers' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchOrders' => true,
                'fetchOrder' => true,
                'createMarketOrder' => false,
            ),
            'timeframes' => array (
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
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/35862606-4f554f14-0b5d-11e8-957d-35058c504b6f.jpg',
                'api' => array (
                    'market' => 'https://apiv2.bitz.com',
                    'trade' => 'https://apiv2.bitz.com',
                    'assets' => 'https://apiv2.bitz.com',
                ),
                'www' => 'https://www.bit-z.com',
                'doc' => 'https://apidoc.bit-z.com/en',
                'fees' => 'https://www.bit-z.com/about/fee',
                'referral' => 'https://u.bit-z.com/register?invite_code=1429193',
            ),
            'api' => array (
                'market' => array (
                    'get' => array (
                        'ticker',
                        'depth',
                        'order', // trades
                        'tickerall',
                        'kline',
                        'symbolList',
                        'currencyRate',
                        'currencyCoinRate',
                        'coinRate',
                    ),
                ),
                'trade' => array (
                    'post' => array (
                        'addEntrustSheet',
                        'cancelEntrustSheet',
                        'cancelAllEntrustSheet',
                        'getUserHistoryEntrustSheet', // closed orders
                        'getUserNowEntrustSheet', // open orders
                        'getEntrustSheetInfo', // order
                    ),
                ),
                'assets' => array (
                    'post' => array (
                        'getUserAssets',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.001,
                    'taker' => 0.001,
                ),
                'funding' => array (
                    'withdraw' => array (
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
            'precision' => array (
                'amount' => 8,
                'price' => 8,
            ),
            'options' => array (
                'fetchOHLCVVolume' => true,
                'fetchOHLCVWarning' => true,
                'lastNonceTimestamp' => 0,
            ),
            'commonCurrencies' => array (
                // https://github.com/ccxt/ccxt/issues/3881
                // https://support.bit-z.pro/hc/en-us/articles/360007500654-BOX-BOX-Token-
                'BOX' => 'BOX Token',
                'XRB' => 'NANO',
                'PXC' => 'Pixiecoin',
            ),
            'exceptions' => array (
                // '200' => Success
                '-102' => '\\ccxt\\ExchangeError', // Invalid parameter
                '-103' => '\\ccxt\\AuthenticationError', // Verification failed
                '-104' => '\\ccxt\\ExchangeNotAvailable', // Network Error-1
                '-105' => '\\ccxt\\AuthenticationError', // Invalid api signature
                '-106' => '\\ccxt\\ExchangeNotAvailable', // Network Error-2
                '-109' => '\\ccxt\\AuthenticationError', // Invalid scretKey
                '-110' => '\\ccxt\\DDoSProtection', // The number of access requests exceeded
                '-111' => '\\ccxt\\PermissionDenied', // Current IP is not in the range of trusted IP
                '-112' => '\\ccxt\\ExchangeNotAvailable', // Service is under maintenance
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

    public function fetch_markets () {
        $response = $this->marketGetSymbolList ();
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array (   ltc_btc => array (          $id => "1",
        //                                        name => "ltc_btc",
        //                                    coinFrom => "ltc",
        //                                      coinTo => "btc",
        //                                 numberFloat => "4",
        //                                  priceFloat => "8",
        //                                      status => "1",
        //                                    minTrade => "0.010",
        //                                    maxTrade => "500000000.000" ),
        //                    qtum_usdt => array (          $id => "196",
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
        $markets = $response['data'];
        $ids = is_array ($markets) ? array_keys ($markets) : array ();
        $result = array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $market = $markets[$id];
            $numericId = $this->safe_string($market, 'id');
            $baseId = $this->safe_string($market, 'coinFrom');
            $quoteId = $this->safe_string($market, 'coinTo');
            $base = strtoupper ($baseId);
            $quote = strtoupper ($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => $this->safe_integer($market, 'numberFloat'),
                'price' => $this->safe_integer($market, 'priceFloat'),
            );
            $result[] = array (
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
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($market, 'minTrade'),
                        'max' => $this->safe_float($market, 'maxTrade'),
                    ),
                    'price' => array (
                        'min' => pow (10, -$precision['price']),
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->assetsPostGetUserAssets ($params);
        //
        //     {
        //         status => 200,
        //         msg => "",
        //         data => array (
        //             cny => 0,
        //             usd => 0,
        //             btc_total => 0,
        //             info => [array (
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
        $balances = $response['data']['info'];
        $result = array ( 'info' => $response );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'name');
            $code = strtoupper ($currencyId);
            if (is_array ($this->markets_by_id) && array_key_exists ($currencyId, $this->markets_by_id)) {
                $code = $this->currencies_by_id[$currencyId]['code'];
            } else {
                $code = $this->common_currency_code($code);
            }
            $account = $this->account ();
            $account['used'] = $this->safe_float($balance, 'lock');
            $account['total'] = $this->safe_float($balance, 'num');
            $account['free'] = $this->safe_float($balance, 'over');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_ticker ($ticker, $market = null) {
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
        //                   open => "0.04036769",
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
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($ticker, 'symbol');
            $market = $this->safe_value($this->markets_by_id, $marketId);
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'now');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bidPrice'),
            'bidVolume' => $this->safe_float($ticker, 'bidQty'),
            'ask' => $this->safe_float($ticker, 'askPrice'),
            'askVolume' => $this->safe_float($ticker, 'askQty'),
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_float($ticker, 'priceChange24h'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'info' => $ticker,
        );
    }

    public function parse_microtime ($microtime) {
        if ($microtime === null) {
            return $microtime;
        }
        $parts = explode (' ', $microtime);
        $milliseconds = floatval ($parts[0]);
        $seconds = intval ($parts[1]);
        $total = $seconds . $milliseconds;
        return intval ($total * 1000);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->marketGetTicker (array_merge (array (
            'symbol' => $market['id'],
        ), $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array (          $symbol => "eth_btc",
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
        $timestamp = $this->parse_microtime ($this->safe_string($response, 'microtime'));
        return array_merge ($ticker, array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
        ));
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array ();
        if ($symbols !== null) {
            $ids = $this->market_ids($symbols);
            $request['symbols'] = implode (',', $ids);
        }
        $response = $this->marketGetTickerall (array_merge ($request, $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => {   ela_btc => array (          $symbol => "ela_btc",
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
        $tickers = $response['data'];
        $timestamp = $this->parse_microtime ($this->safe_string($response, 'microtime'));
        $result = array ();
        $ids = is_array ($tickers) ? array_keys ($tickers) : array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $ticker = $tickers[$id];
            $market = null;
            if (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
            }
            $ticker = $this->parse_ticker($tickers[$id], $market);
            $symbol = $ticker['symbol'];
            if ($symbol === null) {
                if ($market !== null) {
                    $symbol = $market['symbol'];
                } else {
                    list ($baseId, $quoteId) = explode ('_', $id);
                    $base = strtoupper ($baseId);
                    $quote = strtoupper ($quoteId);
                    $base = $this->common_currency_code($baseId);
                    $quote = $this->common_currency_code($quoteId);
                    $symbol = $base . '/' . $quote;
                }
            }
            if ($symbol !== null) {
                $result[$symbol] = array_merge ($ticker, array (
                    'timestamp' => $timestamp,
                    'datetime' => $this->iso8601 ($timestamp),
                ));
            }
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->marketGetDepth (array_merge (array (
            'symbol' => $this->market_id($symbol),
        ), $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array (     asks => [ ["10.00000000", "0.4426", "4.4260"],
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
        $orderbook = $response['data'];
        $timestamp = $this->parse_microtime ($this->safe_string($response, 'microtime'));
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function parse_trade ($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //    array ( $id =>  115807453,
        //       t => "19:36:24",
        //       T =>  1535974584,
        //       p => "0.03983296",
        //       n => "0.1000",
        //       s => "buy"         ),
        //
        $id = $this->safe_string($trade, 'id');
        $timestamp = $this->safe_integer($trade, 'T');
        if ($timestamp !== null) {
            $timestamp = $timestamp * 1000;
        }
        $price = $this->safe_float($trade, 'p');
        $amount = $this->safe_float($trade, 'n');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $cost = $this->price_to_precision($symbol, $amount * $price);
        $side = $this->safe_string($trade, 's');
        return array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => null,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
            'info' => $trade,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->marketGetOrder (array_merge (array (
            'symbol' => $market['id'],
        ), $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => array ( array ( id =>  115807453,
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

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '1m', $since = null, $limit = null) {
        //
        //      {     time => "1535973420000",
        //            open => "0.03975084",
        //            high => "0.03975084",
        //             low => "0.03967700",
        //           close => "0.03967700",
        //          volume => "12.4733",
        //        datetime => "2018-09-03 19:17:00" }
        //
        return array (
            $this->safe_integer($ohlcv, 'time'),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $duration = $this->parse_timeframe($timeframe) * 1000;
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
            'resolution' => $this->timeframes[$timeframe],
        );
        if ($limit !== null) {
            $request['size'] = min ($limit, 300); // 1-300
            if ($since !== null) {
                $request['to'] = $since . $limit * $duration * 1000;
            }
        } else {
            if ($since !== null) {
                throw new ExchangeError ($this->id . ' fetchOHLCV requires a $limit argument if the $since argument is specified');
            }
        }
        $response = $this->marketGetKline (array_merge ($request, $params));
        //
        //     {    status =>    200,
        //             msg =>   "",
        //            data => {       bars => array ( array (     time => "1535973420000",
        //                                        open => "0.03975084",
        //                                        high => "0.03975084",
        //                                         low => "0.03967700",
        //                                       close => "0.03967700",
        //                                      volume => "12.4733",
        //                                    datetime => "2018-09-03 19:17:00" ),
        //                                  array (     time => "1535955480000",
        //                                        open => "0.04009900",
        //                                        high => "0.04016745",
        //                                         low => "0.04009900",
        //                                       close => "0.04012074",
        //                                      volume => "74.4803",
        //                                    datetime => "2018-09-03 14:18:00" }  ),
        //                    resolution =>   "1min",
        //                        $symbol =>   "eth_btc",
        //                          from =>   "1535973420000",
        //                            to =>   "1535955480000",
        //                          size =>    300                                    ),
        //            time =>    1535973435,
        //       microtime =>   "0.56462100 1535973435",
        //          source =>   "api"                                                    }
        //
        return $this->parse_ohlcvs($response['data']['bars'], $market, $timeframe, $since, $limit);
    }

    public function parse_order_status ($status) {
        $statuses = array (
            '0' => 'open',
            '1' => 'open', // partially filled
            '2' => 'closed', // filled
            '3' => 'canceled',
        );
        if (is_array ($statuses) && array_key_exists ($status, $statuses)) {
            return $statuses[$status];
        }
        return $status;
    }

    public function parse_order ($order, $market = null) {
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
                if (is_array ($this->markets_by_id) && array_key_exists ($marketId, $this->markets_by_id)) {
                    $market = $this->safe_value($this->markets_by_id, $marketId);
                } else {
                    $base = strtoupper ($baseId);
                    $quote = strtoupper ($quoteId);
                    $base = $this->common_currency_code($base);
                    $quote = $this->common_currency_code($quote);
                    $symbol = $base . '/' . $quote;
                }
            }
        }
        if ($market !== null)
            $symbol = $market['symbol'];
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
            $timestamp = $this->safe_integer($order, 'created');
            if ($timestamp !== null) {
                $timestamp = $timestamp * 1000;
            }
        }
        $cost = $this->safe_float($order, 'orderTotalPrice');
        if ($price !== null) {
            if ($filled !== null) {
                $cost = $filled * $price;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        return array (
            'id' => $id,
            'datetime' => $this->iso8601 ($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => null,
            'info' => $order,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type !== 'limit') {
            throw new ExchangeError ($this->id . ' createOrder allows limit orders only');
        }
        $market = $this->market ($symbol);
        $orderType = ($side === 'buy') ? '1' : '2';
        if (!$this->password)
            throw new ExchangeError ($this->id . ' createOrder() requires you to set exchange.password = "YOUR_TRADING_PASSWORD" (a trade password is NOT THE SAME as your login password)');
        $request = array (
            'symbol' => $market['id'],
            'type' => $orderType,
            'price' => $this->price_to_precision($symbol, $price),
            'number' => $this->amount_to_precision($symbol, $amount),
            'tradePwd' => $this->password,
        );
        $response = $this->tradePostAddEntrustSheet (array_merge ($request, $params));
        //
        //     {
        //         "status" => 200,
        //         "msg" => "",
        //         "data" => array (
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
        $timestamp = $this->parse_microtime ($this->safe_string($response, 'microtime'));
        $order = array_merge (array (
            'timestamp' => $timestamp,
        ), $response['data']);
        return $this->parse_order($order, $market);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->tradePostCancelEntrustSheet (array_merge (array (
            'entrustSheetId' => $id,
        ), $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":{
        //             "updateAssetsData":array (
        //                 "coin":"bz",
        //                 "over":"1000.00000000",
        //                 "lock":"-1000.00000000"
        //             ),
        //             "assetsInfo":array (
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

    public function cancel_orders ($ids, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = $this->tradePostCancelEntrustSheet (array_merge (array (
            'ids' => implode (',', $ids),
        ), $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":{
        //             "744173808":{
        //                 "updateAssetsData":array (
        //                     "coin":"bz",
        //                     "over":"100.00000000",
        //                     "lock":"-100.00000000"
        //                 ),
        //                 "assetsInfo":array (
        //                     "coin":"bz",
        //                     "over":"899.99999999",
        //                     "lock":"19099.99999999"
        //                 }
        //             ),
        //             "744173809":{
        //                 "updateAssetsData":array (
        //                     "coin":"bz",
        //                     "over":"100.00000000",
        //                     "lock":"-100.00000000"
        //                 ),
        //                 "assetsInfo":array (
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

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'entrustSheetId' => $id,
        );
        $response = $this->tradePostGetEntrustSheetInfo (array_merge ($request, $params));
        //
        //     {
        //         "status":200,
        //         "msg":"",
        //         "data":array (
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

    public function fetch_orders_with_method ($method, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbol === null) {
            throw new ArgumentsRequired ($this->id . ' fetchOpenOrders requires a $symbol argument');
        }
        $market = $this->market ($symbol);
        $request = array (
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
            $request['startTime'] = intval ($since / 1000);
            // $request['endTime'] = intval ($since / 1000);
        }
        $response = $this->$method (array_merge ($request, $params));
        //
        //     {
        //         "status" => 200,
        //         "msg" => "",
        //         "data" => {
        //             "data" => array (
        //                 array (
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
        //                 array (
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
        //             "pageInfo" => array (
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
        $orders = $this->safe_value($response['data'], 'data');
        if ($orders) {
            return $this->parse_orders($response['data']['data'], null, $since, $limit);
        } else {
            return array ();
        }
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method ('tradePostGetUserHistoryEntrustSheet', $symbol, $since, $limit, $params);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method ('tradePostGetUserNowEntrustSheet', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_with_method ('tradePostGetUserHistoryEntrustSheet', $symbol, $since, $limit, $params);
    }

    public function nonce () {
        $currentTimestamp = $this->seconds ();
        if ($currentTimestamp > $this->options['lastNonceTimestamp']) {
            $this->options['lastNonceTimestamp'] = $currentTimestamp;
            $this->options['lastNonce'] = 100000;
        }
        $this->options['lastNonce'] = $this->sum ($this->options['lastNonce'], 1);
        return $this->options['lastNonce'];
    }

    public function sign ($path, $api = 'market', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->capitalize ($api) . '/' . $path;
        $query = null;
        if ($api === 'market') {
            $query = $this->urlencode ($params);
            if (strlen ($query))
                $url .= '?' . $query;
        } else {
            $this->check_required_credentials();
            $body = $this->rawencode ($this->keysort (array_merge (array (
                'apiKey' => $this->apiKey,
                'timeStamp' => $this->seconds (),
                'nonce' => $this->nonce (),
            ), $params)));
            $body .= '&sign=' . $this->hash ($this->encode ($body . $this->secret));
            $headers = array ( 'Content-type' => 'application/x-www-form-urlencoded' );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body) {
        if (gettype ($body) !== 'string')
            return; // fallback to default error handler
        if (strlen ($body) < 2)
            return; // fallback to default error handler
        if (($body[0] === '{') || ($body[0] === '[')) {
            $response = json_decode ($body, $as_associative_array = true);
            $status = $this->safe_string($response, 'status');
            if ($status !== null) {
                $feedback = $this->id . ' ' . $body;
                $exceptions = $this->exceptions;
                //
                //     array ("$status":-107,"msg":"","data":"","time":1535968848,"microtime":"0.89092200 1535968848","source":"api")
                //
                if ($status === '200') {
                    //
                    //     array ("$status":200,"msg":"","data":-200031,"time":1535999806,"microtime":"0.85476800 1535999806","source":"api")
                    //
                    $code = $this->safe_integer($response, 'data');
                    if ($code !== null) {
                        if (is_array ($exceptions) && array_key_exists ($code, $exceptions)) {
                            throw new $exceptions[$code] ($feedback);
                        } else {
                            throw new ExchangeError ($feedback);
                        }
                    } else {
                        return; // no error
                    }
                }
                if (is_array ($exceptions) && array_key_exists ($status, $exceptions)) {
                    throw new $exceptions[$status] ($feedback);
                } else {
                    throw new ExchangeError ($feedback);
                }
            }
        }
    }
}

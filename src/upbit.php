<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AddressPending;
use \ccxt\InvalidOrder;

class upbit extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'upbit',
            'name' => 'Upbit',
            'countries' => array( 'KR' ),
            'version' => 'v1',
            'rateLimit' => 1000,
            'pro' => true,
            // new metainfo interface
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createDepositAddress' => true,
                'createMarketOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => false,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrderBooks' => true,
                'fetchOrders' => false,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => 'minutes',
                '3m' => 'minutes',
                '5m' => 'minutes',
                '15m' => 'minutes',
                '30m' => 'minutes',
                '1h' => 'minutes',
                '4h' => 'minutes',
                '1d' => 'days',
                '1w' => 'weeks',
                '1M' => 'months',
            ),
            'hostname' => 'api.upbit.com',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/49245610-eeaabe00-f423-11e8-9cba-4b0aed794799.jpg',
                'api' => array(
                    'public' => 'https://{hostname}',
                    'private' => 'https://{hostname}',
                ),
                'www' => 'https://upbit.com',
                'doc' => 'https://docs.upbit.com/docs/%EC%9A%94%EC%B2%AD-%EC%88%98-%EC%A0%9C%ED%95%9C',
                'fees' => 'https://upbit.com/service_center/guide',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'market/all',
                        'candles/{timeframe}',
                        'candles/{timeframe}/{unit}',
                        'candles/minutes/{unit}',
                        'candles/minutes/1',
                        'candles/minutes/3',
                        'candles/minutes/5',
                        'candles/minutes/15',
                        'candles/minutes/30',
                        'candles/minutes/60',
                        'candles/minutes/240',
                        'candles/days',
                        'candles/weeks',
                        'candles/months',
                        'trades/ticks',
                        'ticker',
                        'orderbook',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts',
                        'orders/chance',
                        'order',
                        'orders',
                        'withdraws',
                        'withdraw',
                        'withdraws/chance',
                        'deposits',
                        'deposit',
                        'deposits/coin_addresses',
                        'deposits/coin_address',
                    ),
                    'post' => array(
                        'orders',
                        'withdraws/coin',
                        'withdraws/krw',
                        'deposits/generate_coin_address',
                    ),
                    'delete' => array(
                        'order',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.0025,
                    'taker' => 0.0025,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(),
                    'deposit' => array(),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'This key has expired.' => '\\ccxt\\AuthenticationError',
                    'Missing request parameter error. Check the required parameters!' => '\\ccxt\\BadRequest',
                    'side is missing, side does not have a valid value' => '\\ccxt\\InvalidOrder',
                ),
                'broad' => array(
                    'thirdparty_agreement_required' => '\\ccxt\\PermissionDenied',
                    'out_of_scope' => '\\ccxt\\PermissionDenied',
                    'order_not_found' => '\\ccxt\\OrderNotFound',
                    'insufficient_funds' => '\\ccxt\\InsufficientFunds',
                    'invalid_access_key' => '\\ccxt\\AuthenticationError',
                    'jwt_verification' => '\\ccxt\\AuthenticationError',
                    'create_ask_error' => '\\ccxt\\ExchangeError',
                    'create_bid_error' => '\\ccxt\\ExchangeError',
                    'volume_too_large' => '\\ccxt\\InvalidOrder',
                    'invalid_funds' => '\\ccxt\\InvalidOrder',
                ),
            ),
            'options' => array(
                'createMarketBuyOrderRequiresPrice' => true,
                'fetchTickersMaxLength' => 4096, // 2048,
                'fetchOrderBooksMaxLength' => 4096, // 2048,
                'tradingFeesByQuoteCurrency' => array(
                    'KRW' => 0.0005,
                ),
            ),
            'commonCurrencies' => array(
                'TON' => 'Tokamak Network',
            ),
        ));
    }

    public function fetch_currency($code, $params = array ()) {
        // this method is for retrieving funding fees and limits per $currency
        // it requires private access and API keys properly set up
        $this->load_markets();
        $currency = $this->currency($code);
        return $this->fetch_currency_by_id($currency['id'], $params);
    }

    public function fetch_currency_by_id($id, $params = array ()) {
        // this method is for retrieving funding fees and limits per currency
        // it requires private access and API keys properly set up
        $request = array(
            'currency' => $id,
        );
        $response = $this->privateGetWithdrawsChance (array_merge($request, $params));
        //
        //     {
        //         "member_level" => array(
        //             "security_level" => 3,
        //             "fee_level" => 0,
        //             "email_verified" => true,
        //             "identity_auth_verified" => true,
        //             "bank_account_verified" => true,
        //             "kakao_pay_auth_verified" => false,
        //             "$locked" => false,
        //             "wallet_locked" => false
        //         ),
        //         "currency" => array(
        //             "$code" => "BTC",
        //             "withdraw_fee" => "0.0005",
        //             "is_coin" => true,
        //             "wallet_state" => "working",
        //             "wallet_support" => array( "deposit", "withdraw" )
        //         ),
        //         "account" => array(
        //             "currency" => "BTC",
        //             "balance" => "10.0",
        //             "$locked" => "0.0",
        //             "avg_krw_buy_price" => "8042000",
        //             "modified" => false
        //         ),
        //         "withdraw_limit" => {
        //             "currency" => "BTC",
        //             "minimum" => null,
        //             "onetime" => null,
        //             "daily" => "10.0",
        //             "remaining_daily" => "10.0",
        //             "remaining_daily_krw" => "0.0",
        //             "fixed" => null,
        //             "can_withdraw" => true
        //         }
        //     }
        //
        $memberInfo = $this->safe_value($response, 'member_level', array());
        $currencyInfo = $this->safe_value($response, 'currency', array());
        $withdrawLimits = $this->safe_value($response, 'withdraw_limit', array());
        $canWithdraw = $this->safe_value($withdrawLimits, 'can_withdraw');
        $walletState = $this->safe_string($currencyInfo, 'wallet_state');
        $walletLocked = $this->safe_value($memberInfo, 'wallet_locked');
        $locked = $this->safe_value($memberInfo, 'locked');
        $active = true;
        if (($canWithdraw !== null) && !$canWithdraw) {
            $active = false;
        } else if ($walletState !== 'working') {
            $active = false;
        } else if (($walletLocked !== null) && $walletLocked) {
            $active = false;
        } else if (($locked !== null) && $locked) {
            $active = false;
        }
        $maxOnetimeWithdrawal = $this->safe_float($withdrawLimits, 'onetime');
        $maxDailyWithdrawal = $this->safe_float($withdrawLimits, 'daily', $maxOnetimeWithdrawal);
        $remainingDailyWithdrawal = $this->safe_float($withdrawLimits, 'remaining_daily', $maxDailyWithdrawal);
        $maxWithdrawLimit = null;
        if ($remainingDailyWithdrawal > 0) {
            $maxWithdrawLimit = $remainingDailyWithdrawal;
        } else {
            $maxWithdrawLimit = $maxDailyWithdrawal;
        }
        $precision = null;
        $currencyId = $this->safe_string($currencyInfo, 'code');
        $code = $this->safe_currency_code($currencyId);
        return array(
            'info' => $response,
            'id' => $currencyId,
            'code' => $code,
            'name' => $code,
            'active' => $active,
            'fee' => $this->safe_float($currencyInfo, 'withdraw_fee'),
            'precision' => $precision,
            'limits' => array(
                'withdraw' => array(
                    'min' => $this->safe_float($withdrawLimits, 'minimum'),
                    'max' => $maxWithdrawLimit,
                ),
            ),
        );
    }

    public function fetch_market($symbol, $params = array ()) {
        // this method is for retrieving trading fees and limits per $market
        // it requires private access and API keys properly set up
        $this->load_markets();
        $market = $this->market($symbol);
        return $this->fetch_market_by_id($market['id'], $params);
    }

    public function fetch_market_by_id($id, $params = array ()) {
        // this method is for retrieving trading fees and limits per market
        // it requires private access and API keys properly set up
        $request = array(
            'market' => $id,
        );
        $response = $this->privateGetOrdersChance (array_merge($request, $params));
        //
        //     {     bid_fee =>   "0.0005",
        //           ask_fee =>   "0.0005",
        //            market => array(          $id =>   "KRW-BTC",
        //                             name =>   "BTC/KRW",
        //                      order_types => ["limit"],
        //                      order_sides => ["$ask", "$bid"],
        //                              $bid => array(   currency => "KRW",
        //                                     price_unit =>  null,
        //                                      min_total =>  1000  ),
        //                              $ask => array(   currency => "BTC",
        //                                     price_unit =>  null,
        //                                      min_total =>  1000  ),
        //                        max_total =>   "1000000000.0",
        //                            $state =>   "$active"              ),
        //       bid_account => array(          currency => "KRW",
        //                                balance => "0.0",
        //                                 locked => "0.0",
        //                      avg_krw_buy_price => "0",
        //                               modified =>  false ),
        //       ask_account => {          currency => "BTC",
        //                                balance => "0.00780836",
        //                                 locked => "0.0",
        //                      avg_krw_buy_price => "6465564.67",
        //                               modified =>  false        }      }
        //
        $marketInfo = $this->safe_value($response, 'market');
        $bid = $this->safe_value($marketInfo, 'bid');
        $ask = $this->safe_value($marketInfo, 'ask');
        $marketId = $this->safe_string($marketInfo, 'id');
        $baseId = $this->safe_string($ask, 'currency');
        $quoteId = $this->safe_string($bid, 'currency');
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        $symbol = $base . '/' . $quote;
        $precision = array(
            'amount' => 8,
            'price' => 8,
        );
        $state = $this->safe_string($marketInfo, 'state');
        $active = ($state === 'active');
        $bidFee = $this->safe_float($response, 'bid_fee');
        $askFee = $this->safe_float($response, 'ask_fee');
        $fee = max ($bidFee, $askFee);
        return array(
            'info' => $response,
            'id' => $marketId,
            'symbol' => $symbol,
            'base' => $base,
            'quote' => $quote,
            'baseId' => $baseId,
            'quoteId' => $quoteId,
            'active' => $active,
            'precision' => $precision,
            'maker' => $fee,
            'taker' => $fee,
            'limits' => array(
                'amount' => array(
                    'min' => $this->safe_float($ask, 'min_total'),
                    'max' => null,
                ),
                'price' => array(
                    'min' => pow(10, -$precision['price']),
                    'max' => null,
                ),
                'cost' => array(
                    'min' => $this->safe_float($bid, 'min_total'),
                    'max' => $this->safe_float($marketInfo, 'max_total'),
                ),
            ),
        );
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetMarketAll ($params);
        //
        //     array( array(       $market => "KRW-BTC",
        //          korean_name => "비트코인",
        //         english_name => "Bitcoin"  ),
        //       array(       $market => "KRW-DASH",
        //          korean_name => "대시",
        //         english_name => "Dash"      ),
        //       array(       $market => "KRW-ETH",
        //          korean_name => "이더리움",
        //         english_name => "Ethereum" ),
        //       array(       $market => "BTC-ETH",
        //          korean_name => "이더리움",
        //         english_name => "Ethereum" ),
        //       ...,
        //       {       $market => "BTC-BSV",
        //          korean_name => "비트코인에스브이",
        //         english_name => "Bitcoin SV" } )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'market');
            list($quoteId, $baseId) = explode('-', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => 8,
                'price' => 8,
            );
            $active = true;
            $makerFee = $this->safe_float($this->options['tradingFeesByQuoteCurrency'], $quote, $this->fees['trading']['maker']);
            $takerFee = $this->safe_float($this->options['tradingFeesByQuoteCurrency'], $quote, $this->fees['trading']['taker']);
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
                'maker' => $makerFee,
                'taker' => $takerFee,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => null,
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
        $response = $this->privateGetAccounts ($params);
        //
        //     array( array(          currency => "BTC",
        //                   $balance => "0.005",
        //                    locked => "0.0",
        //         avg_krw_buy_price => "7446000",
        //                  modified =>  false     ),
        //       {          currency => "ETH",
        //                   $balance => "0.1",
        //                    locked => "0.0",
        //         avg_krw_buy_price => "250000",
        //                  modified =>  false    }   )
        //
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'balance');
            $account['used'] = $this->safe_float($balance, 'locked');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_books($symbols = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $ids = null;
        if ($symbols === null) {
            $ids = implode(',', $this->ids);
            // max URL length is 2083 $symbols, including http schema, hostname, tld, etc...
            if (strlen($ids) > $this->options['fetchOrderBooksMaxLength']) {
                $numIds = is_array($this->ids) ? count($this->ids) : 0;
                throw new ExchangeError($this->id . ' has ' . (string) $numIds . ' $symbols (' . (string) strlen($ids) . ' characters) exceeding max URL length (' . (string) $this->options['fetchOrderBooksMaxLength'] . ' characters), you are required to specify a list of $symbols in the first argument to fetchOrderBooks');
            }
        } else {
            $ids = $this->market_ids($symbols);
            $ids = implode(',', $ids);
        }
        $request = array(
            'markets' => $ids,
        );
        $response = $this->publicGetOrderbook (array_merge($request, $params));
        //
        //     array( {          market =>   "BTC-ETH",
        //               $timestamp =>    1542899030043,
        //          total_ask_size =>    109.57065201,
        //          total_bid_size =>    125.74430631,
        //         orderbook_units => array( array( ask_price => 0.02926679,
        //                              bid_price => 0.02919904,
        //                               ask_size => 4.20293961,
        //                               bid_size => 11.65043576 ),
        //                            ...,
        //                            array( ask_price => 0.02938209,
        //                              bid_price => 0.0291231,
        //                               ask_size => 0.05135782,
        //                               bid_size => 13.5595     }   ) ),
        //       {          market =>   "KRW-BTC",
        //               $timestamp =>    1542899034662,
        //          total_ask_size =>    12.89790974,
        //          total_bid_size =>    4.88395783,
        //         orderbook_units => array( array( ask_price => 5164000,
        //                              bid_price => 5162000,
        //                               ask_size => 2.57606495,
        //                               bid_size => 0.214       ),
        //                            ...,
        //                            { ask_price => 5176000,
        //                              bid_price => 5152000,
        //                               ask_size => 2.752,
        //                               bid_size => 0.4650305 }    ) }   )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $orderbook = $response[$i];
            $marketId = $this->safe_string($orderbook, 'market');
            $symbol = $this->safe_symbol($marketId, null, '-');
            $timestamp = $this->safe_integer($orderbook, 'timestamp');
            $result[$symbol] = array(
                'bids' => $this->sort_by($this->parse_bids_asks($orderbook['orderbook_units'], 'bid_price', 'bid_size'), 0, true),
                'asks' => $this->sort_by($this->parse_bids_asks($orderbook['orderbook_units'], 'ask_price', 'ask_size'), 0),
                'timestamp' => $timestamp,
                'datetime' => $this->iso8601($timestamp),
                'nonce' => null,
            );
        }
        return $result;
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $orderbooks = $this->fetch_order_books(array( $symbol ), $limit, $params);
        return $this->safe_value($orderbooks, $symbol);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //       {                $market => "BTC-ETH",
        //                    trade_date => "20181122",
        //                    trade_time => "104543",
        //                trade_date_kst => "20181122",
        //                trade_time_kst => "194543",
        //               trade_timestamp =>  1542883543097,
        //                 opening_price =>  0.02976455,
        //                    high_price =>  0.02992577,
        //                     low_price =>  0.02934283,
        //                   trade_price =>  0.02947773,
        //            prev_closing_price =>  0.02966,
        //                        $change => "FALL",
        //                  change_price =>  0.00018227,
        //                   change_rate =>  0.0061453136,
        //           signed_change_price =>  -0.00018227,
        //            signed_change_rate =>  -0.0061453136,
        //                  trade_volume =>  1.00000005,
        //               acc_trade_price =>  100.95825586,
        //           acc_trade_price_24h =>  289.58650166,
        //              acc_trade_volume =>  3409.85311036,
        //          acc_trade_volume_24h =>  9754.40510513,
        //         highest_52_week_price =>  0.12345678,
        //          highest_52_week_date => "2018-02-01",
        //          lowest_52_week_price =>  0.023936,
        //           lowest_52_week_date => "2017-12-08",
        //                     $timestamp =>  1542883543813  }
        //
        $timestamp = $this->safe_integer($ticker, 'trade_timestamp');
        $marketId = $this->safe_string_2($ticker, 'market', 'code');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $previous = $this->safe_float($ticker, 'prev_closing_price');
        $last = $this->safe_float($ticker, 'trade_price');
        $change = $this->safe_float($ticker, 'signed_change_price');
        $percentage = $this->safe_float($ticker, 'signed_change_rate');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high_price'),
            'low' => $this->safe_float($ticker, 'low_price'),
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'opening_price'),
            'close' => $last,
            'last' => $last,
            'previousClose' => $previous,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'acc_trade_volume_24h'),
            'quoteVolume' => $this->safe_float($ticker, 'acc_trade_price_24h'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $ids = null;
        if ($symbols === null) {
            $ids = implode(',', $this->ids);
            // max URL length is 2083 $symbols, including http schema, hostname, tld, etc...
            if (strlen($ids) > $this->options['fetchTickersMaxLength']) {
                $numIds = is_array($this->ids) ? count($this->ids) : 0;
                throw new ExchangeError($this->id . ' has ' . (string) $numIds . ' $symbols exceeding max URL length, you are required to specify a list of $symbols in the first argument to fetchTickers');
            }
        } else {
            $ids = $this->market_ids($symbols);
            $ids = implode(',', $ids);
        }
        $request = array(
            'markets' => $ids,
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        //
        //     array( {                market => "BTC-ETH",
        //                    trade_date => "20181122",
        //                    trade_time => "104543",
        //                trade_date_kst => "20181122",
        //                trade_time_kst => "194543",
        //               trade_timestamp =>  1542883543097,
        //                 opening_price =>  0.02976455,
        //                    high_price =>  0.02992577,
        //                     low_price =>  0.02934283,
        //                   trade_price =>  0.02947773,
        //            prev_closing_price =>  0.02966,
        //                        change => "FALL",
        //                  change_price =>  0.00018227,
        //                   change_rate =>  0.0061453136,
        //           signed_change_price =>  -0.00018227,
        //            signed_change_rate =>  -0.0061453136,
        //                  trade_volume =>  1.00000005,
        //               acc_trade_price =>  100.95825586,
        //           acc_trade_price_24h =>  289.58650166,
        //              acc_trade_volume =>  3409.85311036,
        //          acc_trade_volume_24h =>  9754.40510513,
        //         highest_52_week_price =>  0.12345678,
        //          highest_52_week_date => "2018-02-01",
        //          lowest_52_week_price =>  0.023936,
        //           lowest_52_week_date => "2017-12-08",
        //                     timestamp =>  1542883543813  } )
        //
        $result = array();
        for ($t = 0; $t < count($response); $t++) {
            $ticker = $this->parse_ticker($response[$t]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $tickers = $this->fetch_tickers(array( $symbol ), $params);
        return $this->safe_value($tickers, $symbol);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades
        //
        //       {             $market => "BTC-ETH",
        //             trade_date_utc => "2018-11-22",
        //             trade_time_utc => "13:55:24",
        //                  $timestamp =>  1542894924397,
        //                trade_price =>  0.02914289,
        //               trade_volume =>  0.20074397,
        //         prev_closing_price =>  0.02966,
        //               change_price =>  -0.00051711,
        //                    ask_bid => "ASK",
        //              sequential_id =>  15428949259430000 }
        //
        // fetchOrder trades
        //
        //         {
        //             "$market" => "KRW-BTC",
        //             "uuid" => "78162304-1a4d-4524-b9e6-c9a9e14d76c3",
        //             "$price" => "101000.0",
        //             "volume" => "0.77368323",
        //             "funds" => "78142.00623",
        //             "ask_fee" => "117.213009345",
        //             "bid_fee" => "117.213009345",
        //             "created_at" => "2018-04-05T14:09:15+09:00",
        //             "$side" => "bid",
        //         }
        //
        $id = $this->safe_string_2($trade, 'sequential_id', 'uuid');
        $orderId = null;
        $timestamp = $this->safe_integer($trade, 'timestamp');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($trade, 'created_at'));
        }
        $side = null;
        $askOrBid = $this->safe_string_lower_2($trade, 'ask_bid', 'side');
        if ($askOrBid === 'ask') {
            $side = 'sell';
        } else if ($askOrBid === 'bid') {
            $side = 'buy';
        }
        $cost = $this->safe_float($trade, 'funds');
        $price = $this->safe_float_2($trade, 'trade_price', 'price');
        $amount = $this->safe_float_2($trade, 'trade_volume', 'volume');
        if ($cost === null) {
            if ($amount !== null) {
                if ($price !== null) {
                    $cost = $price * $amount;
                }
            }
        }
        $marketId = $this->safe_string_2($trade, 'market', 'code');
        $market = $this->safe_market($marketId, $market);
        $fee = null;
        $feeCurrency = null;
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            $feeCurrency = $market['quote'];
        } else {
            list($baseId, $quoteId) = explode('-', $marketId);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $feeCurrency = $quote;
        }
        $feeCost = $this->safe_string($trade, $askOrBid . '_fee');
        if ($feeCost !== null) {
            $fee = array(
                'currency' => $feeCurrency,
                'cost' => $feeCost,
            );
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 200;
        }
        $request = array(
            'market' => $market['id'],
            'count' => $limit,
        );
        $response = $this->publicGetTradesTicks (array_merge($request, $params));
        //
        //     array( array(             $market => "BTC-ETH",
        //             trade_date_utc => "2018-11-22",
        //             trade_time_utc => "13:55:24",
        //                  timestamp =>  1542894924397,
        //                trade_price =>  0.02914289,
        //               trade_volume =>  0.20074397,
        //         prev_closing_price =>  0.02966,
        //               change_price =>  -0.00051711,
        //                    ask_bid => "ASK",
        //              sequential_id =>  15428949259430000 ),
        //       {             $market => "BTC-ETH",
        //             trade_date_utc => "2018-11-22",
        //             trade_time_utc => "13:03:10",
        //                  timestamp =>  1542891790123,
        //                trade_price =>  0.02917,
        //               trade_volume =>  7.392,
        //         prev_closing_price =>  0.02966,
        //               change_price =>  -0.00049,
        //                    ask_bid => "ASK",
        //              sequential_id =>  15428917910540000 }  )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         $market => "BTC-ETH",
        //         candle_date_time_utc => "2018-11-22T13:47:00",
        //         candle_date_time_kst => "2018-11-22T22:47:00",
        //         opening_price => 0.02915963,
        //         high_price => 0.02915963,
        //         low_price => 0.02915448,
        //         trade_price => 0.02915448,
        //         timestamp => 1542894473674,
        //         candle_acc_trade_price => 0.0981629437535248,
        //         candle_acc_trade_volume => 3.36693173,
        //         unit => 1
        //     }
        //
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'candle_date_time_utc')),
            $this->safe_float($ohlcv, 'opening_price'),
            $this->safe_float($ohlcv, 'high_price'),
            $this->safe_float($ohlcv, 'low_price'),
            $this->safe_float($ohlcv, 'trade_price'),
            $this->safe_float($ohlcv, 'candle_acc_trade_volume'), // base volume
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $timeframePeriod = $this->parse_timeframe($timeframe);
        $timeframeValue = $this->timeframes[$timeframe];
        if ($limit === null) {
            $limit = 200;
        }
        $request = array(
            'market' => $market['id'],
            'timeframe' => $timeframeValue,
            'count' => $limit,
        );
        $method = 'publicGetCandlesTimeframe';
        if ($timeframeValue === 'minutes') {
            $numMinutes = (int) round($timeframePeriod / 60);
            $request['unit'] = $numMinutes;
            $method .= 'Unit';
        }
        if ($since !== null) {
            // convert `$since` to `to` value
            $request['to'] = $this->iso8601($this->sum($since, $timeframePeriod * $limit * 1000));
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             $market => "BTC-ETH",
        //             candle_date_time_utc => "2018-11-22T13:47:00",
        //             candle_date_time_kst => "2018-11-22T22:47:00",
        //             opening_price => 0.02915963,
        //             high_price => 0.02915963,
        //             low_price => 0.02915448,
        //             trade_price => 0.02915448,
        //             timestamp => 1542894473674,
        //             candle_acc_trade_price => 0.0981629437535248,
        //             candle_acc_trade_volume => 3.36693173,
        //             unit => 1
        //         ),
        //         {
        //             $market => "BTC-ETH",
        //             candle_date_time_utc => "2018-11-22T10:06:00",
        //             candle_date_time_kst => "2018-11-22T19:06:00",
        //             opening_price => 0.0294,
        //             high_price => 0.02940882,
        //             low_price => 0.02934283,
        //             trade_price => 0.02937354,
        //             timestamp => 1542881219276,
        //             candle_acc_trade_price => 0.0762597110943884,
        //             candle_acc_trade_volume => 2.5949617,
        //             unit => 1
        //         }
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            // for $market buy it requires the $amount of quote currency to spend
            if ($side === 'buy') {
                if ($this->options['createMarketBuyOrderRequiresPrice']) {
                    if ($price === null) {
                        throw new InvalidOrder($this->id . " createOrder() requires the $price argument with $market buy orders to calculate total order cost ($amount to spend), where cost = $amount * $price-> Supply a $price argument to createOrder() call if you want the cost to be calculated for you from $price and $amount, or, alternatively, add .options['createMarketBuyOrderRequiresPrice'] = false to supply the cost in the $amount argument (the exchange-specific behaviour)");
                    } else {
                        $amount = $amount * $price;
                    }
                }
            }
        }
        $orderSide = null;
        if ($side === 'buy') {
            $orderSide = 'bid';
        } else if ($side === 'sell') {
            $orderSide = 'ask';
        } else {
            throw new InvalidOrder($this->id . ' createOrder allows buy or sell $side only!');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'side' => $orderSide,
        );
        if ($type === 'limit') {
            $request['volume'] = $this->amount_to_precision($symbol, $amount);
            $request['price'] = $this->price_to_precision($symbol, $price);
            $request['ord_type'] = $type;
        } else if ($type === 'market') {
            if ($side === 'buy') {
                $request['ord_type'] = 'price';
                $request['price'] = $this->price_to_precision($symbol, $amount);
            } else if ($side === 'sell') {
                $request['ord_type'] = $type;
                $request['volume'] = $this->amount_to_precision($symbol, $amount);
            }
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //     {
        //         'uuid' => 'cdd92199-2897-4e14-9448-f923320408ad',
        //         'side' => 'bid',
        //         'ord_type' => 'limit',
        //         'price' => '100.0',
        //         'avg_price' => '0.0',
        //         'state' => 'wait',
        //         'market' => 'KRW-BTC',
        //         'created_at' => '2018-04-10T15:42:23+09:00',
        //         'volume' => '0.01',
        //         'remaining_volume' => '0.01',
        //         'reserved_fee' => '0.0015',
        //         'remaining_fee' => '0.0015',
        //         'paid_fee' => '0.0',
        //         'locked' => '1.0015',
        //         'executed_volume' => '0.0',
        //         'trades_count' => 0
        //     }
        //
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'uuid' => $id,
        );
        $response = $this->privateDeleteOrder (array_merge($request, $params));
        //
        //     {
        //         "uuid" => "cdd92199-2897-4e14-9448-f923320408ad",
        //         "side" => "bid",
        //         "ord_type" => "limit",
        //         "price" => "100.0",
        //         "state" => "wait",
        //         "market" => "KRW-BTC",
        //         "created_at" => "2018-04-10T15:42:23+09:00",
        //         "volume" => "0.01",
        //         "remaining_volume" => "0.01",
        //         "reserved_fee" => "0.0015",
        //         "remaining_fee" => "0.0015",
        //         "paid_fee" => "0.0",
        //         "locked" => "1.0015",
        //         "executed_volume" => "0.0",
        //         "trades_count" => 0
        //     }
        //
        return $this->parse_order($response);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'page' => 1,
            // 'order_by' => 'asc', // 'desc'
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default is 100
        }
        $response = $this->privateGetDeposits (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             "type" => "deposit",
        //             "uuid" => "94332e99-3a87-4a35-ad98-28b0c969f830",
        //             "$currency" => "KRW",
        //             "txid" => "9e37c537-6849-4c8b-a134-57313f5dfc5a",
        //             "state" => "ACCEPTED",
        //             "created_at" => "2017-12-08T15:38:02+09:00",
        //             "done_at" => "2017-12-08T15:38:02+09:00",
        //             "amount" => "100000.0",
        //             "fee" => "0.0"
        //         ),
        //         ...,
        //     )
        //
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'state' => 'submitting', // 'submitted', 'almost_accepted', 'rejected', 'accepted', 'processing', 'done', 'canceled'
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default is 100
        }
        $response = $this->privateGetWithdraws (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             "type" => "withdraw",
        //             "uuid" => "9f432943-54e0-40b7-825f-b6fec8b42b79",
        //             "$currency" => "BTC",
        //             "txid" => null,
        //             "state" => "processing",
        //             "created_at" => "2018-04-13T11:24:01+09:00",
        //             "done_at" => null,
        //             "amount" => "0.01",
        //             "fee" => "0.0",
        //             "krw_amount" => "80420.0"
        //         ),
        //         ...,
        //     )
        //
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'ACCEPTED' => 'ok', // deposits
            // withdrawals:
            'submitting' => 'pending', // 처리 중
            'submitted' => 'pending', // 처리 완료
            'almost_accepted' => 'pending', // 출금대기중
            'rejected' => 'failed', // 거부
            'accepted' => 'pending', // 승인됨
            'processing' => 'pending', // 처리 중
            'done' => 'ok', // 완료
            'canceled' => 'canceled', // 취소됨
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         "$type" => "deposit",
        //         "uuid" => "94332e99-3a87-4a35-ad98-28b0c969f830",
        //         "$currency" => "KRW",
        //         "$txid" => "9e37c537-6849-4c8b-a134-57313f5dfc5a",
        //         "state" => "ACCEPTED",
        //         "created_at" => "2017-12-08T15:38:02+09:00",
        //         "done_at" => "2017-12-08T15:38:02+09:00",
        //         "$amount" => "100000.0",
        //         "fee" => "0.0"
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         "$type" => "withdraw",
        //         "uuid" => "9f432943-54e0-40b7-825f-b6fec8b42b79",
        //         "$currency" => "BTC",
        //         "$txid" => "cd81e9b45df8da29f936836e58c907a106057e454a45767a7b06fcb19b966bba",
        //         "state" => "processing",
        //         "created_at" => "2018-04-13T11:24:01+09:00",
        //         "done_at" => null,
        //         "$amount" => "0.01",
        //         "fee" => "0.0",
        //         "krw_amount" => "80420.0"
        //     }
        //
        $id = $this->safe_string($transaction, 'uuid');
        $amount = $this->safe_float($transaction, 'amount');
        $address = null; // not present in the data structure received from the exchange
        $tag = null; // not present in the data structure received from the exchange
        $txid = $this->safe_string($transaction, 'txid');
        $updated = $this->parse8601($this->safe_string($transaction, 'done_at'));
        $timestamp = $this->parse8601($this->safe_string($transaction, 'created_at', $updated));
        $type = $this->safe_string($transaction, 'type');
        if ($type === 'withdraw') {
            $type = 'withdrawal';
        }
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'state'));
        $feeCost = $this->safe_float($transaction, 'fee');
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => $tag,
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

    public function parse_order_status($status) {
        $statuses = array(
            'wait' => 'open',
            'done' => 'closed',
            'cancel' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "uuid" => "a08f09b1-1718-42e2-9358-f0e5e083d3ee",
        //         "$side" => "bid",
        //         "ord_type" => "limit",
        //         "$price" => "17417000.0",
        //         "state" => "done",
        //         "$market" => "KRW-BTC",
        //         "created_at" => "2018-04-05T14:09:14+09:00",
        //         "volume" => "1.0",
        //         "remaining_volume" => "0.0",
        //         "reserved_fee" => "26125.5",
        //         "remaining_fee" => "25974.0",
        //         "paid_fee" => "151.5",
        //         "locked" => "17341974.0",
        //         "executed_volume" => "1.0",
        //         "trades_count" => 2,
        //         "$trades" => array(
        //             array(
        //                 "$market" => "KRW-BTC",
        //                 "uuid" => "78162304-1a4d-4524-b9e6-c9a9e14d76c3",
        //                 "$price" => "101000.0",
        //                 "volume" => "0.77368323",
        //                 "funds" => "78142.00623",
        //                 "ask_fee" => "117.213009345",
        //                 "bid_fee" => "117.213009345",
        //                 "created_at" => "2018-04-05T14:09:15+09:00",
        //                 "$side" => "bid",
        //             ),
        //             array(
        //                 "$market" => "KRW-BTC",
        //                 "uuid" => "f73da467-c42f-407d-92fa-e10d86450a20",
        //                 "$price" => "101000.0",
        //                 "volume" => "0.22631677",
        //                 "funds" => "22857.99377",
        //                 "ask_fee" => "34.286990655", // missing in $market orders
        //                 "bid_fee" => "34.286990655", // missing in $market orders
        //                 "created_at" => "2018-04-05T14:09:15+09:00", // missing in $market orders
        //                 "$side" => "bid",
        //             ),
        //         ),
        //     }
        //
        $id = $this->safe_string($order, 'uuid');
        $side = $this->safe_string($order, 'side');
        if ($side === 'bid') {
            $side = 'buy';
        } else {
            $side = 'sell';
        }
        $type = $this->safe_string($order, 'ord_type');
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $lastTradeTimestamp = null;
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'volume');
        $remaining = $this->safe_float($order, 'remaining_volume');
        $filled = $this->safe_float($order, 'executed_volume');
        $cost = null;
        if ($type === 'price') {
            $type = 'market';
            $cost = $price;
            $price = null;
        }
        $average = null;
        $fee = null;
        $feeCost = $this->safe_float($order, 'paid_fee');
        $marketId = $this->safe_string($order, 'market');
        $market = $this->safe_market($marketId, $market);
        $trades = $this->safe_value($order, 'trades', array());
        $trades = $this->parse_trades($trades, $market, null, null, array(
            'order' => $id,
            'type' => $type,
        ));
        $numTrades = is_array($trades) ? count($trades) : 0;
        if ($numTrades > 0) {
            // the $timestamp in fetchOrder $trades is missing
            $lastTradeTimestamp = $trades[$numTrades - 1]['timestamp'];
            $getFeesFromTrades = false;
            if ($feeCost === null) {
                $getFeesFromTrades = true;
                $feeCost = 0;
            }
            $cost = 0;
            for ($i = 0; $i < $numTrades; $i++) {
                $trade = $trades[$i];
                $cost = $this->sum($cost, $trade['cost']);
                if ($getFeesFromTrades) {
                    $tradeFee = $this->safe_value($trades[$i], 'fee', array());
                    $tradeFeeCost = $this->safe_float($tradeFee, 'cost');
                    if ($tradeFeeCost !== null) {
                        $feeCost = $this->sum($feeCost, $tradeFeeCost);
                    }
                }
            }
            $average = $cost / $filled;
        }
        if ($feeCost !== null) {
            $fee = array(
                'currency' => $market['quote'],
                'cost' => $feeCost,
            );
        }
        $result = array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $market['symbol'],
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'average' => $average,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
        );
        return $result;
    }

    public function fetch_orders_by_state($state, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'market' => $this->market_id($symbol),
            'state' => $state,
            // 'page' => 1,
            // 'order_by' => 'asc',
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['market'] = $market['id'];
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             "uuid" => "a08f09b1-1718-42e2-9358-f0e5e083d3ee",
        //             "side" => "bid",
        //             "ord_type" => "$limit",
        //             "price" => "17417000.0",
        //             "$state" => "done",
        //             "$market" => "KRW-BTC",
        //             "created_at" => "2018-04-05T14:09:14+09:00",
        //             "volume" => "1.0",
        //             "remaining_volume" => "0.0",
        //             "reserved_fee" => "26125.5",
        //             "remaining_fee" => "25974.0",
        //             "paid_fee" => "151.5",
        //             "locked" => "17341974.0",
        //             "executed_volume" => "1.0",
        //             "trades_count":2
        //         ),
        //     )
        //
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_state('wait', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_state('done', $symbol, $since, $limit, $params);
    }

    public function fetch_canceled_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_state('cancel', $symbol, $since, $limit, $params);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'uuid' => $id,
        );
        $response = $this->privateGetOrder (array_merge($request, $params));
        //
        //     {
        //         "uuid" => "a08f09b1-1718-42e2-9358-f0e5e083d3ee",
        //         "side" => "bid",
        //         "ord_type" => "limit",
        //         "price" => "17417000.0",
        //         "state" => "done",
        //         "market" => "KRW-BTC",
        //         "created_at" => "2018-04-05T14:09:14+09:00",
        //         "volume" => "1.0",
        //         "remaining_volume" => "0.0",
        //         "reserved_fee" => "26125.5",
        //         "remaining_fee" => "25974.0",
        //         "paid_fee" => "151.5",
        //         "locked" => "17341974.0",
        //         "executed_volume" => "1.0",
        //         "trades_count" => 2,
        //         "trades" => array(
        //             array(
        //                 "market" => "KRW-BTC",
        //                 "uuid" => "78162304-1a4d-4524-b9e6-c9a9e14d76c3",
        //                 "price" => "101000.0",
        //                 "volume" => "0.77368323",
        //                 "funds" => "78142.00623",
        //                 "ask_fee" => "117.213009345",
        //                 "bid_fee" => "117.213009345",
        //                 "created_at" => "2018-04-05T14:09:15+09:00",
        //                 "side" => "bid"
        //             ),
        //             {
        //                 "market" => "KRW-BTC",
        //                 "uuid" => "f73da467-c42f-407d-92fa-e10d86450a20",
        //                 "price" => "101000.0",
        //                 "volume" => "0.22631677",
        //                 "funds" => "22857.99377",
        //                 "ask_fee" => "34.286990655",
        //                 "bid_fee" => "34.286990655",
        //                 "created_at" => "2018-04-05T14:09:15+09:00",
        //                 "side" => "bid"
        //             }
        //         )
        //     }
        //
        return $this->parse_order($response);
    }

    public function fetch_deposit_addresses($codes = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privateGetDepositsCoinAddresses ($params);
        //
        //     array(
        //         array(
        //             "currency" => "BTC",
        //             "deposit_address" => "3EusRwybuZUhVDeHL7gh3HSLmbhLcy7NqD",
        //             "secondary_address" => null
        //         ),
        //         array(
        //             "currency" => "ETH",
        //             "deposit_address" => "0x0d73e0a482b8cf568976d2e8688f4a899d29301c",
        //             "secondary_address" => null
        //         ),
        //         {
        //             "currency" => "XRP",
        //             "deposit_address" => "rN9qNpgnBaZwqCg8CvUZRPqCcPPY7wfWep",
        //             "secondary_address" => "3057887915"
        //         }
        //     )
        //
        return $this->parse_deposit_addresses($response);
    }

    public function parse_deposit_address($depositAddress, $currency = null) {
        //
        //     {
        //         "$currency" => "BTC",
        //         "deposit_address" => "3EusRwybuZUhVDeHL7gh3HSLmbhLcy7NqD",
        //         "secondary_address" => null
        //     }
        //
        $address = $this->safe_string($depositAddress, 'deposit_address');
        $tag = $this->safe_string($depositAddress, 'secondary_address');
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
        $currency = $this->currency($code);
        $response = $this->privateGetDepositsCoinAddress (array_merge(array(
            'currency' => $currency['id'],
        ), $params));
        //
        //     {
        //         "$currency" => "BTC",
        //         "deposit_address" => "3EusRwybuZUhVDeHL7gh3HSLmbhLcy7NqD",
        //         "secondary_address" => null
        //     }
        //
        return $this->parse_deposit_address($response);
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        // https://github.com/ccxt/ccxt/issues/6452
        $response = $this->privatePostDepositsGenerateCoinAddress (array_merge($request, $params));
        //
        // https://docs.upbit.com/v1.0/reference#%EC%9E%85%EA%B8%88-%EC%A3%BC%EC%86%8C-%EC%83%9D%EC%84%B1-%EC%9A%94%EC%B2%AD
        // can be any of the two responses:
        //
        //     {
        //         "success" : true,
        //         "$message" : "Creating BTC deposit address."
        //     }
        //
        //     {
        //         "$currency" => "BTC",
        //         "deposit_address" => "3EusRwybuZUhVDeHL7gh3HSLmbhLcy7NqD",
        //         "secondary_address" => null
        //     }
        //
        $message = $this->safe_string($response, 'message');
        if ($message !== null) {
            throw new AddressPending($this->id . ' is generating ' . $code . ' deposit address, call fetchDepositAddress or createDepositAddress one more time later to retrieve the generated address');
        }
        return $this->parse_deposit_address($response);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'amount' => $amount,
        );
        $method = 'privatePostWithdraws';
        if ($code !== 'KRW') {
            $method .= 'Coin';
            $request['currency'] = $currency['id'];
            $request['address'] = $address;
            if ($tag !== null) {
                $request['secondary_address'] = $tag;
            }
        } else {
            $method .= 'Krw';
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "type" => "withdraw",
        //         "uuid" => "9f432943-54e0-40b7-825f-b6fec8b42b79",
        //         "$currency" => "BTC",
        //         "txid" => "ebe6937b-130e-4066-8ac6-4b0e67f28adc",
        //         "state" => "processing",
        //         "created_at" => "2018-04-13T11:24:01+09:00",
        //         "done_at" => null,
        //         "$amount" => "0.01",
        //         "fee" => "0.0",
        //         "krw_amount" => "80420.0"
        //     }
        //
        return $this->parse_transaction($response);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->implode_params($this->urls['api'][$api], array(
            'hostname' => $this->hostname,
        ));
        $url .= '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($method !== 'POST') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $request = array(
                'access_key' => $this->apiKey,
                'nonce' => $nonce,
            );
            if ($query) {
                $auth = $this->urlencode($query);
                $hash = $this->hash($this->encode($auth), 'sha512');
                $request['query_hash'] = $hash;
                $request['query_hash_alg'] = 'SHA512';
            }
            $jwt = $this->jwt($request, $this->encode($this->secret));
            $headers = array(
                'Authorization' => 'Bearer ' . $jwt,
            );
            if (($method !== 'GET') && ($method !== 'DELETE')) {
                $body = $this->json($params);
                $headers['Content-Type'] = 'application/json';
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        //
        //   array( 'error' => array( 'message' => "Missing request parameter $error-> Check the required parameters!", 'name' => 400 ) ),
        //   array( 'error' => array( 'message' => "side is missing, side does not have a valid value", 'name' => "validation_error" ) ),
        //   array( 'error' => array( 'message' => "개인정보 제 3자 제공 동의가 필요합니다.", 'name' => "thirdparty_agreement_required" ) ),
        //   array( 'error' => array( 'message' => "권한이 부족합니다.", 'name' => "out_of_scope" ) ),
        //   array( 'error' => array( 'message' => "주문을 찾지 못했습니다.", 'name' => "order_not_found" ) ),
        //   array( 'error' => array( 'message' => "주문가능한 금액(ETH)이 부족합니다.", 'name' => "insufficient_funds_ask" ) ),
        //   array( 'error' => array( 'message' => "주문가능한 금액(BTC)이 부족합니다.", 'name' => "insufficient_funds_bid" ) ),
        //   array( 'error' => array( 'message' => "잘못된 엑세스 키입니다.", 'name' => "invalid_access_key" ) ),
        //   array( 'error' => array( 'message' => "Jwt 토큰 검증에 실패했습니다.", 'name' => "jwt_verification" ) )
        //
        $error = $this->safe_value($response, 'error');
        if ($error !== null) {
            $message = $this->safe_string($error, 'message');
            $name = $this->safe_string($error, 'name');
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $name, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $name, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

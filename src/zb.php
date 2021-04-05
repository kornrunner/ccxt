<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;
use \ccxt\ExchangeNotAvailable;

class zb extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'zb',
            'name' => 'ZB',
            'countries' => array( 'CN' ),
            'rateLimit' => 1000,
            'version' => 'v1',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchDepositAddress' => true,
                'fetchDepositAddresses' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchClosedOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1min',
                '3m' => '3min',
                '5m' => '5min',
                '15m' => '15min',
                '30m' => '30min',
                '1h' => '1hour',
                '2h' => '2hour',
                '4h' => '4hour',
                '6h' => '6hour',
                '12h' => '12hour',
                '1d' => '1day',
                '3d' => '3day',
                '1w' => '1week',
            ),
            'exceptions' => array(
                'exact' => array(
                    // '1000' => 'Successful operation',
                    '1001' => '\\ccxt\\ExchangeError', // 'General error message',
                    '1002' => '\\ccxt\\ExchangeError', // 'Internal error',
                    '1003' => '\\ccxt\\AuthenticationError', // 'Verification does not pass',
                    '1004' => '\\ccxt\\AuthenticationError', // 'Funding security password lock',
                    '1005' => '\\ccxt\\AuthenticationError', // 'Funds security password is incorrect, please confirm and re-enter.',
                    '1006' => '\\ccxt\\AuthenticationError', // 'Real-name certification pending approval or audit does not pass',
                    '1009' => '\\ccxt\\ExchangeNotAvailable', // 'This interface is under maintenance',
                    '2001' => '\\ccxt\\InsufficientFunds', // 'Insufficient CNY Balance',
                    '2002' => '\\ccxt\\InsufficientFunds', // 'Insufficient BTC Balance',
                    '2003' => '\\ccxt\\InsufficientFunds', // 'Insufficient LTC Balance',
                    '2005' => '\\ccxt\\InsufficientFunds', // 'Insufficient ETH Balance',
                    '2006' => '\\ccxt\\InsufficientFunds', // 'Insufficient ETC Balance',
                    '2007' => '\\ccxt\\InsufficientFunds', // 'Insufficient BTS Balance',
                    '2009' => '\\ccxt\\InsufficientFunds', // 'Account balance is not enough',
                    '3001' => '\\ccxt\\OrderNotFound', // 'Pending orders not found',
                    '3002' => '\\ccxt\\InvalidOrder', // 'Invalid price',
                    '3003' => '\\ccxt\\InvalidOrder', // 'Invalid amount',
                    '3004' => '\\ccxt\\AuthenticationError', // 'User does not exist',
                    '3005' => '\\ccxt\\BadRequest', // 'Invalid parameter',
                    '3006' => '\\ccxt\\AuthenticationError', // 'Invalid IP or inconsistent with the bound IP',
                    '3007' => '\\ccxt\\AuthenticationError', // 'The request time has expired',
                    '3008' => '\\ccxt\\OrderNotFound', // 'Transaction records not found',
                    '3009' => '\\ccxt\\InvalidOrder', // 'The price exceeds the limit',
                    '3011' => '\\ccxt\\InvalidOrder', // 'The entrusted price is abnormal, please modify it and place order again',
                    '4001' => '\\ccxt\\ExchangeNotAvailable', // 'API interface is locked or not enabled',
                    '4002' => '\\ccxt\\DDoSProtection', // 'Request too often',
                ),
                'broad' => array(
                    '提币地址有误，请先添加提币地址。' => '\\ccxt\\InvalidAddress', // array("code":1001,"message":"提币地址有误，请先添加提币地址。")
                ),
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/32859187-cd5214f0-ca5e-11e7-967d-96568e2e2bd1.jpg',
                'api' => array(
                    'public' => 'https://api.zb.today/data',
                    'private' => 'https://trade.zb.today/api',
                ),
                'www' => 'https://www.zb.com',
                'doc' => 'https://www.zb.com/i/developer',
                'fees' => 'https://www.zb.com/i/rate',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'markets',
                        'ticker',
                        'allTicker',
                        'depth',
                        'trades',
                        'kline',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        // spot API
                        'order',
                        'orderMoreV2',
                        'cancelOrder',
                        'getOrder',
                        'getOrders',
                        'getOrdersNew',
                        'getOrdersIgnoreTradeType',
                        'getUnfinishedOrdersIgnoreTradeType',
                        'getFinishedAndPartialOrders',
                        'getAccountInfo',
                        'getUserAddress',
                        'getPayinAddress',
                        'getWithdrawAddress',
                        'getWithdrawRecord',
                        'getChargeRecord',
                        'getCnyWithdrawRecord',
                        'getCnyChargeRecord',
                        'withdraw',
                        // sub accounts
                        'addSubUser',
                        'getSubUserList',
                        'doTransferFunds',
                        'createSubUserKey',
                        // leverage API
                        'getLeverAssetsInfo',
                        'getLeverBills',
                        'transferInLever',
                        'transferOutLever',
                        'loan',
                        'cancelLoan',
                        'getLoans',
                        'getLoanRecords',
                        'borrow',
                        'autoBorrow',
                        'repay',
                        'doAllRepay',
                        'getRepayments',
                        'getFinanceRecords',
                        'changeInvestMark',
                        'changeLoop',
                        // cross API
                        'getCrossAssets',
                        'getCrossBills',
                        'transferInCross',
                        'transferOutCross',
                        'doCrossLoan',
                        'doCrossRepay',
                        'getCrossRepayRecords',
                    ),
                ),
            ),
            'fees' => array(
                'funding' => array(
                    'withdraw' => array(
                        'BTC' => 0.0001,
                        'BCH' => 0.0006,
                        'LTC' => 0.005,
                        'ETH' => 0.01,
                        'ETC' => 0.01,
                        'BTS' => 3,
                        'EOS' => 1,
                        'QTUM' => 0.01,
                        'HSR' => 0.001,
                        'XRP' => 0.1,
                        'USDT' => '0.1%',
                        'QCASH' => 5,
                        'DASH' => 0.002,
                        'BCD' => 0,
                        'UBTC' => 0,
                        'SBTC' => 0,
                        'INK' => 20,
                        'TV' => 0.1,
                        'BTH' => 0,
                        'BCX' => 0,
                        'LBTC' => 0,
                        'CHAT' => 20,
                        'bitCNY' => 20,
                        'HLC' => 20,
                        'BTP' => 0,
                        'BCW' => 0,
                    ),
                ),
                'trading' => array(
                    'maker' => 0.2 / 100,
                    'taker' => 0.2 / 100,
                ),
            ),
            'commonCurrencies' => array(
                'ENT' => 'ENTCash',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $markets = $this->publicGetMarkets ($params);
        //
        //     {
        //         "zb_qc":array(
        //             "amountScale":2,
        //             "minAmount":0.01,
        //             "minSize":5,
        //             "priceScale":4,
        //         ),
        //     }
        //
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $market = $markets[$id];
            list($baseId, $quoteId) = explode('_', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'amountScale'),
                'price' => $this->safe_integer($market, 'priceScale'),
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'base' => $base,
                'quote' => $quote,
                'active' => true,
                'precision' => $precision,
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
                        'min' => 0,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetGetAccountInfo ($params);
        // todo => use this somehow
        // $permissions = $response['result']['base'];
        $balances = $this->safe_value($response['result'], 'coins');
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            //     {        enName => "BTC",
            //               freez => "0.00000000",
            //         unitDecimal =>  8, // always 8
            //              cnName => "BTC",
            //       isCanRecharge =>  true, // TODO => should use this
            //             unitTag => "฿",
            //       isCanWithdraw =>  true,  // TODO => should use this
            //           available => "0.00000000",
            //                 key => "btc"         }
            $account = $this->account();
            $currencyId = $this->safe_string($balance, 'key');
            $code = $this->safe_currency_code($currencyId);
            $account['free'] = $this->safe_number($balance, 'available');
            $account['used'] = $this->safe_number($balance, 'freez');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_deposit_address($depositAddress, $currency = null) {
        //
        // fetchDepositAddress
        //
        //     {
        //         "key" => "0x0af7f36b8f09410f3df62c81e5846da673d4d9a9"
        //     }
        //
        // fetchDepositAddresses
        //
        //     {
        //         "blockChain" => "btc",
        //         "isUseMemo" => false,
        //         "$address" => "1LL5ati6pXHZnTGzHSA3rWdqi4mGGXudwM",
        //         "canWithdraw" => true,
        //         "canDeposit" => true
        //     }
        //     {
        //         "blockChain" => "bts",
        //         "isUseMemo" => true,
        //         "account" => "btstest",
        //         "$memo" => "123",
        //         "canWithdraw" => true,
        //         "canDeposit" => true
        //     }
        //
        $address = $this->safe_string($depositAddress, 'key');
        $tag = null;
        $memo = $this->safe_string($depositAddress, 'memo');
        if ($memo !== null) {
            $tag = $memo;
        } else if (mb_strpos($address, '_') !== false) {
            $parts = explode('_', $address);
            $address = $parts[0];  // WARNING => MAY BE tag_address INSTEAD OF address_tag FOR SOME CURRENCIES!!
            $tag = $parts[1];
        }
        $currencyId = $this->safe_string($depositAddress, 'blockChain');
        $code = $this->safe_currency_code($currencyId, $currency);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $depositAddress,
        );
    }

    public function fetch_deposit_addresses($codes = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privateGetGetPayinAddress ($params);
        //
        //     {
        //         "code" => 1000,
        //         "$message" => {
        //             "des" => "success",
        //             "isSuc" => true,
        //             "$datas" => array(
        //                 array(
        //                     "blockChain" => "btc",
        //                     "isUseMemo" => false,
        //                     "address" => "1LL5ati6pXHZnTGzHSA3rWdqi4mGGXudwM",
        //                     "canWithdraw" => true,
        //                     "canDeposit" => true
        //                 ),
        //                 array(
        //                     "blockChain" => "bts",
        //                     "isUseMemo" => true,
        //                     "account" => "btstest",
        //                     "memo" => "123",
        //                     "canWithdraw" => true,
        //                     "canDeposit" => true
        //                 ),
        //             )
        //         }
        //     }
        //
        $message = $this->safe_value($response, 'message', array());
        $datas = $this->safe_value($message, 'datas', array());
        return $this->parse_deposit_addresses($datas, $codes);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetGetUserAddress (array_merge($request, $params));
        //
        //     {
        //         "$code" => 1000,
        //         "$message" => {
        //             "des" => "success",
        //             "isSuc" => true,
        //             "$datas" => {
        //                 "key" => "0x0af7f36b8f09410f3df62c81e5846da673d4d9a9"
        //             }
        //         }
        //     }
        //
        $message = $this->safe_value($response, 'message', array());
        $datas = $this->safe_value($message, 'datas', array());
        return $this->parse_deposit_address($datas, $currency);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->publicGetDepth (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetAllTicker ($params);
        $result = array();
        $anotherMarketsById = array();
        $marketIds = is_array($this->marketsById) ? array_keys($this->marketsById) : array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $tickerId = str_replace('_', '', $marketIds[$i]);
            $anotherMarketsById[$tickerId] = $this->marketsById[$marketIds[$i]];
        }
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $market = $anotherMarketsById[$ids[$i]];
            $result[$market['symbol']] = $this->parse_ticker($response[$ids[$i]], $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        $ticker = $response['ticker'];
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        return array(
            $this->safe_integer($ohlcv, 0),
            $this->safe_number($ohlcv, 1),
            $this->safe_number($ohlcv, 2),
            $this->safe_number($ohlcv, 3),
            $this->safe_number($ohlcv, 4),
            $this->safe_number($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 1000;
        }
        $request = array(
            'market' => $market['id'],
            'type' => $this->timeframes[$timeframe],
            'limit' => $limit,
        );
        if ($since !== null) {
            $request['since'] = $since;
        }
        $response = $this->publicGetKline (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $side = $this->safe_string($trade, 'trade_type');
        $side = ($side === 'bid') ? 'buy' : 'sell';
        $id = $this->safe_string($trade, 'tid');
        $priceString = $this->safe_string($trade, 'price');
        $amountString = $this->safe_string($trade, 'amount');
        $costString = Precise::string_mul($priceString, $amountString);
        $price = $this->parse_number($priceString);
        $amount = $this->parse_number($amountString);
        $cost = $this->parse_number($costString);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'order' => null,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit') {
            throw new InvalidOrder($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $request = array(
            'price' => $this->price_to_precision($symbol, $price),
            'amount' => $this->amount_to_precision($symbol, $amount),
            'tradeType' => ($side === 'buy') ? '1' : '0',
            'currency' => $this->market_id($symbol),
        );
        $response = $this->privateGetOrder (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['id'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => (string) $id,
            'currency' => $this->market_id($symbol),
        );
        return $this->privateGetCancelOrder (array_merge($request, $params));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $request = array(
            'id' => (string) $id,
            'currency' => $this->market_id($symbol),
        );
        $response = $this->privateGetGetOrder (array_merge($request, $params));
        //
        //     {
        //         'total_amount' => 0.01,
        //         'id' => '20180910244276459',
        //         'price' => 180.0,
        //         'trade_date' => 1536576744960,
        //         'status' => 2,
        //         'trade_money' => '1.96742',
        //         'trade_amount' => 0.01,
        //         'type' => 0,
        //         'currency' => 'eth_usdt'
        //     }
        //
        return $this->parse_order($response, null);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = 50, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . 'fetchOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'pageIndex' => 1, // default pageIndex is 1
            'pageSize' => $limit, // default pageSize is 50
        );
        $method = 'privateGetGetOrdersIgnoreTradeType';
        // tradeType 交易类型1/0[buy/sell]
        if (is_array($params) && array_key_exists('tradeType', $params)) {
            $method = 'privateGetGetOrdersNew';
        }
        $response = null;
        try {
            $response = $this->$method (array_merge($request, $params));
        } catch (Exception $e) {
            if ($e instanceof OrderNotFound) {
                return array();
            }
            throw $e;
        }
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . 'fetchClosedOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'pageIndex' => 1, // default pageIndex is 1
            'pageSize' => 10, // default pageSize is 10, doesn't work with other values now
        );
        $response = $this->privateGetGetFinishedAndPartialOrders (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = 10, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . 'fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['id'],
            'pageIndex' => 1, // default pageIndex is 1
            'pageSize' => $limit, // default pageSize is 10
        );
        $method = 'privateGetGetUnfinishedOrdersIgnoreTradeType';
        // tradeType 交易类型1/0[buy/sell]
        if (is_array($params) && array_key_exists('tradeType', $params)) {
            $method = 'privateGetGetOrdersNew';
        }
        $response = null;
        try {
            $response = $this->$method (array_merge($request, $params));
        } catch (Exception $e) {
            if ($e instanceof OrderNotFound) {
                return array();
            }
            throw $e;
        }
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function parse_order($order, $market = null) {
        //
        //     array(
        //         acctType => 0,
        //         currency => 'btc_usdt',
        //         fees => 3.6e-7,
        //         $id => '202102282829772463',
        //         $price => 45177.5,
        //         $status => 2,
        //         total_amount => 0.0002,
        //         trade_amount => 0.0002,
        //         trade_date => 1614515104998,
        //         trade_money => 8.983712,
        //         $type => 1,
        //         useZbFee => false
        //     ),
        //
        $side = $this->safe_integer($order, 'type');
        $side = ($side === 1) ? 'buy' : 'sell';
        $type = 'limit'; // $market $order is not availalbe in ZB
        $timestamp = $this->safe_integer($order, 'trade_date');
        $marketId = $this->safe_string($order, 'currency');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $price = $this->safe_number($order, 'price');
        $filled = $this->safe_number($order, 'trade_amount');
        $amount = $this->safe_number($order, 'total_amount');
        $cost = $this->safe_number($order, 'trade_money');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $id = $this->safe_string($order, 'id');
        $feeCost = $this->safe_number($order, 'fees');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrency = null;
            $zbFees = $this->safe_value($order, 'useZbFee');
            if ($zbFees === true) {
                $feeCurrency = 'ZB';
            } else if ($market !== null) {
                $feeCurrency = ($side === 'sell') ? $market['quote'] : $market['base'];
            }
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
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
            'average' => null,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        ));
    }

    public function parse_order_status($status) {
        $statuses = array(
            '0' => 'open',
            '1' => 'canceled',
            '2' => 'closed',
            '3' => 'open', // partial
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            '0' => 'pending', // submitted, pending confirmation
            '1' => 'failed',
            '2' => 'ok',
            '3' => 'canceled',
            '5' => 'ok', // confirmed
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // withdraw
        //
        //     {
        //         "$code" => 1000,
        //         "message" => "success",
        //         "$id" => "withdrawalId"
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         "$amount" => 0.01,
        //         "fees" => 0.001,
        //         "$id" => 2016042556231,
        //         "manageTime" => 1461579340000,
        //         "$status" => 3,
        //         "submitTime" => 1461579288000,
        //         "toAddress" => "14fxEPirL9fyfw1i9EF439Pq6gQ5xijUmp",
        //     }
        //
        // fetchDeposits
        //
        //     {
        //         "$address" => "1FKN1DZqCm8HaTujDioRL2Aezdh7Qj7xxx",
        //         "$amount" => "1.00000000",
        //         "$confirmTimes" => 1,
        //         "$currency" => "BTC",
        //         "description" => "Successfully Confirm",
        //         "hash" => "7ce842de187c379abafadd64a5fe66c5c61c8a21fb04edff9532234a1dae6xxx",
        //         "$id" => 558,
        //         "itransfer" => 1,
        //         "$status" => 2,
        //         "submit_time" => "2016-12-07 18:51:57",
        //     }
        //
        $id = $this->safe_string($transaction, 'id');
        $txid = $this->safe_string($transaction, 'hash');
        $amount = $this->safe_number($transaction, 'amount');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'submit_time'));
        $timestamp = $this->safe_integer($transaction, 'submitTime', $timestamp);
        $address = $this->safe_string_2($transaction, 'toAddress', 'address');
        $tag = null;
        if ($address !== null) {
            $parts = explode('_', $address);
            $address = $this->safe_string($parts, 0);
            $tag = $this->safe_string($parts, 1);
        }
        $confirmTimes = $this->safe_integer($transaction, 'confirmTimes');
        $updated = $this->safe_integer($transaction, 'manageTime');
        $type = null;
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        if ($address !== null) {
            $type = ($confirmTimes === null) ? 'withdrawal' : 'deposit';
        }
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $fee = null;
        $feeCost = $this->safe_number($transaction, 'fees');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $code,
            );
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'addressFrom' => null,
            'address' => $address,
            'addressTo' => $address,
            'tagFrom' => null,
            'tag' => $tag,
            'tagTo' => $tag,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $password = $this->safe_string($params, 'safePwd', $this->password);
        if ($password === null) {
            throw new ArgumentsRequired($this->id . ' withdraw() requires exchange.password or a safePwd parameter');
        }
        $fees = $this->safe_number($params, 'fees');
        if ($fees === null) {
            throw new ArgumentsRequired($this->id . ' withdraw() requires a $fees parameter');
        }
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        if ($tag !== null) {
            $address .= '_' . $tag;
        }
        $request = array(
            'amount' => $this->currency_to_precision($code, $amount),
            'currency' => $currency['id'],
            'fees' => $this->currency_to_precision($code, $fees),
            // 'itransfer' => 0, // agree for an internal transfer, 0 disagree, 1 agree, the default is to disagree
            'method' => 'withdraw',
            'receiveAddr' => $address,
            'safePwd' => $password,
        );
        $response = $this->privateGetWithdraw (array_merge($request, $params));
        //
        //     {
        //         "$code" => 1000,
        //         "message" => "success",
        //         "id" => "withdrawalId"
        //     }
        //
        $transaction = $this->parse_transaction($response, $currency);
        return array_merge($transaction, array(
            'type' => 'withdrawal',
            'address' => $address,
            'addressTo' => $address,
            'amount' => $amount,
        ));
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'currency' => $currency['id'],
            // 'pageIndex' => 1,
            // 'pageSize' => $limit,
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $response = $this->privateGetGetWithdrawRecord (array_merge($request, $params));
        //
        //     {
        //         "$code" => 1000,
        //         "$message" => {
        //             "des" => "success",
        //             "isSuc" => true,
        //             "$datas" => {
        //                 "list" => array(
        //                     array(
        //                         "amount" => 0.01,
        //                         "fees" => 0.001,
        //                         "id" => 2016042556231,
        //                         "manageTime" => 1461579340000,
        //                         "status" => 3,
        //                         "submitTime" => 1461579288000,
        //                         "toAddress" => "14fxEPirL9fyfw1i9EF439Pq6gQ5xijUmp",
        //                     ),
        //                 ),
        //                 "pageIndex" => 1,
        //                 "pageSize" => 10,
        //                 "totalCount" => 4,
        //                 "totalPage" => 1
        //             }
        //         }
        //     }
        //
        $message = $this->safe_value($response, 'message', array());
        $datas = $this->safe_value($message, 'datas', array());
        $withdrawals = $this->safe_value($datas, 'list', array());
        return $this->parse_transactions($withdrawals, $currency, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'currency' => $currency['id'],
            // 'pageIndex' => 1,
            // 'pageSize' => $limit,
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['pageSize'] = $limit;
        }
        $response = $this->privateGetGetChargeRecord (array_merge($request, $params));
        //
        //     {
        //         "$code" => 1000,
        //         "$message" => {
        //             "des" => "success",
        //             "isSuc" => true,
        //             "$datas" => {
        //                 "list" => array(
        //                     array(
        //                         "address" => "1FKN1DZqCm8HaTujDioRL2Aezdh7Qj7xxx",
        //                         "amount" => "1.00000000",
        //                         "confirmTimes" => 1,
        //                         "$currency" => "BTC",
        //                         "description" => "Successfully Confirm",
        //                         "hash" => "7ce842de187c379abafadd64a5fe66c5c61c8a21fb04edff9532234a1dae6xxx",
        //                         "id" => 558,
        //                         "itransfer" => 1,
        //                         "status" => 2,
        //                         "submit_time" => "2016-12-07 18:51:57",
        //                     ),
        //                 ),
        //                 "pageIndex" => 1,
        //                 "pageSize" => 10,
        //                 "total" => 8
        //             }
        //         }
        //     }
        //
        $message = $this->safe_value($response, 'message', array());
        $datas = $this->safe_value($message, 'datas', array());
        $deposits = $this->safe_value($datas, 'list', array());
        return $this->parse_transactions($deposits, $currency, $since, $limit);
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        if ($api === 'public') {
            $url .= '/' . $this->version . '/' . $path;
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else {
            $query = $this->keysort(array_merge(array(
                'method' => $path,
                'accesskey' => $this->apiKey,
            ), $params));
            $nonce = $this->nonce();
            $query = $this->keysort($query);
            $auth = $this->rawencode($query);
            $secret = $this->hash($this->encode($this->secret), 'sha1');
            $signature = $this->hmac($this->encode($auth), $this->encode($secret), 'md5');
            $suffix = 'sign=' . $signature . '&reqTime=' . (string) $nonce;
            $url .= '/' . $path . '?' . $auth . '&' . $suffix;
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if ($body[0] === '{') {
            $feedback = $this->id . ' ' . $body;
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $body, $feedback);
            if (is_array($response) && array_key_exists('code', $response)) {
                $code = $this->safe_string($response, 'code');
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
                if ($code !== '1000') {
                    throw new ExchangeError($feedback);
                }
            }
            // special case for array("$result":false,"$message":"服务端忙碌") (a "Busy Server" reply)
            $result = $this->safe_value($response, 'result');
            if ($result !== null) {
                if (!$result) {
                    $message = $this->safe_string($response, 'message');
                    if ($message === '服务端忙碌') {
                        throw new ExchangeNotAvailable($feedback);
                    } else {
                        throw new ExchangeError($feedback);
                    }
                }
            }
        }
    }
}

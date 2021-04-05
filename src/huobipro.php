<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\BadSymbol;
use \ccxt\InvalidOrder;
use \ccxt\NetworkError;

class huobipro extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'huobipro',
            'name' => 'Huobi Pro',
            'countries' => array( 'CN' ),
            'rateLimit' => 2000,
            'userAgent' => $this->userAgents['chrome39'],
            'version' => 'v1',
            'accounts' => null,
            'accountsById' => null,
            'hostname' => 'api.huobi.pro', // api.testnet.huobi.pro
            'pro' => true,
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'cancelOrders' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTradingLimits' => true,
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
                '1w' => '1week',
                '1M' => '1mon',
                '1y' => '1year',
            ),
            'urls' => array(
                'test' => array(
                    'market' => 'https://api.testnet.huobi.pro',
                    'public' => 'https://api.testnet.huobi.pro',
                    'private' => 'https://api.testnet.huobi.pro',
                ),
                'logo' => 'https://user-images.githubusercontent.com/1294454/76137448-22748a80-604e-11ea-8069-6e389271911d.jpg',
                'api' => array(
                    'market' => 'https://{hostname}',
                    'public' => 'https://{hostname}',
                    'private' => 'https://{hostname}',
                    'v2Public' => 'https://{hostname}',
                    'v2Private' => 'https://{hostname}',
                ),
                'www' => 'https://www.huobi.com',
                'referral' => 'https://www.huobi.com/en-us/topic/invited/?invite_code=rwrd3',
                'doc' => 'https://huobiapi.github.io/docs/spot/v1/cn/',
                'fees' => 'https://www.huobi.com/about/fee/',
            ),
            'api' => array(
                'v2Public' => array(
                    'get' => array(
                        'reference/currencies',
                    ),
                ),
                'v2Private' => array(
                    'get' => array(
                        'account/ledger',
                        'account/withdraw/quota',
                        'account/withdraw/address', // 提币地址查询(限母用户可用)
                        'account/deposit/address',
                        'reference/transact-fee-rate',
                        'account/asset-valuation', // 获取账户资产估值
                        'point/account', // 点卡余额查询
                        'sub-user/user-list', // 获取子用户列表
                        'sub-user/user-state', // 获取特定子用户的用户状态
                        'sub-user/account-list', // 获取特定子用户的账户列表
                        'sub-user/deposit-address', // 子用户充币地址查询
                        'sub-user/query-deposit', // 子用户充币记录查询
                        'user/api-key', // 母子用户API key信息查询
                    ),
                    'post' => array(
                        'account/transfer',
                        'point/transfer', // 点卡划转
                        'sub-user/management', // 冻结/解冻子用户
                        'sub-user/creation', // 子用户创建
                        'sub-user/tradable-market', // 设置子用户交易权限
                        'sub-user/transferability', // 设置子用户资产转出权限
                        'sub-user/api-key-generation', // 子用户API key创建
                        'sub-user/api-key-modification', // 修改子用户API key
                        'sub-user/api-key-deletion', // 删除子用户API key
                    ),
                ),
                'market' => array(
                    'get' => array(
                        'history/kline', // 获取K线数据
                        'detail/merged', // 获取聚合行情(Ticker)
                        'depth', // 获取 Market Depth 数据
                        'trade', // 获取 Trade Detail 数据
                        'history/trade', // 批量获取最近的交易记录
                        'detail', // 获取 Market Detail 24小时成交量数据
                        'tickers',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'common/symbols', // 查询系统支持的所有交易对
                        'common/currencys', // 查询系统支持的所有币种
                        'common/timestamp', // 查询系统当前时间
                        'common/exchange', // order limits
                        'settings/currencys', // ?language=en-US
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'account/accounts', // 查询当前用户的所有账户(即account-id)
                        'account/accounts/{id}/balance', // 查询指定账户的余额
                        'account/accounts/{sub-uid}',
                        'account/history',
                        'cross-margin/loan-info',
                        'margin/loan-info', // 查询借币币息率及额度
                        'fee/fee-rate/get',
                        'order/openOrders',
                        'order/orders',
                        'order/orders/{id}', // 查询某个订单详情
                        'order/orders/{id}/matchresults', // 查询某个订单的成交明细
                        'order/orders/getClientOrder',
                        'order/history', // 查询当前委托、历史委托
                        'order/matchresults', // 查询当前成交、历史成交
                        'dw/withdraw-virtual/addresses', // 查询虚拟币提现地址（Deprecated）
                        'query/deposit-withdraw',
                        'margin/loan-info',
                        'margin/loan-orders', // 借贷订单
                        'margin/accounts/balance', // 借贷账户详情
                        'cross-margin/loan-orders', // 查询借币订单
                        'cross-margin/accounts/balance', // 借币账户详情
                        'points/actions',
                        'points/orders',
                        'subuser/aggregate-balance',
                        'stable-coin/exchange_rate',
                        'stable-coin/quote',
                    ),
                    'post' => array(
                        'account/transfer', // 资产划转(该节点为母用户和子用户进行资产划转的通用接口。)
                        'futures/transfer',
                        'order/batch-orders',
                        'order/orders/place', // 创建并执行一个新订单 (一步下单， 推荐使用)
                        'order/orders/submitCancelClientOrder',
                        'order/orders/batchCancelOpenOrders',
                        'order/orders', // 创建一个新的订单请求 （仅创建订单，不执行下单）
                        'order/orders/{id}/place', // 执行一个订单 （仅执行已创建的订单）
                        'order/orders/{id}/submitcancel', // 申请撤销一个订单请求
                        'order/orders/batchcancel', // 批量撤销订单
                        'dw/balance/transfer', // 资产划转
                        'dw/withdraw/api/create', // 申请提现虚拟币
                        'dw/withdraw-virtual/create', // 申请提现虚拟币
                        'dw/withdraw-virtual/{id}/place', // 确认申请虚拟币提现（Deprecated）
                        'dw/withdraw-virtual/{id}/cancel', // 申请取消提现虚拟币
                        'dw/transfer-in/margin', // 现货账户划入至借贷账户
                        'dw/transfer-out/margin', // 借贷账户划出至现货账户
                        'margin/orders', // 申请借贷
                        'margin/orders/{id}/repay', // 归还借贷
                        'cross-margin/transfer-in', // 资产划转
                        'cross-margin/transfer-out', // 资产划转
                        'cross-margin/orders', // 申请借币
                        'cross-margin/orders/{id}/repay', // 归还借币
                        'stable-coin/exchange',
                        'subuser/transfer',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'feeSide' => 'get',
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.002,
                    'taker' => 0.002,
                ),
            ),
            'exceptions' => array(
                'broad' => array(
                    'contract is restricted of closing positions on API.  Please contact customer service' => '\\ccxt\\OnMaintenance',
                    'maintain' => '\\ccxt\\OnMaintenance',
                ),
                'exact' => array(
                    // err-code
                    'bad-request' => '\\ccxt\\BadRequest',
                    'base-date-limit-error' => '\\ccxt\\BadRequest', // array("status":"error","err-code":"base-date-limit-error","err-msg":"date less than system limit","data":null)
                    'api-not-support-temp-addr' => '\\ccxt\\PermissionDenied', // array("status":"error","err-code":"api-not-support-temp-addr","err-msg":"API withdrawal does not support temporary addresses","data":null)
                    'timeout' => '\\ccxt\\RequestTimeout', // array("ts":1571653730865,"status":"error","err-code":"timeout","err-msg":"Request Timeout")
                    'gateway-internal-error' => '\\ccxt\\ExchangeNotAvailable', // array("status":"error","err-code":"gateway-internal-error","err-msg":"Failed to load data. Try again later.","data":null)
                    'account-frozen-balance-insufficient-error' => '\\ccxt\\InsufficientFunds', // array("status":"error","err-code":"account-frozen-balance-insufficient-error","err-msg":"trade account balance is not enough, left => `0.0027`","data":null)
                    'invalid-amount' => '\\ccxt\\InvalidOrder', // eg "Paramemter `amount` is invalid."
                    'order-limitorder-amount-min-error' => '\\ccxt\\InvalidOrder', // limit order amount error, min => `0.001`
                    'order-limitorder-amount-max-error' => '\\ccxt\\InvalidOrder', // market order amount error, max => `1000000`
                    'order-marketorder-amount-min-error' => '\\ccxt\\InvalidOrder', // market order amount error, min => `0.01`
                    'order-limitorder-price-min-error' => '\\ccxt\\InvalidOrder', // limit order price error
                    'order-limitorder-price-max-error' => '\\ccxt\\InvalidOrder', // limit order price error
                    'order-holding-limit-failed' => '\\ccxt\\InvalidOrder', // array("status":"error","err-code":"order-holding-limit-failed","err-msg":"Order failed, exceeded the holding limit of this currency","data":null)
                    'order-orderprice-precision-error' => '\\ccxt\\InvalidOrder', // array("status":"error","err-code":"order-orderprice-precision-error","err-msg":"order price precision error, scale => `4`","data":null)
                    'order-orderstate-error' => '\\ccxt\\OrderNotFound', // canceling an already canceled order
                    'order-queryorder-invalid' => '\\ccxt\\OrderNotFound', // querying a non-existent order
                    'order-update-error' => '\\ccxt\\ExchangeNotAvailable', // undocumented error
                    'api-signature-check-failed' => '\\ccxt\\AuthenticationError',
                    'api-signature-not-valid' => '\\ccxt\\AuthenticationError', // array("status":"error","err-code":"api-signature-not-valid","err-msg":"Signature not valid => Incorrect Access key [Access key错误]","data":null)
                    'base-record-invalid' => '\\ccxt\\OrderNotFound', // https://github.com/ccxt/ccxt/issues/5750
                    'base-symbol-trade-disabled' => '\\ccxt\\BadSymbol', // array("status":"error","err-code":"base-symbol-trade-disabled","err-msg":"Trading is disabled for this symbol","data":null)
                    'base-symbol-error' => '\\ccxt\\BadSymbol', // array("status":"error","err-code":"base-symbol-error","err-msg":"The symbol is invalid","data":null)
                    'system-maintenance' => '\\ccxt\\OnMaintenance', // array("status" => "error", "err-code" => "system-maintenance", "err-msg" => "System is in maintenance!", "data" => null)
                    // err-msg
                    'invalid symbol' => '\\ccxt\\BadSymbol', // array("ts":1568813334794,"status":"error","err-code":"invalid-parameter","err-msg":"invalid symbol")
                    'symbol trade not open now' => '\\ccxt\\BadSymbol', // array("ts":1576210479343,"status":"error","err-code":"invalid-parameter","err-msg":"symbol trade not open now")
                ),
            ),
            'options' => array(
                // https://github.com/ccxt/ccxt/issues/5376
                'fetchOrdersByStatesMethod' => 'private_get_order_orders', // 'private_get_order_history' // https://github.com/ccxt/ccxt/pull/5392
                'fetchOpenOrdersMethod' => 'fetch_open_orders_v1', // 'fetch_open_orders_v2' // https://github.com/ccxt/ccxt/issues/5388
                'createMarketBuyOrderRequiresPrice' => true,
                'fetchMarketsMethod' => 'publicGetCommonSymbols',
                'fetchBalanceMethod' => 'privateGetAccountAccountsIdBalance',
                'createOrderMethod' => 'privatePostOrderOrdersPlace',
                'language' => 'en-US',
            ),
            'commonCurrencies' => array(
                // https://github.com/ccxt/ccxt/issues/6081
                // https://github.com/ccxt/ccxt/issues/3365
                // https://github.com/ccxt/ccxt/issues/2873
                'GET' => 'Themis', // conflict with GET (Guaranteed Entrance Token, GET Protocol)
                'HOT' => 'Hydro Protocol', // conflict with HOT (Holo) https://github.com/ccxt/ccxt/issues/4929
                // https://github.com/ccxt/ccxt/issues/7399
                // https://coinmarketcap.com/currencies/pnetwork/
                // https://coinmarketcap.com/currencies/penta/markets/
                // https://en.cryptonomist.ch/blog/eidoo/the-edo-to-pnt-upgrade-what-you-need-to-know-updated/
                'PNT' => 'Penta',
                'SBTC' => 'Super Bitcoin',
                'BIFI' => 'Bitcoin File', // conflict with Beefy.Finance https://github.com/ccxt/ccxt/issues/8706
            ),
        ));
    }

    public function fetch_trading_limits($symbols = null, $params = array ()) {
        // this method should not be called directly, use loadTradingLimits () instead
        //  by default it will try load withdrawal fees of all currencies (with separate requests)
        //  however if you define $symbols = array( 'ETH/BTC', 'LTC/BTC' ) in args it will only load those
        $this->load_markets();
        if ($symbols === null) {
            $symbols = $this->symbols;
        }
        $result = array();
        for ($i = 0; $i < count($symbols); $i++) {
            $symbol = $symbols[$i];
            $result[$symbol] = $this->fetch_trading_limits_by_id($this->market_id($symbol), $params);
        }
        return $result;
    }

    public function fetch_trading_limits_by_id($id, $params = array ()) {
        $request = array(
            'symbol' => $id,
        );
        $response = $this->publicGetCommonExchange (array_merge($request, $params));
        //
        //     { status =>   "ok",
        //         data => {                                  symbol => "aidocbtc",
        //                              'buy-limit-must-less-than' =>  1.1,
        //                          'sell-limit-must-greater-than' =>  0.9,
        //                         'limit-order-must-greater-than' =>  1,
        //                            'limit-order-must-less-than' =>  5000000,
        //                    'market-buy-order-must-greater-than' =>  0.0001,
        //                       'market-buy-order-must-less-than' =>  100,
        //                   'market-sell-order-must-greater-than' =>  1,
        //                      'market-sell-order-must-less-than' =>  500000,
        //                       'circuit-break-when-greater-than' =>  10000,
        //                          'circuit-break-when-less-than' =>  10,
        //                 'market-sell-order-rate-must-less-than' =>  0.1,
        //                  'market-buy-order-rate-must-less-than' =>  0.1        } }
        //
        return $this->parse_trading_limits($this->safe_value($response, 'data', array()));
    }

    public function parse_trading_limits($limits, $symbol = null, $params = array ()) {
        //
        //   {                                  $symbol => "aidocbtc",
        //                  'buy-limit-must-less-than' =>  1.1,
        //              'sell-limit-must-greater-than' =>  0.9,
        //             'limit-order-must-greater-than' =>  1,
        //                'limit-order-must-less-than' =>  5000000,
        //        'market-buy-order-must-greater-than' =>  0.0001,
        //           'market-buy-order-must-less-than' =>  100,
        //       'market-sell-order-must-greater-than' =>  1,
        //          'market-sell-order-must-less-than' =>  500000,
        //           'circuit-break-when-greater-than' =>  10000,
        //              'circuit-break-when-less-than' =>  10,
        //     'market-sell-order-rate-must-less-than' =>  0.1,
        //      'market-buy-order-rate-must-less-than' =>  0.1        }
        //
        return array(
            'info' => $limits,
            'limits' => array(
                'amount' => array(
                    'min' => $this->safe_number($limits, 'limit-order-must-greater-than'),
                    'max' => $this->safe_number($limits, 'limit-order-must-less-than'),
                ),
            ),
        );
    }

    public function cost_to_precision($symbol, $cost) {
        return $this->decimal_to_precision($cost, TRUNCATE, $this->markets[$symbol]['precision']['cost'], $this->precisionMode);
    }

    public function fetch_markets($params = array ()) {
        $method = $this->options['fetchMarketsMethod'];
        $response = $this->$method ($params);
        $markets = $this->safe_value($response, 'data');
        $numMarkets = is_array($markets) ? count($markets) : 0;
        if ($numMarkets < 1) {
            throw new NetworkError($this->id . ' publicGetCommonSymbols returned empty $response => ' . $this->json($markets));
        }
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $baseId = $this->safe_string($market, 'base-currency');
            $quoteId = $this->safe_string($market, 'quote-currency');
            $id = $baseId . $quoteId;
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'amount-precision'),
                'price' => $this->safe_integer($market, 'price-precision'),
                'cost' => $this->safe_integer($market, 'value-precision'),
            );
            $maker = ($base === 'OMG') ? 0 : 0.2 / 100;
            $taker = ($base === 'OMG') ? 0 : 0.2 / 100;
            $minAmount = $this->safe_number($market, 'min-order-amt', pow(10, -$precision['amount']));
            $maxAmount = $this->safe_number($market, 'max-order-amt');
            $minCost = $this->safe_number($market, 'min-order-value', 0);
            $state = $this->safe_string($market, 'state');
            $active = ($state === 'online');
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'taker' => $taker,
                'maker' => $maker,
                'limits' => array(
                    'amount' => array(
                        'min' => $minAmount,
                        'max' => $maxAmount,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $minCost,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // fetchTicker
        //
        //     {
        //         "amount" => 26228.672978342216,
        //         "$open" => 9078.95,
        //         "$close" => 9146.86,
        //         "high" => 9155.41,
        //         "id" => 209988544334,
        //         "count" => 265846,
        //         "low" => 8988.0,
        //         "version" => 209988544334,
        //         "$ask" => array( 9146.87, 0.156134 ),
        //         "vol" => 2.3822168242201668E8,
        //         "$bid" => array( 9146.86, 0.080758 ),
        //     }
        //
        // fetchTickers
        //     {
        //         $symbol => "bhdht",
        //         $open =>  2.3938,
        //         high =>  2.4151,
        //         low =>  2.3323,
        //         $close =>  2.3909,
        //         amount =>  628.992,
        //         vol =>  1493.71841095,
        //         count =>  2088,
        //         $bid =>  2.3643,
        //         bidSize =>  0.7136,
        //         $ask =>  2.4061,
        //         askSize =>  0.4156
        //     }
        //
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($ticker, 'ts');
        $bid = null;
        $bidVolume = null;
        $ask = null;
        $askVolume = null;
        if (is_array($ticker) && array_key_exists('bid', $ticker)) {
            if (gettype($ticker['bid']) === 'array' && count(array_filter(array_keys($ticker['bid']), 'is_string')) == 0) {
                $bid = $this->safe_number($ticker['bid'], 0);
                $bidVolume = $this->safe_number($ticker['bid'], 1);
            } else {
                $bid = $this->safe_number($ticker, 'bid');
                $bidVolume = $this->safe_value($ticker, 'bidSize');
            }
        }
        if (is_array($ticker) && array_key_exists('ask', $ticker)) {
            if (gettype($ticker['ask']) === 'array' && count(array_filter(array_keys($ticker['ask']), 'is_string')) == 0) {
                $ask = $this->safe_number($ticker['ask'], 0);
                $askVolume = $this->safe_number($ticker['ask'], 1);
            } else {
                $ask = $this->safe_number($ticker, 'ask');
                $askVolume = $this->safe_value($ticker, 'askSize');
            }
        }
        $open = $this->safe_number($ticker, 'open');
        $close = $this->safe_number($ticker, 'close');
        $change = null;
        $percentage = null;
        $average = null;
        if (($open !== null) && ($close !== null)) {
            $change = $close - $open;
            $average = $this->sum($open, $close) / 2;
            if (($close !== null) && ($close > 0)) {
                $percentage = ($change / $open) * 100;
            }
        }
        $baseVolume = $this->safe_number($ticker, 'amount');
        $quoteVolume = $this->safe_number($ticker, 'vol');
        $vwap = $this->vwap($baseVolume, $quoteVolume);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $bid,
            'bidVolume' => $bidVolume,
            'ask' => $ask,
            'askVolume' => $askVolume,
            'vwap' => $vwap,
            'open' => $open,
            'close' => $close,
            'last' => $close,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'type' => 'step0',
        );
        $response = $this->marketGetDepth (array_merge($request, $params));
        //
        //     {
        //         "status" => "ok",
        //         "ch" => "$market->btcusdt.depth.step0",
        //         "ts" => 1583474832790,
        //         "$tick" => {
        //             "bids" => array(
        //                 array( 9100.290000000000000000, 0.200000000000000000 ),
        //                 array( 9099.820000000000000000, 0.200000000000000000 ),
        //                 array( 9099.610000000000000000, 0.205000000000000000 ),
        //             ),
        //             "asks" => array(
        //                 array( 9100.640000000000000000, 0.005904000000000000 ),
        //                 array( 9101.010000000000000000, 0.287311000000000000 ),
        //                 array( 9101.030000000000000000, 0.012121000000000000 ),
        //             ),
        //             "ts":1583474832008,
        //             "version":104999698780
        //         }
        //     }
        //
        if (is_array($response) && array_key_exists('tick', $response)) {
            if (!$response['tick']) {
                throw new BadSymbol($this->id . ' fetchOrderBook() returned empty $response => ' . $this->json($response));
            }
            $tick = $this->safe_value($response, 'tick');
            $timestamp = $this->safe_integer($tick, 'ts', $this->safe_integer($response, 'ts'));
            $result = $this->parse_order_book($tick, $timestamp);
            $result['nonce'] = $this->safe_integer($tick, 'version');
            return $result;
        }
        throw new ExchangeError($this->id . ' fetchOrderBook() returned unrecognized $response => ' . $this->json($response));
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->marketGetDetailMerged (array_merge($request, $params));
        //
        //     {
        //         "status" => "ok",
        //         "ch" => "$market->btcusdt.detail.merged",
        //         "ts" => 1583494336669,
        //         "tick" => {
        //             "amount" => 26228.672978342216,
        //             "open" => 9078.95,
        //             "close" => 9146.86,
        //             "high" => 9155.41,
        //             "id" => 209988544334,
        //             "count" => 265846,
        //             "low" => 8988.0,
        //             "version" => 209988544334,
        //             "ask" => array( 9146.87, 0.156134 ),
        //             "vol" => 2.3822168242201668E8,
        //             "bid" => array( 9146.86, 0.080758 ),
        //         }
        //     }
        //
        $ticker = $this->parse_ticker($response['tick'], $market);
        $timestamp = $this->safe_integer($response, 'ts');
        $ticker['timestamp'] = $timestamp;
        $ticker['datetime'] = $this->iso8601($timestamp);
        return $ticker;
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->marketGetTickers ($params);
        $tickers = $this->safe_value($response, 'data');
        $timestamp = $this->safe_integer($response, 'ts');
        $result = array();
        for ($i = 0; $i < count($tickers); $i++) {
            $marketId = $this->safe_string($tickers[$i], 'symbol');
            $market = $this->safe_market($marketId);
            $symbol = $market['symbol'];
            $ticker = $this->parse_ticker($tickers[$i], $market);
            $ticker['timestamp'] = $timestamp;
            $ticker['datetime'] = $this->iso8601($timestamp);
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "$amount" => 0.010411000000000000,
        //         "$trade-$id" => 102090736910,
        //         "ts" => 1583497692182,
        //         "$id" => 10500517034273194594947,
        //         "$price" => 9096.050000000000000000,
        //         "direction" => "sell"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     array(
        //          'symbol' => 'swftcbtc',
        //          'fee-currency' => 'swftc',
        //          'filled-fees' => '0',
        //          'source' => 'spot-api',
        //          'id' => 83789509854000,
        //          'type' => 'buy-limit',
        //          'order-id' => 83711103204909,
        //          'filled-points' => '0.005826843283532154',
        //          'fee-deduct-currency' => 'ht',
        //          'filled-amount' => '45941.53',
        //          'price' => '0.0000001401',
        //          'created-at' => 1597933260729,
        //          'match-id' => 100087455560,
        //          'role' => 'maker',
        //          'trade-id' => 100050305348
        //     ),
        //
        $marketId = $this->safe_string($trade, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->safe_integer_2($trade, 'ts', 'created-at');
        $order = $this->safe_string($trade, 'order-id');
        $side = $this->safe_string($trade, 'direction');
        $type = $this->safe_string($trade, 'type');
        if ($type !== null) {
            $typeParts = explode('-', $type);
            $side = $typeParts[0];
            $type = $typeParts[1];
        }
        $takerOrMaker = $this->safe_string($trade, 'role');
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number_2($trade, 'filled-amount', 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        $fee = null;
        $feeCost = $this->safe_number($trade, 'filled-fees');
        $feeCurrency = null;
        if ($market !== null) {
            $feeCurrency = $this->safe_currency_code($this->safe_string($trade, 'fee-currency'));
        }
        $filledPoints = $this->safe_number($trade, 'filled-points');
        if ($filledPoints !== null) {
            if (($feeCost === null) || ($feeCost === 0.0)) {
                $feeCost = $filledPoints;
                $feeCurrency = $this->safe_currency_code($this->safe_string($trade, 'fee-deduct-currency'));
            }
        }
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        $tradeId = $this->safe_string_2($trade, 'trade-id', 'tradeId');
        $id = $this->safe_string($trade, 'id', $tradeId);
        return array(
            'id' => $id,
            'info' => $trade,
            'order' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($limit !== null) {
            $request['size'] = $limit; // 1-100 orders, default is 100
        }
        if ($since !== null) {
            $request['start-date'] = $this->ymd($since); // a date within 61 days from today
            $request['end-date'] = $this->ymd($this->sum($since, 86400000));
        }
        $response = $this->privateGetOrderMatchresults (array_merge($request, $params));
        $trades = $this->parse_trades($response['data'], $market, $since, $limit);
        return $trades;
    }

    public function fetch_trades($symbol, $since = null, $limit = 1000, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->marketGetHistoryTrade (array_merge($request, $params));
        //
        //     {
        //         "status" => "ok",
        //         "ch" => "$market->btcusdt.trade.detail",
        //         "ts" => 1583497692365,
        //         "$data" => array(
        //             {
        //                 "id" => 105005170342,
        //                 "ts" => 1583497692182,
        //                 "$data" => array(
        //                     array(
        //                         "amount" => 0.010411000000000000,
        //                         "$trade-id" => 102090736910,
        //                         "ts" => 1583497692182,
        //                         "id" => 10500517034273194594947,
        //                         "price" => 9096.050000000000000000,
        //                         "direction" => "sell"
        //                     }
        //                 )
        //             ),
        //             // ...
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $trades = $this->safe_value($data[$i], 'data', array());
            for ($j = 0; $j < count($trades); $j++) {
                $trade = $this->parse_trade($trades[$j], $market);
                $result[] = $trade;
            }
        }
        $result = $this->sort_by($result, 'timestamp');
        return $this->filter_by_symbol_since_limit($result, $symbol, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "amount":1.2082,
        //         "open":0.025096,
        //         "close":0.025095,
        //         "high":0.025096,
        //         "id":1591515300,
        //         "count":6,
        //         "low":0.025095,
        //         "vol":0.0303205097
        //     }
        //
        return array(
            $this->safe_timestamp($ohlcv, 'id'),
            $this->safe_number($ohlcv, 'open'),
            $this->safe_number($ohlcv, 'high'),
            $this->safe_number($ohlcv, 'low'),
            $this->safe_number($ohlcv, 'close'),
            $this->safe_number($ohlcv, 'amount'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = 1000, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'period' => $this->timeframes[$timeframe],
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $response = $this->marketGetHistoryKline (array_merge($request, $params));
        //
        //     {
        //         "status":"ok",
        //         "ch":"$market->ethbtc.kline.1min",
        //         "ts":1591515374371,
        //         "$data":array(
        //             array("amount":0.0,"open":0.025095,"close":0.025095,"high":0.025095,"id":1591515360,"count":0,"low":0.025095,"vol":0.0),
        //             array("amount":1.2082,"open":0.025096,"close":0.025095,"high":0.025096,"id":1591515300,"count":6,"low":0.025095,"vol":0.0303205097),
        //             array("amount":0.0648,"open":0.025096,"close":0.025096,"high":0.025096,"id":1591515240,"count":2,"low":0.025096,"vol":0.0016262208),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function fetch_accounts($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccountAccounts ($params);
        return $response['data'];
    }

    public function fetch_currencies($params = array ()) {
        $request = array(
            'language' => $this->options['language'],
        );
        $response = $this->publicGetSettingsCurrencys (array_merge($request, $params));
        $currencies = $this->safe_value($response, 'data');
        $result = array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            //
            //  {                     $name => "ctxc",
            //              'display-name' => "CTXC",
            //        'withdraw-precision' =>  8,
            //             'currency-type' => "eth",
            //        'currency-partition' => "pro",
            //             'support-sites' =>  null,
            //                'otc-enable' =>  0,
            //        'deposit-min-amount' => "2",
            //       'withdraw-min-amount' => "4",
            //            'show-precision' => "8",
            //                      weight => "2988",
            //                     visible =>  true,
            //              'deposit-desc' => "Please don’t deposit any other digital assets except CTXC t…",
            //             'withdraw-desc' => "Minimum withdrawal amount => 4 CTXC. !>_<!For security reason…",
            //           'deposit-enabled' =>  true,
            //          'withdraw-enabled' =>  true,
            //    'currency-addr-with-tag' =>  false,
            //             'fast-confirms' =>  15,
            //             'safe-confirms' =>  30                                                             }
            //
            $id = $this->safe_value($currency, 'name');
            $precision = $this->safe_integer($currency, 'withdraw-precision');
            $code = $this->safe_currency_code($id);
            $active = $currency['visible'] && $currency['deposit-enabled'] && $currency['withdraw-enabled'];
            $name = $this->safe_string($currency, 'display-name');
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'type' => 'crypto',
                // 'payin' => $currency['deposit-enabled'],
                // 'payout' => $currency['withdraw-enabled'],
                // 'transfer' => null,
                'name' => $name,
                'active' => $active,
                'fee' => null, // todo need to fetch from fee endpoint
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
                        'min' => $this->safe_number($currency, 'deposit-min-amount'),
                        'max' => pow(10, $precision),
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_number($currency, 'withdraw-min-amount'),
                        'max' => pow(10, $precision),
                    ),
                ),
                'info' => $currency,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $method = $this->options['fetchBalanceMethod'];
        $request = array(
            'id' => $this->accounts[0]['id'],
        );
        $response = $this->$method (array_merge($request, $params));
        $balances = $this->safe_value($response['data'], 'list', array());
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = null;
            if (is_array($result) && array_key_exists($code, $result)) {
                $account = $result[$code];
            } else {
                $account = $this->account();
            }
            if ($balance['type'] === 'trade') {
                $account['free'] = $this->safe_number($balance, 'balance');
            }
            if ($balance['type'] === 'frozen') {
                $account['used'] = $this->safe_number($balance, 'balance');
            }
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_orders_by_states($states, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'states' => $states,
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $method = $this->safe_string($this->options, 'fetchOrdersByStatesMethod', 'private_get_order_orders');
        $response = $this->$method (array_merge($request, $params));
        //
        //     { status =>   "ok",
        //         data => array( {                  id =>  13997833014,
        //                                $symbol => "ethbtc",
        //                          'account-id' =>  3398321,
        //                                amount => "0.045000000000000000",
        //                                 price => "0.034014000000000000",
        //                          'created-at' =>  1545836976871,
        //                                  type => "sell-$limit",
        //                        'field-amount' => "0.045000000000000000",
        //                   'field-cash-amount' => "0.001530630000000000",
        //                          'field-fees' => "0.000003061260000000",
        //                         'finished-at' =>  1545837948214,
        //                                source => "spot-api",
        //                                 state => "filled",
        //                         'canceled-at' =>  0                      }  ) }
        //
        return $this->parse_orders($response['data'], $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetOrderOrdersId (array_merge($request, $params));
        $order = $this->safe_value($response, 'data');
        return $this->parse_order($order);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_states('pre-submitted,submitted,partial-filled,filled,partial-canceled,canceled', $symbol, $since, $limit, $params);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $method = $this->safe_string($this->options, 'fetchOpenOrdersMethod', 'fetch_open_orders_v1');
        return $this->$method ($symbol, $since, $limit, $params);
    }

    public function fetch_open_orders_v1($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrdersV1() requires a $symbol argument');
        }
        return $this->fetch_orders_by_states('pre-submitted,submitted,partial-filled', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_states('filled,partial-canceled,canceled', $symbol, $since, $limit, $params);
    }

    public function fetch_open_orders_v2($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $market = $this->market($symbol);
        $accountId = $this->safe_string($params, 'account-id');
        if ($accountId === null) {
            // pick the first $account
            $this->load_accounts();
            for ($i = 0; $i < count($this->accounts); $i++) {
                $account = $this->accounts[$i];
                if ($account['type'] === 'spot') {
                    $accountId = $this->safe_string($account, 'id');
                    if ($accountId !== null) {
                        break;
                    }
                }
            }
        }
        $request = array(
            'symbol' => $market['id'],
            'account-id' => $accountId,
        );
        if ($limit !== null) {
            $request['size'] = $limit;
        }
        $omitted = $this->omit($params, 'account-id');
        $response = $this->privateGetOrderOpenOrders (array_merge($request, $omitted));
        //
        //     {
        //         "status":"ok",
        //         "$data":array(
        //             {
        //                 "$symbol":"ethusdt",
        //                 "source":"api",
        //                 "amount":"0.010000000000000000",
        //                 "$account-id":1528640,
        //                 "created-at":1561597491963,
        //                 "price":"400.000000000000000000",
        //                 "filled-amount":"0.0",
        //                 "filled-cash-amount":"0.0",
        //                 "filled-fees":"0.0",
        //                 "id":38477101630,
        //                 "state":"submitted",
        //                 "type":"sell-$limit"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'partial-filled' => 'open',
            'partial-canceled' => 'canceled',
            'filled' => 'closed',
            'canceled' => 'canceled',
            'submitted' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {                  $id =>  13997833014,
        //                    $symbol => "ethbtc",
        //              'account-id' =>  3398321,
        //                    $amount => "0.045000000000000000",
        //                     $price => "0.034014000000000000",
        //              'created-at' =>  1545836976871,
        //                      $type => "sell-limit",
        //            'field-amount' => "0.045000000000000000", // they have fixed it for $filled-$amount
        //       'field-cash-amount' => "0.001530630000000000", // they have fixed it for $filled-cash-$amount
        //              'field-fees' => "0.000003061260000000", // they have fixed it for $filled-fees
        //             'finished-at' =>  1545837948214,
        //                    source => "spot-api",
        //                     state => "$filled",
        //             'canceled-at' =>  0                      }
        //
        //     {                  $id =>  20395337822,
        //                    $symbol => "ethbtc",
        //              'account-id' =>  5685075,
        //                    $amount => "0.001000000000000000",
        //                     $price => "0.0",
        //              'created-at' =>  1545831584023,
        //                      $type => "buy-$market",
        //            'field-amount' => "0.029100000000000000", // they have fixed it for $filled-$amount
        //       'field-cash-amount' => "0.000999788700000000", // they have fixed it for $filled-cash-$amount
        //              'field-fees' => "0.000058200000000000", // they have fixed it for $filled-fees
        //             'finished-at' =>  1545831584181,
        //                    source => "spot-api",
        //                     state => "$filled",
        //             'canceled-at' =>  0                      }
        //
        $id = $this->safe_string($order, 'id');
        $side = null;
        $type = null;
        $status = null;
        if (is_array($order) && array_key_exists('type', $order)) {
            $orderType = explode('-', $order['type']);
            $side = $orderType[0];
            $type = $orderType[1];
            $status = $this->parse_order_status($this->safe_string($order, 'state'));
        }
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->safe_integer($order, 'created-at');
        $amount = $this->safe_number($order, 'amount');
        $filled = $this->safe_number_2($order, 'filled-amount', 'field-amount'); // typo in their API, $filled $amount
        $price = $this->safe_number($order, 'price');
        if ($price === 0.0) {
            $price = null;
        }
        $cost = $this->safe_number_2($order, 'filled-cash-amount', 'field-cash-amount'); // same typo
        $feeCost = $this->safe_number_2($order, 'filled-fees', 'field-fees'); // typo in their API, $filled fees
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrency = null;
            if ($market !== null) {
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

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = $this->market($symbol);
        $request = array(
            'account-id' => $this->accounts[0]['id'],
            'symbol' => $market['id'],
            'type' => $side . '-' . $type,
        );
        if (($type === 'market') && ($side === 'buy')) {
            if ($this->options['createMarketBuyOrderRequiresPrice']) {
                if ($price === null) {
                    throw new InvalidOrder($this->id . " $market buy order requires $price argument to calculate cost (total $amount of quote currency to spend for buying, $amount * $price). To switch off this warning exception and specify cost in the $amount argument, set .options['createMarketBuyOrderRequiresPrice'] = false. Make sure you know what you're doing.");
                } else {
                    // despite that cost = $amount * $price is in quote currency and should have quote precision
                    // the exchange API requires the cost supplied in 'amount' to be of base precision
                    // more about it here:
                    // https://github.com/ccxt/ccxt/pull/4395
                    // https://github.com/ccxt/ccxt/issues/7611
                    // we use amountToPrecision here because the exchange requires cost in base precision
                    $request['amount'] = $this->cost_to_precision($symbol, floatval($amount) * floatval($price));
                }
            } else {
                $request['amount'] = $this->cost_to_precision($symbol, $amount);
            }
        } else {
            $request['amount'] = $this->amount_to_precision($symbol, $amount);
        }
        if ($type === 'limit' || $type === 'ioc' || $type === 'limit-maker') {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $method = $this->options['createOrderMethod'];
        $response = $this->$method (array_merge($request, $params));
        $timestamp = $this->milliseconds();
        $id = $this->safe_string($response, 'data');
        return array(
            'info' => $response,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => null,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'filled' => null,
            'remaining' => null,
            'cost' => null,
            'trades' => null,
            'fee' => null,
            'clientOrderId' => null,
            'average' => null,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $response = $this->privatePostOrderOrdersIdSubmitcancel (array( 'id' => $id ));
        //
        //     $response = array(
        //         'status' => 'ok',
        //         'data' => '10138899000',
        //     );
        //
        return array_merge($this->parse_order($response), array(
            'id' => $id,
            'status' => 'canceled',
        ));
    }

    public function cancel_orders($ids, $symbol = null, $params = array ()) {
        $this->load_markets();
        $clientOrderIds = $this->safe_value_2($params, 'clientOrderIds', 'client-order-ids');
        $params = $this->omit($params, array( 'clientOrderIds', 'client-order-ids' ));
        $request = array();
        if ($clientOrderIds === null) {
            $request['order-ids'] = $ids;
        } else {
            $request['client-order-ids'] = $clientOrderIds;
        }
        $response = $this->privatePostOrderOrdersBatchcancel (array_merge($request, $params));
        //
        //     {
        //         "status" => "ok",
        //         "data" => {
        //             "success" => array(
        //                 "5983466"
        //             ),
        //             "failed" => array(
        //                 array(
        //                     "err-msg" => "Incorrect order state",
        //                     "order-state" => 7,
        //                     "order-id" => "",
        //                     "err-code" => "order-orderstate-error",
        //                     "client-order-id" => "first"
        //                 ),
        //                 array(
        //                     "err-msg" => "Incorrect order state",
        //                     "order-state" => 7,
        //                     "order-id" => "",
        //                     "err-code" => "order-orderstate-error",
        //                     "client-order-id" => "second"
        //                 ),
        //                 {
        //                     "err-msg" => "The record is not found.",
        //                     "order-id" => "",
        //                     "err-code" => "base-not-found",
        //                     "client-order-id" => "third"
        //                 }
        //             )
        //         }
        //     }
        //
        return $response;
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'account-id' string false NA The account id used for this cancel Refer to GET /v1/account/accounts
            // 'symbol' => $market['id'], // a list of comma-separated symbols, all symbols by default
            // 'types' 'string', buy-$market, sell-$market, buy-limit, sell-limit, buy-ioc, sell-ioc, buy-stop-limit, sell-stop-limit, buy-limit-fok, sell-limit-fok, buy-stop-limit-fok, sell-stop-limit-fok
            // 'side' => 'buy', // or 'sell'
            // 'size' => 100, // the number of orders to cancel 1-100
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $response = $this->privatePostOrderOrdersBatchCancelOpenOrders (array_merge($request, $params));
        //
        //     {
        //         code => 200,
        //         data => {
        //             "success-count" => 2,
        //             "failed-count" => 0,
        //             "next-id" => 5454600
        //         }
        //     }
        //
        return $response;
    }

    public function currency_to_precision($currency, $fee) {
        return $this->decimal_to_precision($fee, 0, $this->currencies[$currency]['precision']);
    }

    public function parse_deposit_address($depositAddress, $currency = null) {
        //
        //     {
        //         $currency => "eth",
        //         $address => "0xf7292eb9ba7bc50358e27f0e025a4d225a64127b",
        //         addressTag => "",
        //         chain => "eth"
        //     }
        //
        $address = $this->safe_string($depositAddress, 'address');
        $tag = $this->safe_string($depositAddress, 'addressTag');
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
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->v2PrivateGetAccountDepositAddress (array_merge($request, $params));
        //
        //     {
        //         $code => 200,
        //         $data => array(
        //             {
        //                 $currency => "eth",
        //                 address => "0xf7292eb9ba7bc50358e27f0e025a4d225a64127b",
        //                 addressTag => "",
        //                 chain => "eth"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_deposit_address($this->safe_value($data, 0, array()), $currency);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        if ($limit === null || $limit > 100) {
            $limit = 100;
        }
        $this->load_markets();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $request = array(
            'type' => 'deposit',
            'from' => 0, // From 'id' ... if you want to get results after a particular transaction id, pass the id in $params->from
        );
        if ($currency !== null) {
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['size'] = $limit; // max 100
        }
        $response = $this->privateGetQueryDepositWithdraw (array_merge($request, $params));
        // return $response
        return $this->parse_transactions($response['data'], $currency, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        if ($limit === null || $limit > 100) {
            $limit = 100;
        }
        $this->load_markets();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $request = array(
            'type' => 'withdraw',
            'from' => 0, // From 'id' ... if you want to get results after a particular transaction id, pass the id in $params->from
        );
        if ($currency !== null) {
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['size'] = $limit; // max 100
        }
        $response = $this->privateGetQueryDepositWithdraw (array_merge($request, $params));
        // return $response
        return $this->parse_transactions($response['data'], $currency, $since, $limit);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         'id' => 8211029,
        //         'type' => 'deposit',
        //         'currency' => 'eth',
        //         'chain' => 'eth',
        //         'tx-hash' => 'bd315....',
        //         'amount' => 0.81162421,
        //         'address' => '4b8b....',
        //         'address-tag' => '',
        //         'fee' => 0,
        //         'state' => 'safe',
        //         'created-at' => 1542180380965,
        //         'updated-at' => 1542180788077
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         'id' => 6908275,
        //         'type' => 'withdraw',
        //         'currency' => 'btc',
        //         'chain' => 'btc',
        //         'tx-hash' => 'c1a1a....',
        //         'amount' => 0.80257005,
        //         'address' => '1QR....',
        //         'address-tag' => '',
        //         'fee' => 0.0005,
        //         'state' => 'confirmed',
        //         'created-at' => 1552107295685,
        //         'updated-at' => 1552108032859
        //     }
        //
        $timestamp = $this->safe_integer($transaction, 'created-at');
        $updated = $this->safe_integer($transaction, 'updated-at');
        $code = $this->safe_currency_code($this->safe_string($transaction, 'currency'));
        $type = $this->safe_string($transaction, 'type');
        if ($type === 'withdraw') {
            $type = 'withdrawal';
        }
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'state'));
        $tag = $this->safe_string($transaction, 'address-tag');
        $feeCost = $this->safe_number($transaction, 'fee');
        if ($feeCost !== null) {
            $feeCost = abs($feeCost);
        }
        return array(
            'info' => $transaction,
            'id' => $this->safe_string($transaction, 'id'),
            'txid' => $this->safe_string($transaction, 'tx-hash'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $this->safe_string($transaction, 'address'),
            'tag' => $tag,
            'type' => $type,
            'amount' => $this->safe_number($transaction, 'amount'),
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

    public function parse_transaction_status($status) {
        $statuses = array(
            // deposit $statuses
            'unknown' => 'failed',
            'confirming' => 'pending',
            'confirmed' => 'ok',
            'safe' => 'ok',
            'orphan' => 'failed',
            // withdrawal $statuses
            'submitted' => 'pending',
            'canceled' => 'canceled',
            'reexamine' => 'pending',
            'reject' => 'failed',
            'pass' => 'pending',
            'wallet-reject' => 'failed',
            // 'confirmed' => 'ok', // present in deposit $statuses
            'confirm-error' => 'failed',
            'repealed' => 'failed',
            'wallet-transfer' => 'pending',
            'pre-transfer' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $this->check_address($address);
        $currency = $this->currency($code);
        $request = array(
            'address' => $address, // only supports existing addresses in your withdraw $address list
            'amount' => $amount,
            'currency' => strtolower($currency['id']),
        );
        if ($tag !== null) {
            $request['addr-tag'] = $tag; // only for XRP?
        }
        $response = $this->privatePostDwWithdrawApiCreate (array_merge($request, $params));
        $id = $this->safe_string($response, 'data');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '/';
        if ($api === 'market') {
            $url .= $api;
        } else if (($api === 'public') || ($api === 'private')) {
            $url .= $this->version;
        } else if (($api === 'v2Public') || ($api === 'v2Private')) {
            $url .= 'v2';
        }
        $url .= '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'private' || $api === 'v2Private') {
            $this->check_required_credentials();
            $timestamp = $this->ymdhms($this->milliseconds(), 'T');
            $request = array(
                'SignatureMethod' => 'HmacSHA256',
                'SignatureVersion' => '2',
                'AccessKeyId' => $this->apiKey,
                'Timestamp' => $timestamp,
            );
            if ($method !== 'POST') {
                $request = array_merge($request, $query);
            }
            $request = $this->keysort($request);
            $auth = $this->urlencode($request);
            // unfortunately, PHP demands double quotes for the escaped newline symbol
            // eslint-disable-next-line quotes
            $payload = implode("\n", array($method, $this->hostname, $url, $auth));
            $signature = $this->hmac($this->encode($payload), $this->encode($this->secret), 'sha256', 'base64');
            $auth .= '&' . $this->urlencode(array( 'Signature' => $signature ));
            $url .= '?' . $auth;
            if ($method === 'POST') {
                $body = $this->json($query);
                $headers = array(
                    'Content-Type' => 'application/json',
                );
            } else {
                $headers = array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                );
            }
        } else {
            if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        }
        $url = $this->implode_params($this->urls['api'][$api], array(
            'hostname' => $this->hostname,
        )) . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if (is_array($response) && array_key_exists('status', $response)) {
            //
            //     array("$status":"error","err-$code":"order-limitorder-amount-min-error","err-msg":"limit order amount error, min => `0.001`","data":null)
            //
            $status = $this->safe_string($response, 'status');
            if ($status === 'error') {
                $code = $this->safe_string($response, 'err-code');
                $feedback = $this->id . ' ' . $body;
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $body, $feedback);
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
                $message = $this->safe_string($response, 'err-msg');
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
                throw new ExchangeError($feedback);
            }
        }
    }
}

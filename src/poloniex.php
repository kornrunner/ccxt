<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\OrderNotFound;

class poloniex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'poloniex',
            'name' => 'Poloniex',
            'countries' => array( 'US' ),
            'rateLimit' => 1000, // up to 6 calls per second
            'certified' => false,
            'pro' => true,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createDepositAddress' => true,
                'createMarketOrder' => false,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrder' => true, // true endpoint for a single open order
                'fetchOpenOrders' => true, // true endpoint for open orders
                'fetchOrderBook' => true,
                'fetchOrderBooks' => true,
                'fetchOrderTrades' => true, // true endpoint for trades of a single open or closed order
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTradingFee' => true,
                'fetchTradingFees' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => true,
                'cancelAllOrders' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '5m' => 300,
                '15m' => 900,
                '30m' => 1800,
                '2h' => 7200,
                '4h' => 14400,
                '1d' => 86400,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766817-e9456312-5ee6-11e7-9b3c-b628ca5626a5.jpg',
                'api' => array(
                    'public' => 'https://poloniex.com/public',
                    'private' => 'https://poloniex.com/tradingApi',
                ),
                'www' => 'https://www.poloniex.com',
                'doc' => 'https://docs.poloniex.com',
                'fees' => 'https://poloniex.com/fees',
                'referral' => 'https://poloniex.com/signup?c=UBFZJRPJ',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'return24hVolume',
                        'returnChartData',
                        'returnCurrencies',
                        'returnLoanOrders',
                        'returnOrderBook',
                        'returnTicker',
                        'returnTradeHistory',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'buy',
                        'cancelLoanOffer',
                        'cancelOrder',
                        'cancelAllOrders',
                        'closeMarginPosition',
                        'createLoanOffer',
                        'generateNewAddress',
                        'getMarginPosition',
                        'marginBuy',
                        'marginSell',
                        'moveOrder',
                        'returnActiveLoans',
                        'returnAvailableAccountBalances',
                        'returnBalances',
                        'returnCompleteBalances',
                        'returnDepositAddresses',
                        'returnDepositsWithdrawals',
                        'returnFeeInfo',
                        'returnLendingHistory',
                        'returnMarginAccountSummary',
                        'returnOpenLoanOffers',
                        'returnOpenOrders',
                        'returnOrderTrades',
                        'returnOrderStatus',
                        'returnTradableBalances',
                        'returnTradeHistory',
                        'sell',
                        'toggleAutoRenew',
                        'transferBalance',
                        'withdraw',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    // starting from Jan 8 2020
                    'maker' => 0.0009,
                    'taker' => 0.0009,
                ),
                'funding' => array(),
            ),
            'limits' => array(
                'amount' => array(
                    'min' => 0.000001,
                    'max' => 1000000000,
                ),
                'price' => array(
                    'min' => 0.00000001,
                    'max' => 1000000000,
                ),
                'cost' => array(
                    'min' => 0.00000000,
                    'max' => 1000000000,
                ),
            ),
            'precision' => array(
                'amount' => 8,
                'price' => 8,
            ),
            'commonCurrencies' => array(
                'AIR' => 'AirCoin',
                'APH' => 'AphroditeCoin',
                'BCC' => 'BTCtalkcoin',
                'BDG' => 'Badgercoin',
                'BTM' => 'Bitmark',
                'CON' => 'Coino',
                'GOLD' => 'GoldEagles',
                'GPUC' => 'GPU',
                'HOT' => 'Hotcoin',
                'ITC' => 'Information Coin',
                'KEY' => 'KEYCoin',
                'PLX' => 'ParallaxCoin',
                'REPV2' => 'REP',
                'STR' => 'XLM',
                'SOC' => 'SOCC',
                'XAP' => 'API Coin',
                // this is not documented in the API docs for Poloniex
                // https://github.com/ccxt/ccxt/issues/7084
                // when the user calls withdraw ('USDT', amount, address, tag, params)
                // with params = array( 'currencyToWithdrawAs' => 'USDTTRON' )
                // or params = array( 'currencyToWithdrawAs' => 'USDTETH' )
                // fetchWithdrawals ('USDT') returns the corresponding withdrawals
                // with a USDTTRON or a USDTETH currency id, respectfully
                // therefore we have map them back to the original code USDT
                // otherwise the returned withdrawals are filtered out
                'USDTTRON' => 'USDT',
                'USDTETH' => 'USDT',
            ),
            'options' => array(
                'limits' => array(
                    'cost' => array(
                        'min' => array(
                            'BTC' => 0.0001,
                            'ETH' => 0.0001,
                            'XMR' => 0.0001,
                            'USDT' => 1.0,
                        ),
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'You may only place orders that reduce your position.' => '\\ccxt\\InvalidOrder',
                    'Invalid order number, or you are not the person who placed the order.' => '\\ccxt\\OrderNotFound',
                    'Permission denied' => '\\ccxt\\PermissionDenied',
                    'Connection timed out. Please try again.' => '\\ccxt\\RequestTimeout',
                    'Internal error. Please try again.' => '\\ccxt\\ExchangeNotAvailable',
                    'Currently in maintenance mode.' => '\\ccxt\\OnMaintenance',
                    'Order not found, or you are not the person who placed it.' => '\\ccxt\\OrderNotFound',
                    'Invalid API key/secret pair.' => '\\ccxt\\AuthenticationError',
                    'Please do not make more than 8 API calls per second.' => '\\ccxt\\DDoSProtection',
                    'Rate must be greater than zero.' => '\\ccxt\\InvalidOrder', // array("error":"Rate must be greater than zero.")
                    'Invalid currency pair.' => '\\ccxt\\BadSymbol', // array("error":"Invalid currency pair.")
                    'Invalid currencyPair parameter.' => '\\ccxt\\BadSymbol', // array("error":"Invalid currencyPair parameter.")
                ),
                'broad' => array(
                    'Total must be at least' => '\\ccxt\\InvalidOrder', // array("error":"Total must be at least 0.0001.")
                    'This account is frozen.' => '\\ccxt\\AccountSuspended',
                    'Not enough' => '\\ccxt\\InsufficientFunds',
                    'Nonce must be greater' => '\\ccxt\\InvalidNonce',
                    'You have already called cancelOrder or moveOrder on this order.' => '\\ccxt\\CancelPending',
                    'Amount must be at least' => '\\ccxt\\InvalidOrder', // array("error":"Amount must be at least 0.000001.")
                    'is either completed or does not exist' => '\\ccxt\\InvalidOrder', // array("error":"Order 587957810791 is either completed or does not exist.")
                    'Error pulling ' => '\\ccxt\\ExchangeError', // array("error":"Error pulling order book")
                ),
            ),
        ));
    }

    public function calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $key = 'quote';
        $rate = $market[$takerOrMaker];
        $cost = floatval($this->cost_to_precision($symbol, $amount * $rate));
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
        }
        return array(
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval($this->fee_to_precision($symbol, $cost)),
        );
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "date":1590913773,
        //         "high":0.02491611,
        //         "low":0.02491611,
        //         "open":0.02491611,
        //         "close":0.02491611,
        //         "volume":0,
        //         "quoteVolume":0,
        //         "weightedAverage":0.02491611
        //     }
        //
        return array(
            $this->safe_timestamp($ohlcv, 'date'),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'quoteVolume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '5m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currencyPair' => $market['id'],
            'period' => $this->timeframes[$timeframe],
        );
        if ($since === null) {
            $request['end'] = $this->seconds();
            if ($limit === null) {
                $request['start'] = $request['end'] - $this->parse_timeframe('1w'); // max range = 1 week
            } else {
                $request['start'] = $request['end'] - $limit * $this->parse_timeframe($timeframe);
            }
        } else {
            $request['start'] = intval($since / 1000);
            if ($limit !== null) {
                $end = $this->sum($request['start'], $limit * $this->parse_timeframe($timeframe));
                $request['end'] = $end;
            }
        }
        $response = $this->publicGetReturnChartData (array_merge($request, $params));
        //
        //     array(
        //         array("date":1590913773,"high":0.02491611,"low":0.02491611,"open":0.02491611,"close":0.02491611,"volume":0,"quoteVolume":0,"weightedAverage":0.02491611),
        //         array("date":1590913800,"high":0.02495324,"low":0.02489501,"open":0.02491797,"close":0.02493693,"volume":0.0927415,"quoteVolume":3.7227869,"weightedAverage":0.02491185),
        //         array("date":1590914100,"high":0.02498596,"low":0.02488503,"open":0.02493033,"close":0.02497896,"volume":0.21196348,"quoteVolume":8.50291888,"weightedAverage":0.02492832),
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function load_markets($reload = false, $params = array ()) {
        $markets = parent::load_markets($reload, $params);
        $currenciesByNumericId = $this->safe_value($this->options, 'currenciesByNumericId');
        if (($currenciesByNumericId === null) || $reload) {
            $this->options['currenciesByNumericId'] = $this->index_by($this->currencies, 'numericId');
        }
        return $markets;
    }

    public function fetch_markets($params = array ()) {
        $markets = $this->publicGetReturnTicker ($params);
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $market = $markets[$id];
            list($quoteId, $baseId) = explode('_', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $limits = array_merge($this->limits, array(
                'cost' => array(
                    'min' => $this->safe_value($this->options['limits']['cost']['min'], $quote),
                ),
            ));
            $isFrozen = $this->safe_string($market, 'isFrozen');
            $active = ($isFrozen !== '1');
            $numericId = $this->safe_integer($market, 'id');
            $result[] = array(
                'id' => $id,
                'numericId' => $numericId,
                'symbol' => $symbol,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'base' => $base,
                'quote' => $quote,
                'active' => $active,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $request = array(
            'account' => 'all',
        );
        $response = $this->privatePostReturnCompleteBalances (array_merge($request, $params));
        $result = array( 'info' => $response );
        $currencyIds = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $balance = $this->safe_value($response, $currencyId, array());
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['used'] = $this->safe_float($balance, 'onOrders');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_trading_fees($params = array ()) {
        $this->load_markets();
        $fees = $this->privatePostReturnFeeInfo ($params);
        //
        //     {
        //         makerFee => '0.00100000',
        //         takerFee => '0.00200000',
        //         marginMakerFee => '0.00100000',
        //         marginTakerFee => '0.00200000',
        //         thirtyDayVolume => '106.08463302',
        //         nextTier => 500000,
        //     }
        //
        return array(
            'info' => $fees,
            'maker' => $this->safe_float($fees, 'makerFee'),
            'taker' => $this->safe_float($fees, 'takerFee'),
            'withdraw' => array(),
            'deposit' => array(),
        );
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'currencyPair' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['depth'] = $limit; // 100
        }
        $response = $this->publicGetReturnOrderBook (array_merge($request, $params));
        $orderbook = $this->parse_order_book($response);
        $orderbook['nonce'] = $this->safe_integer($response, 'seq');
        return $orderbook;
    }

    public function fetch_order_books($symbols = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'currencyPair' => 'all',
        );
        if ($limit !== null) {
            $request['depth'] = $limit; // 100
        }
        $response = $this->publicGetReturnOrderBook (array_merge($request, $params));
        $marketIds = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $symbol = null;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $symbol = $this->markets_by_id[$marketId]['symbol'];
            } else {
                list($quoteId, $baseId) = explode('_', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
            $orderbook = $this->parse_order_book($response[$marketId]);
            $orderbook['nonce'] = $this->safe_integer($response[$marketId], 'seq');
            $result[$symbol] = $orderbook;
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $open = null;
        $change = null;
        $average = null;
        $last = $this->safe_float($ticker, 'last');
        $relativeChange = $this->safe_float($ticker, 'percentChange');
        if ($relativeChange !== -1) {
            $open = $last / $this->sum(1, $relativeChange);
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high24hr'),
            'low' => $this->safe_float($ticker, 'low24hr'),
            'bid' => $this->safe_float($ticker, 'highestBid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'lowestAsk'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $relativeChange * 100,
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'quoteVolume'),
            'quoteVolume' => $this->safe_float($ticker, 'baseVolume'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetReturnTicker ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $symbol = null;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                list($quoteId, $baseId) = explode('_', $id);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
                $market = array( 'symbol' => $symbol );
            }
            $ticker = $response[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetReturnCurrencies ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $currency = $response[$id];
            $precision = 8; // default $precision, todo => fix "magic constants"
            $code = $this->safe_currency_code($id);
            $active = ($currency['delisted'] === 0) && !$currency['disabled'];
            $numericId = $this->safe_integer($currency, 'id');
            $fee = $this->safe_float($currency, 'txFee');
            $result[$code] = array(
                'id' => $id,
                'numericId' => $numericId,
                'code' => $code,
                'info' => $currency,
                'name' => $currency['name'],
                'active' => $active,
                'fee' => $fee,
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
                    'withdraw' => array(
                        'min' => $fee,
                        'max' => pow(10, $precision),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $response = $this->publicGetReturnTicker ($params);
        $ticker = $response[$market['id']];
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchMyTrades
        //
        //     {
        //       globalTradeID => 471030550,
        //       tradeID => '42582',
        //       date => '2020-06-16 09:47:50',
        //       rate => '0.000079980000',
        //       $amount => '75215.00000000',
        //       total => '6.01569570',
        //       $fee => '0.00095000',
        //       $feeDisplay => '0.26636100 TRX (0.07125%)',
        //       orderNumber => '5963454848',
        //       type => 'sell',
        //       category => 'exchange'
        //     }
        //
        // createOrder (taker trades)
        //
        //     {
        //         'amount' => '200.00000000',
        //         'date' => '2019-12-15 16:04:10',
        //         'rate' => '0.00000355',
        //         'total' => '0.00071000',
        //         'tradeID' => '119871',
        //         'type' => 'buy',
        //         'takerAdjustment' => '200.00000000'
        //     }
        //
        $id = $this->safe_string_2($trade, 'globalTradeID', 'tradeID');
        $orderId = $this->safe_string($trade, 'orderNumber');
        $timestamp = $this->parse8601($this->safe_string($trade, 'date'));
        $symbol = null;
        if ((!$market) && (is_array($trade) && array_key_exists('currencyPair', $trade))) {
            $marketId = $this->safe_string($trade, 'currencyPair');
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                list($quoteId, $baseId) = explode('_', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $side = $this->safe_string($trade, 'type');
        $fee = null;
        $price = $this->safe_float($trade, 'rate');
        $cost = $this->safe_float($trade, 'total');
        $amount = $this->safe_float($trade, 'amount');
        $feeDisplay = $this->safe_string($trade, 'feeDisplay');
        if ($feeDisplay !== null) {
            $parts = explode(' ', $feeDisplay);
            $feeCost = $this->safe_float($parts, 0);
            if ($feeCost !== null) {
                $feeCurrencyId = $this->safe_string($parts, 1);
                $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
                $feeRate = $this->safe_string($parts, 2);
                if ($feeRate !== null) {
                    $feeRate = str_replace('(', '', $feeRate);
                    $feeRateParts = explode('%', $feeRate);
                    $feeRate = $this->safe_string($feeRateParts, 0);
                    $feeRate = floatval($feeRate) / 100;
                }
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $feeCurrencyCode,
                    'rate' => $feeRate,
                );
            }
        }
        $takerOrMaker = null;
        $takerAdjustment = $this->safe_float($trade, 'takerAdjustment');
        if ($takerAdjustment !== null) {
            $takerOrMaker = 'taker';
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => 'limit',
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currencyPair' => $market['id'],
        );
        if ($since !== null) {
            $request['start'] = intval($since / 1000);
            $request['end'] = $this->seconds(); // last 50000 $trades by default
        }
        $trades = $this->publicGetReturnTradeHistory (array_merge($request, $params));
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $pair = $market ? $market['id'] : 'all';
        $request = array( 'currencyPair' => $pair );
        if ($since !== null) {
            $request['start'] = intval($since / 1000);
            $request['end'] = $this->sum($this->seconds(), 1); // adding 1 is a fix for #3411
        }
        // $limit is disabled (does not really work as expected)
        if ($limit !== null) {
            $request['limit'] = intval($limit);
        }
        $response = $this->privatePostReturnTradeHistory (array_merge($request, $params));
        //
        // specific $market ($symbol defined)
        //
        //     array(
        //         array(
        //             globalTradeID => 470912587,
        //             tradeID => '42543',
        //             date => '2020-06-15 17:31:22',
        //             rate => '0.000083840000',
        //             amount => '95237.60321429',
        //             total => '7.98472065',
        //             fee => '0.00095000',
        //             feeDisplay => '0.36137761 TRX (0.07125%)',
        //             orderNumber => '5926344995',
        //             type => 'sell',
        //             category => 'exchange'
        //         ),
        //         {
        //             globalTradeID => 470974497,
        //             tradeID => '42560',
        //             date => '2020-06-16 00:41:23',
        //             rate => '0.000078220000',
        //             amount => '1000000.00000000',
        //             total => '78.22000000',
        //             fee => '0.00095000',
        //             feeDisplay => '3.48189819 TRX (0.07125%)',
        //             orderNumber => '5945490830',
        //             type => 'sell',
        //             category => 'exchange'
        //         }
        //     )
        //
        // all markets ($symbol null)
        //
        //     {
        //        BTC_GNT => [array(
        //             globalTradeID => 470839947,
        //             tradeID => '4322347',
        //             date => '2020-06-15 12:25:24',
        //             rate => '0.000005810000',
        //             amount => '1702.04429303',
        //             total => '0.00988887',
        //             fee => '0.00095000',
        //             feeDisplay => '4.18235294 TRX (0.07125%)',
        //             orderNumber => '102290272520',
        //             type => 'buy',
        //             category => 'exchange'
        //     ), array(
        //             globalTradeID => 470895902,
        //             tradeID => '4322413',
        //             date => '2020-06-15 16:19:00',
        //             rate => '0.000005980000',
        //             amount => '18.66879219',
        //             total => '0.00011163',
        //             fee => '0.00095000',
        //             feeDisplay => '0.04733727 TRX (0.07125%)',
        //             orderNumber => '102298304480',
        //             type => 'buy',
        //             category => 'exchange'
        //         )],
        //     }
        //
        $result = array();
        if ($market !== null) {
            $result = $this->parse_trades($response, $market);
        } else {
            if ($response) {
                $ids = is_array($response) ? array_keys($response) : array();
                for ($i = 0; $i < count($ids); $i++) {
                    $id = $ids[$i];
                    $market = null;
                    if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                        $market = $this->markets_by_id[$id];
                        $trades = $this->parse_trades($response[$id], $market);
                        for ($j = 0; $j < count($trades); $j++) {
                            $result[] = $trades[$j];
                        }
                    } else {
                        list($quoteId, $baseId) = explode('_', $id);
                        $base = $this->safe_currency_code($baseId);
                        $quote = $this->safe_currency_code($quoteId);
                        $symbol = $base . '/' . $quote;
                        $trades = $response[$id];
                        for ($j = 0; $j < count($trades); $j++) {
                            $market = array(
                                'symbol' => $symbol,
                                'base' => $base,
                                'quote' => $quote,
                            );
                            $result[] = $this->parse_trade($trades[$j], $market);
                        }
                    }
                }
            }
        }
        return $this->filter_by_since_limit($result, $since, $limit);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'Open' => 'open',
            'Partially filled' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOpenOrder
        //
        //     {
        //         $status => 'Open',
        //         rate => '0.40000000',
        //         $amount => '1.00000000',
        //         currencyPair => 'BTC_ETH',
        //         date => '2018-10-17 17:04:50',
        //         total => '0.40000000',
        //         $type => 'buy',
        //         startingAmount => '1.00000',
        //     }
        //
        // fetchOpenOrders
        //
        //     {
        //         orderNumber => '514514894224',
        //         $type => 'buy',
        //         rate => '0.00001000',
        //         startingAmount => '100.00000000',
        //         $amount => '100.00000000',
        //         total => '0.00100000',
        //         date => '2018-10-23 17:38:53',
        //         margin => 0,
        //     }
        //
        // createOrder
        //
        //     {
        //         'orderNumber' => '9805453960',
        //         'resultingTrades' => array(
        //             array(
        //                 'amount' => '200.00000000',
        //                 'date' => '2019-12-15 16:04:10',
        //                 'rate' => '0.00000355',
        //                 'total' => '0.00071000',
        //                 'tradeID' => '119871',
        //                 'type' => 'buy',
        //                 'takerAdjustment' => '200.00000000',
        //             ),
        //         ),
        //         'fee' => '0.00000000',
        //         'clientOrderId' => '12345',
        //         'currencyPair' => 'BTC_MANA',
        //         // ---------------------------------------------------------
        //         // the following fields are injected by createOrder
        //         'timestamp' => $timestamp,
        //         'status' => 'open',
        //         'type' => $type,
        //         'side' => $side,
        //         'price' => $price,
        //         'amount' => $amount,
        //         // ---------------------------------------------------------
        //         // 'resultingTrades' in editOrder
        //         'resultingTrades' => {
        //             'BTC_MANA' => array(),
        //          }
        //     }
        //
        $timestamp = $this->safe_integer($order, 'timestamp');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($order, 'date'));
        }
        $symbol = null;
        $marketId = $this->safe_string($order, 'currencyPair');
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                list($quoteId, $baseId) = explode('_', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $trades = null;
        $resultingTrades = $this->safe_value($order, 'resultingTrades');
        if (gettype($resultingTrades) === 'array' && count(array_filter(array_keys($resultingTrades), 'is_string')) != 0) {
            $resultingTrades = $this->safe_value($resultingTrades, $this->safe_string($market, 'id', $marketId));
        }
        if ($resultingTrades !== null) {
            $trades = $this->parse_trades($resultingTrades, $market);
        }
        $price = $this->safe_float_2($order, 'price', 'rate');
        $remaining = $this->safe_float($order, 'amount');
        $amount = $this->safe_float($order, 'startingAmount');
        $filled = null;
        $cost = 0;
        if ($amount !== null) {
            if ($remaining !== null) {
                $filled = $amount - $remaining;
                if ($price !== null) {
                    $cost = $filled * $price;
                }
            }
        } else {
            $amount = $remaining;
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $average = null;
        $lastTradeTimestamp = null;
        if ($filled === null) {
            if ($trades !== null) {
                $filled = 0;
                $cost = 0;
                $tradesLength = is_array($trades) ? count($trades) : 0;
                if ($tradesLength > 0) {
                    $lastTradeTimestamp = $trades[0]['timestamp'];
                    for ($i = 0; $i < $tradesLength; $i++) {
                        $trade = $trades[$i];
                        $tradeAmount = $trade['amount'];
                        $tradePrice = $trade['price'];
                        $filled = $this->sum($filled, $tradeAmount);
                        $cost = $this->sum($cost, $tradePrice * $tradeAmount);
                        $lastTradeTimestamp = max ($lastTradeTimestamp, $trade['timestamp']);
                    }
                }
                if ($amount !== null) {
                    $remaining = max ($amount - $filled, 0);
                    if ($filled >= $amount) {
                        $status = 'closed';
                    }
                }
            }
        }
        if (($filled !== null) && ($cost !== null) && ($filled > 0)) {
            $average = $cost / $filled;
        }
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side', $type);
        if ($type === $side) {
            $type = null;
        }
        $id = $this->safe_string($order, 'orderNumber');
        $fee = null;
        $feeCost = $this->safe_float($order, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyCode = null;
            if ($market !== null) {
                $feeCurrencyCode = ($side === 'buy') ? $market['base'] : $market['quote'];
            }
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        $clientOrderId = $this->safe_string($order, 'clientOrderId');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'status' => $status,
            'symbol' => $symbol,
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
            'trades' => $trades,
            'fee' => $fee,
        );
    }

    public function parse_open_orders($orders, $market, $result) {
        for ($i = 0; $i < count($orders); $i++) {
            $order = $orders[$i];
            $extended = array_merge($order, array(
                'status' => 'open',
                'type' => 'limit',
                'side' => $order['type'],
                'price' => $order['rate'],
            ));
            $result[] = $this->parse_order($extended, $market);
        }
        return $result;
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $pair = $market ? $market['id'] : 'all';
        $request = array(
            'currencyPair' => $pair,
        );
        $response = $this->privatePostReturnOpenOrders (array_merge($request, $params));
        $extension = array( 'status' => 'open' );
        if ($market === null) {
            $marketIds = is_array($response) ? array_keys($response) : array();
            $openOrders = array();
            for ($i = 0; $i < count($marketIds); $i++) {
                $marketId = $marketIds[$i];
                $orders = $response[$marketId];
                $m = $this->markets_by_id[$marketId];
                $openOrders = $this->array_concat($openOrders, $this->parse_orders($orders, $m, null, null, $extension));
            }
            return $this->filter_by_since_limit($openOrders, $since, $limit);
        } else {
            return $this->parse_orders($response, $market, $since, $limit, $extension);
        }
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' createOrder() does not accept $market orders');
        }
        $this->load_markets();
        $method = 'privatePost' . $this->capitalize($side);
        $market = $this->market($symbol);
        $amount = $this->amount_to_precision($symbol, $amount);
        $request = array(
            'currencyPair' => $market['id'],
            'rate' => $this->price_to_precision($symbol, $price),
            'amount' => $amount,
        );
        $clientOrderId = $this->safe_string($params, 'clientOrderId');
        if ($clientOrderId !== null) {
            $request['clientOrderId'] = $clientOrderId;
            $params = $this->omit($params, 'clientOrderId');
        }
        // remember the $timestamp before issuing the $request
        $timestamp = $this->milliseconds();
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         'orderNumber' => '9805453960',
        //         'resultingTrades' => array(
        //             array(
        //                 'amount' => '200.00000000',
        //                 'date' => '2019-12-15 16:04:10',
        //                 'rate' => '0.00000355',
        //                 'total' => '0.00071000',
        //                 'tradeID' => '119871',
        //                 'type' => 'buy',
        //                 'takerAdjustment' => '200.00000000',
        //             ),
        //         ),
        //         'fee' => '0.00000000',
        //         'currencyPair' => 'BTC_MANA',
        //     }
        //
        return $this->parse_order(array_merge(array(
            'timestamp' => $timestamp,
            'status' => 'open',
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
        ), $response), $market);
    }

    public function edit_order($id, $symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $price = floatval($price);
        $request = array(
            'orderNumber' => $id,
            'rate' => $this->price_to_precision($symbol, $price),
        );
        if ($amount !== null) {
            $request['amount'] = $this->amount_to_precision($symbol, $amount);
        }
        $response = $this->privatePostMoveOrder (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $clientOrderId = $this->safe_value($params, 'clientOrderId');
        if ($clientOrderId === null) {
            $request['orderNumber'] = $id;
        } else {
            $request['clientOrderId'] = $clientOrderId;
        }
        $params = $this->omit($params, 'clientOrderId');
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['currencyPair'] = $market['id'];
        }
        $response = $this->privatePostCancelAllOrders (array_merge($request, $params));
        //
        //     {
        //         "success" => 1,
        //         "message" => "Orders canceled",
        //         "orderNumbers" => array(
        //             503749,
        //             888321,
        //             7315825,
        //             7316824
        //         )
        //     }
        //
        return $response;
    }

    public function fetch_open_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $id = (string) $id;
        $request = array(
            'orderNumber' => $id,
        );
        $response = $this->privatePostReturnOrderStatus (array_merge($request, $params));
        //
        //     {
        //         success => 1,
        //         $result => array(
        //             '6071071' => array(
        //                 status => 'Open',
        //                 rate => '0.40000000',
        //                 amount => '1.00000000',
        //                 currencyPair => 'BTC_ETH',
        //                 date => '2018-10-17 17:04:50',
        //                 total => '0.40000000',
        //                 type => 'buy',
        //                 startingAmount => '1.00000',
        //             ),
        //         ),
        //     }
        //
        $result = $this->safe_value($response['result'], $id);
        if ($result === null) {
            throw new OrderNotFound($this->id . ' order $id ' . $id . ' not found');
        }
        return $this->parse_order($result);
    }

    public function fetch_order_status($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $orders = $this->fetch_open_orders($symbol, null, null, $params);
        $indexed = $this->index_by($orders, 'id');
        return (is_array($indexed) && array_key_exists($id, $indexed)) ? 'open' : 'closed';
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderNumber' => $id,
        );
        $trades = $this->privatePostReturnOrderTrades (array_merge($request, $params));
        return $this->parse_trades($trades);
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        // USDT, USDTETH, USDTTRON
        $currencyId = null;
        $currency = null;
        if (is_array($this->currencies) && array_key_exists($code, $this->currencies)) {
            $currency = $this->currency($code);
            $currencyId = $currency['id'];
        } else {
            $currencyId = $code;
        }
        $request = array(
            'currency' => $currencyId,
        );
        $response = $this->privatePostGenerateNewAddress (array_merge($request, $params));
        $address = null;
        $tag = null;
        if ($response['success'] === 1) {
            $address = $this->safe_string($response, 'response');
        }
        $this->check_address($address);
        if ($currency !== null) {
            $depositAddress = $this->safe_string($currency['info'], 'depositAddress');
            if ($depositAddress !== null) {
                $tag = $address;
                $address = $depositAddress;
            }
        }
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostReturnDepositAddresses ($params);
        // USDT, USDTETH, USDTTRON
        $currencyId = null;
        $currency = null;
        if (is_array($this->currencies) && array_key_exists($code, $this->currencies)) {
            $currency = $this->currency($code);
            $currencyId = $currency['id'];
        } else {
            $currencyId = $code;
        }
        $address = $this->safe_string($response, $currencyId);
        $tag = null;
        $this->check_address($address);
        if ($currency !== null) {
            $depositAddress = $this->safe_string($currency['info'], 'depositAddress');
            if ($depositAddress !== null) {
                $tag = $address;
                $address = $depositAddress;
            }
        }
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
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address,
        );
        if ($tag !== null) {
            $request['paymentId'] = $tag;
        }
        $response = $this->privatePostWithdraw (array_merge($request, $params));
        //
        //     {
        //         $response => 'Withdrew 1.00000000 USDT.',
        //         email2FA => false,
        //         withdrawalNumber => 13449869
        //     }
        //
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'withdrawalNumber'),
        );
    }

    public function fetch_transactions_helper($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $year = 31104000; // 60 * 60 * 24 * 30 * 12 = one $year of history, why not
        $now = $this->seconds();
        $start = ($since !== null) ? intval($since / 1000) : $now - 10 * $year;
        $request = array(
            'start' => $start, // UNIX timestamp, required
            'end' => $now, // UNIX timestamp, required
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privatePostReturnDepositsWithdrawals (array_merge($request, $params));
        //
        //     {
        //         "adjustments":array(),
        //         "deposits":array(
        //             array(
        //                 currency => "BTC",
        //                 address => "1MEtiqJWru53FhhHrfJPPvd2tC3TPDVcmW",
        //                 amount => "0.01063000",
        //                 confirmations =>  1,
        //                 txid => "952b0e1888d6d491591facc0d37b5ebec540ac1efb241fdbc22bcc20d1822fb6",
        //                 timestamp =>  1507916888,
        //                 status => "COMPLETE"
        //             ),
        //             {
        //                 currency => "ETH",
        //                 address => "0x20108ba20b65c04d82909e91df06618107460197",
        //                 amount => "4.00000000",
        //                 confirmations => 38,
        //                 txid => "0x4be260073491fe63935e9e0da42bd71138fdeb803732f41501015a2d46eb479d",
        //                 timestamp => 1525060430,
        //                 status => "COMPLETE"
        //             }
        //         ),
        //         "withdrawals":array(
        //             array(
        //                 "withdrawalNumber":13449869,
        //                 "currency":"USDTTRON", // not documented in API docs, see commonCurrencies in describe()
        //                 "address":"TXGaqPW23JdRWhsVwS2mRsGsegbdnAd3Rw",
        //                 "amount":"1.00000000",
        //                 "fee":"0.00000000",
        //                 "timestamp":1591573420,
        //                 "status":"COMPLETE => dadf427224b3d44b38a2c13caa4395e4666152556ca0b2f67dbd86a95655150f",
        //                 "ipAddress":"74.116.3.247",
        //                 "canCancel":0,
        //                 "canResendEmail":0,
        //                 "paymentID":null,
        //                 "scope":"crypto"
        //             ),
        //             array(
        //                 withdrawalNumber => 8224394,
        //                 currency => "EMC2",
        //                 address => "EYEKyCrqTNmVCpdDV8w49XvSKRP9N3EUyF",
        //                 amount => "63.10796020",
        //                 fee => "0.01000000",
        //                 timestamp => 1510819838,
        //                 status => "COMPLETE => d37354f9d02cb24d98c8c4fc17aa42f475530b5727effdf668ee5a43ce667fd6",
        //                 ipAddress => "5.220.220.200"
        //             ),
        //             array(
        //                 withdrawalNumber => 9290444,
        //                 currency => "ETH",
        //                 address => "0x191015ff2e75261d50433fbd05bd57e942336149",
        //                 amount => "0.15500000",
        //                 fee => "0.00500000",
        //                 timestamp => 1514099289,
        //                 status => "COMPLETE => 0x12d444493b4bca668992021fd9e54b5292b8e71d9927af1f076f554e4bea5b2d",
        //                 ipAddress => "5.228.227.214"
        //             ),
        //             {
        //                 withdrawalNumber => 11518260,
        //                 currency => "BTC",
        //                 address => "8JoDXAmE1GY2LRK8jD1gmAmgRPq54kXJ4t",
        //                 amount => "0.20000000",
        //                 fee => "0.00050000",
        //                 timestamp => 1527918155,
        //                 status => "COMPLETE => 1864f4ebb277d90b0b1ff53259b36b97fa1990edc7ad2be47c5e0ab41916b5ff",
        //                 ipAddress => "211.8.195.26"
        //             }
        //         )
        //     }
        //
        return $response;
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->fetch_transactions_helper($code, $since, $limit, $params);
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $withdrawals = $this->safe_value($response, 'withdrawals', array());
        $deposits = $this->safe_value($response, 'deposits', array());
        $withdrawalTransactions = $this->parse_transactions($withdrawals, $currency, $since, $limit);
        $depositTransactions = $this->parse_transactions($deposits, $currency, $since, $limit);
        $transactions = $this->array_concat($depositTransactions, $withdrawalTransactions);
        return $this->filter_by_currency_since_limit($this->sort_by($transactions, 'timestamp'), $code, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->fetch_transactions_helper($code, $since, $limit, $params);
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $withdrawals = $this->safe_value($response, 'withdrawals', array());
        $transactions = $this->parse_transactions($withdrawals, $currency, $since, $limit);
        return $this->filter_by_currency_since_limit($transactions, $code, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->fetch_transactions_helper($code, $since, $limit, $params);
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $deposits = $this->safe_value($response, 'deposits', array());
        $transactions = $this->parse_transactions($deposits, $currency, $since, $limit);
        return $this->filter_by_currency_since_limit($transactions, $code, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'COMPLETE' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // deposits
        //
        //     {
        //         "$txid" => "f49d489616911db44b740612d19464521179c76ebe9021af85b6de1e2f8d68cd",
        //         "$type" => "deposit",
        //         "$amount" => "49798.01987021",
        //         "$status" => "COMPLETE",
        //         "$address" => "DJVJZ58tJC8UeUv9Tqcdtn6uhWobouxFLT",
        //         "$currency" => "DOGE",
        //         "$timestamp" => 1524321838,
        //         "confirmations" => 3371,
        //         "depositNumber" => 134587098
        //     }
        //
        // withdrawals
        //
        //     {
        //         "fee" => "0.00050000",
        //         "$type" => "withdrawal",
        //         "$amount" => "0.40234387",
        //         "$status" => "COMPLETE => fbabb2bf7d81c076f396f3441166d5f60f6cea5fdfe69e02adcc3b27af8c2746",
        //         "$address" => "1EdAqY4cqHoJGAgNfUFER7yZpg1Jc9DUa3",
        //         "$currency" => "BTC",
        //         "canCancel" => 0,
        //         "ipAddress" => "185.230.101.31",
        //         "paymentID" => null,
        //         "$timestamp" => 1523834337,
        //         "canResendEmail" => 0,
        //         "withdrawalNumber" => 11162900
        //     }
        //
        $timestamp = $this->safe_timestamp($transaction, 'timestamp');
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId);
        $status = $this->safe_string($transaction, 'status', 'pending');
        $txid = $this->safe_string($transaction, 'txid');
        if ($status !== null) {
            $parts = explode(' => ', $status);
            $numParts = is_array($parts) ? count($parts) : 0;
            $status = $parts[0];
            if (($numParts > 1) && ($txid === null)) {
                $txid = $parts[1];
            }
            $status = $this->parse_transaction_status($status);
        }
        $defaultType = (is_array($transaction) && array_key_exists('withdrawalNumber', $transaction)) ? 'withdrawal' : 'deposit';
        $type = $this->safe_string($transaction, 'type', $defaultType);
        $id = $this->safe_string_2($transaction, 'withdrawalNumber', 'depositNumber');
        $amount = $this->safe_float($transaction, 'amount');
        $address = $this->safe_string($transaction, 'address');
        $tag = $this->safe_string($transaction, 'paymentID');
        // according to https://poloniex.com/fees/
        $feeCost = $this->safe_float($transaction, 'fee', 0);
        if ($type === 'withdrawal') {
            // poloniex withdrawal $amount includes the fee
            $amount = $amount - $feeCost;
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => $tag,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => array(
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function fetch_position($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currencyPair' => $market['id'],
        );
        $response = $this->privatePostGetMarginPosition (array_merge($request, $params));
        //
        //     {
        //         type => "none",
        //         amount => "0.00000000",
        //         total => "0.00000000",
        //         basePrice => "0.00000000",
        //         liquidationPrice => -1,
        //         pl => "0.00000000",
        //         lendingFees => "0.00000000"
        //     }
        //
        // todo unify parsePosition/parsePositions
        return $response;
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        $query = array_merge(array( 'command' => $path ), $params);
        if ($api === 'public') {
            $url .= '?' . $this->urlencode($query);
        } else {
            $this->check_required_credentials();
            $query['nonce'] = $this->nonce();
            $body = $this->urlencode($query);
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        // array("error":"Permission denied.")
        if (is_array($response) && array_key_exists('error', $response)) {
            $message = $response['error'];
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

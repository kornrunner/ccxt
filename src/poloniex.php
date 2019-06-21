<?php

namespace ccxt;

use Exception as Exception; // a common import

class poloniex extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'poloniex',
            'name' => 'Poloniex',
            'countries' => array ( 'US' ),
            'rateLimit' => 1000, // up to 6 calls per second
            'certified' => true, // 2019-06-07
            'has' => array (
                'CORS' => false,
                'createDepositAddress' => true,
                'createMarketOrder' => false,
                'editOrder' => true,
                'fetchClosedOrders' => 'emulated',
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrder' => true, // true endpoint for a single open order
                'fetchOpenOrders' => true, // true endpoint for open orders
                'fetchOrder' => 'emulated', // no endpoint for a single open-or-closed order (just for an open order only)
                'fetchOrderBooks' => true,
                'fetchOrders' => 'emulated', // no endpoint for open-or-closed orders (just for open orders only)
                'fetchOrderStatus' => 'emulated', // no endpoint for status of a single open-or-closed order (just for open orders only)
                'fetchOrderTrades' => true, // true endpoint for trades of a single open or closed order
                'fetchTickers' => true,
                'fetchTradingFee' => true,
                'fetchTradingFees' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => true,
                'cancelAllOrders' => true,
                'withdraw' => true,
            ),
            'timeframes' => array (
                '5m' => 300,
                '15m' => 900,
                '30m' => 1800,
                '2h' => 7200,
                '4h' => 14400,
                '1d' => 86400,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766817-e9456312-5ee6-11e7-9b3c-b628ca5626a5.jpg',
                'api' => array (
                    'public' => 'https://poloniex.com/public',
                    'private' => 'https://poloniex.com/tradingApi',
                ),
                'www' => 'https://www.poloniex.com',
                'doc' => 'https://docs.poloniex.com',
                'fees' => 'https://poloniex.com/fees',
                'referral' => 'https://www.poloniex.com/?utm_source=ccxt&utm_medium=web',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'return24hVolume',
                        'returnChartData',
                        'returnCurrencies',
                        'returnLoanOrders',
                        'returnOrderBook',
                        'returnTicker',
                        'returnTradeHistory',
                    ),
                ),
                'private' => array (
                    'post' => array (
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
            // Fees are tier-based. More info => https://poloniex.com/fees/
            // Rates below are highest possible.
            'fees' => array (
                'trading' => array (
                    'maker' => 0.001,
                    'taker' => 0.002,
                ),
                'funding' => array(),
            ),
            'limits' => array (
                'amount' => array (
                    'min' => 0.000001,
                    'max' => 1000000000,
                ),
                'price' => array (
                    'min' => 0.00000001,
                    'max' => 1000000000,
                ),
                'cost' => array (
                    'min' => 0.00000000,
                    'max' => 1000000000,
                ),
            ),
            'precision' => array (
                'amount' => 8,
                'price' => 8,
            ),
            'commonCurrencies' => array (
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
                'PLX' => 'ParallaxCoin',
                'KEY' => 'KEYCoin',
                'STR' => 'XLM',
                'SOC' => 'SOCC',
                'XAP' => 'API Coin',
            ),
            'options' => array (
                'limits' => array (
                    'cost' => array (
                        'min' => array (
                            'BTC' => 0.0001,
                            'ETH' => 0.0001,
                            'XMR' => 0.0001,
                            'USDT' => 1.0,
                        ),
                    ),
                ),
            ),
            'exceptions' => array (
                'exact' => array (
                    'You may only place orders that reduce your position.' => '\\ccxt\\InvalidOrder',
                    'Invalid order number, or you are not the person who placed the order.' => '\\ccxt\\OrderNotFound',
                    'Permission denied' => '\\ccxt\\PermissionDenied',
                    'Connection timed out. Please try again.' => '\\ccxt\\RequestTimeout',
                    'Internal error. Please try again.' => '\\ccxt\\ExchangeNotAvailable',
                    'Order not found, or you are not the person who placed it.' => '\\ccxt\\OrderNotFound',
                    'Invalid API key/secret pair.' => '\\ccxt\\AuthenticationError',
                    'Please do not make more than 8 API calls per second.' => '\\ccxt\\DDoSProtection',
                    'Rate must be greater than zero.' => '\\ccxt\\InvalidOrder', // array("error":"Rate must be greater than zero.")
                ),
                'broad' => array (
                    'Total must be at least' => '\\ccxt\\InvalidOrder', // array("error":"Total must be at least 0.0001.")
                    'This account is frozen.' => '\\ccxt\\AccountSuspended',
                    'Not enough' => '\\ccxt\\InsufficientFunds',
                    'Nonce must be greater' => '\\ccxt\\InvalidNonce',
                    'You have already called cancelOrder or moveOrder on this order.' => '\\ccxt\\CancelPending',
                    'Amount must be at least' => '\\ccxt\\InvalidOrder', // array("error":"Amount must be at least 0.000001.")
                    'is either completed or does not exist' => '\\ccxt\\InvalidOrder', // array("error":"Order 587957810791 is either completed or does not exist.")
                ),
            ),
        ));
    }

    public function calculate_fee ($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $key = 'quote';
        $rate = $market[$takerOrMaker];
        $cost = floatval ($this->cost_to_precision($symbol, $amount * $rate));
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
        }
        return array (
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval ($this->fee_to_precision($symbol, $cost)),
        );
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '5m', $since = null, $limit = null) {
        return [
            $ohlcv['date'] * 1000,
            $ohlcv['open'],
            $ohlcv['high'],
            $ohlcv['low'],
            $ohlcv['close'],
            $ohlcv['quoteVolume'],
        ];
    }

    public function fetch_ohlcv ($symbol, $timeframe = '5m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        if ($since === null) {
            $since = 0;
        }
        $request = array (
            'currencyPair' => $market['id'],
            'period' => $this->timeframes[$timeframe],
            'start' => intval ($since / 1000),
        );
        if ($limit !== null) {
            $request['end'] = $this->sum ($request['start'], $limit * $this->timeframes[$timeframe]);
        } else {
            $request['end'] = $this->sum ($this->seconds (), 1);
        }
        $response = $this->publicGetReturnChartData (array_merge ($request, $params));
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_markets ($params = array ()) {
        $markets = $this->publicGetReturnTicker ();
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count ($keys); $i++) {
            $id = $keys[$i];
            $market = $markets[$id];
            list($quoteId, $baseId) = explode('_', $id);
            $base = $this->common_currency_code($baseId);
            $quote = $this->common_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $limits = array_merge ($this->limits, array (
                'cost' => array (
                    'min' => $this->safe_value($this->options['limits']['cost']['min'], $quote),
                ),
            ));
            $isFrozen = $this->safe_string($market, 'isFrozen');
            $active = ($isFrozen !== '1');
            $result[] = array_merge ($this->fees['trading'], array (
                'id' => $id,
                'symbol' => $symbol,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'base' => $base,
                'quote' => $quote,
                'active' => $active,
                'limits' => $limits,
                'info' => $market,
            ));
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $request = array (
            'account' => 'all',
        );
        $response = $this->privatePostReturnCompleteBalances (array_merge ($request, $params));
        $result = array( 'info' => $response );
        $currencyIds = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count ($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $balance = $this->safe_value($response, $currencyId, array());
            $code = $currencyId;
            if (is_array($this->currencies_by_id) && array_key_exists($currencyId, $this->currencies_by_id)) {
                $code = $this->currencies_by_id[$currencyId]['code'];
            } else {
                $code = $this->common_currency_code($currencyId);
            }
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['used'] = $this->safe_float($balance, 'onOrders');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_trading_fees ($params = array ()) {
        $this->load_markets();
        $fees = $this->privatePostReturnFeeInfo ($params);
        return array (
            'info' => $fees,
            'maker' => $this->safe_float($fees, 'makerFee'),
            'taker' => $this->safe_float($fees, 'takerFee'),
            'withdraw' => array(),
            'deposit' => array(),
        );
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'currencyPair' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['depth'] = $limit; // 100
        }
        $response = $this->publicGetReturnOrderBook (array_merge ($request, $params));
        $orderbook = $this->parse_order_book($response);
        $orderbook['nonce'] = $this->safe_integer($response, 'seq');
        return $orderbook;
    }

    public function fetch_order_books ($symbols = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'currencyPair' => 'all',
        );
        //
        //     if (limit !== null) {
        //         $request['depth'] = limit; // 100
        //     }
        //
        $response = $this->publicGetReturnOrderBook (array_merge ($request, $params));
        $marketIds = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count ($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $symbol = null;
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $symbol = $this->markets_by_id[$marketId]['symbol'];
            } else {
                list($quoteId, $baseId) = explode('_', $marketId);
                $base = $this->common_currency_code($baseId);
                $quote = $this->common_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
            $orderbook = $this->parse_order_book($response[$marketId]);
            $orderbook['nonce'] = $this->safe_integer($response[$marketId], 'seq');
            $result[$symbol] = $orderbook;
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->milliseconds ();
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
            $open = $last / $this->sum (1, $relativeChange);
            $change = $last - $open;
            $average = $this->sum ($last, $open) / 2;
        }
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
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

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetReturnTicker ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $symbol = null;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                list($quoteId, $baseId) = explode('_', $id);
                $base = $this->common_currency_code($baseId);
                $quote = $this->common_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
                $market = array( 'symbol' => $symbol );
            }
            $ticker = $response[$id];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $result;
    }

    public function fetch_currencies ($params = array ()) {
        $response = $this->publicGetReturnCurrencies ($params);
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $currency = $response[$id];
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/deposit methods and
            // differentiated fees for each particular method
            $precision = 8; // default $precision, todo => fix "magic constants"
            $code = $this->common_currency_code($id);
            $active = ($currency['delisted'] === 0) && !$currency['disabled'];
            $result[$code] = array (
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $currency['name'],
                'active' => $active,
                'fee' => $this->safe_float($currency, 'txFee'), // todo => redesign
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'price' => array (
                        'min' => pow(10, -$precision),
                        'max' => pow(10, $precision),
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array (
                        'min' => $currency['txFee'],
                        'max' => pow(10, $precision),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetReturnTicker ($params);
        $ticker = $response[$market['id']];
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade ($trade, $market = null) {
        //
        // fetchMyTrades ($symbol defined, specific $market)
        //
        //     {
        //         globalTradeID => 394698946,
        //         tradeID => 45210255,
        //         date => '2018-10-23 17:28:55',
        //         type => 'sell',
        //         $rate => '0.03114126',
        //         $amount => '0.00018753',
        //         total => '0.00000583'
        //     }
        //
        // fetchMyTrades ($symbol null, all markets)
        //
        //     {
        //         globalTradeID => 394131412,
        //         tradeID => '5455033',
        //         date => '2018-10-16 18:05:17',
        //         $rate => '0.06935244',
        //         $amount => '1.40308443',
        //         total => '0.09730732',
        //         $fee => '0.00100000',
        //         orderNumber => '104768235081',
        //         type => 'sell',
        //         category => 'exchange'
        //     }
        //
        $id = $this->safe_string($trade, 'globalTradeID');
        $orderId = $this->safe_string($trade, 'orderNumber');
        $timestamp = $this->parse8601 ($this->safe_string($trade, 'date'));
        $symbol = null;
        $base = null;
        $quote = null;
        if ((!$market) && (is_array($trade) && array_key_exists('currencyPair', $trade))) {
            $currencyPair = $trade['currencyPair'];
            if (is_array($this->markets_by_id) && array_key_exists($currencyPair, $this->markets_by_id)) {
                $market = $this->markets_by_id[$currencyPair];
            } else {
                $parts = explode('_', $currencyPair);
                $quote = $parts[0];
                $base = $parts[1];
                $symbol = $base . '/' . $quote;
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
            $base = $market['base'];
            $quote = $market['quote'];
        }
        $side = $this->safe_string($trade, 'type');
        $fee = null;
        $price = $this->safe_float($trade, 'rate');
        $cost = $this->safe_float($trade, 'total');
        $amount = $this->safe_float($trade, 'amount');
        if (is_array($trade) && array_key_exists('fee', $trade)) {
            $rate = $this->safe_float($trade, 'fee');
            $feeCost = null;
            $currency = null;
            if ($side === 'buy') {
                $currency = $base;
                $feeCost = $amount * $rate;
            } else {
                $currency = $quote;
                if ($cost !== null) {
                    $feeCost = $cost * $rate;
                }
            }
            $fee = array (
                'type' => null,
                'rate' => $rate,
                'cost' => $feeCost,
                'currency' => $currency,
            );
        }
        return array (
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => 'limit',
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'currencyPair' => $market['id'],
        );
        if ($since !== null) {
            $request['start'] = intval ($since / 1000);
            $request['end'] = $this->seconds (); // last 50000 $trades by default
        }
        $trades = $this->publicGetReturnTradeHistory (array_merge ($request, $params));
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
        }
        $pair = $market ? $market['id'] : 'all';
        $request = array( 'currencyPair' => $pair );
        if ($since !== null) {
            $request['start'] = intval ($since / 1000);
            $request['end'] = $this->seconds () . 1; // adding 1 is a fix for #3411
        }
        // $limit is disabled (does not really work as expected)
        if ($limit !== null) {
            $request['limit'] = intval ($limit);
        }
        $response = $this->privatePostReturnTradeHistory (array_merge ($request, $params));
        //
        // specific $market ($symbol defined)
        //
        //     array (
        //         array (
        //             globalTradeID => 394700861,
        //             tradeID => 45210354,
        //             date => '2018-10-23 18:01:58',
        //             type => 'buy',
        //             rate => '0.03117266',
        //             amount => '0.00000652',
        //             total => '0.00000020'
        //         ),
        //         {
        //             globalTradeID => 394698946,
        //             tradeID => 45210255,
        //             date => '2018-10-23 17:28:55',
        //             type => 'sell',
        //             rate => '0.03114126',
        //             amount => '0.00018753',
        //             total => '0.00000583'
        //         }
        //     )
        //
        // all markets ($symbol null)
        //
        //     {
        //         BTC_BCH => [array (
        //             globalTradeID => 394131412,
        //             tradeID => '5455033',
        //             date => '2018-10-16 18:05:17',
        //             rate => '0.06935244',
        //             amount => '1.40308443',
        //             total => '0.09730732',
        //             fee => '0.00100000',
        //             orderNumber => '104768235081',
        //             type => 'sell',
        //             category => 'exchange'
        //         ), array (
        //             globalTradeID => 394126818,
        //             tradeID => '5455007',
        //             date => '2018-10-16 16:55:34',
        //             rate => '0.06935244',
        //             amount => '0.00155709',
        //             total => '0.00010798',
        //             fee => '0.00200000',
        //             orderNumber => '104768179137',
        //             type => 'sell',
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
                for ($i = 0; $i < count ($ids); $i++) {
                    $id = $ids[$i];
                    $market = null;
                    if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                        $market = $this->markets_by_id[$id];
                        $trades = $this->parse_trades($response[$id], $market);
                        for ($j = 0; $j < count ($trades); $j++) {
                            $result[] = $trades[$j];
                        }
                    } else {
                        list($quoteId, $baseId) = explode('_', $id);
                        $base = $this->common_currency_code($baseId);
                        $quote = $this->common_currency_code($quoteId);
                        $symbol = $base . '/' . $quote;
                        $trades = $response[$id];
                        for ($j = 0; $j < count ($trades); $j++) {
                            $result[] = array_merge ($this->parse_trade($trades[$j]), array (
                                'symbol' => $symbol,
                            ));
                        }
                    }
                }
            }
        }
        return $this->filter_by_since_limit($result, $since, $limit);
    }

    public function parse_order_status ($status) {
        $statuses = array (
            'Open' => 'open',
            'Partially filled' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order ($order, $market = null) {
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
        $timestamp = $this->safe_integer($order, 'timestamp');
        if (!$timestamp) {
            $timestamp = $this->parse8601 ($order['date']);
        }
        $trades = null;
        if (is_array($order) && array_key_exists('resultingTrades', $order)) {
            $trades = $this->parse_trades($order['resultingTrades'], $market);
        }
        $symbol = null;
        $marketId = $this->safe_string($order, 'currencyPair');
        $market = $this->safe_value($this->markets_by_id, $marketId, $market);
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $price = $this->safe_float_2($order, 'price', 'rate');
        $remaining = $this->safe_float($order, 'amount');
        $amount = $this->safe_float($order, 'startingAmount', $remaining);
        $filled = null;
        $cost = 0;
        if ($amount !== null) {
            if ($remaining !== null) {
                $filled = $amount - $remaining;
                if ($price !== null) {
                    $cost = $filled * $price;
                }
            }
        }
        if ($filled === null) {
            if ($trades !== null) {
                $filled = 0;
                $cost = 0;
                for ($i = 0; $i < count ($trades); $i++) {
                    $trade = $trades[$i];
                    $tradeAmount = $trade['amount'];
                    $tradePrice = $trade['price'];
                    $filled = $this->sum ($filled, $tradeAmount);
                    $cost .= $tradePrice * $tradeAmount;
                }
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side', $type);
        if ($type === $side) {
            $type = null;
        }
        $id = $this->safe_string($order, 'orderNumber');
        return array (
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => $trades,
            'fee' => null,
        );
    }

    public function parse_open_orders ($orders, $market, $result) {
        for ($i = 0; $i < count ($orders); $i++) {
            $order = $orders[$i];
            $extended = array_merge ($order, array (
                'status' => 'open',
                'type' => 'limit',
                'side' => $order['type'],
                'price' => $order['rate'],
            ));
            $result[] = $this->parse_order($extended, $market);
        }
        return $result;
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
        }
        $pair = $market ? $market['id'] : 'all';
        $request = array (
            'currencyPair' => $pair,
        );
        $response = $this->privatePostReturnOpenOrders (array_merge ($request, $params));
        $openOrders = array();
        if ($market !== null) {
            $openOrders = $this->parse_open_orders ($response, $market, $openOrders);
        } else {
            $marketIds = is_array($response) ? array_keys($response) : array();
            for ($i = 0; $i < count ($marketIds); $i++) {
                $marketId = $marketIds[$i];
                $orders = $response[$marketId];
                $m = $this->markets_by_id[$marketId];
                $openOrders = $this->parse_open_orders ($orders, $m, $openOrders);
            }
        }
        for ($j = 0; $j < count ($openOrders); $j++) {
            $this->orders[$openOrders[$j]['id']] = $openOrders[$j];
        }
        $openOrdersIndexedById = $this->index_by($openOrders, 'id');
        $cachedOrderIds = is_array($this->orders) ? array_keys($this->orders) : array();
        $result = array();
        for ($k = 0; $k < count ($cachedOrderIds); $k++) {
            $id = $cachedOrderIds[$k];
            if (is_array($openOrdersIndexedById) && array_key_exists($id, $openOrdersIndexedById)) {
                $this->orders[$id] = array_merge ($this->orders[$id], $openOrdersIndexedById[$id]);
            } else {
                $order = $this->orders[$id];
                if ($order['status'] === 'open') {
                    $order = array_merge ($order, array (
                        'status' => 'closed',
                        'cost' => null,
                        'filled' => $order['amount'],
                        'remaining' => 0.0,
                    ));
                    if ($order['cost'] === null) {
                        if ($order['filled'] !== null) {
                            $order['cost'] = $order['filled'] * $order['price'];
                        }
                    }
                    $this->orders[$id] = $order;
                }
            }
            $order = $this->orders[$id];
            if ($market !== null) {
                if ($order['symbol'] === $symbol) {
                    $result[] = $order;
                }
            } else {
                $result[] = $order;
            }
        }
        return $this->filter_by_since_limit($result, $since, $limit);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $since = $this->safe_value($params, 'since');
        $limit = $this->safe_value($params, 'limit');
        $request = $this->omit ($params, array ( 'since', 'limit' ));
        $orders = $this->fetch_orders($symbol, $since, $limit, $request);
        for ($i = 0; $i < count ($orders); $i++) {
            if ($orders[$i]['id'] === $id) {
                return $orders[$i];
            }
        }
        throw new OrderNotCached($this->id . ' order $id ' . (string) $id . ' is not in "open" state and not found in cache');
    }

    public function filter_orders_by_status ($orders, $status) {
        $result = array();
        for ($i = 0; $i < count ($orders); $i++) {
            if ($orders[$i]['status'] === $status) {
                $result[] = $orders[$i];
            }
        }
        return $result;
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        return $this->filter_orders_by_status ($orders, 'open');
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        return $this->filter_orders_by_status ($orders, 'closed');
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $method = 'privatePost' . $this->capitalize ($side);
        $market = $this->market ($symbol);
        $request = array (
            'currencyPair' => $market['id'],
            'rate' => $this->price_to_precision($symbol, $price),
            'amount' => $this->amount_to_precision($symbol, $amount),
        );
        $response = $this->$method (array_merge ($request, $params));
        $timestamp = $this->milliseconds ();
        $order = $this->parse_order(array_merge (array (
            'timestamp' => $timestamp,
            'status' => 'open',
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
        ), $response), $market);
        $id = $order['id'];
        $this->orders[$id] = $order;
        return array_merge (array( 'info' => $response ), $order);
    }

    public function edit_order ($id, $symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $price = floatval ($price);
        $request = array (
            'orderNumber' => $id,
            'rate' => $this->price_to_precision($symbol, $price),
        );
        if ($amount !== null) {
            $request['amount'] = $this->amount_to_precision($symbol, $amount);
        }
        $response = $this->privatePostMoveOrder (array_merge ($request, $params));
        $result = null;
        if (is_array($this->orders) && array_key_exists($id, $this->orders)) {
            $this->orders[$id]['status'] = 'canceled';
            $newid = $response['orderNumber'];
            $this->orders[$newid] = array_merge ($this->orders[$id], array (
                'id' => $newid,
                'price' => $price,
                'status' => 'open',
            ));
            if ($amount !== null) {
                $this->orders[$newid]['amount'] = $amount;
            }
            $result = array_merge ($this->orders[$newid], array( 'info' => $response ));
        } else {
            $market = null;
            if ($symbol !== null) {
                $market = $this->market ($symbol);
            }
            $result = $this->parse_order($response, $market);
            $this->orders[$result['id']] = $result;
        }
        return $result;
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = null;
        try {
            $response = $this->privatePostCancelOrder (array_merge (array (
                'orderNumber' => $id,
            ), $params));
        } catch (Exception $e) {
            if ($e instanceof CancelPending) {
                // A request to cancel the order has been sent already.
                // If we then attempt to cancel the order the second time
                // before the first request is processed the exchange will
                // throw a CancelPending exception. Poloniex won't show the
                // order in the list of active (open) orders and the cached
                // order will be marked as 'closed' (see #1801 for details).
                // To avoid that we proactively mark the order as 'canceled'
                // here. If for some reason the order does not get canceled
                // and still appears in the active list then the order cache
                // will eventually get back in sync on a call to `fetchOrder`.
                if (is_array($this->orders) && array_key_exists($id, $this->orders)) {
                    $this->orders[$id]['status'] = 'canceled';
                }
            }
            throw $e;
        }
        if (is_array($this->orders) && array_key_exists($id, $this->orders)) {
            $this->orders[$id]['status'] = 'canceled';
        }
        return $response;
    }

    public function cancel_all_orders ($symbol = null, $params = array ()) {
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['currencyPair'] = $market['id'];
        }
        $response = $this->privatePostCancelAllOrders (array_merge ($request, $params));
        //
        //     {
        //         "success" => 1,
        //         "message" => "Orders canceled",
        //         "orderNumbers" => array (
        //             503749,
        //             888321,
        //             7315825,
        //             7316824
        //         )
        //     }
        //
        $orderIds = $this->safe_value($response, 'orderNumbers', array());
        for ($i = 0; $i < count ($orderIds); $i++) {
            $id = (string) $orderIds[$i];
            if (is_array($this->orders) && array_key_exists($id, $this->orders)) {
                $this->orders[$id]['status'] = 'canceled';
            }
        }
        return $response;
    }

    public function fetch_open_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $id = (string) $id;
        $response = $this->privatePostReturnOrderStatus (array_merge (array (
            'orderNumber' => $id,
        ), $params));
        //
        //     {
        //         success => 1,
        //         $result => array (
        //             '6071071' => array (
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
            throw new OrderNotFound($this->id . ' $order $id ' . $id . ' not found');
        }
        $order = $this->parse_order($result);
        $order['id'] = $id;
        $this->orders[$id] = $order;
        return $order;
    }

    public function fetch_order_status ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $orders = $this->fetch_open_orders($symbol, null, null, $params);
        $indexed = $this->index_by($orders, 'id');
        return (is_array($indexed) && array_key_exists($id, $indexed)) ? 'open' : 'closed';
    }

    public function fetch_order_trades ($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'orderNumber' => $id,
        );
        $trades = $this->privatePostReturnOrderTrades (array_merge ($request, $params));
        return $this->parse_trades($trades);
    }

    public function create_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privatePostGenerateNewAddress (array_merge ($request, $params));
        $address = null;
        $tag = null;
        if ($response['success'] === 1) {
            $address = $this->safe_string($response, 'response');
        }
        $this->check_address($address);
        $depositAddress = $this->safe_string($currency['info'], 'depositAddress');
        if ($depositAddress !== null) {
            $tag = $address;
            $address = $depositAddress;
        }
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $response = $this->privatePostReturnDepositAddresses ($params);
        $currencyId = $currency['id'];
        $address = $this->safe_string($response, $currencyId);
        $tag = null;
        $this->check_address($address);
        $depositAddress = $this->safe_string($currency['info'], 'depositAddress');
        if ($depositAddress !== null) {
            $tag = $address;
            $address = $depositAddress;
        }
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
            'amount' => $amount,
            'address' => $address,
        );
        if ($tag) {
            $request['paymentId'] = $tag;
        }
        $response = $this->privatePostWithdraw (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $response['response'],
        );
    }

    public function fetch_transactions_helper ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $year = 31104000; // 60 * 60 * 24 * 30 * 12 = one $year of history, why not
        $now = $this->seconds ();
        $start = ($since !== null) ? intval ($since / 1000) : $now - 10 * $year;
        $request = array (
            'start' => $start, // UNIX timestamp, required
            'end' => $now, // UNIX timestamp, required
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privatePostReturnDepositsWithdrawals (array_merge ($request, $params));
        //
        //     {    deposits => array ( array (      currency => "BTC",
        //                              address => "1MEtiqJWru53FhhHrfJPPvd2tC3TPDVcmW",
        //                               amount => "0.01063000",
        //                        confirmations =>  1,
        //                                 txid => "952b0e1888d6d491591facc0d37b5ebec540ac1efb241fdbc22bcc20d1822fb6",
        //                            timestamp =>  1507916888,
        //                               status => "COMPLETE"                                                          ),
        //                      {      currency => "ETH",
        //                              address => "0x20108ba20b65c04d82909e91df06618107460197",
        //                               amount => "4.00000000",
        //                        confirmations =>  38,
        //                                 txid => "0x4be260073491fe63935e9e0da42bd71138fdeb803732f41501015a2d46eb479d",
        //                            timestamp =>  1525060430,
        //                               status => "COMPLETE"                                                            }  ),
        //       withdrawals => array ( array ( withdrawalNumber =>  8224394,
        //                                currency => "EMC2",
        //                                 address => "EYEKyCrqTNmVCpdDV8w49XvSKRP9N3EUyF",
        //                                  amount => "63.10796020",
        //                                     fee => "0.01000000",
        //                               timestamp =>  1510819838,
        //                                  status => "COMPLETE => d37354f9d02cb24d98c8c4fc17aa42f475530b5727effdf668ee5a43ce667fd6",
        //                               ipAddress => "5.220.220.200"                                                               ),
        //                      array ( withdrawalNumber =>  9290444,
        //                                currency => "ETH",
        //                                 address => "0x191015ff2e75261d50433fbd05bd57e942336149",
        //                                  amount => "0.15500000",
        //                                     fee => "0.00500000",
        //                               timestamp =>  1514099289,
        //                                  status => "COMPLETE => 0x12d444493b4bca668992021fd9e54b5292b8e71d9927af1f076f554e4bea5b2d",
        //                               ipAddress => "5.228.227.214"                                                                 ),
        //                      { withdrawalNumber =>  11518260,
        //                                currency => "BTC",
        //                                 address => "8JoDXAmE1GY2LRK8jD1gmAmgRPq54kXJ4t",
        //                                  amount => "0.20000000",
        //                                     fee => "0.00050000",
        //                               timestamp =>  1527918155,
        //                                  status => "COMPLETE => 1864f4ebb277d90b0b1ff53259b36b97fa1990edc7ad2be47c5e0ab41916b5ff",
        //                               ipAddress => "211.8.195.26"                                                                }    ) }
        //
        return $response;
    }

    public function fetch_transactions ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->fetch_transactions_helper ($code, $since, $limit, $params);
        for ($i = 0; $i < count ($response['deposits']); $i++) {
            $response['deposits'][$i]['type'] = 'deposit';
        }
        for ($i = 0; $i < count ($response['withdrawals']); $i++) {
            $response['withdrawals'][$i]['type'] = 'withdrawal';
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
        }
        $withdrawals = $this->parseTransactions ($response['withdrawals'], $currency, $since, $limit);
        $deposits = $this->parseTransactions ($response['deposits'], $currency, $since, $limit);
        $transactions = $this->array_concat($deposits, $withdrawals);
        return $this->filterByCurrencySinceLimit ($this->sort_by($transactions, 'timestamp'), $code, $since, $limit);
    }

    public function fetch_withdrawals ($code = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->fetch_transactions_helper ($code, $since, $limit, $params);
        for ($i = 0; $i < count ($response['withdrawals']); $i++) {
            $response['withdrawals'][$i]['type'] = 'withdrawal';
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
        }
        $withdrawals = $this->parseTransactions ($response['withdrawals'], $currency, $since, $limit);
        return $this->filterByCurrencySinceLimit ($withdrawals, $code, $since, $limit);
    }

    public function fetch_deposits ($code = null, $since = null, $limit = null, $params = array ()) {
        $response = $this->fetch_transactions_helper ($code, $since, $limit, $params);
        for ($i = 0; $i < count ($response['deposits']); $i++) {
            $response['deposits'][$i]['type'] = 'deposit';
        }
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency ($code);
        }
        $deposits = $this->parseTransactions ($response['deposits'], $currency, $since, $limit);
        return $this->filterByCurrencySinceLimit ($deposits, $code, $since, $limit);
    }

    public function parse_transaction_status ($status) {
        $statuses = array (
            'COMPLETE' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction ($transaction, $currency = null) {
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
        $timestamp = $this->safe_integer($transaction, 'timestamp');
        if ($timestamp !== null) {
            $timestamp = $timestamp * 1000;
        }
        $code = null;
        $currencyId = $this->safe_string($transaction, 'currency');
        $currency = $this->safe_value($this->currencies_by_id, $currencyId);
        if ($currency === null) {
            $code = $this->common_currency_code($currencyId);
        }
        if ($currency !== null) {
            $code = $currency['code'];
        }
        $status = $this->safe_string($transaction, 'status', 'pending');
        $txid = $this->safe_string($transaction, 'txid');
        if ($status !== null) {
            $parts = explode(' => ', $status);
            $numParts = is_array ($parts) ? count ($parts) : 0;
            $status = $parts[0];
            if (($numParts > 1) && ($txid === null)) {
                $txid = $parts[1];
            }
            $status = $this->parse_transaction_status ($status);
        }
        $type = $this->safe_string($transaction, 'type');
        $id = $this->safe_string_2($transaction, 'withdrawalNumber', 'depositNumber');
        $amount = $this->safe_float($transaction, 'amount');
        $address = $this->safe_string($transaction, 'address');
        $feeCost = $this->safe_float($transaction, 'fee');
        if ($feeCost === null) {
            // according to https://poloniex.com/fees/
            $feeCost = 0; // FIXME => remove hardcoded value that may change any time
        }
        if ($type === 'withdrawal') {
            // poloniex withdrawal $amount includes the fee
            $amount = $amount - $feeCost;
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
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'fee' => array (
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        $query = array_merge (array( 'command' => $path ), $params);
        if ($api === 'public') {
            $url .= '?' . $this->urlencode ($query);
        } else {
            $this->check_required_credentials();
            $query['nonce'] = $this->nonce ();
            $body = $this->urlencode ($query);
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $this->hmac ($this->encode ($body), $this->encode ($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response) {
        if ($response === null) {
            return;
        }
        // array("error":"Permission denied.")
        if (is_array($response) && array_key_exists('error', $response)) {
            $message = $response['error'];
            $feedback = $this->id . ' ' . $this->json ($response);
            $exact = $this->exceptions['exact'];
            if (is_array($exact) && array_key_exists($message, $exact)) {
                throw new $exact[$message]($feedback);
            }
            $broad = $this->exceptions['broad'];
            $broadKey = $this->findBroadlyMatchedKey ($broad, $message);
            if ($broadKey !== null) {
                throw new $broad[$broadKey]($feedback);
            }
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

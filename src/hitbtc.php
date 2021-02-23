<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\InvalidOrder;
use \ccxt\OrderNotFound;
use \ccxt\ExchangeNotAvailable;

class hitbtc extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'hitbtc',
            'name' => 'HitBTC',
            'countries' => array( 'HK' ),
            'rateLimit' => 1500,
            'version' => '2',
            'pro' => true,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createDepositAddress' => true,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => false,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrder' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => false,
                'fetchOrderTrades' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTradingFee' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => false,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => 'M1',
                '3m' => 'M3',
                '5m' => 'M5',
                '15m' => 'M15',
                '30m' => 'M30', // default
                '1h' => 'H1',
                '4h' => 'H4',
                '1d' => 'D1',
                '1w' => 'D7',
                '1M' => '1M',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766555-8eaec20e-5edc-11e7-9c5b-6dc69fc42f5e.jpg',
                'api' => array(
                    'public' => 'https://api.hitbtc.com',
                    'private' => 'https://api.hitbtc.com',
                ),
                'www' => 'https://hitbtc.com',
                'referral' => 'https://hitbtc.com/?ref_id=5a5d39a65d466',
                'doc' => array(
                    'https://api.hitbtc.com',
                    'https://github.com/hitbtc-com/hitbtc-api/blob/master/APIv2.md',
                ),
                'fees' => array(
                    'https://hitbtc.com/fees-and-limits',
                    'https://support.hitbtc.com/hc/en-us/articles/115005148605-Fees-and-limits',
                ),
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'symbol', // Available Currency Symbols
                        'symbol/{symbol}', // Get symbol info
                        'currency', // Available Currencies
                        'currency/{currency}', // Get currency info
                        'ticker', // Ticker list for all symbols
                        'ticker/{symbol}', // Ticker for symbol
                        'trades',
                        'trades/{symbol}', // Trades
                        'orderbook',
                        'orderbook/{symbol}', // Orderbook
                        'candles',
                        'candles/{symbol}', // Candles
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'trading/balance', // Get trading balance
                        'order', // List your current open orders
                        'order/{clientOrderId}', // Get a single order by clientOrderId
                        'trading/fee/all', // Get trading fee rate
                        'trading/fee/{symbol}', // Get trading fee rate
                        'history/order', // Get historical orders
                        'history/trades', // Get historical trades
                        'history/order/{orderId}/trades', // Get historical trades by specified order
                        'account/balance', // Get main acccount balance
                        'account/crypto/address/{currency}', // Get deposit crypro address
                        'account/crypto/is-mine/{address}',
                        'account/transactions', // Get account transactions
                        'account/transactions/{id}', // Get account transaction by id
                        'sub-acc',
                        'sub-acc/acl',
                        'sub-acc/balance/{subAccountUserID}',
                        'sub-acc/deposit-address/{subAccountUserId}/{currency}',
                    ),
                    'post' => array(
                        'order', // Create new order
                        'account/crypto/address/{currency}', // Create new deposit crypro address
                        'account/crypto/withdraw', // Withdraw crypro
                        'account/crypto/transfer-convert',
                        'account/transfer', // Transfer amount to trading
                        'sub-acc/freeze',
                        'sub-acc/activate',
                        'sub-acc/transfer',
                    ),
                    'put' => array(
                        'order/{clientOrderId}', // Create new order
                        'account/crypto/withdraw/{id}', // Commit withdraw crypro
                        'sub-acc/acl/{subAccountUserId}',
                    ),
                    'delete' => array(
                        'order', // Cancel all open orders
                        'order/{clientOrderId}', // Cancel order
                        'account/crypto/withdraw/{id}', // Rollback withdraw crypro
                    ),
                    // outdated?
                    'patch' => array(
                        'order/{clientOrderId}', // Cancel Replace order
                    ),
                ),
            ),
            'precisionMode' => TICK_SIZE,
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.1 / 100,
                    'taker' => 0.2 / 100,
                ),
            ),
            'options' => array(
                'defaultTimeInForce' => 'FOK',
            ),
            'commonCurrencies' => array(
                'BCC' => 'BCC', // initial symbol for Bitcoin Cash, now inactive
                'BET' => 'DAO.Casino',
                'BOX' => 'BOX Token',
                'CPT' => 'Cryptaur', // conflict with CPT = Contents Protocol https://github.com/ccxt/ccxt/issues/4920 and https://github.com/ccxt/ccxt/issues/6081
                'GET' => 'Themis',
                'HSR' => 'HC',
                'IQ' => 'IQ.Cash',
                'LNC' => 'LinkerCoin',
                'PLA' => 'PlayChip',
                'PNT' => 'Penta',
                'SBTC' => 'Super Bitcoin',
                'TV' => 'Tokenville',
                'USD' => 'USDT',
                'XPNT' => 'PNT',
            ),
            'exceptions' => array(
                '504' => '\\ccxt\\RequestTimeout', // array("error":array("code":504,"message":"Gateway Timeout"))
                '1002' => '\\ccxt\\AuthenticationError', // array("error":array("code":1002,"message":"Authorization failed","description":""))
                '1003' => '\\ccxt\\PermissionDenied', // "Action is forbidden for this API key"
                '2010' => '\\ccxt\\InvalidOrder', // "Quantity not a valid number"
                '2001' => '\\ccxt\\BadSymbol', // "Symbol not found"
                '2011' => '\\ccxt\\InvalidOrder', // "Quantity too low"
                '2020' => '\\ccxt\\InvalidOrder', // "Price not a valid number"
                '20002' => '\\ccxt\\OrderNotFound', // canceling non-existent order
                '20001' => '\\ccxt\\InsufficientFunds', // array("error":array("code":20001,"message":"Insufficient funds","description":"Check that the funds are sufficient, given commissions"))
            ),
            'orders' => array(), // orders cache / emulation
        ));
    }

    public function fee_to_precision($symbol, $fee) {
        return $this->decimal_to_precision($fee, TRUNCATE, 8, DECIMAL_PLACES);
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetSymbol ($params);
        //
        //     array(
        //         {
        //             "$id":"BCNBTC",
        //             "baseCurrency":"BCN",
        //             "quoteCurrency":"BTC",
        //             "quantityIncrement":"100",
        //             "tickSize":"0.00000000001",
        //             "takeLiquidityRate":"0.002",
        //             "provideLiquidityRate":"0.001",
        //             "feeCurrency":"BTC"
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'id');
            $baseId = $this->safe_string($market, 'baseCurrency');
            $quoteId = $this->safe_string($market, 'quoteCurrency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            // bequant fix
            if (mb_strpos($id, '_') !== false) {
                $symbol = $id;
            }
            $lot = $this->safe_float($market, 'quantityIncrement');
            $step = $this->safe_float($market, 'tickSize');
            $precision = array(
                'price' => $step,
                'amount' => $lot,
            );
            $taker = $this->safe_float($market, 'takeLiquidityRate');
            $maker = $this->safe_float($market, 'provideLiquidityRate');
            $feeCurrencyId = $this->safe_string($market, 'feeCurrency');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
            $result[] = array_merge($this->fees['trading'], array(
                'info' => $market,
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'taker' => $taker,
                'maker' => $maker,
                'precision' => $precision,
                'feeCurrency' => $feeCurrencyCode,
                'limits' => array(
                    'amount' => array(
                        'min' => $lot,
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => $step,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $lot * $step,
                        'max' => null,
                    ),
                ),
            ));
        }
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrency ($params);
        //
        //     array(
        //         {
        //             "$id":"XPNT",
        //             "fullName":"pToken",
        //             "crypto":true,
        //             "payinEnabled":true,
        //             "payinPaymentId":false,
        //             "payinConfirmations":9,
        //             "payoutEnabled":true,
        //             "payoutIsPaymentId":false,
        //             "transferEnabled":true,
        //             "delisted":false,
        //             "payoutFee":"26.510000000000",
        //             "precisionPayout":18,
        //             "precisionTransfer":8
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'id');
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/deposit methods and
            // differentiated fees for each particular method
            $decimals = $this->safe_integer($currency, 'precisionTransfer', 8);
            $precision = 1 / pow(10, $decimals);
            $code = $this->safe_currency_code($id);
            $payin = $this->safe_value($currency, 'payinEnabled');
            $payout = $this->safe_value($currency, 'payoutEnabled');
            $transfer = $this->safe_value($currency, 'transferEnabled');
            $active = $payin && $payout && $transfer;
            if (is_array($currency) && array_key_exists('disabled', $currency)) {
                if ($currency['disabled']) {
                    $active = false;
                }
            }
            $type = 'fiat';
            if ((is_array($currency) && array_key_exists('crypto', $currency)) && $currency['crypto']) {
                $type = 'crypto';
            }
            $name = $this->safe_string($currency, 'fullName');
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'type' => $type,
                'payin' => $payin,
                'payout' => $payout,
                'transfer' => $transfer,
                'info' => $currency,
                'name' => $name,
                'active' => $active,
                'fee' => $this->safe_float($currency, 'payoutFee'), // todo => redesign
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => 1 / pow(10, $decimals),
                        'max' => pow(10, $decimals),
                    ),
                    'price' => array(
                        'min' => 1 / pow(10, $decimals),
                        'max' => pow(10, $decimals),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => null,
                        'max' => pow(10, $precision),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_trading_fee($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array_merge(array(
            'symbol' => $market['id'],
        ), $this->omit($params, 'symbol'));
        $response = $this->privateGetTradingFeeSymbol ($request);
        //
        //     {
        //         takeLiquidityRate => '0.001',
        //         provideLiquidityRate => '-0.0001'
        //     }
        //
        return array(
            'info' => $response,
            'maker' => $this->safe_float($response, 'provideLiquidityRate'),
            'taker' => $this->safe_float($response, 'takeLiquidityRate'),
        );
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $type = $this->safe_string($params, 'type', 'trading');
        $method = 'privateGet' . $this->capitalize($type) . 'Balance';
        $query = $this->omit($params, 'type');
        $response = $this->$method ($query);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['used'] = $this->safe_float($balance, 'reserved');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "timestamp":"2015-08-20T19:01:00.000Z",
        //         "open":"0.006",
        //         "close":"0.006",
        //         "min":"0.006",
        //         "max":"0.006",
        //         "volume":"0.003",
        //         "volumeQuote":"0.000018"
        //     }
        //
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'timestamp')),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'max'),
            $this->safe_float($ohlcv, 'min'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'period' => $this->timeframes[$timeframe],
        );
        if ($since !== null) {
            $request['from'] = $this->iso8601($since);
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetCandlesSymbol (array_merge($request, $params));
        //
        //     array(
        //         array("timestamp":"2015-08-20T19:01:00.000Z","open":"0.006","close":"0.006","min":"0.006","max":"0.006","volume":"0.003","volumeQuote":"0.000018"),
        //         array("timestamp":"2015-08-20T19:03:00.000Z","open":"0.006","close":"0.006","min":"0.006","max":"0.006","volume":"0.013","volumeQuote":"0.000078"),
        //         array("timestamp":"2015-08-20T19:06:00.000Z","open":"0.0055","close":"0.005","min":"0.005","max":"0.0055","volume":"0.003","volumeQuote":"0.0000155"),
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 100, 0 = unlimited
        }
        $response = $this->publicGetOrderbookSymbol (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'bid', 'ask', 'price', 'size');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->parse8601($ticker['timestamp']);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $baseVolume = $this->safe_float($ticker, 'volume');
        $quoteVolume = $this->safe_float($ticker, 'volumeQuote');
        $open = $this->safe_float($ticker, 'open');
        $last = $this->safe_float($ticker, 'last');
        $change = null;
        $percentage = null;
        $average = null;
        if ($last !== null && $open !== null) {
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
            if ($open > 0) {
                $percentage = $change / $open * 100;
            }
        }
        $vwap = $this->vwap($baseVolume, $quoteVolume);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $response[$i];
            $marketId = $this->safe_string($ticker, 'symbol');
            $market = $this->safe_market($marketId);
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTickerSymbol (array_merge($request, $params));
        if (is_array($response) && array_key_exists('message', $response)) {
            throw new ExchangeError($this->id . ' ' . $response['message']);
        }
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market = null) {
        // createMarketOrder
        //
        //  {       $fee => "0.0004644",
        //           $id =>  386394956,
        //        $price => "0.4644",
        //     quantity => "1",
        //    $timestamp => "2018-10-25T16:41:44.780Z" }
        //
        // fetchTrades
        //
        // { $id => 974786185,
        //   $price => '0.032462',
        //   quantity => '0.3673',
        //   $side => 'buy',
        //   $timestamp => '2020-10-16T12:57:39.846Z' }
        //
        // fetchMyTrades
        //
        // { $id => 277210397,
        //   clientOrderId => '6e102f3e7f3f4e04aeeb1cdc95592f1a',
        //   $orderId => 28102855393,
        //   $symbol => 'ETHBTC',
        //   $side => 'sell',
        //   quantity => '0.002',
        //   $price => '0.073365',
        //   $fee => '0.000000147',
        //   $timestamp => '2018-04-28T18:39:55.345Z' }
        $timestamp = $this->parse8601($trade['timestamp']);
        $marketId = $this->safe_string($trade, 'symbol');
        $market = $this->safe_market($marketId, $market);
        $symbol = $market['symbol'];
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyCode = $market ? $market['feeCurrency'] : null;
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        // we use clientOrderId as the order $id with this exchange intentionally
        // because most of their endpoints will require clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        $orderId = $this->safe_string($trade, 'clientOrderId');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'quantity');
        $cost = $price * $amount;
        $side = $this->safe_string($trade, 'side');
        $id = $this->safe_string($trade, 'id');
        return array(
            'info' => $trade,
            'id' => $id,
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

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array();
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['asset'] = $currency['id'];
        }
        if ($since !== null) {
            $request['startTime'] = $since;
        }
        $response = $this->privateGetAccountTransactions (array_merge($request, $params));
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //     array(
        //         $id => 'd53ee9df-89bf-4d09-886e-849f8be64647',
        //         index => 1044718371,
        //         $type => 'payout',
        //         $status => 'success',
        //         $currency => 'ETH',
        //         $amount => '4.522683200000000000000000',
        //         createdAt => '2018-06-07T00:43:32.426Z',
        //         updatedAt => '2018-06-07T00:45:36.447Z',
        //         hash => '0x973e5683dfdf80a1fb1e0b96e19085b6489221d2ddf864daa46903c5ec283a0f',
        //         $address => '0xC5a59b21948C1d230c8C54f05590000Eb3e1252c',
        //         $fee => '0.00958',
        //     ),
        //     array(
        //         $id => 'e6c63331-467e-4922-9edc-019e75d20ba3',
        //         index => 1044714672,
        //         $type => 'exchangeToBank',
        //         $status => 'success',
        //         $currency => 'ETH',
        //         $amount => '4.532263200000000000',
        //         createdAt => '2018-06-07T00:42:39.543Z',
        //         updatedAt => '2018-06-07T00:42:39.683Z',
        //     ),
        //     array(
        //         $id => '3b052faa-bf97-4636-a95c-3b5260015a10',
        //         index => 1009280164,
        //         $type => 'bankToExchange',
        //         $status => 'success',
        //         $currency => 'CAS',
        //         $amount => '104797.875800000000000000',
        //         createdAt => '2018-05-19T02:34:36.750Z',
        //         updatedAt => '2018-05-19T02:34:36.857Z',
        //     ),
        //     {
        //         $id => 'd525249f-7498-4c81-ba7b-b6ae2037dc08',
        //         index => 1009279948,
        //         $type => 'payin',
        //         $status => 'success',
        //         $currency => 'CAS',
        //         $amount => '104797.875800000000000000',
        //         createdAt => '2018-05-19T02:30:16.698Z',
        //         updatedAt => '2018-05-19T02:34:28.159Z',
        //         hash => '0xa6530e1231de409cf1f282196ed66533b103eac1df2aa4a7739d56b02c5f0388',
        //         $address => '0xd53ed559a6d963af7cb3f3fcd0e7ca499054db8b',
        //     }
        //
        //     {
        //         "$id" => "4f351f4f-a8ee-4984-a468-189ed590ddbd",
        //         "index" => 3112719565,
        //         "$type" => "withdraw",
        //         "$status" => "success",
        //         "$currency" => "BCHOLD",
        //         "$amount" => "0.02423133",
        //         "createdAt" => "2019-07-16T16:52:04.494Z",
        //         "updatedAt" => "2019-07-16T16:54:07.753Z"
        //     }
        $id = $this->safe_string($transaction, 'id');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'createdAt'));
        $updated = $this->parse8601($this->safe_string($transaction, 'updatedAt'));
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        $amount = $this->safe_float($transaction, 'amount');
        $address = $this->safe_string($transaction, 'address');
        $txid = $this->safe_string($transaction, 'hash');
        $fee = null;
        $feeCost = $this->safe_float($transaction, 'fee');
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => $code,
            );
        }
        $type = $this->parse_transaction_type($this->safe_string($transaction, 'type'));
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'tag' => null,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'pending' => 'pending',
            'failed' => 'failed',
            'success' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction_type($type) {
        $types = array(
            'payin' => 'deposit',
            'payout' => 'withdrawal',
            'withdraw' => 'withdrawal',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        if ($since !== null) {
            $request['sort'] = 'ASC';
            $request['from'] = $this->iso8601($since);
        }
        $response = $this->publicGetTradesSymbol (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        // we use $clientOrderId as the $order id with this exchange intentionally
        // because most of their endpoints will require $clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        // their max accepted length is 32 characters
        $uuid = $this->uuid();
        $parts = explode('-', $uuid);
        $clientOrderId = implode('', $parts);
        $clientOrderId = mb_substr($clientOrderId, 0, 32 - 0);
        $amount = floatval($amount);
        $request = array(
            'clientOrderId' => $clientOrderId,
            'symbol' => $market['id'],
            'side' => $side,
            'quantity' => $this->amount_to_precision($symbol, $amount),
            'type' => $type,
        );
        if ($type === 'limit') {
            $request['price'] = $this->price_to_precision($symbol, $price);
        } else {
            $request['timeInForce'] = $this->options['defaultTimeInForce'];
        }
        $response = $this->privatePostOrder (array_merge($request, $params));
        $order = $this->parse_order($response);
        if ($order['status'] === 'rejected') {
            throw new InvalidOrder($this->id . ' $order was rejected by the exchange ' . $this->json($order));
        }
        return $order;
    }

    public function edit_order($id, $symbol, $type, $side, $amount = null, $price = null, $params = array ()) {
        $this->load_markets();
        // we use clientOrderId as the order $id with this exchange intentionally
        // because most of their endpoints will require clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        // their max accepted length is 32 characters
        $uuid = $this->uuid();
        $parts = explode('-', $uuid);
        $requestClientId = implode('', $parts);
        $requestClientId = mb_substr($requestClientId, 0, 32 - 0);
        $request = array(
            'clientOrderId' => $id,
            'requestClientId' => $requestClientId,
        );
        if ($amount !== null) {
            $request['quantity'] = $this->amount_to_precision($symbol, $amount);
        }
        if ($price !== null) {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePatchOrderClientOrderId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        // we use clientOrderId as the order $id with this exchange intentionally
        // because most of their endpoints will require clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        $request = array(
            'clientOrderId' => $id,
        );
        $response = $this->privateDeleteOrderClientOrderId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'new' => 'open',
            'suspended' => 'open',
            'partiallyFilled' => 'open',
            'filled' => 'closed',
            'canceled' => 'canceled',
            'expired' => 'failed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createMarketOrder
        //
        //     {
        //         $clientOrderId => "fe36aa5e190149bf9985fb673bbb2ea0",
        //         createdAt => "2018-10-25T16:41:44.780Z",
        //         cumQuantity => "1",
        //         $id => "66799540063",
        //         quantity => "1",
        //         $side => "sell",
        //         $status => "$filled",
        //         $symbol => "XRPUSDT",
        //         $timeInForce => "FOK",
        //         tradesReport => array(
        //             {
        //                 $fee => "0.0004644",
        //                 $id =>  386394956,
        //                 $price => "0.4644",
        //                 quantity => "1",
        //                 timestamp => "2018-10-25T16:41:44.780Z"
        //             }
        //         ),
        //         $type => "$market",
        //         updatedAt => "2018-10-25T16:41:44.780Z"
        //     }
        //
        //     {
        //         "$id" => 119499457455,
        //         "$clientOrderId" => "87baab109d58401b9202fa0749cb8288",
        //         "$symbol" => "ETHUSD",
        //         "$side" => "buy",
        //         "$status" => "$filled",
        //         "$type" => "$market",
        //         "$timeInForce" => "FOK",
        //         "quantity" => "0.0007",
        //         "$price" => "181.487",
        //         "avgPrice" => "164.989",
        //         "cumQuantity" => "0.0007",
        //         "createdAt" => "2019-04-17T13:27:38.062Z",
        //         "updatedAt" => "2019-04-17T13:27:38.062Z"
        //     }
        //
        $created = $this->parse8601($this->safe_string($order, 'createdAt'));
        $updated = $this->parse8601($this->safe_string($order, 'updatedAt'));
        $marketId = $this->safe_string($order, 'symbol');
        $market = $this->safe_market($marketId, $market);
        $symbol = $market['symbol'];
        $amount = $this->safe_float($order, 'quantity');
        $filled = $this->safe_float($order, 'cumQuantity');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        // we use $clientOrderId as the $order $id with this exchange intentionally
        // because most of their endpoints will require $clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        $id = $this->safe_string($order, 'clientOrderId');
        $clientOrderId = $id;
        $price = $this->safe_float($order, 'price');
        $remaining = null;
        $cost = null;
        if ($amount !== null) {
            if ($filled !== null) {
                $remaining = $amount - $filled;
                if ($price !== null) {
                    $cost = $filled * $price;
                }
            }
        }
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side');
        $trades = $this->safe_value($order, 'tradesReport');
        $fee = null;
        $average = $this->safe_value($order, 'avgPrice');
        if ($trades !== null) {
            $trades = $this->parse_trades($trades, $market);
            $feeCost = null;
            $numTrades = is_array($trades) ? count($trades) : 0;
            $tradesCost = 0;
            for ($i = 0; $i < $numTrades; $i++) {
                if ($feeCost === null) {
                    $feeCost = 0;
                }
                $tradesCost = $this->sum($tradesCost, $trades[$i]['cost']);
                $tradeFee = $this->safe_value($trades[$i], 'fee', array());
                $tradeFeeCost = $this->safe_float($tradeFee, 'cost');
                if ($tradeFeeCost !== null) {
                    $feeCost = $this->sum($feeCost, $tradeFeeCost);
                }
            }
            $cost = $tradesCost;
            if (($filled !== null) && ($filled > 0)) {
                if ($average === null) {
                    $average = $cost / $filled;
                }
                if ($type === 'market') {
                    if ($price === null) {
                        $price = $average;
                    }
                }
            }
            if ($feeCost !== null) {
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $market['quote'],
                );
            }
        }
        $timeInForce = $this->safe_string($order, 'timeInForce');
        return array(
            'id' => $id,
            'clientOrderId' => $clientOrderId, // https://github.com/ccxt/ccxt/issues/5674
            'timestamp' => $created,
            'datetime' => $this->iso8601($created),
            'lastTradeTimestamp' => $updated,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => $timeInForce,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'average' => $average,
            'amount' => $amount,
            'cost' => $cost,
            'filled' => $filled,
            'remaining' => $remaining,
            'fee' => $fee,
            'trades' => $trades,
            'info' => $order,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        // we use clientOrderId as the order $id with this exchange intentionally
        // because most of their endpoints will require clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        $request = array(
            'clientOrderId' => $id,
        );
        $response = $this->privateGetHistoryOrder (array_merge($request, $params));
        $numOrders = is_array($response) ? count($response) : 0;
        if ($numOrders > 0) {
            return $this->parse_order($response[0]);
        }
        throw new OrderNotFound($this->id . ' order ' . $id . ' not found');
    }

    public function fetch_open_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        // we use clientOrderId as the order $id with this exchange intentionally
        // because most of their endpoints will require clientOrderId
        // explained here => https://github.com/ccxt/ccxt/issues/5674
        $request = array(
            'clientOrderId' => $id,
        );
        $response = $this->privateGetOrderClientOrderId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $response = $this->privateGetOrder (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        if ($since !== null) {
            $request['from'] = $this->iso8601($since);
        }
        $response = $this->privateGetHistoryOrder (array_merge($request, $params));
        $parsedOrders = $this->parse_orders($response, $market);
        $orders = array();
        for ($i = 0; $i < count($parsedOrders); $i++) {
            $order = $parsedOrders[$i];
            $status = $order['status'];
            if (($status === 'closed') || ($status === 'canceled')) {
                $orders[] = $order;
            }
        }
        return $this->filter_by_since_limit($orders, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'symbol' => 'BTC/USD', // optional
            // 'sort' =>   'DESC', // or 'ASC'
            // 'by' =>     'timestamp', // or 'id' String timestamp by default, or id
            // 'from' =>   'Datetime or Number', // ISO 8601
            // 'till' =>   'Datetime or Number',
            // 'limit' =>  100,
            // 'offset' => 0,
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['from'] = $this->iso8601($since);
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetHistoryTrades (array_merge($request, $params));
        //
        //     array(
        //         array(
        //         "id" => 9535486,
        //         "clientOrderId" => "f8dbaab336d44d5ba3ff578098a68454",
        //         "orderId" => 816088377,
        //         "$symbol" => "ETHBTC",
        //         "side" => "sell",
        //         "quantity" => "0.061",
        //         "price" => "0.045487",
        //         "fee" => "0.000002775",
        //         "timestamp" => "2017-05-17T12:32:57.848Z"
        //         ),
        //         {
        //         "id" => 9535437,
        //         "clientOrderId" => "27b9bfc068b44194b1f453c7af511ed6",
        //         "orderId" => 816088021,
        //         "$symbol" => "ETHBTC",
        //         "side" => "buy",
        //         "quantity" => "0.038",
        //         "price" => "0.046000",
        //         "fee" => "-0.000000174",
        //         "timestamp" => "2017-05-17T12:30:57.848Z"
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        // The $id needed here is the exchange's $id, and not the clientOrderID,
        // which is the $id that is stored in the unified order $id
        // To get the exchange's $id you need to grab it from order['info']['id']
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $request = array(
            'orderId' => $id,
        );
        $response = $this->privateGetHistoryOrderOrderIdTrades (array_merge($request, $params));
        $numOrders = is_array($response) ? count($response) : 0;
        if ($numOrders > 0) {
            return $this->parse_trades($response, $market, $since, $limit);
        }
        throw new OrderNotFound($this->id . ' order ' . $id . ' not found, ' . $this->id . '.fetchOrderTrades() requires an exchange-specific order $id, you need to grab it from order["info"]["$id"]');
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privatePostAccountCryptoAddressCurrency (array_merge($request, $params));
        $address = $this->safe_string($response, 'address');
        $this->check_address($address);
        $tag = $this->safe_string($response, 'paymentId');
        return array(
            'currency' => $currency,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetAccountCryptoAddressCurrency (array_merge($request, $params));
        $address = $this->safe_string($response, 'address');
        $this->check_address($address);
        $tag = $this->safe_string($response, 'paymentId');
        return array(
            'currency' => $currency['code'],
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $this->check_address($address);
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'amount' => floatval($amount),
            'address' => $address,
        );
        if ($tag) {
            $request['paymentId'] = $tag;
        }
        $response = $this->privatePostAccountCryptoWithdraw (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['id'],
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '/api/' . $this->version . '/';
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            $url .= $api . '/' . $this->implode_params($path, $params);
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $url .= $this->implode_params($path, $params);
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            } else if ($query) {
                $body = $this->json($query);
            }
            $payload = $this->encode($this->apiKey . ':' . $this->secret);
            $auth = base64_encode($payload);
            $headers = array(
                'Authorization' => 'Basic ' . $this->decode($auth),
                'Content-Type' => 'application/json',
            );
        }
        $url = $this->urls['api'][$api] . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        if ($code >= 400) {
            $feedback = $this->id . ' ' . $body;
            // array("$code":504,"$message":"Gateway Timeout","description":"")
            if (($code === 503) || ($code === 504)) {
                throw new ExchangeNotAvailable($feedback);
            }
            // fallback to default error handler on rate limit errors
            // array("$code":429,"$message":"Too many requests","description":"Too many requests")
            if ($code === 429) {
                return;
            }
            // array("error":array("$code":20002,"$message":"Order not found","description":""))
            if ($body[0] === '{') {
                if (is_array($response) && array_key_exists('error', $response)) {
                    $errorCode = $this->safe_string($response['error'], 'code');
                    $this->throw_exactly_matched_exception($this->exceptions, $errorCode, $feedback);
                    $message = $this->safe_string($response['error'], 'message');
                    if ($message === 'Duplicate clientOrderId') {
                        throw new InvalidOrder($feedback);
                    }
                }
            }
            throw new ExchangeError($feedback);
        }
    }
}

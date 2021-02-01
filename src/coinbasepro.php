<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidAddress;
use \ccxt\NotSupported;

class coinbasepro extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinbasepro',
            'name' => 'Coinbase Pro',
            'countries' => array( 'US' ),
            'rateLimit' => 1000,
            'userAgent' => $this->userAgents['chrome'],
            'pro' => true,
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'CORS' => true,
                'createDepositAddress' => true,
                'createOrder' => true,
                'deposit' => true,
                'fetchAccounts' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => true,
                'fetchClosedOrders' => true,
                'fetchDepositAddress' => false, // the exchange does not have this method, only createDepositAddress, see https://github.com/ccxt/ccxt/pull/7405
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchOrderTrades' => true,
                'fetchTime' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
                'fetchTransactions' => true,
                'withdraw' => true,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
            ),
            'timeframes' => array(
                '1m' => 60,
                '5m' => 300,
                '15m' => 900,
                '1h' => 3600,
                '6h' => 21600,
                '1d' => 86400,
            ),
            'urls' => array(
                'test' => array(
                    'public' => 'https://api-public.sandbox.pro.coinbase.com',
                    'private' => 'https://api-public.sandbox.pro.coinbase.com',
                ),
                'logo' => 'https://user-images.githubusercontent.com/1294454/41764625-63b7ffde-760a-11e8-996d-a6328fa9347a.jpg',
                'api' => array(
                    'public' => 'https://api.pro.coinbase.com',
                    'private' => 'https://api.pro.coinbase.com',
                ),
                'www' => 'https://pro.coinbase.com/',
                'doc' => 'https://docs.pro.coinbase.com',
                'fees' => array(
                    'https://docs.pro.coinbase.com/#fees',
                    'https://support.pro.coinbase.com/customer/en/portal/articles/2945310-fees',
                ),
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'password' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencies',
                        'products',
                        'products/{id}',
                        'products/{id}/book',
                        'products/{id}/candles',
                        'products/{id}/stats',
                        'products/{id}/ticker',
                        'products/{id}/trades',
                        'time',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts',
                        'accounts/{id}',
                        'accounts/{id}/holds',
                        'accounts/{id}/ledger',
                        'accounts/{id}/transfers',
                        'coinbase-accounts',
                        'fills',
                        'funding',
                        'fees',
                        'margin/profile_information',
                        'margin/buying_power',
                        'margin/withdrawal_power',
                        'margin/withdrawal_power_all',
                        'margin/exit_plan',
                        'margin/liquidation_history',
                        'margin/position_refresh_amounts',
                        'margin/status',
                        'oracle',
                        'orders',
                        'orders/{id}',
                        'orders/client:{client_oid}',
                        'otc/orders',
                        'payment-methods',
                        'position',
                        'profiles',
                        'profiles/{id}',
                        'reports/{report_id}',
                        'transfers',
                        'transfers/{transfer_id}',
                        'users/self/trailing-volume',
                        'users/self/exchange-limits',
                        'withdrawals/fee-estimate',
                    ),
                    'post' => array(
                        'conversions',
                        'deposits/coinbase-account',
                        'deposits/payment-method',
                        'coinbase-accounts/{id}/addresses',
                        'funding/repay',
                        'orders',
                        'position/close',
                        'profiles/margin-transfer',
                        'profiles/transfer',
                        'reports',
                        'withdrawals/coinbase',
                        'withdrawals/coinbase-account',
                        'withdrawals/crypto',
                        'withdrawals/payment-method',
                    ),
                    'delete' => array(
                        'orders',
                        'orders/client:{client_oid}',
                        'orders/{id}',
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'CGLD' => 'CELO',
            ),
            'precisionMode' => TICK_SIZE,
            'fees' => array(
                'trading' => array(
                    'tierBased' => true, // complicated tier system per coin
                    'percentage' => true,
                    'maker' => 0.5 / 100, // highest fee of all tiers
                    'taker' => 0.5 / 100, // highest fee of all tiers
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(
                        'BCH' => 0,
                        'BTC' => 0,
                        'LTC' => 0,
                        'ETH' => 0,
                        'EUR' => 0.15,
                        'USD' => 25,
                    ),
                    'deposit' => array(
                        'BCH' => 0,
                        'BTC' => 0,
                        'LTC' => 0,
                        'ETH' => 0,
                        'EUR' => 0.15,
                        'USD' => 10,
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'Insufficient funds' => '\\ccxt\\InsufficientFunds',
                    'NotFound' => '\\ccxt\\OrderNotFound',
                    'Invalid API Key' => '\\ccxt\\AuthenticationError',
                    'invalid signature' => '\\ccxt\\AuthenticationError',
                    'Invalid Passphrase' => '\\ccxt\\AuthenticationError',
                    'Invalid order id' => '\\ccxt\\InvalidOrder',
                    'Private rate limit exceeded' => '\\ccxt\\RateLimitExceeded',
                    'Trading pair not available' => '\\ccxt\\PermissionDenied',
                    'Product not found' => '\\ccxt\\InvalidOrder',
                ),
                'broad' => array(
                    'Order already done' => '\\ccxt\\OrderNotFound',
                    'order not found' => '\\ccxt\\OrderNotFound',
                    'price too small' => '\\ccxt\\InvalidOrder',
                    'price too precise' => '\\ccxt\\InvalidOrder',
                    'under maintenance' => '\\ccxt\\OnMaintenance',
                    'size is too small' => '\\ccxt\\InvalidOrder',
                    'Cancel only mode' => '\\ccxt\\OnMaintenance', // https://github.com/ccxt/ccxt/issues/7690
                ),
            ),
        ));
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     array(
        //         {
        //             $id => 'XTZ',
        //             $name => 'Tezos',
        //             min_size => '0.000001',
        //             $status => 'online',
        //             message => '',
        //             max_precision => '0.000001',
        //             convertible_to => array(),
        //             $details => {
        //                 type => 'crypto',
        //                 symbol => 'Τ',
        //                 network_confirmations => 60,
        //                 sort_order => 53,
        //                 crypto_address_link => 'https://tzstats.com/array({address})',
        //                 crypto_transaction_link => 'https://tzstats.com/array({txId})',
        //                 push_payment_methods => array( 'crypto' ),
        //                 group_types => array(),
        //                 display_name => '',
        //                 processing_time_seconds => 0,
        //                 min_withdrawal_amount => 1
        //             }
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'id');
            $name = $this->safe_string($currency, 'name');
            $code = $this->safe_currency_code($id);
            $details = $this->safe_value($currency, 'details', array());
            $precision = $this->safe_float($currency, 'max_precision');
            $status = $this->safe_string($currency, 'status');
            $active = ($status === 'online');
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'type' => $this->safe_string($details, 'type'),
                'name' => $name,
                'active' => $active,
                'fee' => null,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($details, 'min_size'),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_float($details, 'min_withdrawal_amount'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetProducts ($params);
        //
        //     array(
        //         {
        //             "$id":"ZEC-BTC",
        //             "base_currency":"ZEC",
        //             "quote_currency":"BTC",
        //             "base_min_size":"0.01000000",
        //             "base_max_size":"1500.00000000",
        //             "quote_increment":"0.00000100",
        //             "base_increment":"0.00010000",
        //             "display_name":"ZEC/BTC",
        //             "min_market_funds":"0.001",
        //             "max_market_funds":"30",
        //             "margin_enabled":false,
        //             "post_only":false,
        //             "limit_only":false,
        //             "cancel_only":false,
        //             "trading_disabled":false,
        //             "$status":"online",
        //             "status_message":""
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'id');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quote_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $priceLimits = array(
                'min' => $this->safe_float($market, 'quote_increment'),
                'max' => null,
            );
            $precision = array(
                'amount' => $this->safe_float($market, 'base_increment'),
                'price' => $this->safe_float($market, 'quote_increment'),
            );
            $status = $this->safe_string($market, 'status');
            $active = ($status === 'online');
            $result[] = array_merge($this->fees['trading'], array(
                'id' => $id,
                'symbol' => $symbol,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'base' => $base,
                'quote' => $quote,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'base_min_size'),
                        'max' => $this->safe_float($market, 'base_max_size'),
                    ),
                    'price' => $priceLimits,
                    'cost' => array(
                        'min' => $this->safe_float($market, 'min_market_funds'),
                        'max' => $this->safe_float($market, 'max_market_funds'),
                    ),
                ),
                'active' => $active,
                'info' => $market,
            ));
        }
        return $result;
    }

    public function fetch_accounts($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccounts ($params);
        //
        //     array(
        //         array(
        //             id => '4aac9c60-cbda-4396-9da4-4aa71e95fba0',
        //             currency => 'BTC',
        //             balance => '0.0000000000000000',
        //             available => '0',
        //             hold => '0.0000000000000000',
        //             profile_id => 'b709263e-f42a-4c7d-949a-a95c83d065da'
        //         ),
        //         array(
        //             id => 'f75fa69a-1ad1-4a80-bd61-ee7faa6135a3',
        //             currency => 'USDC',
        //             balance => '0.0000000000000000',
        //             available => '0',
        //             hold => '0.0000000000000000',
        //             profile_id => 'b709263e-f42a-4c7d-949a-a95c83d065da'
        //         ),
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $account = $response[$i];
            $accountId = $this->safe_string($account, 'id');
            $currencyId = $this->safe_string($account, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $result[] = array(
                'id' => $accountId,
                'type' => null,
                'currency' => $code,
                'info' => $account,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccounts ($params);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = array(
                'free' => $this->safe_float($balance, 'available'),
                'used' => $this->safe_float($balance, 'hold'),
                'total' => $this->safe_float($balance, 'balance'),
            );
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        // level 1 - only the best bid and ask
        // level 2 - top 50 bids and asks (aggregated)
        // level 3 - full order book (non aggregated)
        $request = array(
            'id' => $this->market_id($symbol),
            'level' => 2, // 1 best bidask, 2 aggregated, 3 full
        );
        $response = $this->publicGetProductsIdBook (array_merge($request, $params));
        //
        //     {
        //         "sequence":1924393896,
        //         "bids":[
        //             ["0.01825","24.34811287",2],
        //             ["0.01824","72.5463",3],
        //             ["0.01823","424.54298049",6],
        //         ],
        //         "asks":[
        //             ["0.01826","171.10414904",4],
        //             ["0.01827","22.60427028",1],
        //             ["0.01828","397.46018784",7],
        //         ]
        //     }
        //
        $orderbook = $this->parse_order_book($response);
        $orderbook['nonce'] = $this->safe_integer($response, 'sequence');
        return $orderbook;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // publicGetProductsIdTicker
        //
        //     {
        //         "trade_id":843439,
        //         "price":"0.997999",
        //         "size":"80.29769",
        //         "time":"2020-01-28T02:13:33.012523Z",
        //         "$bid":"0.997094",
        //         "$ask":"0.998",
        //         "volume":"1903188.03750000"
        //     }
        //
        // publicGetProductsIdStats
        //
        //     {
        //         "open" => "34.19000000",
        //         "high" => "95.70000000",
        //         "low" => "7.06000000",
        //         "volume" => "2.41000000"
        //     }
        //
        $timestamp = $this->parse8601($this->safe_value($ticker, 'time'));
        $bid = $this->safe_float($ticker, 'bid');
        $ask = $this->safe_float($ticker, 'ask');
        $last = $this->safe_float($ticker, 'price');
        $symbol = ($market === null) ? null : $market['symbol'];
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $bid,
            'bidVolume' => null,
            'ask' => $ask,
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        // publicGetProductsIdTicker or publicGetProductsIdStats
        $method = $this->safe_string($this->options, 'fetchTickerMethod', 'publicGetProductsIdTicker');
        $response = $this->$method (array_merge($request, $params));
        //
        // publicGetProductsIdTicker
        //
        //     {
        //         "trade_id":843439,
        //         "price":"0.997999",
        //         "size":"80.29769",
        //         "time":"2020-01-28T02:13:33.012523Z",
        //         "bid":"0.997094",
        //         "ask":"0.998",
        //         "volume":"1903188.03750000"
        //     }
        //
        // publicGetProductsIdStats
        //
        //     {
        //         "open" => "34.19000000",
        //         "high" => "95.70000000",
        //         "low" => "7.06000000",
        //         "volume" => "2.41000000"
        //     }
        //
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market = null) {
        //
        //     {
        //         $type => 'match',
        //         trade_id => 82047307,
        //         maker_order_id => '0f358725-2134-435e-be11-753912a326e0',
        //         taker_order_id => '252b7002-87a3-425c-ac73-f5b9e23f3caf',
        //         $side => 'sell',
        //         size => '0.00513192',
        //         $price => '9314.78',
        //         product_id => 'BTC-USD',
        //         sequence => 12038915443,
        //         time => '2020-01-31T20:03:41.158814Z'
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string_2($trade, 'time', 'created_at'));
        $marketId = $this->safe_string($trade, 'product_id');
        $symbol = $this->safe_symbol($marketId, $market, '-');
        $feeRate = null;
        $feeCurrency = null;
        $takerOrMaker = null;
        if ($market !== null) {
            $feeCurrency = $market['quote'];
            if (is_array($trade) && array_key_exists('liquidity', $trade)) {
                $takerOrMaker = ($trade['liquidity'] === 'T') ? 'taker' : 'maker';
                $feeRate = $market[$takerOrMaker];
            }
        }
        $feeCost = $this->safe_float_2($trade, 'fill_fees', 'fee');
        $fee = array(
            'cost' => $feeCost,
            'currency' => $feeCurrency,
            'rate' => $feeRate,
        );
        $type = null;
        $id = $this->safe_string($trade, 'trade_id');
        $side = ($trade['side'] === 'buy') ? 'sell' : 'buy';
        $orderId = $this->safe_string($trade, 'order_id');
        // Coinbase Pro returns inverted $side to fetchMyTrades vs fetchTrades
        if ($orderId !== null) {
            $side = ($trade['side'] === 'buy') ? 'buy' : 'sell';
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'size');
        return array(
            'id' => $id,
            'order' => $orderId,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'takerOrMaker' => $takerOrMaker,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'fee' => $fee,
            'cost' => $price * $amount,
        );
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        // as of 2018-08-23
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'product_id' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetFills (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'], // fixes issue #2
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 100
        }
        $response = $this->publicGetProductsIdTrades (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1591514160,
        //         0.02507,
        //         0.02507,
        //         0.02507,
        //         0.02507,
        //         0.02816506
        //     )
        //
        return array(
            $this->safe_timestamp($ohlcv, 0),
            $this->safe_float($ohlcv, 3),
            $this->safe_float($ohlcv, 2),
            $this->safe_float($ohlcv, 1),
            $this->safe_float($ohlcv, 4),
            $this->safe_float($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $granularity = $this->timeframes[$timeframe];
        $request = array(
            'id' => $market['id'],
            'granularity' => $granularity,
        );
        if ($since !== null) {
            $request['start'] = $this->iso8601($since);
            if ($limit === null) {
                // https://docs.pro.coinbase.com/#get-historic-rates
                $limit = 300; // max = 300
            }
            $request['end'] = $this->iso8601($this->sum(($limit - 1) * $granularity * 1000, $since));
        }
        $response = $this->publicGetProductsIdCandles (array_merge($request, $params));
        //
        //     [
        //         [1591514160,0.02507,0.02507,0.02507,0.02507,0.02816506],
        //         [1591514100,0.02507,0.02507,0.02507,0.02507,1.63830323],
        //         [1591514040,0.02505,0.02507,0.02505,0.02507,0.19918178]
        //     ]
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetTime ($params);
        //
        //     {
        //         "iso":"2020-05-12T08:00:51.504Z",
        //         "epoch":1589270451.504
        //     }
        //
        return $this->safe_timestamp($response, 'epoch');
    }

    public function parse_order_status($status) {
        $statuses = array(
            'pending' => 'open',
            'active' => 'open',
            'open' => 'open',
            'done' => 'closed',
            'canceled' => 'canceled',
            'canceling' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "$id" => "d0c5340b-6d6c-49d9-b567-48c4bfca13d2",
        //         "$price" => "0.10000000",
        //         "size" => "0.01000000",
        //         "product_id" => "BTC-USD",
        //         "$side" => "buy",
        //         "stp" => "dc",
        //         "$type" => "limit",
        //         "time_in_force" => "GTC",
        //         "post_only" => false,
        //         "created_at" => "2016-12-08T20:02:28.53864Z",
        //         "fill_fees" => "0.0000000000000000",
        //         "filled_size" => "0.00000000",
        //         "executed_value" => "0.0000000000000000",
        //         "$status" => "pending",
        //         "settled" => false
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string($order, 'created_at'));
        $marketId = $this->safe_string($order, 'product_id');
        $market = $this->safe_market($marketId, $market, '-');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $price = $this->safe_float($order, 'price');
        $filled = $this->safe_float($order, 'filled_size');
        $amount = $this->safe_float($order, 'size', $filled);
        $remaining = null;
        if ($amount !== null) {
            if ($filled !== null) {
                $remaining = $amount - $filled;
            }
        }
        $cost = $this->safe_float($order, 'executed_value');
        $feeCost = $this->safe_float($order, 'fill_fees');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrencyCode = null;
            if ($market !== null) {
                $feeCurrencyCode = $market['quote'];
            }
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
                'rate' => null,
            );
        }
        $id = $this->safe_string($order, 'id');
        $type = $this->safe_string($order, 'type');
        $side = $this->safe_string($order, 'side');
        $timeInForce = $this->safe_string($order, 'time_in_force');
        $postOnly = $this->safe_value($order, 'post_only');
        $stopPrice = $this->safe_float($order, 'stop_price');
        $clientOrderId = $this->safe_string($order, 'client_oid');
        return array(
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $market['symbol'],
            'type' => $type,
            'timeInForce' => $timeInForce,
            'postOnly' => $postOnly,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'fee' => $fee,
            'average' => null,
            'trades' => null,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $clientOrderId = $this->safe_string_2($params, 'clientOrderId', 'client_oid');
        $method = null;
        if ($clientOrderId === null) {
            $method = 'privateGetOrdersId';
            $request['id'] = $id;
        } else {
            $method = 'privateGetOrdersClientClientOid';
            $request['client_oid'] = $clientOrderId;
            $params = $this->omit($params, array( 'clientOrderId', 'client_oid' ));
        }
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_order_trades($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $request = array(
            'order_id' => $id,
        );
        $response = $this->privateGetFills (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'all',
        );
        return $this->fetch_open_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['product_id'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 100
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'status' => 'done',
        );
        return $this->fetch_open_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            // common $params --------------------------------------------------
            // 'client_oid' => $clientOrderId,
            'type' => $type,
            'side' => $side,
            'product_id' => $market['id'],
            // 'size' => $this->amount_to_precision($symbol, $amount),
            // 'stp' => 'dc', // self-trade prevention, dc = decrease and cancel, co = cancel oldest, cn = cancel newest, cb = cancel both
            // 'stop' => 'loss', // "loss" = stop loss below $price, "entry" = take profit above $price
            // 'stop_price' => $this->price_to_precision($symbol, $price),
            // limit order $params ---------------------------------------------
            // 'price' => $this->price_to_precision($symbol, $price),
            // 'size' => $this->amount_to_precision($symbol, $amount),
            // 'time_in_force' => 'GTC', // GTC, GTT, IOC, or FOK
            // 'cancel_after' [optional]* min, hour, day, requires time_in_force to be GTT
            // 'post_only' => false, // invalid when time_in_force is IOC or FOK
            // $market order $params --------------------------------------------
            // 'size' => $this->amount_to_precision($symbol, $amount),
            // 'funds' => $this->cost_to_precision($symbol, $amount),
        );
        $clientOrderId = $this->safe_string_2($params, 'clientOrderId', 'client_oid');
        if ($clientOrderId !== null) {
            $request['client_oid'] = $clientOrderId;
            $params = $this->omit($params, array( 'clientOrderId', 'client_oid' ));
        }
        $stopPrice = $this->safe_float_2($params, 'stopPrice', 'stop_price');
        if ($stopPrice !== null) {
            $request['stop_price'] = $this->price_to_precision($symbol, $stopPrice);
            $params = $this->omit($params, array( 'stopPrice', 'stop_price' ));
        }
        $timeInForce = $this->safe_string_2($params, 'timeInForce', 'time_in_force');
        if ($timeInForce !== null) {
            $request['time_in_force'] = $timeInForce;
            $params = $this->omit($params, array( 'timeInForce', 'time_in_force' ));
        }
        if ($type === 'limit') {
            $request['price'] = $this->price_to_precision($symbol, $price);
            $request['size'] = $this->amount_to_precision($symbol, $amount);
        } else if ($type === 'market') {
            $cost = $this->safe_float_2($params, 'cost', 'funds');
            if ($cost === null) {
                if ($price !== null) {
                    $cost = $amount * $price;
                }
            } else {
                $params = $this->omit($params, array( 'cost', 'funds' ));
            }
            if ($cost !== null) {
                $request['funds'] = $this->cost_to_precision($symbol, $cost);
            } else {
                $request['size'] = $this->amount_to_precision($symbol, $amount);
            }
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //     {
        //         "id" => "d0c5340b-6d6c-49d9-b567-48c4bfca13d2",
        //         "$price" => "0.10000000",
        //         "size" => "0.01000000",
        //         "product_id" => "BTC-USD",
        //         "$side" => "buy",
        //         "stp" => "dc",
        //         "$type" => "limit",
        //         "time_in_force" => "GTC",
        //         "post_only" => false,
        //         "created_at" => "2016-12-08T20:02:28.53864Z",
        //         "fill_fees" => "0.0000000000000000",
        //         "filled_size" => "0.00000000",
        //         "executed_value" => "0.0000000000000000",
        //         "status" => "pending",
        //         "settled" => false
        //     }
        //
        return $this->parse_order($response, $market);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'product_id' => $market['id'], // the $request will be more performant if you include it
        );
        $clientOrderId = $this->safe_string_2($params, 'clientOrderId', 'client_oid');
        $method = null;
        if ($clientOrderId === null) {
            $method = 'privateDeleteOrdersId';
            $request['id'] = $id;
        } else {
            $method = 'privateDeleteOrdersClientClientOid';
            $request['client_oid'] = $clientOrderId;
            $params = $this->omit($params, array( 'clientOrderId', 'client_oid' ));
        }
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['product_id'] = $market['symbol']; // the $request will be more performant if you include it
        }
        return $this->$method (array_merge($request, $params));
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['product_id'] = $market['symbol']; // the $request will be more performant if you include it
        }
        return $this->privateDeleteOrders (array_merge($request, $params));
    }

    public function calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $rate = $market[$takerOrMaker];
        $cost = $amount * $price;
        $currency = $market['quote'];
        return array(
            'type' => $takerOrMaker,
            'currency' => $currency,
            'rate' => $rate,
            'cost' => floatval($this->currency_to_precision($currency, $rate * $cost)),
        );
    }

    public function fetch_payment_methods($params = array ()) {
        return $this->privateGetPaymentMethods ($params);
    }

    public function deposit($code, $amount, $address, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'amount' => $amount,
        );
        $method = 'privatePostDeposits';
        if (is_array($params) && array_key_exists('payment_method_id', $params)) {
            // deposit from a payment_method, like a bank account
            $method .= 'PaymentMethod';
        } else if (is_array($params) && array_key_exists('coinbase_account_id', $params)) {
            // deposit into Coinbase Pro account from a Coinbase account
            $method .= 'CoinbaseAccount';
        } else {
            // deposit methodotherwise we did not receive a supported deposit location
            // relevant docs link for the Googlers
            // https://docs.pro.coinbase.com/#deposits
            throw new NotSupported($this->id . ' deposit() requires one of `coinbase_account_id` or `payment_method_id` extra params');
        }
        $response = $this->$method (array_merge($request, $params));
        if (!$response) {
            throw new ExchangeError($this->id . ' deposit() error => ' . $this->json($response));
        }
        return array(
            'info' => $response,
            'id' => $response['id'],
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
            'amount' => $amount,
        );
        $method = 'privatePostWithdrawals';
        if (is_array($params) && array_key_exists('payment_method_id', $params)) {
            $method .= 'PaymentMethod';
        } else if (is_array($params) && array_key_exists('coinbase_account_id', $params)) {
            $method .= 'CoinbaseAccount';
        } else {
            $method .= 'Crypto';
            $request['crypto_address'] = $address;
        }
        $response = $this->$method (array_merge($request, $params));
        if (!$response) {
            throw new ExchangeError($this->id . ' withdraw() error => ' . $this->json($response));
        }
        return array(
            'info' => $response,
            'id' => $response['id'],
        );
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $currency = null;
        $id = $this->safe_string($params, 'id'); // $account $id
        if ($id === null) {
            if ($code !== null) {
                $currency = $this->currency($code);
                $accountsByCurrencyCode = $this->index_by($this->accounts, 'currency');
                $account = $this->safe_value($accountsByCurrencyCode, $code);
                if ($account === null) {
                    throw new ExchangeError($this->id . ' fetchTransactions() could not find $account $id for ' . $code);
                }
                $id = $account['id'];
            }
        }
        $request = array();
        if ($id !== null) {
            $request['id'] = $id;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = null;
        if ($id === null) {
            $response = $this->privateGetTransfers (array_merge($request, $params));
            for ($i = 0; $i < count($response); $i++) {
                $account_id = $this->safe_string($response[$i], 'account_id');
                $account = $this->safe_value($this->accountsById, $account_id);
                $code = $this->safe_string($account, 'currency');
                $response[$i]['currency'] = $code;
            }
        } else {
            $response = $this->privateGetAccountsIdTransfers (array_merge($request, $params));
            for ($i = 0; $i < count($response); $i++) {
                $response[$i]['currency'] = $code;
            }
        }
        return $this->parse_transactions($response, $currency, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions($code, $since, $limit, array_merge($params, array( 'type' => 'deposit' )));
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions($code, $since, $limit, array_merge($params, array( 'type' => 'withdraw' )));
    }

    public function parse_transaction_status($transaction) {
        $canceled = $this->safe_value($transaction, 'canceled_at');
        if ($canceled) {
            return 'canceled';
        }
        $processed = $this->safe_value($transaction, 'processed_at');
        $completed = $this->safe_value($transaction, 'completed_at');
        if ($completed) {
            return 'ok';
        } else if ($processed && !$completed) {
            return 'failed';
        } else {
            return 'pending';
        }
    }

    public function parse_transaction($transaction, $currency = null) {
        $details = $this->safe_value($transaction, 'details', array());
        $id = $this->safe_string($transaction, 'id');
        $txid = $this->safe_string($details, 'crypto_transaction_hash');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'created_at'));
        $updated = $this->parse8601($this->safe_string($transaction, 'processed_at'));
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $status = $this->parse_transaction_status($transaction);
        $amount = $this->safe_float($transaction, 'amount');
        $type = $this->safe_string($transaction, 'type');
        $address = $this->safe_string($details, 'crypto_address');
        $tag = $this->safe_string($details, 'destination_tag');
        $address = $this->safe_string($transaction, 'crypto_address', $address);
        $fee = null;
        if ($type === 'withdraw') {
            $type = 'withdrawal';
            $address = $this->safe_string($details, 'sent_to_address', $address);
            $feeCost = $this->safe_float($details, 'fee');
            if ($feeCost !== null) {
                if ($amount !== null) {
                    $amount -= $feeCost;
                }
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $code,
                );
            }
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'tag' => $tag,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $request = '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($method === 'GET') {
            if ($query) {
                $request .= '?' . $this->urlencode($query);
            }
        }
        $url = $this->urls['api'][$api] . $request;
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $payload = '';
            if ($method !== 'GET') {
                if ($query) {
                    $body = $this->json($query);
                    $payload = $body;
                }
            }
            $what = $nonce . $method . $request . $payload;
            $secret = base64_decode($this->secret);
            $signature = $this->hmac($this->encode($what), $secret, 'sha256', 'base64');
            $headers = array(
                'CB-ACCESS-KEY' => $this->apiKey,
                'CB-ACCESS-SIGN' => $signature,
                'CB-ACCESS-TIMESTAMP' => $nonce,
                'CB-ACCESS-PASSPHRASE' => $this->password,
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $accounts = $this->safe_value($this->options, 'coinbaseAccounts');
        if ($accounts === null) {
            $accounts = $this->privateGetCoinbaseAccounts ();
            $this->options['coinbaseAccounts'] = $accounts; // cache it
            $this->options['coinbaseAccountsByCurrencyId'] = $this->index_by($accounts, 'currency');
        }
        $currencyId = $currency['id'];
        $account = $this->safe_value($this->options['coinbaseAccountsByCurrencyId'], $currencyId);
        if ($account === null) {
            // eslint-disable-next-line quotes
            throw new InvalidAddress($this->id . " fetchDepositAddress() could not find $currency $code " . $code . " with id = " . $currencyId . " in $this->options['coinbaseAccountsByCurrencyId']");
        }
        $request = array(
            'id' => $account['id'],
        );
        $response = $this->privatePostCoinbaseAccountsIdAddresses (array_merge($request, $params));
        $address = $this->safe_string($response, 'address');
        $tag = $this->safe_string($response, 'destination_tag');
        return array(
            'currency' => $code,
            'address' => $this->check_address($address),
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (($code === 400) || ($code === 404)) {
            if ($body[0] === '{') {
                $message = $this->safe_string($response, 'message');
                $feedback = $this->id . ' ' . $message;
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
                throw new ExchangeError($feedback); // unknown $message
            }
            throw new ExchangeError($this->id . ' ' . $body);
        }
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (gettype($response) !== 'string') {
            if (is_array($response) && array_key_exists('message', $response)) {
                throw new ExchangeError($this->id . ' ' . $this->json($response));
            }
        }
        return $response;
    }
}

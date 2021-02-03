<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\OrderNotFound;
use \ccxt\NotSupported;
use \ccxt\DDoSProtection;

class liquid extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'liquid',
            'name' => 'Liquid',
            'countries' => array( 'JP', 'CN', 'TW' ),
            'version' => '2',
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'withdraw' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/45798859-1a872600-bcb4-11e8-8746-69291ce87b04.jpg',
                'api' => 'https://api.liquid.com',
                'www' => 'https://www.liquid.com',
                'doc' => array(
                    'https://developers.liquid.com',
                ),
                'fees' => 'https://help.liquid.com/getting-started-with-liquid/the-platform/fee-structure',
                'referral' => 'https://www.liquid.com/sign-up/?affiliate=SbzC62lt30976',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencies',
                        'products',
                        'products/{id}',
                        'products/{id}/price_levels',
                        'executions',
                        'ir_ladders/{currency}',
                        'fees', // add fetchFees, fetchTradingFees, fetchFundingFees
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts', // undocumented https://github.com/ccxt/ccxt/pull/7493
                        'accounts/balance',
                        'accounts/main_asset',
                        'accounts/{id}',
                        'accounts/{currency}/reserved_balance_details',
                        'crypto_accounts', // add fetchAccounts
                        'crypto_withdrawals', // add fetchWithdrawals
                        'executions/me',
                        'fiat_accounts', // add fetchAccounts
                        'fund_infos', // add fetchDeposits
                        'loan_bids',
                        'loans',
                        'orders',
                        'orders/{id}',
                        'orders/{id}/trades', // add fetchOrderTrades
                        'trades',
                        'trades/{id}/loans',
                        'trading_accounts',
                        'trading_accounts/{id}',
                        'transactions',
                        'withdrawals', // add fetchWithdrawals
                    ),
                    'post' => array(
                        'crypto_withdrawals',
                        'fund_infos',
                        'fiat_accounts',
                        'loan_bids',
                        'orders',
                        'withdrawals',
                    ),
                    'put' => array(
                        'crypto_withdrawal/{id}/cancel',
                        'loan_bids/{id}/close',
                        'loans/{id}',
                        'orders/{id}', // add editOrder
                        'orders/{id}/cancel',
                        'trades/{id}',
                        'trades/{id}/adjust_margin',
                        'trades/{id}/close',
                        'trades/close_all',
                        'trading_accounts/{id}',
                        'withdrawals/{id}/cancel',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.0015,
                    'maker' => 0.0000,
                    'tiers' => array(
                        'perpetual' => array(
                            'maker' => array(
                                array( 0, 0.0000 ),
                                array( 25000, 0.0000 ),
                                array( 50000, -0.00025 ),
                                array( 100000, -0.00025 ),
                                array( 1000000, -0.00025 ),
                                array( 10000000, -0.00025 ),
                                array( 25000000, -0.00025 ),
                                array( 50000000, -0.00025 ),
                                array( 75000000, -0.00025 ),
                                array( 100000000, -0.00025 ),
                                array( 200000000, -0.00025 ),
                                array( 300000000, -0.00025 ),
                            ),
                            'taker' => array(
                                array( 0, 0.000600 ),
                                array( 25000, 0.000575 ),
                                array( 50000, 0.000550 ),
                                array( 100000, 0.000525 ),
                                array( 1000000, 0.000500 ),
                                array( 10000000, 0.000475 ),
                                array( 25000000, 0.000450 ),
                                array( 50000000, 0.000425 ),
                                array( 75000000, 0.000400 ),
                                array( 100000000, 0.000375 ),
                                array( 200000000, 0.000350 ),
                                array( 300000000, 0.000325 ),
                            ),
                        ),
                        'spot' => array(
                            'taker' => array(
                                array( 0, 0.0015 ),
                                array( 10000, 0.0015 ),
                                array( 20000, 0.0014 ),
                                array( 50000, 0.0013 ),
                                array( 100000, 0.0010 ),
                                array( 1000000, 0.0008 ),
                                array( 5000000, 0.0006 ),
                                array( 10000000, 0.0005 ),
                                array( 25000000, 0.0005 ),
                                array( 50000000, 0.00045 ),
                                array( 100000000, 0.0004 ),
                                array( 200000000, 0.0003 ),
                            ),
                            'maker' => array(
                                array( 0, 0.0000 ),
                                array( 10000, 0.0015 ),
                                array( 20000, 0.1400 ),
                                array( 50000, 0.1300 ),
                                array( 100000, 0.0800 ),
                                array( 1000000, 0.0004 ),
                                array( 5000000, 0.00035 ),
                                array( 10000000, 0.00025 ),
                                array( 25000000, 0.0000 ),
                                array( 50000000, 0.0000 ),
                                array( 100000000, 0.0000 ),
                                array( 200000000, 0.0000 ),
                            ),
                        ),
                    ),
                ),
            ),
            'precisionMode' => TICK_SIZE,
            'exceptions' => array(
                'API rate limit exceeded. Please retry after 300s' => '\\ccxt\\DDoSProtection',
                'API Authentication failed' => '\\ccxt\\AuthenticationError',
                'Nonce is too small' => '\\ccxt\\InvalidNonce',
                'Order not found' => '\\ccxt\\OrderNotFound',
                'Can not update partially filled order' => '\\ccxt\\InvalidOrder',
                'Can not update non-live order' => '\\ccxt\\OrderNotFound',
                'not_enough_free_balance' => '\\ccxt\\InsufficientFunds',
                'must_be_positive' => '\\ccxt\\InvalidOrder',
                'less_than_order_size' => '\\ccxt\\InvalidOrder',
                'price_too_high' => '\\ccxt\\InvalidOrder',
                'price_too_small' => '\\ccxt\\InvalidOrder', // array("errors":array("order":["price_too_small"]))
            ),
            'commonCurrencies' => array(
                'WIN' => 'WCOIN',
                'HOT' => 'HOT Token',
                'MIOTA' => 'IOTA', // https://github.com/ccxt/ccxt/issues/7487
            ),
            'options' => array(
                'cancelOrderException' => true,
            ),
        ));
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     array(
        //         array(
        //             currency_type => 'fiat',
        //             $currency => 'USD',
        //             symbol => '$',
        //             assets_precision => 2,
        //             quoting_precision => 5,
        //             minimum_withdrawal => '15.0',
        //             withdrawal_fee => 5,
        //             minimum_fee => null,
        //             minimum_order_quantity => null,
        //             display_precision => 2,
        //             depositable => true,
        //             withdrawable => true,
        //             discount_fee => 0.5,
        //         ),
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'currency');
            $code = $this->safe_currency_code($id);
            $active = $currency['depositable'] && $currency['withdrawable'];
            $amountPrecision = $this->safe_integer($currency, 'display_precision');
            $pricePrecision = $this->safe_integer($currency, 'quoting_precision');
            $precision = max ($amountPrecision, $pricePrecision);
            $decimalPrecision = 1 / pow(10, $precision);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $code,
                'active' => $active,
                'fee' => $this->safe_float($currency, 'withdrawal_fee'),
                'precision' => $decimalPrecision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$amountPrecision),
                        'max' => pow(10, $amountPrecision),
                    ),
                    'price' => array(
                        'min' => pow(10, -$pricePrecision),
                        'max' => pow(10, $pricePrecision),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_float($currency, 'minimum_withdrawal'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $spot = $this->publicGetProducts ($params);
        //
        //     array(
        //         array(
        //             "$id":"637",
        //             "product_type":"CurrencyPair",
        //             "code":"CASH",
        //             "name":null,
        //             "market_ask":"0.00000797",
        //             "market_bid":"0.00000727",
        //             "indicator":null,
        //             "currency":"BTC",
        //             "currency_pair_code":"TFTBTC",
        //             "$symbol":null,
        //             "btc_minimum_withdraw":null,
        //             "fiat_minimum_withdraw":null,
        //             "pusher_channel":"product_cash_tftbtc_637",
        //             "taker_fee":"0.0",
        //             "maker_fee":"0.0",
        //             "low_market_bid":"0.00000685",
        //             "high_market_ask":"0.00000885",
        //             "volume_24h":"3696.0755956",
        //             "last_price_24h":"0.00000716",
        //             "last_traded_price":"0.00000766",
        //             "last_traded_quantity":"1748.0377978",
        //             "average_price":null,
        //             "quoted_currency":"BTC",
        //             "base_currency":"TFT",
        //             "tick_size":"0.00000001",
        //             "$disabled":false,
        //             "margin_enabled":false,
        //             "cfd_enabled":false,
        //             "perpetual_enabled":false,
        //             "last_event_timestamp":"1596962820.000797146",
        //             "timestamp":"1596962820.000797146",
        //             "multiplier_up":"9.0",
        //             "multiplier_down":"0.1",
        //             "average_time_interval":null
        //         ),
        //     )
        //
        $perpetual = $this->publicGetProducts (array( 'perpetual' => '1' ));
        //
        //     array(
        //         array(
        //             "$id":"604",
        //             "product_type":"Perpetual",
        //             "code":"CASH",
        //             "name":null,
        //             "market_ask":"11721.5",
        //             "market_bid":"11719.0",
        //             "indicator":null,
        //             "currency":"USD",
        //             "currency_pair_code":"P-BTCUSD",
        //             "$symbol":"$",
        //             "btc_minimum_withdraw":null,
        //             "fiat_minimum_withdraw":null,
        //             "pusher_channel":"product_cash_p-btcusd_604",
        //             "taker_fee":"0.0012",
        //             "maker_fee":"0.0",
        //             "low_market_bid":"11624.5",
        //             "high_market_ask":"11859.0",
        //             "volume_24h":"0.271",
        //             "last_price_24h":"11621.5",
        //             "last_traded_price":"11771.5",
        //             "last_traded_quantity":"0.09",
        //             "average_price":"11771.5",
        //             "quoted_currency":"USD",
        //             "base_currency":"P-BTC",
        //             "tick_size":"0.5",
        //             "$disabled":false,
        //             "margin_enabled":false,
        //             "cfd_enabled":false,
        //             "perpetual_enabled":true,
        //             "last_event_timestamp":"1596963309.418853092",
        //             "timestamp":"1596963309.418853092",
        //             "multiplier_up":null,
        //             "multiplier_down":"0.1",
        //             "average_time_interval":300,
        //             "index_price":"11682.8124",
        //             "mark_price":"11719.96781",
        //             "funding_rate":"0.00273",
        //             "fair_price":"11720.2745"
        //         ),
        //     )
        //
        $currencies = $this->fetch_currencies();
        $currenciesByCode = $this->index_by($currencies, 'code');
        $result = array();
        $markets = $this->array_concat($spot, $perpetual);
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'id');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quoted_currency');
            $productType = $this->safe_string($market, 'product_type');
            $type = 'spot';
            $spot = true;
            $swap = false;
            if ($productType === 'Perpetual') {
                $spot = false;
                $swap = true;
                $type = 'swap';
            }
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = null;
            if ($swap) {
                $symbol = $this->safe_string($market, 'currency_pair_code');
            } else {
                $symbol = $base . '/' . $quote;
            }
            $maker = $this->fees['trading']['maker'];
            $taker = $this->fees['trading']['taker'];
            if ($type === 'swap') {
                $maker = $this->safe_float($market, 'maker_fee', $this->fees['trading']['maker']);
                $taker = $this->safe_float($market, 'taker_fee', $this->fees['trading']['taker']);
            }
            $disabled = $this->safe_value($market, 'disabled', false);
            $active = !$disabled;
            $baseCurrency = $this->safe_value($currenciesByCode, $base);
            $precision = array(
                'amount' => 0.00000001,
                'price' => $this->safe_float($market, 'tick_size'),
            );
            $minAmount = null;
            if ($baseCurrency !== null) {
                $minAmount = $this->safe_float($baseCurrency['info'], 'minimum_order_quantity');
            }
            $limits = array(
                'amount' => array(
                    'min' => $minAmount,
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
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'type' => $type,
                'spot' => $spot,
                'swap' => $swap,
                'maker' => $maker,
                'taker' => $taker,
                'limits' => $limits,
                'precision' => $precision,
                'active' => $active,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetAccounts ($params);
        //
        //     {
        //         crypto_accounts => array(
        //             array(
        //                 id => 2221179,
        //                 currency => 'USDT',
        //                 $balance => '0.0',
        //                 reserved_balance => '0.0',
        //                 pusher_channel => 'user_xxxxx_account_usdt',
        //                 lowest_offer_interest_rate => null,
        //                 highest_offer_interest_rate => null,
        //                 address => '0',
        //                 currency_symbol => 'USDT',
        //                 minimum_withdraw => null,
        //                 currency_type => 'crypto'
        //             ),
        //         ),
        //         fiat_accounts => array(
        //             {
        //                 id => 1112734,
        //                 currency => 'USD',
        //                 $balance => '0.0',
        //                 reserved_balance => '0.0',
        //                 pusher_channel => 'user_xxxxx_account_usd',
        //                 lowest_offer_interest_rate => null,
        //                 highest_offer_interest_rate => null,
        //                 currency_symbol => '$',
        //                 send_to_btc_address => null,
        //                 exchange_rate => '1.0',
        //                 currency_type => 'fiat'
        //             }
        //         )
        //     }
        //
        $result = array( 'info' => $response );
        $crypto = $this->safe_value($response, 'crypto_accounts', array());
        $fiat = $this->safe_value($response, 'fiat_accounts', array());
        for ($i = 0; $i < count($crypto); $i++) {
            $balance = $crypto[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_float($balance, 'balance');
            $account['used'] = $this->safe_float($balance, 'reserved_balance');
            $result[$code] = $account;
        }
        for ($i = 0; $i < count($fiat); $i++) {
            $balance = $fiat[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['total'] = $this->safe_float($balance, 'balance');
            $account['used'] = $this->safe_float($balance, 'reserved_balance');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $response = $this->publicGetProductsIdPriceLevels (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'buy_price_levels', 'sell_price_levels');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $last = null;
        if (is_array($ticker) && array_key_exists('last_traded_price', $ticker)) {
            if ($ticker['last_traded_price']) {
                $length = is_array($ticker['last_traded_price']) ? count($ticker['last_traded_price']) : 0;
                if ($length > 0) {
                    $last = $this->safe_float($ticker, 'last_traded_price');
                }
            }
        }
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($ticker, 'id');
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                $baseId = $this->safe_string($ticker, 'base_currency');
                $quoteId = $this->safe_string($ticker, 'quoted_currency');
                if (is_array($this->markets) && array_key_exists($symbol, $this->markets)) {
                    $market = $this->markets[$symbol];
                } else {
                    $symbol = $this->safe_currency_code($baseId) . '/' . $this->safe_currency_code($quoteId);
                }
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $change = null;
        $percentage = null;
        $average = null;
        $open = $this->safe_float($ticker, 'last_price_24h');
        if ($open !== null && $last !== null) {
            $change = $last - $open;
            $average = $this->sum($last, $open) / 2;
            if ($open > 0) {
                $percentage = $change / $open * 100;
            }
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high_market_ask'),
            'low' => $this->safe_float($ticker, 'low_market_bid'),
            'bid' => $this->safe_float($ticker, 'market_bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'market_ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'volume_24h'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetProducts ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        $response = $this->publicGetProductsId (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market = null) {
        // {             $id =>  12345,
        //         quantity => "6.789",
        //            $price => "98765.4321",
        //       taker_side => "sell",
        //       created_at =>  1512345678,
        //          my_side => "buy"           }
        $timestamp = $this->safe_timestamp($trade, 'created_at');
        $orderId = $this->safe_string($trade, 'order_id');
        // 'taker_side' gets filled for both fetchTrades and fetchMyTrades
        $takerSide = $this->safe_string($trade, 'taker_side');
        // 'my_side' gets filled for fetchMyTrades only and may differ from 'taker_side'
        $mySide = $this->safe_string($trade, 'my_side');
        $side = ($mySide !== null) ? $mySide : $takerSide;
        $takerOrMaker = null;
        if ($mySide !== null) {
            $takerOrMaker = ($takerSide === $mySide) ? 'taker' : 'maker';
        }
        $cost = null;
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'quantity');
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $id = $this->safe_string($trade, 'id');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
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
            'product_id' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        if ($since !== null) {
            // timestamp should be in seconds, whereas we use milliseconds in $since and everywhere
            $request['timestamp'] = intval($since / 1000);
        }
        $response = $this->publicGetExecutions (array_merge($request, $params));
        $result = ($since !== null) ? $response : $response['models'];
        return $this->parse_trades($result, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        // the `with_details` param is undocumented - it adds the order_id to the results
        $request = array(
            'product_id' => $market['id'],
            'with_details' => true,
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetExecutionsMe (array_merge($request, $params));
        return $this->parse_trades($response['models'], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $clientOrderId = $this->safe_string_2($params, 'clientOrderId', 'client_order_id');
        $params = $this->omit($params, array( 'clientOrderId', 'client_order_id' ));
        $request = array(
            'order_type' => $type,
            'product_id' => $this->market_id($symbol),
            'side' => $side,
            'quantity' => $this->amount_to_precision($symbol, $amount),
        );
        if ($clientOrderId !== null) {
            $request['client_order_id'] = $clientOrderId;
        }
        if (($type === 'limit') || ($type === 'limit_post_only') || ($type === 'market_with_range') || ($type === 'stop')) {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //     {
        //         "id" => 2157474,
        //         "order_type" => "limit",
        //         "quantity" => "0.01",
        //         "disc_quantity" => "0.0",
        //         "iceberg_total_quantity" => "0.0",
        //         "$side" => "sell",
        //         "filled_quantity" => "0.0",
        //         "$price" => "500.0",
        //         "created_at" => 1462123639,
        //         "updated_at" => 1462123639,
        //         "status" => "live",
        //         "leverage_level" => 1,
        //         "source_exchange" => "QUOINE",
        //         "product_id" => 1,
        //         "product_code" => "CASH",
        //         "funding_currency" => "USD",
        //         "currency_pair_code" => "BTCUSD",
        //         "order_fee" => "0.0",
        //         "client_order_id" => null,
        //     }
        //
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privatePutOrdersIdCancel (array_merge($request, $params));
        $order = $this->parse_order($response);
        if ($order['status'] === 'closed') {
            if ($this->options['cancelOrderException']) {
                throw new OrderNotFound($this->id . ' $order closed already => ' . $this->json($response));
            }
        }
        return $order;
    }

    public function edit_order($id, $symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($price === null) {
            throw new ArgumentsRequired($this->id . ' editOrder() requires the $price argument');
        }
        $request = array(
            'order' => array(
                'quantity' => $this->amount_to_precision($symbol, $amount),
                'price' => $this->price_to_precision($symbol, $price),
            ),
            'id' => $id,
        );
        $response = $this->privatePutOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'live' => 'open',
            'filled' => 'closed',
            'cancelled' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "id" => 2157474,
        //         "order_type" => "limit",
        //         "quantity" => "0.01",
        //         "disc_quantity" => "0.0",
        //         "iceberg_total_quantity" => "0.0",
        //         "$side" => "sell",
        //         "filled_quantity" => "0.0",
        //         "$price" => "500.0",
        //         "created_at" => 1462123639,
        //         "updated_at" => 1462123639,
        //         "$status" => "live",
        //         "leverage_level" => 1,
        //         "source_exchange" => "QUOINE",
        //         "product_id" => 1,
        //         "product_code" => "CASH",
        //         "funding_currency" => "USD",
        //         "currency_pair_code" => "BTCUSD",
        //         "order_fee" => "0.0"
        //         "client_order_id" => null,
        //     }
        //
        // fetchOrder, fetchOrders, fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "id" => 2157479,
        //         "order_type" => "limit",
        //         "quantity" => "0.01",
        //         "disc_quantity" => "0.0",
        //         "iceberg_total_quantity" => "0.0",
        //         "$side" => "sell",
        //         "filled_quantity" => "0.01",
        //         "$price" => "500.0",
        //         "created_at" => 1462123639,
        //         "updated_at" => 1462123639,
        //         "$status" => "$filled",
        //         "leverage_level" => 2,
        //         "source_exchange" => "QUOINE",
        //         "product_id" => 1,
        //         "product_code" => "CASH",
        //         "funding_currency" => "USD",
        //         "currency_pair_code" => "BTCUSD",
        //         "order_fee" => "0.0",
        //         "executions" => array(
        //             {
        //                 "id" => 4566133,
        //                 "quantity" => "0.01",
        //                 "$price" => "500.0",
        //                 "taker_side" => "buy",
        //                 "my_side" => "sell",
        //                 "created_at" => 1465396785
        //             }
        //         )
        //     }
        //
        $orderId = $this->safe_string($order, 'id');
        $timestamp = $this->safe_timestamp($order, 'created_at');
        $marketId = $this->safe_string($order, 'product_id');
        $market = $this->safe_value($this->markets_by_id, $marketId);
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $amount = $this->safe_float($order, 'quantity');
        $filled = $this->safe_float($order, 'filled_quantity');
        $price = $this->safe_float($order, 'price');
        $symbol = null;
        $feeCurrency = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            $feeCurrency = $market['quote'];
        }
        $type = $this->safe_string($order, 'order_type');
        $tradeCost = 0;
        $tradeFilled = 0;
        $average = $this->safe_float($order, 'average_price');
        $trades = $this->parse_trades($this->safe_value($order, 'executions', array()), $market, null, null, array(
            'order' => $orderId,
            'type' => $type,
        ));
        $numTrades = is_array($trades) ? count($trades) : 0;
        for ($i = 0; $i < $numTrades; $i++) {
            // php copies values upon assignment, but not references them
            // todo rewrite this (shortly)
            $trade = $trades[$i];
            $trade['order'] = $orderId;
            $trade['type'] = $type;
            $tradeFilled = $this->sum($tradeFilled, $trade['amount']);
            $tradeCost = $this->sum($tradeCost, $trade['cost']);
        }
        $cost = null;
        $lastTradeTimestamp = null;
        if ($numTrades > 0) {
            $lastTradeTimestamp = $trades[$numTrades - 1]['timestamp'];
            if (!$average && ($tradeFilled > 0)) {
                $average = $tradeCost / $tradeFilled;
            }
            if ($cost === null) {
                $cost = $tradeCost;
            }
            if ($filled === null) {
                $filled = $tradeFilled;
            }
        }
        $remaining = null;
        if ($amount !== null && $filled !== null) {
            $remaining = $amount - $filled;
        }
        $side = $this->safe_string($order, 'side');
        $clientOrderId = $this->safe_string($order, 'client_order_id');
        return array(
            'id' => $orderId,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'status' => $status,
            'symbol' => $symbol,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'filled' => $filled,
            'cost' => $cost,
            'remaining' => $remaining,
            'average' => $average,
            'trades' => $trades,
            'fee' => array(
                'currency' => $feeCurrency,
                'cost' => $this->safe_float($order, 'order_fee'),
            ),
            'info' => $order,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array(
            // 'funding_currency' => $market['quoteId'], // filter $orders based on "funding" currency (quote currency)
            // 'product_id' => $market['id'],
            // 'status' => 'live', // 'filled', 'cancelled'
            // 'trading_type' => 'spot', // 'margin', 'cfd'
            'with_details' => 1, // return full order details including executions
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['product_id'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetOrders (array_merge($request, $params));
        //
        //     {
        //         "models" => array(
        //             {
        //                 "id" => 2157474,
        //                 "order_type" => "$limit",
        //                 "quantity" => "0.01",
        //                 "disc_quantity" => "0.0",
        //                 "iceberg_total_quantity" => "0.0",
        //                 "side" => "sell",
        //                 "filled_quantity" => "0.0",
        //                 "price" => "500.0",
        //                 "created_at" => 1462123639,
        //                 "updated_at" => 1462123639,
        //                 "status" => "live",
        //                 "leverage_level" => 1,
        //                 "source_exchange" => "QUOINE",
        //                 "product_id" => 1,
        //                 "product_code" => "CASH",
        //                 "funding_currency" => "USD",
        //                 "currency_pair_code" => "BTCUSD",
        //                 "order_fee" => "0.0",
        //                 "executions" => array(), // optional
        //             }
        //         ),
        //         "current_page" => 1,
        //         "total_pages" => 1
        //     }
        //
        $orders = $this->safe_value($response, 'models', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array( 'status' => 'live' );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array( 'status' => 'filled' );
        return $this->fetch_orders($symbol, $since, $limit, array_merge($request, $params));
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            // 'auth_code' => '', // optional 2fa $code
            'currency' => $currency['id'],
            'address' => $address,
            'amount' => $this->currency_to_precision($code, $amount),
            // 'payment_id' => $tag, // for XRP only
            // 'memo_type' => 'text', // 'text', 'id' or 'hash', for XLM only
            // 'memo_value' => $tag, // for XLM only
        );
        if ($tag !== null) {
            if ($code === 'XRP') {
                $request['payment_id'] = $tag;
            } else if ($code === 'XLM') {
                $request['memo_type'] = 'text'; // overrideable via $params
                $request['memo_value'] = $tag;
            } else {
                throw new NotSupported($this->id . ' withdraw() only supports a $tag along the $address for XRP or XLM');
            }
        }
        $response = $this->privatePostCryptoWithdrawals (array_merge($request, $params));
        //
        //     {
        //         "id" => 1353,
        //         "$address" => "1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2",
        //         "$amount" => 1.0,
        //         "state" => "pending",
        //         "$currency" => "BTC",
        //         "withdrawal_fee" => 0.0,
        //         "created_at" => 1568016450,
        //         "updated_at" => 1568016450,
        //         "payment_id" => null
        //     }
        //
        return $this->parse_transaction($response, $currency);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'pending' => 'pending',
            'cancelled' => 'canceled',
            'approved' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // withdraw
        //
        //     {
        //         "$id" => 1353,
        //         "$address" => "1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2",
        //         "$amount" => 1.0,
        //         "state" => "pending",
        //         "$currency" => "BTC",
        //         "withdrawal_fee" => 0.0,
        //         "created_at" => 1568016450,
        //         "updated_at" => 1568016450,
        //         "payment_id" => null
        //     }
        //
        // fetchDeposits, fetchWithdrawals
        //
        //     ...
        //
        $id = $this->safe_string($transaction, 'id');
        $address = $this->safe_string($transaction, 'address');
        $tag = $this->safe_string_2($transaction, 'payment_id', 'memo_value');
        $txid = null;
        $currencyId = $this->safe_string($transaction, 'asset');
        $code = $this->safe_currency_code($currencyId, $currency);
        $timestamp = $this->safe_timestamp($transaction, 'created_at');
        $updated = $this->safe_timestamp($transaction, 'updated_at');
        $type = 'withdrawal';
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'state'));
        $amount = $this->safe_float($transaction, 'amount');
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
            'fee' => null,
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        $headers = array(
            'X-Quoine-API-Version' => $this->version,
            'Content-Type' => 'application/json',
        );
        if ($api === 'private') {
            $this->check_required_credentials();
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            } else if ($query) {
                $body = $this->json($query);
            }
            $nonce = $this->nonce();
            $request = array(
                'path' => $url,
                'token_id' => $this->apiKey,
                'iat' => (int) floor($nonce / 1000), // issued at
            );
            if (!(is_array($query) && array_key_exists('client_order_id', $query))) {
                $request['nonce'] = $nonce;
            }
            $headers['X-Quoine-Auth'] = $this->jwt($request, $this->encode($this->secret));
        } else {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        }
        $url = $this->urls['api'] . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($code >= 200 && $code < 300) {
            return;
        }
        if ($code === 401) {
            // expected non-json $response
            $this->throw_exactly_matched_exception($this->exceptions, $body, $body);
            return;
        }
        if ($code === 429) {
            throw new DDoSProtection($this->id . ' ' . $body);
        }
        if ($response === null) {
            return;
        }
        $feedback = $this->id . ' ' . $body;
        $message = $this->safe_string($response, 'message');
        $errors = $this->safe_value($response, 'errors');
        if ($message !== null) {
            //
            //  array( "$message" => "Order not found" )
            //
            $this->throw_exactly_matched_exception($this->exceptions, $message, $feedback);
        } else if ($errors !== null) {
            //
            //  array( "$errors" => array( "user" => ["not_enough_free_balance"] ))
            //  array( "$errors" => array( "quantity" => ["less_than_order_size"] ))
            //  array( "$errors" => array( "order" => ["Can not update partially filled order"] ))
            //
            $types = is_array($errors) ? array_keys($errors) : array();
            for ($i = 0; $i < count($types); $i++) {
                $type = $types[$i];
                $errorMessages = $errors[$type];
                for ($j = 0; $j < count($errorMessages); $j++) {
                    $message = $errorMessages[$j];
                    $this->throw_exactly_matched_exception($this->exceptions, $message, $feedback);
                }
            }
        } else {
            throw new ExchangeError($feedback);
        }
    }
}

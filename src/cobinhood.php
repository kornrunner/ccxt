<?php

namespace ccxt;

use Exception as Exception; // a common import

class cobinhood extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'cobinhood',
            'name' => 'COBINHOOD',
            'countries' => array ( 'TW' ),
            'rateLimit' => 1000 / 10,
            'version' => 'v1',
            'has' => array (
                'fetchCurrencies' => true,
                'fetchTickers' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchOrderTrades' => true,
                'fetchOrder' => true,
                'fetchDepositAddress' => true,
                'createDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
                'fetchMyTrades' => true,
                'editOrder' => true,
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => false,
            ),
            'timeframes' => array (
                // the first two don't seem to work at all
                '1m' => '1m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '3h' => '3h',
                '6h' => '6h',
                '12h' => '12h',
                '1d' => '1D',
                '1w' => '7D',
                '2w' => '14D',
                '1M' => '1M',
            ),
            'urls' => array (
                'referral' => 'https://cobinhood.com?referrerId=a9d57842-99bb-4d7c-b668-0479a15a458b',
                'logo' => 'https://user-images.githubusercontent.com/1294454/35755576-dee02e5c-0878-11e8-989f-1595d80ba47f.jpg',
                'api' => 'https://api.cobinhood.com',
                'www' => 'https://cobinhood.com',
                'doc' => 'https://cobinhood.github.io/api-public',
            ),
            'api' => array (
                'system' => array (
                    'get' => array (
                        'info',
                        'time',
                        'messages',
                        'messages/{message_id}',
                    ),
                ),
                'admin' => array (
                    'get' => array (
                        'system/messages',
                        'system/messages/{message_id}',
                    ),
                    'post' => array (
                        'system/messages',
                    ),
                    'patch' => array (
                        'system/messages/{message_id}',
                    ),
                    'delete' => array (
                        'system/messages/{message_id}',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'market/fundingbook/precisions/{currency_id}',
                        'market/fundingbooks/{currency_id}',
                        'market/tickers',
                        'market/currencies',
                        'market/quote_currencies',
                        'market/trading_pairs',
                        'market/orderbook/precisions/{trading_pair_id}',
                        'market/orderbooks/{trading_pair_id}',
                        'market/stats',
                        'market/tickers', // fetchTickers
                        'market/tickers/{trading_pair_id}',
                        'market/trades/{trading_pair_id}',
                        'market/trades_history/{trading_pair_id}',
                        'market/trading_pairs',
                        'chart/candles/{trading_pair_id}',
                        'system/time',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'funding/auto_offerings',
                        'funding/auto_offerings/{currency_id}',
                        'funding/funding_history',
                        'funding/fundings',
                        'funding/loans',
                        'funding/loans/{loan_id}',
                        'trading/orders/{order_id}',
                        'trading/orders/{order_id}/trades',
                        'trading/orders',
                        'trading/order_history',
                        'trading/positions',
                        'trading/positions/{trading_pair_id}',
                        'trading/positions/{trading_pair_id}/claimable_size',
                        'trading/trades',
                        'trading/trades/{trade_id}',
                        'trading/volume',
                        'wallet/balances',
                        'wallet/ledger',
                        'wallet/limits/withdrawal',
                        'wallet/generic_deposits',
                        'wallet/generic_deposits/{generic_deposit_id}',
                        'wallet/generic_withdrawals',
                        'wallet/generic_withdrawals/{generic_withdrawal_id}',
                        // older endpoints
                        'wallet/deposit_addresses',
                        'wallet/deposit_addresses/iota',
                        'wallet/withdrawal_addresses',
                        'wallet/withdrawal_frozen',
                        'wallet/withdrawals/{withdrawal_id}',
                        'wallet/withdrawals',
                        'wallet/deposits/{deposit_id}',
                        'wallet/deposits',
                    ),
                    'patch' => array (
                        'trading/positions/{trading_pair_id}',
                    ),
                    'post' => array (
                        'funding/auto_offerings',
                        'funding/fundings',
                        'trading/check_order',
                        'trading/orders',
                        // older endpoints
                        'wallet/deposit_addresses',
                        'wallet/transfer',
                        'wallet/withdrawal_addresses',
                        'wallet/withdrawals',
                        'wallet/withdrawals/fee',
                    ),
                    'put' => array (
                        'funding/fundings/{funding_id}',
                        'trading/orders/{order_id}',
                    ),
                    'delete' => array (
                        'funding/auto_offerings/{currency_id}',
                        'funding/fundings/{funding_id}',
                        'funding/loans/{loan_id}',
                        'trading/orders/{order_id}',
                        'trading/positions/{trading_pair_id}',
                        'wallet/generic_withdrawals/{generic_withdrawal_id}',
                        'wallet/withdrawal_addresses/{wallet_id}',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'maker' => 0.0,
                    'taker' => 0.0,
                ),
            ),
            'precision' => array (
                'amount' => 8,
                'price' => 8,
            ),
            'exceptions' => array (
                'insufficient_balance' => '\\ccxt\\InsufficientFunds',
                'invalid_order_size' => '\\ccxt\\InvalidOrder',
                'invalid_nonce' => '\\ccxt\\InvalidNonce',
                'unauthorized_scope' => '\\ccxt\\PermissionDenied',
                'invalid_address' => '\\ccxt\\InvalidAddress',
                'parameter_error' => '\\ccxt\\OrderNotFound',
            ),
            'commonCurrencies' => array (
                'SMT' => 'SocialMedia.Market',
                'MTN' => 'Motion Token',
            ),
        ));
    }

    public function fetch_currencies ($params = array ()) {
        $response = $this->publicGetMarketCurrencies ($params);
        $currencies = $response['result']['currencies'];
        $result = array();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'currency');
            $name = $this->safe_string($currency, 'name');
            $code = $this->common_currency_code($id);
            $minUnit = $this->safe_float($currency, 'min_unit');
            $result[$code] = array (
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'active' => true,
                'fiat' => false,
                'precision' => $this->precision_from_string($currency['min_unit']),
                'limits' => array (
                    'amount' => array (
                        'min' => $minUnit,
                        'max' => null,
                    ),
                    'price' => array (
                        'min' => $minUnit,
                        'max' => null,
                    ),
                    'deposit' => array (
                        'min' => $minUnit,
                        'max' => null,
                    ),
                    'withdraw' => array (
                        'min' => $minUnit,
                        'max' => null,
                    ),
                ),
                'funding' => array (
                    'withdraw' => array (
                        'fee' => $this->safe_float($currency, 'withdrawal_fee'),
                    ),
                    'deposit' => array (
                        'fee' => $this->safe_float($currency, 'deposit_fee'),
                    ),
                ),
                'info' => $currency,
            );
        }
        return $result;
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetMarketTradingPairs ($params);
        $markets = $this->safe_value($response['result'], 'trading_pairs');
        $result = array();
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'id');
            list($baseId, $quoteId) = explode('-', $id);
            $base = $this->common_currency_code($baseId);
            $quote = $this->common_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array (
                'amount' => 8,
                'price' => $this->precision_from_string($market['quote_increment']),
            );
            $active = $this->safe_value($market, 'is_active', true);
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => $this->safe_float($market, 'base_min_size'),
                        'max' => $this->safe_float($market, 'base_max_size'),
                    ),
                    'price' => array (
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array (
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($ticker, 'trading_pair_id');
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                list($baseId, $quoteId) = explode('-', $marketId);
                $base = $this->common_currency_code($baseId);
                $quote = $this->common_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($ticker, 'timestamp');
        $last = $this->safe_float($ticker, 'last_trade_price');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, '24h_high'),
            'low' => $this->safe_float($ticker, '24h_low'),
            'bid' => $this->safe_float($ticker, 'highest_bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'lowest_ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_float($ticker, 'percentChanged24hr'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, '24h_volume'),
            'quoteVolume' => $this->safe_float($ticker, 'quote_volume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'trading_pair_id' => $market['id'],
        );
        $response = $this->publicGetMarketTickersTradingPairId (array_merge ($request, $params));
        $ticker = $this->safe_value($response['result'], 'ticker');
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetMarketTickers ($params);
        $tickers = $this->safe_value($response['result'], 'tickers');
        $result = array();
        for ($i = 0; $i < count ($tickers); $i++) {
            $result[] = $this->parse_ticker($tickers[$i]);
        }
        return $this->index_by($result, 'symbol');
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'trading_pair_id' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // 100
        }
        $response = $this->publicGetMarketOrderbooksTradingPairId (array_merge ($request, $params));
        return $this->parse_order_book($response['result']['orderbook'], null, 'bids', 'asks', 0, 2);
    }

    public function parse_trade ($trade, $market = null) {
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($trade, 'timestamp');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'size');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        // you can't determine your $side from maker/taker $side and vice versa
        // you can't determine if your order/trade was a maker or a taker based
        // on just the $side of your order/trade
        // https://github.com/ccxt/ccxt/issues/4300
        // $side = ($trade['maker_side'] === 'bid') ? 'sell' : 'buy';
        $side = null;
        $id = $this->safe_string($trade, 'id');
        return array (
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => null,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = 50, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'trading_pair_id' => $market['id'],
            'limit' => $limit, // default 20, but that seems too little
        );
        $response = $this->publicGetMarketTradesTradingPairId (array_merge ($request, $params));
        $trades = $this->safe_value($response['result'], 'trades');
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '5m', $since = null, $limit = null) {
        return [
            // they say that timestamps are Unix Timestamps in seconds, but in fact those are milliseconds
            $ohlcv['timestamp'],
            floatval ($ohlcv['open']),
            floatval ($ohlcv['high']),
            floatval ($ohlcv['low']),
            floatval ($ohlcv['close']),
            floatval ($ohlcv['volume']),
        ];
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        //
        // they say in their docs that end_time defaults to current server time
        // but if you don't specify it, their range limits does not allow you to query anything
        //
        // they also say that start_time defaults to 0,
        // but most calls fail if you do not specify any of end_time
        //
        // to make things worse, their docs say it should be a Unix Timestamp
        // but with seconds it fails, so we set milliseconds (somehow it works that way)
        //
        $endTime = $this->milliseconds ();
        $request = array (
            'trading_pair_id' => $market['id'],
            'timeframe' => $this->timeframes[$timeframe],
            'end_time' => $endTime,
        );
        if ($since !== null) {
            $request['start_time'] = $since;
        }
        $response = $this->publicGetChartCandlesTradingPairId (array_merge ($request, $params));
        $ohlcv = $this->safe_value($response['result'], 'candles');
        return $this->parse_ohlcvs($ohlcv, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetWalletBalances ($params);
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response['result'], 'balances');
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $currencyId;
            if (is_array($this->currencies_by_id) && array_key_exists($currencyId, $this->currencies_by_id)) {
                $code = $this->currencies_by_id[$currencyId]['code'];
            } else {
                $code = $this->common_currency_code($currencyId);
            }
            $account = $this->account ();
            $account['used'] = $this->safe_float($balance, 'on_order');
            $account['total'] = $this->safe_float($balance, 'total');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order_status ($status) {
        $statuses = array (
            'filled' => 'closed',
            'rejected' => 'closed',
            'partially_filled' => 'open',
            'pending_cancellation' => 'open',
            'pending_modification' => 'open',
            'open' => 'open',
            'new' => 'open',
            'queued' => 'open',
            'cancelled' => 'canceled',
            'triggered' => 'triggered',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order ($order, $market = null) {
        //
        //     {
        //         'completed_at' => None,
        //         'eq_price' => '0',
        //         'filled' => '0',
        //         'id' => '88426800-beae-4407-b4a1-f65cef693542',
        //         'price' => '0.00000507',
        //         'side' => 'bid',
        //         'size' => '3503.6489',
        //         'source' => 'exchange',
        //         'state' => 'open',
        //         'timestamp' => 1535258403597,
        //         'trading_pair_id' => 'ACT-BTC',
        //         'type' => 'limit',
        //     }
        //
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string_2($order, 'trading_pair', 'trading_pair_id');
            $market = $this->safe_value($this->markets_by_id, $marketId);
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($order, 'timestamp');
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'eq_price');
        $amount = $this->safe_float($order, 'size');
        $filled = $this->safe_float($order, 'filled');
        $remaining = null;
        $cost = null;
        if ($filled !== null && $average !== null) {
            $cost = $average * $filled;
        } else if ($average !== null) {
            $cost = $average * $amount;
        }
        if ($amount !== null) {
            if ($filled !== null) {
                $remaining = $amount - $filled;
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $side = $this->safe_string($order, 'side');
        if ($side === 'bid') {
            $side = 'buy';
        } else if ($side === 'ask') {
            $side = 'sell';
        }
        return array (
            'id' => $this->safe_string($order, 'id'),
            'datetime' => $this->iso8601 ($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $this->safe_string($order, 'type'), // $market, limit, stop, stop_limit, trailing_stop, fill_or_kill
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'average' => $average,
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
        $market = $this->market ($symbol);
        $side = ($side === 'sell') ? 'ask' : 'bid';
        $request = array (
            'trading_pair_id' => $market['id'],
            'type' => $type, // $market, limit, stop, stop_limit
            'side' => $side,
            'size' => $this->amount_to_precision($symbol, $amount),
        );
        if ($type !== 'market') {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostTradingOrders (array_merge ($request, $params));
        $order = $this->parse_order($response['result']['order'], $market);
        $id = $order['id'];
        $this->orders[$id] = $order;
        return $order;
    }

    public function edit_order ($id, $symbol, $type, $side, $amount, $price, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
            'price' => $this->price_to_precision($symbol, $price),
            'size' => $this->amount_to_precision($symbol, $amount),
        );
        $response = $this->privatePutTradingOrdersOrderId (array_merge ($request, $params));
        return $this->parse_order(array_merge ($response, array (
            'id' => $id,
        )));
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
        );
        $response = $this->privateDeleteTradingOrdersOrderId (array_merge ($request, $params));
        return $this->parse_order(array_merge ($response, array (
            'id' => $id,
        )));
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => (string) $id,
        );
        $response = $this->privateGetTradingOrdersOrderId (array_merge ($request, $params));
        return $this->parse_order($response['result']['order']);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $result = $this->privateGetTradingOrders ($params);
        $orders = $this->parse_orders($result['result']['orders'], null, $since, $limit);
        if ($symbol !== null) {
            return $this->filter_by_symbol_since_limit($orders, $symbol, $since, $limit);
        }
        return $this->filter_by_since_limit($orders, $since, $limit);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['trading_pair_id'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 50, max 100
        }
        $response = $this->privateGetTradingOrderHistory (array_merge ($request, $params));
        $orders = $this->parse_orders($response['result']['orders'], $market, $since, $limit);
        if ($symbol !== null) {
            return $this->filter_by_symbol_since_limit($orders, $symbol, $since, $limit);
        }
        return $this->filter_by_since_limit($orders, $since, $limit);
    }

    public function fetch_order_trades ($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
        );
        $response = $this->privateGetTradingOrdersOrderIdTrades (array_merge ($request, $params));
        $market = ($symbol === null) ? null : $this->market ($symbol);
        return $this->parse_trades($response['result']['trades'], $market);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array();
        if ($symbol !== null) {
            $request['trading_pair_id'] = $market['id'];
        }
        $response = $this->privateGetTradingTrades (array_merge ($request, $params));
        return $this->parse_trades($response['result']['trades'], $market, $since, $limit);
    }

    public function create_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        // 'ledger_type' is required, see => https://cobinhood.github.io/api-public/#create-new-deposit-$address
        $ledgerType = $this->safe_string($params, 'ledger_type', 'exchange');
        $request = array (
            'currency' => $currency['id'],
            'ledger_type' => $ledgerType,
        );
        $response = $this->privatePostWalletDepositAddresses (array_merge ($request, $params));
        $address = $this->safe_string($response['result']['deposit_address'], 'address');
        $tag = $this->safe_string($response['result']['deposit_address'], 'memo');
        $this->check_address($address);
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
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privateGetWalletDepositAddresses (array_merge ($request, $params));
        //
        //     { success =>    true,
        //        result => { deposit_addresses => array ( {       $address => "abcdefg",
        //                                         blockchain_id => "eosio",
        //                                            created_at =>  1536768050235,
        //                                              $currency => "EOS",
        //                                                  memo => "12345678",
        //                                                  type => "exchange"      } ) } }
        //
        $addresses = $this->safe_value($response['result'], 'deposit_addresses', array());
        $address = null;
        $tag = null;
        if (strlen ($addresses) > 0) {
            $address = $this->safe_string($addresses[0], 'address');
            $tag = $this->safe_string_2($addresses[0], 'memo', 'tag');
        }
        $this->check_address($address);
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address,
        );
        if ($tag !== null) {
            $request['memo'] = $tag;
        }
        $response = $this->privatePostWalletWithdrawals (array_merge ($request, $params));
        return array (
            'id' => null,
            'info' => $response,
        );
    }

    public function fetch_deposits ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($code === null) {
            throw new ExchangeError($this->id . ' fetchDeposits() requires a $currency $code argument');
        }
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privateGetWalletDeposits (array_merge ($request, $params));
        return $this->parseTransactions ($response['result']['deposits'], $currency);
    }

    public function fetch_withdrawals ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        if ($code === null) {
            throw new ExchangeError($this->id . ' fetchWithdrawals() requires a $currency $code argument');
        }
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privateGetWalletWithdrawals (array_merge ($request, $params));
        return $this->parseTransactions ($response['result']['withdrawals'], $currency);
    }

    public function parse_transaction_status ($status) {
        $statuses = array (
            'tx_pending_two_factor_auth' => 'pending',
            'tx_pending_email_auth' => 'pending',
            'tx_pending_approval' => 'pending',
            'tx_approved' => 'pending',
            'tx_processing' => 'pending',
            'tx_pending' => 'pending',
            'tx_sent' => 'pending',
            'tx_cancelled' => 'canceled',
            'tx_timeout' => 'failed',
            'tx_invalid' => 'failed',
            'tx_rejected' => 'failed',
            'tx_confirmed' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction ($transaction, $currency = null) {
        $timestamp = $this->safe_integer($transaction, 'created_at');
        $code = null;
        if ($currency === null) {
            $currencyId = $this->safe_string($transaction, 'currency');
            if (is_array($this->currencies_by_id) && array_key_exists($currencyId, $this->currencies_by_id)) {
                $currency = $this->currencies_by_id[$currencyId];
            } else {
                $code = $this->common_currency_code($currencyId);
            }
        }
        if ($currency !== null) {
            $code = $currency['code'];
        }
        $id = null;
        $withdrawalId = $this->safe_string($transaction, 'withdrawal_id');
        $depositId = $this->safe_string($transaction, 'deposit_id');
        $type = null;
        $address = null;
        if ($withdrawalId !== null) {
            $type = 'withdrawal';
            $id = $withdrawalId;
            $address = $this->safe_string($transaction, 'to_address');
        } else if ($depositId !== null) {
            $type = 'deposit';
            $id = $depositId;
            $address = $this->safe_string($transaction, 'from_address');
        }
        $additionalInfo = $this->safe_value($transaction, 'additional_info', array());
        $tag = $this->safe_string($additionalInfo, 'memo');
        return array (
            'info' => $transaction,
            'id' => $id,
            'txid' => $this->safe_string($transaction, 'txhash'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'address' => $address,
            'tag' => $tag, // refix it properly
            'type' => $type,
            'amount' => $this->safe_float($transaction, 'amount'),
            'currency' => $code,
            'status' => $this->parse_transaction_status ($transaction['status']),
            'updated' => null,
            'fee' => array (
                'cost' => $this->safe_float($transaction, 'fee'),
                'rate' => null,
            ),
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        $headers = array();
        if ($api === 'private') {
            $this->check_required_credentials();
            // $headers['device_id'] = $this->apiKey;
            $headers['nonce'] = (string) $this->nonce ();
            $headers['Authorization'] = $this->apiKey;
        }
        if ($method === 'GET') {
            $query = $this->urlencode ($query);
            if (strlen ($query)) {
                $url .= '?' . $query;
            }
        } else {
            $headers['Content-type'] = 'application/json; charset=UTF-8';
            $body = $this->json ($query);
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response) {
        if ($code < 400 || $code >= 600) {
            return;
        }
        if ($body[0] !== '{') {
            throw new ExchangeError($this->id . ' ' . $body);
        }
        $feedback = $this->id . ' ' . $this->json ($response);
        $errorCode = $this->safe_value($response['error'], 'error_code');
        if ($method === 'DELETE' || $method === 'GET') {
            if ($errorCode === 'parameter_error') {
                if (mb_strpos($url, 'trading/orders/') !== false) {
                    // Cobinhood returns vague "parameter_error" on fetchOrder() and cancelOrder() calls
                    // for invalid order IDs as well as orders that are not "open"
                    throw new InvalidOrder($feedback);
                }
            }
        }
        $exceptions = $this->exceptions;
        if (is_array($exceptions) && array_key_exists($errorCode, $exceptions)) {
            throw new $exceptions[$errorCode]($feedback);
        }
        throw new ExchangeError($feedback);
    }

    public function nonce () {
        return $this->milliseconds ();
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;

class braziliex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'braziliex',
            'name' => 'Braziliex',
            'countries' => array( 'BR' ),
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/34703593-c4498674-f504-11e7-8d14-ff8e44fb78c1.jpg',
                'api' => 'https://braziliex.com/api/v1',
                'www' => 'https://braziliex.com/',
                'doc' => 'https://braziliex.com/exchange/api.php',
                'fees' => 'https://braziliex.com/exchange/fees.php',
                'referral' => 'https://braziliex.com/?ref=5FE61AB6F6D67DA885BC98BA27223465',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currencies',
                        'ticker',
                        'ticker/{market}',
                        'orderbook/{market}',
                        'tradehistory/{market}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'balance',
                        'complete_balance',
                        'open_orders',
                        'trade_history',
                        'deposit_address',
                        'sell',
                        'buy',
                        'cancel_order',
                        'order_status',
                    ),
                ),
            ),
            'commonCurrencies' => array(
                'EPC' => 'Epacoin',
                'ABC' => 'Anti Bureaucracy Coin',
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.005,
                    'taker' => 0.005,
                ),
            ),
            'precision' => array(
                'amount' => 8,
                'price' => 8,
            ),
            'options' => array(
                'fetchCurrencies' => array(
                    'expires' => 1000, // 1 second
                ),
            ),
        ));
    }

    public function fetch_currencies_from_cache($params = array ()) {
        // this method is $now redundant
        // currencies are $now fetched before markets
        $options = $this->safe_value($this->options, 'fetchCurrencies', array());
        $timestamp = $this->safe_integer($options, 'timestamp');
        $expires = $this->safe_integer($options, 'expires', 1000);
        $now = $this->milliseconds();
        if (($timestamp === null) || (($now - $timestamp) > $expires)) {
            $response = $this->publicGetCurrencies ($params);
            $this->options['fetchCurrencies'] = array_merge($options, array(
                'response' => $response,
                'timestamp' => $now,
            ));
        }
        return $this->safe_value($this->options['fetchCurrencies'], 'response');
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->fetch_currencies_from_cache($params);
        //
        //     {
        //         brl => array(
        //             name => "Real",
        //             withdrawal_txFee =>  0.0075,
        //             txWithdrawalFee =>  9,
        //             MinWithdrawal =>  30,
        //             minConf =>  1,
        //             minDeposit =>  0,
        //             txDepositFee =>  0,
        //             txDepositPercentageFee =>  0,
        //             minAmountTradeFIAT =>  5,
        //             minAmountTradeBTC =>  0.0001,
        //             minAmountTradeUSDT =>  0.0001,
        //             decimal =>  8,
        //             decimal_withdrawal =>  8,
        //             $active =>  1,
        //             dev_active =>  1,
        //             under_maintenance =>  0,
        //             order => "010",
        //             is_withdrawal_active =>  1,
        //             is_deposit_active =>  1,
        //             is_token_erc20 =>  0,
        //             is_fiat =>  1,
        //             gateway =>  0,
        //         ),
        //         btc => {
        //             name => "Bitcoin",
        //             txWithdrawalMinFee =>  0.000125,
        //             txWithdrawalFee =>  0.00015625,
        //             MinWithdrawal =>  0.0005,
        //             minConf =>  1,
        //             minDeposit =>  0,
        //             txDepositFee =>  0,
        //             txDepositPercentageFee =>  0,
        //             minAmountTradeFIAT =>  5,
        //             minAmountTradeBTC =>  0.0001,
        //             minAmountTradeUSDT =>  0.0001,
        //             decimal =>  8,
        //             decimal_withdrawal =>  8,
        //             $active =>  1,
        //             dev_active =>  1,
        //             under_maintenance =>  0,
        //             order => "011",
        //             is_withdrawal_active =>  1,
        //             is_deposit_active =>  1,
        //             is_token_erc20 =>  0,
        //             is_fiat =>  0,
        //             gateway =>  1,
        //         }
        //     }
        //
        $this->options['currencies'] = array(
            'timestamp' => $this->milliseconds(),
            'response' => $response,
        );
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $currency = $response[$id];
            $precision = $this->safe_integer($currency, 'decimal');
            $code = $this->safe_currency_code($id);
            $active = $this->safe_integer($currency, 'active') === 1;
            $maintenance = $this->safe_integer($currency, 'under_maintenance');
            if ($maintenance !== 0) {
                $active = false;
            }
            $canWithdraw = $this->safe_integer($currency, 'is_withdrawal_active') === 1;
            $canDeposit = $this->safe_integer($currency, 'is_deposit_active') === 1;
            if (!$canWithdraw || !$canDeposit) {
                $active = false;
            }
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $currency['name'],
                'active' => $active,
                'precision' => $precision,
                'funding' => array(
                    'withdraw' => array(
                        'active' => $canWithdraw,
                        'fee' => $this->safe_number($currency, 'txWithdrawalFee'),
                    ),
                    'deposit' => array(
                        'active' => $canDeposit,
                        'fee' => $this->safe_number($currency, 'txDepositFee'),
                    ),
                ),
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
                        'min' => $this->safe_number($currency, 'MinWithdrawal'),
                        'max' => pow(10, $precision),
                    ),
                    'deposit' => array(
                        'min' => $this->safe_number($currency, 'minDeposit'),
                        'max' => null,
                    ),
                ),
                'info' => $currency,
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $currencies = $this->fetch_currencies_from_cache($params);
        $response = $this->publicGetTicker ();
        //
        //     {
        //         btc_brl => array(
        //             $active => 1,
        //             $market => 'btc_brl',
        //             last => 14648,
        //             percentChange => -0.95,
        //             baseVolume24 => 27.856,
        //             quoteVolume24 => 409328.039,
        //             baseVolume => 27.856,
        //             quoteVolume => 409328.039,
        //             highestBid24 => 14790,
        //             lowestAsk24 => 14450.01,
        //             highestBid => 14450.37,
        //             lowestAsk => 14699.98
        //         ),
        //         ...
        //     }
        //
        $ids = is_array($response) ? array_keys($response) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $response[$id];
            list($baseId, $quoteId) = explode('_', $id);
            $uppercaseBaseId = strtoupper($baseId);
            $uppercaseQuoteId = strtoupper($quoteId);
            $base = $this->safe_currency_code($uppercaseBaseId);
            $quote = $this->safe_currency_code($uppercaseQuoteId);
            $symbol = $base . '/' . $quote;
            $baseCurrency = $this->safe_value($currencies, $baseId, array());
            $quoteCurrency = $this->safe_value($currencies, $quoteId, array());
            $quoteIsFiat = $this->safe_integer($quoteCurrency, 'is_fiat', 0);
            $minCost = null;
            if ($quoteIsFiat) {
                $minCost = $this->safe_number($baseCurrency, 'minAmountTradeFIAT');
            } else {
                $minCost = $this->safe_number($baseCurrency, 'minAmountTrade' . $uppercaseQuoteId);
            }
            $isActive = $this->safe_integer($market, 'active');
            $active = ($isActive === 1);
            $precision = array(
                'amount' => 8,
                'price' => 8,
            );
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => pow(10, $precision['amount']),
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => pow(10, $precision['price']),
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
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->milliseconds();
        $last = $this->safe_number($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'highestBid24'),
            'low' => $this->safe_number($ticker, 'lowestAsk24'),
            'bid' => $this->safe_number($ticker, 'highestBid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'lowestAsk'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $this->safe_number($ticker, 'percentChange'),
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'baseVolume24'),
            'quoteVolume' => $this->safe_number($ticker, 'quoteVolume24'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicGetTickerMarket (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        $result = array();
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $marketId = $ids[$i];
            $market = $this->safe_market($marketId);
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($response[$marketId], $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'market' => $this->market_id($symbol),
        );
        $response = $this->publicGetOrderbookMarket (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'bids', 'asks', 'price', 'amount');
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->parse8601($this->safe_string_2($trade, 'date_exec', 'date'));
        $price = $this->safe_number($trade, 'price');
        $amount = $this->safe_number($trade, 'amount');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $cost = $this->safe_number($trade, 'total');
        $orderId = $this->safe_string($trade, 'order_number');
        $type = 'limit';
        $side = $this->safe_string($trade, 'type');
        $id = $this->safe_string($trade, '_id');
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => $type,
            'side' => $side,
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
        $response = $this->publicGetTradehistoryMarket (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $balances = $this->privatePostCompleteBalance ($params);
        $result = array( 'info' => $balances );
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $balance = $balances[$currencyId];
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'available');
            $account['total'] = $this->safe_number($balance, 'total');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "order_number":"58ee441d05f8233fadabfb07",
        //         "type":"buy",
        //         "$market":"ltc_btc",
        //         "$price":"0.01000000",
        //         "$amount":"0.00200000",
        //         "total":"0.00002000",
        //         "progress":"1.0000",
        //         "date":"2017-03-12 15:13:33"
        //     }
        //
        $marketId = $this->safe_string($order, 'market');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $timestamp = $this->safe_integer($order, 'timestamp');
        if ($timestamp === null) {
            $timestamp = $this->parse8601($this->safe_string($order, 'date'));
        }
        $price = $this->safe_number($order, 'price');
        $cost = $this->safe_number($order, 'total');
        $amount = $this->safe_number($order, 'amount');
        $filledPercentage = $this->safe_number($order, 'progress');
        $filled = $amount * $filledPercentage;
        $id = $this->safe_string($order, 'order_number');
        $fee = $this->safe_value($order, 'fee'); // propagated from createOrder
        $status = ($filledPercentage === 1.0) ? 'closed' : 'open';
        $side = $this->safe_string($order, 'type');
        return $this->safe_order(array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'trades' => null,
            'fee' => $fee,
            'info' => $order,
            'average' => null,
        ));
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $method = 'privatePost' . $this->capitalize($side);
        $request = array(
            'market' => $market['id'],
            // 'price' => $this->price_to_precision($symbol, $price),
            // 'amount' => $this->amount_to_precision($symbol, $amount),
            'price' => $price,
            'amount' => $amount,
        );
        $response = $this->$method (array_merge($request, $params));
        //
        // sell
        //
        //     {
        //         "$success":1,
        //         "$message":" ##RESERVED FOR ORDER / SELL / XMR_BTC / AMOUNT => 0.01 XMR / PRICE => 0.017 BTC / TOTAL => 0.00017000 BTC / FEE => 0.00002500 XMR ",
        //         "order_number":"590b962ba5b98335965fa0a8"
        //     }
        //
        // buy
        //
        //     {
        //         "$success":1,
        //         "$message":" ##RESERVED FOR ORDER / BUY / XMR_BTC / AMOUNT => 0.005 XMR / PRICE => 0.017 BTC / TOTAL => 0.00008500 BTC / FEE => 0.00000021 BTC ",
        //         "order_number":"590b962ba5b98335965fa0c0"
        //     }
        //
        $success = $this->safe_integer($response, 'success');
        if ($success !== 1) {
            throw new InvalidOrder($this->id . ' ' . $this->json($response));
        }
        $message = $this->safe_string($response, 'message');
        $parts = explode(' / ', $message);
        $parts = mb_substr($parts, 1);
        $feeParts = explode(' ', $parts[5]);
        $amountParts = explode(' ', $parts[2]);
        $priceParts = explode(' ', $parts[3]);
        $totalParts = explode(' ', $parts[4]);
        $order = $this->parse_order(array(
            'timestamp' => $this->milliseconds(),
            'order_number' => $response['order_number'],
            'type' => $this->safe_string_lower($parts, 0),
            'market' => strtolower($parts[0]),
            'amount' => $this->safe_string($amountParts, 1),
            'price' => $this->safe_string($priceParts, 1),
            'total' => $this->safe_string($totalParts, 1),
            'fee' => array(
                'cost' => $this->safe_number($feeParts, 1),
                'currency' => $this->safe_string($feeParts, 2),
            ),
            'progress' => '0.0',
            'info' => $response,
        ), $market);
        return $order;
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'order_number' => $id,
            'market' => $market['id'],
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'order_number' => $id,
            'market' => $market['id'],
        );
        $response = $this->privatePostOrderStatus (array_merge($request, $params));
        return $this->parse_order($response, $market);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->privatePostOpenOrders (array_merge($request, $params));
        $orders = $this->safe_value($response, 'order_open', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->privatePostTradeHistory (array_merge($request, $params));
        $trades = $this->safe_value($response, 'trade_history', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privatePostDepositAddress (array_merge($request, $params));
        $address = $this->safe_string($response, 'deposit_address');
        $this->check_address($address);
        $tag = $this->safe_string($response, 'payment_id');
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $api;
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            $url .= '/' . $this->implode_params($path, $params);
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $query = array_merge(array(
                'command' => $path,
                'nonce' => $this->nonce(),
            ), $query);
            $body = $this->urlencode($query);
            $signature = $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512');
            $headers = array(
                'Content-type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $signature,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if ((gettype($response) === 'string') && (strlen($response) < 1)) {
            throw new ExchangeError($this->id . ' returned empty response');
        }
        if (is_array($response) && array_key_exists('success', $response)) {
            $success = $this->safe_integer($response, 'success');
            if ($success === 0) {
                $message = $this->safe_string($response, 'message');
                if ($message === 'Invalid APIKey') {
                    throw new AuthenticationError($message);
                }
                throw new ExchangeError($message);
            }
        }
        return $response;
    }
}

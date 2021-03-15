<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class tidex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'tidex',
            'name' => 'Tidex',
            'countries' => array( 'UK' ),
            'rateLimit' => 2000,
            'version' => '3',
            'userAgent' => $this->userAgents['chrome'],
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrderBooks' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'withdraw' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/30781780-03149dc4-a12e-11e7-82bb-313b269d24d4.jpg',
                'api' => array(
                    'web' => 'https://gate.tidex.com/api',
                    'public' => 'https://api.tidex.com/api/3',
                    'private' => 'https://api.tidex.com/tapi',
                ),
                'www' => 'https://tidex.com',
                'doc' => 'https://tidex.com/exchange/public-api',
                'referral' => 'https://tidex.com/exchange/?ref=57f5638d9cd7',
                'fees' => array(
                    'https://tidex.com/exchange/assets-spec',
                    'https://tidex.com/exchange/pairs-spec',
                ),
            ),
            'api' => array(
                'web' => array(
                    'get' => array(
                        'currency',
                        'pairs',
                        'tickers',
                        'orders',
                        'ordershistory',
                        'trade-data',
                        'trade-data/{id}',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'info',
                        'ticker/{pair}',
                        'depth/{pair}',
                        'trades/{pair}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'getInfoExt',
                        'getInfo',
                        'Trade',
                        'ActiveOrders',
                        'OrderInfo',
                        'CancelOrder',
                        'TradeHistory',
                        'CoinDepositAddress',
                        'WithdrawCoin',
                        'CreateCoupon',
                        'RedeemCoupon',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.1 / 100,
                    'maker' => 0.1 / 100,
                ),
            ),
            'commonCurrencies' => array(
                'DSH' => 'DASH',
                'EMGO' => 'MGO',
                'MGO' => 'WMGO',
            ),
            'exceptions' => array(
                'exact' => array(
                    '803' => '\\ccxt\\InvalidOrder', // "Count could not be less than 0.001." (selling below minAmount)
                    '804' => '\\ccxt\\InvalidOrder', // "Count could not be more than 10000." (buying above maxAmount)
                    '805' => '\\ccxt\\InvalidOrder', // "price could not be less than X." (minPrice violation on buy & sell)
                    '806' => '\\ccxt\\InvalidOrder', // "price could not be more than X." (maxPrice violation on buy & sell)
                    '807' => '\\ccxt\\InvalidOrder', // "cost could not be less than X." (minCost violation on buy & sell)
                    '831' => '\\ccxt\\InsufficientFunds', // "Not enougth X to create buy order." (buying with balance.quote < order.cost)
                    '832' => '\\ccxt\\InsufficientFunds', // "Not enougth X to create sell order." (selling with balance.base < order.amount)
                    '833' => '\\ccxt\\OrderNotFound', // "Order with id X was not found." (cancelling non-existent, closed and cancelled order)
                ),
                'broad' => array(
                    'Invalid pair name' => '\\ccxt\\ExchangeError', // array("success":0,"error":"Invalid pair name => btc_eth")
                    'invalid api key' => '\\ccxt\\AuthenticationError',
                    'invalid sign' => '\\ccxt\\AuthenticationError',
                    'api key dont have trade permission' => '\\ccxt\\AuthenticationError',
                    'invalid parameter' => '\\ccxt\\InvalidOrder',
                    'invalid order' => '\\ccxt\\InvalidOrder',
                    'Requests too often' => '\\ccxt\\DDoSProtection',
                    'not available' => '\\ccxt\\ExchangeNotAvailable',
                    'data unavailable' => '\\ccxt\\ExchangeNotAvailable',
                    'external service unavailable' => '\\ccxt\\ExchangeNotAvailable',
                ),
            ),
            'options' => array(
                'fetchTickersMaxLength' => 2048,
            ),
            'orders' => array(), // orders cache / emulation
        ));
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->webGetCurrency ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'symbol');
            $precision = $currency['amountPoint'];
            $code = $this->safe_currency_code($id);
            $active = $currency['visible'] === true;
            $canWithdraw = $currency['withdrawEnable'] === true;
            $canDeposit = $currency['depositEnable'] === true;
            if (!$canWithdraw || !$canDeposit) {
                $active = false;
            }
            $name = $this->safe_string($currency, 'name');
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $name,
                'active' => $active,
                'precision' => $precision,
                'funding' => array(
                    'withdraw' => array(
                        'active' => $canWithdraw,
                        'fee' => $currency['withdrawFee'],
                    ),
                    'deposit' => array(
                        'active' => $canDeposit,
                        'fee' => 0.0,
                    ),
                ),
                'limits' => array(
                    'amount' => array(
                        'min' => null,
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
                        'min' => $this->safe_float($currency, 'withdrawMinAmount'),
                        'max' => null,
                    ),
                    'deposit' => array(
                        'min' => $this->safe_float($currency, 'depositMinAmount'),
                        'max' => null,
                    ),
                ),
                'info' => $currency,
            );
        }
        return $result;
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
            'cost' => $cost,
        );
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetInfo ($params);
        $markets = $response['pairs'];
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
                'amount' => $this->safe_integer($market, 'decimal_places'),
                'price' => $this->safe_integer($market, 'decimal_places'),
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'min_amount'),
                    'max' => $this->safe_float($market, 'max_amount'),
                ),
                'price' => array(
                    'min' => $this->safe_float($market, 'min_price'),
                    'max' => $this->safe_float($market, 'max_price'),
                ),
                'cost' => array(
                    'min' => $this->safe_float($market, 'min_total'),
                ),
            );
            $hidden = $this->safe_integer($market, 'hidden');
            $active = ($hidden === 0);
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'taker' => $market['fee'] / 100,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetInfoExt ($params);
        $balances = $this->safe_value($response, 'return');
        $result = array( 'info' => $balances );
        $funds = $this->safe_value($balances, 'funds', array());
        $currencyIds = is_array($funds) ? array_keys($funds) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $balance = $this->safe_value($funds, $currencyId, array());
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'value');
            $account['used'] = $this->safe_float($balance, 'inOrders');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 150, max = 2000
        }
        $response = $this->publicGetDepthPair (array_merge($request, $params));
        $market_id_in_reponse = (is_array($response) && array_key_exists($market['id'], $response));
        if (!$market_id_in_reponse) {
            throw new ExchangeError($this->id . ' ' . $market['symbol'] . ' order book is empty or not available');
        }
        $orderbook = $response[$market['id']];
        return $this->parse_order_book($orderbook);
    }

    public function fetch_order_books($symbols = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $ids = null;
        if ($symbols === null) {
            $ids = implode('-', $this->ids);
            // max URL length is 2083 $symbols, including http schema, hostname, tld, etc...
            if (strlen($ids) > 2048) {
                $numIds = is_array($this->ids) ? count($this->ids) : 0;
                throw new ExchangeError($this->id . ' has ' . (string) $numIds . ' $symbols exceeding max URL length, you are required to specify a list of $symbols in the first argument to fetchOrderBooks');
            }
        } else {
            $ids = $this->market_ids($symbols);
            $ids = implode('-', $ids);
        }
        $request = array(
            'pair' => $ids,
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 150, max = 2000
        }
        $response = $this->publicGetDepthPair (array_merge($request, $params));
        $result = array();
        $ids = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $symbol = $this->safe_symbol($id);
            $result[$symbol] = $this->parse_order_book($response[$id]);
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //   {    high => 0.03497582,
        //         low => 0.03248474,
        //         avg => 0.03373028,
        //         vol => 120.11485715062999,
        //     vol_cur => 3572.24914074,
        //        $last => 0.0337611,
        //         buy => 0.0337442,
        //        sell => 0.03377798,
        //     updated => 1537522009          }
        //
        $timestamp = $this->safe_timestamp($ticker, 'updated');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            if (!$market['active']) {
                $timestamp = null;
            }
        }
        $last = $this->safe_float($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => $this->safe_float($ticker, 'avg'),
            'baseVolume' => $this->safe_float($ticker, 'vol_cur'),
            'quoteVolume' => $this->safe_float($ticker, 'vol'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $ids = $this->ids;
        if ($symbols === null) {
            $numIds = is_array($ids) ? count($ids) : 0;
            $ids = implode('-', $ids);
            // max URL length is 2048 $symbols, including http schema, hostname, tld, etc...
            if (strlen($ids) > $this->options['fetchTickersMaxLength']) {
                $maxLength = $this->safe_integer($this->options, 'fetchTickersMaxLength', 2048);
                throw new ArgumentsRequired($this->id . ' has ' . (string) $numIds . ' markets exceeding max URL length for this endpoint (' . (string) $maxLength . ' characters), please, specify a list of $symbols of interest in the first argument to fetchTickers');
            }
        } else {
            $ids = $this->market_ids($symbols);
            $ids = implode('-', $ids);
        }
        $request = array(
            'pair' => $ids,
        );
        $response = $this->publicGetTickerPair (array_merge($request, $params));
        $result = array();
        $keys = is_array($response) ? array_keys($response) : array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $market = $this->safe_market($id);
            $symbol = $market['symbol'];
            $result[$symbol] = $this->parse_ticker($response[$id], $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $tickers = $this->fetch_tickers(array( $symbol ), $params);
        return $tickers[$symbol];
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'timestamp');
        $side = $this->safe_string($trade, 'type');
        if ($side === 'ask') {
            $side = 'sell';
        } else if ($side === 'bid') {
            $side = 'buy';
        }
        $price = $this->safe_float_2($trade, 'rate', 'price');
        $id = $this->safe_string_2($trade, 'trade_id', 'tid');
        $orderId = $this->safe_string($trade, 'order_id');
        $marketId = $this->safe_string($trade, 'pair');
        $symbol = $this->safe_symbol($marketId, $market);
        $amount = $this->safe_float($trade, 'amount');
        $type = 'limit'; // all trades are still limit trades
        $takerOrMaker = null;
        $fee = null;
        $feeCost = $this->safe_float($trade, 'commission');
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'commissionCurrency');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        $isYourOrder = $this->safe_value($trade, 'is_your_order');
        if ($isYourOrder !== null) {
            $takerOrMaker = 'taker';
            if ($isYourOrder) {
                $takerOrMaker = 'maker';
            }
            if ($fee === null) {
                $fee = $this->calculate_fee($symbol, $type, $side, $amount, $price, $takerOrMaker);
            }
        }
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $amount * $price;
            }
        }
        return array(
            'id' => $id,
            'order' => $orderId,
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
            'info' => $trade,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->publicGetTradesPair (array_merge($request, $params));
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) == 0) {
            $numElements = is_array($response) ? count($response) : 0;
            if ($numElements === 0) {
                return array();
            }
        }
        return $this->parse_trades($response[$market['id']], $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
            'type' => $side,
            'amount' => $this->amount_to_precision($symbol, $amount),
            'rate' => $this->price_to_precision($symbol, $price),
        );
        $price = floatval($price);
        $amount = floatval($amount);
        $response = $this->privatePostTrade (array_merge($request, $params));
        $id = null;
        $status = 'open';
        $filled = 0.0;
        $remaining = $amount;
        if (is_array($response) && array_key_exists('return', $response)) {
            $id = $this->safe_string($response['return'], 'order_id');
            if ($id === '0') {
                $id = $this->safe_string($response['return'], 'init_order_id');
                $status = 'closed';
            }
            $filled = $this->safe_float($response['return'], 'received', 0.0);
            $remaining = $this->safe_float($response['return'], 'remains', $amount);
        }
        $timestamp = $this->milliseconds();
        return array(
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $price * $filled,
            'amount' => $amount,
            'remaining' => $remaining,
            'filled' => $filled,
            'fee' => null,
            // 'trades' => $this->parse_trades(order['trades'], $market),
            'info' => $response,
            'clientOrderId' => null,
            'average' => null,
            'trades' => null,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => intval($id),
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function parse_order_status($status) {
        $statuses = array(
            '0' => 'open',
            '1' => 'closed',
            '2' => 'canceled',
            '3' => 'canceled', // or partially-filled and still open? https://github.com/ccxt/ccxt/issues/1594
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        $id = $this->safe_string($order, 'id');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $timestamp = $this->safe_timestamp($order, 'timestamp_created');
        $marketId = $this->safe_string($order, 'pair');
        $symbol = $this->safe_symbol($marketId, $market);
        $remaining = null;
        $amount = null;
        $price = $this->safe_float($order, 'rate');
        if (is_array($order) && array_key_exists('start_amount', $order)) {
            $amount = $this->safe_float($order, 'start_amount');
            $remaining = $this->safe_float($order, 'amount');
        } else {
            $remaining = $this->safe_float($order, 'amount');
        }
        $fee = null;
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $this->safe_string($order, 'type'),
            'price' => $price,
            'stopPrice' => null,
            'cost' => null,
            'amount' => $amount,
            'remaining' => $remaining,
            'filled' => null,
            'status' => $status,
            'fee' => $fee,
            'average' => null,
            'trades' => null,
        ));
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => intval($id),
        );
        $response = $this->privatePostOrderInfo (array_merge($request, $params));
        $id = (string) $id;
        $result = $this->safe_value($response, 'return', array());
        $order = $this->safe_value($result, $id);
        return $this->parse_order(array_merge(array( 'id' => $id ), $order));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        $response = $this->privatePostActiveOrders (array_merge($request, $params));
        //
        //     {
        //         "success":1,
        //         "return":{
        //             "1255468911":array(
        //                 "status":0,
        //                 "pair":"spike_usdt",
        //                 "type":"sell",
        //                 "amount":35028.44256388,
        //                 "rate":0.00199989,
        //                 "timestamp_created":1602684432
        //             }
        //         ),
        //         "stat":{
        //             "isSuccess":true,
        //             "serverTime":"00:00:00.0000826",
        //             "time":"00:00:00.0091423",
        //             "errors":null
        //         }
        //     }
        //
        // it can only return 'open' $orders (i.e. no way to fetch 'closed' $orders)
        $orders = $this->safe_value($response, 'return', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        // some derived classes use camelcase notation for $request fields
        $request = array(
            // 'from' => 123456789, // trade ID, from which the display starts numerical 0 (test result => liqui ignores this field)
            // 'count' => 1000, // the number of $trades for display numerical, default = 1000
            // 'from_id' => trade ID, from which the display starts numerical 0
            // 'end_id' => trade ID on which the display ends numerical ∞
            // 'order' => 'ASC', // sorting, default = DESC (test result => liqui ignores this field, most recent trade always goes last)
            // 'since' => 1234567890, // UTC start time, default = 0 (test result => liqui ignores this field)
            // 'end' => 1234567890, // UTC end time, default = ∞ (test result => liqui ignores this field)
            // 'pair' => 'eth_btc', // default = all markets
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        if ($limit !== null) {
            $request['count'] = intval($limit);
        }
        if ($since !== null) {
            $request['since'] = intval($since / 1000);
        }
        $response = $this->privatePostTradeHistory (array_merge($request, $params));
        $trades = $this->safe_value($response, 'return', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'coinName' => $currency['id'],
            'amount' => floatval($amount),
            'address' => $address,
        );
        // no docs on the $tag, yet...
        if ($tag !== null) {
            throw new ExchangeError($this->id . ' withdraw() does not support the $tag argument yet due to a lack of docs on withdrawing with tag/memo on behalf of the exchange.');
        }
        $response = $this->privatePostWithdrawCoin (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['return']['tId'],
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array(
                'nonce' => $nonce,
                'method' => $path,
            ), $query));
            $signature = $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512');
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $signature,
            );
        } else if ($api === 'public') {
            $url .= '/' . $this->implode_params($path, $params);
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $url .= '/' . $this->implode_params($path, $params);
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode($query);
                }
            } else {
                if ($query) {
                    $body = $this->json($query);
                    $headers = array(
                        'Content-Type' => 'application/json',
                    );
                }
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if (is_array($response) && array_key_exists('success', $response)) {
            //
            // 1 - The exchange only returns the integer 'success' key from their private API
            //
            //     array( "$success" => 1, ... ) $httpCode === 200
            //     array( "$success" => 0, ... ) $httpCode === 200
            //
            // 2 - However, derived exchanges can return non-integers
            //
            //     It can be a numeric string
            //     array( "sucesss" => "1", ... )
            //     array( "sucesss" => "0", ... ), $httpCode >= 200 (can be 403, 502, etc)
            //
            //     Or just a string
            //     array( "$success" => "true", ... )
            //     array( "$success" => "false", ... ), $httpCode >= 200
            //
            //     Or a boolean
            //     array( "$success" => true, ... )
            //     array( "$success" => false, ... ), $httpCode >= 200
            //
            // 3 - Oversimplified, Python PEP8 forbids comparison operator (===) of different types
            //
            // 4 - We do not want to copy-paste and duplicate the $code of this handler to other exchanges derived from Liqui
            //
            // To cover points 1, 2, 3 and 4 combined this handler should work like this:
            //
            $success = $this->safe_value($response, 'success', false);
            if (gettype($success) === 'string') {
                if (($success === 'true') || ($success === '1')) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
            if (!$success) {
                $code = $this->safe_string($response, 'code');
                $message = $this->safe_string($response, 'error');
                $feedback = $this->id . ' ' . $body;
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
                $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
                throw new ExchangeError($feedback); // unknown $message
            }
        }
    }
}

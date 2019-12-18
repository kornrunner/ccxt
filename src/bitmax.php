<?php

namespace ccxt;

use Exception; // a common import

class bitmax extends Exchange {

    public function describe () {
        return array_replace_recursive(parent::describe (), array(
            'id' => 'bitmax',
            'name' => 'BitMax',
            'countries' => array( 'CN' ), // China
            'rateLimit' => 500,
            'certified' => false,
            // new metainfo interface
            'has' => array(
                'CORS' => false,
                'fetchAccounts' => true,
                'fetchTickers' => true,
                'fetchOHLCV' => true,
                'fetchMyTrades' => false,
                'fetchOrder' => true,
                'fetchOrders' => false,
                'fetchOpenOrders' => true,
                'fetchOrderTrades' => true,
                'fetchClosedOrders' => true,
                'fetchTransactions' => false,
                'fetchCurrencies' => true,
                'cancelAllOrders' => true,
                'fetchDepositAddress' => true,
            ),
            'timeframes' => array(
                '1m' => '1',
                '3m' => '3',
                '5m' => '5',
                '15m' => '15',
                '30m' => '30',
                '1h' => '60',
                '2h' => '120',
                '4h' => '240',
                '6h' => '360',
                '12h' => '720',
                '1d' => '1d',
                '1w' => '1w',
                '1M' => '1m',
            ),
            'version' => 'v1',
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/66820319-19710880-ef49-11e9-8fbe-16be62a11992.jpg',
                'api' => 'https://bitmax.io',
                'test' => 'https://bitmax-test.io/api',
                'www' => 'https://bitmax.io',
                'doc' => array(
                    'https://github.com/bitmax-exchange/api-doc/blob/master/bitmax-api-doc-v1.2.md',
                ),
                'fees' => 'https://bitmax.io/#/feeRate/tradeRate',
                'referral' => 'https://bitmax.io/#/register?inviteCode=EL6BXBQM',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'assets',
                        'depth',
                        'fees',
                        'quote',
                        'depth',
                        'trades',
                        'products',
                        'ticker/24hr',
                        'barhist',
                        'barhist/info',
                        'margin/ref-price',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'deposit',
                        'user/info',
                        'balance',
                        'order/batch',
                        'order/open',
                        'order',
                        'order/history',
                        'order/{coid}',
                        'order/fills/{coid}',
                        'transaction',
                        'margin/balance',
                        'margin/order/open',
                        'margin/order',
                    ),
                    'post' => array(
                        'margin/order',
                        'order',
                        'order/batch',
                    ),
                    'delete' => array(
                        'margin/order',
                        'order',
                        'order/all',
                        'order/batch',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.001,
                    'maker' => 0.001,
                ),
            ),
            'options' => array(
                'accountGroup' => null,
                'parseOrderToPrecision' => false,
            ),
            'exceptions' => array(
                'exact' => array(
                    '2100' => '\\ccxt\\AuthenticationError', // array("code":2100,"message":"ApiKeyFailure")
                    '5002' => '\\ccxt\\BadSymbol', // array("code":5002,"message":"Invalid Symbol")
                    '6010' => '\\ccxt\\InsufficientFunds', // array('code' => 6010, 'message' => 'Not enough balance.')
                    '60060' => '\\ccxt\\InvalidOrder', // array( 'code' => 60060, 'message' => 'The order is already filled or canceled.' )
                    '600503' => '\\ccxt\\InvalidOrder', // array("code":600503,"message":"Notional is too small.")
                ),
                'broad' => array(),
            ),
        ));
    }

    public function fetch_currencies ($params = array ()) {
        $response = $this->publicGetAssets ($params);
        //
        //     array(
        //         array(
        //           "assetCode" : "LTO",
        //           "assetName" : "LTO",
        //           "precisionScale" : 9,
        //           "nativeScale" : 3,
        //           "withdrawalFee" : 5.0,
        //           "minWithdrawalAmt" : 10.0,
        //           "$status" : "Normal"
        //         ),
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $currency = $response[$i];
            $id = $this->safe_string($currency, 'assetCode');
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/deposit methods and
            // differentiated fees for each particular method
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer($currency, 'precisionScale');
            $fee = $this->safe_float($currency, 'withdrawalFee'); // todo => redesign
            $status = $this->safe_string($currency, 'status');
            $active = ($status === 'Normal');
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'type' => null,
                'name' => $this->safe_string($currency, 'assetName'),
                'active' => $active,
                'fee' => $fee,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision),
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_float($currency, 'minWithdrawalAmt'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetProducts ($params);
        //
        //     array(
        //         array(
        //             "$symbol" : "BCH/USDT",
        //             "domain" : "USDS",
        //             "baseAsset" : "BCH",
        //             "quoteAsset" : "USDT",
        //             "priceScale" : 2,
        //             "qtyScale" : 3,
        //             "notionalScale" : 9,
        //             "minQty" : "0.000000001",
        //             "maxQty" : "1000000000",
        //             "minNotional" : "5",
        //             "maxNotional" : "200000",
        //             "$status" : "Normal",
        //             "miningStatus" : "",
        //             "marginTradable" : true,
        //             "commissionType" : "Quote",
        //             "commissionReserveRate" : 0.0010000000
        //         ),
        //     )
        //
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $id = $this->safe_string($market, 'symbol');
            $baseId = $this->safe_string($market, 'baseAsset');
            $quoteId = $this->safe_string($market, 'quoteAsset');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'qtyScale'),
                'price' => $this->safe_integer($market, 'notionalScale'),
            );
            $status = $this->safe_string($market, 'status');
            $active = ($status === 'Normal');
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => $active,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'minQty'),
                        'max' => $this->safe_float($market, 'maxQty'),
                    ),
                    'price' => array( 'min' => null, 'max' => null ),
                    'cost' => array(
                        'min' => $this->safe_float($market, 'minNotional'),
                        'max' => $this->safe_float($market, 'maxNotional'),
                    ),
                ),
            );
        }
        return $result;
    }

    public function calculate_fee ($symbol, $type, $side, $amount, $price, $takerOrMaker = 'taker', $params = array ()) {
        $market = $this->markets[$symbol];
        $key = 'quote';
        $rate = $market[$takerOrMaker];
        $cost = $amount * $rate;
        $precision = $market['precision']['price'];
        if ($side === 'sell') {
            $cost *= $price;
        } else {
            $key = 'base';
            $precision = $market['precision']['amount'];
        }
        $cost = $this->decimal_to_precision($cost, ROUND, $precision, $this->precisionMode);
        return array(
            'type' => $takerOrMaker,
            'currency' => $market[$key],
            'rate' => $rate,
            'cost' => floatval ($cost),
        );
    }

    public function fetch_accounts ($params = array ()) {
        $accountGroup = $this->safe_string($this->options, 'accountGroup');
        $response = null;
        if ($accountGroup === null) {
            $response = $this->privateGetUserInfo ($params);
            //
            //     {
            //         "$accountGroup" => 5
            //     }
            //
            $accountGroup = $this->safe_string($response, 'accountGroup');
        }
        return array(
            array(
                'id' => $accountGroup,
                'type' => null,
                'currency' => null,
                'info' => $response,
            ),
        );
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $response = $this->privateGetBalance ($params);
        //
        //     {
        //         "$code" => 0,
        //         "status" => "success", // this field will be deprecated soon
        //         "email" => "foo@bar.com", // this field will be deprecated soon
        //         "data" => array(
        //             array(
        //                 "assetCode" => "TSC",
        //                 "assetName" => "Ethereum",
        //                 "totalAmount" => "20.03", // total $balance amount
        //                 "availableAmount" => "20.03", // $balance amount available to trade
        //                 "inOrderAmount" => "0.000", // in order amount
        //                 "btcValue" => "70.81"     // the current BTC value of the $balance, may be missing
        //             ),
        //         )
        //     }
        //
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'data', array());
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $code = $this->safe_currency_code($this->safe_string($balance, 'assetCode'));
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'availableAmount');
            $account['used'] = $this->safe_float($balance, 'inOrderAmount');
            $account['total'] = $this->safe_float($balance, 'totalAmount');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['n'] = $limit; // default = maximum = 100
        }
        $response = $this->publicGetDepth (array_merge($request, $params));
        //
        //     {
        //         "m":"depth",
        //         "ts":1570866464777,
        //         "seqnum":5124140078,
        //         "s":"ETH/USDT",
        //         "asks":[
        //             ["183.57","5.92"],
        //             ["183.6","10.185"]
        //         ],
        //         "bids":[
        //             ["183.54","0.16"],
        //             ["183.53","10.8"],
        //         ]
        //     }
        //
        $timestamp = $this->safe_integer($response, 'ts');
        $result = $this->parse_order_book($response, $timestamp);
        $result['nonce'] = $this->safe_integer($response, 'seqnum');
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        //
        //     {
        //         "$symbol":"BCH/USDT",
        //         "interval":"1d",
        //         "barStartTime":1570866600000,
        //         "openPrice":"225.16",
        //         "closePrice":"224.05",
        //         "highPrice":"226.08",
        //         "lowPrice":"218.92",
        //         "volume":"8607.036"
        //     }
        //
        $timestamp = $this->safe_integer($ticker, 'barStartTime');
        $symbol = null;
        $marketId = $this->safe_string($ticker, 'symbol');
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
        } else if ($marketId !== null) {
            list($baseId, $quoteId) = explode('/', $marketId);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'closePrice');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'highPrice'),
            'low' => $this->safe_float($ticker, 'lowPrice'),
            'bid' => null,
            'bidVolume' => null,
            'ask' => null,
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_float($ticker, 'openPrice'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null, // previous day close
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_tickers ($rawTickers, $symbols = null) {
        $tickers = array();
        for ($i = 0; $i < count($rawTickers); $i++) {
            $tickers[] = $this->parse_ticker($rawTickers[$i]);
        }
        return $this->filter_by_array($tickers, 'symbol', $symbols);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTicker24hr (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker24hr ($params);
        return $this->parse_tickers ($response, $symbols);
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '1m', $since = null, $limit = null) {
        //
        //     {
        //         "m":"bar",
        //         "s":"ETH/BTC",
        //         "ba":"ETH",
        //         "qa":"BTC",
        //         "i":"1",
        //         "t":1570867020000,
        //         "o":"0.022023",
        //         "c":"0.022018",
        //         "h":"0.022023",
        //         "l":"0.022018",
        //         "v":"2.510",
        //     }
        //
        return array(
            $this->safe_integer($ohlcv, 't'),
            $this->safe_float($ohlcv, 'o'),
            $this->safe_float($ohlcv, 'h'),
            $this->safe_float($ohlcv, 'l'),
            $this->safe_float($ohlcv, 'c'),
            $this->safe_float($ohlcv, 'v'),
        );
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
            'interval' => $this->timeframes[$timeframe],
        );
        // if $since and $limit are not specified
        // the exchange will return just 1 last candle by default
        $duration = $this->parse_timeframe($timeframe);
        if ($since !== null) {
            $request['from'] = $since;
            if ($limit !== null) {
                $request['to'] = $this->sum ($since, $limit * $duration * 1000, 1);
            }
        } else if ($limit !== null) {
            $request['to'] = $this->milliseconds ();
            $request['from'] = $request['to'] - $limit * $duration * 1000 - 1;
        }
        $response = $this->publicGetBarhist (array_merge($request, $params));
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function parse_trade ($trade, $market = null) {
        //
        // public fetchTrades
        //
        //     {
        //         "p" => "13.75", // $price
        //         "q" => "6.68", // quantity
        //         "t" => 1528988084944, // $timestamp
        //         "bm" => False, // if true, the buyer is the $market maker, we only use this field to "define the $side" of a public $trade
        //     }
        //
        // private fetchOrderTrades
        //
        //     {
        //         "ap" => "0.029062965", // average filled $price
        //         "bb" => "36851.981", // $base asset total balance
        //         "bc" => "0", // if possitive, this is the BTMX commission charged by reverse mining, if negative, this is the mining output of the current fill.
        //         "bpb" => "36851.981", // $base asset pending balance
        //         "btmxBal" => "0.0", // optional, the BTMX balance of the current account. This field is only available when bc is non-zero.
        //         "cat" => "CASH", // account category => CASH/MARGIN
        //         "coid" => "41g6wtPRFrJXgg6YxjqI6Qoog139Dmoi", // client order id, (needed to cancel order)
        //         "ei" => "NULL_VAL", // execution instruction
        //         "errorCode" => "NULL_VAL", // if the order is rejected, this field explains why
        //         "execId" => "12562285", // for each user, this is a strictly increasing long integer (represented as string)
        //         "f" => "78.074", // filled quantity, this is the aggregated quantity executed by all past fills
        //         "fa" => "BTC", // $fee asset
        //         "$fee" => "0.000693608", // $fee
        //         'lp' => "0.029064", // last $price, the $price executed by the last fill
        //         "l" => "11.932", // last quantity, the quantity executed by the last fill
        //         "m" => "order", // message $type
        //         "orderType" => "Limit", // Limit, Market, StopLimit, StopMarket
        //         "p" => "0.029066", // limit $price, only available for limit and stop limit orders
        //         "q" => "100.000", // order quantity
        //         "qb" => "98878.642957097", // $quote asset total balance
        //         "qpb" => "98877.967247508", // $quote asset pending balance
        //         "s" => "ETH/BTC", // $symbol
        //         "$side" => "Buy", // $side
        //         "status" => "PartiallyFilled", // order status
        //         "t" => 1561131458389, // $timestamp
        //     }
        //
        $timestamp = $this->safe_integer($trade, 't');
        $price = $this->safe_float($trade, 'p');
        $amount = $this->safe_float($trade, 'q');
        $cost = null;
        if (($price !== null) && ($amount !== null)) {
            $cost = $price * $amount;
        }
        $buyerIsMaker = $this->safe_value($trade, 'bm');
        $symbol = null;
        $marketId = $this->safe_string($trade, 's');
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            } else {
                list($baseId, $quoteId) = explode('/', $market);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'fa');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        $orderId = $this->safe_string($trade, 'coid');
        $side = $this->safe_string_lower($trade, 'side');
        if (($side === null) && ($buyerIsMaker !== null)) {
            $side = $buyerIsMaker ? 'buy' : 'sell';
        }
        $type = $this->safe_string_lower($trade, 'orderType');
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'id' => null,
            'order' => $orderId,
            'type' => $type,
            'takerOrMaker' => null,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['n'] = $limit; // currently limited to 100 or fewer
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        //
        //     {
        //         "m" => "marketTrades", // message type
        //         "s" => "ETH/BTC", // $symbol
        //         "$trades" => array(
        //             array(
        //                 "p" => "13.75", // price
        //                 "q" => "6.68", // quantity
        //                 "t" => 1528988084944, // timestamp
        //                 "bm" => False, // if true, the buyer is the $market maker
        //             ),
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_order_status ($status) {
        $statuses = array(
            'PendingNew' => 'open',
            'New' => 'open',
            'PartiallyFilled' => 'open',
            'Filled' => 'closed',
            'Canceled' => 'canceled',
            'Rejected' => 'rejected',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order ($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "coid" => "xxx...xxx",
        //         "action" => "new",
        //         "success" => true  // success = true means the $order has been submitted to the matching engine.
        //     }
        //
        // fetchOrder, fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "accountCategory" => "CASH",
        //         "accountId" => "cshKAhmTHQNUKhR1pQyrDOdotE3Tsnz4",
        //         "avgPrice" => "0.000000000",
        //         "baseAsset" => "ETH",
        //         "btmxCommission" => "0.000000000",
        //         "coid" => "41g6wtPRFrJXgg6Y7vpIkcCyWhgcK0cF", // the unique identifier, you will need, this value to cancel this $order
        //         "errorCode" => "NULL_VAL",
        //         "execId" => "12452288",
        //         "execInst" => "NULL_VAL",
        //         "$fee" => "0.000000000", // cumulative $fee paid for this $order
        //         "feeAsset" => "", // the asset
        //         "filledQty" => "0.000000000", // $filled quantity
        //         "notional" => "0.000000000",
        //         "orderPrice" => "0.310000000", // only available for limit and stop limit orders
        //         "orderQty" => "1.000000000",
        //         "orderType" => "StopLimit",
        //         "quoteAsset" => "BTC",
        //         "$side" => "Buy",
        //         "$status" => "PendingNew",
        //         "stopPrice" => "0.300000000", // only available for stop $market and stop limit orders
        //         "$symbol" => "ETH/BTC",
        //         "time" => 1566091628227, // The last execution time of the $order
        //         "sendingTime" => 1566091503547, // The sending time of the $order
        //         "userId" => "supEQeSJQllKkxYSgLOoVk7hJAX59WSz"
        //     }
        //
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = null;
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            } else {
                list($baseId, $quoteId) = explode('/', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->safe_integer($order, 'sendingTime');
        $price = $this->safe_float($order, 'orderPrice');
        $amount = $this->safe_float($order, 'orderQty');
        $filled = $this->safe_float($order, 'filledQty');
        $remaining = null;
        $cost = $this->safe_float($order, 'cummulativeQuoteQty');
        if ($filled !== null) {
            if ($amount !== null) {
                $remaining = $amount - $filled;
                if ($this->options['parseOrderToPrecision']) {
                    $remaining = floatval ($this->amount_to_precision($symbol, $remaining));
                }
                $remaining = max ($remaining, 0.0);
            }
            if ($price !== null) {
                if ($cost === null) {
                    $cost = $price * $filled;
                }
            }
        }
        $id = $this->safe_string($order, 'coid');
        $type = $this->safe_string($order, 'orderType');
        if ($type !== null) {
            $type = strtolower($type);
            if ($type === 'market') {
                if ($price === 0.0) {
                    if (($cost !== null) && ($filled !== null)) {
                        if (($cost > 0) && ($filled > 0)) {
                            $price = $cost / $filled;
                        }
                    }
                }
            }
        }
        $side = $this->safe_string_lower($order, 'side');
        $fee = array(
            'cost' => $this->safe_float($order, 'fee'),
            'currency' => $this->safe_string($order, 'feeAsset'),
        );
        $average = $this->safe_float($order, 'avgPrice');
        return array(
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        );
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = $this->market ($symbol);
        $request = array(
            'coid' => $this->coid (), // a unique identifier of length 32
            // 'time' => $this->milliseconds (), // milliseconds since UNIX epoch in UTC, this is filled in the private section of the sign() method below
            'symbol' => $market['id'],
            // 'orderPrice' => $this->price_to_precision($symbol, $price), // optional, limit $price of the order. This field is required for limit orders and stop limit orders
            // 'stopPrice' => '15.7', // optional, stopPrice of the order. This field is required for stop_market orders and stop limit orders
            'orderQty' => $this->amount_to_precision($symbol, $amount),
            'orderType' => $type, // order $type, you shall specify one of the following => "limit", "$market", "stop_market", "stop_limit"
            'side' => $side, // "buy" or "sell"
            // 'postOnly' => true, // optional, if true, the order will either be posted to the limit order book or be cancelled, i.e. the order cannot take liquidity, default is false
            // 'timeInForce' => 'GTC', // optional, supports "GTC" good-till-canceled and "IOC" immediate-or-cancel
        );
        if (($type === 'limit') || ($type === 'stop_limit')) {
            $request['orderPrice'] = $this->price_to_precision($symbol, $price);
        }
        $response = $this->privatePostOrder (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "email" => "foo@bar.com", // this field will be deprecated soon
        //         "status" => "success", // this field will be deprecated soon
        //         "$data" => {
        //             "coid" => "xxx...xxx",
        //             "action" => "new",
        //             "success" => true, // success = true means the order has been submitted to the matching engine
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data, $market);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
        }
        $request = array(
            'coid' => $id,
        );
        $response = $this->privateGetOrderCoid (array_merge($request, $params));
        //
        //     {
        //         'code' => 0,
        //         'status' => 'success', // this field will be deprecated soon
        //         'email' => 'foo@bar.com', // this field will be deprecated soon
        //         "$data" => {
        //             "accountCategory" => "CASH",
        //             "accountId" => "cshKAhmTHQNUKhR1pQyrDOdotE3Tsnz4",
        //             "avgPrice" => "0.000000000",
        //             "baseAsset" => "ETH",
        //             "btmxCommission" => "0.000000000",
        //             "coid" => "41g6wtPRFrJXgg6Y7vpIkcCyWhgcK0cF", // the unique identifier, you will need, this value to cancel this order
        //             "errorCode" => "NULL_VAL",
        //             "execId" => "12452288",
        //             "execInst" => "NULL_VAL",
        //             "fee" => "0.000000000", // cumulative fee paid for this order
        //             "feeAsset" => "", // the asset
        //             "filledQty" => "0.000000000", // filled quantity
        //             "notional" => "0.000000000",
        //             "orderPrice" => "0.310000000", // only available for limit and stop limit orders
        //             "orderQty" => "1.000000000",
        //             "orderType" => "StopLimit",
        //             "quoteAsset" => "BTC",
        //             "side" => "Buy",
        //             "status" => "PendingNew",
        //             "stopPrice" => "0.300000000", // only available for stop $market and stop limit orders
        //             "$symbol" => "ETH/BTC",
        //             "time" => 1566091628227, // The last execution time of the order
        //             "sendingTime" => 1566091503547, // The sending time of the order
        //             "userId" => "supEQeSJQllKkxYSgLOoVk7hJAX59WSz"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_order($data, $market);
    }

    public function fetch_order_trades ($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
        }
        $request = array(
            'coid' => $id,
        );
        $response = $this->privateGetOrderFillsCoid (array_merge($request, $params));
        //
        //     {
        //         'code' => 0,
        //         'status' => 'success', // this field will be deprecated soon
        //         'email' => 'foo@bar.com', // this field will be deprecated soon
        //         "$data" => array(
        //             array(
        //                 "ap" => "0.029062965", // average filled price
        //                 "bb" => "36851.981", // base asset total balance
        //                 "bc" => "0", // if possitive, this is the BTMX commission charged by reverse mining, if negative, this is the mining output of the current fill.
        //                 "bpb" => "36851.981", // base asset pending balance
        //                 "btmxBal" => "0.0", // optional, the BTMX balance of the current account. This field is only available when bc is non-zero.
        //                 "cat" => "CASH", // account category => CASH/MARGIN
        //                 "coid" => "41g6wtPRFrJXgg6YxjqI6Qoog139Dmoi", // client order $id, (needed to cancel order)
        //                 "ei" => "NULL_VAL", // execution instruction
        //                 "errorCode" => "NULL_VAL", // if the order is rejected, this field explains why
        //                 "execId" => "12562285", // for each user, this is a strictly increasing long integer (represented as string)
        //                 "f" => "78.074", // filled quantity, this is the aggregated quantity executed by all past fills
        //                 "fa" => "BTC", // fee asset
        //                 "fee" => "0.000693608", // fee
        //                 'lp' => "0.029064", // last price, the price executed by the last fill
        //                 "l" => "11.932", // last quantity, the quantity executed by the last fill
        //                 "m" => "order", // message type
        //                 "orderType" => "Limit", // Limit, Market, StopLimit, StopMarket
        //                 "p" => "0.029066", // $limit price, only available for $limit and stop $limit orders
        //                 "q" => "100.000", // order quantity
        //                 "qb" => "98878.642957097", // quote asset total balance
        //                 "qpb" => "98877.967247508", // quote asset pending balance
        //                 "s" => "ETH/BTC", // $symbol
        //                 "side" => "Buy", // side
        //                 "status" => "PartiallyFilled", // order status
        //                 "t" => 1561131458389, // timestamp
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = null;
        $request = array(
            // 'side' => 'buy', // or 'sell', optional
        );
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['symbol'] = $market['id'];
        }
        $response = $this->privateGetOrderOpen (array_merge($request, $params));
        //
        //     {
        //         'code' => 0,
        //         'status' => 'success', // this field will be deprecated soon
        //         'email' => 'foo@bar.com', // this field will be deprecated soon
        //         "$data" => array(
        //             array(
        //                 "accountCategory" => "CASH",
        //                 "accountId" => "cshKAhmTHQNUKhR1pQyrDOdotE3Tsnz4",
        //                 "avgPrice" => "0.000000000",
        //                 "baseAsset" => "ETH",
        //                 "btmxCommission" => "0.000000000",
        //                 "coid" => "41g6wtPRFrJXgg6Y7vpIkcCyWhgcK0cF", // the unique identifier, you will need, this value to cancel this order
        //                 "errorCode" => "NULL_VAL",
        //                 "execId" => "12452288",
        //                 "execInst" => "NULL_VAL",
        //                 "fee" => "0.000000000", // cumulative fee paid for this order
        //                 "feeAsset" => "", // the asset
        //                 "filledQty" => "0.000000000", // filled quantity
        //                 "notional" => "0.000000000",
        //                 "orderPrice" => "0.310000000", // only available for $limit and stop $limit orders
        //                 "orderQty" => "1.000000000",
        //                 "orderType" => "StopLimit",
        //                 "quoteAsset" => "BTC",
        //                 "side" => "Buy",
        //                 "status" => "PendingNew",
        //                 "stopPrice" => "0.300000000", // only available for stop $market and stop $limit orders
        //                 "$symbol" => "ETH/BTC",
        //                 "time" => 1566091628227, // The last execution time of the order
        //                 "sendingTime" => 1566091503547, // The sending time of the order
        //                 "userId" => "supEQeSJQllKkxYSgLOoVk7hJAX59WSz"
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $market = null;
        $request = array(
            // 'symbol' => 'ETH/BTC', // optional
            // 'category' => 'CASH', // optional, string
            // 'orderType' => 'Market', // optional, string
            // 'page' => 1, // optional, integer type, starts at 1
            // 'pageSize' => 100, // optional, integer type
            // 'side' => 'buy', // or 'sell', optional, case insensitive.
            // 'startTime' => 1566091628227, // optional, integer milliseconds $since UNIX epoch representing the start of the range
            // 'endTime' => 1566091628227, // optional, integer milliseconds $since UNIX epoch representing the end of the range
            // 'status' => 'Filled', // optional, can only be one of "Filled", "Canceled", "Rejected"
        );
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['startTime'] = $since;
        }
        if ($limit !== null) {
            $request['n'] = $limit; // default 15, max 50
        }
        $response = $this->privateGetOrderHistory (array_merge($request, $params));
        //
        //     {
        //         'code' => 0,
        //         'status' => 'success', // this field will be deprecated soon
        //         'email' => 'foo@bar.com', // this field will be deprecated soon
        //         'data' => {
        //             'page' => 1,
        //             'pageSize' => 20,
        //             'limit' => 500,
        //             'hasNext' => False,
        //             'data' => array(
        //                 array(
        //                     'time' => 1566091429000, // The last execution time of the order (This timestamp is in second level resolution)
        //                     'coid' => 'QgQIMJhPFrYfUf60ZTihmseTqhzzwOCx',
        //                     'execId' => '331',
        //                     'symbol' => 'BTMX/USDT',
        //                     'orderType' => 'Market',
        //                     'baseAsset' => 'BTMX',
        //                     'quoteAsset' => 'USDT',
        //                     'side' => 'Buy',
        //                     'stopPrice' => '0.000000000', // only meaningful for stop $market and stop $limit $orders
        //                     'orderPrice' => '0.123000000', // only meaningful for $limit and stop $limit $orders
        //                     'orderQty' => '9229.409000000',
        //                     'filledQty' => '9229.409000000',
        //                     'avgPrice' => '0.095500000',
        //                     'fee' => '0.352563424',
        //                     'feeAsset' => 'USDT',
        //                     'btmxCommission' => '0.000000000',
        //                     'status' => 'Filled',
        //                     'notional' => '881.408559500',
        //                     'userId' => '5DNEppWy33SayHjFQpgQUTjwNMSjEhD3',
        //                     'accountId' => 'ACPHERRWRIA3VQADMEAB2ZTLYAXNM3PJ',
        //                     'accountCategory' => 'CASH',
        //                     'errorCode' => 'NULL_VAL',
        //                     'execInst' => 'NULL_VAL',
        //                     "sendingTime" => 1566091382736, // The sending time of the order
        //                ),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $orders = $this->safe_value($data, 'data', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder requires a $symbol argument');
        }
        $this->load_markets();
        $this->load_accounts();
        $market = $this->market ($symbol);
        $request = array(
            'symbol' => $market['id'],
            'coid' => $this->coid (),
            'origCoid' => $id,
            // 'time' => $this->milliseconds (), // this is filled in the private section of the sign() method below
        );
        $response = $this->privateDeleteOrder (array_merge($request, $params));
        //
        //     {
        //         'code' => 0,
        //         'status' => 'success', // this field will be deprecated soon
        //         'email' => 'foo@bar.com', // this field will be deprecated soon
        //         'data' => {
        //             'action' => 'cancel',
        //             'coid' => 'gaSRTi3o3Yo4PaXpVK0NSLP47vmJuLea',
        //             'success' => True,
        //         }
        //     }
        //
        $order = $this->safe_value($response, 'data', array());
        return $this->parse_order($order);
    }

    public function cancel_all_orders ($symbol = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $request = array(
            // 'side' => 'buy', // optional string field (case-insensitive), either "buy" or "sell"
        );
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['symbol'] = $market['id']; // optional
        }
        $response = $this->privateDeleteOrderAll (array_merge($request, $params));
        //
        //     ?
        //
        return $response;
    }

    public function coid () {
        $uuid = $this->uuid ();
        $parts = explode('-', $uuid);
        $clientOrderId = implode('', $parts);
        $coid = mb_substr($clientOrderId, 0, 32 - 0);
        return $coid;
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $currency = $this->currency ($code);
        $request = array(
            'requestId' => $this->coid (),
            // 'time' => $this->milliseconds (), // this is filled in the private section of the sign() method below
            'assetCode' => $currency['id'],
        );
        // note => it is highly recommended to use V2 version of this route,
        // especially for assets with multiple block chains such as USDT.
        $response = $this->privateGetDeposit (array_merge($request, $params));
        //
        // v1
        //
        //     {
        //         "data" => array(
        //             "$address" => "0x26a3CB49578F07000575405a57888681249c35Fd"
        //         ),
        //         "email" => "igor.kroitor@gmial.com",
        //         "status" => "success",
        //     }
        //
        // v2
        //
        //     {
        //         "$code" => 0,
        //         "data" => array(
        //             {
        //                 "asset" => "XRP",
        //                 "blockChain" => "Ripple",
        //                 "$addressData" => {
        //                     "$address" => "rpinhtY4p35bPmVXPbfWRUtZ1w1K1gYShB",
        //                     "destTag" => "54301"
        //                 }
        //             }
        //         ),
        //         "email" => "xxx@xxx.com",
        //         "status" => "success" // the $request has been submitted to the server
        //     }
        //
        $addressData = $this->safe_value($response, 'data');
        if (gettype($addressData) === 'array' && count(array_filter(array_keys($addressData), 'is_string')) == 0) {
            $firstElement = $this->safe_value($addressData, 0, array());
            $addressData = $this->safe_value($firstElement, 'addressData', array());
        }
        $address = $this->safe_string($addressData, 'address');
        $tag = $this->safe_string($addressData, 'destTag');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '/api/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else {
            $this->check_required_credentials();
            $accountGroup = $this->safe_string($this->options, 'accountGroup');
            if ($accountGroup === null) {
                if ($this->accounts !== null) {
                    $accountGroup = $this->accounts[0]['id'];
                }
            }
            if ($accountGroup !== null) {
                $url = '/' . $accountGroup . $url;
            }
            $coid = $this->safe_string($query, 'coid');
            $query['time'] = (string) $this->milliseconds ();
            $auth = $query['time'] . '+' . str_replace('/{$coid}', '', $path); // fix sign error
            $headers = array(
                'x-$auth-key' => $this->apiKey,
                'x-$auth-timestamp' => $query['time'],
                'Content-Type' => 'application/json',
            );
            if ($coid !== null) {
                $auth .= '+' . $coid;
                $headers['x-$auth-coid'] = $coid;
            }
            $signature = $this->hmac ($this->encode ($auth), $this->encode ($this->secret), 'sha256', 'base64');
            $headers['x-$auth-signature'] = $signature;
            if ($method === 'GET') {
                if ($query) {
                    $url .= '?' . $this->urlencode ($query);
                }
            } else {
                $body = $this->json ($query);
            }
        }
        $url = $this->urls['api'] . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        //
        //     array("$code":2100,"$message":"ApiKeyFailure")
        //     array('code' => 6010, 'message' => 'Not enough balance.')
        //     array('code' => 60060, 'message' => 'The order is already filled or canceled.')
        //
        $code = $this->safe_string($response, 'code');
        $message = $this->safe_string($response, 'message');
        $error = ($code !== null) && ($code !== '0');
        if ($error || ($message !== null)) {
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $code, $feedback);
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}

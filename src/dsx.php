<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class dsx extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'dsx',
            'name' => 'DSX',
            'countries' => array( 'UK' ),
            'rateLimit' => 1500,
            'version' => 'v3',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createDepositAddress' => true,
                'createMarketOrder' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => false,
                'fetchDepositAddress' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrderBooks' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTransactions' => true,
                'fetchTrades' => true,
                'withdraw' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/76909626-cb2bb100-68bc-11ea-99e0-28ba54f04792.jpg',
                'api' => array(
                    'public' => 'https://dsxglobal.com/mapi', // market data
                    'private' => 'https://dsxglobal.com/tapi', // trading
                    'dwapi' => 'https://dsxglobal.com/dwapi', // deposit/withdraw
                ),
                'www' => 'https://dsxglobal.com',
                'doc' => array(
                    'https://dsxglobal.com/developers/publicApi',
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => true,
                    'percentage' => true,
                    'maker' => 0.15 / 100,
                    'taker' => 0.25 / 100,
                ),
            ),
            'timeframes' => array(
                '1m' => 'm',
                '1h' => 'h',
                '1d' => 'd',
            ),
            'api' => array(
                // market data (public)
                'public' => array(
                    'get' => array(
                        'barsFromMoment/{pair}/{period}/{start}',
                        'depth/{pair}',
                        'info',
                        'lastBars/{pair}/{period}/{amount}', // period is 'm', 'h' or 'd'
                        'periodBars/{pair}/{period}/{start}/{end}',
                        'ticker/{pair}',
                        'trades/{pair}',
                    ),
                ),
                // trading (private)
                'private' => array(
                    'post' => array(
                        'info/account',
                        'history/transactions',
                        'history/trades',
                        'history/orders',
                        'orders',
                        'order/cancel',
                        // 'order/cancel/all',
                        'order/status',
                        'order/new',
                        'volume',
                        'fees', // trading fee schedule
                    ),
                ),
                // deposit / withdraw (private)
                'dwapi' => array(
                    'post' => array(
                        'deposit/cryptoaddress',
                        'withdraw/crypto',
                        'withdraw/fiat',
                        'withdraw/submit',
                        // 'withdraw/cancel',
                        'transaction/status', // see 'history/transactions' in private tapi above
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'Sign is invalid' => '\\ccxt\\AuthenticationError', // array("success":0,"error":"Sign is invalid")
                    'Order was rejected. Incorrect price.' => '\\ccxt\\InvalidOrder', // array("success":0,"error":"Order was rejected. Incorrect price.")
                    "Order was rejected. You don't have enough money." => '\\ccxt\\InsufficientFunds', // array("success":0,"error":"Order was rejected. You don't have enough money.")
                    'This method is blocked for your pair of keys' => '\\ccxt\\PermissionDenied', // array("success":0,"error":"This method is blocked for your pair of keys")
                ),
                'broad' => array(
                    'INVALID_PARAMETER' => '\\ccxt\\BadRequest',
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
                    'nonce is invalid' => '\\ccxt\\InvalidNonce', // array("success":0,"error":"Parameter => nonce is invalid")
                    'Incorrect volume' => '\\ccxt\\InvalidOrder', // array("success" => 0,"error":"Order was rejected. Incorrect volume.")
                ),
            ),
            'options' => array(
                'fetchTickersMaxLength' => 250,
            ),
            'commonCurrencies' => array(
                'DSH' => 'DASH',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetInfo ($params);
        //
        //     {
        //         "server_time" => 1522057909,
        //         "pairs" => {
        //             "ethusd" => {
        //                 "decimal_places" => 5,
        //                 "min_price" => 100,
        //                 "max_price" => 1500,
        //                 "min_amount" => 0.01,
        //                 "$hidden" => 0,
        //                 "fee" => 0,
        //                 "amount_decimal_places" => 4,
        //                 "quoted_currency" => "USD",
        //                 "base_currency" => "ETH"
        //             }
        //         }
        //     }
        //
        $markets = $this->safe_value($response, 'pairs');
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $market = $markets[$id];
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quoted_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'decimal_places'),
                'price' => $this->safe_integer($market, 'decimal_places'),
            );
            $amountLimits = array(
                'min' => $this->safe_float($market, 'min_amount'),
                'max' => $this->safe_float($market, 'max_amount'),
            );
            $priceLimits = array(
                'min' => $this->safe_float($market, 'min_price'),
                'max' => $this->safe_float($market, 'max_price'),
            );
            $costLimits = array(
                'min' => $this->safe_float($market, 'min_total'),
            );
            $limits = array(
                'amount' => $amountLimits,
                'price' => $priceLimits,
                'cost' => $costLimits,
            );
            $hidden = $this->safe_integer($market, 'hidden');
            $active = ($hidden === 0);
            // see parseMarket below
            // https://github.com/ccxt/ccxt/pull/5786
            $otherId = strtolower($base) . strtolower($quote);
            $result[] = array(
                'id' => $id,
                'otherId' => $otherId, // https://github.com/ccxt/ccxt/pull/5786
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostInfoAccount ();
        //
        //     {
        //         "success" : 1,
        //         "return" : {
        //             "$funds" : {
        //                 "BTC" : array(
        //                     "total" : 0,
        //                     "available" : 0
        //                 ),
        //                 "USD" : array(
        //                     "total" : 0,
        //                     "available" : 0
        //                 ),
        //                 "USDT" : array(
        //                     "total" : 0,
        //                     "available" : 0
        //                 }
        //             ),
        //             "rights" : array(
        //                 "info" : 1,
        //                 "trade" : 1
        //             ),
        //             "transactionCount" : 0,
        //             "openOrders" : 0,
        //             "serverTime" : 1537451465
        //         }
        //     }
        //
        $balances = $this->safe_value($response, 'return');
        $result = array( 'info' => $response );
        $funds = $this->safe_value($balances, 'funds');
        $currencyIds = is_array($funds) ? array_keys($funds) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $code = $this->safe_currency_code($currencyId);
            $balance = $this->safe_value($funds, $currencyId, array());
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['total'] = $this->safe_float($balance, 'total');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //   {    high =>  0.03492,
        //         low =>  0.03245,
        //         avg =>  29.46133,
        //         vol =>  500.8661,
        //     vol_cur =>  17.000797104,
        //        $last =>  0.03364,
        //         buy =>  0.03362,
        //        sell =>  0.03381,
        //     updated =>  1537521993,
        //        pair => "ethbtc"       }
        //
        $timestamp = $this->safe_timestamp($ticker, 'updated');
        $symbol = null;
        $marketId = $this->safe_string($ticker, 'pair');
        $market = $this->parse_market($marketId);
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        // dsx $average is inverted, liqui $average is not
        $average = $this->safe_float($ticker, 'avg');
        if ($average !== null) {
            if ($average > 0) {
                $average = 1 / $average;
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
            'average' => $average,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => $this->safe_float($ticker, 'vol_cur'),
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "$amount" : 0.0128,
        //         "$price" : 6483.99000,
        //         "$timestamp" : 1540334614,
        //         "tid" : 35684364,
        //         "$type" : "ask"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "number" => "36635882", // <-- this is present if the $trade has come from the '/order/status' call
        //         "$id" => "36635882", // <-- this may have been artifically added by the parseTrades method
        //         "pair" => "btcusd",
        //         "$type" => "buy",
        //         "volume" => 0.0595,
        //         "rate" => 9750,
        //         "$orderId" => 77149299,
        //         "$timestamp" => 1519612317,
        //         "commission" => 0.00020825,
        //         "commissionCurrency" => "btc"
        //     }
        //
        $timestamp = $this->safe_timestamp($trade, 'timestamp');
        $side = $this->safe_string($trade, 'type');
        if ($side === 'ask') {
            $side = 'sell';
        } else if ($side === 'bid') {
            $side = 'buy';
        }
        $price = $this->safe_float_2($trade, 'rate', 'price');
        $id = $this->safe_string_2($trade, 'number', 'id');
        $orderId = $this->safe_string($trade, 'orderId');
        $marketId = $this->safe_string($trade, 'pair');
        $market = $this->parse_market($marketId);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $amount = $this->safe_float_2($trade, 'amount', 'volume');
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
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
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

    public function parse_trades($trades, $market = null, $since = null, $limit = null, $params = array ()) {
        $result = array();
        if (gettype($trades) === 'array' && count(array_filter(array_keys($trades), 'is_string')) == 0) {
            for ($i = 0; $i < count($trades); $i++) {
                $result[] = $this->parse_trade($trades[$i], $market);
            }
        } else {
            $ids = is_array($trades) ? array_keys($trades) : array();
            for ($i = 0; $i < count($ids); $i++) {
                $id = $ids[$i];
                $trade = $this->parse_trade($trades[$id], $market);
                $result[] = array_merge($trade, array( 'id' => $id ), $params);
            }
        }
        $result = $this->sort_by($result, 'timestamp');
        $symbol = ($market !== null) ? $market['symbol'] : null;
        return $this->filter_by_symbol_since_limit($result, $symbol, $since, $limit);
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
            $symbol = $id;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            }
            $result[$symbol] = $this->parse_order_book($response[$id]);
        }
        return $result;
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $ids = $this->ids;
        if ($symbols === null) {
            $numIds = is_array($ids) ? count($ids) : 0;
            $ids = implode('-', $ids);
            $maxLength = $this->safe_integer($this->options, 'fetchTickersMaxLength', 2048);
            // max URL length is 2048 $symbols, including http schema, hostname, tld, etc...
            if (strlen($ids) > $this->options['fetchTickersMaxLength']) {
                throw new ArgumentsRequired($this->id . ' has ' . (string) $numIds . ' markets exceeding max URL length for this endpoint (' . (string) $maxLength . ' characters), please, specify a list of $symbols of interest in the first argument to fetchTickers');
            }
        } else {
            $ids = $this->market_ids($symbols);
            $ids = implode('-', $ids);
        }
        $request = array(
            'pair' => $ids,
        );
        $tickers = $this->publicGetTickerPair (array_merge($request, $params));
        //
        //     {
        //         "bchbtc" : array(
        //             "high" : 0.02989,
        //             "low" : 0.02736,
        //             "avg" : 33.90585,
        //             "vol" : 0.65982205,
        //             "vol_cur" : 0.0194604180960,
        //             "last" : 0.03000,
        //             "buy" : 0.02980,
        //             "sell" : 0.03001,
        //             "updated" : 1568104614,
        //             "pair" : "bchbtc"
        //         ),
        //         "ethbtc" : {
        //             "high" : 0.01772,
        //             "low" : 0.01742,
        //             "avg" : 56.89082,
        //             "vol" : 229.247115044,
        //             "vol_cur" : 4.02959737298943,
        //             "last" : 0.01769,
        //             "buy" : 0.01768,
        //             "sell" : 0.01776,
        //             "updated" : 1568104614,
        //             "pair" : "ethbtc"
        //         }
        //     }
        //
        $result = array();
        $keys = is_array($tickers) ? array_keys($tickers) : array();
        for ($k = 0; $k < count($keys); $k++) {
            $id = $keys[$k];
            $ticker = $tickers[$id];
            $symbol = $id;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            }
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $tickers = $this->fetch_tickers(array( $symbol ), $params);
        return $tickers[$symbol];
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

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "high" : 0.01955,
        //         "open" : 0.01955,
        //         "low" : 0.01955,
        //         "close" : 0.01955,
        //         "amount" : 2.5,
        //         "timestamp" : 1565155740000
        //     }
        //
        return array(
            $this->safe_integer($ohlcv, 'timestamp'),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'amount'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
            'period' => $this->timeframes[$timeframe],
        );
        $method = 'publicGetLastBarsPairPeriodAmount';
        if ($since === null) {
            if ($limit === null) {
                $limit = 100; // required, max 2000
            }
            $request['amount'] = $limit;
        } else {
            $method = 'publicGetPeriodBarsPairPeriodStartEnd';
            // in their docs they expect milliseconds
            // but it returns empty arrays with milliseconds
            // however, it does work properly with seconds
            $request['start'] = intval($since / 1000);
            if ($limit === null) {
                $request['end'] = $this->seconds();
            } else {
                $duration = $this->parse_timeframe($timeframe) * 1000;
                $end = $this->sum($since, $duration * $limit);
                $request['end'] = intval($end / 1000);
            }
        }
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "ethbtc" => array(
        //             array(
        //                 "high" : 0.01955,
        //                 "open" : 0.01955,
        //                 "low" : 0.01955,
        //                 "close" : 0.01955,
        //                 "amount" : 2.5,
        //                 "timestamp" : 1565155740000
        //             ),
        //             {
        //                 "high" : 0.01967,
        //                 "open" : 0.01967,
        //                 "low" : 0.01967,
        //                 "close" : 0.01967,
        //                 "amount" : 0,
        //                 "timestamp" : 1565155680000
        //             }
        //         )
        //     }
        //
        $candles = $this->safe_value($response, $market['id'], array());
        return $this->parse_ohlcvs($candles, $market, $timeframe, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($type === 'market' && $price === null) {
            throw new ArgumentsRequired($this->id . ' createOrder() requires a $price argument even for $market orders, that is the worst $price that you agree to fill your order for');
        }
        $request = array(
            'pair' => $market['id'],
            'type' => $side,
            'volume' => $this->amount_to_precision($symbol, $amount),
            'rate' => $this->price_to_precision($symbol, $price),
            'orderType' => $type,
        );
        $price = floatval($price);
        $amount = floatval($amount);
        $response = $this->privatePostOrderNew (array_merge($request, $params));
        //
        //     {
        //       "success" => 1,
        //       "return" => {
        //         "received" => 0,
        //         "remains" => 10,
        //         "funds" => {
        //           "BTC" => array(
        //             "total" => 100,
        //             "available" => 95
        //           ),
        //           "USD" => array(
        //             "total" => 10000,
        //             "available" => 9995
        //           ),
        //           "EUR" => array(
        //             "total" => 1000,
        //             "available" => 995
        //           ),
        //           "LTC" => array(
        //             "total" => 1000,
        //             "available" => 995
        //           }
        //         ),
        //         "orderId" => 0, // https://github.com/ccxt/ccxt/issues/3677
        //       }
        //     }
        //
        $status = 'open';
        $filled = 0.0;
        $remaining = $amount;
        $responseReturn = $this->safe_value($response, 'return');
        $id = $this->safe_string_2($responseReturn, 'orderId', 'order_id');
        if ($id === '0') {
            $id = $this->safe_string($responseReturn, 'initOrderId', 'init_order_id');
            $status = 'closed';
        }
        $filled = $this->safe_float($responseReturn, 'received', 0.0);
        $remaining = $this->safe_float($responseReturn, 'remains', $amount);
        $timestamp = $this->milliseconds();
        return array(
            'info' => $response,
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
        );
    }

    public function parse_order_status($status) {
        $statuses = array(
            '0' => 'open', // Active
            '1' => 'closed', // Filled
            '2' => 'canceled', // Killed
            '3' => 'canceling', // Killing
            '7' => 'canceled', // Rejected
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_market($id) {
        if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
            return $this->markets_by_id[$id];
        } else {
            // the following is a fix for
            // https://github.com/ccxt/ccxt/pull/5786
            // https://github.com/ccxt/ccxt/issues/5770
            $markets_by_other_id = $this->safe_value($this->options, 'markets_by_other_id');
            if ($markets_by_other_id === null) {
                $this->options['markets_by_other_id'] = $this->index_by($this->markets, 'otherId');
                $markets_by_other_id = $this->options['markets_by_other_id'];
            }
            if (is_array($markets_by_other_id) && array_key_exists($id, $markets_by_other_id)) {
                return $markets_by_other_id[$id];
            }
        }
        return null;
    }

    public function parse_order($order, $market = null) {
        //
        // fetchOrder
        //
        //   {
        //     "number" => 36635882,
        //     "pair" => "btcusd",
        //     "type" => "buy",
        //     "remainingVolume" => 10,
        //     "volume" => 10,
        //     "rate" => 1000.0,
        //     "timestampCreated" => 1496670,
        //     "$status" => 0,
        //     "$orderType" => "limit",
        //     "$deals" => array(
        //       {
        //         "pair" => "btcusd",
        //         "type" => "buy",
        //         "$amount" => 1,
        //         "rate" => 1000.0,
        //         "orderId" => 1,
        //         "$timestamp" => 1496672724,
        //         "commission" => 0.001,
        //         "commissionCurrency" => "btc"
        //       }
        //     )
        //   }
        //
        $id = $this->safe_string($order, 'id');
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $timestamp = $this->safe_timestamp($order, 'timestampCreated');
        $marketId = $this->safe_string($order, 'pair');
        $market = $this->parse_market($marketId);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $remaining = $this->safe_float($order, 'remainingVolume');
        $amount = $this->safe_float($order, 'volume');
        $price = $this->safe_float($order, 'rate');
        $filled = null;
        $cost = null;
        if ($amount !== null) {
            if ($remaining !== null) {
                $filled = $amount - $remaining;
                $cost = $price * $filled;
            }
        }
        $orderType = $this->safe_string($order, 'orderType');
        $side = $this->safe_string($order, 'type');
        $fee = null;
        $deals = $this->safe_value($order, 'deals', array());
        $numDeals = is_array($deals) ? count($deals) : 0;
        $trades = null;
        $lastTradeTimestamp = null;
        if ($numDeals > 0) {
            $trades = $this->parse_trades($deals);
            $feeCost = null;
            $feeCurrency = null;
            for ($i = 0; $i < count($trades); $i++) {
                $trade = $trades[$i];
                if ($feeCost === null) {
                    $feeCost = 0;
                }
                $feeCost = $this->sum($feeCost, $trade['fee']['cost']);
                $feeCurrency = $trade['fee']['currency'];
                $lastTradeTimestamp = $trade['timestamp'];
            }
            if ($feeCost !== null) {
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $feeCurrency,
                );
            }
        }
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'type' => $orderType,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'remaining' => $remaining,
            'filled' => $filled,
            'status' => $status,
            'fee' => $fee,
            'trades' => $trades,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => intval($id),
        );
        $response = $this->privatePostOrderStatus (array_merge($request, $params));
        //
        //     {
        //       "success" => 1,
        //       "return" => {
        //         "pair" => "btcusd",
        //         "type" => "buy",
        //         "remainingVolume" => 10,
        //         "volume" => 10,
        //         "rate" => 1000.0,
        //         "timestampCreated" => 1496670,
        //         "status" => 0,
        //         "orderType" => "limit",
        //         "deals" => array(
        //           {
        //             "pair" => "btcusd",
        //             "type" => "buy",
        //             "amount" => 1,
        //             "rate" => 1000.0,
        //             "orderId" => 1,
        //             "timestamp" => 1496672724,
        //             "commission" => 0.001,
        //             "commissionCurrency" => "btc"
        //           }
        //         )
        //       }
        //     }
        //
        return $this->parse_order(array_merge(array(
            'id' => $id,
        ), $response['return']));
    }

    public function parse_orders_by_id($orders, $symbol = null, $since = null, $limit = null) {
        $ids = is_array($orders) ? array_keys($orders) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $order = $this->parse_order(array_merge(array(
                'id' => (string) $id,
            ), $orders[$id]));
            $result[] = $order;
        }
        return $this->filter_by_symbol_since_limit($result, $symbol, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'count' => 10, // Decimal, The maximum number of orders to return
            // 'fromId' => 123, // Decimal, ID of the first order of the selection
            // 'endId' => 321, // Decimal, ID of the last order of the selection
            // 'order' => 'ASC', // String, Order in which orders shown. Possible values are "ASC" — from first to last, "DESC" — from last to first.
        );
        $response = $this->privatePostOrders (array_merge($request, $params));
        //
        //     {
        //       "success" => 1,
        //       "return" => {
        //         "0" => {
        //           "pair" => "btcusd",
        //           "type" => "buy",
        //           "remainingVolume" => 10,
        //           "volume" => 10,
        //           "rate" => 1000.0,
        //           "timestampCreated" => 1496670,
        //           "status" => 0,
        //           "orderType" => "$limit"
        //         }
        //       }
        //     }
        //
        return $this->parse_orders_by_id($this->safe_value($response, 'return', array()), $symbol, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'count' => 10, // Decimal, The maximum number of orders to return
            // 'fromId' => 123, // Decimal, ID of the first order of the selection
            // 'endId' => 321, // Decimal, ID of the last order of the selection
            // 'order' => 'ASC', // String, Order in which orders shown. Possible values are "ASC" — from first to last, "DESC" — from last to first.
        );
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->privatePostHistoryOrders (array_merge($request, $params));
        //
        //     {
        //       "success" => 1,
        //       "return" => {
        //         "0" => {
        //           "pair" => "btcusd",
        //           "type" => "buy",
        //           "remainingVolume" => 10,
        //           "volume" => 10,
        //           "rate" => 1000.0,
        //           "timestampCreated" => 1496670,
        //           "status" => 0,
        //           "orderType" => "$limit"
        //         }
        //       }
        //     }
        //
        return $this->parse_orders_by_id($this->safe_value($response, 'return', array()), $symbol, $since, $limit);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderId' => $id,
        );
        $response = $this->privatePostOrderCancel (array_merge($request, $params));
        return $response;
    }

    public function parse_orders($orders, $market = null, $since = null, $limit = null, $params = array ()) {
        $result = array();
        $ids = is_array($orders) ? array_keys($orders) : array();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $order = array_merge(array( 'id' => $id ), $orders[$id]);
            $result[] = array_merge($this->parse_order($order, $market), $params);
        }
        return $this->filter_by_symbol_since_limit($result, $symbol, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        return $this->filter_by($orders, 'status', 'closed');
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
        $response = $this->privatePostHistoryTrades (array_merge($request, $params));
        $trades = array();
        if (is_array($response) && array_key_exists('return', $response)) {
            $trades = $response['return'];
        }
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array();
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($since !== null) {
            $request['since'] = $since;
        }
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->privatePostHistoryTransactions (array_merge($request, $params));
        //
        //     {
        //         "success" => 1,
        //         "return" => array(
        //             {
        //                 "id" => 1,
        //                 "timestamp" => 11,
        //                 "type" => "Withdraw",
        //                 "amount" => 1,
        //                 "$currency" => "btc",
        //                 "confirmationsCount" => 6,
        //                 "address" => "address",
        //                 "status" => 2,
        //                 "commission" => 0.0001
        //             }
        //         )
        //     }
        //
        $transactions = $this->safe_value($response, 'return', array());
        return $this->parse_transactions($transactions, $currency, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            '1' => 'failed',
            '2' => 'ok',
            '3' => 'pending',
            '4' => 'failed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //     {
        //         "id" => 1,
        //         "$timestamp" => 11, // 11 in their docs (
        //         "$type" => "Withdraw",
        //         "amount" => 1,
        //         "$currency" => "btc",
        //         "confirmationsCount" => 6,
        //         "address" => "address",
        //         "$status" => 2,
        //         "commission" => 0.0001
        //     }
        //
        $timestamp = $this->safe_timestamp($transaction, 'timestamp');
        $type = $this->safe_string($transaction, 'type');
        if ($type !== null) {
            if ($type === 'Incoming') {
                $type = 'deposit';
            } else if ($type === 'Withdraw') {
                $type = 'withdrawal';
            }
        }
        $currencyId = $this->safe_string($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'status'));
        return array(
            'id' => $this->safe_string($transaction, 'id'),
            'txid' => $this->safe_string($transaction, 'txid'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $this->safe_string($transaction, 'address'),
            'type' => $type,
            'amount' => $this->safe_float($transaction, 'amount'),
            'currency' => $code,
            'status' => $status,
            'fee' => array(
                'currency' => $code,
                'cost' => $this->safe_float($transaction, 'commission'),
                'rate' => null,
            ),
            'info' => $transaction,
        );
    }

    public function create_deposit_address($code, $params = array ()) {
        $request = array(
            'new' => 1,
        );
        $response = $this->fetch_deposit_address($code, array_merge($request, $params));
        return $response;
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->dwapiPostDepositCryptoaddress (array_merge($request, $params));
        $result = $this->safe_value($response, 'return', array());
        $address = $this->safe_string($result, 'address');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => null, // not documented in DSX API
            'info' => $response,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $commission = $this->safe_value($params, 'commission');
        if ($commission === null) {
            throw new ArgumentsRequired($this->id . ' withdraw() requires a `$commission` (withdrawal fee) parameter (string)');
        }
        $params = $this->omit($params, $commission);
        $request = array(
            'currency' => $currency['id'],
            'amount' => floatval($amount),
            'address' => $address,
            'commission' => $commission,
        );
        if ($tag !== null) {
            $request['address'] .= ':' . $tag;
        }
        $response = $this->dwapiPostWithdrawCrypto (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "success" => 1,
        //             "return" => {
        //                 "transactionId" => 2863073
        //             }
        //         }
        //     )
        //
        $data = $this->safe_value($response, 'return', array());
        $id = $this->safe_string($data, 'transactionId');
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api];
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'private' || $api === 'dwapi') {
            $url .= '/' . $this->version . '/' . $this->implode_params($path, $params);
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array(
                'nonce' => $nonce,
            ), $query));
            $signature = $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512', 'base64');
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
            // 1 - Liqui only returns the integer 'success' key from their private API
            //
            //     array( "$success" => 1, ... ) $httpCode === 200
            //     array( "$success" => 0, ... ) $httpCode === 200
            //
            // 2 - However, exchanges derived from Liqui, can return non-integers
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

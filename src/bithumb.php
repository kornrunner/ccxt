<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;

class bithumb extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bithumb',
            'name' => 'Bithumb',
            'countries' => array( 'KR' ), // South Korea
            'rateLimit' => 500,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createMarketOrder' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'withdraw' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/30597177-ea800172-9d5e-11e7-804c-b9d4fa9b56b0.jpg',
                'api' => array(
                    'public' => 'https://api.bithumb.com/public',
                    'private' => 'https://api.bithumb.com',
                ),
                'www' => 'https://www.bithumb.com',
                'doc' => 'https://apidocs.bithumb.com',
                'fees' => 'https://en.bithumb.com/customer_support/info_fee',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'ticker/{currency}',
                        'ticker/all',
                        'ticker/ALL_BTC',
                        'ticker/ALL_KRW',
                        'orderbook/{currency}',
                        'orderbook/all',
                        'transaction_history/{currency}',
                        'transaction_history/all',
                        'candlestick/{currency}/{interval}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'info/account',
                        'info/balance',
                        'info/wallet_address',
                        'info/ticker',
                        'info/orders',
                        'info/user_transactions',
                        'info/order_detail',
                        'trade/place',
                        'trade/cancel',
                        'trade/btc_withdrawal',
                        'trade/krw_deposit',
                        'trade/krw_withdrawal',
                        'trade/market_buy',
                        'trade/market_sell',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.25 / 100,
                    'taker' => 0.25 / 100,
                ),
            ),
            'precisionMode' => SIGNIFICANT_DIGITS,
            'exceptions' => array(
                'Bad Request(SSL)' => '\\ccxt\\BadRequest',
                'Bad Request(Bad Method)' => '\\ccxt\\BadRequest',
                'Bad Request.(Auth Data)' => '\\ccxt\\AuthenticationError', // array( "status" => "5100", "message" => "Bad Request.(Auth Data)" )
                'Not Member' => '\\ccxt\\AuthenticationError',
                'Invalid Apikey' => '\\ccxt\\AuthenticationError', // array("status":"5300","message":"Invalid Apikey")
                'Method Not Allowed.(Access IP)' => '\\ccxt\\PermissionDenied',
                'Method Not Allowed.(BTC Adress)' => '\\ccxt\\InvalidAddress',
                'Method Not Allowed.(Access)' => '\\ccxt\\PermissionDenied',
                'Database Fail' => '\\ccxt\\ExchangeNotAvailable',
                'Invalid Parameter' => '\\ccxt\\BadRequest',
                '5600' => '\\ccxt\\ExchangeError',
                'Unknown Error' => '\\ccxt\\ExchangeError',
                'After May 23th, recent_transactions is no longer, hence users will not be able to connect to recent_transactions' => '\\ccxt\\ExchangeError', // array("status":"5100","message":"After May 23th, recent_transactions is no longer, hence users will not be able to connect to recent_transactions")
            ),
            'timeframes' => array(
                '1m' => '1m',
                '3m' => '3m',
                '5m' => '5m',
                '10m' => '10m',
                '30m' => '30m',
                '1h' => '1h',
                '6h' => '6h',
                '12h' => '12h',
                '1d' => '24h',
            ),
            'options' => array(
                'quoteCurrencies' => array(
                    'BTC' => array(
                        'precision' => array(
                            'price' => 8,
                        ),
                    ),
                    'KRW' => array(),
                ),
            ),
        ));
    }

    public function amount_to_precision($symbol, $amount) {
        return $this->decimal_to_precision($amount, TRUNCATE, $this->markets[$symbol]['precision']['amount'], DECIMAL_PLACES);
    }

    public function fetch_markets($params = array ()) {
        $result = array();
        $quoteCurrencies = $this->safe_value($this->options, 'quoteCurrencies', array());
        $quotes = is_array($quoteCurrencies) ? array_keys($quoteCurrencies) : array();
        for ($i = 0; $i < count($quotes); $i++) {
            $quote = $quotes[$i];
            $extension = $this->safe_value($quoteCurrencies, $quote, array());
            $method = 'publicGetTickerALL' . $quote;
            $response = $this->$method ($params);
            $data = $this->safe_value($response, 'data');
            $currencyIds = is_array($data) ? array_keys($data) : array();
            for ($j = 0; $j < count($currencyIds); $j++) {
                $currencyId = $currencyIds[$j];
                if ($currencyId === 'date') {
                    continue;
                }
                $market = $data[$currencyId];
                $base = $this->safe_currency_code($currencyId);
                $symbol = $currencyId . '/' . $quote;
                $active = true;
                if (gettype($market) === 'array' && count(array_filter(array_keys($market), 'is_string')) == 0) {
                    $numElements = is_array($market) ? count($market) : 0;
                    if ($numElements === 0) {
                        $active = false;
                    }
                }
                $entry = $this->deep_extend(array(
                    'id' => $currencyId,
                    'symbol' => $symbol,
                    'base' => $base,
                    'quote' => $quote,
                    'info' => $market,
                    'active' => $active,
                    'precision' => array(
                        'amount' => 4,
                        'price' => 4,
                    ),
                    'limits' => array(
                        'amount' => array(
                            'min' => null,
                            'max' => null,
                        ),
                        'price' => array(
                            'min' => null,
                            'max' => null,
                        ),
                        'cost' => array(
                            'min' => 500,
                            'max' => 5000000000,
                        ),
                    ),
                    'baseId' => null,
                    'quoteId' => null,
                ), $extension);
                $result[] = $entry;
            }
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $request = array(
            'currency' => 'ALL',
        );
        $response = $this->privatePostInfoBalance (array_merge($request, $params));
        $result = array( 'info' => $response );
        $balances = $this->safe_value($response, 'data');
        $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $account = $this->account();
            $currency = $this->currency($code);
            $lowerCurrencyId = $this->safe_string_lower($currency, 'id');
            $account['total'] = $this->safe_float($balances, 'total_' . $lowerCurrencyId);
            $account['used'] = $this->safe_float($balances, 'in_use_' . $lowerCurrencyId);
            $account['free'] = $this->safe_float($balances, 'available_' . $lowerCurrencyId);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['base'],
        );
        if ($limit !== null) {
            $request['count'] = $limit; // default 30, max 30
        }
        $response = $this->publicGetOrderbookCurrency (array_merge($request, $params));
        //
        //     {
        //         "status":"0000",
        //         "$data":{
        //             "$timestamp":"1587621553942",
        //             "payment_currency":"KRW",
        //             "order_currency":"BTC",
        //             "bids":array(
        //                 array("price":"8652000","quantity":"0.0043"),
        //                 array("price":"8651000","quantity":"0.0049"),
        //                 array("price":"8650000","quantity":"8.4791"),
        //             ),
        //             "asks":array(
        //                 array("price":"8654000","quantity":"0.119"),
        //                 array("price":"8655000","quantity":"0.254"),
        //                 array("price":"8658000","quantity":"0.119"),
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $timestamp = $this->safe_integer($data, 'timestamp');
        return $this->parse_order_book($data, $timestamp, 'bids', 'asks', 'price', 'quantity');
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // fetchTicker, fetchTickers
        //
        //     {
        //         "opening_price":"227100",
        //         "closing_price":"228400",
        //         "min_price":"222300",
        //         "max_price":"230000",
        //         "units_traded":"82618.56075337",
        //         "acc_trade_value":"18767376138.6031",
        //         "prev_closing_price":"227100",
        //         "units_traded_24H":"151871.13484676",
        //         "acc_trade_value_24H":"34247610416.8974",
        //         "fluctate_24H":"8700",
        //         "fluctate_rate_24H":"3.96",
        //         "date":"1587710327264", // fetchTickers inject this
        //     }
        //
        $timestamp = $this->safe_integer($ticker, 'date');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $open = $this->safe_float($ticker, 'opening_price');
        $close = $this->safe_float($ticker, 'closing_price');
        $change = null;
        $percentage = null;
        $average = null;
        if (($close !== null) && ($open !== null)) {
            $change = $close - $open;
            if ($open > 0) {
                $percentage = $change / $open * 100;
            }
            $average = $this->sum($open, $close) / 2;
        }
        $baseVolume = $this->safe_float($ticker, 'units_traded_24H');
        $quoteVolume = $this->safe_float($ticker, 'acc_trade_value_24H');
        $vwap = $this->vwap($baseVolume, $quoteVolume);
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'max_price'),
            'low' => $this->safe_float($ticker, 'min_price'),
            'bid' => $this->safe_float($ticker, 'buy_price'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell_price'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => $open,
            'close' => $close,
            'last' => $close,
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
        $response = $this->publicGetTickerAll ($params);
        //
        //     {
        //         "status":"0000",
        //         "$data":{
        //             "BTC":array(
        //                 "opening_price":"9045000",
        //                 "closing_price":"9132000",
        //                 "min_price":"8938000",
        //                 "max_price":"9168000",
        //                 "units_traded":"4619.79967497",
        //                 "acc_trade_value":"42021363832.5187",
        //                 "prev_closing_price":"9041000",
        //                 "units_traded_24H":"8793.5045804",
        //                 "acc_trade_value_24H":"78933458515.4962",
        //                 "fluctate_24H":"530000",
        //                 "fluctate_rate_24H":"6.16"
        //             ),
        //             "date":"1587710878669"
        //         }
        //     }
        //
        $result = array();
        $data = $this->safe_value($response, 'data', array());
        $timestamp = $this->safe_integer($data, 'date');
        $tickers = $this->omit($data, 'date');
        $ids = is_array($tickers) ? array_keys($tickers) : array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $symbol = $id;
            $market = null;
            if (is_array($this->markets_by_id) && array_key_exists($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            }
            $ticker = $tickers[$id];
            $isArray = gettype($ticker) === 'array' && count(array_filter(array_keys($ticker), 'is_string')) == 0;
            if (!$isArray) {
                $ticker['date'] = $timestamp;
                $result[$symbol] = $this->parse_ticker($ticker, $market);
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['base'],
        );
        $response = $this->publicGetTickerCurrency (array_merge($request, $params));
        //
        //     {
        //         "status":"0000",
        //         "$data":{
        //             "opening_price":"227100",
        //             "closing_price":"228400",
        //             "min_price":"222300",
        //             "max_price":"230000",
        //             "units_traded":"82618.56075337",
        //             "acc_trade_value":"18767376138.6031",
        //             "prev_closing_price":"227100",
        //             "units_traded_24H":"151871.13484676",
        //             "acc_trade_value_24H":"34247610416.8974",
        //             "fluctate_24H":"8700",
        //             "fluctate_rate_24H":"3.96",
        //             "date":"1587710327264"
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ticker($data, $market);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1576823400000, // 기준 시간
        //         '8284000', // 시가
        //         '8286000', // 종가
        //         '8289000', // 고가
        //         '8276000', // 저가
        //         '15.41503692' // 거래량
        //     )
        //
        return array(
            $this->safe_integer($ohlcv, 0),
            $this->safe_float($ohlcv, 1),
            $this->safe_float($ohlcv, 3),
            $this->safe_float($ohlcv, 4),
            $this->safe_float($ohlcv, 2),
            $this->safe_float($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'currency' => $market['base'],
            'interval' => $this->timeframes[$timeframe],
        );
        $response = $this->publicGetCandlestickCurrencyInterval (array_merge($request, $params));
        //
        //     {
        //         'status' => '0000',
        //         'data' => {
        //             array(
        //                 1576823400000, // 기준 시간
        //                 '8284000', // 시가
        //                 '8286000', // 종가
        //                 '8289000', // 고가
        //                 '8276000', // 저가
        //                 '15.41503692' // 거래량
        //             ),
        //             array(
        //                 1576824000000, // 기준 시간
        //                 '8284000', // 시가
        //                 '8281000', // 종가
        //                 '8289000', // 고가
        //                 '8275000', // 저가
        //                 '6.19584467' // 거래량
        //             ),
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "transaction_date":"2020-04-23 22:21:46",
        //         "$type":"ask",
        //         "units_traded":"0.0125",
        //         "$price":"8667000",
        //         "total":"108337"
        //     }
        //
        // fetchOrder (private)
        //
        //     {
        //         "transaction_date" => "1572497603902030",
        //         "$price" => "8601000",
        //         "units" => "0.005",
        //         "fee_currency" => "KRW",
        //         "$fee" => "107.51",
        //         "total" => "43005"
        //     }
        //
        // a workaround for their bug in date format, hours are not 0-padded
        $timestamp = null;
        $transactionDatetime = $this->safe_string($trade, 'transaction_date');
        if ($transactionDatetime !== null) {
            $parts = explode(' ', $transactionDatetime);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts > 1) {
                $transactionDate = $parts[0];
                $transactionTime = $parts[1];
                if (strlen($transactionTime) < 8) {
                    $transactionTime = '0' . $transactionTime;
                }
                $timestamp = $this->parse8601($transactionDate . ' ' . $transactionTime);
            } else {
                $timestamp = $this->safe_integer_product($trade, 'transaction_date', 0.001);
            }
        }
        if ($timestamp !== null) {
            $timestamp -= 9 * 3600000; // they report UTC + 9 hours, server in Korean timezone
        }
        $type = null;
        $side = $this->safe_string($trade, 'type');
        $side = ($side === 'ask') ? 'sell' : 'buy';
        $id = $this->safe_string($trade, 'cont_no');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float_2($trade, 'units_traded', 'units');
        $cost = $this->safe_float($trade, 'total');
        if ($cost === null) {
            if ($amount !== null) {
                if ($price !== null) {
                    $cost = $price * $amount;
                }
            }
        }
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'fee_currency');
            $feeCurrencyCode = $this->common_currency_code($feeCurrencyId);
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        return array(
            'id' => $id,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'order' => null,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => null,
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
            'currency' => $market['base'],
        );
        if ($limit === null) {
            $request['count'] = $limit; // default 20, max 100
        }
        $response = $this->publicGetTransactionHistoryCurrency (array_merge($request, $params));
        //
        //     {
        //         "status":"0000",
        //         "$data":array(
        //             array(
        //                 "transaction_date":"2020-04-23 22:21:46",
        //                 "type":"ask",
        //                 "units_traded":"0.0125",
        //                 "price":"8667000",
        //                 "total":"108337"
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'order_currency' => $market['id'],
            'payment_currency' => $market['quote'],
            'units' => $amount,
        );
        $method = 'privatePostTradePlace';
        if ($type === 'limit') {
            $request['price'] = $price;
            $request['type'] = ($side === 'buy') ? 'bid' : 'ask';
        } else {
            $method = 'privatePostTradeMarket' . $this->capitalize($side);
        }
        $response = $this->$method (array_merge($request, $params));
        $id = $this->safe_string($response, 'order_id');
        if ($id === null) {
            throw new InvalidOrder($this->id . ' createOrder() did not return an order id');
        }
        return array(
            'info' => $response,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'id' => $id,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'order_id' => $id,
            'count' => 1,
            'order_currency' => $market['base'],
            'payment_currency' => $market['quote'],
        );
        $response = $this->privatePostInfoOrderDetail (array_merge($request, $params));
        //
        //     {
        //         "status" => "0000",
        //         "$data" => {
        //             order_date => '1603161798539254',
        //             type => 'ask',
        //             order_status => 'Cancel',
        //             order_currency => 'BTC',
        //             payment_currency => 'KRW',
        //             watch_price => '0',
        //             order_price => '13344000',
        //             order_qty => '0.0125',
        //             cancel_date => '1603161803809993',
        //             cancel_type => '사용자취소',
        //             contract => array(
        //                 {
        //                     transaction_date => '1603161799976383',
        //                     price => '13344000',
        //                     units => '0.0015',
        //                     fee_currency => 'KRW',
        //                     fee => '0',
        //                     total => '20016'
        //                 }
        //             ),
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data');
        return $this->parse_order(array_merge($data, array( 'order_id' => $id )), $market);
    }

    public function parse_order_status($status) {
        $statuses = array(
            'Pending' => 'open',
            'Completed' => 'closed',
            'Cancel' => 'canceled',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //
        // fetchOrder
        //
        //     {
        //         "transaction_date" => "1572497603668315",
        //         "$type" => "bid",
        //         "order_status" => "Completed",
        //         "order_currency" => "BTC",
        //         "payment_currency" => "KRW",
        //         "order_price" => "8601000",
        //         "order_qty" => "0.007",
        //         "cancel_date" => "",
        //         "cancel_type" => "",
        //         "contract" => array(
        //             array(
        //                 "transaction_date" => "1572497603902030",
        //                 "$price" => "8601000",
        //                 "units" => "0.005",
        //                 "fee_currency" => "KRW",
        //                 "$fee" => "107.51",
        //                 "total" => "43005"
        //             ),
        //         )
        //     }
        //
        //     {
        //         order_date => '1603161798539254',
        //         $type => 'ask',
        //         order_status => 'Cancel',
        //         order_currency => 'BTC',
        //         payment_currency => 'KRW',
        //         watch_price => '0',
        //         order_price => '13344000',
        //         order_qty => '0.0125',
        //         cancel_date => '1603161803809993',
        //         cancel_type => '사용자취소',
        //         contract => array(
        //             {
        //                 transaction_date => '1603161799976383',
        //                 $price => '13344000',
        //                 units => '0.0015',
        //                 fee_currency => 'KRW',
        //                 $fee => '0',
        //                 total => '20016'
        //             }
        //         ),
        //     }
        //
        // fetchOpenOrders
        //
        //     {
        //         "order_currency" => "BTC",
        //         "payment_currency" => "KRW",
        //         "order_id" => "C0101000007408440032",
        //         "order_date" => "1571728739360570",
        //         "$type" => "bid",
        //         "units" => "5.0",
        //         "units_remaining" => "5.0",
        //         "$price" => "501000",
        //     }
        //
        $timestamp = $this->safe_integer_product($order, 'order_date', 0.001);
        $sideProperty = $this->safe_value_2($order, 'type', 'side');
        $side = ($sideProperty === 'bid') ? 'buy' : 'sell';
        $status = $this->parse_order_status($this->safe_string($order, 'order_status'));
        $price = $this->safe_float_2($order, 'order_price', 'price');
        $type = 'limit';
        if ($price === 0) {
            $price = null;
            $type = 'market';
        }
        $amount = $this->safe_float_2($order, 'order_qty', 'units');
        $remaining = $this->safe_float($order, 'units_remaining');
        if ($remaining === null) {
            if ($status === 'closed') {
                $remaining = 0;
            } else if ($status !== 'canceled') {
                $remaining = $amount;
            }
        }
        $symbol = null;
        $baseId = $this->safe_string($order, 'order_currency');
        $quoteId = $this->safe_string($order, 'payment_currency');
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        if (($base !== null) && ($quote !== null)) {
            $symbol = $base . '/' . $quote;
        }
        if (($symbol === null) && ($market !== null)) {
            $symbol = $market['symbol'];
        }
        $filled = null;
        $cost = null;
        $average = null;
        $id = $this->safe_string($order, 'order_id');
        $rawTrades = $this->safe_value($order, 'contract');
        $trades = null;
        $fee = null;
        $fees = null;
        $feesByCurrency = null;
        if ($rawTrades !== null) {
            $trades = $this->parse_trades($rawTrades, $market, null, null, array(
                'side' => $side,
                'symbol' => $symbol,
                'order' => $id,
            ));
            $filled = 0;
            $feesByCurrency = array();
            for ($i = 0; $i < count($trades); $i++) {
                $trade = $trades[$i];
                $filled = $this->sum($filled, $trade['amount']);
                $cost = $this->sum($cost, $trade['cost']);
                $tradeFee = $trade['fee'];
                $feeCurrency = $tradeFee['currency'];
                if (is_array($feesByCurrency) && array_key_exists($feeCurrency, $feesByCurrency)) {
                    $feesByCurrency[$feeCurrency] = array(
                        'currency' => $feeCurrency,
                        'cost' => $this->sum($feesByCurrency[$feeCurrency]['cost'], $tradeFee['cost']),
                    );
                } else {
                    $feesByCurrency[$feeCurrency] = array(
                        'currency' => $feeCurrency,
                        'cost' => $tradeFee['cost'],
                    );
                }
            }
            $feeCurrencies = is_array($feesByCurrency) ? array_keys($feesByCurrency) : array();
            $feeCurrenciesLength = is_array($feeCurrencies) ? count($feeCurrencies) : 0;
            if ($feeCurrenciesLength > 1) {
                $fees = array();
                for ($i = 0; $i < count($feeCurrencies); $i++) {
                    $feeCurrency = $feeCurrencies[$i];
                    $fees[] = $feesByCurrency[$feeCurrency];
                }
            } else {
                $fee = $this->safe_value($feesByCurrency, $feeCurrencies[0]);
            }
            if ($filled !== 0) {
                $average = $cost / $filled;
            }
        }
        if ($amount !== null) {
            if (($filled === null) && ($remaining !== null)) {
                $filled = max (0, $amount - $remaining);
            }
            if (($remaining === null) && ($filled !== null)) {
                $remaining = max (0, $amount - $filled);
            }
        }
        $result = array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => $trades,
        );
        if ($fee !== null) {
            $result['fee'] = $fee;
        } else if ($fees !== null) {
            $result['fees'] = $fees;
        }
        return $result;
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        if ($limit === null) {
            $limit = 100;
        }
        $request = array(
            'count' => $limit,
            'order_currency' => $market['base'],
            'payment_currency' => $market['quote'],
        );
        if ($since !== null) {
            $request['after'] = $since;
        }
        $response = $this->privatePostInfoOrders (array_merge($request, $params));
        //
        //     {
        //         "status" => "0000",
        //         "$data" => array(
        //             {
        //                 "order_currency" => "BTC",
        //                 "payment_currency" => "KRW",
        //                 "order_id" => "C0101000007408440032",
        //                 "order_date" => "1571728739360570",
        //                 "type" => "bid",
        //                 "units" => "5.0",
        //                 "units_remaining" => "5.0",
        //                 "price" => "501000",
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $side_in_params = (is_array($params) && array_key_exists('side', $params));
        if (!$side_in_params) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a `$symbol` argument and a `$side` parameter (sell or buy)');
        }
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a `$symbol` argument and a `$side` parameter (sell or buy)');
        }
        $market = $this->market($symbol);
        $side = ($params['side'] === 'buy') ? 'bid' : 'ask';
        $params = $this->omit($params, array( 'side', 'currency' ));
        // https://github.com/ccxt/ccxt/issues/6771
        $request = array(
            'order_id' => $id,
            'type' => $side,
            'order_currency' => $market['base'],
            'payment_currency' => $market['quote'],
        );
        return $this->privatePostTradeCancel (array_merge($request, $params));
    }

    public function cancel_unified_order($order, $params = array ()) {
        $request = array(
            'side' => $order['side'],
        );
        return $this->cancel_order($order['id'], $order['symbol'], array_merge($request, $params));
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'units' => $amount,
            'address' => $address,
            'currency' => $currency['id'],
        );
        if ($currency === 'XRP' || $currency === 'XMR' || $currency === 'EOS' || $currency === 'STEEM') {
            $destination = $this->safe_string($params, 'destination');
            if (($tag === null) && ($destination === null)) {
                throw new ArgumentsRequired($this->id . ' ' . $code . ' withdraw() requires a $tag argument or an extra $destination param');
            } else if ($tag !== null) {
                $request['destination'] = $tag;
            }
        }
        $response = $this->privatePostTradeBtcWithdrawal (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => null,
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $endpoint = '/' . $this->implode_params($path, $params);
        $url = $this->urls['api'][$api] . $endpoint;
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $body = $this->urlencode(array_merge(array(
                'endpoint' => $endpoint,
            ), $query));
            $nonce = (string) $this->nonce();
            $auth = $endpoint . "\0" . $body . "\0" . $nonce; // eslint-disable-line quotes
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret), 'sha512');
            $signature64 = $this->decode(base64_encode($signature));
            $headers = array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Api-Key' => $this->apiKey,
                'Api-Sign' => $signature64,
                'Api-Nonce' => $nonce,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($httpCode, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return; // fallback to default error handler
        }
        if (is_array($response) && array_key_exists('status', $response)) {
            //
            //     array("$status":"5100","$message":"After May 23th, recent_transactions is no longer, hence users will not be able to connect to recent_transactions")
            //
            $status = $this->safe_string($response, 'status');
            $message = $this->safe_string($response, 'message');
            if ($status !== null) {
                if ($status === '0000') {
                    return; // no error
                }
                $feedback = $this->id . ' ' . $body;
                $this->throw_exactly_matched_exception($this->exceptions, $status, $feedback);
                $this->throw_exactly_matched_exception($this->exceptions, $message, $feedback);
                throw new ExchangeError($feedback);
            }
        }
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('status', $response)) {
            if ($response['status'] === '0000') {
                return $response;
            }
            throw new ExchangeError($this->id . ' ' . $this->json($response));
        }
        return $response;
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\NullResponse;
use \ccxt\InvalidOrder;
use \ccxt\NotSupported;

class cex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'cex',
            'name' => 'CEX.IO',
            'countries' => array( 'GB', 'EU', 'CY', 'RU' ),
            'rateLimit' => 1500,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766442-8ddc33b0-5ed8-11e7-8b98-f786aef0f3c9.jpg',
                'api' => 'https://cex.io/api',
                'www' => 'https://cex.io',
                'doc' => 'https://cex.io/cex-api',
                'fees' => array(
                    'https://cex.io/fee-schedule',
                    'https://cex.io/limits-commissions',
                ),
                'referral' => 'https://cex.io/r/0/up105393824/0/',
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'currency_profile',
                        'currency_limits/',
                        'last_price/{pair}/',
                        'last_prices/{currencies}/',
                        'ohlcv/hd/{yyyymmdd}/{pair}',
                        'order_book/{pair}/',
                        'ticker/{pair}/',
                        'tickers/{currencies}/',
                        'trade_history/{pair}/',
                    ),
                    'post' => array(
                        'convert/{pair}',
                        'price_stats/{pair}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'active_orders_status/',
                        'archived_orders/{pair}/',
                        'balance/',
                        'cancel_order/',
                        'cancel_orders/{pair}/',
                        'cancel_replace_order/{pair}/',
                        'close_position/{pair}/',
                        'get_address/',
                        'get_myfee/',
                        'get_order/',
                        'get_order_tx/',
                        'open_orders/{pair}/',
                        'open_orders/',
                        'open_position/{pair}/',
                        'open_positions/{pair}/',
                        'place_order/{pair}/',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'maker' => 0.16 / 100,
                    'taker' => 0.25 / 100,
                ),
                'funding' => array(
                    'withdraw' => array(
                        // 'USD' => null,
                        // 'EUR' => null,
                        // 'RUB' => null,
                        // 'GBP' => null,
                        'BTC' => 0.001,
                        'ETH' => 0.01,
                        'BCH' => 0.001,
                        'DASH' => 0.01,
                        'BTG' => 0.001,
                        'ZEC' => 0.001,
                        'XRP' => 0.02,
                    ),
                    'deposit' => array(
                        // 'USD' => amount => amount * 0.035 + 0.25,
                        // 'EUR' => amount => amount * 0.035 + 0.24,
                        // 'RUB' => amount => amount * 0.05 + 15.57,
                        // 'GBP' => amount => amount * 0.035 + 0.2,
                        'BTC' => 0.0,
                        'ETH' => 0.0,
                        'BCH' => 0.0,
                        'DASH' => 0.0,
                        'BTG' => 0.0,
                        'ZEC' => 0.0,
                        'XRP' => 0.0,
                        'XLM' => 0.0,
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(),
                'broad' => array(
                    'Insufficient funds' => '\\ccxt\\InsufficientFunds',
                    'Nonce must be incremented' => '\\ccxt\\InvalidNonce',
                    'Invalid Order' => '\\ccxt\\InvalidOrder',
                    'Order not found' => '\\ccxt\\OrderNotFound',
                    'limit exceeded' => '\\ccxt\\RateLimitExceeded', // array("error":"rate limit exceeded")
                    'Invalid API key' => '\\ccxt\\AuthenticationError',
                    'There was an error while placing your order' => '\\ccxt\\InvalidOrder',
                    'Sorry, too many clients already' => '\\ccxt\\DDoSProtection',
                ),
            ),
            'options' => array(
                'fetchOHLCVWarning' => true,
                'createMarketBuyOrderRequiresPrice' => true,
                'order' => array(
                    'status' => array(
                        'c' => 'canceled',
                        'd' => 'closed',
                        'cd' => 'canceled',
                        'a' => 'open',
                    ),
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
            $response = $this->publicGetCurrencyProfile ($params);
            $this->options['fetchCurrencies'] = array_merge($options, array(
                'response' => $response,
                'timestamp' => $now,
            ));
        }
        return $this->safe_value($this->options['fetchCurrencies'], 'response');
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->fetch_currencies_from_cache($params);
        $this->options['currencies'] = array(
            'timestamp' => $this->milliseconds(),
            'response' => $response,
        );
        //
        //     {
        //         "e":"currency_profile",
        //         "ok":"ok",
        //         "$data":{
        //             "symbols":array(
        //                 array(
        //                     "$code":"GHS",
        //                     "contract":true,
        //                     "commodity":true,
        //                     "fiat":false,
        //                     "description":"CEX.IO doesn't provide cloud mining services anymore.",
        //                     "$precision":8,
        //                     "scale":0,
        //                     "minimumCurrencyAmount":"0.00000001",
        //                     "minimalWithdrawalAmount":-1
        //                 ),
        //                 array(
        //                     "$code":"BTC",
        //                     "contract":false,
        //                     "commodity":false,
        //                     "fiat":false,
        //                     "description":"",
        //                     "$precision":8,
        //                     "scale":0,
        //                     "minimumCurrencyAmount":"0.00000001",
        //                     "minimalWithdrawalAmount":0.002
        //                 ),
        //                 {
        //                     "$code":"ETH",
        //                     "contract":false,
        //                     "commodity":false,
        //                     "fiat":false,
        //                     "description":"",
        //                     "$precision":8,
        //                     "scale":2,
        //                     "minimumCurrencyAmount":"0.00000100",
        //                     "minimalWithdrawalAmount":0.01
        //                 }
        //             ),
        //             "pairs":array(
        //                 array(
        //                     "symbol1":"BTC",
        //                     "symbol2":"USD",
        //                     "pricePrecision":1,
        //                     "priceScale":"/1000000",
        //                     "minLotSize":0.002,
        //                     "minLotSizeS2":20
        //                 ),
        //                 {
        //                     "symbol1":"ETH",
        //                     "symbol2":"USD",
        //                     "pricePrecision":2,
        //                     "priceScale":"/10000",
        //                     "minLotSize":0.1,
        //                     "minLotSizeS2":20
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $currencies = $this->safe_value($data, 'symbols', array());
        $result = array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'code');
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer($currency, 'precision');
            $active = true;
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'name' => $id,
                'active' => $active,
                'precision' => $precision,
                'fee' => null,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($currency, 'minimumCurrencyAmount'),
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
                        'min' => $this->safe_float($currency, 'minimalWithdrawalAmount'),
                        'max' => null,
                    ),
                ),
                'info' => $currency,
            );
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $currenciesResponse = $this->fetch_currencies_from_cache($params);
        $currenciesData = $this->safe_value($currenciesResponse, 'data', array());
        $currencies = $this->safe_value($currenciesData, 'symbols', array());
        $currenciesById = $this->index_by($currencies, 'code');
        $pairs = $this->safe_value($currenciesData, 'pairs', array());
        $response = $this->publicGetCurrencyLimits ($params);
        //
        //     {
        //         "e":"currency_limits",
        //         "ok":"ok",
        //         "data" => {
        //             "$pairs":array(
        //                 array(
        //                     "symbol1":"BTC",
        //                     "symbol2":"USD",
        //                     "minLotSize":0.002,
        //                     "minLotSizeS2":20,
        //                     "maxLotSize":30,
        //                     "minPrice":"1500",
        //                     "maxPrice":"35000"
        //                 ),
        //                 {
        //                     "symbol1":"BCH",
        //                     "symbol2":"EUR",
        //                     "minLotSize":0.1,
        //                     "minLotSizeS2":20,
        //                     "maxLotSize":null,
        //                     "minPrice":"25",
        //                     "maxPrice":"8192"
        //                 }
        //             )
        //         }
        //     }
        //
        $result = array();
        $markets = $this->safe_value($response['data'], 'pairs');
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $baseId = $this->safe_string($market, 'symbol1');
            $quoteId = $this->safe_string($market, 'symbol2');
            $id = $baseId . '/' . $quoteId;
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $baseCurrency = $this->safe_value($currenciesById, $baseId, array());
            $quoteCurrency = $this->safe_value($currenciesById, $quoteId, array());
            $pricePrecision = $this->safe_integer($quoteCurrency, 'precision', 8);
            for ($j = 0; $j < count($pairs); $j++) {
                $pair = $pairs[$j];
                if (($pair['symbol1'] === $baseId) && ($pair['symbol2'] === $quoteId)) {
                    // we might need to account for `priceScale` here
                    $pricePrecision = $this->safe_integer($pair, 'pricePrecision', $pricePrecision);
                }
            }
            $baseCcyPrecision = $this->safe_integer($baseCurrency, 'precision', 8);
            $baseCcyScale = $this->safe_integer($baseCurrency, 'scale', 0);
            $amountPrecision = $baseCcyPrecision - $baseCcyScale;
            $precision = array(
                'amount' => $amountPrecision,
                'price' => $pricePrecision,
            );
            $result[] = array(
                'id' => $id,
                'info' => $market,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_float($market, 'minLotSize'),
                        'max' => $this->safe_float($market, 'maxLotSize'),
                    ),
                    'price' => array(
                        'min' => $this->safe_float($market, 'minPrice'),
                        'max' => $this->safe_float($market, 'maxPrice'),
                    ),
                    'cost' => array(
                        'min' => $this->safe_float($market, 'minLotSizeS2'),
                        'max' => null,
                    ),
                ),
                'active' => null,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalance ($params);
        $result = array( 'info' => $response );
        $ommited = array( 'username', 'timestamp' );
        $balances = $this->omit($response, $ommited);
        $currencyIds = is_array($balances) ? array_keys($balances) : array();
        for ($i = 0; $i < count($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $balance = $this->safe_value($balances, $currencyId, array());
            $account = $this->account();
            $account['free'] = $this->safe_float($balance, 'available');
            // https://github.com/ccxt/ccxt/issues/5484
            $account['used'] = $this->safe_float($balance, 'orders', 0.0);
            $code = $this->safe_currency_code($currencyId);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'pair' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['depth'] = $limit;
        }
        $response = $this->publicGetOrderBookPair (array_merge($request, $params));
        $timestamp = $this->safe_timestamp($response, 'timestamp');
        return $this->parse_order_book($response, $timestamp);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1591403940,
        //         0.024972,
        //         0.024972,
        //         0.024969,
        //         0.024969,
        //         0.49999900
        //     )
        //
        return array(
            $this->safe_timestamp($ohlcv, 0),
            $this->safe_float($ohlcv, 1),
            $this->safe_float($ohlcv, 2),
            $this->safe_float($ohlcv, 3),
            $this->safe_float($ohlcv, 4),
            $this->safe_float($ohlcv, 5),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if ($since === null) {
            $since = $this->milliseconds() - 86400000; // yesterday
        } else {
            if ($this->options['fetchOHLCVWarning']) {
                throw new ExchangeError($this->id . " fetchOHLCV warning => CEX can return historical candles for a certain date only, this might produce an empty or null reply. Set exchange.options['fetchOHLCVWarning'] = false or add (array( 'options' => array( 'fetchOHLCVWarning' => false ))) to constructor $params to suppress this warning message.");
            }
        }
        $ymd = $this->ymd($since);
        $ymd = explode('-', $ymd);
        $ymd = implode('', $ymd);
        $request = array(
            'pair' => $market['id'],
            'yyyymmdd' => $ymd,
        );
        try {
            $response = $this->publicGetOhlcvHdYyyymmddPair (array_merge($request, $params));
            //
            //     {
            //         "time":20200606,
            //         "data1m":"[[1591403940,0.024972,0.024972,0.024969,0.024969,0.49999900]]",
            //     }
            //
            $key = 'data' . $this->timeframes[$timeframe];
            $data = $this->safe_string($response, $key);
            $ohlcvs = json_decode($data, $as_associative_array = true);
            return $this->parse_ohlcvs($ohlcvs, $market, $timeframe, $since, $limit);
        } catch (Exception $e) {
            if ($e instanceof NullResponse) {
                return array();
            }
        }
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_timestamp($ticker, 'timestamp');
        $volume = $this->safe_float($ticker, 'volume');
        $high = $this->safe_float($ticker, 'high');
        $low = $this->safe_float($ticker, 'low');
        $bid = $this->safe_float($ticker, 'bid');
        $ask = $this->safe_float($ticker, 'ask');
        $last = $this->safe_float($ticker, 'last');
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $high,
            'low' => $low,
            'bid' => $bid,
            'bidVolume' => null,
            'ask' => $ask,
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $volume,
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $currencies = is_array($this->currencies) ? array_keys($this->currencies) : array();
        $request = array(
            'currencies' => implode('/', $currencies),
        );
        $response = $this->publicGetTickersCurrencies (array_merge($request, $params));
        $tickers = $response['data'];
        $result = array();
        for ($t = 0; $t < count($tickers); $t++) {
            $ticker = $tickers[$t];
            $symbol = str_replace(':', '/', $ticker['pair']);
            $market = $this->markets[$symbol];
            $result[$symbol] = $this->parse_ticker($ticker, $market);
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        $ticker = $this->publicGetTickerPair (array_merge($request, $params));
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $id = $this->safe_string($trade, 'tid');
        $type = null;
        $side = $this->safe_string($trade, 'type');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($amount !== null) {
            if ($price !== null) {
                $cost = $amount * $price;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'order' => null,
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
            'pair' => $market['id'],
        );
        $response = $this->publicGetTradeHistoryPair (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        // for market buy it requires the $amount of quote currency to spend
        if (($type === 'market') && ($side === 'buy')) {
            if ($this->options['createMarketBuyOrderRequiresPrice']) {
                if ($price === null) {
                    throw new InvalidOrder($this->id . " createOrder() requires the $price argument with market buy orders to calculate total order cost ($amount to spend), where cost = $amount * $price-> Supply a $price argument to createOrder() call if you want the cost to be calculated for you from $price and $amount, or, alternatively, add .options['createMarketBuyOrderRequiresPrice'] = false to supply the cost in the $amount argument (the exchange-specific behaviour)");
                } else {
                    $amount = $amount * $price;
                }
            }
        }
        $this->load_markets();
        $request = array(
            'pair' => $this->market_id($symbol),
            'type' => $side,
            'amount' => $amount,
        );
        if ($type === 'limit') {
            $request['price'] = $price;
        } else {
            $request['order_type'] = $type;
        }
        $response = $this->privatePostPlaceOrderPair (array_merge($request, $params));
        //
        //     {
        //         "id" => "12978363524",
        //         "time" => 1586610022259,
        //         "$type" => "buy",
        //         "$price" => "0.033934",
        //         "$amount" => "0.10722802",
        //         "pending" => "0.10722802",
        //         "$complete" => false
        //     }
        //
        $placedAmount = $this->safe_float($response, 'amount');
        $remaining = $this->safe_float($response, 'pending');
        $timestamp = $this->safe_value($response, 'time');
        $complete = $this->safe_value($response, 'complete');
        $status = $complete ? 'closed' : 'open';
        $filled = null;
        if (($placedAmount !== null) && ($remaining !== null)) {
            $filled = max ($placedAmount - $remaining, 0);
        }
        return array(
            'id' => $this->safe_string($response, 'id'),
            'info' => $response,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => null,
            'type' => $type,
            'side' => $this->safe_string($response, 'type'),
            'symbol' => $symbol,
            'status' => $status,
            'price' => $this->safe_float($response, 'price'),
            'amount' => $placedAmount,
            'cost' => null,
            'average' => null,
            'remaining' => $remaining,
            'filled' => $filled,
            'fee' => null,
            'trades' => null,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function parse_order($order, $market = null) {
        // Depending on the call, 'time' can be a unix int, unix string or ISO string
        // Yes, really
        $timestamp = $this->safe_value($order, 'time');
        if (gettype($timestamp) === 'string' && mb_strpos($timestamp, 'T') !== false) {
            // ISO8601 string
            $timestamp = $this->parse8601($timestamp);
        } else {
            // either integer or string integer
            $timestamp = intval($timestamp);
        }
        $symbol = null;
        if ($market === null) {
            $baseId = $this->safe_string($order, 'symbol1');
            $quoteId = $this->safe_string($order, 'symbol2');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            if (is_array($this->markets) && array_key_exists($symbol, $this->markets)) {
                $market = $this->market($symbol);
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount');
        // sell orders can have a negative $amount
        // https://github.com/ccxt/ccxt/issues/5338
        if ($amount !== null) {
            $amount = abs($amount);
        }
        $remaining = $this->safe_float_2($order, 'pending', 'remains');
        $filled = $amount - $remaining;
        $fee = null;
        $cost = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            $taCost = $this->safe_float($order, 'ta:' . $market['quote']);
            $ttaCost = $this->safe_float($order, 'tta:' . $market['quote']);
            $cost = $this->sum($taCost, $ttaCost);
            $baseFee = 'fa:' . $market['base'];
            $baseTakerFee = 'tfa:' . $market['base'];
            $quoteFee = 'fa:' . $market['quote'];
            $quoteTakerFee = 'tfa:' . $market['quote'];
            $feeRate = $this->safe_float($order, 'tradingFeeMaker');
            if (!$feeRate) {
                $feeRate = $this->safe_float($order, 'tradingFeeTaker', $feeRate);
            }
            if ($feeRate) {
                $feeRate /= 100.0; // convert to mathematically-correct percentage coefficients => 1.0 = 100%
            }
            if ((is_array($order) && array_key_exists($baseFee, $order)) || (is_array($order) && array_key_exists($baseTakerFee, $order))) {
                $baseFeeCost = $this->safe_float_2($order, $baseFee, $baseTakerFee);
                $fee = array(
                    'currency' => $market['base'],
                    'rate' => $feeRate,
                    'cost' => $baseFeeCost,
                );
            } else if ((is_array($order) && array_key_exists($quoteFee, $order)) || (is_array($order) && array_key_exists($quoteTakerFee, $order))) {
                $quoteFeeCost = $this->safe_float_2($order, $quoteFee, $quoteTakerFee);
                $fee = array(
                    'currency' => $market['quote'],
                    'rate' => $feeRate,
                    'cost' => $quoteFeeCost,
                );
            }
        }
        if (!$cost) {
            $cost = $price * $filled;
        }
        $side = $order['type'];
        $trades = null;
        $orderId = $order['id'];
        if (is_array($order) && array_key_exists('vtx', $order)) {
            $trades = array();
            for ($i = 0; $i < count($order['vtx']); $i++) {
                $item = $order['vtx'][$i];
                $tradeSide = $this->safe_string($item, 'type');
                if ($tradeSide === 'cancel') {
                    // looks like this might represent the cancelled part of an $order
                    //   { id => '4426729543',
                    //     type => 'cancel',
                    //     time => '2017-09-22T00:24:30.476Z',
                    //     user => 'up106404164',
                    //     c => 'user:up106404164:a:BCH',
                    //     d => 'order:4426728375:a:BCH',
                    //     a => '0.09935956',
                    //     $amount => '0.09935956',
                    //     balance => '0.42580261',
                    //     $symbol => 'BCH',
                    //     $order => '4426728375',
                    //     buy => null,
                    //     sell => null,
                    //     pair => null,
                    //     pos => null,
                    //     cs => '0.42580261',
                    //     ds => 0 }
                    continue;
                }
                $tradePrice = $this->safe_float($item, 'price');
                if ($tradePrice === null) {
                    // this represents the $order
                    //   {
                    //     "a" => "0.47000000",
                    //     "c" => "user:up106404164:a:EUR",
                    //     "d" => "$order:6065499239:a:EUR",
                    //     "cs" => "1432.93",
                    //     "ds" => "476.72",
                    //     "id" => "6065499249",
                    //     "buy" => null,
                    //     "pos" => null,
                    //     "pair" => null,
                    //     "sell" => null,
                    //     "time" => "2018-04-22T13:07:22.152Z",
                    //     "type" => "buy",
                    //     "user" => "up106404164",
                    //     "$order" => "6065499239",
                    //     "$amount" => "-715.97000000",
                    //     "$symbol" => "EUR",
                    //     "balance" => "1432.93000000" }
                    continue;
                }
                // todo => deal with these
                if ($tradeSide === 'costsNothing') {
                    continue;
                }
                // --
                // if ($side !== $tradeSide)
                //     throw new \Exception(json_encode($order, null, 2));
                // if ($orderId !== $item['order'])
                //     throw new \Exception(json_encode($order, null, 2));
                // --
                // partial buy trade
                //   {
                //     "a" => "0.01589885",
                //     "c" => "user:up106404164:a:BTC",
                //     "d" => "$order:6065499239:a:BTC",
                //     "cs" => "0.36300000",
                //     "ds" => 0,
                //     "id" => "6067991213",
                //     "buy" => "6065499239",
                //     "pos" => null,
                //     "pair" => null,
                //     "sell" => "6067991206",
                //     "time" => "2018-04-22T23:09:11.773Z",
                //     "type" => "buy",
                //     "user" => "up106404164",
                //     "$order" => "6065499239",
                //     "$price" => 7146.5,
                //     "$amount" => "0.01589885",
                //     "$symbol" => "BTC",
                //     "balance" => "0.36300000",
                //     "symbol2" => "EUR",
                //     "fee_amount" => "0.19" }
                // --
                // trade with zero $amount, but non-zero $fee
                //   {
                //     "a" => "0.00000000",
                //     "c" => "user:up106404164:a:EUR",
                //     "d" => "$order:5840654423:a:EUR",
                //     "cs" => 559744,
                //     "ds" => 0,
                //     "id" => "5840654429",
                //     "buy" => "5807238573",
                //     "pos" => null,
                //     "pair" => null,
                //     "sell" => "5840654423",
                //     "time" => "2018-03-15T03:20:14.010Z",
                //     "type" => "sell",
                //     "user" => "up106404164",
                //     "$order" => "5840654423",
                //     "$price" => 730,
                //     "$amount" => "0.00000000",
                //     "$symbol" => "EUR",
                //     "balance" => "5597.44000000",
                //     "symbol2" => "BCH",
                //     "fee_amount" => "0.01" }
                // --
                // trade which should have an $amount of exactly 0.002BTC
                //   {
                //     "a" => "16.70000000",
                //     "c" => "user:up106404164:a:GBP",
                //     "d" => "$order:9927386681:a:GBP",
                //     "cs" => "86.90",
                //     "ds" => 0,
                //     "id" => "9927401610",
                //     "buy" => "9927401601",
                //     "pos" => null,
                //     "pair" => null,
                //     "sell" => "9927386681",
                //     "time" => "2019-08-21T15:25:37.777Z",
                //     "type" => "sell",
                //     "user" => "up106404164",
                //     "$order" => "9927386681",
                //     "$price" => 8365,
                //     "$amount" => "16.70000000",
                //     "office" => "UK",
                //     "$symbol" => "GBP",
                //     "balance" => "86.90000000",
                //     "symbol2" => "BTC",
                //     "fee_amount" => "0.03"
                //   }
                $tradeTimestamp = $this->parse8601($this->safe_string($item, 'time'));
                $tradeAmount = $this->safe_float($item, 'amount');
                $feeCost = $this->safe_float($item, 'fee_amount');
                $absTradeAmount = ($tradeAmount < 0) ? -$tradeAmount : $tradeAmount;
                $tradeCost = null;
                if ($tradeSide === 'sell') {
                    $tradeCost = $absTradeAmount;
                    $absTradeAmount = $this->sum($feeCost, $tradeCost) / $tradePrice;
                } else {
                    $tradeCost = $absTradeAmount * $tradePrice;
                }
                $trades[] = array(
                    'id' => $this->safe_string($item, 'id'),
                    'timestamp' => $tradeTimestamp,
                    'datetime' => $this->iso8601($tradeTimestamp),
                    'order' => $orderId,
                    'symbol' => $symbol,
                    'price' => $tradePrice,
                    'amount' => $absTradeAmount,
                    'cost' => $tradeCost,
                    'side' => $tradeSide,
                    'fee' => array(
                        'cost' => $feeCost,
                        'currency' => $market['quote'],
                    ),
                    'info' => $item,
                    'type' => null,
                    'takerOrMaker' => null,
                );
            }
        }
        return array(
            'id' => $orderId,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => ($price === null) ? 'market' : 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'trades' => $trades,
            'fee' => $fee,
            'info' => $order,
            'average' => null,
        );
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $method = 'privatePostOpenOrders';
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
            $method .= 'Pair';
        }
        $orders = $this->$method (array_merge($request, $params));
        for ($i = 0; $i < count($orders); $i++) {
            $orders[$i] = array_merge($orders[$i], array( 'status' => 'open' ));
        }
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $method = 'privatePostArchivedOrdersPair';
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchClosedOrders() requires a $symbol argument');
        }
        $market = $this->market($symbol);
        $request = array( 'pair' => $market['id'] );
        $response = $this->$method (array_merge($request, $params));
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => (string) $id,
        );
        $response = $this->privatePostGetOrderTx (array_merge($request, $params));
        $data = $this->safe_value($response, 'data', array());
        //
        //     {
        //         "$id" => "5442731603",
        //         "type" => "sell",
        //         "time" => 1516132358071,
        //         "lastTxTime" => 1516132378452,
        //         "lastTx" => "5442734452",
        //         "pos" => null,
        //         "user" => "up106404164",
        //         "status" => "d",
        //         "symbol1" => "ETH",
        //         "symbol2" => "EUR",
        //         "amount" => "0.50000000",
        //         "kind" => "api",
        //         "price" => "923.3386",
        //         "tfacf" => "1",
        //         "fa:EUR" => "0.55",
        //         "ta:EUR" => "369.77",
        //         "remains" => "0.00000000",
        //         "tfa:EUR" => "0.22",
        //         "tta:EUR" => "91.95",
        //         "a:ETH:cds" => "0.50000000",
        //         "a:EUR:cds" => "461.72",
        //         "f:EUR:cds" => "0.77",
        //         "tradingFeeMaker" => "0.15",
        //         "tradingFeeTaker" => "0.23",
        //         "tradingFeeStrategy" => "userVolumeAmount",
        //         "tradingFeeUserVolumeAmount" => "2896912572",
        //         "orderId" => "5442731603",
        //         "next" => false,
        //         "vtx" => array(
        //             array(
        //                 "$id" => "5442734452",
        //                 "type" => "sell",
        //                 "time" => "2018-01-16T19:52:58.452Z",
        //                 "user" => "up106404164",
        //                 "c" => "user:up106404164:a:EUR",
        //                 "d" => "order:5442731603:a:EUR",
        //                 "a" => "104.53000000",
        //                 "amount" => "104.53000000",
        //                 "balance" => "932.71000000",
        //                 "$symbol" => "EUR",
        //                 "order" => "5442731603",
        //                 "buy" => "5442734443",
        //                 "sell" => "5442731603",
        //                 "pair" => null,
        //                 "pos" => null,
        //                 "office" => null,
        //                 "cs" => "932.71",
        //                 "ds" => 0,
        //                 "price" => 923.3386,
        //                 "symbol2" => "ETH",
        //                 "fee_amount" => "0.16"
        //             ),
        //             array(
        //                 "$id" => "5442731609",
        //                 "type" => "sell",
        //                 "time" => "2018-01-16T19:52:38.071Z",
        //                 "user" => "up106404164",
        //                 "c" => "user:up106404164:a:EUR",
        //                 "d" => "order:5442731603:a:EUR",
        //                 "a" => "91.73000000",
        //                 "amount" => "91.73000000",
        //                 "balance" => "563.49000000",
        //                 "$symbol" => "EUR",
        //                 "order" => "5442731603",
        //                 "buy" => "5442618127",
        //                 "sell" => "5442731603",
        //                 "pair" => null,
        //                 "pos" => null,
        //                 "office" => null,
        //                 "cs" => "563.49",
        //                 "ds" => 0,
        //                 "price" => 924.0092,
        //                 "symbol2" => "ETH",
        //                 "fee_amount" => "0.22"
        //             ),
        //             {
        //                 "$id" => "5442731604",
        //                 "type" => "sell",
        //                 "time" => "2018-01-16T19:52:38.071Z",
        //                 "user" => "up106404164",
        //                 "c" => "order:5442731603:a:ETH",
        //                 "d" => "user:up106404164:a:ETH",
        //                 "a" => "0.50000000",
        //                 "amount" => "-0.50000000",
        //                 "balance" => "15.80995000",
        //                 "$symbol" => "ETH",
        //                 "order" => "5442731603",
        //                 "buy" => null,
        //                 "sell" => null,
        //                 "pair" => null,
        //                 "pos" => null,
        //                 "office" => null,
        //                 "cs" => "0.50000000",
        //                 "ds" => "15.80995000"
        //             }
        //         )
        //     }
        //
        return $this->parse_order($data);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'limit' => $limit,
            'pair' => $market['id'],
            'dateFrom' => $since,
        );
        $response = $this->privatePostArchivedOrdersPair (array_merge($request, $params));
        $results = array();
        for ($i = 0; $i < count($response); $i++) {
            // cancelled (unfilled):
            //    { id => '4005785516',
            //     $type => 'sell',
            //     $time => '2017-07-18T19:08:34.223Z',
            //     $lastTxTime => '2017-07-18T19:08:34.396Z',
            //     lastTx => '4005785522',
            //     pos => null,
            //     $status => 'c',
            //     symbol1 => 'ETH',
            //     symbol2 => 'GBP',
            //     $amount => '0.20000000',
            //     $price => '200.5625',
            //     remains => '0.20000000',
            //     'a:ETH:cds' => '0.20000000',
            //     tradingFeeMaker => '0',
            //     tradingFeeTaker => '0.16',
            //     tradingFeeUserVolumeAmount => '10155061217',
            //     orderId => '4005785516' }
            // --
            // cancelled (partially $filled buy):
            //    { id => '4084911657',
            //     $type => 'buy',
            //     $time => '2017-08-05T03:18:39.596Z',
            //     $lastTxTime => '2019-03-19T17:37:46.404Z',
            //     lastTx => '8459265833',
            //     pos => null,
            //     $status => 'cd',
            //     symbol1 => 'BTC',
            //     symbol2 => 'GBP',
            //     $amount => '0.05000000',
            //     $price => '2241.4692',
            //     tfacf => '1',
            //     remains => '0.03910535',
            //     'tfa:GBP' => '0.04',
            //     'tta:GBP' => '24.39',
            //     'a:BTC:cds' => '0.01089465',
            //     'a:GBP:cds' => '112.26',
            //     'f:GBP:cds' => '0.04',
            //     tradingFeeMaker => '0',
            //     tradingFeeTaker => '0.16',
            //     tradingFeeUserVolumeAmount => '13336396963',
            //     orderId => '4084911657' }
            // --
            // cancelled (partially $filled sell):
            //    { id => '4426728375',
            //     $type => 'sell',
            //     $time => '2017-09-22T00:24:20.126Z',
            //     $lastTxTime => '2017-09-22T00:24:30.476Z',
            //     lastTx => '4426729543',
            //     pos => null,
            //     $status => 'cd',
            //     symbol1 => 'BCH',
            //     symbol2 => 'BTC',
            //     $amount => '0.10000000',
            //     $price => '0.11757182',
            //     tfacf => '1',
            //     remains => '0.09935956',
            //     'tfa:BTC' => '0.00000014',
            //     'tta:BTC' => '0.00007537',
            //     'a:BCH:cds' => '0.10000000',
            //     'a:BTC:cds' => '0.00007537',
            //     'f:BTC:cds' => '0.00000014',
            //     tradingFeeMaker => '0',
            //     tradingFeeTaker => '0.18',
            //     tradingFeeUserVolumeAmount => '3466715450',
            //     orderId => '4426728375' }
            // --
            // $filled:
            //    { id => '5342275378',
            //     $type => 'sell',
            //     $time => '2018-01-04T00:28:12.992Z',
            //     $lastTxTime => '2018-01-04T00:28:12.992Z',
            //     lastTx => '5342275393',
            //     pos => null,
            //     $status => 'd',
            //     symbol1 => 'BCH',
            //     symbol2 => 'BTC',
            //     $amount => '0.10000000',
            //     kind => 'api',
            //     $price => '0.17',
            //     remains => '0.00000000',
            //     'tfa:BTC' => '0.00003902',
            //     'tta:BTC' => '0.01699999',
            //     'a:BCH:cds' => '0.10000000',
            //     'a:BTC:cds' => '0.01699999',
            //     'f:BTC:cds' => '0.00003902',
            //     tradingFeeMaker => '0.15',
            //     tradingFeeTaker => '0.23',
            //     tradingFeeUserVolumeAmount => '1525951128',
            //     orderId => '5342275378' }
            // --
            // $market $order (buy):
            //    { "id" => "6281946200",
            //     "pos" => null,
            //     "$time" => "2018-05-23T11:55:43.467Z",
            //     "$type" => "buy",
            //     "$amount" => "0.00000000",
            //     "lastTx" => "6281946210",
            //     "$status" => "d",
            //     "amount2" => "20.00",
            //     "orderId" => "6281946200",
            //     "remains" => "0.00000000",
            //     "symbol1" => "ETH",
            //     "symbol2" => "EUR",
            //     "$tfa:EUR" => "0.05",
            //     "$tta:EUR" => "19.94",
            //     "a:ETH:cds" => "0.03764100",
            //     "a:EUR:cds" => "20.00",
            //     "f:EUR:cds" => "0.05",
            //     "$lastTxTime" => "2018-05-23T11:55:43.467Z",
            //     "tradingFeeTaker" => "0.25",
            //     "tradingFeeUserVolumeAmount" => "55998097" }
            // --
            // $market $order (sell):
            //   { "id" => "6282200948",
            //     "pos" => null,
            //     "$time" => "2018-05-23T12:42:58.315Z",
            //     "$type" => "sell",
            //     "$amount" => "-0.05000000",
            //     "lastTx" => "6282200958",
            //     "$status" => "d",
            //     "orderId" => "6282200948",
            //     "remains" => "0.00000000",
            //     "symbol1" => "ETH",
            //     "symbol2" => "EUR",
            //     "$tfa:EUR" => "0.07",
            //     "$tta:EUR" => "26.49",
            //     "a:ETH:cds" => "0.05000000",
            //     "a:EUR:cds" => "26.49",
            //     "f:EUR:cds" => "0.07",
            //     "$lastTxTime" => "2018-05-23T12:42:58.315Z",
            //     "tradingFeeTaker" => "0.25",
            //     "tradingFeeUserVolumeAmount" => "56294576" }
            $order = $response[$i];
            $status = $this->parse_order_status($this->safe_string($order, 'status'));
            $baseId = $this->safe_string($order, 'symbol1');
            $quoteId = $this->safe_string($order, 'symbol2');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $side = $this->safe_string($order, 'type');
            $baseAmount = $this->safe_float($order, 'a:' . $baseId . ':cds');
            $quoteAmount = $this->safe_float($order, 'a:' . $quoteId . ':cds');
            $fee = $this->safe_float($order, 'f:' . $quoteId . ':cds');
            $amount = $this->safe_float($order, 'amount');
            $price = $this->safe_float($order, 'price');
            $remaining = $this->safe_float($order, 'remains');
            $filled = $amount - $remaining;
            $orderAmount = null;
            $cost = null;
            $average = null;
            $type = null;
            if (!$price) {
                $type = 'market';
                $orderAmount = $baseAmount;
                $cost = $quoteAmount;
                $average = $orderAmount / $cost;
            } else {
                $ta = $this->safe_float($order, 'ta:' . $quoteId, 0);
                $tta = $this->safe_float($order, 'tta:' . $quoteId, 0);
                $fa = $this->safe_float($order, 'fa:' . $quoteId, 0);
                $tfa = $this->safe_float($order, 'tfa:' . $quoteId, 0);
                if ($side === 'sell') {
                    $cost = $this->sum($this->sum($ta, $tta), $this->sum($fa, $tfa));
                } else {
                    $cost = $this->sum($ta, $tta) - $this->sum($fa, $tfa);
                }
                $type = 'limit';
                $orderAmount = $amount;
                $average = $cost / $filled;
            }
            $time = $this->safe_string($order, 'time');
            $lastTxTime = $this->safe_string($order, 'lastTxTime');
            $timestamp = $this->parse8601($time);
            $results[] = array(
                'id' => $this->safe_string($order, 'id'),
                'timestamp' => $timestamp,
                'datetime' => $this->iso8601($timestamp),
                'lastUpdated' => $this->parse8601($lastTxTime),
                'status' => $status,
                'symbol' => $symbol,
                'side' => $side,
                'price' => $price,
                'amount' => $orderAmount,
                'average' => $average,
                'type' => $type,
                'filled' => $filled,
                'cost' => $cost,
                'remaining' => $remaining,
                'fee' => array(
                    'cost' => $fee,
                    'currency' => $quote,
                ),
                'info' => $order,
            );
        }
        return $results;
    }

    public function parse_order_status($status) {
        return $this->safe_string($this->options['order']['status'], $status, $status);
    }

    public function edit_order($id, $symbol, $type, $side, $amount = null, $price = null, $params = array ()) {
        if ($amount === null) {
            throw new ArgumentsRequired($this->id . ' editOrder() requires a $amount argument');
        }
        if ($price === null) {
            throw new ArgumentsRequired($this->id . ' editOrder() requires a $price argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        // see => https://cex.io/rest-api#/definitions/CancelReplaceOrderRequest
        $request = array(
            'pair' => $market['id'],
            'type' => $side,
            'amount' => $amount,
            'price' => $price,
            'order_id' => $id,
        );
        $response = $this->privatePostCancelReplaceOrderPair (array_merge($request, $params));
        return $this->parse_order($response, $market);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        if ($code === 'XRP' || $code === 'XLM') {
            // https://github.com/ccxt/ccxt/pull/2327#issuecomment-375204856
            throw new NotSupported($this->id . ' fetchDepositAddress does not support XRP and XLM addresses yet (awaiting docs from CEX.io)');
        }
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privatePostGetAddress (array_merge($request, $params));
        $address = $this->safe_string($response, 'data');
        $this->check_address($address);
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => null,
            'info' => $response,
        );
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce();
            $auth = $nonce . $this->uid . $this->apiKey;
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret));
            $body = $this->json(array_merge(array(
                'key' => $this->apiKey,
                'signature' => strtoupper($signature),
                'nonce' => $nonce,
            ), $query));
            $headers = array(
                'Content-Type' => 'application/json',
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (gettype($response) === 'array' && count(array_filter(array_keys($response), 'is_string')) == 0) {
            return $response; // public endpoints may return array()-arrays
        }
        if ($body === 'true') {
            return;
        }
        if ($response === null) {
            throw new NullResponse($this->id . ' returned ' . $this->json($response));
        }
        if (is_array($response) && array_key_exists('e', $response)) {
            if (is_array($response) && array_key_exists('ok', $response)) {
                if ($response['ok'] === 'ok') {
                    return;
                }
            }
        }
        if (is_array($response) && array_key_exists('error', $response)) {
            $message = $this->safe_string($response, 'error');
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            throw new ExchangeError($feedback);
        }
    }
}

<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\DDoSProtection;

class whitebit extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'whitebit',
            'name' => 'WhiteBit',
            'version' => 'v2',
            'countries' => array( 'EE' ),
            'rateLimit' => 500,
            'has' => array(
                'cancelOrder' => false,
                'CORS' => false,
                'createDepositAddress' => false,
                'createLimitOrder' => false,
                'createMarketOrder' => false,
                'createOrder' => false,
                'deposit' => false,
                'editOrder' => false,
                'fetchBalance' => false,
                'fetchBidsAsks' => false,
                'fetchCurrencies' => true,
                'fetchMarkets' => true,
                'fetchOHLCV' => true,
                'fetchOrderBook' => true,
                'fetchStatus' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTradingFees' => true,
                'privateAPI' => false,
                'publicAPI' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '3m' => '3m',
                '5m' => '5m',
                '15m' => '15m',
                '30m' => '30m',
                '1h' => '1h',
                '2h' => '2h',
                '4h' => '4h',
                '6h' => '6h',
                '8h' => '8h',
                '12h' => '12h',
                '1d' => '1d',
                '3d' => '3d',
                '1w' => '1w',
                '1M' => '1M',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/66732963-8eb7dd00-ee66-11e9-849b-10d9282bb9e0.jpg',
                'api' => array(
                    'web' => 'https://whitebit.com/',
                    'publicV2' => 'https://whitebit.com/api/v2/public',
                    'publicV1' => 'https://whitebit.com/api/v1/public',
                ),
                'www' => 'https://www.whitebit.com',
                'doc' => 'https://documenter.getpostman.com/view/7473075/Szzj8dgv?version=latest',
                'fees' => 'https://whitebit.com/fee-schedule',
                'referral' => 'https://whitebit.com/referral/d9bdf40e-28f2-4b52-b2f9-cd1415d82963',
            ),
            'api' => array(
                'web' => array(
                    'get' => array(
                        'v1/healthcheck',
                    ),
                ),
                'publicV1' => array(
                    'get' => array(
                        'markets',
                        'tickers',
                        'ticker',
                        'symbols',
                        'depth/result',
                        'history',
                        'kline',
                    ),
                ),
                'publicV2' => array(
                    'get' => array(
                        'markets',
                        'ticker',
                        'assets',
                        'fee',
                        'depth/{market}',
                        'trades/{market}',
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
                'fetchTradesMethod' => 'fetchTradesV1',
            ),
            'exceptions' => array(
                'exact' => array(
                    '503' => '\\ccxt\\ExchangeNotAvailable', // array("response":null,"status":503,"errors":array("message":[""]),"notification":null,"warning":null,"_token":null)
                ),
                'broad' => array(
                    'Market is not available' => '\\ccxt\\BadSymbol', // array("success":false,"message":array("market":["Market is not available"]),"result":array())
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicV2GetMarkets ($params);
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result":array(
        //             {
        //                 "name":"BTC_USD",
        //                 "moneyPrec":"2",
        //                 "stock":"BTC",
        //                 "money":"USD",
        //                 "stockPrec":"6",
        //                 "feePrec":"4",
        //                 "minAmount":"0.001",
        //                 "tradesEnabled":true,
        //                 "minTotal":"0.001"
        //             }
        //         )
        //     }
        //
        $markets = $this->safe_value($response, 'result');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'name');
            $baseId = $this->safe_string($market, 'stock');
            $quoteId = $this->safe_string($market, 'money');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $active = $this->safe_value($market, 'tradesEnabled');
            $entry = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'active' => $active,
                'precision' => array(
                    'amount' => $this->safe_integer($market, 'stockPrec'),
                    'price' => $this->safe_integer($market, 'moneyPrec'),
                ),
                'limits' => array(
                    'amount' => array(
                        'min' => $this->safe_number($market, 'minAmount'),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => null,
                        'max' => null,
                    ),
                    'cost' => array(
                        'min' => $this->safe_number($market, 'minTotal'),
                        'max' => null,
                    ),
                ),
            );
            $result[] = $entry;
        }
        return $result;
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicV2GetAssets ($params);
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result":{
        //             "BTC":{
        //                 "$id":"4f37bc79-f612-4a63-9a81-d37f7f9ff622",
        //                 "lastUpdateTimestamp":"2019-10-12T04:40:05.000Z",
        //                 "$name":"Bitcoin",
        //                 "$canWithdraw":true,
        //                 "$canDeposit":true,
        //                 "minWithdrawal":"0.001",
        //                 "maxWithdrawal":"0",
        //                 "makerFee":"0.1",
        //                 "takerFee":"0.1"
        //             }
        //         }
        //     }
        //
        $currencies = $this->safe_value($response, 'result');
        $ids = is_array($currencies) ? array_keys($currencies) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $currency = $currencies[$id];
            // breaks down in Python due to utf8 encoding issues on the exchange side
            // $name = $this->safe_string($currency, 'name');
            $canDeposit = $this->safe_value($currency, 'canDeposit', true);
            $canWithdraw = $this->safe_value($currency, 'canWithdraw', true);
            $active = $canDeposit && $canWithdraw;
            $code = $this->safe_currency_code($id);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency, // the original payload
                'name' => null, // see the comment above
                'active' => $active,
                'fee' => null,
                'precision' => null,
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
                        'min' => null,
                        'max' => null,
                    ),
                    'withdraw' => array(
                        'min' => $this->safe_number($currency, 'minWithdrawal'),
                        'max' => $this->safe_number($currency, 'maxWithdrawal'),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_trading_fees($params = array ()) {
        $response = $this->publicV2GetFee ($params);
        $fees = $this->safe_value($response, 'result');
        return array(
            'maker' => $this->safe_number($fees, 'makerFee'),
            'taker' => $this->safe_number($fees, 'takerFee'),
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        $response = $this->publicV1GetTicker (array_merge($request, $params));
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "result" => array(
        //             "bid":"0.021979",
        //             "ask":"0.021996",
        //             "open":"0.02182",
        //             "high":"0.022039",
        //             "low":"0.02161",
        //             "last":"0.021987",
        //             "volume":"2810.267",
        //             "deal":"61.383565474",
        //             "change":"0.76",
        //         ),
        //     }
        //
        $ticker = $this->safe_value($response, 'result', array());
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // fetchTicker
        //
        //     {
        //         "bid":"0.021979",
        //         "ask":"0.021996",
        //         "open":"0.02182",
        //         "high":"0.022039",
        //         "low":"0.02161",
        //         "$last":"0.021987",
        //         "volume":"2810.267",
        //         "deal":"61.383565474",
        //         "$change":"0.76",
        //     }
        //
        // fetchTickers v1
        //
        //     {
        //         "at":1571022144,
        //         "$ticker" => {
        //             "bid":"0.022024",
        //             "ask":"0.022042",
        //             "low":"0.02161",
        //             "high":"0.022062",
        //             "$last":"0.022036",
        //             "vol":"2813.503",
        //             "deal":"61.457279261",
        //             "$change":"0.95"
        //         }
        //     }
        //
        $timestamp = $this->safe_timestamp($ticker, 'at', $this->milliseconds());
        $ticker = $this->safe_value($ticker, 'ticker', $ticker);
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_number($ticker, 'last');
        $percentage = $this->safe_number($ticker, 'change');
        $change = null;
        if ($percentage !== null) {
            $change = $this->number_to_string($percentage * 0.01);
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_number($ticker, 'high'),
            'low' => $this->safe_number($ticker, 'low'),
            'bid' => $this->safe_number($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => $this->safe_number($ticker, 'open'),
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'volume'),
            'quoteVolume' => $this->safe_number($ticker, 'deal'),
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicV1GetTickers ($params);
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result" => {
        //             "ETH_BTC" => array(
        //                 "at":1571022144,
        //                 "$ticker" => array(
        //                     "bid":"0.022024",
        //                     "ask":"0.022042",
        //                     "low":"0.02161",
        //                     "high":"0.022062",
        //                     "last":"0.022036",
        //                     "vol":"2813.503",
        //                     "deal":"61.457279261",
        //                     "change":"0.95"
        //                 }
        //             ),
        //         ),
        //     }
        //
        $data = $this->safe_value($response, 'result');
        $marketIds = is_array($data) ? array_keys($data) : array();
        $result = array();
        for ($i = 0; $i < count($marketIds); $i++) {
            $marketId = $marketIds[$i];
            $market = $this->safe_market($marketId);
            $ticker = $this->parse_ticker($data[$marketId], $market);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 50, maximum = 100
        }
        $response = $this->publicV2GetDepthMarket (array_merge($request, $params));
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result":{
        //             "lastUpdateTimestamp":"2019-10-14T03:15:47.000Z",
        //             "asks":[
        //                 ["0.02204","2.03"],
        //                 ["0.022041","2.492"],
        //                 ["0.022042","2.254"],
        //             ],
        //             "bids":[
        //                 ["0.022018","2.327"],
        //                 ["0.022017","1.336"],
        //                 ["0.022015","2.089"],
        //             ],
        //         }
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        $timestamp = $this->parse8601($this->safe_string($result, 'lastUpdateTimestamp'));
        return $this->parse_order_book($result, $timestamp);
    }

    public function fetch_trades_v1($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'lastId' => 1, // todo add $since
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 50, maximum = 10000
        }
        $response = $this->publicV1GetHistory (array_merge($request, $params));
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result":array(
        //             {
        //                 "id":11887426,
        //                 "type":"buy",
        //                 "time":1571023057.413769,
        //                 "amount":"0.171",
        //                 "price":"0.022052"
        //             }
        //         ),
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_trades($result, $market, $since, $limit);
    }

    public function fetch_trades_v2($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default = 50, maximum = 10000
        }
        $response = $this->publicV2GetTradesMarket (array_merge($request, $params));
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result" => array(
        //             array(
        //                 "tradeId":11903347,
        //                 "price":"0.022044",
        //                 "volume":"0.029",
        //                 "time":"2019-10-14T06:30:57.000Z",
        //                 "isBuyerMaker":false
        //             ),
        //         ),
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_trades($result, $market, $since, $limit);
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $method = $this->safe_string($this->options, 'fetchTradesMethod', 'fetchTradesV2');
        return $this->$method ($symbol, $since, $limit, $params);
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTradesV1
        //
        //     {
        //         "$id":11887426,
        //         "type":"buy",
        //         "time":1571023057.413769,
        //         "$amount":"0.171",
        //         "$price":"0.022052"
        //     }
        //
        // fetchTradesV2
        //
        //     {
        //         "tradeId":11903347,
        //         "$price":"0.022044",
        //         "volume":"0.029",
        //         "time":"2019-10-14T06:30:57.000Z",
        //         "$isBuyerMaker":false
        //     }
        //
        $timestamp = $this->safe_value($trade, 'time');
        if (gettype($timestamp) === 'string') {
            $timestamp = $this->parse8601($timestamp);
        } else {
            $timestamp = intval($timestamp * 1000);
        }
        $priceString = $this->safe_string($trade, 'price');
        $amountString = $this->safe_string_2($trade, 'amount', 'volume');
        $cost = $this->parse_number(Precise::string_mul($priceString, $amountString));
        $price = $this->parse_number($priceString);
        $amount = $this->parse_number($amountString);
        $id = $this->safe_string_2($trade, 'id', 'tradeId');
        $side = $this->safe_string($trade, 'type');
        if ($side === null) {
            $isBuyerMaker = $this->safe_value($trade, 'isBuyerMaker');
            $side = $isBuyerMaker ? 'buy' : 'sell';
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => null,
            'type' => null,
            'takerOrMaker' => null,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'market' => $market['id'],
            'interval' => $this->timeframes[$timeframe],
        );
        if ($since !== null) {
            $maxLimit = 1440;
            if ($limit === null) {
                $limit = $maxLimit;
            }
            $limit = min ($limit, $maxLimit);
            $start = intval($since / 1000);
            $duration = $this->parse_timeframe($timeframe);
            $end = $this->sum($start, $duration * $limit);
            $request['start'] = $start;
            $request['end'] = $end;
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // max 1440
        }
        $response = $this->publicV1GetKline (array_merge($request, $params));
        //
        //     {
        //         "success":true,
        //         "message":"",
        //         "$result":[
        //             [1591488000,"0.025025","0.025025","0.025029","0.025023","6.181","0.154686629"],
        //             [1591488060,"0.025028","0.025033","0.025035","0.025026","8.067","0.201921167"],
        //             [1591488120,"0.025034","0.02505","0.02505","0.025034","20.089","0.503114696"],
        //         ]
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_ohlcvs($result, $market, $timeframe, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1591488000,
        //         "0.025025",
        //         "0.025025",
        //         "0.025029",
        //         "0.025023",
        //         "6.181",
        //         "0.154686629"
        //     )
        //
        return array(
            $this->safe_timestamp($ohlcv, 0), // timestamp
            $this->safe_number($ohlcv, 1), // open
            $this->safe_number($ohlcv, 3), // high
            $this->safe_number($ohlcv, 4), // low
            $this->safe_number($ohlcv, 2), // close
            $this->safe_number($ohlcv, 5), // volume
        );
    }

    public function fetch_status($params = array ()) {
        $response = $this->webGetV1Healthcheck ($params);
        $status = $this->safe_integer($response, 'status');
        $formattedStatus = 'ok';
        if ($status === 503) {
            $formattedStatus = 'maintenance';
        }
        $this->status = array_merge($this->status, array(
            'status' => $formattedStatus,
            'updated' => $this->milliseconds(),
        ));
        return $this->status;
    }

    public function sign($path, $api = 'publicV1', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = $this->omit($params, $this->extract_params($path));
        $url = $this->urls['api'][$api] . '/' . $this->implode_params($path, $params);
        if ($query) {
            $url .= '?' . $this->urlencode($query);
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if (($code === 418) || ($code === 429)) {
            throw new DDoSProtection($this->id . ' ' . (string) $code . ' ' . $reason . ' ' . $body);
        }
        if ($code === 404) {
            throw new ExchangeError($this->id . ' ' . (string) $code . ' endpoint not found');
        }
        if ($response !== null) {
            $success = $this->safe_value($response, 'success');
            if (!$success) {
                $feedback = $this->id . ' ' . $body;
                $status = $this->safe_string($response, 'status');
                if (gettype($status) === 'string') {
                    $this->throw_exactly_matched_exception($this->exceptions['exact'], $status, $feedback);
                }
                $this->throw_broadly_matched_exception($this->exceptions['broad'], $body, $feedback);
                throw new ExchangeError($feedback);
            }
        }
    }
}

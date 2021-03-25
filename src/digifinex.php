<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\BadResponse;
use \ccxt\InvalidAddress;
use \ccxt\OrderNotFound;

class digifinex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'digifinex',
            'name' => 'DigiFinex',
            'countries' => array( 'SG' ),
            'version' => 'v3',
            'rateLimit' => 900, // 300 for posts
            'has' => array(
                'cancelOrder' => true,
                'cancelOrders' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchDeposits' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchStatus' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchTrades' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1',
                '5m' => '5',
                '15m' => '15',
                '30m' => '30',
                '1h' => '60',
                '4h' => '240',
                '12h' => '720',
                '1d' => '1D',
                '1w' => '1W',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87443315-01283a00-c5fe-11ea-8628-c2a0feaf07ac.jpg',
                'api' => 'https://openapi.digifinex.com',
                'www' => 'https://www.digifinex.com',
                'doc' => array(
                    'https://docs.digifinex.com',
                ),
                'fees' => 'https://digifinex.zendesk.com/hc/en-us/articles/360000328422-Fee-Structure-on-DigiFinex',
                'referral' => 'https://www.digifinex.com/en-ww/from/DhOzBg?channelCode=ljaUPp',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        '{market}/symbols',
                        'kline',
                        'margin/currencies',
                        'margin/symbols',
                        'markets',
                        'order_book',
                        'ping',
                        'spot/symbols',
                        'time',
                        'trades',
                        'trades/symbols',
                        'ticker',
                        'currencies', // todo add fetchCurrencies
                    ),
                ),
                'private' => array(
                    'get' => array(
                        '{market}/financelog',
                        '{market}/mytrades',
                        '{market}/order',
                        '{market}​/order​/detail', // todo add fetchOrder
                        '{market}/order/current',
                        '{market}/order/history',
                        'margin/assets',
                        'margin/financelog',
                        'margin/mytrades',
                        'margin/order',
                        'margin/order/current',
                        'margin/order/history',
                        'margin/positions',
                        'otc/financelog',
                        'spot/assets',
                        'spot/financelog',
                        'spot/mytrades',
                        'spot/order',
                        'spot/order/current',
                        'spot/order/history',
                        'deposit/address', // todo add fetchDepositAddress
                        'deposit/history', // todo add fetchDeposits
                        'withdraw/history', // todo add fetchWithdrawals
                    ),
                    'post' => array(
                        '{market}/order/cancel',
                        '{market}/order/new',
                        '{market}​/order​/batch_new',
                        'margin/order/cancel',
                        'margin/order/new',
                        'margin/position/close',
                        'spot/order/cancel',
                        'spot/order/new',
                        'transfer',
                        'withdraw/new', // todo add withdraw()
                        'withdraw/cancel',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.002,
                    'taker' => 0.002,
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    '10001' => array( '\\ccxt\\BadRequest', "Wrong request method, please check it's a GET ot POST request" ),
                    '10002' => array( '\\ccxt\\AuthenticationError', 'Invalid ApiKey' ),
                    '10003' => array( '\\ccxt\\AuthenticationError', "Sign doesn't match" ),
                    '10004' => array( '\\ccxt\\BadRequest', 'Illegal request parameters' ),
                    '10005' => array( '\\ccxt\\DDoSProtection', 'Request frequency exceeds the limit' ),
                    '10006' => array( '\\ccxt\\PermissionDenied', 'Unauthorized to execute this request' ),
                    '10007' => array( '\\ccxt\\PermissionDenied', 'IP address Unauthorized' ),
                    '10008' => array( '\\ccxt\\InvalidNonce', 'Timestamp for this request is invalid, timestamp must within 1 minute' ),
                    '10009' => array( '\\ccxt\\NetworkError', 'Unexist endpoint, please check endpoint URL' ),
                    '10011' => array( '\\ccxt\\AccountSuspended', 'ApiKey expired. Please go to client side to re-create an ApiKey' ),
                    '20001' => array( '\\ccxt\\PermissionDenied', 'Trade is not open for this trading pair' ),
                    '20002' => array( '\\ccxt\\PermissionDenied', 'Trade of this trading pair is suspended' ),
                    '20003' => array( '\\ccxt\\InvalidOrder', 'Invalid price or amount' ),
                    '20007' => array( '\\ccxt\\InvalidOrder', 'Price precision error' ),
                    '20008' => array( '\\ccxt\\InvalidOrder', 'Amount precision error' ),
                    '20009' => array( '\\ccxt\\InvalidOrder', 'Amount is less than the minimum requirement' ),
                    '20010' => array( '\\ccxt\\InvalidOrder', 'Cash Amount is less than the minimum requirement' ),
                    '20011' => array( '\\ccxt\\InsufficientFunds', 'Insufficient balance' ),
                    '20012' => array( '\\ccxt\\BadRequest', 'Invalid trade type, valid value => buy/sell)' ),
                    '20013' => array( '\\ccxt\\InvalidOrder', 'No order info found' ),
                    '20014' => array( '\\ccxt\\BadRequest', 'Invalid date, Valid format => 2018-07-25)' ),
                    '20015' => array( '\\ccxt\\BadRequest', 'Date exceeds the limit' ),
                    '20018' => array( '\\ccxt\\PermissionDenied', 'Your trading rights have been banned by the system' ),
                    '20019' => array( '\\ccxt\\BadRequest', 'Wrong trading pair symbol. Correct format:"usdt_btc". Quote asset is in the front' ),
                    '20020' => array( '\\ccxt\\DDoSProtection', "You have violated the API operation trading rules and temporarily forbid trading. At present, we have certain restrictions on the user's transaction rate and withdrawal rate." ),
                    '50000' => array( '\\ccxt\\ExchangeError', 'Exception error' ),
                    '20021' => array( '\\ccxt\\BadRequest', 'Invalid currency' ),
                    '20022' => array( '\\ccxt\\BadRequest', 'The ending timestamp must be larger than the starting timestamp' ),
                    '20023' => array( '\\ccxt\\BadRequest', 'Invalid transfer type' ),
                    '20024' => array( '\\ccxt\\BadRequest', 'Invalid amount' ),
                    '20025' => array( '\\ccxt\\BadRequest', 'This currency is not transferable at the moment' ),
                    '20026' => array( '\\ccxt\\InsufficientFunds', 'Transfer amount exceed your balance' ),
                    '20027' => array( '\\ccxt\\PermissionDenied', 'Abnormal account status' ),
                    '20028' => array( '\\ccxt\\PermissionDenied', 'Blacklist for transfer' ),
                    '20029' => array( '\\ccxt\\PermissionDenied', 'Transfer amount exceed your daily limit' ),
                    '20030' => array( '\\ccxt\\BadRequest', 'You have no position on this trading pair' ),
                    '20032' => array( '\\ccxt\\PermissionDenied', 'Withdrawal limited' ),
                    '20033' => array( '\\ccxt\\BadRequest', 'Wrong Withdrawal ID' ),
                    '20034' => array( '\\ccxt\\PermissionDenied', 'Withdrawal service of this crypto has been closed' ),
                    '20035' => array( '\\ccxt\\PermissionDenied', 'Withdrawal limit' ),
                    '20036' => array( '\\ccxt\\ExchangeError', 'Withdrawal cancellation failed' ),
                    '20037' => array( '\\ccxt\\InvalidAddress', 'The withdrawal address, Tag or chain type is not included in the withdrawal management list' ),
                    '20038' => array( '\\ccxt\\InvalidAddress', 'The withdrawal address is not on the white list' ),
                    '20039' => array( '\\ccxt\\ExchangeError', "Can't be canceled in current status" ),
                    '20040' => array( '\\ccxt\\RateLimitExceeded', 'Withdraw too frequently; limitation => 3 times a minute, 100 times a day' ),
                    '20041' => array( '\\ccxt\\PermissionDenied', 'Beyond the daily withdrawal limit' ),
                    '20042' => array( '\\ccxt\\BadSymbol', 'Current trading pair does not support API trading' ),
                ),
                'broad' => array(
                ),
            ),
            'options' => array(
                'defaultType' => 'spot',
                'types' => array( 'spot', 'margin', 'otc' ),
            ),
            'commonCurrencies' => array(
                'BHT' => 'Black House Test',
                'MBN' => 'Mobilian Coin',
                'TEL' => 'TEL666',
            ),
        ));
    }

    public function fetch_currencies($params = array ()) {
        $response = $this->publicGetCurrencies ($params);
        //
        //     {
        //         "$data":array(
        //             array(
        //                 "deposit_status":1,
        //                 "min_deposit_amount":10,
        //                 "withdraw_fee_rate":0,
        //                 "min_withdraw_amount":10,
        //                 "min_withdraw_fee":5,
        //                 "$currency":"USDT",
        //                 "withdraw_status":0,
        //                 "chain":"OMNI"
        //             ),
        //             array(
        //                 "deposit_status":1,
        //                 "min_deposit_amount":10,
        //                 "withdraw_fee_rate":0,
        //                 "min_withdraw_amount":10,
        //                 "min_withdraw_fee":3,
        //                 "$currency":"USDT",
        //                 "withdraw_status":1,
        //                 "chain":"ERC20"
        //             ),
        //             array(
        //                 "deposit_status":0,
        //                 "min_deposit_amount":0,
        //                 "withdraw_fee_rate":0,
        //                 "min_withdraw_amount":0,
        //                 "min_withdraw_fee":0,
        //                 "$currency":"DGF13",
        //                 "withdraw_status":0,
        //                 "chain":""
        //             ),
        //         ),
        //         "$code":200
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $currency = $data[$i];
            $id = $this->safe_string($currency, 'currency');
            $code = $this->safe_currency_code($id);
            $depositStatus = $this->safe_value($currency, 'deposit_status', 1);
            $withdrawStatus = $this->safe_value($currency, 'withdraw_status', 1);
            $active = $depositStatus && $withdrawStatus;
            $fee = $this->safe_float($currency, 'withdraw_fee_rate');
            if (is_array($result) && array_key_exists($code, $result)) {
                if (gettype($result[$code]['info']) === 'array' && count(array_filter(array_keys($result[$code]['info']), 'is_string')) == 0) {
                    $result[$code]['info'][] = $currency;
                } else {
                    $result[$code]['info'] = [ $result[$code]['info'], $currency ];
                }
            } else {
                $result[$code] = array(
                    'id' => $id,
                    'code' => $code,
                    'info' => $currency,
                    'type' => null,
                    'name' => null,
                    'active' => $active,
                    'fee' => $fee,
                    'precision' => 8, // todo fix hardcoded value
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
                            'min' => $this->safe_float($currency, 'min_withdraw_amount'),
                            'max' => null,
                        ),
                    ),
                );
            }
        }
        return $result;
    }

    public function fetch_markets($params = array ()) {
        $options = $this->safe_value($this->options, 'fetchMarkets', array());
        $method = $this->safe_string($options, 'method', 'fetch_markets_v2');
        return $this->$method ($params);
    }

    public function fetch_markets_v2($params = array ()) {
        $response = $this->publicGetTradesSymbols ($params);
        //
        //     {
        //         "symbol_list":[
        //             {
        //                 "order_types":["LIMIT","MARKET"],
        //                 "quote_asset":"USDT",
        //                 "minimum_value":2,
        //                 "amount_precision":4,
        //                 "$status":"TRADING",
        //                 "minimum_amount":0.0001,
        //                 "$symbol":"BTC_USDT",
        //                 "is_allow":1,
        //                 "zone":"MAIN",
        //                 "base_asset":"BTC",
        //                 "price_precision":2
        //             }
        //         ],
        //         "code":0
        //     }
        //
        $markets = $this->safe_value($response, 'symbol_list', array());
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'symbol');
            $baseId = $this->safe_string($market, 'base_asset');
            $quoteId = $this->safe_string($market, 'quote_asset');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'amount_precision'),
                'price' => $this->safe_integer($market, 'price_precision'),
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'minimum_amount'),
                    'max' => null,
                ),
                'price' => array(
                    'min' => null,
                    'max' => null,
                ),
                'cost' => array(
                    'min' => $this->safe_float($market, 'minimum_value'),
                    'max' => null,
                ),
            );
            //
            // The $status is documented in the exchange API docs as follows:
            // TRADING, HALT (delisted), BREAK (trading paused)
            // https://docs.digifinex.vip/en-ww/v3/#/public/spot/symbols
            // However, all $spot $markets actually have $status === 'HALT'
            // despite that they appear to be $active on the exchange website.
            // Apparently, we can't trust this $status->
            // $status = $this->safe_string($market, 'status');
            // $active = ($status === 'TRADING');
            //
            $isAllowed = $this->safe_value($market, 'is_allow', 1);
            $active = $isAllowed ? true : false;
            $type = 'spot';
            $spot = ($type === 'spot');
            $margin = ($type === 'margin');
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => $active,
                'type' => $type,
                'spot' => $spot,
                'margin' => $margin,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_markets_v1($params = array ()) {
        $response = $this->publicGetMarkets ($params);
        //
        //     {
        //         "data" => array(
        //             array(
        //                 "volume_precision":4,
        //                 "price_precision":2,
        //                 "$market":"btc_usdt",
        //                 "min_amount":2,
        //                 "min_volume":0.0001
        //             ),
        //         ),
        //         "date":1564507456,
        //         "code":0
        //     }
        //
        $markets = $this->safe_value($response, 'data', array());
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'market');
            list($baseId, $quoteId) = explode('_', $id);
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $precision = array(
                'amount' => $this->safe_integer($market, 'volume_precision'),
                'price' => $this->safe_integer($market, 'price_precision'),
            );
            $limits = array(
                'amount' => array(
                    'min' => $this->safe_float($market, 'min_volume'),
                    'max' => null,
                ),
                'price' => array(
                    'min' => null,
                    'max' => null,
                ),
                'cost' => array(
                    'min' => $this->safe_float($market, 'min_amount'),
                    'max' => null,
                ),
            );
            $active = null;
            $result[] = array(
                'id' => $id,
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
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $type = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $method = 'privateGet' . $this->capitalize($type) . 'Assets';
        $response = $this->$method ($params);
        //
        //     {
        //         "$code" => 0,
        //         "list" => array(
        //             {
        //                 "currency" => "BTC",
        //                 "free" => 4723846.89208129,
        //                 "total" => 0
        //             }
        //         )
        //     }
        $balances = $this->safe_value($response, 'list', array());
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $account['used'] = $this->safe_float($balance, 'frozen');
            $account['free'] = $this->safe_float($balance, 'free');
            $account['total'] = $this->safe_float($balance, 'total');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 10, max 150
        }
        $response = $this->publicGetOrderBook (array_merge($request, $params));
        //
        //     {
        //         "bids" => [
        //             [9605.77,0.0016],
        //             [9605.46,0.0003],
        //             [9602.04,0.0127],
        //         ],
        //         "asks" => [
        //             [9627.22,0.025803],
        //             [9627.12,0.168543],
        //             [9626.52,0.0011529],
        //         ],
        //         "date":1564509499,
        //         "code":0
        //     }
        //
        $timestamp = $this->safe_timestamp($response, 'date');
        return $this->parse_order_book($response, $timestamp);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTicker ($params);
        //
        //    {
        //        "$ticker" => [array(
        //            "vol" => 40717.4461,
        //            "change" => -1.91,
        //            "base_vol" => 392447999.65374,
        //            "sell" => 9592.23,
        //            "last" => 9592.22,
        //            "$symbol" => "btc_usdt",
        //            "low" => 9476.24,
        //            "buy" => 9592.03,
        //            "high" => 9793.87
        //        )],
        //        "$date" => 1589874294,
        //        "code" => 0
        //    }
        //
        $result = array();
        $tickers = $this->safe_value($response, 'ticker', array());
        $date = $this->safe_integer($response, 'date');
        for ($i = 0; $i < count($tickers); $i++) {
            $rawTicker = array_merge(array(
                'date' => $date,
            ), $tickers[$i]);
            $ticker = $this->parse_ticker($rawTicker);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTicker (array_merge($request, $params));
        //
        //    {
        //        "ticker" => [array(
        //            "vol" => 40717.4461,
        //            "change" => -1.91,
        //            "base_vol" => 392447999.65374,
        //            "sell" => 9592.23,
        //            "last" => 9592.22,
        //            "$symbol" => "btc_usdt",
        //            "low" => 9476.24,
        //            "buy" => 9592.03,
        //            "high" => 9793.87
        //        )],
        //        "$date" => 1589874294,
        //        "code" => 0
        //    }
        //
        $date = $this->safe_integer($response, 'date');
        $tickers = $this->safe_value($response, 'ticker', array());
        $firstTicker = $this->safe_value($tickers, 0, array());
        $result = array_merge(array( 'date' => $date ), $firstTicker);
        return $this->parse_ticker($result, $market);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        // fetchTicker, fetchTickers
        //
        //     {
        //         "$last":0.021957,
        //         "$symbol" => "btc_usdt",
        //         "base_vol":2249.3521732227,
        //         "change":-0.6,
        //         "vol":102443.5111,
        //         "sell":0.021978,
        //         "low":0.021791,
        //         "buy":0.021946,
        //         "high":0.022266,
        //         "date"1564518452, // injected from fetchTicker/fetchTickers
        //     }
        //
        $marketId = $this->safe_string_upper($ticker, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $timestamp = $this->safe_timestamp($ticker, 'date');
        $last = $this->safe_float($ticker, 'last');
        $percentage = $this->safe_float($ticker, 'change');
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
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => $this->safe_float($ticker, 'base_vol'),
            'info' => $ticker,
        );
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "date":1564520003,
        //         "$id":1596149203,
        //         "$amount":0.7073,
        //         "type":"buy",
        //         "$price":0.02193,
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "$symbol" => "BTC_USDT",
        //         "order_id" => "6707cbdcda0edfaa7f4ab509e4cbf966",
        //         "$id" => 28457,
        //         "$price" => 0.1,
        //         "$amount" => 0,
        //         "$fee" => 0.096,
        //         "fee_currency" => "USDT",
        //         "$timestamp" => 1499865549,
        //         "$side" => "buy",
        //         "is_maker" => true
        //     }
        //
        $id = $this->safe_string($trade, 'id');
        $orderId = $this->safe_string($trade, 'order_id');
        $timestamp = $this->safe_timestamp_2($trade, 'date', 'timestamp');
        $side = $this->safe_string_2($trade, 'type', 'side');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $marketId = $this->safe_string($trade, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $takerOrMaker = $this->safe_value($trade, 'is_maker');
        $feeCost = $this->safe_float($trade, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $feeCurrencyId = $this->safe_string($trade, 'fee_currency');
            $feeCurrencyCode = $this->safe_currency_code($feeCurrencyId);
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
            'type' => null,
            'order' => $orderId,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'takerOrMaker' => $takerOrMaker,
            'fee' => $fee,
        );
    }

    public function fetch_time($params = array ()) {
        $response = $this->publicGetTime ($params);
        //
        //     {
        //         "server_time" => 1589873762,
        //         "code" => 0
        //     }
        //
        return $this->safe_timestamp($response, 'server_time');
    }

    public function fetch_status($params = array ()) {
        $this->publicGetPing ($params);
        //
        //     {
        //         "msg" => "pong",
        //         "code" => 0
        //     }
        //
        $this->status = array_merge($this->status, array(
            'status' => 'ok',
            'updated' => $this->milliseconds(),
        ));
        return $this->status;
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 100, max 500
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        //
        //     {
        //         "$data":array(
        //             array(
        //                 "date":1564520003,
        //                 "id":1596149203,
        //                 "amount":0.7073,
        //                 "type":"buy",
        //                 "price":0.02193,
        //             ),
        //             array(
        //                 "date":1564520002,
        //                 "id":1596149165,
        //                 "amount":0.3232,
        //                 "type":"sell",
        //                 "price":0.021927,
        //             ),
        //         ),
        //         "code" => 0,
        //         "date" => 1564520003,
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     array(
        //         1556712900,
        //         2205.899,
        //         0.029967,
        //         0.02997,
        //         0.029871,
        //         0.029927
        //     )
        //
        return array(
            $this->safe_timestamp($ohlcv, 0),
            $this->safe_float($ohlcv, 5), // open
            $this->safe_float($ohlcv, 3), // high
            $this->safe_float($ohlcv, 4), // low
            $this->safe_float($ohlcv, 2), // close
            $this->safe_float($ohlcv, 1), // volume
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'period' => $this->timeframes[$timeframe],
            // 'start_time' => 1564520003, // starting timestamp, 200 candles before end_time by default
            // 'end_time' => 1564520003, // ending timestamp, current timestamp by default
        );
        if ($since !== null) {
            $startTime = intval($since / 1000);
            $request['start_time'] = $startTime;
            if ($limit !== null) {
                $duration = $this->parse_timeframe($timeframe);
                $request['end_time'] = $this->sum($startTime, $limit * $duration);
            }
        } else if ($limit !== null) {
            $endTime = $this->seconds();
            $duration = $this->parse_timeframe($timeframe);
            $request['startTime'] = $this->sum($endTime, -$limit * $duration);
        }
        $response = $this->publicGetKline (array_merge($request, $params));
        //
        //     {
        //         "code":0,
        //         "$data":[
        //             [1556712900,2205.899,0.029967,0.02997,0.029871,0.029927],
        //             [1556713800,1912.9174,0.029992,0.030014,0.029955,0.02996],
        //             [1556714700,1556.4795,0.029974,0.030019,0.029969,0.02999],
        //         ]
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_ohlcvs($data, $market, $timeframe, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $request = array(
            'market' => $orderType,
            'symbol' => $market['id'],
            'amount' => $this->amount_to_precision($symbol, $amount),
            // 'post_only' => 0, // 0 by default, if set to 1 the order will be canceled if it can be executed immediately, making sure there will be no $market taking
        );
        $suffix = '';
        if ($type === 'market') {
            $suffix = '_market';
        } else {
            $request['price'] = $this->price_to_precision($symbol, $price);
        }
        $request['type'] = $side . $suffix;
        $response = $this->privatePostMarketOrderNew (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "order_id" => "198361cecdc65f9c8c9bb2fa68faec40"
        //     }
        //
        $result = $this->parse_order($response, $market);
        return array_merge($result, array(
            'symbol' => $symbol,
            'side' => $side,
            'type' => $type,
            'amount' => $amount,
            'price' => $price,
        ));
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $request = array(
            'market' => $orderType,
            'order_id' => $id,
        );
        $response = $this->privatePostMarketOrderCancel (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "success" => array(
        //             "198361cecdc65f9c8c9bb2fa68faec40",
        //             "3fb0d98e51c18954f10d439a9cf57de0"
        //         ),
        //         "error" => array(
        //             "78a7104e3c65cc0c5a212a53e76d0205"
        //         )
        //     }
        //
        $canceledOrders = $this->safe_value($response, 'success', array());
        $numCanceledOrders = is_array($canceledOrders) ? count($canceledOrders) : 0;
        if ($numCanceledOrders !== 1) {
            throw new OrderNotFound($this->id . ' cancelOrder ' . $id . ' not found');
        }
        return $response;
    }

    public function cancel_orders($ids, $symbol = null, $params = array ()) {
        $this->load_markets();
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $request = array(
            'market' => $orderType,
            'order_id' => implode(',', $ids),
        );
        $response = $this->privatePostCancelOrder (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "success" => array(
        //             "198361cecdc65f9c8c9bb2fa68faec40",
        //             "3fb0d98e51c18954f10d439a9cf57de0"
        //         ),
        //         "error" => array(
        //             "78a7104e3c65cc0c5a212a53e76d0205"
        //         )
        //     }
        //
        $canceledOrders = $this->safe_value($response, 'success', array());
        $numCanceledOrders = is_array($canceledOrders) ? count($canceledOrders) : 0;
        if ($numCanceledOrders < 1) {
            throw new OrderNotFound($this->id . ' cancelOrders error');
        }
        return $response;
    }

    public function parse_order_status($status) {
        $statuses = array(
            '0' => 'open',
            '1' => 'open', // partially filled
            '2' => 'closed',
            '3' => 'canceled',
            '4' => 'canceled', // partially filled and canceled
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "code" => 0,
        //         "order_id" => "198361cecdc65f9c8c9bb2fa68faec40"
        //     }
        //
        // fetchOrder, fetchOpenOrders, fetchOrders
        //
        //     {
        //         "$symbol" => "BTC_USDT",
        //         "order_id" => "dd3164b333a4afa9d5730bb87f6db8b3",
        //         "created_date" => 1562303547,
        //         "finished_date" => 0,
        //         "$price" => 0.1,
        //         "$amount" => 1,
        //         "cash_amount" => 1,
        //         "executed_amount" => 0,
        //         "avg_price" => 0,
        //         "$status" => 1,
        //         "$type" => "buy",
        //         "kind" => "margin"
        //     }
        //
        $id = $this->safe_string($order, 'order_id');
        $timestamp = $this->safe_timestamp($order, 'created_date');
        $lastTradeTimestamp = $this->safe_timestamp($order, 'finished_date');
        $side = $this->safe_string($order, 'type');
        $type = null;
        if ($side !== null) {
            $parts = explode('_', $side);
            $numParts = is_array($parts) ? count($parts) : 0;
            if ($numParts > 1) {
                $side = $parts[0];
                $type = $parts[1];
            } else {
                $type = 'limit';
            }
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market, '_');
        $amount = $this->safe_float($order, 'amount');
        $filled = $this->safe_float($order, 'executed_amount');
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'avg_price');
        return $this->safe_order(array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => null,
            'cost' => null,
            'average' => $average,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        ));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $this->load_markets();
        $market = null;
        $request = array(
            'market' => $orderType,
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $response = $this->privateGetMarketOrderCurrent (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "$data" => array(
        //             {
        //                 "$symbol" => "BTC_USDT",
        //                 "order_id" => "dd3164b333a4afa9d5730bb87f6db8b3",
        //                 "created_date" => 1562303547,
        //                 "finished_date" => 0,
        //                 "price" => 0.1,
        //                 "amount" => 1,
        //                 "cash_amount" => 1,
        //                 "executed_amount" => 0,
        //                 "avg_price" => 0,
        //                 "status" => 1,
        //                 "type" => "buy",
        //                 "kind" => "margin"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $this->load_markets();
        $market = null;
        $request = array(
            'market' => $orderType,
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['start_time'] = intval($since / 1000); // default 3 days from now, max 30 days
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 10, max 100
        }
        $response = $this->privateGetMarketOrderHistory (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "$data" => array(
        //             {
        //                 "$symbol" => "BTC_USDT",
        //                 "order_id" => "dd3164b333a4afa9d5730bb87f6db8b3",
        //                 "created_date" => 1562303547,
        //                 "finished_date" => 0,
        //                 "price" => 0.1,
        //                 "amount" => 1,
        //                 "cash_amount" => 1,
        //                 "executed_amount" => 0,
        //                 "avg_price" => 0,
        //                 "status" => 1,
        //                 "type" => "buy",
        //                 "kind" => "margin"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_orders($data, $market, $since, $limit);
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $request = array(
            'market' => $orderType,
            'order_id' => $id,
        );
        $response = $this->privateGetMarketOrder (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "$data" => array(
        //             {
        //                 "$symbol" => "BTC_USDT",
        //                 "order_id" => "dd3164b333a4afa9d5730bb87f6db8b3",
        //                 "created_date" => 1562303547,
        //                 "finished_date" => 0,
        //                 "price" => 0.1,
        //                 "amount" => 1,
        //                 "cash_amount" => 1,
        //                 "executed_amount" => 0,
        //                 "avg_price" => 0,
        //                 "status" => 1,
        //                 "type" => "buy",
        //                 "kind" => "margin"
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $order = $this->safe_value($data, 0);
        if ($order === null) {
            throw new OrderNotFound($this->id . ' fetchOrder() $order ' . $id . ' not found');
        }
        return $this->parse_order($order, $market);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $this->load_markets();
        $market = null;
        $request = array(
            'market' => $orderType,
        );
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['start_time'] = intval($since / 1000); // default 3 days from now, max 30 days
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 10, max 100
        }
        $response = $this->privateGetMarketMytrades (array_merge($request, $params));
        //
        //     {
        //         "code" => 0,
        //         "list" => array(
        //             {
        //                 "$symbol" => "BTC_USDT",
        //                 "order_id" => "6707cbdcda0edfaa7f4ab509e4cbf966",
        //                 "id" => 28457,
        //                 "price" => 0.1,
        //                 "amount" => 0,
        //                 "fee" => 0.096,
        //                 "fee_currency" => "USDT",
        //                 "timestamp" => 1499865549,
        //                 "side" => "buy",
        //                 "is_maker" => true
        //             }
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'list', array());
        return $this->parse_trades($data, $market, $since, $limit);
    }

    public function parse_ledger_entry_type($type) {
        $types = array();
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        //     {
        //         "currency_mark" => "BTC",
        //         "$type" => 100234,
        //         "num" => 28457,
        //         "balance" => 0.1,
        //         "time" => 1546272000
        //     }
        //
        $id = $this->safe_string($item, 'num');
        $account = null;
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'type'));
        $code = $this->safe_currency_code($this->safe_string($item, 'currency_mark'), $currency);
        $timestamp = $this->safe_timestamp($item, 'time');
        $before = null;
        $after = $this->safe_float($item, 'balance');
        $status = 'ok';
        return array(
            'info' => $item,
            'id' => $id,
            'direction' => null,
            'account' => $account,
            'referenceId' => null,
            'referenceAccount' => null,
            'type' => $type,
            'currency' => $code,
            'amount' => null,
            'before' => $before,
            'after' => $after,
            'status' => $status,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => null,
        );
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $defaultType = $this->safe_string($this->options, 'defaultType', 'spot');
        $orderType = $this->safe_string($params, 'type', $defaultType);
        $params = $this->omit($params, 'type');
        $this->load_markets();
        $request = array(
            'market' => $orderType,
        );
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency_mark'] = $currency['id'];
        }
        if ($since !== null) {
            $request['start_time'] = intval($since / 1000);
        }
        if ($limit !== null) {
            $request['limit'] = $limit; // default 100, max 1000
        }
        $response = $this->privateGetMarketFinancelog (array_merge($request, $params));
        //
        //     {
        //         "$code" => 0,
        //         "$data" => {
        //             "total" => 521,
        //             "finance" => array(
        //                 {
        //                     "currency_mark" => "BTC",
        //                     "type" => 100234,
        //                     "num" => 28457,
        //                     "balance" => 0.1,
        //                     "time" => 1546272000
        //                 }
        //             )
        //         }
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $items = $this->safe_value($data, 'finance', array());
        return $this->parse_ledger($items, $currency, $since, $limit);
    }

    public function parse_deposit_address($depositAddress, $currency = null) {
        //
        //     {
        //         "addressTag":"",
        //         "$address":"0xf1104d9f8624f89775a3e9d480fc0e75a8ef4373",
        //         "$currency":"USDT",
        //         "chain":"ERC20"
        //     }
        //
        $address = $this->safe_string($depositAddress, 'address');
        $tag = $this->safe_string($depositAddress, 'addressTag');
        $currencyId = $this->safe_string_upper($depositAddress, 'currency');
        $code = $this->safe_currency_code($currencyId);
        return array(
            'info' => $depositAddress,
            'code' => $code,
            'address' => $address,
            'tag' => $tag,
        );
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privateGetDepositAddress (array_merge($request, $params));
        //
        //     {
        //         "$data":array(
        //             {
        //                 "addressTag":"",
        //                 "$address":"0xf1104d9f8624f89775a3e9d480fc0e75a8ef4373",
        //                 "$currency":"USDT",
        //                 "chain":"ERC20"
        //             }
        //         ),
        //         "$code":200
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        $addresses = $this->parse_deposit_addresses($data);
        $address = $this->safe_value($addresses, $code);
        if ($address === null) {
            throw new InvalidAddress($this->id . ' fetchDepositAddress did not return an $address for ' . $code . ' - create the deposit $address in the user settings on the exchange website first.');
        }
        return $address;
    }

    public function fetch_transactions_by_type($type, $code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array(
            // 'currency' => $currency['id'],
            // 'from' => 'fromId', // When direct is' prev ', from is 1, returning from old to new ascending, when direct is' next ', from is the ID of the most recent record, returned from the old descending order
            // 'size' => 100, // default 100, max 500
            // 'direct' => 'prev', // "prev" ascending, "next" descending
        );
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['currency'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['size'] = min (500, $limit);
        }
        $method = ($type === 'deposit') ? 'privateGetDepositHistory' : 'privateGetWithdrawHistory';
        $response = $this->$method (array_merge($request, $params));
        //
        //     {
        //         "$code" => 200,
        //         "$data" => array(
        //             array(
        //                 "id" => 1171,
        //                 "$currency" => "xrp",
        //                 "hash" => "ed03094b84eafbe4bc16e7ef766ee959885ee5bcb265872baaa9c64e1cf86c2b",
        //                 "chain" => "",
        //                 "amount" => 7.457467,
        //                 "address" => "rae93V8d2mdoUQHwBDBdM4NHCMehRJAsbm",
        //                 "memo" => "100040",
        //                 "fee" => 0,
        //                 "state" => "safe",
        //                 "created_date" => "2020-04-20 11:23:00",
        //                 "finished_date" => "2020-04-20 13:23:00"
        //             ),
        //         )
        //     }
        //
        $data = $this->safe_value($response, 'data', array());
        return $this->parse_transactions($data, $currency, $since, $limit, array( 'type' => $type ));
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type('deposit', $code, $since, $limit, $params);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type('withdrawal', $code, $since, $limit, $params);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            '0' => 'pending', // Email Sent
            '1' => 'canceled', // Cancelled (different from 1 = ok in deposits)
            '2' => 'pending', // Awaiting Approval
            '3' => 'failed', // Rejected
            '4' => 'pending', // Processing
            '5' => 'failed', // Failure
            '6' => 'ok', // Completed
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // withdraw
        //
        //     {
        //         "$code" => 200,
        //         "withdraw_id" => 700
        //     }
        //
        // fetchDeposits, fetchWithdrawals
        //
        //     {
        //         "$id" => 1171,
        //         "$currency" => "xrp",
        //         "hash" => "ed03094b84eafbe4bc16e7ef766ee959885ee5bcb265872baaa9c64e1cf86c2b",
        //         "chain" => "",
        //         "$amount" => 7.457467,
        //         "$address" => "rae93V8d2mdoUQHwBDBdM4NHCMehRJAsbm",
        //         "memo" => "100040",
        //         "$fee" => 0,
        //         "state" => "safe",
        //         "created_date" => "2020-04-20 11:23:00",
        //         "finished_date" => "2020-04-20 13:23:00"
        //     }
        //
        $id = $this->safe_string_2($transaction, 'id', 'withdraw_id');
        $address = $this->safe_string($transaction, 'address');
        $tag = $this->safe_string($transaction, 'memo'); // set but unused
        if ($tag !== null) {
            if (strlen($tag) < 1) {
                $tag = null;
            }
        }
        $txid = $this->safe_string($transaction, 'hash');
        $currencyId = $this->safe_string_upper($transaction, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $timestamp = $this->parse8601($this->safe_string($transaction, 'created_date'));
        $updated = $this->parse8601($this->safe_string($transaction, 'finished_date'));
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'state'));
        $amount = $this->safe_float($transaction, 'amount');
        $feeCost = $this->safe_float($transaction, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $fee = array( 'currency' => $code, 'cost' => $feeCost );
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'addressTo' => $address,
            'addressFrom' => null,
            'tag' => $tag,
            'tagTo' => $tag,
            'tagFrom' => null,
            'type' => null,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => $updated,
            'fee' => $fee,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            // 'chain' => 'ERC20', 'OMNI', 'TRC20', // required for USDT
            'address' => $address,
            'amount' => floatval($amount),
            'currency' => $currency['id'],
        );
        if ($tag !== null) {
            $request['memo'] = $tag;
        }
        $response = $this->privatePostWithdrawNew (array_merge($request, $params));
        //
        //     {
        //         "$code" => 200,
        //         "withdraw_id" => 700
        //     }
        //
        return $this->parse_transaction($response, $currency);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $version = $this->version;
        $url = $this->urls['api'] . '/' . $version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        $urlencoded = $this->urlencode($this->keysort($query));
        if ($api === 'private') {
            $nonce = (string) $this->nonce();
            $auth = $urlencoded;
            // the $signature is not time-limited :\
            $signature = $this->hmac($this->encode($auth), $this->encode($this->secret));
            if ($method === 'GET') {
                if ($urlencoded) {
                    $url .= '?' . $urlencoded;
                }
            } else if ($method === 'POST') {
                $headers = array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                );
                if ($urlencoded) {
                    $body = $urlencoded;
                }
            }
            $headers = array(
                'ACCESS-KEY' => $this->apiKey,
                'ACCESS-SIGN' => $signature,
                'ACCESS-TIMESTAMP' => $nonce,
            );
        } else {
            if ($urlencoded) {
                $url .= '?' . $urlencoded;
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($statusCode, $statusText, $url, $method, $responseHeaders, $responseBody, $response, $requestHeaders, $requestBody) {
        if (!$response) {
            return; // fall back to default error handler
        }
        $code = $this->safe_string($response, 'code');
        if (($code === '0') || ($code === '200')) {
            return; // no error
        }
        $feedback = $this->id . ' ' . $responseBody;
        if ($code === null) {
            throw new BadResponse($feedback);
        }
        $unknownError = array( '\\ccxt\\ExchangeError', $feedback );
        list($ExceptionClass, $message) = $this->safe_value($this->exceptions['exact'], $code, $unknownError);
        throw new $ExceptionClass($message);
    }
}

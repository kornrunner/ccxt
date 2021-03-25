<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\AuthenticationError;
use \ccxt\ArgumentsRequired;
use \ccxt\OrderNotFound;

class bibox extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bibox',
            'name' => 'Bibox',
            'countries' => array( 'CN', 'US', 'KR' ),
            'version' => 'v1',
            'hostname' => 'bibox365.com',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createMarketOrder' => false, // or they will return https://github.com/ccxt/ccxt/issues/2338
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchCurrencies' => true,
                'fetchDeposits' => true,
                'fetchDepositAddress' => true,
                'fetchFundingFees' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchWithdrawals' => true,
                'publicAPI' => false,
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1min',
                '5m' => '5min',
                '15m' => '15min',
                '30m' => '30min',
                '1h' => '1hour',
                '2h' => '2hour',
                '4h' => '4hour',
                '6h' => '6hour',
                '12h' => '12hour',
                '1d' => 'day',
                '1w' => 'week',
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/77257418-3262b000-6c85-11ea-8fb8-20bdf20b3592.jpg',
                'api' => 'https://api.{hostname}',
                'www' => 'https://www.bibox365.com',
                'doc' => array(
                    'https://biboxcom.github.io/en/',
                ),
                'fees' => 'https://bibox.zendesk.com/hc/en-us/articles/360002336133',
                'referral' => 'https://w2.bibox365.com/login/register?invite_code=05Kj3I',
            ),
            'api' => array(
                'public' => array(
                    'post' => array(
                        // TODO => rework for full endpoint/cmd paths here
                        'mdata',
                    ),
                    'get' => array(
                        'cquery',
                        'mdata',
                        'cdata',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'cquery',
                        'ctrade',
                        'user',
                        'orderpending',
                        'transfer',
                    ),
                ),
                'v2private' => array(
                    'post' => array(
                        'assets/transfer/spot',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'taker' => 0.001,
                    'maker' => 0.0008,
                ),
                'funding' => array(
                    'tierBased' => false,
                    'percentage' => false,
                    'withdraw' => array(),
                    'deposit' => array(),
                ),
            ),
            'exceptions' => array(
                '2011' => '\\ccxt\\AccountSuspended', // Account is locked
                '2015' => '\\ccxt\\AuthenticationError', // Google authenticator is wrong
                '2021' => '\\ccxt\\InsufficientFunds', // Insufficient balance available for withdrawal
                '2027' => '\\ccxt\\InsufficientFunds', // Insufficient balance available (for trade)
                '2033' => '\\ccxt\\OrderNotFound', // operation failed! Orders have been completed or revoked
                '2065' => '\\ccxt\\InvalidOrder', // Precatory price is exorbitant, please reset
                '2066' => '\\ccxt\\InvalidOrder', // Precatory price is low, please reset
                '2067' => '\\ccxt\\InvalidOrder', // Does not support market orders
                '2068' => '\\ccxt\\InvalidOrder', // The number of orders can not be less than
                '2078' => '\\ccxt\\InvalidOrder', // unvalid order price
                '2085' => '\\ccxt\\InvalidOrder', // Order quantity is too small
                '2091' => '\\ccxt\\RateLimitExceeded', // request is too frequency, please try again later
                '2092' => '\\ccxt\\InvalidOrder', // Minimum amount not met
                '3000' => '\\ccxt\\BadRequest', // Requested parameter incorrect
                '3002' => '\\ccxt\\BadRequest', // Parameter cannot be null
                '3012' => '\\ccxt\\AuthenticationError', // invalid apiKey
                '3016' => '\\ccxt\\BadSymbol', // Trading pair error
                '3024' => '\\ccxt\\PermissionDenied', // wrong apikey permissions
                '3025' => '\\ccxt\\AuthenticationError', // signature failed
                '4000' => '\\ccxt\\ExchangeNotAvailable', // current network is unstable
                '4003' => '\\ccxt\\DDoSProtection', // server busy please try again later
            ),
            'commonCurrencies' => array(
                'BOX' => 'DefiBox',
                'BPT' => 'BlockPool Token',
                'KEY' => 'Bihu',
                'MTC' => 'MTC Mesh Network', // conflict with MTC Docademic doc.com Token https://github.com/ccxt/ccxt/issues/6081 https://github.com/ccxt/ccxt/issues/3025
                'PAI' => 'PCHAIN',
                'TERN' => 'Ternio-ERC20',
            ),
            'options' => array(
                'fetchCurrencies' => 'fetch_currencies_public', // or 'fetch_currencies_private' with apiKey and secret
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $request = array(
            'cmd' => 'marketAll',
        );
        $response = $this->publicGetMdata (array_merge($request, $params));
        //
        //     {
        //         "$result" => array(
        //             {
        //                 "is_hide":0,
        //                 "high_cny":"1.9478",
        //                 "amount":"272.41",
        //                 "coin_symbol":"BIX",
        //                 "last":"0.00002487",
        //                 "currency_symbol":"BTC",
        //                 "change":"+0.00000073",
        //                 "low_cny":"1.7408",
        //                 "base_last_cny":"1.84538041",
        //                 "area_id":7,
        //                 "percent":"+3.02%",
        //                 "last_cny":"1.8454",
        //                 "high":"0.00002625",
        //                 "low":"0.00002346",
        //                 "pair_type":0,
        //                 "last_usd":"0.2686",
        //                 "vol24H":"10940613",
        //                 "$id":1,
        //                 "high_usd":"0.2835",
        //                 "low_usd":"0.2534"
        //             }
        //         ),
        //         "cmd":"marketAll",
        //         "ver":"1.1"
        //     }
        //
        $markets = $this->safe_value($response, 'result');
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $numericId = $this->safe_integer($market, 'id');
            $baseId = $this->safe_string($market, 'coin_symbol');
            $quoteId = $this->safe_string($market, 'currency_symbol');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $id = $baseId . '_' . $quoteId;
            $precision = array(
                'amount' => 4,
                'price' => 8,
            );
            $result[] = array(
                'id' => $id,
                'numericId' => $numericId,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => true,
                'info' => $market,
                'precision' => $precision,
                'limits' => array(
                    'amount' => array(
                        'min' => pow(10, -$precision['amount']),
                        'max' => null,
                    ),
                    'price' => array(
                        'min' => pow(10, -$precision['price']),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function parse_ticker($ticker, $market = null) {
        // we don't set values that are not defined by the exchange
        $timestamp = $this->safe_integer($ticker, 'timestamp');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        } else {
            $baseId = $this->safe_string($ticker, 'coin_symbol');
            $quoteId = $this->safe_string($ticker, 'currency_symbol');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
        }
        $last = $this->safe_float($ticker, 'last');
        $change = $this->safe_float($ticker, 'change');
        $baseVolume = $this->safe_float_2($ticker, 'vol', 'vol24H');
        $open = null;
        if (($last !== null) && ($change !== null)) {
            $open = $last - $change;
        }
        $percentage = $this->safe_string($ticker, 'percent');
        if ($percentage !== null) {
            $percentage = str_replace('%', '', $percentage);
            $percentage = floatval($percentage);
        }
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
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $this->safe_float($ticker, 'amount'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'cmd' => 'ticker',
            'pair' => $market['id'],
        );
        $response = $this->publicGetMdata (array_merge($request, $params));
        return $this->parse_ticker($response['result'], $market);
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $request = array(
            'cmd' => 'marketAll',
        );
        $response = $this->publicGetMdata (array_merge($request, $params));
        $tickers = $this->parse_tickers($response['result'], $symbols);
        $result = $this->index_by($tickers, 'symbol');
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_integer_2($trade, 'time', 'createdAt');
        $side = $this->safe_integer_2($trade, 'side', 'order_side');
        $side = ($side === 1) ? 'buy' : 'sell';
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($trade, 'pair');
            if ($marketId === null) {
                $baseId = $this->safe_string($trade, 'coin_symbol');
                $quoteId = $this->safe_string($trade, 'currency_symbol');
                if (($baseId !== null) && ($quoteId !== null)) {
                    $marketId = $baseId . '_' . $quoteId;
                }
            }
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        $feeCurrency = $this->safe_string($trade, 'fee_symbol');
        if ($feeCurrency !== null) {
            if (is_array($this->currencies_by_id) && array_key_exists($feeCurrency, $this->currencies_by_id)) {
                $feeCurrency = $this->currencies_by_id[$feeCurrency]['code'];
            } else {
                $feeCurrency = $this->safe_currency_code($feeCurrency);
            }
        }
        $feeRate = null; // todo => deduce from $market if $market is defined
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null && $amount !== null) {
            $cost = $price * $amount;
        }
        if ($feeCost !== null) {
            $fee = array(
                'cost' => -$feeCost,
                'currency' => $feeCurrency,
                'rate' => $feeRate,
            );
        }
        $id = $this->safe_string($trade, 'id');
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => null, // Bibox does not have it (documented) yet
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => 'limit',
            'takerOrMaker' => null,
            'side' => $side,
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
            'cmd' => 'deals',
            'pair' => $market['id'],
        );
        if ($limit !== null) {
            $request['size'] = $limit; // default = 200
        }
        $response = $this->publicGetMdata (array_merge($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'cmd' => 'depth',
            'pair' => $market['id'],
        );
        if ($limit !== null) {
            $request['size'] = $limit; // default = 200
        }
        $response = $this->publicGetMdata (array_merge($request, $params));
        return $this->parse_order_book($response['result'], $this->safe_float($response['result'], 'update_time'), 'bids', 'asks', 'price', 'volume');
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "time":1591448220000,
        //         "open":"0.02507029",
        //         "high":"0.02507029",
        //         "low":"0.02506349",
        //         "close":"0.02506349",
        //         "vol":"5.92000000"
        //     }
        //
        return array(
            $this->safe_integer($ohlcv, 'time'),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'vol'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = 1000, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'cmd' => 'kline',
            'pair' => $market['id'],
            'period' => $this->timeframes[$timeframe],
            'size' => $limit,
        );
        $response = $this->publicGetMdata (array_merge($request, $params));
        //
        //     {
        //         "$result":array(
        //             array("time":1591448220000,"open":"0.02507029","high":"0.02507029","low":"0.02506349","close":"0.02506349","vol":"5.92000000"),
        //             array("time":1591448280000,"open":"0.02506449","high":"0.02506975","low":"0.02506108","close":"0.02506843","vol":"5.72000000"),
        //             array("time":1591448340000,"open":"0.02506698","high":"0.02506698","low":"0.02506452","close":"0.02506519","vol":"4.86000000"),
        //         ),
        //         "cmd":"kline",
        //         "ver":"1.1"
        //     }
        //
        $result = $this->safe_value($response, 'result', array());
        return $this->parse_ohlcvs($result, $market, $timeframe, $since, $limit);
    }

    public function fetch_currencies($params = array ()) {
        $method = $this->safe_string($this->options, 'fetchCurrencies', 'fetch_currencies_public');
        return $this->$method ($params);
    }

    public function fetch_currencies_public($params = array ()) {
        $request = array(
            'cmd' => 'currencies',
        );
        $response = $this->publicGetCdata (array_merge($request, $params));
        //
        // publicGetCdata
        //
        //     {
        //         "$result":[
        //             {
        //                 "symbol":"BTC",
        //                 "$name":"BTC",
        //                 "valid_decimals":8,
        //                 "original_decimals":8,
        //                 "is_erc20":0,
        //                 "enable_withdraw":1,
        //                 "enable_deposit":1,
        //                 "withdraw_min":0.005,
        //                 "describe_summary":"[array(\"lang\":\"zh-cn\",\"text\":\"Bitcoin 比特币的概念最初由中本聪在2009年提出，是点对点的基于 SHA-256 算法的一种P2P形式的数字货币，点对点的传输意味着一个去中心化的支付系统。\"),array(\"lang\":\"en-ww\",\"text\":\"Bitcoin is a digital asset and a payment system invented by Satoshi Nakamoto who published a related paper in 2008 and released it as open-source software in 2009. The system featured as peer-to-peer; users can transact directly without an intermediary.\")]"
        //             }
        //         ],
        //         "cmd":"$currencies"
        //     }
        //
        $currencies = $this->safe_value($response, 'result');
        $result = array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'symbol');
            $name = $this->safe_string($currency, 'name'); // contains hieroglyphs causing python ASCII bug
            $code = $this->safe_currency_code($id);
            $precision = $this->safe_integer($currency, 'valid_decimals');
            $deposit = $this->safe_value($currency, 'enable_deposit');
            $withdraw = $this->safe_value($currency, 'enable_withdraw');
            $active = ($deposit && $withdraw);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $name,
                'active' => $active,
                'fee' => null,
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
                        'min' => $this->safe_float($currency, 'withdraw_min'),
                        'max' => null,
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_currencies_private($params = array ()) {
        if (!$this->apiKey || !$this->secret) {
            throw new AuthenticationError($this->id . " fetchCurrencies is an authenticated endpoint, therefore it requires 'apiKey' and 'secret' credentials. If you don't need $currency details, set exchange.has['fetchCurrencies'] = false before calling its methods.");
        }
        $request = array(
            'cmd' => 'transfer/coinList',
            'body' => array(),
        );
        $response = $this->privatePostTransfer (array_merge($request, $params));
        //
        //     {
        //         "$result":[
        //             array(
        //                 "totalBalance":"14.57582269",
        //                 "balance":"14.57582269",
        //                 "freeze":"0.00000000",
        //                 "$id":60,
        //                 "symbol":"USDT",
        //                 "icon_url":"/appimg/USDT_icon.png",
        //                 "describe_url":"[array(\"lang\":\"zh-cn\",\"link\":\"https://bibox.zendesk.com/hc/zh-cn/articles/115004798234\"),array(\"lang\":\"en-ww\",\"link\":\"https://bibox.zendesk.com/hc/en-us/articles/115004798234\")]",
        //                 "$name":"USDT",
        //                 "enable_withdraw":1,
        //                 "enable_deposit":1,
        //                 "enable_transfer":1,
        //                 "confirm_count":2,
        //                 "is_erc20":1,
        //                 "forbid_info":null,
        //                 "describe_summary":"[array(\"lang\":\"zh-cn\",\"text\":\"USDT 是 Tether 公司推出的基于稳定价值货币美元（USD）的代币 Tether USD（简称USDT），1USDT=1美元，用户可以随时使用 USDT 与 USD 进行1:1的兑换。\"),array(\"lang\":\"en-ww\",\"text\":\"USDT is a cryptocurrency asset issued on the Bitcoin blockchain via the Omni Layer Protocol. Each USDT unit is backed by a U.S Dollar held in the reserves of the Tether Limited and can be redeemed through the Tether Platform.\")]",
        //                 "total_amount":4776930644,
        //                 "supply_amount":4642367414,
        //                 "price":"--",
        //                 "contract_father":"OMNI",
        //                 "supply_time":"--",
        //                 "comment":null,
        //                 "contract":"31",
        //                 "original_decimals":8,
        //                 "deposit_type":0,
        //                 "hasCobo":0,
        //                 "BTCValue":"0.00126358",
        //                 "CNYValue":"100.93381445",
        //                 "USDValue":"14.57524654",
        //                 "children":array(
        //                     array("type":"OMNI","symbol":"USDT","enable_deposit":1,"enable_withdraw":1,"confirm_count":2),
        //                     array("type":"TRC20","symbol":"tUSDT","enable_deposit":1,"enable_withdraw":1,"confirm_count":20),
        //                     array("type":"ERC20","symbol":"eUSDT","enable_deposit":1,"enable_withdraw":1,"confirm_count":25)
        //                 )
        //             ),
        //         ],
        //         "cmd":"transfer/coinList"
        //     }
        //
        $currencies = $this->safe_value($response, 'result');
        $result = array();
        for ($i = 0; $i < count($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $this->safe_string($currency, 'symbol');
            $name = $currency['name']; // contains hieroglyphs causing python ASCII bug
            $code = $this->safe_currency_code($id);
            $precision = 8;
            $deposit = $this->safe_value($currency, 'enable_deposit');
            $withdraw = $this->safe_value($currency, 'enable_withdraw');
            $active = ($deposit && $withdraw);
            $result[$code] = array(
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $name,
                'active' => $active,
                'fee' => null,
                'precision' => $precision,
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
                        'min' => null,
                        'max' => pow(10, $precision),
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $type = $this->safe_string($params, 'type', 'assets');
        $params = $this->omit($params, 'type');
        $request = array(
            'cmd' => 'transfer/' . $type, // assets, mainAssets
            'body' => array_merge(array(
                'select' => 1, // return full info
            ), $params),
        );
        $response = $this->privatePostTransfer ($request);
        $balances = $this->safe_value($response, 'result');
        $result = array( 'info' => $balances );
        $indexed = null;
        if (is_array($balances) && array_key_exists('assets_list', $balances)) {
            $indexed = $this->index_by($balances['assets_list'], 'coin_symbol');
        } else {
            $indexed = $balances;
        }
        $keys = is_array($indexed) ? array_keys($indexed) : array();
        for ($i = 0; $i < count($keys); $i++) {
            $id = $keys[$i];
            $code = strtoupper($id);
            if (mb_strpos($code, 'TOTAL_') !== false) {
                $code = mb_substr($code, 6);
            }
            if (is_array($this->currencies_by_id) && array_key_exists($code, $this->currencies_by_id)) {
                $code = $this->currencies_by_id[$code]['code'];
            }
            $account = $this->account();
            $balance = $indexed[$id];
            if (gettype($balance) === 'string') {
                $balance = floatval($balance);
                $account['free'] = $balance;
                $account['used'] = 0.0;
                $account['total'] = $balance;
            } else {
                $account['free'] = $this->safe_float($balance, 'balance');
                $account['used'] = $this->safe_float($balance, 'freeze');
            }
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array(
            'page' => 1,
        );
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['symbol'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['size'] = $limit;
        } else {
            $request['size'] = 100;
        }
        $response = $this->privatePostTransfer (array(
            'cmd' => 'transfer/transferInList',
            'body' => array_merge($request, $params),
        ));
        $deposits = $this->safe_value($response['result'], 'items', array());
        for ($i = 0; $i < count($deposits); $i++) {
            $deposits[$i]['type'] = 'deposit';
        }
        return $this->parse_transactions($deposits, $currency, $since, $limit);
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        $request = array(
            'page' => 1,
        );
        if ($code !== null) {
            $currency = $this->currency($code);
            $request['symbol'] = $currency['id'];
        }
        if ($limit !== null) {
            $request['size'] = $limit;
        } else {
            $request['size'] = 100;
        }
        $response = $this->privatePostTransfer (array(
            'cmd' => 'transfer/transferOutList',
            'body' => array_merge($request, $params),
        ));
        $withdrawals = $this->safe_value($response['result'], 'items', array());
        for ($i = 0; $i < count($withdrawals); $i++) {
            $withdrawals[$i]['type'] = 'withdrawal';
        }
        return $this->parse_transactions($withdrawals, $currency, $since, $limit);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        // fetchDeposits
        //
        //     {
        //         'id' => 1023291,
        //         'coin_symbol' => 'ETH',
        //         'to_address' => '0x7263....',
        //         'amount' => '0.49170000',
        //         'confirmCount' => '16',
        //         'createdAt' => 1553123867000,
        //         'status' => 2
        //     }
        //
        // fetchWithdrawals
        //
        //     {
        //         'id' => 521844,
        //         'coin_symbol' => 'ETH',
        //         'to_address' => '0xfd4e....',
        //         'addr_remark' => '',
        //         'amount' => '0.39452750',
        //         'fee' => '0.00600000',
        //         'createdAt' => 1553226906000,
        //         'memo' => '',
        //         'status' => 3
        //     }
        //
        $id = $this->safe_string($transaction, 'id');
        $address = $this->safe_string($transaction, 'to_address');
        $currencyId = $this->safe_string($transaction, 'coin_symbol');
        $code = $this->safe_currency_code($currencyId, $currency);
        $timestamp = $this->safe_string($transaction, 'createdAt');
        $tag = $this->safe_string($transaction, 'addr_remark');
        $type = $this->safe_string($transaction, 'type');
        $status = $this->parse_transaction_status_by_type($this->safe_string($transaction, 'status'), $type);
        $amount = $this->safe_float($transaction, 'amount');
        $feeCost = $this->safe_float($transaction, 'fee');
        if ($type === 'deposit') {
            $feeCost = 0;
            $tag = null;
        }
        $fee = array(
            'cost' => $feeCost,
            'currency' => $code,
        );
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'address' => $address,
            'tag' => $tag,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => null,
            'fee' => $fee,
        );
    }

    public function parse_transaction_status_by_type($status, $type = null) {
        $statuses = array(
            'deposit' => array(
                '1' => 'pending',
                '2' => 'ok',
            ),
            'withdrawal' => array(
                '0' => 'pending',
                '3' => 'ok',
            ),
        );
        return $this->safe_string($this->safe_value($statuses, $type, array()), $status, $status);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $orderType = ($type === 'limit') ? 2 : 1;
        $orderSide = ($side === 'buy') ? 1 : 2;
        $request = array(
            'cmd' => 'orderpending/trade',
            'body' => array_merge(array(
                'pair' => $market['id'],
                'account_type' => 0,
                'order_type' => $orderType,
                'order_side' => $orderSide,
                'pay_bix' => 0,
                'amount' => $amount,
                'price' => $price,
            ), $params),
        );
        $response = $this->privatePostOrderpending ($request);
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'result'),
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $request = array(
            'cmd' => 'orderpending/cancelTrade',
            'body' => array_merge(array(
                'orders_id' => $id,
            ), $params),
        );
        $response = $this->privatePostOrderpending ($request);
        return $response;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'cmd' => 'orderpending/order',
            'body' => array_merge(array(
                'id' => (string) $id,
                'account_type' => 0, // 0 = spot account
            ), $params),
        );
        $response = $this->privatePostOrderpending ($request);
        $order = $this->safe_value($response, 'result');
        if ($this->is_empty($order)) {
            throw new OrderNotFound($this->id . ' $order ' . $id . ' not found');
        }
        return $this->parse_order($order);
    }

    public function parse_order($order, $market = null) {
        $symbol = null;
        if ($market === null) {
            $marketId = null;
            $baseId = $this->safe_string($order, 'coin_symbol');
            $quoteId = $this->safe_string($order, 'currency_symbol');
            if (($baseId !== null) && ($quoteId !== null)) {
                $marketId = $baseId . '_' . $quoteId;
            }
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $rawType = $this->safe_string($order, 'order_type');
        $type = ($rawType === '1') ? 'market' : 'limit';
        $timestamp = $this->safe_integer($order, 'createdAt');
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'deal_price');
        $filled = $this->safe_float($order, 'deal_amount');
        $amount = $this->safe_float($order, 'amount');
        $cost = $this->safe_float_2($order, 'deal_money', 'money');
        $rawSide = $this->safe_string($order, 'order_side');
        $side = ($rawSide === '1') ? 'buy' : 'sell';
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $id = $this->safe_string($order, 'id');
        $feeCost = $this->safe_float($order, 'fee');
        $fee = null;
        if ($feeCost !== null) {
            $fee = array(
                'cost' => $feeCost,
                'currency' => null,
            );
        }
        return $this->safe_order(array(
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
            'remaining' => null,
            'status' => $status,
            'fee' => $fee,
            'trades' => null,
        ));
    }

    public function parse_order_status($status) {
        $statuses = array(
            // original comments from bibox:
            '1' => 'open', // pending
            '2' => 'open', // part completed
            '3' => 'closed', // completed
            '4' => 'canceled', // part canceled
            '5' => 'canceled', // canceled
            '6' => 'canceled', // canceling
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $pair = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $pair = $market['id'];
        }
        $size = $limit ? $limit : 200;
        $request = array(
            'cmd' => 'orderpending/orderPendingList',
            'body' => array_merge(array(
                'pair' => $pair,
                'account_type' => 0, // 0 - regular, 1 - margin
                'page' => 1,
                'size' => $size,
            ), $params),
        );
        $response = $this->privatePostOrderpending ($request);
        $orders = $this->safe_value($response['result'], 'items', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = 200, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchClosedOrders() requires a `$symbol` argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'cmd' => 'orderpending/pendingHistoryList',
            'body' => array_merge(array(
                'pair' => $market['id'],
                'account_type' => 0, // 0 - regular, 1 - margin
                'page' => 1,
                'size' => $limit,
            ), $params),
        );
        $response = $this->privatePostOrderpending ($request);
        $orders = $this->safe_value($response['result'], 'items', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a `$symbol` argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $size = $limit ? $limit : 200;
        $request = array(
            'cmd' => 'orderpending/orderHistoryList',
            'body' => array_merge(array(
                'pair' => $market['id'],
                'account_type' => 0, // 0 - regular, 1 - margin
                'page' => 1,
                'size' => $size,
                'coin_symbol' => $market['baseId'],
                'currency_symbol' => $market['quoteId'],
            ), $params),
        );
        $response = $this->privatePostOrderpending ($request);
        $trades = $this->safe_value($response['result'], 'items', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'cmd' => 'transfer/transferIn',
            'body' => array_merge(array(
                'coin_symbol' => $currency['id'],
            ), $params),
        );
        $response = $this->privatePostTransfer ($request);
        //
        //     {
        //         "$result":"3Jx6RZ9TNMsAoy9NUzBwZf68QBppDruSKW","cmd":"transfer/transferIn"
        //     }
        //
        //     {
        //         "$result":"array(\"account\":\"PERSONALLY OMITTED\",\"memo\":\"PERSONALLY OMITTED\")","cmd":"transfer/transferIn"
        //     }
        //
        $result = $this->safe_string($response, 'result');
        $address = $result;
        $tag = null;
        if ($this->is_json_encoded_object($result)) {
            $parsed = json_decode($result, $as_associative_array = true);
            $address = $this->safe_string($parsed, 'account');
            $tag = $this->safe_string($parsed, 'memo');
        }
        return array(
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency($code);
        if ($this->password === null) {
            if (!(is_array($params) && array_key_exists('trade_pwd', $params))) {
                throw new ExchangeError($this->id . ' withdraw() requires $this->password set on the exchange instance or a trade_pwd parameter');
            }
        }
        if (!(is_array($params) && array_key_exists('totp_code', $params))) {
            throw new ExchangeError($this->id . ' withdraw() requires a totp_code parameter for 2FA authentication');
        }
        $request = array(
            'trade_pwd' => $this->password,
            'coin_symbol' => $currency['id'],
            'amount' => $amount,
            'addr' => $address,
        );
        if ($tag !== null) {
            $request['address_remark'] = $tag;
        }
        $response = $this->privatePostTransfer (array(
            'cmd' => 'transfer/transferOut',
            'body' => array_merge($request, $params),
        ));
        return array(
            'info' => $response,
            'id' => null,
        );
    }

    public function fetch_funding_fees($codes = null, $params = array ()) {
        // by default it will try load withdrawal fees of all currencies (with separate requests)
        // however if you define $codes = array( 'ETH', 'BTC' ) in args it will only load those
        $this->load_markets();
        $withdrawFees = array();
        $info = array();
        if ($codes === null) {
            $codes = is_array($this->currencies) ? array_keys($this->currencies) : array();
        }
        for ($i = 0; $i < count($codes); $i++) {
            $code = $codes[$i];
            $currency = $this->currency($code);
            $request = array(
                'cmd' => 'transfer/coinConfig',
                'body' => array_merge(array(
                    'coin_symbol' => $currency['id'],
                ), $params),
            );
            $response = $this->privatePostTransfer ($request);
            $info[$code] = $response;
            $withdrawFees[$code] = $this->safe_float($response['result'], 'withdraw_fee');
        }
        return array(
            'info' => $info,
            'withdraw' => $withdrawFees,
            'deposit' => array(),
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->implode_params($this->urls['api'], array( 'hostname' => $this->hostname )) . '/' . $this->version . '/' . $path;
        $cmds = $this->json(array( $params ));
        if ($api === 'public') {
            if ($method !== 'GET') {
                $body = array( 'cmds' => $cmds );
            } else if ($params) {
                $url .= '?' . $this->urlencode($params);
            }
        } else if ($api === 'v2private') {
            $this->check_required_credentials();
            $url = $this->implode_params($this->urls['api'], array( 'hostname' => $this->hostname )) . '/v2/' . $path;
            $json_params = $this->json($params);
            $body = array(
                'body' => $json_params,
                'apikey' => $this->apiKey,
                'sign' => $this->hmac($this->encode($json_params), $this->encode($this->secret), 'md5'),
            );
        } else {
            $this->check_required_credentials();
            $body = array(
                'cmds' => $cmds,
                'apikey' => $this->apiKey,
                'sign' => $this->hmac($this->encode($cmds), $this->encode($this->secret), 'md5'),
            );
        }
        if ($body !== null) {
            $body = $this->json($body, array( 'convertArraysToObjects' => true ));
        }
        $headers = array( 'Content-Type' => 'application/json' );
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        if (is_array($response) && array_key_exists('error', $response)) {
            if (is_array($response['error']) && array_key_exists('code', $response['error'])) {
                $code = $this->safe_string($response['error'], 'code');
                $feedback = $this->id . ' ' . $body;
                $this->throw_exactly_matched_exception($this->exceptions, $code, $feedback);
                throw new ExchangeError($feedback);
            }
            throw new ExchangeError($this->id . ' ' . $body);
        }
        if (!(is_array($response) && array_key_exists('result', $response))) {
            throw new ExchangeError($this->id . ' ' . $body);
        }
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if ($method === 'GET') {
            return $response;
        } else {
            return $response['result'][0];
        }
    }
}

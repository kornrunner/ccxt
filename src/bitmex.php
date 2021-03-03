<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\BadRequest;
use \ccxt\OrderNotFound;
use \ccxt\DDoSProtection;

class bitmex extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitmex',
            'name' => 'BitMEX',
            'countries' => array( 'SC' ), // Seychelles
            'version' => 'v1',
            'userAgent' => null,
            'rateLimit' => 2000,
            'pro' => true,
            'has' => array(
                'cancelAllOrders' => true,
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'editOrder' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => 'emulated',
                'withdraw' => true,
            ),
            'timeframes' => array(
                '1m' => '1m',
                '5m' => '5m',
                '1h' => '1h',
                '1d' => '1d',
            ),
            'urls' => array(
                'test' => array(
                    'public' => 'https://testnet.bitmex.com',
                    'private' => 'https://testnet.bitmex.com',
                ),
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766319-f653c6e6-5ed4-11e7-933d-f0bc3699ae8f.jpg',
                'api' => array(
                    'public' => 'https://www.bitmex.com',
                    'private' => 'https://www.bitmex.com',
                ),
                'www' => 'https://www.bitmex.com',
                'doc' => array(
                    'https://www.bitmex.com/app/apiOverview',
                    'https://github.com/BitMEX/api-connectors/tree/master/official-http',
                ),
                'fees' => 'https://www.bitmex.com/app/fees',
                'referral' => 'https://www.bitmex.com/register/upZpOX',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'announcement',
                        'announcement/urgent',
                        'funding',
                        'instrument',
                        'instrument/active',
                        'instrument/activeAndIndices',
                        'instrument/activeIntervals',
                        'instrument/compositeIndex',
                        'instrument/indices',
                        'insurance',
                        'leaderboard',
                        'liquidation',
                        'orderBook',
                        'orderBook/L2',
                        'quote',
                        'quote/bucketed',
                        'schema',
                        'schema/websocketHelp',
                        'settlement',
                        'stats',
                        'stats/history',
                        'trade',
                        'trade/bucketed',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'apiKey',
                        'chat',
                        'chat/channels',
                        'chat/connected',
                        'execution',
                        'execution/tradeHistory',
                        'notification',
                        'order',
                        'position',
                        'user',
                        'user/affiliateStatus',
                        'user/checkReferralCode',
                        'user/commission',
                        'user/depositAddress',
                        'user/executionHistory',
                        'user/margin',
                        'user/minWithdrawalFee',
                        'user/wallet',
                        'user/walletHistory',
                        'user/walletSummary',
                    ),
                    'post' => array(
                        'apiKey',
                        'apiKey/disable',
                        'apiKey/enable',
                        'chat',
                        'order',
                        'order/bulk',
                        'order/cancelAllAfter',
                        'order/closePosition',
                        'position/isolate',
                        'position/leverage',
                        'position/riskLimit',
                        'position/transferMargin',
                        'user/cancelWithdrawal',
                        'user/confirmEmail',
                        'user/confirmEnableTFA',
                        'user/confirmWithdrawal',
                        'user/disableTFA',
                        'user/logout',
                        'user/logoutAll',
                        'user/preferences',
                        'user/requestEnableTFA',
                        'user/requestWithdrawal',
                    ),
                    'put' => array(
                        'order',
                        'order/bulk',
                        'user',
                    ),
                    'delete' => array(
                        'apiKey',
                        'order',
                        'order/all',
                    ),
                ),
            ),
            'exceptions' => array(
                'exact' => array(
                    'Invalid API Key.' => '\\ccxt\\AuthenticationError',
                    'This key is disabled.' => '\\ccxt\\PermissionDenied',
                    'Access Denied' => '\\ccxt\\PermissionDenied',
                    'Duplicate clOrdID' => '\\ccxt\\InvalidOrder',
                    'orderQty is invalid' => '\\ccxt\\InvalidOrder',
                    'Invalid price' => '\\ccxt\\InvalidOrder',
                    'Invalid stopPx for ordType' => '\\ccxt\\InvalidOrder',
                ),
                'broad' => array(
                    'Signature not valid' => '\\ccxt\\AuthenticationError',
                    'overloaded' => '\\ccxt\\ExchangeNotAvailable',
                    'Account has insufficient Available Balance' => '\\ccxt\\InsufficientFunds',
                    'Service unavailable' => '\\ccxt\\ExchangeNotAvailable', // array("error":array("message":"Service unavailable","name":"HTTPError"))
                    'Server Error' => '\\ccxt\\ExchangeError', // array("error":array("message":"Server Error","name":"HTTPError"))
                    'Unable to cancel order due to existing state' => '\\ccxt\\InvalidOrder',
                ),
            ),
            'precisionMode' => TICK_SIZE,
            'options' => array(
                // https://blog.bitmex.com/api_announcement/deprecation-of-api-nonce-header/
                // https://github.com/ccxt/ccxt/issues/4789
                'api-expires' => 5, // in seconds
                'fetchOHLCVOpenTimestamp' => true,
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->publicGetInstrumentActiveAndIndices ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $market = $response[$i];
            $active = ($market['state'] !== 'Unlisted');
            $id = $market['symbol'];
            $baseId = $market['underlying'];
            $quoteId = $market['quoteCurrency'];
            $basequote = $baseId . $quoteId;
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $swap = ($id === $basequote);
            // 'positionCurrency' may be empty ("", as Bitmex currently returns for ETHUSD)
            // so let's take the $quote currency first and then adjust if needed
            $positionId = $this->safe_string_2($market, 'positionCurrency', 'quoteCurrency');
            $type = null;
            $future = false;
            $prediction = false;
            $position = $this->safe_currency_code($positionId);
            $symbol = $id;
            if ($swap) {
                $type = 'swap';
                $symbol = $base . '/' . $quote;
            } else if (mb_strpos($id, 'B_') !== false) {
                $prediction = true;
                $type = 'prediction';
            } else {
                $future = true;
                $type = 'future';
            }
            $precision = array(
                'amount' => null,
                'price' => null,
            );
            $lotSize = $this->safe_float($market, 'lotSize');
            $tickSize = $this->safe_float($market, 'tickSize');
            if ($lotSize !== null) {
                $precision['amount'] = $lotSize;
            }
            if ($tickSize !== null) {
                $precision['price'] = $tickSize;
            }
            $limits = array(
                'amount' => array(
                    'min' => null,
                    'max' => null,
                ),
                'price' => array(
                    'min' => $tickSize,
                    'max' => $this->safe_float($market, 'maxPrice'),
                ),
                'cost' => array(
                    'min' => null,
                    'max' => null,
                ),
            );
            $limitField = ($position === $quote) ? 'cost' : 'amount';
            $limits[$limitField] = array(
                'min' => $lotSize,
                'max' => $this->safe_float($market, 'maxOrderQty'),
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
                'limits' => $limits,
                'taker' => $this->safe_float($market, 'takerFee'),
                'maker' => $this->safe_float($market, 'makerFee'),
                'type' => $type,
                'spot' => false,
                'swap' => $swap,
                'future' => $future,
                'prediction' => $prediction,
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_balance_response($response) {
        //
        //     array(
        //         {
        //             "$account":1455728,
        //             "currency":"XBt",
        //             "riskLimit":1000000000000,
        //             "prevState":"",
        //             "state":"",
        //             "action":"",
        //             "amount":263542,
        //             "pendingCredit":0,
        //             "pendingDebit":0,
        //             "confirmedDebit":0,
        //             "prevRealisedPnl":0,
        //             "prevUnrealisedPnl":0,
        //             "grossComm":0,
        //             "grossOpenCost":0,
        //             "grossOpenPremium":0,
        //             "grossExecCost":0,
        //             "grossMarkValue":0,
        //             "riskValue":0,
        //             "taxableMargin":0,
        //             "initMargin":0,
        //             "maintMargin":0,
        //             "sessionMargin":0,
        //             "targetExcessMargin":0,
        //             "varMargin":0,
        //             "realisedPnl":0,
        //             "unrealisedPnl":0,
        //             "indicativeTax":0,
        //             "unrealisedProfit":0,
        //             "syntheticMargin":null,
        //             "walletBalance":263542,
        //             "marginBalance":263542,
        //             "marginBalancePcnt":1,
        //             "marginLeverage":0,
        //             "marginUsedPcnt":0,
        //             "excessMargin":263542,
        //             "excessMarginPcnt":1,
        //             "availableMargin":263542,
        //             "withdrawableMargin":263542,
        //             "timestamp":"2020-08-03T12:01:01.246Z",
        //             "grossLastValue":0,
        //             "commission":null
        //         }
        //     )
        //
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account();
            $free = $this->safe_float($balance, 'availableMargin');
            $total = $this->safe_float($balance, 'marginBalance');
            if ($code === 'BTC') {
                if ($free !== null) {
                    $free /= 100000000;
                }
                if ($total !== null) {
                    $total /= 100000000;
                }
            }
            $account['free'] = $free;
            $account['total'] = $total;
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $request = array(
            'currency' => 'all',
        );
        $response = $this->privateGetUserMargin (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "account":1455728,
        //             "currency":"XBt",
        //             "riskLimit":1000000000000,
        //             "prevState":"",
        //             "state":"",
        //             "action":"",
        //             "amount":263542,
        //             "pendingCredit":0,
        //             "pendingDebit":0,
        //             "confirmedDebit":0,
        //             "prevRealisedPnl":0,
        //             "prevUnrealisedPnl":0,
        //             "grossComm":0,
        //             "grossOpenCost":0,
        //             "grossOpenPremium":0,
        //             "grossExecCost":0,
        //             "grossMarkValue":0,
        //             "riskValue":0,
        //             "taxableMargin":0,
        //             "initMargin":0,
        //             "maintMargin":0,
        //             "sessionMargin":0,
        //             "targetExcessMargin":0,
        //             "varMargin":0,
        //             "realisedPnl":0,
        //             "unrealisedPnl":0,
        //             "indicativeTax":0,
        //             "unrealisedProfit":0,
        //             "syntheticMargin":null,
        //             "walletBalance":263542,
        //             "marginBalance":263542,
        //             "marginBalancePcnt":1,
        //             "marginLeverage":0,
        //             "marginUsedPcnt":0,
        //             "excessMargin":263542,
        //             "excessMarginPcnt":1,
        //             "availableMargin":263542,
        //             "withdrawableMargin":263542,
        //             "timestamp":"2020-08-03T12:01:01.246Z",
        //             "grossLastValue":0,
        //             "commission":null
        //         }
        //     )
        //
        return $this->parse_balance_response($response);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['depth'] = $limit;
        }
        $response = $this->publicGetOrderBookL2 (array_merge($request, $params));
        $result = array(
            'bids' => array(),
            'asks' => array(),
            'timestamp' => null,
            'datetime' => null,
            'nonce' => null,
        );
        for ($i = 0; $i < count($response); $i++) {
            $order = $response[$i];
            $side = ($order['side'] === 'Sell') ? 'asks' : 'bids';
            $amount = $this->safe_float($order, 'size');
            $price = $this->safe_float($order, 'price');
            // https://github.com/ccxt/ccxt/issues/4926
            // https://github.com/ccxt/ccxt/issues/4927
            // the exchange sometimes returns null $price in the orderbook
            if ($price !== null) {
                $result[$side][] = array( $price, $amount );
            }
        }
        $result['bids'] = $this->sort_by($result['bids'], 0, true);
        $result['asks'] = $this->sort_by($result['asks'], 0);
        return $result;
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $filter = array(
            'filter' => array(
                'orderID' => $id,
            ),
        );
        $response = $this->fetch_orders($symbol, null, null, $this->deep_extend($filter, $params));
        $numResults = is_array($response) ? count($response) : 0;
        if ($numResults === 1) {
            return $response[0];
        }
        throw new OrderNotFound($this->id . ' => The order ' . $id . ' not found.');
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['startTime'] = $this->iso8601($since);
        }
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $request = $this->deep_extend($request, $params);
        // why the hassle? urlencode in python is kinda broken for nested dicts.
        // E.g. self.urlencode(array("filter" => array("open" => True))) will return "filter=array('open':+True)"
        // Bitmex doesn't like that. Hence resorting to this hack.
        if (is_array($request) && array_key_exists('filter', $request)) {
            $request['filter'] = $this->json($request['filter']);
        }
        $response = $this->privateGetOrder ($request);
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'filter' => array(
                'open' => true,
            ),
        );
        return $this->fetch_orders($symbol, $since, $limit, $this->deep_extend($request, $params));
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        // Bitmex barfs if you set 'open' => false in the filter...
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        return $this->filter_by($orders, 'status', 'closed');
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array();
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        if ($since !== null) {
            $request['startTime'] = $this->iso8601($since);
        }
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $request = $this->deep_extend($request, $params);
        // why the hassle? urlencode in python is kinda broken for nested dicts.
        // E.g. self.urlencode(array("filter" => array("open" => True))) will return "filter=array('open':+True)"
        // Bitmex doesn't like that. Hence resorting to this hack.
        if (is_array($request) && array_key_exists('filter', $request)) {
            $request['filter'] = $this->json($request['filter']);
        }
        $response = $this->privateGetExecutionTradeHistory ($request);
        //
        //     array(
        //         {
        //             "execID" => "string",
        //             "orderID" => "string",
        //             "clOrdID" => "string",
        //             "clOrdLinkID" => "string",
        //             "account" => 0,
        //             "$symbol" => "string",
        //             "side" => "string",
        //             "lastQty" => 0,
        //             "lastPx" => 0,
        //             "underlyingLastPx" => 0,
        //             "lastMkt" => "string",
        //             "lastLiquidityInd" => "string",
        //             "simpleOrderQty" => 0,
        //             "orderQty" => 0,
        //             "price" => 0,
        //             "displayQty" => 0,
        //             "stopPx" => 0,
        //             "pegOffsetValue" => 0,
        //             "pegPriceType" => "string",
        //             "currency" => "string",
        //             "settlCurrency" => "string",
        //             "execType" => "string",
        //             "ordType" => "string",
        //             "timeInForce" => "string",
        //             "execInst" => "string",
        //             "contingencyType" => "string",
        //             "exDestination" => "string",
        //             "ordStatus" => "string",
        //             "triggered" => "string",
        //             "workingIndicator" => true,
        //             "ordRejReason" => "string",
        //             "simpleLeavesQty" => 0,
        //             "leavesQty" => 0,
        //             "simpleCumQty" => 0,
        //             "cumQty" => 0,
        //             "avgPx" => 0,
        //             "commission" => 0,
        //             "tradePublishIndicator" => "string",
        //             "multiLegReportingType" => "string",
        //             "text" => "string",
        //             "trdMatchID" => "string",
        //             "execCost" => 0,
        //             "execComm" => 0,
        //             "homeNotional" => 0,
        //             "foreignNotional" => 0,
        //             "transactTime" => "2019-03-05T12:47:02.762Z",
        //             "timestamp" => "2019-03-05T12:47:02.762Z"
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'Withdrawal' => 'transaction',
            'RealisedPNL' => 'margin',
            'UnrealisedPNL' => 'margin',
            'Deposit' => 'transaction',
            'Transfer' => 'transfer',
            'AffiliatePayout' => 'referral',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        //     {
        //         transactID => "69573da3-7744-5467-3207-89fd6efe7a47",
        //         $account =>  24321,
        //         $currency => "XBt",
        //         transactType => "Withdrawal", // "AffiliatePayout", "Transfer", "Deposit", "RealisedPNL", ...
        //         $amount =>  -1000000,
        //         $fee =>  300000,
        //         transactStatus => "Completed", // "Canceled", ...
        //         address => "1Ex4fkF4NhQaQdRWNoYpqiPbDBbq18Kdd9",
        //         tx => "3BMEX91ZhhKoWtsH9QRb5dNXnmnGpiEetA",
        //         text => "",
        //         transactTime => "2017-03-21T20:05:14.388Z",
        //         walletBalance =>  0, // balance $after
        //         marginBalance =>  null,
        //         $timestamp => "2017-03-22T13:09:23.514Z"
        //     }
        //
        // ButMEX returns the unrealized pnl from the wallet history endpoint.
        // The unrealized pnl transaction has an empty $timestamp->
        // It is not related to historical pnl it has $status set to "Pending".
        // Therefore it's not a part of the history at all.
        // https://github.com/ccxt/ccxt/issues/6047
        //
        //     {
        //         "transactID":"00000000-0000-0000-0000-000000000000",
        //         "$account":121210,
        //         "$currency":"XBt",
        //         "transactType":"UnrealisedPNL",
        //         "$amount":-5508,
        //         "$fee":0,
        //         "transactStatus":"Pending",
        //         "address":"XBTUSD",
        //         "tx":"",
        //         "text":"",
        //         "transactTime":null,  # ←---------------------------- null
        //         "walletBalance":139198767,
        //         "marginBalance":139193259,
        //         "$timestamp":null  # ←---------------------------- null
        //     }
        //
        $id = $this->safe_string($item, 'transactID');
        $account = $this->safe_string($item, 'account');
        $referenceId = $this->safe_string($item, 'tx');
        $referenceAccount = null;
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'transactType'));
        $currencyId = $this->safe_string($item, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $amount = $this->safe_float($item, 'amount');
        if ($amount !== null) {
            $amount = $amount / 100000000;
        }
        $timestamp = $this->parse8601($this->safe_string($item, 'transactTime'));
        if ($timestamp === null) {
            // https://github.com/ccxt/ccxt/issues/6047
            // set the $timestamp to zero, 1970 Jan 1 00:00:00
            // for unrealized pnl and other transactions without a $timestamp
            $timestamp = 0; // see comments above
        }
        $feeCost = $this->safe_float($item, 'fee', 0);
        if ($feeCost !== null) {
            $feeCost = $feeCost / 100000000;
        }
        $fee = array(
            'cost' => $feeCost,
            'currency' => $code,
        );
        $after = $this->safe_float($item, 'walletBalance');
        if ($after !== null) {
            $after = $after / 100000000;
        }
        $before = $this->sum($after, -$amount);
        $direction = null;
        if ($amount < 0) {
            $direction = 'out';
            $amount = abs($amount);
        } else {
            $direction = 'in';
        }
        $status = $this->parse_transaction_status($this->safe_string($item, 'transactStatus'));
        return array(
            'id' => $id,
            'info' => $item,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'direction' => $direction,
            'account' => $account,
            'referenceId' => $referenceId,
            'referenceAccount' => $referenceAccount,
            'type' => $type,
            'currency' => $code,
            'amount' => $amount,
            'before' => $before,
            'after' => $after,
            'status' => $status,
            'fee' => $fee,
        );
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        $request = array(
            // 'start' => 123,
        );
        //
        //     if ($since !== null) {
        //         // date-based pagination not supported
        //     }
        //
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->privateGetUserWalletHistory (array_merge($request, $params));
        //
        //     array(
        //         {
        //             transactID => "69573da3-7744-5467-3207-89fd6efe7a47",
        //             account =>  24321,
        //             $currency => "XBt",
        //             transactType => "Withdrawal", // "AffiliatePayout", "Transfer", "Deposit", "RealisedPNL", ...
        //             amount =>  -1000000,
        //             fee =>  300000,
        //             transactStatus => "Completed", // "Canceled", ...
        //             address => "1Ex4fkF4NhQaQdRWNoYpqiPbDBbq18Kdd9",
        //             tx => "3BMEX91ZhhKoWtsH9QRb5dNXnmnGpiEetA",
        //             text => "",
        //             transactTime => "2017-03-21T20:05:14.388Z",
        //             walletBalance =>  0, // balance after
        //             marginBalance =>  null,
        //             timestamp => "2017-03-22T13:09:23.514Z"
        //         }
        //     )
        //
        return $this->parse_ledger($response, $currency, $since, $limit);
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            // 'start' => 123,
        );
        //
        //     if ($since !== null) {
        //         // date-based pagination not supported
        //     }
        //
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->privateGetUserWalletHistory (array_merge($request, $params));
        $transactions = $this->filter_by_array($response, 'transactType', array( 'Withdrawal', 'Deposit' ), false);
        $currency = null;
        if ($code !== null) {
            $currency = $this->currency($code);
        }
        return $this->parse_transactions($transactions, $currency, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'Canceled' => 'canceled',
            'Completed' => 'ok',
            'Pending' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //   {
        //      'transactID' => 'ffe699c2-95ee-4c13-91f9-0faf41daec25',
        //      'account' => 123456,
        //      'currency' => 'XBt',
        //      'transactType' => 'Withdrawal',
        //      'amount' => -100100000,
        //      'fee' => 100000,
        //      'transactStatus' => 'Completed',
        //      'address' => '385cR5DM96n1HvBDMzLHPYcw89fZAXULJP',
        //      'tx' => '3BMEXabcdefghijklmnopqrstuvwxyz123',
        //      'text' => '',
        //      'transactTime' => '2019-01-02T01:00:00.000Z',
        //      'walletBalance' => 99900000,
        //      'marginBalance' => None,
        //      'timestamp' => '2019-01-02T13:00:00.000Z'
        //   }
        //
        $id = $this->safe_string($transaction, 'transactID');
        // For deposits, $transactTime == $timestamp
        // For withdrawals, $transactTime is submission, $timestamp is processed
        $transactTime = $this->parse8601($this->safe_string($transaction, 'transactTime'));
        $timestamp = $this->parse8601($this->safe_string($transaction, 'timestamp'));
        $type = $this->safe_string_lower($transaction, 'transactType');
        // Deposits have no from $address or to $address, withdrawals have both
        $address = null;
        $addressFrom = null;
        $addressTo = null;
        if ($type === 'withdrawal') {
            $address = $this->safe_string($transaction, 'address');
            $addressFrom = $this->safe_string($transaction, 'tx');
            $addressTo = $address;
        }
        $amount = $this->safe_integer($transaction, 'amount');
        if ($amount !== null) {
            $amount = abs($amount) / 10000000;
        }
        $feeCost = $this->safe_integer($transaction, 'fee');
        if ($feeCost !== null) {
            $feeCost = $feeCost / 10000000;
        }
        $fee = array(
            'cost' => $feeCost,
            'currency' => 'BTC',
        );
        $status = $this->safe_string($transaction, 'transactStatus');
        if ($status !== null) {
            $status = $this->parse_transaction_status($status);
        }
        return array(
            'info' => $transaction,
            'id' => $id,
            'txid' => null,
            'timestamp' => $transactTime,
            'datetime' => $this->iso8601($transactTime),
            'addressFrom' => $addressFrom,
            'address' => $address,
            'addressTo' => $addressTo,
            'tagFrom' => null,
            'tag' => null,
            'tagTo' => null,
            'type' => $type,
            'amount' => $amount,
            // BTC is the only $currency on Bitmex
            'currency' => 'BTC',
            'status' => $status,
            'updated' => $timestamp,
            'comment' => null,
            'fee' => $fee,
        );
    }

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        if (!$market['active']) {
            throw new ExchangeError($this->id . ' => $symbol ' . $symbol . ' is delisted');
        }
        $tickers = $this->fetch_tickers(array( $symbol ), $params);
        $ticker = $this->safe_value($tickers, $symbol);
        if ($ticker === null) {
            throw new ExchangeError($this->id . ' $ticker $symbol ' . $symbol . ' not found');
        }
        return $ticker;
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetInstrumentActiveAndIndices ($params);
        $result = array();
        for ($i = 0; $i < count($response); $i++) {
            $ticker = $this->parse_ticker($response[$i]);
            $symbol = $this->safe_string($ticker, 'symbol');
            if ($symbol !== null) {
                $result[$symbol] = $ticker;
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function parse_ticker($ticker, $market = null) {
        //
        //     {                         $symbol => "ETHH19",
        //                           rootSymbol => "ETH",
        //                                state => "Open",
        //                                  typ => "FFCCSX",
        //                              listing => "2018-12-17T04:00:00.000Z",
        //                                front => "2019-02-22T12:00:00.000Z",
        //                               expiry => "2019-03-29T12:00:00.000Z",
        //                               settle => "2019-03-29T12:00:00.000Z",
        //                       relistInterval =>  null,
        //                           inverseLeg => "",
        //                              sellLeg => "",
        //                               buyLeg => "",
        //                     optionStrikePcnt =>  null,
        //                    optionStrikeRound =>  null,
        //                    optionStrikePrice =>  null,
        //                     optionMultiplier =>  null,
        //                     positionCurrency => "ETH",
        //                           underlying => "ETH",
        //                        quoteCurrency => "XBT",
        //                     underlyingSymbol => "ETHXBT=",
        //                            reference => "BMEX",
        //                      referenceSymbol => ".BETHXBT30M",
        //                         calcInterval =>  null,
        //                      publishInterval =>  null,
        //                          publishTime =>  null,
        //                          maxOrderQty =>  100000000,
        //                             maxPrice =>  10,
        //                              lotSize =>  1,
        //                             tickSize =>  0.00001,
        //                           multiplier =>  100000000,
        //                        settlCurrency => "XBt",
        //       underlyingToPositionMultiplier =>  1,
        //         underlyingToSettleMultiplier =>  null,
        //              quoteToSettleMultiplier =>  100000000,
        //                             isQuanto =>  false,
        //                            isInverse =>  false,
        //                           initMargin =>  0.02,
        //                          maintMargin =>  0.01,
        //                            riskLimit =>  5000000000,
        //                             riskStep =>  5000000000,
        //                                limit =>  null,
        //                               capped =>  false,
        //                                taxed =>  true,
        //                           deleverage =>  true,
        //                             makerFee =>  -0.0005,
        //                             takerFee =>  0.0025,
        //                        settlementFee =>  0,
        //                         insuranceFee =>  0,
        //                    fundingBaseSymbol => "",
        //                   fundingQuoteSymbol => "",
        //                 fundingPremiumSymbol => "",
        //                     fundingTimestamp =>  null,
        //                      fundingInterval =>  null,
        //                          fundingRate =>  null,
        //                indicativeFundingRate =>  null,
        //                   rebalanceTimestamp =>  null,
        //                    rebalanceInterval =>  null,
        //                     openingTimestamp => "2019-02-13T08:00:00.000Z",
        //                     closingTimestamp => "2019-02-13T09:00:00.000Z",
        //                      sessionInterval => "2000-01-01T01:00:00.000Z",
        //                       prevClosePrice =>  0.03347,
        //                       limitDownPrice =>  null,
        //                         limitUpPrice =>  null,
        //               bankruptLimitDownPrice =>  null,
        //                 bankruptLimitUpPrice =>  null,
        //                      prevTotalVolume =>  1386531,
        //                          totalVolume =>  1387062,
        //                               volume =>  531,
        //                            volume24h =>  17118,
        //                    prevTotalTurnover =>  4741294246000,
        //                        totalTurnover =>  4743103466000,
        //                             turnover =>  1809220000,
        //                          turnover24h =>  57919845000,
        //                      homeNotional24h =>  17118,
        //                   foreignNotional24h =>  579.19845,
        //                         prevPrice24h =>  0.03349,
        //                                 vwap =>  0.03383564,
        //                            highPrice =>  0.03458,
        //                             lowPrice =>  0.03329,
        //                            lastPrice =>  0.03406,
        //                   lastPriceProtected =>  0.03406,
        //                    lastTickDirection => "ZeroMinusTick",
        //                       lastChangePcnt =>  0.017,
        //                             bidPrice =>  0.03406,
        //                             midPrice =>  0.034065,
        //                             askPrice =>  0.03407,
        //                       impactBidPrice =>  0.03406,
        //                       impactMidPrice =>  0.034065,
        //                       impactAskPrice =>  0.03407,
        //                         hasLiquidity =>  true,
        //                         openInterest =>  83679,
        //                            openValue =>  285010674000,
        //                           fairMethod => "ImpactMidPrice",
        //                        fairBasisRate =>  0,
        //                            fairBasis =>  0,
        //                            fairPrice =>  0.03406,
        //                           markMethod => "FairPrice",
        //                            markPrice =>  0.03406,
        //                    indicativeTaxRate =>  0,
        //                indicativeSettlePrice =>  0.03406,
        //                optionUnderlyingPrice =>  null,
        //                         settledPrice =>  null,
        //                            $timestamp => "2019-02-13T08:40:30.000Z",
        //     }
        //
        $symbol = null;
        $marketId = $this->safe_string($ticker, 'symbol');
        $market = $this->safe_value($this->markets_by_id, $marketId, $market);
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $timestamp = $this->parse8601($this->safe_string($ticker, 'timestamp'));
        $open = $this->safe_float($ticker, 'prevPrice24h');
        $last = $this->safe_float($ticker, 'lastPrice');
        $change = null;
        $percentage = null;
        if ($last !== null && $open !== null) {
            $change = $last - $open;
            if ($open > 0) {
                $percentage = $change / $open * 100;
            }
        }
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => $this->safe_float($ticker, 'highPrice'),
            'low' => $this->safe_float($ticker, 'lowPrice'),
            'bid' => $this->safe_float($ticker, 'bidPrice'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'askPrice'),
            'askVolume' => null,
            'vwap' => $this->safe_float($ticker, 'vwap'),
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $percentage,
            'average' => $this->sum($open, $last) / 2,
            'baseVolume' => $this->safe_float($ticker, 'homeNotional24h'),
            'quoteVolume' => $this->safe_float($ticker, 'foreignNotional24h'),
            'info' => $ticker,
        );
    }

    public function parse_ohlcv($ohlcv, $market = null) {
        //
        //     {
        //         "timestamp":"2015-09-25T13:38:00.000Z",
        //         "symbol":"XBTUSD",
        //         "open":237.45,
        //         "high":237.45,
        //         "low":237.45,
        //         "close":237.45,
        //         "trades":0,
        //         "volume":0,
        //         "vwap":null,
        //         "lastSize":null,
        //         "turnover":0,
        //         "homeNotional":0,
        //         "foreignNotional":0
        //     }
        //
        return array(
            $this->parse8601($this->safe_string($ohlcv, 'timestamp')),
            $this->safe_float($ohlcv, 'open'),
            $this->safe_float($ohlcv, 'high'),
            $this->safe_float($ohlcv, 'low'),
            $this->safe_float($ohlcv, 'close'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        // send JSON key/value pairs, such as array("key" => "value")
        // $filter by individual fields and do advanced queries on timestamps
        // $filter = array( 'key' => 'value' );
        // send a bare series (e.g. XBU) to nearest expiring contract in that series
        // you can also send a $timeframe, e.g. XBU:monthly
        // timeframes => daily, weekly, monthly, quarterly, and biquarterly
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'binSize' => $this->timeframes[$timeframe],
            'partial' => true,     // true == include yet-incomplete current bins
            // 'filter' => $filter, // $filter by individual fields and do advanced queries
            // 'columns' => array(),    // will return all columns if omitted
            // 'start' => 0,       // starting point for results (wtf?)
            // 'reverse' => false, // true == newest first
            // 'endTime' => '',    // ending date $filter for results
        );
        if ($limit !== null) {
            $request['count'] = $limit; // default 100, max 500
        }
        $duration = $this->parse_timeframe($timeframe) * 1000;
        $fetchOHLCVOpenTimestamp = $this->safe_value($this->options, 'fetchOHLCVOpenTimestamp', true);
        // if $since is not set, they will return candles starting from 2017-01-01
        if ($since !== null) {
            $timestamp = $since;
            if ($fetchOHLCVOpenTimestamp) {
                $timestamp = $this->sum($timestamp, $duration);
            }
            $ymdhms = $this->ymdhms($timestamp);
            $request['startTime'] = $ymdhms; // starting date $filter for results
        } else {
            $request['reverse'] = true;
        }
        $response = $this->publicGetTradeBucketed (array_merge($request, $params));
        //
        //     array(
        //         array("$timestamp":"2015-09-25T13:38:00.000Z","$symbol":"XBTUSD","open":237.45,"high":237.45,"low":237.45,"close":237.45,"trades":0,"volume":0,"vwap":null,"lastSize":null,"turnover":0,"homeNotional":0,"foreignNotional":0),
        //         array("$timestamp":"2015-09-25T13:39:00.000Z","$symbol":"XBTUSD","open":237.45,"high":237.45,"low":237.45,"close":237.45,"trades":0,"volume":0,"vwap":null,"lastSize":null,"turnover":0,"homeNotional":0,"foreignNotional":0),
        //         array("$timestamp":"2015-09-25T13:40:00.000Z","$symbol":"XBTUSD","open":237.45,"high":237.45,"low":237.45,"close":237.45,"trades":0,"volume":0,"vwap":null,"lastSize":null,"turnover":0,"homeNotional":0,"foreignNotional":0)
        //     )
        //
        $result = $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
        if ($fetchOHLCVOpenTimestamp) {
            // bitmex returns the candle's close $timestamp - https://github.com/ccxt/ccxt/issues/4446
            // we can emulate the open $timestamp by shifting all the timestamps one place
            // so the previous close becomes the current open, and we drop the first candle
            for ($i = 0; $i < count($result); $i++) {
                $result[$i][0] = $result[$i][0] - $duration;
            }
        }
        return $result;
    }

    public function parse_trade($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         $timestamp => '2018-08-28T00:00:02.735Z',
        //         $symbol => 'XBTUSD',
        //         $side => 'Buy',
        //         size => 2000,
        //         $price => 6906.5,
        //         tickDirection => 'PlusTick',
        //         trdMatchID => 'b9a42432-0a46-6a2f-5ecc-c32e9ca4baf8',
        //         grossValue => 28958000,
        //         homeNotional => 0.28958,
        //         foreignNotional => 2000
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "execID" => "string",
        //         "orderID" => "string",
        //         "clOrdID" => "string",
        //         "clOrdLinkID" => "string",
        //         "account" => 0,
        //         "$symbol" => "string",
        //         "$side" => "string",
        //         "lastQty" => 0,
        //         "lastPx" => 0,
        //         "underlyingLastPx" => 0,
        //         "lastMkt" => "string",
        //         "lastLiquidityInd" => "string",
        //         "simpleOrderQty" => 0,
        //         "orderQty" => 0,
        //         "$price" => 0,
        //         "displayQty" => 0,
        //         "stopPx" => 0,
        //         "pegOffsetValue" => 0,
        //         "pegPriceType" => "string",
        //         "currency" => "string",
        //         "settlCurrency" => "string",
        //         "execType" => "string",
        //         "ordType" => "string",
        //         "timeInForce" => "string",
        //         "execInst" => "string",
        //         "contingencyType" => "string",
        //         "exDestination" => "string",
        //         "ordStatus" => "string",
        //         "triggered" => "string",
        //         "workingIndicator" => true,
        //         "ordRejReason" => "string",
        //         "simpleLeavesQty" => 0,
        //         "leavesQty" => 0,
        //         "simpleCumQty" => 0,
        //         "cumQty" => 0,
        //         "avgPx" => 0,
        //         "commission" => 0,
        //         "tradePublishIndicator" => "string",
        //         "multiLegReportingType" => "string",
        //         "text" => "string",
        //         "trdMatchID" => "string",
        //         "execCost" => 0,
        //         "execComm" => 0,
        //         "homeNotional" => 0,
        //         "foreignNotional" => 0,
        //         "transactTime" => "2019-03-05T12:47:02.762Z",
        //         "$timestamp" => "2019-03-05T12:47:02.762Z"
        //     }
        //
        $timestamp = $this->parse8601($this->safe_string($trade, 'timestamp'));
        $price = $this->safe_float_2($trade, 'avgPx', 'price');
        $amount = $this->safe_float_2($trade, 'size', 'lastQty');
        $id = $this->safe_string($trade, 'trdMatchID');
        $order = $this->safe_string($trade, 'orderID');
        $side = $this->safe_string_lower($trade, 'side');
        // $price * $amount doesn't work for all symbols (e.g. XBT, ETH)
        $cost = $this->safe_float($trade, 'execCost');
        if ($cost !== null) {
            $cost = abs($cost) / 100000000;
        }
        $fee = null;
        if (is_array($trade) && array_key_exists('execComm', $trade)) {
            $feeCost = $this->safe_float($trade, 'execComm');
            $feeCost = $feeCost / 100000000;
            $currencyId = $this->safe_string($trade, 'settlCurrency');
            $feeCurrency = $this->safe_currency_code($currencyId);
            $feeRate = $this->safe_float($trade, 'commission');
            $fee = array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
                'rate' => $feeRate,
            );
        }
        $takerOrMaker = null;
        if ($fee !== null) {
            $takerOrMaker = ($fee['cost'] < 0) ? 'maker' : 'taker';
        }
        $marketId = $this->safe_string($trade, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $type = $this->safe_string_lower($trade, 'ordType');
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => $id,
            'order' => $order,
            'type' => $type,
            'takerOrMaker' => $takerOrMaker,
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'fee' => $fee,
        );
    }

    public function parse_order_status($status) {
        $statuses = array(
            'New' => 'open',
            'PartiallyFilled' => 'open',
            'Filled' => 'closed',
            'DoneForDay' => 'open',
            'Canceled' => 'canceled',
            'PendingCancel' => 'open',
            'PendingNew' => 'open',
            'Rejected' => 'rejected',
            'Expired' => 'expired',
            'Stopped' => 'open',
            'Untriggered' => 'open',
            'Triggered' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_time_in_force($timeInForce) {
        $timeInForces = array(
            'Day' => 'Day',
            'GoodTillCancel' => 'GTC',
            'ImmediateOrCancel' => 'IOC',
            'FillOrKill' => 'FOK',
        );
        return $this->safe_string($timeInForces, $timeInForce, $timeInForce);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "orderID":"56222c7a-9956-413a-82cf-99f4812c214b",
        //         "clOrdID":"",
        //         "clOrdLinkID":"",
        //         "account":1455728,
        //         "$symbol":"XBTUSD",
        //         "$side":"Sell",
        //         "simpleOrderQty":null,
        //         "orderQty":1,
        //         "$price":40000,
        //         "displayQty":null,
        //         "stopPx":null,
        //         "pegOffsetValue":null,
        //         "pegPriceType":"",
        //         "currency":"USD",
        //         "settlCurrency":"XBt",
        //         "ordType":"Limit",
        //         "$timeInForce":"GoodTillCancel",
        //         "$execInst":"",
        //         "contingencyType":"",
        //         "exDestination":"XBME",
        //         "ordStatus":"New",
        //         "triggered":"",
        //         "workingIndicator":true,
        //         "ordRejReason":"",
        //         "simpleLeavesQty":null,
        //         "leavesQty":1,
        //         "simpleCumQty":null,
        //         "cumQty":0,
        //         "avgPx":null,
        //         "multiLegReportingType":"SingleSecurity",
        //         "text":"Submitted via API.",
        //         "transactTime":"2021-01-02T21:38:49.246Z",
        //         "$timestamp":"2021-01-02T21:38:49.246Z"
        //     }
        //
        $status = $this->parse_order_status($this->safe_string($order, 'ordStatus'));
        $marketId = $this->safe_string($order, 'symbol');
        $symbol = $this->safe_symbol($marketId, $market);
        $timestamp = $this->parse8601($this->safe_string($order, 'timestamp'));
        $lastTradeTimestamp = $this->parse8601($this->safe_string($order, 'transactTime'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'orderQty');
        $filled = $this->safe_float($order, 'cumQty', 0.0);
        $remaining = null;
        if ($amount !== null) {
            if ($filled !== null) {
                $remaining = max ($amount - $filled, 0.0);
            }
        }
        $average = $this->safe_float($order, 'avgPx');
        $cost = null;
        if ($filled !== null) {
            if ($average !== null) {
                $cost = $average * $filled;
            } else if ($price !== null) {
                $cost = $price * $filled;
            }
        }
        $id = $this->safe_string($order, 'orderID');
        $type = $this->safe_string_lower($order, 'ordType');
        $side = $this->safe_string_lower($order, 'side');
        $clientOrderId = $this->safe_string($order, 'clOrdID');
        $timeInForce = $this->parse_time_in_force($this->safe_string($order, 'timeInForce'));
        $stopPrice = $this->safe_float($order, 'stopPx');
        $execInst = $this->safe_string($order, 'execInst');
        $postOnly = ($execInst === 'ParticipateDoNotInitiate');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => $clientOrderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'timeInForce' => $timeInForce,
            'postOnly' => $postOnly,
            'side' => $side,
            'price' => $price,
            'stopPrice' => $stopPrice,
            'amount' => $amount,
            'cost' => $cost,
            'average' => $average,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        if ($since !== null) {
            $request['startTime'] = $this->iso8601($since);
        } else {
            // by default reverse=false, i.e. trades are fetched $since the time of $market inception (year 2015 for XBTUSD)
            $request['reverse'] = true;
        }
        if ($limit !== null) {
            $request['count'] = $limit;
        }
        $response = $this->publicGetTrade (array_merge($request, $params));
        //
        //     array(
        //         array(
        //             timestamp => '2018-08-28T00:00:02.735Z',
        //             $symbol => 'XBTUSD',
        //             side => 'Buy',
        //             size => 2000,
        //             price => 6906.5,
        //             tickDirection => 'PlusTick',
        //             trdMatchID => 'b9a42432-0a46-6a2f-5ecc-c32e9ca4baf8',
        //             grossValue => 28958000,
        //             homeNotional => 0.28958,
        //             foreignNotional => 2000
        //         ),
        //         array(
        //             timestamp => '2018-08-28T00:00:03.778Z',
        //             $symbol => 'XBTUSD',
        //             side => 'Sell',
        //             size => 1000,
        //             price => 6906,
        //             tickDirection => 'MinusTick',
        //             trdMatchID => '0d4f1682-5270-a800-569b-4a0eb92db97c',
        //             grossValue => 14480000,
        //             homeNotional => 0.1448,
        //             foreignNotional => 1000
        //         ),
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $orderType = $this->capitalize($type);
        $request = array(
            'symbol' => $market['id'],
            'side' => $this->capitalize($side),
            'orderQty' => floatval($this->amount_to_precision($symbol, $amount)),
            'ordType' => $orderType,
        );
        if (($orderType === 'Stop') || ($orderType === 'StopLimit') || ($orderType === 'MarketIfTouched') || ($orderType === 'LimitIfTouched')) {
            $stopPrice = $this->safe_float_2($params, 'stopPx', 'stopPrice');
            if ($stopPrice === null) {
                throw new ArgumentsRequired($this->id . ' createOrder() requires a stopPx or $stopPrice parameter for the ' . $orderType . ' order type');
            } else {
                $request['stopPx'] = floatval($this->price_to_precision($symbol, $stopPrice));
                $params = $this->omit($params, array( 'stopPx', 'stopPrice' ));
            }
        }
        if (($orderType === 'Limit') || ($orderType === 'StopLimit') || ($orderType === 'LimitIfTouched')) {
            $request['price'] = floatval($this->price_to_precision($symbol, $price));
        }
        $clientOrderId = $this->safe_string_2($params, 'clOrdID', 'clientOrderId');
        if ($clientOrderId !== null) {
            $request['clOrdID'] = $clientOrderId;
            $params = $this->omit($params, array( 'clOrdID', 'clientOrderId' ));
        }
        $response = $this->privatePostOrder (array_merge($request, $params));
        return $this->parse_order($response, $market);
    }

    public function edit_order($id, $symbol, $type, $side, $amount = null, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $origClOrdID = $this->safe_string_2($params, 'origClOrdID', 'clientOrderId');
        if ($origClOrdID !== null) {
            $request['origClOrdID'] = $origClOrdID;
            $clientOrderId = $this->safe_string($params, 'clOrdID', 'clientOrderId');
            if ($clientOrderId !== null) {
                $request['clOrdID'] = $clientOrderId;
            }
            $params = $this->omit($params, array( 'origClOrdID', 'clOrdID', 'clientOrderId' ));
        } else {
            $request['orderID'] = $id;
        }
        if ($amount !== null) {
            $request['orderQty'] = $amount;
        }
        if ($price !== null) {
            $request['price'] = $price;
        }
        $response = $this->privatePutOrder (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        // https://github.com/ccxt/ccxt/issues/6507
        $clientOrderId = $this->safe_string_2($params, 'clOrdID', 'clientOrderId');
        $request = array();
        if ($clientOrderId === null) {
            $request['orderID'] = $id;
        } else {
            $request['clOrdID'] = $clientOrderId;
            $params = $this->omit($params, array( 'clOrdID', 'clientOrderId' ));
        }
        $response = $this->privateDeleteOrder (array_merge($request, $params));
        $order = $this->safe_value($response, 0, array());
        $error = $this->safe_string($order, 'error');
        if ($error !== null) {
            if (mb_strpos($error, 'Unable to cancel $order due to existing state') !== false) {
                throw new OrderNotFound($this->id . ' cancelOrder() failed => ' . $error);
            }
        }
        return $this->parse_order($order);
    }

    public function cancel_all_orders($symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['symbol'] = $market['id'];
        }
        $response = $this->privateDeleteOrderAll (array_merge($request, $params));
        //
        //     array(
        //         {
        //             "orderID" => "string",
        //             "clOrdID" => "string",
        //             "clOrdLinkID" => "string",
        //             "account" => 0,
        //             "$symbol" => "string",
        //             "side" => "string",
        //             "simpleOrderQty" => 0,
        //             "orderQty" => 0,
        //             "price" => 0,
        //             "displayQty" => 0,
        //             "stopPx" => 0,
        //             "pegOffsetValue" => 0,
        //             "pegPriceType" => "string",
        //             "currency" => "string",
        //             "settlCurrency" => "string",
        //             "ordType" => "string",
        //             "timeInForce" => "string",
        //             "execInst" => "string",
        //             "contingencyType" => "string",
        //             "exDestination" => "string",
        //             "ordStatus" => "string",
        //             "triggered" => "string",
        //             "workingIndicator" => true,
        //             "ordRejReason" => "string",
        //             "simpleLeavesQty" => 0,
        //             "leavesQty" => 0,
        //             "simpleCumQty" => 0,
        //             "cumQty" => 0,
        //             "avgPx" => 0,
        //             "multiLegReportingType" => "string",
        //             "text" => "string",
        //             "transactTime" => "2020-06-01T09:36:35.290Z",
        //             "timestamp" => "2020-06-01T09:36:35.290Z"
        //         }
        //     )
        //
        return $this->parse_orders($response, $market);
    }

    public function fetch_positions($symbols = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privateGetPosition ($params);
        //     array(
        //         {
        //             "account" => 0,
        //             "symbol" => "string",
        //             "currency" => "string",
        //             "underlying" => "string",
        //             "quoteCurrency" => "string",
        //             "commission" => 0,
        //             "initMarginReq" => 0,
        //             "maintMarginReq" => 0,
        //             "riskLimit" => 0,
        //             "leverage" => 0,
        //             "crossMargin" => true,
        //             "deleveragePercentile" => 0,
        //             "rebalancedPnl" => 0,
        //             "prevRealisedPnl" => 0,
        //             "prevUnrealisedPnl" => 0,
        //             "prevClosePrice" => 0,
        //             "openingTimestamp" => "2020-11-09T06:53:59.892Z",
        //             "openingQty" => 0,
        //             "openingCost" => 0,
        //             "openingComm" => 0,
        //             "openOrderBuyQty" => 0,
        //             "openOrderBuyCost" => 0,
        //             "openOrderBuyPremium" => 0,
        //             "openOrderSellQty" => 0,
        //             "openOrderSellCost" => 0,
        //             "openOrderSellPremium" => 0,
        //             "execBuyQty" => 0,
        //             "execBuyCost" => 0,
        //             "execSellQty" => 0,
        //             "execSellCost" => 0,
        //             "execQty" => 0,
        //             "execCost" => 0,
        //             "execComm" => 0,
        //             "currentTimestamp" => "2020-11-09T06:53:59.893Z",
        //             "currentQty" => 0,
        //             "currentCost" => 0,
        //             "currentComm" => 0,
        //             "realisedCost" => 0,
        //             "unrealisedCost" => 0,
        //             "grossOpenCost" => 0,
        //             "grossOpenPremium" => 0,
        //             "grossExecCost" => 0,
        //             "isOpen" => true,
        //             "markPrice" => 0,
        //             "markValue" => 0,
        //             "riskValue" => 0,
        //             "homeNotional" => 0,
        //             "foreignNotional" => 0,
        //             "posState" => "string",
        //             "posCost" => 0,
        //             "posCost2" => 0,
        //             "posCross" => 0,
        //             "posInit" => 0,
        //             "posComm" => 0,
        //             "posLoss" => 0,
        //             "posMargin" => 0,
        //             "posMaint" => 0,
        //             "posAllowance" => 0,
        //             "taxableMargin" => 0,
        //             "initMargin" => 0,
        //             "maintMargin" => 0,
        //             "sessionMargin" => 0,
        //             "targetExcessMargin" => 0,
        //             "varMargin" => 0,
        //             "realisedGrossPnl" => 0,
        //             "realisedTax" => 0,
        //             "realisedPnl" => 0,
        //             "unrealisedGrossPnl" => 0,
        //             "longBankrupt" => 0,
        //             "shortBankrupt" => 0,
        //             "taxBase" => 0,
        //             "indicativeTaxRate" => 0,
        //             "indicativeTax" => 0,
        //             "unrealisedTax" => 0,
        //             "unrealisedPnl" => 0,
        //             "unrealisedPnlPcnt" => 0,
        //             "unrealisedRoePcnt" => 0,
        //             "simpleQty" => 0,
        //             "simpleCost" => 0,
        //             "simpleValue" => 0,
        //             "simplePnl" => 0,
        //             "simplePnlPcnt" => 0,
        //             "avgCostPrice" => 0,
        //             "avgEntryPrice" => 0,
        //             "breakEvenPrice" => 0,
        //             "marginCallPrice" => 0,
        //             "liquidationPrice" => 0,
        //             "bankruptPrice" => 0,
        //             "timestamp" => "2020-11-09T06:53:59.894Z",
        //             "lastPrice" => 0,
        //             "lastValue" => 0
        //         }
        //     )
        //
        // todo unify parsePosition/parsePositions
        return $response;
    }

    public function is_fiat($currency) {
        if ($currency === 'EUR') {
            return true;
        }
        if ($currency === 'PLN') {
            return true;
        }
        return false;
    }

    public function withdraw($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        // $currency = $this->currency($code);
        if ($code !== 'BTC') {
            throw new ExchangeError($this->id . ' supoprts BTC withdrawals only, other currencies coming soon...');
        }
        $request = array(
            'currency' => 'XBt', // temporarily
            'amount' => $amount,
            'address' => $address,
            // 'otpToken' => '123456', // requires if two-factor auth (OTP) is enabled
            // 'fee' => 0.001, // bitcoin network fee
        );
        $response = $this->privatePostUserRequestWithdrawal (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $this->safe_string($response, 'transactID'),
        );
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        if ($code === 429) {
            throw new DDoSProtection($this->id . ' ' . $body);
        }
        if ($code >= 400) {
            $error = $this->safe_value($response, 'error', array());
            $message = $this->safe_string($error, 'message');
            $feedback = $this->id . ' ' . $body;
            $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
            $this->throw_broadly_matched_exception($this->exceptions['broad'], $message, $feedback);
            if ($code === 400) {
                throw new BadRequest($feedback);
            }
            throw new ExchangeError($feedback); // unknown $message
        }
    }

    public function nonce() {
        return $this->milliseconds();
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $query = '/api/' . $this->version . '/' . $path;
        if ($method === 'GET') {
            if ($params) {
                $query .= '?' . $this->urlencode($params);
            }
        } else {
            $format = $this->safe_string($params, '_format');
            if ($format !== null) {
                $query .= '?' . $this->urlencode(array( '_format' => $format ));
                $params = $this->omit($params, '_format');
            }
        }
        $url = $this->urls['api'][$api] . $query;
        if ($this->apiKey && $this->secret) {
            $auth = $method . $query;
            $expires = $this->safe_integer($this->options, 'api-expires');
            $headers = array(
                'Content-Type' => 'application/json',
                'api-key' => $this->apiKey,
            );
            $expires = $this->sum($this->seconds(), $expires);
            $expires = (string) $expires;
            $auth .= $expires;
            $headers['api-expires'] = $expires;
            if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
                if ($params) {
                    $body = $this->json($params);
                    $auth .= $body;
                }
            }
            $headers['api-signature'] = $this->hmac($this->encode($auth), $this->encode($this->secret));
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

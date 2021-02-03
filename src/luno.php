<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;

class luno extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'luno',
            'name' => 'luno',
            'countries' => array( 'GB', 'SG', 'ZA' ),
            'rateLimit' => 1000,
            'version' => '1',
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchAccounts' => true,
                'fetchBalance' => true,
                'fetchClosedOrders' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchMyTrades' => true,
                'fetchOpenOrders' => true,
                'fetchOrder' => true,
                'fetchOrderBook' => true,
                'fetchOrders' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTradingFee' => true,
                'fetchTradingFees' => true,
            ),
            'urls' => array(
                'referral' => 'https://www.luno.com/invite/44893A',
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766607-8c1a69d8-5ede-11e7-930c-540b5eb9be24.jpg',
                'api' => array(
                    'public' => 'https://api.luno.com/api',
                    'private' => 'https://api.luno.com/api',
                    'exchange' => 'https://api.luno.com/api/exchange',
                ),
                'www' => 'https://www.luno.com',
                'doc' => array(
                    'https://www.luno.com/en/api',
                    'https://npmjs.org/package/bitx',
                    'https://github.com/bausmeier/node-bitx',
                ),
            ),
            'api' => array(
                'exchange' => array(
                    'get' => array(
                        'markets',
                    ),
                ),
                'public' => array(
                    'get' => array(
                        'orderbook',
                        'orderbook_top',
                        'ticker',
                        'tickers',
                        'trades',
                    ),
                ),
                'private' => array(
                    'get' => array(
                        'accounts/{id}/pending',
                        'accounts/{id}/transactions',
                        'balance',
                        'beneficiaries',
                        'fee_info',
                        'funding_address',
                        'listorders',
                        'listtrades',
                        'orders/{id}',
                        'quotes/{id}',
                        'withdrawals',
                        'withdrawals/{id}',
                    ),
                    'post' => array(
                        'accounts',
                        'accounts/{id}/name',
                        'postorder',
                        'marketorder',
                        'stoporder',
                        'funding_address',
                        'withdrawals',
                        'send',
                        'quotes',
                        'oauth2/grant',
                    ),
                    'put' => array(
                        'accounts/{id}/name',
                        'quotes/{id}',
                    ),
                    'delete' => array(
                        'quotes/{id}',
                        'withdrawals/{id}',
                    ),
                ),
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $response = $this->exchangeGetMarkets ($params);
        //
        //     {
        //         "$markets":array(
        //             array(
        //                 "market_id":"BCHXBT",
        //                 "trading_status":"ACTIVE",
        //                 "base_currency":"BCH",
        //                 "counter_currency":"XBT",
        //                 "min_volume":"0.01",
        //                 "max_volume":"100.00",
        //                 "volume_scale":2,
        //                 "min_price":"0.0001",
        //                 "max_price":"1.00",
        //                 "price_scale":6,
        //                 "fee_scale":8,
        //             ),
        //         )
        //     }
        //
        $result = array();
        $markets = $this->safe_value($response, 'markets', array());
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'market_id');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'counter_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $status = $this->safe_string($market, 'trading_status');
            $active = ($status === 'ACTIVE');
            $precision = array(
                'amount' => $this->safe_integer($market, 'volume_scale'),
                'price' => $this->safe_integer($market, 'price_scale'),
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
                        'min' => $this->safe_float($market, 'min_volume'),
                        'max' => $this->safe_float($market, 'max_volume'),
                    ),
                    'price' => array(
                        'min' => $this->safe_float($market, 'min_price'),
                        'max' => $this->safe_float($market, 'max_price'),
                    ),
                    'cost' => array(
                        'min' => null,
                        'max' => null,
                    ),
                ),
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_accounts($params = array ()) {
        $response = $this->privateGetBalance ($params);
        $wallets = $this->safe_value($response, 'balance', array());
        $result = array();
        for ($i = 0; $i < count($wallets); $i++) {
            $account = $wallets[$i];
            $accountId = $this->safe_string($account, 'account_id');
            $currencyId = $this->safe_string($account, 'asset');
            $code = $this->safe_currency_code($currencyId);
            $result[] = array(
                'id' => $accountId,
                'type' => null,
                'currency' => $code,
                'info' => $account,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetBalance ($params);
        //
        //     {
        //         'balance' => [
        //             array('account_id' => '119...1336','asset' => 'XBT','balance' => '0.00','reserved' => '0.00','unconfirmed' => '0.00'),
        //             array('account_id' => '66...289','asset' => 'XBT','balance' => '0.00','reserved' => '0.00','unconfirmed' => '0.00'),
        //             array('account_id' => '718...5300','asset' => 'ETH','balance' => '0.00','reserved' => '0.00','unconfirmed' => '0.00'),
        //             array('account_id' => '818...7072','asset' => 'ZAR','balance' => '0.001417','reserved' => '0.00','unconfirmed' => '0.00')]}
        //         ]
        //     }
        //
        $wallets = $this->safe_value($response, 'balance', array());
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($wallets); $i++) {
            $wallet = $wallets[$i];
            $currencyId = $this->safe_string($wallet, 'asset');
            $code = $this->safe_currency_code($currencyId);
            $reserved = $this->safe_float($wallet, 'reserved');
            $unconfirmed = $this->safe_float($wallet, 'unconfirmed');
            $balance = $this->safe_float($wallet, 'balance');
            if (is_array($result) && array_key_exists($code, $result)) {
                $result[$code]['used'] = $this->sum($result[$code]['used'], $reserved, $unconfirmed);
                $result[$code]['total'] = $this->sum($result[$code]['total'], $balance, $unconfirmed);
            } else {
                $account = $this->account();
                $account['used'] = $this->sum($reserved, $unconfirmed);
                $account['total'] = $this->sum($balance, $unconfirmed);
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $method = 'publicGetOrderbook';
        if ($limit !== null) {
            if ($limit <= 100) {
                $method .= 'Top'; // get just the top of the orderbook when $limit is low
            }
        }
        $request = array(
            'pair' => $this->market_id($symbol),
        );
        $response = $this->$method (array_merge($request, $params));
        $timestamp = $this->safe_integer($response, 'timestamp');
        return $this->parse_order_book($response, $timestamp, 'bids', 'asks', 'price', 'volume');
    }

    public function parse_order_status($status) {
        $statuses = array(
            // todo add other $statuses
            'PENDING' => 'open',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order($order, $market = null) {
        //
        //     {
        //         "base" => "string",
        //         "completed_timestamp" => "string",
        //         "counter" => "string",
        //         "creation_timestamp" => "string",
        //         "expiration_timestamp" => "string",
        //         "fee_base" => "string",
        //         "fee_counter" => "string",
        //         "limit_price" => "string",
        //         "limit_volume" => "string",
        //         "order_id" => "string",
        //         "pair" => "string",
        //         "state" => "PENDING",
        //         "type" => "BID"
        //     }
        //
        $timestamp = $this->safe_integer($order, 'creation_timestamp');
        $status = $this->parse_order_status($this->safe_string($order, 'state'));
        $status = ($status === 'open') ? $status : $status;
        $side = ($order['type'] === 'ASK') ? 'sell' : 'buy';
        $marketId = $this->safe_string($order, 'pair');
        $symbol = $this->safe_symbol($marketId, $market);
        $price = $this->safe_float($order, 'limit_price');
        $amount = $this->safe_float($order, 'limit_volume');
        $quoteFee = $this->safe_float($order, 'fee_counter');
        $baseFee = $this->safe_float($order, 'fee_base');
        $filled = $this->safe_float($order, 'base');
        $cost = $this->safe_float($order, 'counter');
        $remaining = null;
        if ($amount !== null) {
            if ($filled !== null) {
                $remaining = max (0, $amount - $filled);
            }
        }
        $fee = array( 'currency' => null );
        if ($quoteFee) {
            $fee['cost'] = $quoteFee;
            if ($market !== null) {
                $fee['currency'] = $market['quote'];
            }
        } else {
            $fee['cost'] = $baseFee;
            if ($market !== null) {
                $fee['currency'] = $market['base'];
            }
        }
        $id = $this->safe_string($order, 'order_id');
        return array(
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => null,
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => $amount,
            'filled' => $filled,
            'cost' => $cost,
            'remaining' => $remaining,
            'trades' => null,
            'fee' => $fee,
            'info' => $order,
            'average' => null,
        );
    }

    public function fetch_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $id,
        );
        $response = $this->privateGetOrdersId (array_merge($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_orders_by_state($state = null, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        $market = null;
        if ($state !== null) {
            $request['state'] = $state;
        }
        if ($symbol !== null) {
            $market = $this->market($symbol);
            $request['pair'] = $market['id'];
        }
        $response = $this->privateGetListorders (array_merge($request, $params));
        $orders = $this->safe_value($response, 'orders', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_state(null, $symbol, $since, $limit, $params);
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_state('PENDING', $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_orders_by_state('COMPLETE', $symbol, $since, $limit, $params);
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->safe_integer($ticker, 'timestamp');
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_float($ticker, 'last_trade');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'rolling_24_hour_volume'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetTickers ($params);
        $tickers = $this->index_by($response['tickers'], 'pair');
        $ids = is_array($tickers) ? array_keys($tickers) : array();
        $result = array();
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $market = $this->safe_market($id);
            $symbol = $market['symbol'];
            $ticker = $tickers[$id];
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
        $response = $this->publicGetTicker (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market) {
        // For public $trade data (is_buy === True) indicates 'buy' $side but for private $trade data
        // is_buy indicates maker or taker. The value of "type" (ASK/BID) indicate sell/buy $side->
        // Private $trade data includes ID field which public $trade data does not.
        $orderId = $this->safe_string($trade, 'order_id');
        $takerOrMaker = null;
        $side = null;
        if ($orderId !== null) {
            $side = ($trade['type'] === 'ASK') ? 'sell' : 'buy';
            if ($side === 'sell' && $trade['is_buy']) {
                $takerOrMaker = 'maker';
            } else if ($side === 'buy' && !$trade['is_buy']) {
                $takerOrMaker = 'maker';
            } else {
                $takerOrMaker = 'taker';
            }
        } else {
            $side = $trade['is_buy'] ? 'buy' : 'sell';
        }
        $feeBase = $this->safe_float($trade, 'fee_base');
        $feeCounter = $this->safe_float($trade, 'fee_counter');
        $feeCurrency = null;
        $feeCost = null;
        if ($feeBase !== null) {
            if ($feeBase !== 0.0) {
                $feeCurrency = $market['base'];
                $feeCost = $feeBase;
            }
        } else if ($feeCounter !== null) {
            if ($feeCounter !== 0.0) {
                $feeCurrency = $market['quote'];
                $feeCost = $feeCounter;
            }
        }
        $timestamp = $this->safe_integer($trade, 'timestamp');
        return array(
            'info' => $trade,
            'id' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $market['symbol'],
            'order' => $orderId,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => $takerOrMaker,
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'volume'),
            // Does not include potential fee costs
            'cost' => $this->safe_float($trade, 'counter'),
            'fee' => array(
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            ),
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        if ($since !== null) {
            $request['since'] = $since;
        }
        $response = $this->publicGetTrades (array_merge($request, $params));
        $trades = $this->safe_value($response, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_my_trades($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'pair' => $market['id'],
        );
        if ($since !== null) {
            $request['since'] = $since;
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privateGetListtrades (array_merge($request, $params));
        $trades = $this->safe_value($response, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_trading_fees($params = array ()) {
        $this->load_markets();
        $response = $this->privateGetFeeInfo ($params);
        return array(
            'info' => $response,
            'maker' => $this->safe_float($response, 'maker_fee'),
            'taker' => $this->safe_float($response, 'taker_fee'),
        );
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $method = 'privatePost';
        $request = array(
            'pair' => $this->market_id($symbol),
        );
        if ($type === 'market') {
            $method .= 'Marketorder';
            $request['type'] = strtoupper($side);
            if ($side === 'buy') {
                $request['counter_volume'] = $amount;
            } else {
                $request['base_volume'] = $amount;
            }
        } else {
            $method .= 'Postorder';
            $request['volume'] = $amount;
            $request['price'] = $price;
            $request['type'] = ($side === 'buy') ? 'BID' : 'ASK';
        }
        $response = $this->$method (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => $response['order_id'],
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'order_id' => $id,
        );
        return $this->privatePostStoporder (array_merge($request, $params));
    }

    public function fetch_ledger_by_entries($code = null, $entry = -1, $limit = 1, $params = array ()) {
        // by default without $entry number or $limit number, return most recent $entry
        $since = null;
        $request = array(
            'min_row' => $entry,
            'max_row' => $this->sum($entry, $limit),
        );
        return $this->fetch_ledger($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $this->load_accounts();
        $currency = null;
        $id = $this->safe_string($params, 'id'); // $account $id
        $min_row = $this->safe_value($params, 'min_row');
        $max_row = $this->safe_value($params, 'max_row');
        if ($id === null) {
            if ($code === null) {
                throw new ArgumentsRequired($this->id . ' fetchLedger() requires a $currency $code argument if no $account $id specified in params');
            }
            $currency = $this->currency($code);
            $accountsByCurrencyCode = $this->index_by($this->accounts, 'currency');
            $account = $this->safe_value($accountsByCurrencyCode, $code);
            if ($account === null) {
                throw new ExchangeError($this->id . ' fetchLedger() could not find $account $id for ' . $code);
            }
            $id = $account['id'];
        }
        if ($min_row === null && $max_row === null) {
            $max_row = 0; // Default to most recent transactions
            $min_row = -1000; // Maximum number of records supported
        } else if ($min_row === null || $max_row === null) {
            throw new ExchangeError($this->id . " fetchLedger() require both $params 'max_row' and 'min_row' or neither to be defined");
        }
        if ($limit !== null && $max_row - $min_row > $limit) {
            if ($max_row <= 0) {
                $min_row = $max_row - $limit;
            } else if ($min_row > 0) {
                $max_row = $min_row . $limit;
            }
        }
        if ($max_row - $min_row > 1000) {
            throw new ExchangeError($this->id . " fetchLedger() requires the $params 'max_row' - 'min_row' <= 1000");
        }
        $request = array(
            'id' => $id,
            'min_row' => $min_row,
            'max_row' => $max_row,
        );
        $response = $this->privateGetAccountsIdTransactions (array_merge($params, $request));
        $entries = $this->safe_value($response, 'transactions', array());
        return $this->parse_ledger($entries, $currency, $since, $limit);
    }

    public function parse_ledger_comment($comment) {
        $words = explode(' ', $comment);
        $types = array(
            'Withdrawal' => 'fee',
            'Trading' => 'fee',
            'Payment' => 'transaction',
            'Sent' => 'transaction',
            'Deposit' => 'transaction',
            'Received' => 'transaction',
            'Released' => 'released',
            'Reserved' => 'reserved',
            'Sold' => 'trade',
            'Bought' => 'trade',
            'Failure' => 'failed',
        );
        $referenceId = null;
        $firstWord = $this->safe_string($words, 0);
        $thirdWord = $this->safe_string($words, 2);
        $fourthWord = $this->safe_string($words, 3);
        $type = $this->safe_string($types, $firstWord, null);
        if (($type === null) && ($thirdWord === 'fee')) {
            $type = 'fee';
        }
        if (($type === 'reserved') && ($fourthWord === 'order')) {
            $referenceId = $this->safe_string($words, 4);
        }
        return array(
            'type' => $type,
            'referenceId' => $referenceId,
        );
    }

    public function parse_ledger_entry($entry, $currency = null) {
        // $details = $this->safe_value($entry, 'details', array());
        $id = $this->safe_string($entry, 'row_index');
        $account_id = $this->safe_string($entry, 'account_id');
        $timestamp = $this->safe_value($entry, 'timestamp');
        $currencyId = $this->safe_string($entry, 'currency');
        $code = $this->safe_currency_code($currencyId, $currency);
        $available_delta = $this->safe_float($entry, 'available_delta');
        $balance_delta = $this->safe_float($entry, 'balance_delta');
        $after = $this->safe_float($entry, 'balance');
        $comment = $this->safe_string($entry, 'description');
        $before = $after;
        $amount = 0.0;
        $result = $this->parse_ledger_comment($comment);
        $type = $result['type'];
        $referenceId = $result['referenceId'];
        $direction = null;
        $status = null;
        if ($balance_delta !== 0.0) {
            $before = $after - $balance_delta; // TODO => float precision
            $status = 'ok';
            $amount = abs($balance_delta);
        } else if ($available_delta < 0.0) {
            $status = 'pending';
            $amount = abs($available_delta);
        } else if ($available_delta > 0.0) {
            $status = 'canceled';
            $amount = abs($available_delta);
        }
        if ($balance_delta > 0 || $available_delta > 0) {
            $direction = 'in';
        } else if ($balance_delta < 0 || $available_delta < 0) {
            $direction = 'out';
        }
        return array(
            'id' => $id,
            'direction' => $direction,
            'account' => $account_id,
            'referenceId' => $referenceId,
            'referenceAccount' => null,
            'type' => $type,
            'currency' => $code,
            'amount' => $amount,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'before' => $before,
            'after' => $after,
            'status' => $status,
            'fee' => null,
            'info' => $entry,
        );
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($query) {
            $url .= '?' . $this->urlencode($query);
        }
        if ($api === 'private') {
            $this->check_required_credentials();
            $auth = base64_encode($this->apiKey . ':' . $this->secret);
            $headers = array(
                'Authorization' => 'Basic ' . $this->decode($auth),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('error', $response)) {
            throw new ExchangeError($this->id . ' ' . $this->json($response));
        }
        return $response;
    }
}

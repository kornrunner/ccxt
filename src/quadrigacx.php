<?php

namespace ccxt;

use Exception as Exception; // a common import

class quadrigacx extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'quadrigacx',
            'name' => 'QuadrigaCX',
            'countries' => array ( 'CA' ),
            'rateLimit' => 1000,
            'version' => 'v2',
            'has' => array (
                'fetchDepositAddress' => true,
                'fetchTickers' => true,
                'fetchOrder' => true,
                'fetchMyTrades' => true,
                'fetchTransactions' => true,
                'CORS' => true,
                'withdraw' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766825-98a6d0de-5ee7-11e7-9fa4-38e11a2c6f52.jpg',
                'api' => 'https://api.quadrigacx.com',
                'www' => 'https://www.quadrigacx.com',
                'doc' => 'https://www.quadrigacx.com/api_info',
                'referral' => 'https://www.quadrigacx.com/?ref=laiqgbp6juewva44finhtmrk',
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'order_book',
                        'ticker',
                        'transactions',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'balance',
                        'bitcoin_deposit_address',
                        'bitcoin_withdrawal',
                        'bitcoincash_deposit_address',
                        'bitcoincash_withdrawal',
                        'bitcoingold_deposit_address',
                        'bitcoingold_withdrawal',
                        'buy',
                        'cancel_order',
                        'ether_deposit_address',
                        'ether_withdrawal',
                        'litecoin_deposit_address',
                        'litecoin_withdrawal',
                        'lookup_order',
                        'open_orders',
                        'sell',
                        'user_transactions',
                    ),
                ),
            ),
            'markets' => array (
                'BTC/CAD' => array ( 'id' => 'btc_cad', 'symbol' => 'BTC/CAD', 'base' => 'BTC', 'quote' => 'CAD', 'baseId' => 'btc', 'quoteId' => 'cad', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTC/USD' => array ( 'id' => 'btc_usd', 'symbol' => 'BTC/USD', 'base' => 'BTC', 'quote' => 'USD', 'baseId' => 'btc', 'quoteId' => 'usd', 'maker' => 0.005, 'taker' => 0.005 ),
                'ETH/BTC' => array ( 'id' => 'eth_btc', 'symbol' => 'ETH/BTC', 'base' => 'ETH', 'quote' => 'BTC', 'baseId' => 'eth', 'quoteId' => 'btc', 'maker' => 0.002, 'taker' => 0.002 ),
                'ETH/CAD' => array ( 'id' => 'eth_cad', 'symbol' => 'ETH/CAD', 'base' => 'ETH', 'quote' => 'CAD', 'baseId' => 'eth', 'quoteId' => 'cad', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/CAD' => array ( 'id' => 'ltc_cad', 'symbol' => 'LTC/CAD', 'base' => 'LTC', 'quote' => 'CAD', 'baseId' => 'ltc', 'quoteId' => 'cad', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/BTC' => array ( 'id' => 'ltc_btc', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'baseId' => 'ltc', 'quoteId' => 'btc', 'maker' => 0.005, 'taker' => 0.005 ),
                'BCH/CAD' => array ( 'id' => 'bch_cad', 'symbol' => 'BCH/CAD', 'base' => 'BCH', 'quote' => 'CAD', 'baseId' => 'bch', 'quoteId' => 'cad', 'maker' => 0.005, 'taker' => 0.005 ),
                'BCH/BTC' => array ( 'id' => 'bch_btc', 'symbol' => 'BCH/BTC', 'base' => 'BCH', 'quote' => 'BTC', 'baseId' => 'bch', 'quoteId' => 'btc', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTG/CAD' => array ( 'id' => 'btg_cad', 'symbol' => 'BTG/CAD', 'base' => 'BTG', 'quote' => 'CAD', 'baseId' => 'btg', 'quoteId' => 'cad', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTG/BTC' => array ( 'id' => 'btg_btc', 'symbol' => 'BTG/BTC', 'base' => 'BTG', 'quote' => 'BTC', 'baseId' => 'btg', 'quoteId' => 'btc', 'maker' => 0.005, 'taker' => 0.005 ),
            ),
            'exceptions' => array (
                '101' => '\\ccxt\\AuthenticationError',
                '106' => '\\ccxt\\OrderNotFound', // array ( 'code':106, 'message' => 'Cannot perform request - not found' )
            ),
        ));
    }

    public function fetch_balance ($params = array ()) {
        $balances = $this->privatePostBalance ();
        $result = array ( 'info' => $balances );
        $currencyIds = is_array ($this->currencies_by_id) ? array_keys ($this->currencies_by_id) : array ();
        for ($i = 0; $i < count ($currencyIds); $i++) {
            $currencyId = $currencyIds[$i];
            $currency = $this->currencies_by_id[$currencyId];
            $code = $currency['code'];
            $result[$code] = array (
                'free' => $this->safe_float($balances, $currencyId . '_available'),
                'used' => $this->safe_float($balances, $currencyId . '_reserved'),
                'total' => $this->safe_float($balances, $currencyId . '_balance'),
            );
        }
        return $this->parse_balance($result);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $market = null;
        $request = array ();
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['book'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privatePostUserTransactions (array_merge ($request, $params));
        $trades = $this->filter_by($response, 'type', 2);
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_transactions ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $market = null;
        $request = array ();
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['book'] = $market['id'];
        }
        if ($limit !== null) {
            $request['limit'] = $limit;
        }
        $response = $this->privatePostUserTransactions (array_merge ($request, $params));
        $user_transactions = $this->filter_by_array($response, 'type', [0, 1], false);
        // return $user_transactions;
        return $this->parseTransactions ($user_transactions, $market, $since, $limit);
    }

    public function parse_transaction ($transaction, $currency = null) {
        //
        //     {
        //         "btc":"0.99985260",
        //         "method":"Bitcoin",
        //         "$fee":"0.00000000",
        //         "$type":0,
        //         "datetime":"2018-10-08 05:26:23"
        //     }
        //
        //     {
        //         "btc":"-0.50000000",
        //         "method":"Bitcoin",
        //         "$fee":"0.00000000",
        //         "$type":1,
        //         "datetime":"2018-08-27 13:50:10"
        //     }
        //
        $code = null;
        $amount = null;
        $omitted = $this->omit ($transaction, array ( 'datetime', 'type', 'method', 'fee' ));
        $keys = is_array ($omitted) ? array_keys ($omitted) : array ();
        for ($i = 0; $i < count ($keys); $i++) {
            if (is_array ($this->currencies_by_id) && array_key_exists ($keys[$i], $this->currencies_by_id)) {
                $code = $keys[$i];
            }
        }
        if ($code !== null) {
            $amount = $this->safe_string($transaction, $code);
        }
        $timestamp = $this->parse8601 ($this->safe_string($transaction, 'datetime'));
        $status = 'ok';
        $fee = $this->safe_float($transaction, 'fee');
        $type = $this->safe_integer($transaction, 'type');
        $type = ($type === 1) ? 'withdrawal' : 'deposit';
        return array (
            'info' => $transaction,
            'id' => null,
            'txid' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'address' => null,
            'tag' => null,
            'type' => $type,
            'amount' => $amount,
            'currency' => $code,
            'status' => $status,
            'updated' => null,
            'fee' => array (
                'currency' => $code,
                'cost' => $fee,
            ),
        );
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $request = array (
            'id' => $id,
        );
        $response = $this->privatePostLookupOrder (array_merge ($request, $params));
        return $this->parse_orders($response);
    }

    public function parse_order_status ($status) {
        $statuses = array (
            '-1' => 'canceled',
            '0' => 'open',
            '1' => 'open',
            '2' => 'closed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_order ($order, $market = null) {
        $id = $this->safe_string($order, 'id');
        $price = $this->safe_float($order, 'price');
        $amount = null;
        $filled = null;
        $remaining = $this->safe_float($order, 'amount');
        $cost = null;
        $symbol = null;
        $marketId = $this->safe_string($order, 'book');
        if (is_array ($this->markets_by_id) && array_key_exists ($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
        } else {
            list ($baseId, $quoteId) = explode ('_', $marketId);
            $base = strtoupper ($baseId);
            $quote = strtoupper ($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
        }
        $side = $this->safe_string($order, 'type');
        if ($side === '0') {
            $side = 'buy';
        } else {
            $side = 'sell';
        }
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $timestamp = $this->parse8601 ($this->safe_string($order, 'created'));
        $lastTradeTimestamp = $this->parse8601 ($this->safe_string($order, 'updated'));
        $type = ($price === 0.0) ? 'market' : 'limit';
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        if ($status === 'closed') {
            $amount = $remaining;
            $filled = $remaining;
            $remaining = 0;
        }
        if (($type === 'limit') && ($price !== null)) {
            if ($filled !== null) {
                $cost = $price * $filled;
            }
        }
        $result = array (
            'info' => $order,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => $lastTradeTimestamp,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $cost,
            'average' => null,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
        );
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $orderbook = $this->publicGetOrderBook (array_merge (array (
            'book' => $this->market_id($symbol),
        ), $params));
        $timestamp = intval ($orderbook['timestamp']) * 1000;
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $response = $this->publicGetTicker (array_merge (array (
            'book' => 'all',
        ), $params));
        $ids = is_array ($response) ? array_keys ($response) : array ();
        $result = array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $symbol = $id;
            $market = null;
            if (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                list ($baseId, $quoteId) = explode ('_', $id);
                $base = strtoupper ($baseId);
                $quote = strtoupper ($quoteId);
                $base = $this->common_currency_code($base);
                $quote = $this->common_currency_code($base);
                $symbol = $base . '/' . $quote;
                $market = array (
                    'symbol' => $symbol,
                );
            }
            $result[$symbol] = $this->parse_ticker($response[$id], $market);
        }
        return $result;
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTicker (array_merge (array (
            'book' => $market['id'],
        ), $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_ticker ($ticker, $market = null) {
        $symbol = null;
        if ($market !== null)
            $symbol = $market['symbol'];
        $timestamp = intval ($ticker['timestamp']) * 1000;
        $vwap = $this->safe_float($ticker, 'vwap');
        $baseVolume = $this->safe_float($ticker, 'volume');
        $quoteVolume = null;
        if ($baseVolume !== null && $vwap !== null)
            $quoteVolume = $baseVolume * $vwap;
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     array ("$amount":"2.26252009","date":"1541355778","$price":"0.03300000","tid":3701722,"$side":"sell")
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "datetime" => "2018-01-01T00:00:00", // date and time
        //         "$id" => 123, // unique identifier (only for trades)
        //         "type" => 2, // transaction type (0 - deposit; 1 - withdrawal; 2 - $trade)
        //         "method" => "...", // deposit or withdrawal method
        //         "(minor currency code)" – the minor currency $amount
        //         "(major currency code)" – the major currency $amount
        //         "order_id" => "...", // a 64 character long hexadecimal string representing the order that was fully or partially filled (only for trades)
        //         "$fee" => 123.45, // transaction $fee
        //         "$rate" => 54.321, // $rate per btc (only for trades)
        //     }
        //
        $id = $this->safe_string_2($trade, 'tid', 'id');
        $timestamp = $this->parse8601 ($this->safe_string($trade, 'datetime'));
        if ($timestamp === null) {
            $timestamp = $this->safe_integer($trade, 'date');
            if ($timestamp !== null) {
                $timestamp *= 1000;
            }
        }
        $symbol = null;
        $omitted = $this->omit ($trade, array ( 'datetime', 'id', 'type', 'method', 'order_id', 'fee', 'rate' ));
        $keys = is_array ($omitted) ? array_keys ($omitted) : array ();
        $rate = $this->safe_float($trade, 'rate');
        for ($i = 0; $i < count ($keys); $i++) {
            $marketId = $keys[$i];
            $floatValue = $this->safe_float($trade, $marketId);
            if ($floatValue === $rate) {
                if (is_array ($this->markets_by_id) && array_key_exists ($marketId, $this->markets_by_id)) {
                    $market = $this->markets_by_id[$marketId];
                } else {
                    $currencyIds = explode ('_', $marketId);
                    $numCurrencyIds = is_array ($currencyIds) ? count ($currencyIds) : 0;
                    if ($numCurrencyIds === 2) {
                        $baseId = $currencyIds[0];
                        $quoteId = $currencyIds[1];
                        $base = strtoupper ($baseId);
                        $quote = strtoupper ($quoteId);
                        $base = $this->common_currency_code($base);
                        $quote = $this->common_currency_code($base);
                        $symbol = $base . '/' . $quote;
                    }
                }
            }
        }
        $orderId = $this->safe_string($trade, 'order_id');
        $side = $this->safe_string($trade, 'side');
        $price = $this->safe_float($trade, 'price', $rate);
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            $baseId = $market['baseId'];
            $quoteId = $market['quoteId'];
            if ($amount === null) {
                $amount = $this->safe_float($trade, $baseId);
                if ($amount !== null) {
                    $amount = abs ($amount);
                }
            }
            $cost = $this->safe_float($trade, $quoteId);
            if ($cost !== null) {
                $cost = abs ($cost);
            }
            if ($side === null) {
                $baseValue = $this->safe_float($trade, $market['baseId']);
                if (($baseValue !== null) && ($baseValue > 0)) {
                    $side = 'buy';
                } else {
                    $side = 'sell';
                }
            }
        }
        if ($cost === null) {
            if ($price !== null) {
                if ($amount !== null) {
                    $cost = $amount * $price;
                }
            }
        }
        $fee = null;
        $feeCost = $this->safe_float($trade, 'fee');
        if ($feeCost !== null) {
            $feeCurrency = null;
            if ($market !== null) {
                $feeCurrency = ($side === 'buy') ? $market['base'] : $market['quote'];
            }
            $fee = array (
                'cost' => $feeCost,
                'currency' => $feeCurrency,
            );
        }
        return array (
            'info' => $trade,
            'id' => $id,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'order' => $orderId,
            'type' => null,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $market = $this->market ($symbol);
        $response = $this->publicGetTransactions (array_merge (array (
            'book' => $market['id'],
        ), $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $method = 'privatePost' . $this->capitalize ($side);
        $order = array (
            'amount' => $amount,
            'book' => $this->market_id($symbol),
        );
        if ($type === 'limit')
            $order['price'] = $price;
        $response = $this->$method (array_merge ($order, $params));
        return array (
            'info' => $response,
            'id' => (string) $response['id'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        return $this->privatePostCancelOrder (array_merge (array (
            'id' => $id,
        ), $params));
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $method = 'privatePost' . $this->get_currency_name ($code) . 'DepositAddress';
        $response = $this->$method ($params);
        // [E|e]rror
        if (mb_strpos ($response, 'rror') !== false) {
            throw new ExchangeError ($this->id . ' ' . $response);
        }
        $this->check_address($response);
        return array (
            'currency' => $code,
            'address' => $response,
            'tag' => null,
            'info' => $response,
        );
    }

    public function get_currency_name ($code) {
        $currencies = array (
            'ETH' => 'Ether',
            'BTC' => 'Bitcoin',
            'LTC' => 'Litecoin',
            'BCH' => 'Bitcoincash',
            'BTG' => 'Bitcoingold',
        );
        return $currencies[$code];
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $request = array (
            'amount' => $amount,
            'address' => $address,
        );
        $method = 'privatePost' . $this->get_currency_name ($code) . 'Withdrawal';
        $response = $this->$method (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => null,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            $url .= '?' . $this->urlencode ($params);
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $request = implode ('', array ((string) $nonce, $this->uid, $this->apiKey));
            $signature = $this->hmac ($this->encode ($request), $this->encode ($this->secret));
            $query = array_merge (array (
                'key' => $this->apiKey,
                'nonce' => $nonce,
                'signature' => $signature,
            ), $params);
            $body = $this->json ($query);
            $headers = array (
                'Content-Type' => 'application/json',
            );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($statusCode, $statusText, $url, $method, $headers, $body, $response) {
        if (gettype ($body) !== 'string')
            return; // fallback to default $error handler
        if (strlen ($body) < 2)
            return;
        if (($body[0] === '{') || ($body[0] === '[')) {
            $error = $this->safe_value($response, 'error');
            if ($error !== null) {
                //
                // array ("$error":{"$code":101,"message":"Invalid API Code or Invalid Signature")}
                //
                $code = $this->safe_string($error, 'code');
                $feedback = $this->id . ' ' . $this->json ($response);
                $exceptions = $this->exceptions;
                if (is_array ($exceptions) && array_key_exists ($code, $exceptions)) {
                    throw new $exceptions[$code] ($feedback);
                } else {
                    throw new ExchangeError ($this->id . ' unknown "$error" value => ' . $this->json ($response));
                }
            }
        }
    }
}

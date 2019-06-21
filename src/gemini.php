<?php

namespace ccxt;

use Exception as Exception; // a common import

class gemini extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'gemini',
            'name' => 'Gemini',
            'countries' => array ( 'US' ),
            'rateLimit' => 1500, // 200 for private API
            'version' => 'v1',
            'has' => array (
                'fetchDepositAddress' => false,
                'createDepositAddress' => true,
                'CORS' => false,
                'fetchBidsAsks' => false,
                'fetchTickers' => false,
                'fetchMyTrades' => true,
                'fetchOrder' => true,
                'fetchOrders' => false,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => false,
                'createMarketOrder' => false,
                'withdraw' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => false,
                'fetchDeposits' => false,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27816857-ce7be644-6096-11e7-82d6-3c257263229c.jpg',
                'api' => 'https://api.gemini.com',
                'www' => 'https://gemini.com',
                'doc' => array (
                    'https://docs.gemini.com/rest-api',
                    'https://docs.sandbox.gemini.com',
                ),
                'test' => 'https://api.sandbox.gemini.com',
                'fees' => array (
                    'https://gemini.com/fee-schedule/',
                    'https://gemini.com/transfer-fees/',
                ),
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'symbols',
                        'pubticker/{symbol}',
                        'book/{symbol}',
                        'trades/{symbol}',
                        'auction/{symbol}',
                        'auction/{symbol}/history',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'order/new',
                        'order/cancel',
                        'order/cancel/session',
                        'order/cancel/all',
                        'order/status',
                        'orders',
                        'mytrades',
                        'tradevolume',
                        'transfers',
                        'balances',
                        'deposit/{currency}/newAddress',
                        'withdraw/{currency}',
                        'heartbeat',
                        'transfers',
                    ),
                ),
            ),
            'fees' => array (
                'trading' => array (
                    'taker' => 0.0035,
                    'maker' => 0.001,
                ),
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetSymbols ($params);
        $result = array();
        for ($i = 0; $i < count ($response); $i++) {
            $id = $response[$i];
            $market = $id;
            $baseId = mb_substr($id, 0, 3 - 0);
            $quoteId = mb_substr($id, 3, 6 - 3);
            $base = strtoupper($baseId);
            $quote = strtoupper($quoteId);
            $base = $this->common_currency_code($base);
            $quote = $this->common_currency_code($quote);
            $symbol = $base . '/' . $quote;
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbol' => $this->market_id($symbol),
        );
        if ($limit !== null) {
            $request['limit_bids'] = $limit;
            $request['limit_asks'] = $limit;
        }
        $response = $this->publicGetBookSymbol (array_merge ($request, $params));
        return $this->parse_order_book($response, null, 'bids', 'asks', 'price', 'amount');
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        $ticker = $this->publicGetPubtickerSymbol (array_merge ($request, $params));
        $timestamp = $this->safe_integer($ticker['volume'], 'timestamp');
        $baseVolume = $this->safe_float($market, 'base');
        $quoteVolume = $this->safe_float($market, 'quote');
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
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
            'baseVolume' => $this->safe_float($ticker['volume'], $baseVolume),
            'quoteVolume' => $this->safe_float($ticker['volume'], $quoteVolume),
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = $this->safe_integer($trade, 'timestampms');
        $id = $this->safe_string($trade, 'tid');
        $orderId = $this->safe_string($trade, 'order_id');
        $fee = $this->safe_float($trade, 'fee_amount');
        if ($fee !== null) {
            $currency = $this->safe_string($trade, 'fee_currency');
            if ($currency !== null) {
                if (is_array($this->currencies_by_id) && array_key_exists($currency, $this->currencies_by_id)) {
                    $currency = $this->currencies_by_id[$currency]['code'];
                }
                $currency = $this->common_currency_code($currency);
            }
            $fee = array (
                'cost' => $this->safe_float($trade, 'fee_amount'),
                'currency' => $currency,
            );
        }
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $type = null;
        $side = $this->safe_string($trade, 'type');
        if ($side !== null) {
            $side = strtolower($side);
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array (
            'id' => $id,
            'order' => $orderId,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'takerOrMaker' => null,
            'price' => $price,
            'cost' => $cost,
            'amount' => $amount,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTradesSymbol (array_merge ($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostBalances ($params);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count ($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'currency');
            $code = $this->common_currency_code($currencyId);
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['total'] = $this->safe_float($balance, 'amount');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order ($order, $market = null) {
        $timestamp = $this->safe_integer($order, 'timestampms');
        $amount = $this->safe_float($order, 'original_amount');
        $remaining = $this->safe_float($order, 'remaining_amount');
        $filled = $this->safe_float($order, 'executed_amount');
        $status = 'closed';
        if ($order['is_live']) {
            $status = 'open';
        }
        if ($order['is_cancelled']) {
            $status = 'canceled';
        }
        $price = $this->safe_float($order, 'price');
        $average = $this->safe_float($order, 'avg_execution_price');
        $cost = null;
        if ($filled !== null) {
            if ($average !== null) {
                $cost = $filled * $average;
            }
        }
        $type = $this->safe_string($order, 'type');
        if ($type === 'exchange limit') {
            $type = 'limit';
        } else if ($type === 'market buy' || $type === 'market sell') {
            $type = 'market';
        } else {
            $type = $order['type'];
        }
        $fee = null;
        $symbol = null;
        if ($market === null) {
            $marketId = $this->safe_string($order, 'symbol');
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $id = $this->safe_string($order, 'order_id');
        return array (
            'id' => $id,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => strtolower($order['side']),
            'price' => $price,
            'average' => $average,
            'cost' => $cost,
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'fee' => $fee,
        );
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
        );
        $response = $this->privatePostOrderStatus (array_merge ($request, $params));
        return $this->parse_order($response);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->privatePostOrders ($params);
        $orders = $this->parse_orders($response, null, $since, $limit);
        if ($symbol !== null) {
            $market = $this->market ($symbol); // throws on non-existent $symbol
            $orders = $this->filter_by_symbol($orders, $market['symbol']);
        }
        return $orders;
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        if ($type === 'market') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $nonce = $this->nonce ();
        $request = array (
            'client_order_id' => (string) $nonce,
            'symbol' => $this->market_id($symbol),
            'amount' => (string) $amount,
            'price' => (string) $price,
            'side' => $side,
            'type' => 'exchange limit', // gemini allows limit orders only
        );
        $response = $this->privatePostOrderNew (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $response['order_id'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'order_id' => $id,
        );
        return $this->privatePostOrderCancel (array_merge ($request, $params));
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        if ($limit !== null) {
            $request['limit_trades'] = $limit;
        }
        if ($since !== null) {
            $request['timestamp'] = intval ($since / 1000);
        }
        $response = $this->privatePostMytrades (array_merge ($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
            'amount' => $amount,
            'address' => $address,
        );
        $response = $this->privatePostWithdrawCurrency (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $this->safe_string($response, 'txHash'),
        );
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function fetch_transactions ($code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array();
        if ($limit !== null) {
            $request['limit_transfers'] = $limit;
        }
        if ($since !== null) {
            $request['timestamp'] = $since;
        }
        $response = $this->privatePostTransfers (array_merge ($request, $params));
        return $this->parseTransactions ($response);
    }

    public function parse_transaction ($transaction, $currency = null) {
        $timestamp = $this->safe_integer($transaction, 'timestampms');
        $code = null;
        if ($currency === null) {
            $currencyId = $this->safe_string($transaction, 'currency');
            if (is_array($this->currencies_by_id) && array_key_exists($currencyId, $this->currencies_by_id)) {
                $currency = $this->currencies_by_id[$currencyId];
            }
        }
        if ($currency !== null) {
            $code = $currency['code'];
        }
        $address = $this->safe_string($transaction, 'destination');
        $type = $this->safe_string($transaction, 'type');
        if ($type !== null) {
            $type = strtolower($type);
        }
        $status = 'pending';
        // When deposits show as Advanced or Complete they are available for trading.
        if ($transaction['status']) {
            $status = 'ok';
        }
        $fee = null;
        $feeAmount = $this->safe_float($transaction, 'feeAmount');
        if ($feeAmount !== null) {
            $fee = array (
                'cost' => $feeAmount,
                'currency' => $code,
            );
        }
        return array (
            'info' => $transaction,
            'id' => $this->safe_string($transaction, 'eid'),
            'txid' => $this->safe_string($transaction, 'txHash'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'address' => $address,
            'tag' => null, // or is it defined?
            'type' => $type, // direction of the $transaction, ('deposit' | 'withdraw')
            'amount' => $this->safe_float($transaction, 'amount'),
            'currency' => $code,
            'status' => $status,
            'updated' => null,
            'fee' => $fee,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $request = array_merge (array (
                'request' => $url,
                'nonce' => $nonce,
            ), $query);
            $payload = $this->json ($request);
            $payload = base64_encode ($this->encode ($payload));
            $signature = $this->hmac ($payload, $this->encode ($this->secret), 'sha384');
            $headers = array (
                'Content-Type' => 'text/plain',
                'X-GEMINI-APIKEY' => $this->apiKey,
                'X-GEMINI-PAYLOAD' => $this->decode ($payload),
                'X-GEMINI-SIGNATURE' => $signature,
            );
        }
        $url = $this->urls['api'] . $url;
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('result', $response)) {
            if ($response['result'] === 'error') {
                throw new ExchangeError($this->id . ' ' . $this->json ($response));
            }
        }
        return $response;
    }

    public function create_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $request = array (
            'currency' => $currency['id'],
        );
        $response = $this->privatePostDepositCurrencyNewAddress (array_merge ($request, $params));
        $address = $this->safe_string($response, 'address');
        $this->check_address($address);
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => null,
            'info' => $response,
        );
    }
}

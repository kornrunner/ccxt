<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ExchangeError;
use \ccxt\ArgumentsRequired;
use \ccxt\InvalidOrder;

class coinfloor extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'coinfloor',
            'name' => 'coinfloor',
            'rateLimit' => 1000,
            'countries' => array( 'UK' ),
            'has' => array(
                'cancelOrder' => true,
                'CORS' => false,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchLedger' => true,
                'fetchOpenOrders' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTrades' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/51840849/87153925-ef265e80-c2c0-11ea-91b5-020c804b90e0.jpg',
                'api' => 'https://webapi.coinfloor.co.uk/bist',
                'www' => 'https://www.coinfloor.co.uk',
                'doc' => array(
                    'https://github.com/coinfloor/api',
                    'https://www.coinfloor.co.uk/api',
                ),
            ),
            'requiredCredentials' => array(
                'apiKey' => true,
                'secret' => false,
                'password' => true,
                'uid' => true,
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        '{id}/ticker/',
                        '{id}/order_book/',
                        '{id}/transactions/',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        '{id}/balance/',
                        '{id}/user_transactions/',
                        '{id}/open_orders/',
                        '{symbol}/cancel_order/',
                        '{id}/buy/',
                        '{id}/sell/',
                        '{id}/buy_market/',
                        '{id}/sell_market/',
                        '{id}/estimate_sell_market/',
                        '{id}/estimate_buy_market/',
                    ),
                ),
            ),
            'markets' => array(
                'BTC/GBP' => array( 'id' => 'XBT/GBP', 'symbol' => 'BTC/GBP', 'base' => 'BTC', 'quote' => 'GBP', 'baseId' => 'XBT', 'quoteId' => 'GBP', 'precision' => array( 'price' => 0, 'amount' => 4 )),
                'BTC/EUR' => array( 'id' => 'XBT/EUR', 'symbol' => 'BTC/EUR', 'base' => 'BTC', 'quote' => 'EUR', 'baseId' => 'XBT', 'quoteId' => 'EUR', 'precision' => array( 'price' => 0, 'amount' => 4 )),
            ),
            'exceptions' => array(
                'exact' => array(
                    'You have insufficient funds.' => '\\ccxt\\InsufficientFunds',
                    'Tonce is out of sequence.' => '\\ccxt\\InvalidNonce',
                ),
            ),
        ));
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $market = null;
        $query = $params;
        $symbol = $this->safe_string($params, 'symbol');
        if ($symbol !== null) {
            $market = $this->market($params['symbol']);
            $query = $this->omit($params, 'symbol');
        }
        $marketId = $this->safe_string($params, 'id');
        if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
            $market = $this->markets_by_id[$marketId];
        }
        if ($market === null) {
            throw new ArgumentsRequired($this->id . ' fetchBalance() requires a $symbol param');
        }
        $request = array(
            'id' => $market['id'],
        );
        $response = $this->privatePostIdBalance (array_merge($request, $query));
        $result = array(
            'info' => $response,
        );
        // base/quote used for keys e.g. "xbt_reserved"
        $base = $market['base'];
        $quote = $market['quote'];
        $baseIdLower = $this->safe_string_lower($market, 'baseId');
        $quoteIdLower = $this->safe_string_lower($market, 'quoteId');
        $result[$base] = array(
            'free' => $this->safe_float($response, $baseIdLower . '_available'),
            'used' => $this->safe_float($response, $baseIdLower . '_reserved'),
            'total' => $this->safe_float($response, $baseIdLower . '_balance'),
        );
        $result[$quote] = array(
            'free' => $this->safe_float($response, $quoteIdLower . '_available'),
            'used' => $this->safe_float($response, $quoteIdLower . '_reserved'),
            'total' => $this->safe_float($response, $quoteIdLower . '_balance'),
        );
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $response = $this->publicGetIdOrderBook (array_merge($request, $params));
        return $this->parse_order_book($response);
    }

    public function parse_ticker($ticker, $market = null) {
        // rewrite to get the $timestamp from HTTP headers
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $vwap = $this->safe_float($ticker, 'vwap');
        $baseVolume = $this->safe_float($ticker, 'volume');
        $quoteVolume = null;
        if ($vwap !== null) {
            $quoteVolume = $baseVolume * $vwap;
        }
        $last = $this->safe_float($ticker, 'last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
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

    public function fetch_ticker($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        $response = $this->publicGetIdTicker (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market = null) {
        $timestamp = $this->safe_timestamp($trade, 'date');
        $id = $this->safe_string($trade, 'tid');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'id' => $id,
            'order' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'type' => null,
            'side' => null,
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
            'id' => $market['id'],
        );
        $response = $this->publicGetIdTransactions (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        // $code is actually a $market symbol in this situation, not a currency $code
        $this->load_markets();
        $market = null;
        if ($code !== null) {
            $market = $this->market($code);
            if ($market === null) {
                throw new ArgumentsRequired($this->id . ' fetchTransactions() requires a $code argument (a $market symbol)');
            }
        }
        $request = array(
            'id' => $market['id'],
            'limit' => $limit,
        );
        $response = $this->privatePostIdUserTransactions (array_merge($request, $params));
        return $this->parse_ledger($response, null, $since, null);
    }

    public function parse_ledger_entry_status($status) {
        $types = array(
            'completed' => 'ok',
        );
        return $this->safe_string($types, $status, $status);
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            '0' => 'transaction', // deposit
            '1' => 'transaction', // withdrawal
            '2' => 'trade',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        // trade
        //
        //     {
        //         "datetime" => "2017-07-25 06:41:24",
        //         "id" => 1500964884381265,
        //         "$type" => 2,
        //         "xbt" => "0.1000",
        //         "xbt_eur" => "2322.00",
        //         "eur" => "-232.20",
        //         "$fee" => "0.00",
        //         "order_id" => 84696745
        //     }
        //
        // transaction (withdrawal)
        //
        //     {
        //         "datetime" => "2017-07-25 13:19:46",
        //         "id" => 97669,
        //         "$type" => 1,
        //         "xbt" => "-3.0000",
        //         "xbt_eur" => null,
        //         "eur" => "0",
        //         "$fee" => "0.0000",
        //         "order_id" => null
        //     }
        //
        // transaction (deposit)
        //
        //     {
        //         "datetime" => "2017-07-27 16:44:55",
        //         "id" => 98277,
        //         "$type" => 0,
        //         "xbt" => "0",
        //         "xbt_eur" => null,
        //         "eur" => "4970.04",
        //         "$fee" => "0.00",
        //         "order_id" => null
        //     }
        //
        $keys = is_array($item) ? array_keys($item) : array();
        $baseId = null;
        $quoteId = null;
        $baseAmount = null;
        $quoteAmount = null;
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            if (mb_strpos($key, '_') > 0) {
                $parts = explode('_', $key);
                $numParts = is_array($parts) ? count($parts) : 0;
                if ($numParts === 2) {
                    $tmpBaseAmount = $this->safe_float($item, $parts[0]);
                    $tmpQuoteAmount = $this->safe_float($item, $parts[1]);
                    if ($tmpBaseAmount !== null && $tmpQuoteAmount !== null) {
                        $baseId = $parts[0];
                        $quoteId = $parts[1];
                        $baseAmount = $tmpBaseAmount;
                        $quoteAmount = $tmpQuoteAmount;
                    }
                }
            }
        }
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        $type = $this->parse_ledger_entry_type($this->safe_string($item, 'type'));
        $referenceId = $this->safe_string($item, 'id');
        $timestamp = $this->parse8601($this->safe_string($item, 'datetime'));
        $fee = null;
        $feeCost = $this->safe_float($item, 'fee');
        $result = array(
            'id' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'amount' => null,
            'direction' => null,
            'currency' => null,
            'type' => $type,
            'referenceId' => $referenceId,
            'referenceAccount' => null,
            'before' => null,
            'after' => null,
            'status' => 'ok',
            'fee' => $fee,
            'info' => $item,
        );
        if ($type === 'trade') {
            //
            // it's a trade so let's make multiple entries, we have several options:
            //
            // if $fee is always in $quote $currency (the exchange uses this)
            // https://github.com/coinfloor/API/blob/master/IMPL-GUIDE.md#how-fees-affect-trade-quantities
            //
            if ($feeCost !== null) {
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $quote,
                );
            }
            return array(
                array_merge($result, array( 'currency' => $base, 'amount' => abs($baseAmount), 'direction' => ($baseAmount > 0) ? 'in' : 'out' )),
                array_merge($result, array( 'currency' => $quote, 'amount' => abs($quoteAmount), 'direction' => ($quoteAmount > 0) ? 'in' : 'out', 'fee' => $fee )),
            );
            //
            // if $fee is $base or $quote depending on buy/sell side
            //
            //     $baseFee = ($baseAmount > 0) ? array( 'currency' => $base, 'cost' => $feeCost ) : null;
            //     $quoteFee = ($quoteAmount > 0) ? array( 'currency' => $quote, 'cost' => $feeCost ) : null;
            //     return array(
            //         array_merge($result, array( 'currency' => $base, 'amount' => $baseAmount, 'direction' => ($baseAmount > 0) ? 'in' : 'out', 'fee' => $baseFee )),
            //         array_merge($result, array( 'currency' => $quote, 'amount' => $quoteAmount, 'direction' => ($quoteAmount > 0) ? 'in' : 'out', 'fee' => $quoteFee )),
            //     );
            //
            // $fee as the 3rd $item
            //
            //     return array(
            //         array_merge($result, array( 'currency' => $base, 'amount' => $baseAmount, 'direction' => ($baseAmount > 0) ? 'in' : 'out' )),
            //         array_merge($result, array( 'currency' => $quote, 'amount' => $quoteAmount, 'direction' => ($quoteAmount > 0) ? 'in' : 'out' )),
            //         array_merge($result, array( 'currency' => feeCurrency, 'amount' => $feeCost, 'direction' => 'out', 'type' => 'fee' )),
            //     );
            //
        } else {
            //
            // it's a regular transaction (deposit or withdrawal)
            //
            $amount = ($baseAmount === 0) ? $quoteAmount : $baseAmount;
            $code = ($baseAmount === 0) ? $quote : $base;
            $direction = ($amount > 0) ? 'in' : 'out';
            if ($feeCost !== null) {
                $fee = array(
                    'cost' => $feeCost,
                    'currency' => $code,
                );
            }
            return array_merge($result, array(
                'currency' => $code,
                'amount' => abs($amount),
                'direction' => $direction,
                'fee' => $fee,
            ));
        }
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'id' => $this->market_id($symbol),
        );
        $method = 'privatePostId' . $this->capitalize($side);
        if ($type === 'market') {
            $request['quantity'] = $amount;
            $method .= 'Market';
        } else {
            $request['price'] = $price;
            $request['amount'] = $amount;
        }
        //
        //     {
        //         "id":31950584,
        //         "datetime":"2020-05-21 08:38:18",
        //         "$type":1,
        //         "$price":"9100",
        //         "$amount":"0.0026"
        //     }
        //
        $response = $this->$method (array_merge($request, $params));
        $timestamp = $this->parse8601($this->safe_string($response, 'datetime'));
        return array(
            'id' => $this->safe_string($response, 'id'),
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'type' => $type,
            'price' => $this->safe_float($response, 'price'),
            'remaining' => $this->safe_float($response, 'amount'),
            'info' => $response,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelOrder() requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
            'id' => $id,
        );
        $response = $this->privatePostSymbolCancelOrder ($request);
        if ($response === 'false') {
            // unfortunately the exchange does not give much info in the $response
            throw new InvalidOrder($this->id . ' cancel was rejected');
        }
        return $response;
    }

    public function parse_order($order, $market = null) {
        $timestamp = $this->parse8601($this->safe_string($order, 'datetime'));
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $price * $amount;
            }
        }
        $side = null;
        $status = $this->safe_string($order, 'status');
        if ($order['type'] === 0) {
            $side = 'buy';
        } else if ($order['type'] === 1) {
            $side = 'sell';
        }
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        $id = $this->safe_string($order, 'id');
        return array(
            'info' => $order,
            'id' => $id,
            'clientOrderId' => null,
            'datetime' => $this->iso8601($timestamp),
            'timestamp' => $timestamp,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => 'limit',
            'timeInForce' => null,
            'postOnly' => null,
            'side' => $side,
            'price' => $price,
            'stopPrice' => null,
            'amount' => null,
            'filled' => null,
            'remaining' => $amount,
            'cost' => $cost,
            'fee' => null,
            'average' => null,
            'trades' => null,
        );
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOpenOrders() requires a $symbol param');
        }
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'id' => $market['id'],
        );
        $response = $this->privatePostIdOpenOrders (array_merge($request, $params));
        //   {
        //     "amount" => "1.0000",
        //     "datetime" => "2019-07-12 13:28:16",
        //     "id" => 233123443,
        //     "price" => "1000.00",
        //     "type" => 0
        //   }
        return $this->parse_orders($response, $market, $since, $limit, array( 'status' => 'open' ));
    }

    public function handle_errors($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($code < 400) {
            return;
        }
        if ($response === null) {
            return;
        }
        $message = $this->safe_string($response, 'error_msg');
        $feedback = $this->id . ' ' . $body;
        $this->throw_exactly_matched_exception($this->exceptions['exact'], $message, $feedback);
        throw new ExchangeError($feedback);
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        // curl -k -u '[User ID]/[API key]:[Passphrase]' https://webapi.coinfloor.co.uk:8090/bist/XBT/GBP/balance/
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode($query);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $body = $this->urlencode(array_merge(array( 'nonce' => $nonce ), $query));
            $auth = $this->uid . '/' . $this->apiKey . ':' . $this->password;
            $signature = $this->decode(base64_encode($auth));
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $signature,
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}

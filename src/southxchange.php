<?php

namespace ccxt;

use Exception; // a common import
use \ccxt\ArgumentsRequired;

class southxchange extends Exchange {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'southxchange',
            'name' => 'SouthXchange',
            'countries' => array( 'AR' ), // Argentina
            'rateLimit' => 1000,
            'has' => array(
                'cancelOrder' => true,
                'CORS' => true,
                'createDepositAddress' => true,
                'createOrder' => true,
                'fetchBalance' => true,
                'fetchDeposits' => true,
                'fetchLedger' => true,
                'fetchMarkets' => true,
                'fetchOpenOrders' => true,
                'fetchOrderBook' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTrades' => true,
                'fetchTransactions' => true,
                'fetchWithdrawals' => true,
                'withdraw' => true,
            ),
            'urls' => array(
                'logo' => 'https://user-images.githubusercontent.com/1294454/27838912-4f94ec8a-60f6-11e7-9e5d-bbf9bd50a559.jpg',
                'api' => 'https://www.southxchange.com/api',
                'www' => 'https://www.southxchange.com',
                'doc' => 'https://www.southxchange.com/Home/Api',
            ),
            'api' => array(
                'public' => array(
                    'get' => array(
                        'markets',
                        'price/{symbol}',
                        'prices',
                        'book/{symbol}',
                        'trades/{symbol}',
                    ),
                ),
                'private' => array(
                    'post' => array(
                        'cancelMarketOrders',
                        'cancelOrder',
                        'getOrder',
                        'generatenewaddress',
                        'listOrders',
                        'listBalances',
                        'listTransactions',
                        'placeOrder',
                        'withdraw',
                    ),
                ),
            ),
            'fees' => array(
                'trading' => array(
                    'tierBased' => false,
                    'percentage' => true,
                    'maker' => 0.1 / 100,
                    'taker' => 0.3 / 100,
                ),
            ),
            'commonCurrencies' => array(
                'BHD' => 'Bithold',
                'GHOST' => 'GHOSTPRISM',
                'MTC' => 'Marinecoin',
                'SMT' => 'SmartNode',
            ),
        ));
    }

    public function fetch_markets($params = array ()) {
        $markets = $this->publicGetMarkets ($params);
        $result = array();
        for ($i = 0; $i < count($markets); $i++) {
            $market = $markets[$i];
            $baseId = $market[0];
            $quoteId = $market[1];
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $id = $baseId . '/' . $quoteId;
            $result[] = array(
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'active' => null,
                'info' => $market,
                'precision' => $this->precision,
                'limits' => $this->limits,
            );
        }
        return $result;
    }

    public function fetch_balance($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostListBalances ($params);
        $result = array( 'info' => $response );
        for ($i = 0; $i < count($response); $i++) {
            $balance = $response[$i];
            $currencyId = $this->safe_string($balance, 'Currency');
            $code = $this->safe_currency_code($currencyId);
            $deposited = $this->safe_number($balance, 'Deposited');
            $unconfirmed = $this->safe_number($balance, 'Unconfirmed');
            $account = $this->account();
            $account['free'] = $this->safe_number($balance, 'Available');
            $account['total'] = $this->sum($deposited, $unconfirmed);
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'symbol' => $this->market_id($symbol),
        );
        $response = $this->publicGetBookSymbol (array_merge($request, $params));
        return $this->parse_order_book($response, null, 'BuyOrders', 'SellOrders', 'Price', 'Amount');
    }

    public function parse_ticker($ticker, $market = null) {
        $timestamp = $this->milliseconds();
        $symbol = null;
        if ($market) {
            $symbol = $market['symbol'];
        }
        $last = $this->safe_number($ticker, 'Last');
        return array(
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'high' => null,
            'low' => null,
            'bid' => $this->safe_number($ticker, 'Bid'),
            'bidVolume' => null,
            'ask' => $this->safe_number($ticker, 'Ask'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $this->safe_number($ticker, 'Variation24Hr'),
            'average' => null,
            'baseVolume' => $this->safe_number($ticker, 'Volume24Hr'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_tickers($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetPrices ($params);
        $tickers = $this->index_by($response, 'Market');
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
            'symbol' => $market['id'],
        );
        $response = $this->publicGetPriceSymbol (array_merge($request, $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_trade($trade, $market) {
        $timestamp = $this->safe_timestamp($trade, 'At');
        $priceString = $this->safe_string($trade, 'Price');
        $amountString = $this->safe_string($trade, 'Amount');
        $price = $this->parse_number($priceString);
        $amount = $this->parse_number($amountString);
        $cost = $this->parse_number(Precise::string_mul($priceString, $amountString));
        $side = $this->safe_string($trade, 'Type');
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        }
        return array(
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'symbol' => $symbol,
            'id' => null,
            'order' => null,
            'type' => null,
            'side' => $side,
            'price' => $price,
            'takerOrMaker' => null,
            'amount' => $amount,
            'cost' => $cost,
            'fee' => null,
        );
    }

    public function fetch_trades($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'symbol' => $market['id'],
        );
        $response = $this->publicGetTradesSymbol (array_merge($request, $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function parse_order($order, $market = null) {
        $status = 'open';
        $baseId = $this->safe_string($order, 'ListingCurrency');
        $quoteId = $this->safe_string($order, 'ReferenceCurrency');
        $base = $this->safe_currency_code($baseId);
        $quote = $this->safe_currency_code($quoteId);
        $symbol = $base . '/' . $quote;
        $timestamp = null;
        $price = $this->safe_number($order, 'LimitPrice');
        $amount = $this->safe_number($order, 'OriginalAmount');
        $remaining = $this->safe_number($order, 'Amount');
        $type = 'limit';
        $side = $this->safe_string_lower($order, 'Type');
        $id = $this->safe_string($order, 'Code');
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
            'cost' => null,
            'filled' => null,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'average' => null,
            'trades' => null,
        ));
    }

    public function fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market($symbol);
        }
        $response = $this->privatePostListOrders ($params);
        return $this->parse_orders($response, $market, $since, $limit);
    }

    public function create_order($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market($symbol);
        $request = array(
            'listingCurrency' => $market['base'],
            'referenceCurrency' => $market['quote'],
            'type' => $side,
            'amount' => $amount,
        );
        if ($type === 'limit') {
            $request['limitPrice'] = $price;
        }
        $response = $this->privatePostPlaceOrder (array_merge($request, $params));
        $id = json_decode($response, $as_associative_array = true);
        return array(
            'info' => $response,
            'id' => $id,
        );
    }

    public function cancel_order($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array(
            'orderCode' => $id,
        );
        return $this->privatePostCancelOrder (array_merge($request, $params));
    }

    public function create_deposit_address($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency($code);
        $request = array(
            'currency' => $currency['id'],
        );
        $response = $this->privatePostGeneratenewaddress (array_merge($request, $params));
        //
        // the exchange API returns a quoted-quoted-string
        //
        //     "\"0x4d43674209fcb66cc21469a6e5e52de7dd5bcd93\""
        //
        $address = $response;
        if ($address[0] === '"') {
            $address = json_decode($address, $as_associative_array = true);
            if ($address[0] === '"') {
                $address = json_decode($address, $as_associative_array = true);
            }
        }
        $parts = explode('|', $address);
        $numParts = is_array($parts) ? count($parts) : 0;
        $address = $parts[0];
        $this->check_address($address);
        $tag = null;
        if ($numParts > 1) {
            $tag = $parts[1];
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
        $request = array(
            'currency' => $currency['id'],
            'address' => $address,
            'amount' => $amount,
        );
        if ($tag !== null) {
            $request['address'] = $address . '|' . $tag;
        }
        $response = $this->privatePostWithdraw (array_merge($request, $params));
        return array(
            'info' => $response,
            'id' => null,
        );
    }

    public function parse_ledger_entry_type($type) {
        $types = array(
            'trade' => 'trade',
            'tradefee' => 'fee',
            'withdraw' => 'transaction',
            'deposit' => 'transaction',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function parse_ledger_entry($item, $currency = null) {
        //
        //     {
        //         "Date":"2020-08-07T12:36:52.72",
        //         "CurrencyCode":"USDT",
        //         "Amount":27.614678000000000000,
        //         "TotalBalance":27.614678000000000000,
        //         "Type":"deposit",
        //         "Status":"confirmed",
        //         "Address":"0x4d43674209fcb66cc21469a6e5e52de7dd5bcd93",
        //         "Hash":"0x1809f1950c51a2f64fd2c4a27d4b06450fd249883fd91c852b79a99a124837f3",
        //         "Price":0.0,
        //         "OtherAmount":0.0,
        //         "OtherCurrency":null,
        //         "OrderCode":null,
        //         "TradeId":null,
        //         "MovementId":2732259
        //     }
        //
        $id = $this->safe_string($item, 'MovementId');
        $direction = null;
        $account = null;
        $referenceId = $this->safe_string_2($item, 'TradeId', 'OrderCode');
        $referenceId = $this->safe_string($item, 'Hash', $referenceId);
        $referenceAccount = $this->safe_string($item, 'Address');
        $type = $this->safe_string($item, 'Type');
        $ledgerEntryType = $this->parse_ledger_entry_type($type);
        $code = $this->safe_currency_code($this->safe_string($item, 'CurrencyCode'), $currency);
        $amount = $this->safe_number($item, 'Amount');
        $after = $this->safe_number($item, 'TotalBalance');
        $before = null;
        if ($amount !== null) {
            if ($after !== null) {
                $before = $after - $amount;
            }
            if ($type === 'withdrawal') {
                $direction = 'out';
            } else if ($type === 'deposit') {
                $direction = 'in';
            } else if (($type === 'trade') || ($type === 'tradefee')) {
                $direction = ($amount < 0) ? 'out' : 'in';
                $amount = abs($amount);
            }
        }
        $timestamp = $this->parse8601($this->safe_string($item, 'Date'));
        $fee = null;
        $status = $this->safe_string($item, 'Status');
        return array(
            'info' => $item,
            'id' => $id,
            'direction' => $direction,
            'account' => $account,
            'referenceId' => $referenceId,
            'referenceAccount' => $referenceAccount,
            'type' => $ledgerEntryType,
            'currency' => $code,
            'amount' => $amount,
            'before' => $before,
            'after' => $after,
            'status' => $status,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => $fee,
        );
    }

    public function fetch_ledger($code = null, $since = null, $limit = null, $params = array ()) {
        if ($code === null) {
            throw new ArgumentsRequired($this->id . ' fetchLedger() requires a $code argument');
        }
        $this->load_markets();
        $currency = $this->currency($code);
        $limit = ($limit === null) ? 50 : $limit;
        $request = array(
            'Currency' => $currency['id'],
            // 'TransactionType' => 'transactions', // deposits, withdrawals, depositswithdrawals, transactions
            // 'PageIndex' => 0,
            'PageSize' => $limit, // max 50
            'SortField' => 'Date',
            // 'Descending' => true,
        );
        $pageIndex = $this->safe_integer($params, 'PageIndex');
        if ($pageIndex === null) {
            $request['Descending'] = true;
        }
        $response = $this->privatePostListTransactions (array_merge($request, $params));
        //
        // fetchLedger ('BTC')
        //
        //     {
        //         "TotalElements":2,
        //         "Result":array(
        //             array(
        //                 "Date":"2020-08-07T13:06:22.117",
        //                 "CurrencyCode":"BTC",
        //                 "Amount":-0.000000301000000000,
        //                 "TotalBalance":0.000100099000000000,
        //                 "Type":"tradefee",
        //                 "Status":"confirmed",
        //                 "Address":null,
        //                 "Hash":null,
        //                 "Price":0.0,
        //                 "OtherAmount":0.0,
        //                 "OtherCurrency":null,
        //                 "OrderCode":null,
        //                 "TradeId":5298215,
        //                 "MovementId":null
        //             ),
        //             {
        //                 "Date":"2020-08-07T13:06:22.117",
        //                 "CurrencyCode":"BTC",
        //                 "Amount":0.000100400000000000,
        //                 "TotalBalance":0.000100400000000000,
        //                 "Type":"trade",
        //                 "Status":"confirmed",
        //                 "Address":null,
        //                 "Hash":null,
        //                 "Price":11811.474849000000000000,
        //                 "OtherAmount":1.185872,
        //                 "OtherCurrency":"USDT",
        //                 "OrderCode":"78389610",
        //                 "TradeId":5298215,
        //                 "MovementId":null
        //             }
        //         )
        //     }
        //
        // fetchLedger ('BTC'), same trade, other side
        //
        //     {
        //         "TotalElements":2,
        //         "Result":array(
        //             array(
        //                 "Date":"2020-08-07T13:06:22.133",
        //                 "CurrencyCode":"USDT",
        //                 "Amount":-1.185872000000000000,
        //                 "TotalBalance":26.428806000000000000,
        //                 "Type":"trade",
        //                 "Status":"confirmed",
        //                 "Address":null,
        //                 "Hash":null,
        //                 "Price":11811.474849000000000000,
        //                 "OtherAmount":0.000100400,
        //                 "OtherCurrency":"BTC",
        //                 "OrderCode":"78389610",
        //                 "TradeId":5298215,
        //                 "MovementId":null
        //             ),
        //             {
        //                 "Date":"2020-08-07T12:36:52.72",
        //                 "CurrencyCode":"USDT",
        //                 "Amount":27.614678000000000000,
        //                 "TotalBalance":27.614678000000000000,
        //                 "Type":"deposit",
        //                 "Status":"confirmed",
        //                 "Address":"0x4d43674209fcb66cc21469a6e5e52de7dd5bcd93",
        //                 "Hash":"0x1809f1950c51a2f64fd2c4a27d4b06450fd249883fd91c852b79a99a124837f3",
        //                 "Price":0.0,
        //                 "OtherAmount":0.0,
        //                 "OtherCurrency":null,
        //                 "OrderCode":null,
        //                 "TradeId":null,
        //                 "MovementId":2732259
        //             }
        //         )
        //     }
        //
        $result = $this->safe_value($response, 'Result', array());
        return $this->parse_ledger($result, $currency, $since, $limit);
    }

    public function parse_transaction_status($status) {
        $statuses = array(
            'pending' => 'pending',
            'processed' => 'pending',
            'confirmed' => 'ok',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction($transaction, $currency = null) {
        //
        //     {
        //         "Date":"2020-08-07T12:36:52.72",
        //         "CurrencyCode":"USDT",
        //         "Amount":27.614678000000000000,
        //         "TotalBalance":27.614678000000000000,
        //         "Type":"deposit",
        //         "Status":"confirmed",
        //         "Address":"0x4d43674209fcb66cc21469a6e5e52de7dd5bcd93",
        //         "Hash":"0x1809f1950c51a2f64fd2c4a27d4b06450fd249883fd91c852b79a99a124837f3",
        //         "Price":0.0,
        //         "OtherAmount":0.0,
        //         "OtherCurrency":null,
        //         "OrderCode":null,
        //         "TradeId":null,
        //         "MovementId":2732259
        //     }
        //
        $id = $this->safe_string($transaction, 'MovementId');
        $amount = $this->safe_number($transaction, 'Amount');
        $address = $this->safe_string($transaction, 'Address');
        $addressTo = $address;
        $addressFrom = null;
        $tag = null;
        $tagTo = $tag;
        $tagFrom = null;
        $txid = $this->safe_string($transaction, 'Hash');
        $type = $this->safe_string($transaction, 'Type');
        $timestamp = $this->parse8601($this->safe_string($transaction, 'Date'));
        $status = $this->parse_transaction_status($this->safe_string($transaction, 'Status'));
        $currencyId = $this->safe_string($transaction, 'CurrencyCode');
        $code = $this->safe_currency_code($currencyId, $currency);
        return array(
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'addressTo' => $addressTo,
            'addressFrom' => $addressFrom,
            'tag' => $tag,
            'tagTo' => $tagTo,
            'tagFrom' => $tagFrom,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601($timestamp),
            'fee' => null,
        );
    }

    public function fetch_transactions($code = null, $since = null, $limit = null, $params = array ()) {
        if ($code === null) {
            throw new ArgumentsRequired($this->id . ' fetchTransactions() requires a $code argument');
        }
        $this->load_markets();
        $currency = $this->currency($code);
        $limit = ($limit === null) ? 50 : $limit;
        $request = array(
            'Currency' => $currency['id'],
            'TransactionType' => 'depositswithdrawals', // deposits, withdrawals, depositswithdrawals, transactions
            // 'PageIndex' => 0,
            'PageSize' => $limit, // max 50
            'SortField' => 'Date',
            // 'Descending' => true,
        );
        $pageIndex = $this->safe_integer($params, 'PageIndex');
        if ($pageIndex === null) {
            $request['Descending'] = true;
        }
        $response = $this->privatePostListTransactions (array_merge($request, $params));
        //
        //     {
        //         "TotalElements":2,
        //         "Result":array(
        //             {
        //                 "Date":"2020-08-07T12:36:52.72",
        //                 "CurrencyCode":"USDT",
        //                 "Amount":27.614678000000000000,
        //                 "TotalBalance":27.614678000000000000,
        //                 "Type":"deposit",
        //                 "Status":"confirmed",
        //                 "Address":"0x4d43674209fcb66cc21469a6e5e52de7dd5bcd93",
        //                 "Hash":"0x1809f1950c51a2f64fd2c4a27d4b06450fd249883fd91c852b79a99a124837f3",
        //                 "Price":0.0,
        //                 "OtherAmount":0.0,
        //                 "OtherCurrency":null,
        //                 "OrderCode":null,
        //                 "TradeId":null,
        //                 "MovementId":2732259
        //             }
        //         )
        //     }
        //
        $result = $this->safe_value($response, 'Result', array());
        return $this->parse_transactions($result, $currency, $since, $limit);
    }

    public function fetch_deposits($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'TransactionType' => 'deposits',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function fetch_withdrawals($code = null, $since = null, $limit = null, $params = array ()) {
        $request = array(
            'TransactionType' => 'withdrawals',
        );
        return $this->fetch_transactions($code, $since, $limit, array_merge($request, $params));
    }

    public function sign($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->implode_params($path, $params);
        $query = $this->omit($params, $this->extract_params($path));
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = $this->nonce();
            $query = array_merge(array(
                'key' => $this->apiKey,
                'nonce' => $nonce,
            ), $query);
            $body = $this->json($query);
            $headers = array(
                'Content-Type' => 'application/json',
                'Hash' => $this->hmac($this->encode($body), $this->encode($this->secret), 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}
